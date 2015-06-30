<?php
/**
* 
* 业务数据基类
* 
* @author lizengwang <lizengwang@gmail.com>
* @version 1.0.3
*/

abstract class Moses_Core_Service{	

	//默认dao标记
	const DTO_SIGN = 'ForDto';
	
	//
	protected $_dao = null;
	
	/**
	 * 构造函数
	 */
	public function __construct(){
		$this->_dao = $this->initDao();
	}
	
	/**
	 * 函数自动转换
	 * @param string $vMethod
	 * @param mixed $vArg
	 * @return mixed
	 */
	public function __call($vMethod,$vArg){

		//是否转换输出
		$revertOut = false;
		
		//参数个数
		$argCount = count($vArg);

		$dtoSignPosition = strpos($vMethod,self::DTO_SIGN);
		
		if($dtoSignPosition !== false && $argCount >= 1){
			
			//真实执行方法名称
			$realMethodName = substr($vMethod,0,$dtoSignPosition);
						
			//过滤入口，转换实体类为数组，该实体类必须为Dto实例
			foreach($vArg as $key=>$node){
				
				if($node instanceof Moses_Core_Dto){							
					$vArg[$key] = $node->toArray();
				}
			}
			
			//参数最后一个元素
			$lastArg = $vArg[$argCount-1];

			//出口需要转换,最后一个参数必须为类名称，并且类存在
			if(is_string($lastArg) && class_exists($lastArg)){
				
				$dtoClassName = array_pop($vArg);
				$revertOut = true;
			}
			
			$result = call_user_func_array(array($this->_dao,$realMethodName), $vArg);
			
			if($revertOut == true){
				return $this->record2Dto($result, $dtoClassName);
			}else{
				return $result;
			}
			
		}else{
			
			return call_user_func_array(array($this->_dao,$vMethod), $vArg);
			
		}			
	
	}	
	
	
	/**
	 * 记录转Dto
	 * @param 数据库记录 $vResult
	 * @param Dto类名称 $vDtoClassName
	 */
	protected function record2Dto($vResult,$vDtoClassName){
	
		if(!is_array($vResult)) return $vResult;
	
		$item = current($vResult);
		if(is_array($item) == true){
			$result = array();
			foreach($vResult as $node){
				$obj = self::singleRecord2Dto($node, $vDtoClassName);
				if($obj != null) $result[] = $obj;
			}
				
			return $result;
				
		}else{
			return self::singleRecord2Dto($vResult, $vDtoClassName);
		}
	
	}
	
	/**
	 * 单条记录转DTO
	 * @param array $vSingleRecord
	 * @param string $vDtoClassName
	 * @return unknown|NULL
	 */
	protected function singleRecord2Dto(array $vSingleRecord,$vDtoClassName){
	
		if(class_exists($vDtoClassName)){
				
			$dto = new $vDtoClassName();
				
			foreach($vSingleRecord as $key=>$node){
				$dto->$key = $node;
			}
	
			return $dto;
				
		}else{
				
			return null;
		}
	
	}
	
	/**
	 * 初始化dao
	 */
	abstract function initDao();
}
