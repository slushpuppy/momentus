<?php
namespace Lib\Core;

class Http {
    /**
     * @var
     */
    private static $_cache;

    /**
     * @return mixed
     */
    public static function request() {
		if (!self::$_cache['request']) {
			$get = $_GET;
			
			$first = array_splice($get,0,1);
			$key_first = key($first);
			$pages = [];
			if (strpos($key_first,'/') !== FALSE && current($first) == '') {
				$pages = array_values(array_filter(explode('/',$key_first)));
			}
			self::$_cache['request'] = array_merge(['_pages' => $pages],$get);		
		} 
		return self::$_cache['request'];

	}

    /**
     * @param $url
     * @return string
     */
    public static function getYoutubeId($url) {
		if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
			return $match[1];
		}
		return '';
	}

    /**
     * @return mixed
     */
    public static function getIPAddress() {
	    return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @return string
     */
    public static function getJWTAuthorizationToken() {
        $headers = \getallheaders();
        if (isset($headers['Authorization']))
        return substr($headers['Authorization'],7);
        return '';
    }

    /**
     * @return \stdClass
     */
    public static function getJSONRequest() {
        $inputJSON = file_get_contents('php://input');
        if ($inputJSON == '') return new \stdClass();
        $json = \json_decode($inputJSON);
        if (!$json) return new \stdClass();
        return $json;
    }

}