<?php

use App\Core\DB;
use Dotenv\Dotenv;
use Workerman\Worker;
use Twig\Loader\FilesystemLoader;
use App\Controllers\IndexController;
use Workerman\Protocols\Http\Request;
use App\Controllers\RedirectController;
use Workerman\Connection\TcpConnection;

require_once __DIR__ . '/vendor/autoload.php';

// init dotenv
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// init db structure
$db = DB::new();
$db->query(file_get_contents(__DIR__ . '/' . $_ENV['INIT_SQL_PATH']));

// init twig
$loader = new FilesystemLoader($_ENV['VIEWS_PATH']);
$template = new \Twig\Environment($loader);

// create worker & configure processes number
$http_worker = new Worker($_ENV['APP_HOST'] . ':' . $_ENV['APP_PORT']);
$http_worker->count = $_ENV['PROCESS_NUMBER'];

// handle request
$http_worker->onMessage = function (TcpConnection $connection, Request $request) use ($template, $db) {
  if ($request->path() === '/') {
    if ($request->method() === 'GET') {
      $output = IndexController::showIndexPage($request, $template, $db);
    } elseif ($request->method() === 'POST') {
      $output = IndexController::submitForm($request, $template, $db);
    }
  } elseif (!str_contains($request->path(), '.')) {
    $output = RedirectController::resolveUrl($request, $template, $db);
  } else {
    $output = null;
  }
  $connection->send($output);
};

// run all workers
Worker::runAll();