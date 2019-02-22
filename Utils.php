<?php

/**
 * gather all necessary functions
 */
class Utils
{
    public static function remove_accent($str)
    {
        $a = array(
                    'á','é','í','ó','ú','ý','Á','É','Í','Ó','Ú','Ý',
                    'à','è','ì','ò','ù','ỳ','À','È','Ì','Ò','Ù','Ỳ',
                    'ả','ẻ','ỉ','ỏ','ủ','ỷ','Ả','Ẻ','Ỉ','Ỏ','Ủ','Ỷ',
                    'ã','ẽ','ĩ','õ','ũ','ỹ','Ã','Ẽ','Ĩ','Õ','Ũ','Ỹ',
                    'ạ','ẹ','ị','ọ','ụ','ỵ','Ạ','Ẹ','Ị','Ọ','Ụ','Ỵ',
                    'â','ê','ô','ư','Â','Ê','Ô','Ư',
                    'ấ','ế','ố','ứ','Ấ','Ế','Ố','Ứ',
                    'ầ','ề','ồ','ừ','Ầ','Ề','Ồ','Ừ',
                    'ẩ','ể','ổ','ử','Ẩ','Ể','Ổ','Ử',
                    'ậ','ệ','ộ','ự','Ậ','Ệ','Ộ','Ự'
                );
        $b = array(
                    'a','e','i','o','u','y','A','E','I','O','U','Y',
                    'a','e','i','o','u','y','A','E','I','O','U','Y',
                    'a','e','i','o','u','y','A','E','I','O','U','Y',
                    'a','e','i','o','u','y','A','E','I','O','U','Y',
                    'a','e','i','o','u','y','A','E','I','O','U','Y',
                    'a','e','o','u','A','E','O','U',
                    'a','e','o','u','A','E','O','U',
                    'a','e','o','u','A','E','O','U',
                    'a','e','o','u','A','E','O','U',
                    'a','e','o','u','A','E','O','U'
                );
        return str_replace($a, $b, $str);
    }
    /**
     * slugify string
     * @param  [string] $string
     * @return [string]
     */
    public static function slugify($string)
    {
        $string = self::remove_accent($string);
        $slug = preg_replace('/[^A-Za-z0-9-_]+/', '-', $string);
        return strtolower($slug);
    }

    /**
     * curl get
     * @param  string $url
     * @param  array $fields
     * @return [type]
     */
    public static function curl_get($url, $fields = array(), $headers = array())
    {
        return self::curl_method($url, 'GET', $fields, $headers);
    }

    /**
     * curl_post
     * @param  string $url
     * @param  array $fields
     * @param  array $headers
     * @return type
     */
    public static function curl_post($url, $fields, $headers = array(), $json_fields = false)
    {
        return self::curl_method($url, 'POST', $fields, $headers, $json_fields);
    }

    /**
     * curl_post
     * @param  string $url
     * @param  array $fields
     * @param  array $headers
     * @return type
     */
    public static function curl_put($url, $fields, $headers = array(), $json_fields = false)
    {
        return self::curl_method($url, 'PUT', $fields, $headers, $json_fields);
    }
    /**
     * curl_method
     * @param  string $url
     * @param  string $method :  POST or GET
     * @param  array  $fields
     * @return [type]
     */
    public static function curl_method($url, $method, $fields = array(), $headers = array(), $json_fields = false, $echo = false)
    {

        $fields_string = '';
        //url-ify the data for the POST
        //

        if ($json_fields) {
            $fields_string = json_encode($fields);
        } else {
            foreach ($fields as $key => $value) {
                $fields_string .= $key . '=' . $value . '&';
            }
            rtrim($fields_string, '&');
        }

        if ($method == 'GET') {
            $url .= (strpos($url, '?') === false ? '?' : '&') . $fields_string;
        }
        
        //open connection
        $ch = curl_init($url);

        // headers
        // var_dump($headers);
        if (isset($headers) && count($headers)) {
            $headr = array();
            // $headr[] = 'Content-length: '.(isset($headers['content_length']) ? $headers['content_length'] : 0);
            $headr[] = 'Content-type: ' . (isset($headers['content_type']) ? $headers['content_type'] : 'application/json');
            if (isset($headers['authorization'])) {
                $headr[] = 'Authorization: ' . $headers['authorization'];
            }
            // var_dump($headr);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        }
        //set the url, number of POST vars, POST data
        // var_dump($fields_string);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        }  elseif($method == 'PUT') {
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        } else {
            $url .= (strpos($url, '?') !== false ? '?' : '&') . $fields_string;
        }
        // curl_setopt($ch, CURLOPT_URL, $url);
        // display or not result
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
        if ($echo) {
            echo $result;
        }
        return $result;
    }

