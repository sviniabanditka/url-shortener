<?php

namespace App\Controllers;

use PDO;
use App\Models\Url;
use Twig\Environment;
use Workerman\Protocols\Http\Request;

class RedirectController
{
    public static function resolveUrl(Request $request, Environment $template, PDO $db): string
    {
        $identifier = ltrim($request->path(), '/');
        $row = (new Url($db))->findByIdentifier($identifier);

        if ($row instanceof Url) {
            $row->incrementClicks();

            return $template->render('redirect.html', [
                'url' => $row->getOriginalUrl(),
                'clicks' => $row->getClicks()
            ]);
        }

        return $template->render('error.html', [
            'error' => 'Requested link not found or expired!'
        ]);
    }
}
