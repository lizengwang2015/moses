<?php
/**
 *
 * CACHE基类
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 */

class Moses_Core_Cache{

	//全局cache前缀
	private $_keyPrefix = '';
	
	//cache配置信息
	private $_instanceConfig = array();
	
	//缓存实例类
	private $_instance = null;
	
	/**
	 * 初始化缓存实例
	 * @param array $vConfig
	 * @return mixed
	 */
	public function __construct($vConfig){
		
		//保存配置信息
		$this->_instanceConfig = $vConfig;
		
		//初始化缓存前缀信息
		isset($vConfig['PREFIX']) && $this->_keyPrefix = $vConfig['PREFIX'];
		
		//实例化不同类型的
		$type = strtolower($vConfig['TYPE']);		
		switch($type){
			case 'file':
				$this->_instance = new Moses_Core_Cache_File($vConfig['OPTIONS'][0]);				
				break;
			case 'memcache':
				if(extension_loaded('memcached')){					
					$this->_instance = new Moses_Core_Cache_Memcached($vConfig['OPTIONS']);
				}else if(extension_loaded('memcache')){
					$this->_instance = new Moses_Core_Cache_Memcache($vConfig['OPTIONS']);
				}else{
					E('memcache or memcached client is not found.');
				}					
				break;			
			default:
				E('cache type is not found.'.$type);
				break;				
		}		 
	}
	
	/**
	 * set缓存
	 * @param string $vKey
	 * @param mixed $vValue
	 * @param int $vTime
	 * @return void
	 */
	public function set($vKey,$vValue,$vTime = 0){
		$this->_instance->set($this->_keyPrefix.$vKey,$vValue,$vTime);	
	}
	
	/**
	 * get缓存
	 * @param string $vKey
	 * @return mixed
	 */
	public function get($vKey){		
		return $this->_instance->get($this->_keyPrefix.$vKey);
	}
	
	/**
	 * 清空某一个key的缓存
	 * @param string $vKey
	 * @return boolean
	 */
	public function clear($vKey){
		return $this->_instance->clear($this->_keyPrefix.$vKey);
	}
	
	/**
	 * 清空该缓存实例的所有缓存
	 * @return boolean
	 */
	public function clearAll(){
		return $this->_instance->clearAll();
	}
	
}

