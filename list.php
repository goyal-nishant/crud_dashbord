<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location: login.php');
    exit();
    }
include 'connection.php';

function showcategory($parentid, $level = 0) {
    $sql = "SELECT * FROM categories WHERE parent_id = $parentid";
    $result = mysqli_query($GLOBALS['connect'], $sql);
    
    if ($level === 0) {
        
        $output = "<div class='container'>
        <table class='table'>\n";
        $output .= "<thead><tr style='background-color: white; text-align:center;'><th>Category Name</th><th>Description</th><th>Action</th><th>View post</th></tr></thead>\n";
        $output .= "<tbody>\n";
    } else {
        $output = "<ul>\n";
    }
    
    while($data = mysqli_fetch_array($result)) {
        $output .= "<tr>\n";
        $output .= "<td>" . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . "<i class='fa-solid fa-circle-dot'></i> " . $data["name"] . "</td>";
        $output .= "<td>" . $data["description"] . "</td>";
        $output .= "<td><a href='update_category.php?id=".$data['id']."'>Edit</a> | "; // Link to update category
        $output .= "<a href='delete_category.php?id=".$data['id']."' onclick='return confirmDelete();'>Delete</a></td>"; // Link to delete category
        $output .= "<td><a href='view_posts.php?category_id=".$data['id']."'>View Posts</a></td>"; // Link to view posts related to the category
        $output .= "</tr>\n";
        
        
        $output .= showcategory($data['id'], $level + 1); 
    }
    
    if ($level === 0) {
        $output .= "</tbody>\n";
        $output .= "</table>
        </div>\n";

    } else {
        $output .= "</ul>\n";
    }
    
    return $output;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category list</title>
    <link rel="stylesheet" href="list.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="download.png">

</head>
<body>
<div class="topnav" id="myTopnav">
  <a href="http://localhost/crudByoops/home.php" class="active">Home</a>
  <a href="http://localhost/crudByoops/list.php">Category</a>
  <a href="http://localhost/crudByoops/view_post.php">Post</a>
  <a href="logout.php"><input type="submit" name="" value="logout"></a>
  <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
</div>

<div class="border container pb-5 pt-3 ml-1" style="text-align: center;background-color:white;">
    <h3>Categories</h3>
    <div class="" style="margin-top: 25px;">
         <a href="http://localhost/crudByoops/catagory.php"><input type="submit" value="Create Categories" class="btn btn-primary"></a>
    </div>
</div>
    <?php
    echo showcategory(0); 
    ?>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>
