<?php
/**
 * 核心配置文件
 * 
 * 多个配置文件，同一个键值覆盖，不同键值追加
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 */

return array(

	//调试信息显示	
	'M_DEBUG_TOKEN_SALT' => 'baiduck',
	
	//模块列表
	'M_MODULE_LIST'	     => array(),	//只有存在MODULE的时候才需要配置
	
	/**
	 * 数据库配置
	 * 
	 */	
	'M_DB' => array(
		'TYPE'			=>'',					//mysql
		'HOST'			=>'',					//localhost
		'NAME'			=>'',					//dbname
		'USER'			=>'',					//dbuser
		'PWD'			=>''					//dbpassword
	),
		
	'M_CACHE' => array(
		'PREFIX'		=>'',					//moses_
		'TYPE'			=>'',					//file,memcache
		'OPTIONS'		=>array()	            //[file]:array('D:/www/Moses/tmp/cache'),[memcache]:
	)
		
);
