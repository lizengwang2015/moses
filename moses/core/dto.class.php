<?php
/**
* 
* 数据传输对象基类
* 
* @author lizengwang <lizengwang@gmail.com>
* @version 1.0.3
*/

class Moses_Core_Dto{		
	
	protected $id = 0;
	
	protected $create_time = 0;
	
	protected $last_modify_time = 0;
	
	protected $assignments = array();
	
	/**
	 * set拦截器，拦截所有的set操作，保存在assignments当中
	 * @param string $vName
	 * @param mixed $vValue
	 */
	public function __set($vName,$vValue){
				
		if(property_exists($this, $vName) == true){
			$this->$vName = $vValue;
			
			$this->assignments[$vName] = $vValue;
		}
	}
	
	/**
	 * get拦截器，拦截所有的get操作，从assignments当中获取
	 * @param unknown $vName
	 * @return multitype:|NULL
	 */
	public function __get($vName){
		
		if(array_key_exists($vName, $this->assignments)){
			
			return $this->assignments[$vName];
			
		}else{
			
			return null;			
		}
	}
	
	/**
	 * 最终返回信息
	 * @return multitype:
	 */
	public function toArray(){		
		return $this->assignments;
	}
	

}
