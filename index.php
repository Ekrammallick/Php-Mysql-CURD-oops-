<?php
include "db.php";
$db=new DB();
//$db->insert('students',['name'=>'demo6','age'=>'22',"sex"=>'male','class'=>'five']);
$db->select("students","*",null,null,2,null,null);
echo "\n\n Select result is:";
echo "<pre>";
print_r($db->get_result());
echo "</pre>";
$db->pagination("students",null,null,2);