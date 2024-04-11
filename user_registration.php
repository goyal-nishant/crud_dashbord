<?php
include 'conn.php';
include 'header_user.php';

$name = "";
$email = "";
$username = "";
$message = "";

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username or email already exists
    $check_query = "SELECT * FROM user_login WHERE username = ? OR email = ?";
    $stmt = mysqli_prepare($GLOBALS['conn'], $check_query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if(mysqli_stmt_num_rows($stmt) > 0) {
        $message = "Username or email already exists!";
    } else {
        // Insert new user with hashed password
        //$hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user_login (name,email,username,password) VALUES (?,?,?,?)";
        $stmt = mysqli_prepare($GLOBALS['conn'], $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $username, $password);
        if(mysqli_stmt_execute($stmt)) {
            $message = "Registration successful!";
        } else {
            $message = "Error: " . mysqli_error($GLOBALS['conn']);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration Form</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
</head>
<body>
    <div class="container" style="margin-top:20px;"><h3>Registration form</h3></div>
    <div class="container">
        <form action="" method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" placeholder="Enter your name" required value="<?php echo $name; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" placeholder="Enter your email" required value="<?php echo $email; ?>">
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Enter your username" required value="<?php echo $username; ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
            </div>
            <input type="submit" value="submit" name="submit">
        </form>
        <?php echo $message; ?>
    </div>
    <a href="login_for_users.php"><input type="button" name="login" value="login"></a>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
