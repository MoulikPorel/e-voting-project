<?php
$host="localhost";
$user="root";
$pass="";
$db="e-Voting"; 
$conn= new mysqli($host,$user,$pass,$db);
if($conn->connect_error){
    echo " Faild to connect to db".$conn->connect_error;
}


?>