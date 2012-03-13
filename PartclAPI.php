<?php

class PartclAPI {
	private static $publish_key = '';
	private static $web_key = '';
	
	public static function setPublishKey($key = null) {
		if (empty($key)) return false;
		self::$publish_key = $key;
	}
	
	public static function setWebKey($key = null) {
		if (empty($key)) return false;
		self::$web_key = $key;
	}
	
	public static function send($tag = null, $val) {
		if (empty($tag)) return false;
		
		if(is_array($val) ) {
			$val = json_encode($val);
		}
		else {
			$val = urlencode($val);
		}
		$url = 'http://partcl.com/publish?publish_key='. self::$publish_key .'&id='. $tag .'&value='. $val;
		
		file_get_contents($url);
	}
	
	/*
		@param $tag Mixed - tag code to fetch
		@return Boolean|Mixed - tag value or boolean if error
		
		!IMPORTANT! You must set your public web_key perviosly to fetch any data (see setWebKey function)
		
		Examples:
			PartclAPI::setWebKey('<your web_key>');
			echo PartclAPI::get('srv:time.gmt'); // output e.g. 11:58:37:529
		
	*/
	public static function get($tag = null) {
		if (empty($tag)) return false;
		if (empty(self::$web_key)) return false;
		
		$_server = rand(1,2); //round server
		$sessionId = rand() . '_' . rand(1, 99999);
				
		$url = 'http://push'.$_server.'.partcl.com/poll?tags='.trim($tag).'&sessionId='.$sessionId.'&clientId='.self::$web_key.'&_='.microtime(true);
		$_resp = file_get_contents($url);
		
		try
		{
			$obj = json_decode($_resp, true);
			
			if (($obj['status'] == 'OK') && (array_key_exists('data', $obj)) && (array_key_exists($tag, $obj['data'])))
			{
				return $obj['data'][ $tag ];
			}
			else
				return false;
				
		}catch(Exception $x){
			return false;
		}
	}
}