<?php
/**
 *
 * 字符串过滤工具类
 * 
 * Moses_Lib_Filter::filter('html')
 * 
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version v1.0.0
 */

class Moses_Lib_Filter{
	
	//过滤器类型
	public static $sFilterType = array(		
		'int',
		'float',
		'string',
		'enum',					
		'max',
		'min',
		'length',
		'slashes',
		'html',
		'phptag',
		'script'					
	);
	
	/**
	 * 判断过滤器类型是否存在
	 * @param string $vFilterType
	 * @return boolean
	 */
	public static function isFilterTypeExists($vFilterType){
		return in_array($vFilterType,self::$sFilterType);	
	}
	
	/**
	 * 递归过滤某一中类型的过滤器
	 * @param string $vFilterType
	 * @param mixed $vVal
	 * @param mixed $vArgs
	 * @return mixed
	 */
	public static function filter($vFilterType,$vVal,$vArgs = array()){
	
		if(self::isFilterTypeExists($vFilterType) == false) return null;
		
		if(is_array($vVal)){
				
			foreach($vVal as $key=>$node){
				$result = self::filter($vFilterType,$node,$vArgs);
				if($result){
					$vVal[$key] = $result;
				}
			}
				
			return $vVal;
	
		}else{
					
			$vars = array_merge(array($vVal),$vArgs);
			$methodName = $vFilterType.'Filter';
			
			return forward_static_call_array(array('Moses_Lib_Filter',$methodName),$vars);
			
		
		}
	}
	
	/**
	 * 过滤INT值
	 * @param unknown $vVar	 
	 * @return mixed
	 */
	public static function intFilter($vVar){
		
		return intval($vVar);
	}
	
	/**
	 * 过滤FLOAT值
	 * @param unknown $vVar	 
	 * @return number
	 */
	public static function floatFilter($vVar){
		
		return floatval($vVar);
	}
	
	/**
	 * 过滤String值
	 * @param unknown $vVar	 
	 * @return string
	 */
	public static function stringFilter($vVar){
		
		$value = str_replace(array("\0","%00","\r"), '', $vVar);
		$value = preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','/&(?!(#[0-9]+|[a-z]+);)/is'), array('', '&amp;'), $value);
		$value = str_replace(array("%3C",'<'), '&lt;', $value);
		$value = str_replace(array("%3E",'>'), '&gt;', $value);
		$value = str_replace(array('"',"'","\t",'  '), array('&quot;','&#39;','    ','&nbsp;&nbsp;'), $value);
		
		return trim($value);				
	}
	
	/**
	 * 过滤枚举类型
	 * @param unknown $vVar
	 * @param array $vTargeArray
	 * @return mixed
	 */
	public static function enumFilter($vVar,array $vTargeArray){
		
		$var = trim($vVar);
		
		if(in_array($var, $vTargeArray,true) === true){
			
			return $var;
			
		}else{
			
			return null;
			
		}	
	}	
	
	/**
	 * 过滤最大值
	 * @param number $vVar
	 * @param number $vMax
	 */
	public static function maxFilter($vVar,$vMax){
	
		$var = $max = 0;
	
		if(is_float($vVar)){
				
			$var = floatval($vVar);
			$max = floatval($vMax);
				
		}else if(is_int($vVar)){
				
			$var = intval($vVar);
			$max = intval($vMax);
				
		}
	
		if($var <= $max){
			return $var;
		}else{
			return 0;
		}
	}
	
	/**
	 * 过滤最小值
	 * @param number $vVar
	 * @param number $vMin
	 */
	public static function minFilter($vVar,$vMin){
	
		$var = $min = 0;
	
		if(is_float($vVar)){
	
			$var = floatval($vVar);
			$min = floatval($vMin);
	
		}else if(is_int($vVar)){
	
			$var = intval($vVar);
			$min = intval($vMin);
	
		}
	
		if($var >= $min){
			return $var;
		}else{
			return 0;
		}
	}
	
	/**
	 * 过滤多余字符
	 * @param string $vVar
	 * @param int $vLength
	 * @return string
	 */
	public static function lengthFilter($vVar,$vLength){
		
		$var = trim($vVar);
		$length = intval($vLength);
				
		$var = substr($var, 0,$length);
		
		return $var;
	}
	
	/**
	 * 过滤反斜杠，防止sql注入
	 * @param string $vVar
	 * @return string
	 */
	public static function slashesFilter($vVar){
		
		if(get_magic_quotes_gpc()) return $vVar;
		else return addslashes($vVar);
		
	}
	
	/**
	 * 过滤html字符
	 * @param string $vVar
	 * @return string
	 */
	public static function htmlFilter($vVar){
		
		if (function_exists('htmlspecialchars')) return htmlspecialchars($vVar);
		return str_replace(array("&", '"', "'", "<", ">"), array("&amp;", "&quot;", "&#039;", "&lt;", "&gt;"), $vVar);
				
	}
	
	/**
	 * 过滤PHP标签
	 * @param string $vVar
	 * @return string
	 */
	public static function phptagFilter($vVar){
		
		return str_replace(array('<?','?>'), array('&lt;?','?&gt;'), $vVar);
		
	}
	
	/**
	 * 过滤script脚本
	 * @param string $vVar
	 * @return mixed
	 */
	public static function scriptFilter($vVar){
		
		$value = preg_replace("/(javascript:)?on(click|load|key|mouse|error|abort|move|unload|change|dblclick|move|reset|resize|submit)/i","&111n\\2",$vVar);
		$value = preg_replace("/<script(.*?)>(.*?)<\/script>/si","",$value);
		$value = preg_replace("/<iframe(.*?)>(.*?)<\/iframe>/si","",$value);
		$value = preg_replace ("/<object.+<\/object>/iesU", '', $value);
		
		return $value;
	}
	
}
