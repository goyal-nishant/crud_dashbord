<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location: login.php');
    exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
  <title>Home Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="style.css">
<link rel="icon" type="image/x-icon" href="download.png">

</head>
<body>

<div class="topnav" id="myTopnav">
  <a href="http://localhost/crudByoops/home.php" class="active">Home</a>
  <a href="http://localhost/crudByoops/list.php">Category</a>
  <a href="http://localhost/crudByoops/view_post.php">Post</a>
  <!-- <a href="#contact">Contact</a> -->
  <!-- <div class="dropdown">
    <button class="dropbtn">Create Post 
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="#">Link 1</a>
      <a href="#">Link 2</a>
      <a href="#">Link 3</a>
    </div>
  </div>  -->

  <a href="logout.php"><input type="submit" name="" value="logout"></a>
  <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
</div>

<!-- Text on Background Image -->
<div class="bg-text">This is a home page</div>

<script>
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
</script>

</body>
</html>
