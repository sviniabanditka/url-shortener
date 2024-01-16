<?php

namespace App\Controllers;

use PDO;
use App\Models\Url;
use Twig\Environment;
use Rakit\Validation\Validator;
use Workerman\Protocols\Http\Request;

class IndexController
{
    public static function showIndexPage(Request $request, Environment $template, PDO $db): string
    {
        return $template->render('index.html');
    }

    public static function submitForm(Request $request, Environment $template, PDO $db): string
    {
        // validate input
        $validator = new Validator();
        $validation = $validator->make($request->post(), [
            'url'  => 'required|url|max:100',
            'date' => 'required|numeric|min:5',
        ]);
        $validation->validate();
        if ($validation->fails()) {
            return $template->render('index.html', [
                'errors' => $validation->errors()->all()
            ]);
        } else {
            // generate data
            $identifier = uniqid();
            $original_url = $request->post('url');
            $expired_at = date("Y-m-d H:i:s", strtotime("+" . $request->post('date') . " sec"));

            // insert data
            (new Url($db))->setIdentifier($identifier)
                ->setOriginalUrl($original_url)
                ->setExpiredAt($expired_at)
                ->createUrl();

            return $template->render('url.html', [
                'url' => $_ENV['APP_HOST'] . ':' . $_ENV['APP_PORT'] . '/' . $identifier
            ]);
        }
    }
}
