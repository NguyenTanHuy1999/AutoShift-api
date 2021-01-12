<?php
namespace Gma;

use Illuminate\Support\Facades\Session;

class Curl
{
    /*
     *
     * @param string $url
     * @param array postFields
     * @param bool getJson
     *
     * @return array response
     **/
    public static function makeCurlPost($url, $header, $postFields, $getJson = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $body = curl_exec($ch);
        curl_close($ch);
        if (!$getJson) {
            return $body;
        }
        $json = @json_decode($body, true);
        if (empty($json)) {
            die('Unexpected response from server: '.$body);
        }

        return $json;
    }
}


?>