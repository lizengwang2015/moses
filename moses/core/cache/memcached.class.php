<?php
/**
 *
 * memcache缓存基类,LIB方式
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 */

class Moses_Core_Cache_Memcached implements Moses_Core_Cache_Interface{
	
	//memcache实例
	private $_memcached = null;
	
	/**
	 * 初始化缓存
	 * @return void
	 */
	public function __construct($vOptions){
		
		if(!extension_loaded('memcached')){
			E('The memcached extension must be loaded for using this function !');
		}
		
		$this->_memcached = new Memcached();
		
		if(isset($vOptions['setting'])){
				
			$setting = $vOptions['setting'];
				
			//初始化配置
			if(isset($setting['OPT_COMPRESSION'])){
				$this->_memcached->setOption(Memcached::OPT_COMPRESSION,$setting['OPT_COMPRESSION']);
			}
			if(isset($setting['OPT_RETRY_TIMEOUT'])){
				$this->_memcached->setOption(Memcached::OPT_RETRY_TIMEOUT,$setting['OPT_RETRY_TIMEOUT']);
			}
			if(isset($setting['OPT_DISTRIBUTION'])){
				$this->_memcached->setOption(Memcached::OPT_DISTRIBUTION,$setting['OPT_DISTRIBUTION']);
			}
			if(isset($setting['OPT_LIBKETAMA_COMPATIBLE'])){
				$this->_memcached->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE,$setting['OPT_LIBKETAMA_COMPATIBLE']);
			}
			if(isset($setting['OPT_BINARY_PROTOCOL'])){
				$this->_memcached->setOption(Memcached::OPT_BINARY_PROTOCOL,$setting['OPT_BINARY_PROTOCOL']);
			}
				
		}
				
		$servers = $vOptions['servers'];		
		foreach($servers as $server){	
			
			$weight = 1;
			isset($server['weight']) && $weight = $server['weight'];
					
			$this->_memcached->addServer($server['host'], $server['port'],$weight);
		}		
	}
	
	/**
	 * set缓存
	 * @param string $vKey
	 * @param mixed $vValue
	 * @param int $vTime,秒数
	 * @return void
	 */
	public function set($vKey,$vValue,$vTime = 0){
		return $this->_memcached->set($vKey, $vValue, $vTime);		
	}
	
	/**
	 * get缓存
	 * @param string $vKey
	 * @return mixed
	 */
	public function get($vKey){		
		return $this->_memcached->get($vKey);
	}
	
	/**
	 * 清空某一个key的缓存
	 * @param string $vKey
	 * @return boolean
	 */
	public function clear($vKey){
		return $this->_memcached->delete($vKey);		
	}
	
	/**
	 * 清空该缓存实例的所有缓存
	 * @return boolean
	 */
	public function clearAll(){
		return $this->_memcached->flush();		
	}
	
}
