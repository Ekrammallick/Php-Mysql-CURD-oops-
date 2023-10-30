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
    public function select($table,$row="*",$join=null,$where=null,$limit=null,$order=null,$and=null){
            if($this->table_exists($table)){
                  $sql="SELECT $row FROM $table";
                  if($join !=null){
                    $sql .=" JOIN $join";
                  }
                  if($where !=null){
                    $sql .=" WHERE $where";
                  }
                  if($order !=null){
                    $sql .=" ORDER $order";
                  }
                  if($limit !=null){
                    if(isset($_GET['page'])){
                        $page=$_GET['page'];
                    }else{
                        $page=1;
                    }
                    $start=($page-1)*$limit;
                    $sql .=" LIMIT  $start,$limit";
                  }
                  if($and !=null){
                    $sql .=" AND,$and";
                  }
                
                  $this->sql($sql);
            }else{
                return false;
            }
    }
    public function sql($sql){
       $query=$this->mysqli->query($sql);
       if($query){
        $this->result=$query->fetch_all(MYSQLI_ASSOC);
        return true;
       }else{
        array_push($this->result,$this->mysqli->error);
        return false;
       }
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
    public function pagination($table,$join=null,$where=null,$limit=null){
        if($this->table_exists($table)){
               if($limit !=null){
                $sql="SELECT COUNT(*) FROM $table";
                if($join !=null){
                    $sql .="JOIN $join";
                }
                if($where !=null){
                    $sql .="WHERE $where";
                }
                $query=$this->mysqli->query($sql);
                $total_record =$query->fetch_array();
                $total_record = $total_record[0];
                $total_page= ceil($total_record / $limit);
                $url = basename($_SERVER['PHP_SELF']);
                if(isset($_GET['page'])){
                    $page=$_GET['page'];
                }else{
                    $page=1;
                }
                 $output="<ul class='pagination'>";
                 if($total_record > $limit){
                         for($i=1;$i<=$total_page;$i++){
                            if($i == $page){
                                $cls="class='active'";
                            }else{
                                $cls="";
                            }
                            $output .="<li><a $cls href='$url?page=$i'>$i</a></li>";
                         }
                 }
                 $output .=  "</ul>";
                echo $output;
               }else{
                return false;
               }
        }else{
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
