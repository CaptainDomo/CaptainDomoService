<?php

namespace cds\Controller;


use cds\Config\Config;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthenticationController
{
    function authenticate(Request $request, Response $response)
    {
        $postBody = $request->getParsedBody();

        $username = $postBody['username'];
        $password = $postBody['password'];

        if ($username == '' || $password == '') {
            return $response->withJson(array('success' => false));
        }

        foreach (Config::get()['BasicAuthUsers'] as $valid_user => $valid_password) {
            if ($valid_user == $username && $valid_password == $password) {
                return $response->withJson(array('success' => true));
            }
        }

        return $response->withJson(array('success' => false));
    }
}