<?php
/**
 *
 * DB基类
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 */

class Moses_Core_Db{
	
	//数据库连接
	private $_conn = null;
	
	//存取方式
	private $_fetchMode = PDO::FETCH_ASSOC;	

	/**
	 * 初始化PDO数据库
	 * @param string $vDsn      
	 * @param string $vUserName 
	 * @param string $vPwd      
	 * @param array $vOptions  
	 */
	public function __construct($vDsn,$vUserName,$vPwd,$vOptions = array()){
		
		!extension_loaded('pdo') && E('The PDO extension is required for this adapter but the extension is not loaded');    	

		//设置编码，默认utf8
		$driverOptions = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "UTF8"');		
		try{            
            
            $this->_conn = new PDO($vDsn,$vUserName,$vPwd,array_merge($vOptions,$driverOptions));
            $this->_conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
            $this->_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }catch (PDOException $e){
            E('Init PDO failed.Msg:'.$e->getMessage());
        }
	}
	
	/**
	 * 设置存取方式
	 * @param int $mode 
	 * @return void
	 */
	public function setFetchMode($mode){

        !extension_loaded('pdo') && E('The PDO extension is required for this adapter but the extension is not loaded');        

        switch ($mode) {
            case PDO::FETCH_LAZY:
            case PDO::FETCH_ASSOC:
            case PDO::FETCH_NUM:
            case PDO::FETCH_BOTH:
            case PDO::FETCH_NAMED:
            case PDO::FETCH_OBJ:
                $this->_fetchMode = $mode;
                break;
            default:
                 $this->_fetchMode = PDO::FETCH_ASSOC;	
                break;
        }
    }

	public function beginTransaction(){$this->_conn->beginTransaction();}
    public function commit(){$this->_conn->commit();}
    public function rollBack(){$this->_conn->rollBack();}
	public function getQuoteIdentifierSymbol(){return '`';}
	public function lastInsertId(){return $this->_conn->lastInsertId();}

	/**
	 * 插入操作
	 * @param  string $vTable 
	 * @param  array  $vBind  
	 * @return int         
	 */
	public function insert($vTable, array $vBind = array()){
        
        $cols = array();
        $vals = array();
        foreach ($vBind as $col => $val) {
            $cols[] = $this->_quoteIdentifier($col);
            $vals[] = '?';            
        }

        $sql = "INSERT INTO "
             . $this->_quoteIdentifier($vTable)
             . ' (' . implode(', ', $cols) . ') '
             . 'VALUES (' . implode(', ', $vals) . ')';
		
        $stmt = $this->_query($sql, array_values($vBind));        
        return $stmt->rowCount();        
    }

    /**
     * 更新操作
     * @param  string $vTable 
     * @param  array  $vBind  
     * @param  string $vWhere 
     * @return int         
     */
	public function update($vTable, array $vBind = array(), $vWhere = ''){
        $set = array();
        $i = 0;
        foreach($vBind as $col => $val){
            $set[] = $this->_quoteIdentifier($col) . ' = ?' ;
        }
        $sql = "UPDATE "
             . $this->_quoteIdentifier($vTable)
             . ' SET ' . implode(', ', $set)
             . (($vWhere) ? " WHERE $vWhere" : '');
        $stmt = $this->_query($sql, array_values($vBind));
        return $stmt->rowCount();      
    }

    /**
     * 删除操作
     * @param  string $vTable 
     * @param  string $vWhere 
     * @return int        
     */
	public function delete($vTable, $vWhere = ''){
        
        $sql = "DELETE FROM "
             . $this->_quoteIdentifier($vTable)
             . (($vWhere) ? " WHERE $vWhere" : '');
        $stmt = $this->_query($sql);
        return $stmt->rowCount();        
    }

    /**
     * 查询值操作
     * @param  string $vSql  
     * @param  array  $vBind 
     * @return mixed       
     */
	public function fetchValue($vSql, array $vBind = array()){
        $stmt = $this->_query($vSql, $vBind);
        return $stmt->fetchColumn();        
    }

    /**
     * 查询一行记录操作
     * @param  string $vSql  
     * @param  array  $vBind 
     * @return array        
     */
    public function fetchRow($vSql, array $vBind = array()){
        
        $stmt = $this->_query($vSql, $vBind);        
        return $stmt->fetch();        
    }

 	/**
     * 查询列表操作
     * @param string  $vSql  
     * @param  array  $vBind 
     * @return array      
     */
	public function fetchAll($vSql, array $vBind = array()){
      
        $stmt = $this->_query($vSql, $vBind);
        return $stmt->fetchAll();        
    }
    
    /**
     * 执行操作
     * @param  string $vSql 
     * @return int       
     */
	public function exec($vSql){
        try{
            $affected = $this->_conn->exec($vSql);            
            if ($affected === false) {
                $errorInfo = $this->_conn->errorInfo();
                E($errorInfo[2]);
            }
            return $affected;
        }catch(PDOException $e){
            E($e->getMessage());
        }
    }
	
	/**
	 * 保护系统字段
	 * @param  mixed $value 
	 * @return string        
	 */
	protected function _quoteIdentifier($value){        
       $q = $this->getQuoteIdentifierSymbol();
	   return ($q . str_replace("$q", "$q$q", $value) . $q);        
    }

    /**
     * 准备sql
     * @param  string $vSql 
     * @return mixed       
     */
	protected function _prepare($vSql){        
        $stmt = $this->_conn->prepare($vSql);        
        return $stmt;
    }

    /**
     * 执行sql语句
     * @param  string $vSql 
     * @param  array  $vBind
     * @return mixed
     */
    protected function _query($vSql, array $vBind = array()){
		    	
        $stmt = $this->_prepare($vSql);                  
        
        Moses_Lib_Util::runtime('sql_start');
        
        //执行sql语句
        $stmt->execute($vBind);                  
        
        Moses_Lib_Util::runtime('sql_end');
        
        if(M_DEBUG == true) Moses_Lib_Log::I('sql:'.$vSql.',time:'.Moses_Lib_Util::runtime('sql_start','sql_end').'s,params:'.print_r($vBind,true));
        
        $stmt->setFetchMode($this->_fetchMode);           
        return $stmt;
	}
}

