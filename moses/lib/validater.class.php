<?php
/**
 *
 * 字段验证工具类
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version v1.0.1
 */


class Moses_Lib_Validater{
	
	/**
	 * 邮箱验证
	 * @param unknown $vVar
	 * @return number
	 */
	public static function emailValidater($vVar){
		
		$rule = '/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(?:.[a-zA-Z0-9_-]{2,})+$/';
		return preg_match($rule,$vVar);
	}
	
	/**
	 * url验证
	 * @param unknown $vVar
	 * @return number
	 */
	public static function urlValidater($vVar){
		
		$rule = '/^(https|http|ftp|rtsp|mms):\/\/[a-zA-Z0-9_-]+(?:.[a-zA-Z0-9_-]{2,})+(:[0-9]{1,4})?([\w-\.\/?%&=]*)?$/';
		return preg_match($rule,$vVar);
	}
	
	/**
	 * IP验证
	 * @param unknown $vVar
	 * @return number
	 */
	public static function ipValidater($vVar){
		
		$rule = '/^(?:((?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d))))$/';
		return preg_match($rule,$vVar);
	}
	
	/**
	 * qq验证
	 * @param unknown $vVar
	 * @return number
	 */
	public static function qqValidater($vVar){
		
		$rule = '[1-9][0-9]{4,}';
		return preg_match($rule,$vVar);
	}
	
	/**
	 * md5验证
	 * @param unknown $vVar
	 * @return number
	 */
	public static function md5Validater($vVar){
	
		$rule = '/^[a-z0-9]{32}$/';
		return preg_match($rule,$vVar);
	}
}
