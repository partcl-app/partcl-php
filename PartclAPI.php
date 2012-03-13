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
		@param Mixed $tag - tag code to fetch (one tag or array of tags)
		@param Boolean $decodedValueAsJson - if set to true, automated decoding tag value as json string
		@return Boolean|Mixed - tag value or boolean if error
		
		!IMPORTANT! You must set your public web_key perviosly to fetch any data (see setWebKey function)
		
		Examples:
			PartclAPI::setWebKey('<your web_key>');
			echo PartclAPI::get('srv:time.gmt'); // output e.g. 11:58:37:529
			
			Multiple tag at once:
			
			var_dump( PartclAPI::get(Array('srv:time.gmt','srv:all.today.messages')) );
		
	*/
	public static function get($tag = null, $decodedValueAsJson = false) {
		if (empty($tag)) return false;
		if (empty(self::$web_key)) return false;
		
		$output_type = 'val'; //'array'
		
		$_server = rand(1,2); //round server
		$sessionId = rand() . '_' . rand(1, 99999);
		
		$_tags = Array();
		if (is_array($tag))
		{
			$output_type = 'array';
			
			foreach($tag as $x)
			{
				$x = trim($x);
				
				if ((!empty($x)) && (!in_array($x, $_tags)))
					$_tags[] = trim($x);
			}
		}
		else
			$_tags[] = trim($tag);
				
		$url = 'http://push'.$_server.'.partcl.com/poll?tags='.implode(',', $_tags).'&sessionId='.$sessionId.'&clientId='.self::$web_key.'&_='.microtime(true);
		$_resp = file_get_contents($url);
		
		try
		{
			$obj = json_decode($_resp, true);
			$_return = Array();
			
			if (($obj['status'] == 'OK') && (array_key_exists('data', $obj)))
			{
				foreach($_tags as $x)
				{
					if (array_key_exists($x, $obj['data']))
					{
						if ($decodedValueAsJson === true)
							$_return[ $x ] = json_decode($obj['data'][ $x ], true);
						else
							$_return[ $x ] = $obj['data'][ $x ];
					}
				}
				
				if ($output_type == 'array')
					return $_return;
				else
					return array_shift($_return);
			}
			else
				return false;
				
		}catch(Exception $x){
			return false;
		}
	}
}