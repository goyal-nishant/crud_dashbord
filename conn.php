<?php
$Server = "localhost";
$username = "root";
$password = "";
$db = "post";

$conn = new mysqli($Server,$username,$password,$db);

if(!$conn){
    echo "Error";
}
else{
    //echo "done";
}
?>