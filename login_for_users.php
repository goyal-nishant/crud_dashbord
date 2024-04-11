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
<?php
include 'header_user.php';

$username = "";
$error = "";

if(isset($_POST['login'])){
    session_start();
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM user_login WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_name'] = $username;
            header("Location: view_category_user.php"); 
        }
     else {
        $error = "Invalid username or password";
    }
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
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Enter your username" autocomplete="off" value="<?php echo $username; ?>" required>
            </div>    
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter your password" autocomplete="off" required>
            </div>    
            <button type="submit" class="btn btn-primary" name="login">Login</button>
            <?php if($error) { echo "<p style='color:red;'>$error</p>"; } ?>
        </form>
    </div>
    
    <div class="container register">
        <a href="user_registration.php" class="btn btn-danger" style="margin-top: 20px;">Register</a>
        <p>If you're not registered yet, please <span>Register</span></p>   
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
