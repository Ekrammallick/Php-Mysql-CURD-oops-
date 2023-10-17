<?php

class DB
{
    private $db_name = 'test';
    private $db_user = 'root';
    private $db_pass = '';
    private $db_host = 'localhost';
    private $mysqli = null;
    private $result = array();
    private $conn = false;

    public function __construct()
    {
        if (!$this->conn) {
            $this->mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
            $this->conn = true;
            if ($this->mysqli->connect_error) {
                array_push($this->result, $this->mysqli->connect_error);
            }
        } else {

            return true;
        }
    }

    public function insert($table, $data = array())
    {

        if ($this->table_exists($table)) {
            $table_columns = implode(',', array_keys($data));
            $table_values = implode("','", $data);
            $sql = "INSERT INTO $table ( $table_columns) VALUES ('$table_values')";
            if ($this->mysqli->query($sql)) {
                array_push($this->result, $this->mysqli->insert_id);
                return true;
            } else {
                array_push($this->result, $this->mysqli->error);
                return false;
            }
        } else {
            return false;
        }
    }
    public function update($table, $data = array(), $where = null)
    {
        if ($this->table_exists($table)) {
            //print_r($data);
            $args=array();
            foreach($data as $key => $value){
                $args[]="$key = '$value'";
            }
            $sql = "UPDATE $table SET ".implode(',',$args);
            if($where !=null){
               $sql.="WHERE $where";
            }
            if ($this->mysqli->query($sql)) {
                array_push($this->result, $this->mysqli->affected_rows);
                return true;
            } else {
                array_push($this->result, $this->mysqli->error);
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete($table,$cond=null){
        if($this->table_exists($table)){
           $sql="DELETE FROM $table ";
           if($cond != null){
            $sql .= "WHERE ".$cond;
           }
           if ($this->mysqli->query($sql)) {
            array_push($this->result, $this->mysqli->affected_rows);
            return true;
        } else {
            array_push($this->result, $this->mysqli->error);
            return false;
        }

        }else{
            return false;
        }
    }
    public function select(){
            
    }
    public function __destruct()
    {
        if ($this->conn) {
            if ($this->mysqli->close()) {
                $this->conn = false;
                return true;
            }
        } else {
            return false;
        }
    }
    private function table_exists($table)
    {
        $sql = "SHOW TABLES FROM $this->db_name LIKE '$table'";

        if ($this->mysqli->query($sql)->num_rows == 1) {
            return true;
        } else {
            array_push($this->result, $table . 'dos not exits in this database');
            return false;
        }
    }

    public function get_result()
    {
        $val = $this->result;
        $this->result = array();
        return $val;
    }
}
