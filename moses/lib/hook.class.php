<?php
/**
 *
 * 钩子工具类
 *
 * 添加钩子
 * Moses_Lib_Hook::add('before_index',ClassName);
 *
 * 打印钩子
 * Moses_Lib_Hook::get();
 * Moses_Lib_Hook::get('before_index');
 *
 * 执行钩子方法
 * Moses_Lib_Hook::exec('before_index',$param);
 *  
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 * 
 */

class Moses_Lib_Hook{

	//钩子名称
	private static $sTags = array();

	/**
	 * 添加钩子
	 * @param string $vTag       钩子名称
	 * @param string $vClassName 执行的钩子类
	 */
	public static function add($vTag,$vClassName){
		
		!isset(self::$sTags[$vTag]) && self::$sTags[$vTag] = array();		
		self::$sTags[$vTag][] = $vClassName;
		return;
		
	}

	/**
	 * 获取钩子
	 * @param  string $vTag 钩子名称
	 * @return mixed
	 */
	public static function get($vTag = ''){
		if(empty($vTag)) return self::$sTags;
		else return self::$sTags[$vTag];
	}

	/**
	 * 执行钩子
	 * @param  string $vTag     钩子名称
	 * @param  unknown &$vParams 参数
	 * @return 
	 */
	public static function exec($vTag,&$vParams = null){

		if(isset(self::$sTags[$vTag])){
					
			foreach(self::$sTags[$vTag] as $className){
								
				$instance = new $className();
				
				if($instance instanceof Moses_Lib_Hook_Interface){
					
					$result = $instance->run($vParams);
					if($result === false) return;
				}
				
			}
		}
		return;
	}
}

