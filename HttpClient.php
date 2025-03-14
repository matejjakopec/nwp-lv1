<?php

class HttpClient
{
    public static function get($url)
    {
        try {
            $ch = curl_init();

            if ($ch === false) {
                throw new Exception('failed to initialize');
            }

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $content = curl_exec($ch);
            if ($content === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
            $httpReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        } catch(Exception $e) {
            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);
        } finally {
            if (is_resource($ch)) {
                curl_close($ch);
            }
        }

        return $content;
    }
}