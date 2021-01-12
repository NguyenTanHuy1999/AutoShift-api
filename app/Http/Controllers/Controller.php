<?php

namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Laravel\Lumen\Routing\Controller as BaseLumenController;

class Controller extends BaseLumenController
{
    use Helpers;

    protected $_domain;

    public function __construct()
    {
        // $domain = get_request_domain();
        // $this->domain = $domain;
    }

    protected function errorBadRequest($message = '')
    {
        if (is_array($message)) {
            $tmp = array();
            foreach ($message as $key => $value) {
                if (is_array($value)) {
                    $tmp[] = $value[0];
                } else {
                    $tmp[] = $value;
                }
            }
            $message = $tmp;
        } else {
            $message = array($message);
        }

        $response = array(
            'error_code' => 400,
            'message' => $message,
            'data' => array(),
            );

        return $this->response->array($response, 400);
    }

    protected function successRequest($data = array())
    {
        $response = array(
            'error_code' => 0,
            'message' => ['Successfully'],
            'data' => $data,
            );
        return $this->response->array($response, 200);
    }

    protected function transformItem($item, $transformer)
    {
        return $transformer->transform($item);
    }

    protected function errorForbidden($message = '')
    {
        if (is_array($message)) {
            $message = array_values($message);
        } else {
            $message = array($message);
        }

        $response = array(
            'error_code' => 403,
            'message' => $message,
            'data' => array(),
            );

        return $this->response->array($response, 403);
    }
    protected function errorUnauthorized($message = '')
    {
        if (is_array($message)) {
            $message = array_values($message);
        } else {
            $message = array($message);
        }

        $response = array(
            'error_code' => 401,
            'message' => $message,
            'data' => array(),
            );

        return $this->response->array($response, 401);
    }
}
