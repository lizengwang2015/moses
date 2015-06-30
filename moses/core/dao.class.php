<?php
/**
* 
* 数据模型基类
* 
* @author lizengwang <lizengwang@gmail.com>
* @version 1.0.3
*/

class Moses_Core_Dao{		
		
	//默认数据库实例
	protected $_db = null;
	
	//默认cache实例
	protected $_cache = null;
	
	/**
	 * 初始化数据库操作实例
	 * @param  array  $vConfig 
	 * @return Moses_Core_Db
	 */
	public function initDb(array $vConfig){
		
		$dsn = "{$vConfig['TYPE']}:host={$vConfig['HOST']};dbname={$vConfig['NAME']};";
		return new Moses_Core_Db($dsn,$vConfig['USER'],$vConfig['PWD']);
	}
	
	/**
	 * 初始化cache操作实例
	 * @param array $vConfig
	 * @return Moses_Core_Cache
	 */
	public function initCache(array $vConfig){
		return new Moses_Core_Cache($vConfig);		
	}
	
	/**
	 * 方法拦截器
	 * @param string $vMethod
	 * @param array $vArgs
	 * @return mixed
	 */
	public function __call($vMethod,$vArgs){
		
		//首先判断是否为cache请求
		$cacheSignPosition = strpos($vMethod,'Cache');
		
		if($cacheSignPosition !== false){
			
			if($this->_cache == null){
				
				E('cache instance is not init');
				
			}else{
				
				$realMethodName = substr($vMethod, 0,$cacheSignPosition);
				return call_user_func_array(array($this->_cache,$realMethodName), $vArgs);
			}
			
		}else{
			
			//再次判断是否为db请求			
			if($this->_db == null){
									
				E('db instance is not init');
									
			}else{
									
				return call_user_func_array(array($this->_db,$vMethod), $vArgs);
									
			}
					
		}
	}
	
	
		
}
