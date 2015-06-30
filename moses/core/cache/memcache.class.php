<?php
/**
 *
 * memcache缓存基类，内置方式
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 */

class Moses_Core_Cache_Memcache implements Moses_Core_Cache_Interface{
	
	//memcache实例
	private $_memcache = null;
	
	//是否压缩数据
	private $_isCompressed = false;
	
	/**
	 * 初始化缓存
	 * @return void
	 */
	public function __construct($vOptions){
		
		if(!extension_loaded('memcache')){
			E('The memcache extension must be loaded for using this function !');
		}
		
		$this->_memcache = new Memcache();		
		
		if(isset($vOptions['setting'])){
			
			$setting = $vOptions['setting'];
			
			//初始化配置
			if(isset($setting['OPT_COMPRESSION'])){
				$this->_isCompressed = $setting['OPT_COMPRESSION'];
			}
			
		}
				
		$servers = $vOptions['servers'];
        foreach($servers as $server){
        	
        	//是否持久连接
        	$persistent =  true;
        	isset($server['persistent'])  && $persistent = $server['persistent'];
        	
        	//权重
        	$weight =  1;
        	isset($server['weight'])  && $weight = $server['weight'];
        	
        	//超时(s)
        	$timout =  1;
        	isset($server['timout'])  && $timout = $server['timout'];
        	
            //连接失败重新连接时间间隔（s）
        	$retry_interval = 15;
        	isset($server['retry_interval'])  && $retry_interval = $server['retry_interval'];
        	
        	//控制此服务器是否可以被标记为在线状态，默认允许故障转移
        	$status = true;
        	isset($server['status'])  && $status = $server['status'];
        	
            $this->_memcache->addServer($server['host'], $server['port'], $persistent,$weight, $timout,$retry_interval);
            
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
		return $this->_memcache->add($vKey, $vValue,$this->_isCompressed,$vTime);
	}
	
	/**
	 * get缓存
	 * @param string $vKey
	 * @return mixed
	 */
	public function get($vKey){		
		return $this->_memcache->get($vKey);
	}
	
	/**
	 * 清空某一个key的缓存
	 * @param string $vKey
	 * @return boolean
	 */
	public function clear($vKey){
		return $this->_memcache->delete($vKey);		
	}
	
	/**
	 * 清空该缓存实例的所有缓存
	 * @return boolean
	 */
	public function clearAll(){
		return $this->_memcache->flush();	
	}
	
}
