<?php

declare(strict_types=1);
error_reporting(0);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

require '../includes/CustomerDetails.php';


return function (App $app) {

    $app->get('/customerDetails/{vno}', function (Request $request, Response $response) {
        $reqid = randomPassword();
        $this->logger->info("customerDetails", ['reqid' => $reqid, 'ip' => $request->getAttribute('ip_address'), 'request' => $request->getAttributes()]);

      
            if (!haveEmptyParameters(array('vno'), $request, $response)) {
                $vno = $request->getAttribute('vno');

                $db = new CustomerDetails;

                $data = $db->getCustomerDetails($vno);
                $response->getBody()->write(json_encode($data));
            }
            $this->logger->info("customerDetails", ['reqid' => $reqid, 'ip' => $request->getAttribute('ip_address'), 'response' => $response->getBody()]);
        
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    });


};



$app->post('/getDPlist', function (Request $request, Response $response) {
	$reqid = randomPassword();
    $this->logger->info("getDPlist", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'request' => $request->getParsedBody()]);

    

        if (!haveEmptyParameters(array('rtom', 'eqtype', 'latitude', 'longitude'), $request, $response)) {

            $request_data = $request->getParsedBody();

            $rtom = $request_data['rtom'];
            $eqtype = $request_data['eqtype'];
            $lat = $request_data['latitude'];
            $lon = $request_data['longitude'];

           // $db = new DbOperationsNetFC;
          //  $data = $db->getDPlist($rtom, $eqtype, $lat, $lon,$this->logger);
          //  $response->getBody()->write(json_encode($data));
        }
        $this->logger->info("getDPlist", ['reqid'=>$reqid,'ip' => $request->getAttribute('ip_address'), 'response' => $response->getBody()]);
    
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});