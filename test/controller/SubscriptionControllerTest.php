<?php

use cds\CaptainDomoService;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;

require '../../vendor/autoload.php';

class SubscriptionControllerTest extends PHPUnit_Framework_TestCase
{

    public function requestFactory($method, $uri_string)
    {
        $env = Environment::mock();
        $uri = Uri::createFromString($uri_string);
        $headers = Headers::createFromEnvironment($env);
        $cookies = [];
        $serverParams = $env->all();
        $body = new RequestBody();
        $uploadedFiles = UploadedFile::createFromEnvironment($env);
        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);
        return $request;
    }




    public function testGetAll()
    {
        $request = $this->requestFactory('GET', '/management/subscription');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withMethod('GET');

        $app = new CaptainDomoService();
        $resOut = $app($request, new Response());
        $resOut->getBody()->rewind();
        $response_body = $resOut->getBody()->getContents();

        $expectedJson = '{"subscriptionList":[{"id":1,"number":"23","firstname":"Philipp","lastname":"Feustel","email":"phfeustel@gmx.de","member":{"id":null,"number":23,"firstname":"Philipp","lastname":"Feustel"}},{"id":9,"number":"353","firstname":"Helena","lastname":"Borowski","email":"lenchen.zone@gmx.de","member":{"id":null,"number":353,"firstname":"Helena","lastname":"Borowski"}},{"id":12,"number":"748","firstname":"Helena","lastname":"August","email":"puck.August@gmx.com","member":{"id":null,"number":748,"firstname":"Helena","lastname":"August"}},{"id":11,"number":"748","firstname":"Helena","lastname":"August","email":"lenchen.zone@gmx.de","member":{"id":null,"number":748,"firstname":"Helena","lastname":"August"}}]}';

        $this->assertEquals('200', $resOut->getStatusCode());
        $this->assertJsonStringEqualsJsonString($expectedJson, $response_body);
    }
}
