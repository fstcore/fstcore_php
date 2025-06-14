<?php

class ListData{

    private $db;
    private $database;
    private $bind;
    private $param;
    private $query;

    function __construct($val=null){
        $this->db = new Database();
    }

    public function set_db($database){
        $this->database = $database;
    }

    public function set_bind($bind){
        $this->bind = $bind;
    }

    public function set_param($param){
        $this->param = $param;
    }

    public function set_query($query){
        $this->query = $query;
    }

    public function start(){
        $this->db->set_database($this->database);
        return $this->db->select($this->bind, $this->param, $this->query);
    }

    function __destruct(){
        
    }

}

?>