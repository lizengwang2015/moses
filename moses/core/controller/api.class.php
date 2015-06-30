<?php
/**
* 
* Api控制器类
* 
* 增加对于get，post方式的判断
* 增加了json、xml的返回格式判断
* 增加了URL后缀【M_URL_EXT】的处理
* 
* @author lizengwang <lizengwang@gmail.com>
* @version 1.0.3
*/

abstract class Moses_Core_Controller_Api{
	
	//请求方法
	private $_method = '';
	
	//默认请求方法
	private $_defaultMethod = 'get';
	
	//可接受的请求方法列表
	private $_allowedMethod = array('get','post');
	
	//请求返回数据类型
	private $_type = '';
	
	//默认请求返回数据类型
	private $_defaultType = 'json';
	
	//可接受的请求返回数据类型
	private $_allowedType = array('json','xml');	
	
	
	/**
	 * 初始化type和method
	 * @return void
	 */
    public function __construct(){
    	    	
    	//请求数据类型
    	if(M_URL_EXT != '' && in_array(M_URL_EXT, $this->_allowedType) !== false){
    		$this->_type = M_URL_EXT;
    	}else{
    		$this->_type = $this->_defaultType;
    	}
    	
    	
    	//请求方法
    	if(M_IS_GET == true){			
    		$this->_method = 'get';    		
    	}else if(M_IS_POST == true){    		    		   		
    		$method = 'post';    	
    	}else{    		
    		$this->_method = $this->_defaultMethod;
    	}    	    	
    	
    	//默认认证
    	if($this->auth() == false){
    		$this->response(1000,'认证失败！');
    	}
    	
    }
        
    /**
     * 最终输出
     * @param int $vCode
     * @param string $vMsg
     * @param array $vData
     */    
    public function response($vCode,$vMsg = '',$vData = array()){
    	
    	$code = intval($vCode);
    	$result = array(
			'code' => $code,
    		'msg' => $vMsg,
    		'data' => $vData
    	);
    	 
    	$method = 'response'.ucfirst($this->_type);
    	
    	$this->$method($result);
    }
    
	/**
	 * JSON数据输出
	 * @param mixed $vResult
	 */
    protected function responseJson($vResult){
    	
    	header('Content-type: text/json');    	
    	echo json_encode($vResult);
    	
    	
    	exit;
    	
    }
    
    /**
     * xml数据输出
     * @param mixed $vResult
     */
    protected function responseXml($vResult){
    	
    	header('Content-Type:text/xml');    	
    	
    	$xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
    	$xml .= "<root>";
    	$xml .= Moses_Lib_Util::xmlEncode($vResult);
    	$xml .= "</root>";
    	echo $xml;
    	
    	exit;
    }
    
    
	/**
	 * 空方法捕捉
	 * @return void
	 */
	public function __call($vMethod,$vArgs){	
			
		$this->response(1001,'请求方法不存在！');
				
	}
	
    
    /**
     * 认证方法
     * @return boolean
     */
    abstract protected function auth();
	
}
