<?php 
$crashreason  = $_POST["crashreason"];
$crashid  = $_POST["crashid"];
$file = "contacts.txt";
$fp =fopen($file,"a") or die("coudnt open");
$data = "contact name:".$crashreason." and contactnumber:".$crashid;
fwrite($fp,$data) or die("coudnt");
die("success!");
fclose($fp);
?>