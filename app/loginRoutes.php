<?php

declare(strict_types=1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

require '../includes/LoginDetails.php';


return function (App $app) {


$app->post('/login', function (Request $request, Response $response) {
	$reqid = randomPassword();
    $this->logger->info("Login", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'request' => $request->getParsedBody()]);

    

        if (!haveEmptyParameters(array('uname', 'pwd'), $request, $response)) {

            $request_data = $request->getParsedBody();

            $uname = $request_data['uname'];
            $pwd = $request_data['pwd'];

            $db = new LoginDetails;
            $data = $db->getUserDetails($uname, $pwd);
            $response->getBody()->write(json_encode($data));
        }
        $this->logger->info("login", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'response' => $response->getBody()]);
    
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});


};