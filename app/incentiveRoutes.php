<?php

declare(strict_types=1);
error_reporting(0);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

require '../includes/IncentiveDetails.php';


return function (App $app) {


$app->post('/SocountCurMonth', function (Request $request, Response $response) {
	$reqid = randomPassword();
    $this->logger->info("SocountCurMonth", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'request' => $request->getParsedBody()]);

   // $sno = $request->getAttribute('sno');
   $request_data = $request->getParsedBody();
    $sno = $request_data['sno'];
            $db = new IncentiveDetails;
            $data = $db->SocountCurMonth($sno);
            $response->getBody()->write(json_encode($data));
        
        $this->logger->info("SocountCurMonth", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'response' => $response->getBody()]);
    
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});



$app->post('/getTargetCUCo', function (Request $request, Response $response) {
	$reqid = randomPassword();
    $this->logger->info("getTargetCUCo", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'request' => $request->getParsedBody()]);

    $request_data = $request->getParsedBody();
    $sno = $request_data['sno'];
            $db = new IncentiveDetails;
            try{
            $data = $db->getTargetCUCo($sno);
            }catch(Exception  $e){
                $this->logger->info("getTargetCUCo", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'error' => $e->getMessage()]);

            }
            $response->getBody()->write(json_encode($data));
        
        $this->logger->info("getTargetCUCo", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'response' => $response->getBody()]);
    
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});


// $app->post('/getTargetSS', function (Request $request, Response $response) {
// 	$reqid = randomPassword();
//     $this->logger->info("getTargetSS", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'request' => $request->getParsedBody()]);

//     $request_data = $request->getParsedBody();
//     $sno = $request_data['sno'];
//             $db = new IncentiveDetails;
//             $data = $db->getTargetSS($sno);
//             $response->getBody()->write(json_encode($data));
        
//         $this->logger->info("getTargetSS", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'response' => $response->getBody()]);
    
//     return $response
//         ->withHeader('Content-type', 'application/json')
//         ->withStatus(200);
// });



$app->post('/getMonthSales', function (Request $request, Response $response) {
	$reqid = randomPassword();
    $this->logger->info("getMonthSales", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'request' => $request->getParsedBody()]);

    $request_data = $request->getParsedBody();
    $sno = $request_data['sno'];
    $month = $request_data['month'];
            $db = new IncentiveDetails;
            try{
            $data = $db->getMonthSales($sno,$month);
            }catch(Exception  $e){
                $this->logger->info("getMonthSales", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'error' => $e->getMessage()]);

            }
            $response->getBody()->write(json_encode($data));
        
        $this->logger->info("getMonthSales", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'response' => $response->getBody()]);
    
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});



$app->post('/getSlab', function (Request $request, Response $response) {
	$reqid = randomPassword();
    $this->logger->info("getSlab", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'request' => $request->getParsedBody()]);

    $request_data = $request->getParsedBody();
    $sno = $request_data['sno'];
    $salecount = $request_data['salecount'];
            $db = new IncentiveDetails;
            try{
            $data = $db->getSlab($sno,$salecount);
            }catch(Exception  $e){
                $this->logger->info("getSlab", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'error' => $e->getMessage()]);

            }
            $response->getBody()->write(json_encode($data));
        
        $this->logger->info("getSlab", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'response' => $response->getBody()]);
    
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});


$app->post('/getLastUpdatedTime', function (Request $request, Response $response) {
	$reqid = randomPassword();
    $this->logger->info("getLastUpdatedTime", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'request' => $request->getParsedBody()]);

    $request_data = $request->getParsedBody();
    $sno = $request_data['sno'];

            $db = new IncentiveDetails;
            try{
            $data = $db->getLastUpdatedTime($sno);
            }catch(Exception  $e){
                $this->logger->info("getLastUpdatedTime", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'error' => $e->getMessage()]);
                $data = $e->getMessage();
            }
            $response->getBody()->write(json_encode($data));
        
        $this->logger->info("getLastUpdatedTime", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'response' => $response->getBody()]);
    
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});


};