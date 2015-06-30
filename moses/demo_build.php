<?php 
/**
 *
 * demo自动生成文件
 *
 * @author lizengwang<lizengwang@gmail.com>
 * @version 1.0.3
 */

$root = realpath(dirname(__DIR__)).DIRECTORY_SEPARATOR;

$dirs = array(
	'conf',
	'controller/test1',
	'dao',
	'lib',	
	'static/tpl/index',
	'static/tpl/test1/index',
	'tmp/log'								
);

//创建目录
foreach($dirs as $node){
	$realDir = $root.$node;
	@mkdir($realDir,0755,true);	
}

/******************************* 新增index.php文件 *******************************/ 
$indexContent = <<<INDEX
<?php
/**
* 入口文件
* 
* @version 1.0.3
*/

//启动调试
define('M_DEBUG',true);

//包含启动脚本
require('./moses/bootstrap.php');

?>
INDEX;
file_put_contents($root.'index.php', $indexContent);

/******************************* 新增my.inc.php文件 *******************************/
$myIncContent = <<<INC
<?php
/**
 *
* 实际应用配置文件
*
* @author lizengwang <lizengwang@gmail.com>
* @version 1.0.3
*/

return array(

		//模块列表
		'M_MODULE_LIST'	     => array('test1'),	//只有存在MODULE的时候才需要配置

);

INC;
file_put_contents($root.'conf/my.inc.php', $myIncContent);

/******************************* 新增controller/index.class.php文件 *******************************/ 
$indexControllerContent = <<<INDEXC
<?php

class Controller_Index extends Moses_Core_Controller{
	
	public function indexAction(){
		\$this->assign('name','hello world index without login');
		\$this->display();
	}
	
}

?>
INDEXC;
file_put_contents($root.'controller/index.class.php', $indexControllerContent);


/******************************* 新增controller/test1/index.class.php文件 *******************************/
$test1ControllerContent = <<<INDEXC
<?php

class Controller_Test1_Index extends Moses_Core_Controller_Login{

	public function indexAction(){
		\$this->assign('name','hello world test1 with default login');
		\$this->display();
	}
		
	protected function isLogin(){
		return true;
	}	
}
?>
INDEXC;
file_put_contents($root.'controller/test1/index.class.php', $test1ControllerContent);


/******************************* 新增模板文件 *******************************/
$indexTplContent = <<<INDEXT
<?php

echo \$name;

?>

INDEXT;
file_put_contents($root.'static/tpl/index/index.tpl.php', $indexTplContent);
file_put_contents($root.'static/tpl/test1/index/index.tpl.php', $indexTplContent);

//设置tmp目录权限
chmod($root.'tmp', 0777);
chmod($root.'tmp/log', 0777);


echo '<a href="../" target="_blank">index</a><br/>';
echo '<a href="../test1/" target="_blank">test1</a><br/>';