    /**
     * check if string (haystack) starts with ($needle)
     * @param  string $haystack
     * @param  string $needle
     * @return string
     */
    public static function startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /**
     * check if string(haystack) ends with ($needle)
     * @param  string $haystack
     * @param  string $needle
     * @return string
     */
    public static function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }
    
    /**
     * this method works use phalconphp code
     * crop Image, 
     * @param  string $path image path
     * @param  array  $size width, heigth, offsetX, offsetY
     * @return bool     true if is OK
     */
    public static function cropImage($path, $size, $action = 'crop')
    {
        $image = new Phalcon\Image\Adapter\Imagick($path);
        if (method_exists($image, $action)) {
            switch ($action) {
                case 'resize':
                    $image->resize($size['width'], $size['heigth']);
                    break;

                default:
                    $image->crop($size['width'], $size['heigth'], $size['offsetX'], $size['offsetY']);
                    break;
            }

            return $image->save();
        } else {
            throw new Exception("Invalide action", 1);
        }
    }
    /**
     * @param $string the original string
     * @param $count the word count
     * @param $ellipsis
     *   TRUE to add "..."
     *   or use a string to define other character
     *
     * @return
     *   trimmed string with ellipsis added if it was truncated
     */
    public static function ellipsis($string, $count = 15, $ellipsis = true)
    {
        $words = explode(' ', $string);
        if (count($words) > $count) {
            array_splice($words, $count);
            $string = implode(' ', $words);
            if (is_string($ellipsis)) {
                $string .= $ellipsis;
            } elseif ($ellipsis) {
                $string .= ' ...';
            }
        }
        return $string;
    }

    /**
     * Extract username from email
     * @param  string $email
     * @return string username
     */
    public static function extractUsernameFromEmail($email)
    {
        if (!isset($email) or !$email) {
            return '';
        }
        $parts = explode("@", $email);
        if (count($parts)) {
            return $parts[0].rand(11, 99);
        } else {
            return $email;
        }
    }

    /**
     * time Elapsed
     * @param  int $time seconde
     * @return string  elapsed time (humain form)
     */
    public static function timeElapsed($time)
    {
        $secs = time() - $time;
        $bit = array(
            ' an'     => $secs / 31556926 % 12,
            // ' M'     => $secs / 2592000  % ,
            ' semaine'   => $secs / 604800 % 52,
            'J'     => $secs / 86400 % 7,
            'H'     => $secs / 3600 % 24,
            'm'     => $secs / 60 % 60,
            's'     => $secs % 60
        );
        
        foreach ($bit as $k => $v) {
            if ($v > 1) {
                $ret[] = $v . $k /*. 's'*/;
            }
            if ($v == 1) {
                $ret[] = $v . $k;
            }
        }
        array_splice($ret, count($ret)-1, 0, 'et');
        $ret[] = 'ago.';
        
        return join(' ', $ret);
    }
    
    /**
     * load zip codes
     * @param  int $time seconde
     * @return string  elapsed time (humain form)
     */
    public static function loadzipcodes()
    {
        $zips = json_decode(file_get_contents(APP_PATH . "/data/zips.json"), true);

        return $zips;
    }
    /**
     * strip_desc_tags
     * @param  string $str
     * @param  string $tag
     * @return string
     */
    public static function strip_desc_tags($str, $tag = '')
    {
        return $str;
        if (!$tag) {
            return $str;
        }
        
        $pattern = [
            '/<p>/i',
            '/<\/p>/i'
        ];
        $replacement = [
            '\n',
            '\n',
        ];
        return preg_replace($pattern, $replacement, $str);
    }
}
