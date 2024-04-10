<?php
include 'conn.php';
include 'header_login.php';

if(isset($_GET['id'])){
    $category_id = $_GET['id'];
    $query = "SELECT  * FROM posts WHERE id = '$category_id' ";
    $result = mysqli_query($GLOBALS['conn'],$query);
    $data = mysqli_fetch_assoc($result);
    

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>
    <link rel="stylesheet" href="display_posts.css">
</head>
<body>
    <div class="container">
        <?php if (isset($data)): ?>
        <h2 class="post-title"><?=$data['title'];?></h2>
        
        <div class="post-content">
            <div class="image">
                <img src='uploads/<?=$data["image"]?>' style='max-width: 220px; max-height: 220px;' alt='Post Image'>
            </div>    
            <div class="description">
                <?=$data['description']?>
            </div>
            
        </div>
     
        <?php else: ?>
            echo "<p>No post found.</p>";
            <?php endif ?>
    </div>
  
   
    
    </body> 
</html>
