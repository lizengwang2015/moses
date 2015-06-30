<?php
/**
 *
 * 文件缓存基类
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 */

class Moses_Core_Cache_File implements Moses_Core_Cache_Interface{
	
	//数据保存分隔符
	const DATA_SEPERATER = '~~';
	
	//文件缓存文件夹
	private $_fileCacheDir = '';
	
	/**
	 * 初始化文件缓存
	 * @param string $vCacheDir
	 * @return void
	 */
	public function __construct($vCacheDir){
		
		if(!empty($vCacheDir) && file_exists($vCacheDir) == true){
			
			$this->_fileCacheDir = rtrim($vCacheDir,M_DS).M_DS;
			
		}else{
			
			E('cache dir is not found.'.$vCacheDir);
		}
	}
	
	/**
	 * 获取文件缓存路径
	 * @return string
	 */
	public function getCachDir(){
		
		return $this->_fileCacheDir;
	}
	
	/**
	 * set缓存
	 * @param string $vKey
	 * @param mixed $vValue
	 * @param int $vTime,秒数
	 * @return void
	 */
	public function set($vKey,$vValue,$vTime = 0){
		
		$realFileName = $this->getFileName($vKey);
		
		@file_put_contents($realFileName,time().self::DATA_SEPERATER.$vTime.self::DATA_SEPERATER.serialize($vValue));
		@chmod($realFileName,0777);
		
		clearstatcache();
		
		return;
	}
	
	/**
	 * get缓存
	 * @param string $vKey
	 * @return mixed
	 */
	public function get($vKey){
		
		$realFileName = $this->getFileName($vKey);
		
		//缓存不存在
		if(file_exists($realFileName) == false) return false;
		
		$totalData = file_get_contents($realFileName);
		$contentArray = explode(self::DATA_SEPERATER,$totalData);
		
		$createTime = $contentArray[0];
		$expireTime = $contentArray[1];
		$dataContent = $contentArray[2];
		
		//如果没有设置过期时间，直接返回缓存数据
		if(intval($expireTime) == 0) return @unserialize($dataContent);
		
		//如果设置了过期时间，而且已经过期，返回false
		if(time() > ($createTime + $expireTime)){
			
			@unlink($realFileName);
			return false;
			
		}else{
			return @unserialize($dataContent);
		}
		
		
	}
	
	/**
	 * 清空某一个key的缓存
	 * @param string $vKey
	 * @return boolean
	 */
	public function clear($vKey){
		
		$realFileName = $this->getFileName($vKey);
		
		//缓存不存在
		if(file_exists($realFileName) == false) return true;
		
		@unlink($realFileName);
		return true;
	}
	
	/**
	 * 清空该缓存实例的所有缓存
	 * @return boolean
	 */
	public function clearAll(){
		
		set_time_limit(3600);
		$path = opendir($this->_fileCacheDir);
		while (false !== ($filename = readdir($path))) {
			if ($filename !== '.' && $filename !== '..') {
				@unlink($this->_fileCacheDir.$filename);
			}
		}
		closedir($path);
		return true;
	}
	
	/**
	 * 获取缓存文件名
	 * @param  string $vKey 缓存键值
	 * @return string
	 */
	private function getFileName($vKey) {
		
		$filename = md5($vKey);
		$filename = $this->_fileCacheDir.$filename.'.cache';
		return $filename;		
		
	}
	
}
