<?php

  session_start();
  if (!isset($_SESSION['user_name'])) {
    header('location: login.php');
    exit();
}

  // Include database file
  include 'customers.php';
  
  $customerObj = new Customers();
  // Edit customer record
  if(isset($_GET['editId']) && !empty($_GET['editId'])) {
    $editId = $_GET['editId'];
    $customer = $customerObj->displyaRecordById($editId); // Corrected function name
  } else {
    // Redirect or display an error message if editId is not provided
    // For example: header("Location: index.php");
  }
  // Update Record in customer table  
  if(isset($_POST['update'])) {
    $customerObj->updateRecord($_POST);
  }  
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>PHP: CRUD (Add, Edit, Delete, View) Application using OOP (Object Oriented Programming) and MYSQL</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
  <link rel="icon" type="image/x-icon" href="download.png">
</head>
<body>
<div class="card text-center" style="padding:15px;">
  <h4>PHP: CRUD (Add, Edit, Delete, View) Application using OOP (Object Oriented Programming) and MYSQL</h4>
</div><br> 
<div class="container">
  <form action="edit.php" method="POST">
    <div class="form-group">
      <label for="name">Name:</label>
      <input type="text" class="form-control" name="uname" value="<?php echo isset($customer['name']) ? $customer['name'] : ''; ?>" required="">
    </div>
    <div class="form-group">
      <label for="email">Email address:</label>
      <input type="email" class="form-control" name="uemail" value="<?php echo isset($customer['email']) ? $customer['email'] : ''; ?>" required="">
    </div>
    <div class="form-group">
      <label for="username">Username:</label>
      <input type="text" class="form-control" name="upname" value="<?php echo isset($customer['username']) ? $customer['username'] : ''; ?>" required="">
    </div>
    <div class="form-group">
      <input type="hidden" name="id" value="<?php echo isset($customer['id']) ? $customer['id'] : ''; ?>">
      <input type="submit" name="update" class="btn btn-primary" style="float:right;" value="Update">
    </div>
  </form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
