<?php
/**
 *
* CACHE接口
*
* @author lizengwang <lizengwang@gmail.com>
* @version 1.0.3
*/

interface Moses_Core_Cache_Interface{

	/**
	 * set缓存
	 * @param string $vKey
	 * @param mixed $vValue
	 * @param int $vTime
	 * @return void
	 */
	public function set($vKey,$vValue,$vTime = 0);

	/**
	 * get缓存
	 * @param string $vKey
	 * @return mixed
	 */
	public function get($vKey);

	/**
	 * 清空某一个key的缓存
	 * @param string $vKey
	 * @return boolean
	 */
	public function clear($vKey);

	/**
	 * 清空该缓存实例的所有缓存
	 * @return boolean
	 */
	public function clearAll();

}
