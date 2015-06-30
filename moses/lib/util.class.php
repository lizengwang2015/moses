<?php
/**
 * 
 * 框架通用工具类
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 */

class Moses_Lib_Util{
	
 	/**
 	 * 遍历加载目录下的配置文件（.inc.php文件）
 	 * @param string $vDir
 	 * @return 
 	 */
 	public static function loadConfDir($vDir){
 		
 		$dir = $vDir;
 		
 		if(is_dir($dir)){
 			
 			$dh = opendir($dir); 			
 			if($dh){
 				while(($file = readdir($dh)) !== false){
 					if(is_file($dir.$file) && $file != '.' && $file != '..'){
 						$extLength = strlen(M_CONFIG_EXT);
 						if(substr($file,-$extLength) === M_CONFIG_EXT){
 							$configArray = include $dir.$file;
 							foreach($configArray as $key=>$item){ 								
 								C($key,$item);
 							} 							
 						}
 					}
 				} 				
 				closedir($dh);
 			}
 		}
 		
 		return;
 	}
 	
 	/**
 	 * 获取|验证是否开启调试token
 	 * @param string $vToken
 	 * @return boolean|string
 	 */
 	public static function debugToken($vToken = null){
 	
 		$time = date('YmdH');
 		$token = md5($time.C('M_DEBUG_TOKEN_SALT'));
 		
 		if(is_null($vToken) == true){ 		
 			return $token; 			
 		}else{ 			
 			if($vToken == $token) return true;
 			else return false;
 		}
 	
 	} 	
	
 	/**
 	 * 记录运行时间
 	 * @param string $vStartTag
 	 * @param string $vEndTag
 	 * @param number $vDec 保留位数，默认4位
 	 * @return Ambigous <string, mixed>
 	 */
 	public static function runtime($vStartTag,$vEndTag = '',$vDec = 4){
 	
 		static $gTime = array();
 	
 		if($vEndTag == ''){
 				
 			$gTime[$vStartTag] = microtime(true);
 			$result = $gTime[$vStartTag];
 				
 		}else{
 				
 			if(isset($gTime[$vStartTag]) && isset($gTime[$vEndTag])){
 				$result =  $gTime[$vEndTag] - $gTime[$vStartTag];
 			}
 		}
 	
 		if(isset($result)){ 			
 			return number_format($result,$vDec);
 		}
 	
 		
 	}
 	
	/**
	 * 数组转xml字符串
	 * @param array $vResult
	 * @return string
	 */
	public static function xmlEncode(array $vResult){
		
		$xml = $attr = '';
		
		foreach($vResult as $key => $value) {
			
			//判断键值对，如果是数字键值不允许
			if(is_numeric($key)) {
				$attr = " id='" . $key . "'";
				$key = "item";
			}
			
			$xml .= "<{$key}{$attr}>";
			//以递归形式返回，主要是因为数组在xml中显示是array，必须显示出来具体键值对
			$xml .= is_array($value) ? self::xmlEncode($value) : $value;
			$xml .= "</{$key}>\n";
		}
		
		return $xml;
	}
	
	/**
	 * 隐藏IP前两位
	 * @param string $vIP
	 * @return string
	 */
	public static function hideIP($vIP){
		
		if(Moses_Lib_Validater::ipValidater($vIP) == true){
			
			$hostArray = explode('.', $vIP);
			$host = '*.*.'.$hostArray[2].'.'.$hostArray[3];
			
		}else{
			
			$host = $vIP;
		}
		
		return $host;
	}
	
	/**
	 * 打印数组定义字符串
	 * @param array $vArray
	 * @return string
	 */
	public static function printArray(array $vArray){
	
		$result = '';	
		foreach($vArray as $key=>$node){
			
			if(is_int($key) == false){
				$result .= "'$key'=>";
			}else{
				$result .= "$key=>";
			}
			
			if(is_array($node) !== false){

				$result .= 'array(';
				$result .= Moses_Lib_Util::printArray($node);
				$result .= '),';
				
			}else{
								
				if(empty($node)){
					if(is_int($node)){
						$result .= '0,';
					}else if(is_string($node)){
						$result .= '\'\',';
					}else{
						$result .= 'null,';
					}	
				}else{
					if(is_int($node)){
						$result .= "$node,";
					}else if(is_string($node)){
						$result .= "'$node',";
					}					
				}
				
			}
		}
		
		return $result;	
	}
}
