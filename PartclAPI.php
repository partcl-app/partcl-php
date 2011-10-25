<?php

class PartclAPI {
	private static $publish_key = '';
	
	public static function setPublishKey($key) {
		self::$publish_key = $key;
	}
	
	public static function send($tag, $val) {
		if(is_array($val) ) {
			$val = json_encode($val);
		}
		$url = 'http://partcl.com/publish?publish_key='. self::$publish_key .'&id='. $tag .'&value='. $val;
		
		file_get_contents($url);
	}
}