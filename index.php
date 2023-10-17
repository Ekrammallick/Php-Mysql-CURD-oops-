<?php
include "db.php";
$db=new DB();
//$db->insert('students',['name'=>'demo6','age'=>'22',"sex"=>'male','class'=>'five']);
$db->select("students","*");
print_r($db->get_result());
