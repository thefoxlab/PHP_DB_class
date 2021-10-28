<?php

require_once(BASE_PATH.'vendors/PHP-MySQLi-Database-Class-master/MysqliDb.php');

class DB extends MysqliDb {
    
    private $_table;
    private $_fields;
    public $fields;
    function __construct($host = null, $username = null, $password = null, $db = null,$port = null, $charset = '', $socket = null) {
        parent::__construct($host, $username , $password , $db ,$port,$charset,$socket);
    }
    
    
    function get($table, $select = array(), $conditions = array(), $order = array(), $limit = NULL, $offset = NULL) {
        
        $this->buildWhere($conditions);
        $this->buildOrderBy($order);
        
        $pag = null;
        if($limit && $offset)
        {
            $pag = [$offset,$limit];
        }
        else if($limit)
        {
            $pag = $limit;
        }
        return parent::get($table,$pag,$select);
    }
    function buildWhere($conditions){
        if ($conditions)
        {
            if(is_array($conditions))
            {
                foreach ($conditions as $key=>$val)
                {
                    $oprator = "=";
                    $ke_oper = explode(" ", $key);
                    
                    if(isset($ke_oper['1']) && $ke_oper['1'])
                    {
                        $oprator = $ke_oper['1'];
                        $key = $ke_oper['0'];
                    }
                    
                    $this->where($key, $val,$oprator);
                }
            }
            else {
                $this->where($conditions);
            }
        }
    }
    function buildOrderBy($order){
        if ($order && is_array($order))
        {
            foreach ($order as $key=>$val)
            {
                if(is_array($val))
                {
                    $this->buildOrderBy($val); //recursion
                }
                else{
                    $this->orderBy($key, $val);
                }
            }
        }
    }
    function get_row($table,$select = array(), $conditions = array(), $order = array()) {
        
        $this->buildWhere($conditions);
        $this->buildOrderBy($order);
        

        $rs = parent::get($table,1,$select);
        return $rs ? $rs['0'] : [];
    }
    
    function save($table , $data = []) {
        return $this->insert($table,$data);
    }
    function save_multiple($table , $data = []) {
        return $this->insertMulti($table,$data);
    }
    
    
    function update($table , $data, $conditions = array()) {
        if (!empty($data) && !empty($conditions)) {
            
            $this->buildWhere($conditions);
            parent::update($table, $data);
            
            if ($this->count > 0)
                return true;
            else
                return false;
        }
        return false;
    }
    function delete($table ,  $conditions = array()) {
        if (!empty($conditions)) {
            
            $this->buildWhere($conditions);
            parent::delete($table);
            return $this->count;
        }
        else{
            return false;
        }
    }
    function soft_delete($table, $where = [])
    {
        return $this->update($table,['del'=>'yes'],$where);
    }
    
}