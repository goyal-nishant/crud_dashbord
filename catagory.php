<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location: login.php');
    exit();
}

include 'connection.php';
include 'conn.php';     

$message = ""; // Variable to store response message

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Retrieve form data
    $parentCat = $_POST['parentcat'];
    $catName = $_POST['catname'];
    $description = $_POST['description'];
    
    // Check if category already exists
    $checkCategoryQuery = "SELECT id FROM categories WHERE name = ?";
    $stmt = mysqli_prepare($GLOBALS['connect'], $checkCategoryQuery);
    mysqli_stmt_bind_param($stmt, 's', $catName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $rowCount = mysqli_stmt_num_rows($stmt);
    
    if ($rowCount > 0) {
        $message = "Category already exists";
    } else {
        // Insert into categories table
        $insertCategoryQuery = "INSERT INTO categories (parent_id, name, description) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($GLOBALS['connect'], $insertCategoryQuery);
        mysqli_stmt_bind_param($stmt, 'iss', $parentCat, $catName, $description);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $message = "Category added successfully";
            
            // Retrieve the ID of the newly inserted category
            $categoryId = mysqli_insert_id($GLOBALS['connect']);
            
            // Insert into posts_categories table
            $insertPostsCategoriesQuery = "INSERT INTO posts_categories (category_id) VALUES (?)";
            $stmt = mysqli_prepare($GLOBALS['connect'], $insertPostsCategoriesQuery);
            mysqli_stmt_bind_param($stmt, 'i', $categoryId);
            mysqli_stmt_execute($stmt);

            $quer = "SELECT * FROM posts";
            $res = mysqli_query($GLOBALS['conn'],$quer);
            $total = mysqli_fetch_assoc($res);
            $val = $total['id'];
    
            $q2 = "INSERT INTO posts_categories (post_id) VALUES ('$val')";
        } else {
            $message = "Error adding category";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="category.css">
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

<div class="container">
    <h2>Add Category</h2>
    <form method="post">
        <label for="parentcat">Parent Category:</label>
        <select name="parentcat"> 
            <option value="0">None</option>
            <?php 
            $sqldata = "SELECT * FROM categories";
            $sresult = mysqli_query($GLOBALS['connect'],$sqldata);
            while($cat = mysqli_fetch_assoc($sresult)){
            ?>
            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name'];  ?></option>
            <?php } ?>
        </select>
        <label for="catname">Category Name:</label>
        <input type="text" name="catname" required> 
        <label for="description">Description:</label>
        <input type="text" name="description" required>
        <input class="sub" type="submit" name="submit" value="Submit">
    </form>

    <!-- Display response message -->
    <?php 
    if(isset($message)) {
        $class = isset($result) && $result ? 'message' : 'message error'; // Fix for undefined $result
        echo "<div class='$class'>$message</div>";
    }
    ?>
</div>

<!-- Button for viewing categories -->
<div class="container">
    <form method="get" action="list.php">
        <input class="sub" type="submit" value="View Categories">
    </form>
</div>

</body>
</html>
