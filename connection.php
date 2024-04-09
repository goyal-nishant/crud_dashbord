<?php 
$Servername = "localhost";
$username = "root";
$password = "";
$dbname = "post";

$connect = new mysqli($Servername,$username,$password,$dbname);

if(!$connect){
    echo mysqli_error($connect);
}
else{
//echo "Done";
}
?>
