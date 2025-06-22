<?php
$basedir = preg_replace('/\\\lib.*|\/lib.*/', '', __DIR__);
require_once($basedir.'/lib/util/encoder/Encoder.php');
require_once($basedir.'/lib/util/filesystem_/FileSystem_.php');

/**
 * DATABASE
 */


/**
 *MYSQL PREPARE
 *0:delete insert update
 *1:retrieve data
 *2:tell exist or not
 */

class Database
{

  private $db_json;
  private $dbname;
  public $debug;
  public $fstcore;

  function __construct()
  {
    //$this->db_json = $this->encoder->jsonn_decode($this->fs->readfile($GLOBALS['basedir'].'/data/env/', 'db.json'));
  }

  public function connection()
  {
    $conn = new mysqli($this->db_json['database'][$this->dbname]['host'], $this->db_json['database'][$this->dbname]['port'], $this->db_json['database'][$this->dbname]['username'], $this->db_json['database'][$this->dbname]['password'], $this->db_json['database'][$this->dbname]['database']);
    return $conn;
  }
  
  public function escape($string){
    $conn = self::connection();
    $return = mysqli_real_escape_string($conn, $this->fstcore->util->encoder->htmlentities($string));
    $conn->close();
    return $return;
  }

  public function set_database($dbname){
    $this->dbname = $dbname;
  }

  public function insert($bind, $param, $query)
  {
    $data_array = array();
    $conn = self::connection();
    $stmt = $conn->prepare($query);
    call_user_func_array(array($stmt, "bind_param"), array_merge(array($bind), $param));
    $stmt->execute();
    $stmt->store_result();
    $data_array['result'] = $stmt->get_result();
    $stmt->close();
    return $data_array;
  }

  public function select($bind, $param, $query)
  {
    $data_array = array();
    $conn = self::connection();
    $stmt = $conn->prepare($query);
    call_user_func_array(array($stmt, "bind_param"), array_merge(array($bind), $param));
    $stmt->execute();
    $stmt->store_result();
    $data_array['data'] = $stmt->get_result();
    $data_array['row_count'] = $stmt->num_rows;
    $stmt->close();
    return $data_array;
  }

  public function delete($bind, $param, $query)
  {
    $data_array = array();
    $conn = self::connection();
    $stmt = $conn->prepare($query);
    call_user_func_array(array($stmt, "bind_param"), array_merge(array($bind), $param));
    $stmt->execute();
    $stmt->store_result();
    $data_array['result'] = $stmt->get_result();
    $stmt->close();
    return $data_array;
  }

  public function truncate($query)
  {
    //$bind, $param,
    $data_array = array();
    $conn = self::connection();
    $stmt = $conn->prepare($query);
    //call_user_func_array(array($stmt, "bind_param"), array_merge(array($bind), $param));
    $stmt->execute();
    $stmt->store_result();
    $data_array['result'] = $stmt->get_result();
    $stmt->close();
    return $data_array;
  }

  function __destruct(){
    
  }

  /*public function mysqld($query, $bind, $param, $signal){
    $return = '';
    $mysql = new ConfigConnectionCore1996();
    $MysqlOpenConnection = $mysql->connection();
    $stmt = $MysqlOpenConnection->prepare($query);
    //$stmt = $MysqlOpenConnection->bind_param($bind, call_user_func_array($param));
    call_user_func_array(array($stmt, "bind_param"), array_merge(array($bind), $param));
    $stmt->execute();
    if($signal == 1){
      $return = array();
      $return = $stmt;
    }
    if($signal == 2){
      if($stmt->num_rows > 1){
        $return = true;
      }
      else{
        $return = false;
      }
    }
    if($signal == 0){
      $return = true;
    }
    $stmt->close();
    return $return;
  }*/

}
