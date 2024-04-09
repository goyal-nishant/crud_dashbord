<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location: login.php');
    exit();
}

include 'connection.php';
include 'conn.php';

$error_message = '';

if(isset($_GET['id'])) {
    $category_id = $_GET['id'];
    $sql = "SELECT * FROM categories WHERE id = $category_id";
    $result = mysqli_query($GLOBALS['connect'], $sql);
    $category = mysqli_fetch_assoc($result);
}

$sql = "SELECT * FROM categories";
$result = mysqli_query($GLOBALS['conn'], $sql);

$categories = array();
$parent_categories = array(); 

if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $categories[$row['id']] = $row; 
        if ($row['parent_id'] == 0) {
            $parent_categories[$row['id']] = $row; 
        }
    }
}

if(isset($_POST['update_category'])) {
    $category_name = $_POST['category_name'];
    $category_description = $_POST['category_description'];
    $new_parent_id = $_POST['categories']; 
    
    // Check if the category name already exists
    $check_duplicate_sql = "SELECT * FROM categories WHERE name = '$category_name' AND id != $category_id";
    $check_result = mysqli_query($GLOBALS['connect'], $check_duplicate_sql);
    if(mysqli_num_rows($check_result) > 0) {
        $error_message = "Error: Category name already exists.";
    } else {
        // Check if parent category has changed
        if ($category['parent_id'] != $new_parent_id) {
            // Remove category from previous parent's children list
            $previous_parent_id = $category['parent_id'];
            unset($parent_categories[$previous_parent_id]['children'][$category_id]);
            
            // Add category to new parent's children list
            $parent_categories[$new_parent_id]['children'][$category_id] = $category;
            
            // Update the corresponding entries in the posts_categories table
            $update_posts_categories_sql = "UPDATE posts_categories SET category_id=$new_parent_id WHERE category_id=$category_id";
            echo $update_posts_categories_sql;
            if(!mysqli_query($GLOBALS['conn'], $update_posts_categories_sql)) {
                echo "Error updating posts_categories table: " . mysqli_error($GLOBALS['conn']);
            }
        }
        
        // Update the category in the categories table
        $sql = "UPDATE categories SET name='$category_name', description='$category_description', parent_id=$new_parent_id, updated_at=NOW() WHERE id=$category_id";
        
        if(mysqli_query($GLOBALS['connect'], $sql)) {
            header("Location: list.php");
            exit;
        } else {
            echo "Error updating category: " . mysqli_error($GLOBALS['connect']);
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Category</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="update_category.css">
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
<h2>Update Category</h2>
<form method="POST">
    <label for="category_name">Category Name:</label><br>
    <input type="text" id="category_name" name="category_name" value="<?php echo $category['name']; ?>"><br>
    <label for="category_description">Category Description:</label><br>
    <textarea id="category_description" name="category_description"><?php echo $category['description']; ?></textarea><br><br>
    <label for="categories">Parent Category:</label>
    <select name="categories" id="categories" required>
    <option value="0">No Parent</option>
    <?php foreach($parent_categories as $parent): ?>
        <?php if ($parent['id'] != $category['id']): ?>
            <option value="<?php echo $parent['id']; ?>" <?php if ($parent['id'] == $category['parent_id']) echo "selected"; ?>><?php echo $parent['name']; ?></option>
        <?php endif; ?>
    <?php endforeach; ?>
</select><br><br>

    <input class="input1" type="submit" name="update_category" value="Update Category">
</form>
<?php echo $error_message; ?> <!-- Display error message here -->
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
</html
