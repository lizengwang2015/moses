<?php
/**
 * 
 * 视图基类
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 */

class Moses_Core_View {
	
	//参数列表
	protected $_vars = array();

	/**
	 * 注册参数
	 * @param string $vKey
	 * @param mixed $vValue
	 */
	public function assign($vKey,$vValue){
		$this->_vars[$vKey] = $vValue;
	}

	/**
	 * 显示模板
	 * @param string $vTpl
	 */
	public function display($vTpl = ''){
		
		//解析到具体模板文件路径
		$tplRealPath = $this->parseTpl($vTpl);
		
		!file_exists($tplRealPath) && E('tpl file not found: '.$tplRealPath);					

		$finalContent = $this->fetch($tplRealPath);
		$this->render($finalContent);
	}
	
	/**
	 * 获取页面带当前的变量列表
	 * 
	 */
	public function getVars(){
		return $this->_vars;
	}

	/**
	 * 分析模板描述，指定模板文件路径
	 * @param string $vTpl
	 */
	public function parseTpl($vTpl = ''){
		
		$tplFileRealPath = '';
		
		if($vTpl == ''){
						
			if(defined('M_CURRENT_MODULE')){
				$tplFileRealPath .=  M_TPL_PATH.M_DS.M_CURRENT_MODULE.M_DS;
			}else{
				$tplFileRealPath .=  M_TPL_PATH.M_DS;
			}
			
			$tplFileRealPath .=  M_CURRENT_CONTROLLER.M_DS;
			$tplFileRealPath .= M_CURRENT_ACTION.M_TPL_EXT;
			
		}else{
			
			$tplList = explode('/',$vTpl);
			
			$tplCount = count($tplList);
			$tplFileRealPath .=  M_TPL_PATH.M_DS;
			
			for($i = 0;$i<$tplCount-1;$i++){
				$tplFileRealPath .= strtolower($tplList[$i]).M_DS;
			}
			
			$tplFileRealPath .= strtolower($tplList[$i]).M_TPL_EXT;
			
		}
		
		return $tplFileRealPath;
	}
	
	/**
	 * 解析模板
	 * @param string $vTplFile 模板文件路径
	 * @return string
	 */
	protected function fetch($vTplFile){
		
		ob_start();
        ob_implicit_flush(0);	

		// 模板阵列变量分解成为独立变量
        extract($this->_vars, EXTR_OVERWRITE);
        
        // 直接载入PHP模板
		include $vTplFile;
		
		return ob_get_clean();		
	}
	
	/**
	 * 渲染页面
	 * @param string $vContent
	 */
	protected function render($vContent){		
        
        header('Content-Type:text/html; charset=UTF-8');  // 网页字符编码        
        header('X-Powered-By:Moses');
              
        // 输出模板文件
        echo $vContent;
	}
		
}
