<?php
include 'customers.php';
include 'header_login.php';
$obj = new Customers();

if(isset($_POST['login'])){
    $obj -> login($_POST);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
    <link rel="icon" type="image/x-icon" href="download.png">
</head>
<body>
    <div class="container" style="margin-top:50px;"><h3>Login page</h3></div>

     <div class="container">
        
        <form method="POST" action="">
        <div class="form-group">
            <label>username</label>
            <input type="text" class="form-control"  name="username" placeholder="Enter your username" autocomplete="off">
        </div>    

        <div class="form-group">
            <label>password</label>
            <input type="password" class="form-control" name="password" placeholder="Enter your password" autocomplete="off">
        </div>    
     
        <input type="submit" class="btn btn-primary" value="login" name="login">

        </form>
     </div>
     
     <div class="container register"><a href="add.php">
            <input type="submit" class="btn btn-danger" style="margin-top: 20px;" value="Register" name=""></a>
            <p>If user not login so please <span>Register</span></p>   

    </div>
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
window.onload = function() {
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';
};
</script>

</body>
</html>