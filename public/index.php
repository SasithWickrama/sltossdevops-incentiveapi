<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use \Firebase\JWT\JWT;
use PhpParser\Node\Arg;

date_default_timezone_set("Asia/Colombo");

require '../vendor/autoload.php';


$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        "jwt" => [
            'secret' => 'AE8362811BA95D28'
        ]
    ]
]);





$container = $app->getContainer();
$container['logger'] = function ($c) {

    $logname = __DIR__ . '/../logs/app.log';
    if (file_exists($logname) && filesize($logname) > 10000000) {
        $path_parts = pathinfo($logname);
        $pattern = $path_parts['dirname'] . '/' . $path_parts['filename'] . "-%d." . $path_parts['extension'];

        for ($i = 100000 - 1; $i > 0; $i--) {
            $fn = sprintf($pattern, $i);
            if (file_exists($fn))
                rename($fn, sprintf($pattern, $i + 1));
        }
        rename($logname, sprintf($pattern, 1));
    }

    $stream = new Monolog\Handler\StreamHandler($logname);
    $logger = new Monolog\Logger('my_logger');
    $logger->pushHandler($stream);

    return $logger;
};

$checkProxyHeaders = true;
$trustedProxies = [];
$app->add(new RKA\Middleware\IpAddress($checkProxyHeaders, $trustedProxies));


$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

$cusInfoRoutes = require __DIR__ . '/../app/loginRoutes.php';
$cusInfoRoutes($app);

$orderRoutes = require __DIR__ . '/../app/incentiveRoutes.php';
$orderRoutes($app);


function randomPassword()
{
    $alphabet = "ABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}


function haveEmptyParameters($required_params, $request, $response)
{
    $error = false;
    $error_params = '';
    $request_params = $request->getParsedBody();
    if(!$request_params){
        $request_params = $request->getAttributes();
    }

    foreach ($required_params as $param) {
        if (!isset($request_params[$param]) || strlen($request_params[$param]) <= 0) {
            $error = true;
            $error_params .= $param . ', ';
        }
    }

    if ($error) {
        $error_detail = array();
        $error_detail['error'] = true;
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        $response->write(json_encode($error_detail));
    }
    return $error;
}





$app->run();
