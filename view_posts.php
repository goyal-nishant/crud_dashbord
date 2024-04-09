<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location: login.php');
    exit();
}

include 'header.php';
include 'connection.php';
include 'conn.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View posts by category</title> 
    <link rel="stylesheet" href="view_posts.css">    
</head>
<body>
<div class="container">
    <ul>
    <?php
    if (isset($_GET['category_id'])) {
        $category_id = $_GET['category_id'];
        $sql_category = "SELECT name FROM categories WHERE id = $category_id";
        $result_category = mysqli_query($GLOBALS['connect'], $sql_category);
        
        if ($result_category === false) {
            echo "Error: " . mysqli_error($GLOBALS['connect']);
        } else {
            $category_name = mysqli_fetch_assoc($result_category)['name'];
            echo "<h2>Category : $category_name</h2>";

            $sql = "SELECT * FROM posts_categories WHERE category_id = $category_id";
            $result = mysqli_query($GLOBALS['connect'], $sql);
            
            if ($result === false) {
                echo "Error: " . mysqli_error($GLOBALS['connect']);
            } else {
                $post_ids = [];
                while ($data = mysqli_fetch_assoc($result)) {
                    $post_id = $data['post_id'];
                    if (is_numeric($post_id)) {
                        $post_ids[] = $post_id;
                    }
                }
                
                if (!empty($post_ids)) {
                    $post_ids_str = implode(",", $post_ids); 
                    $sql_posts = "SELECT * FROM posts WHERE id IN ($post_ids_str)";
                    $result_posts = mysqli_query($GLOBALS['connect'], $sql_posts);
                    
                    if ($result_posts === false) {
                        echo "Error: " . mysqli_error($GLOBALS['connect']);
                    } else {
                        echo "<ul>";
                        while ($post_data = mysqli_fetch_assoc($result_posts)) {
                            echo "<li>";
                            echo "Post Title: " . $post_data['title'] . "<br>";
                            echo "Post Content: " . $post_data['description'] . "<br>";
                            echo "</li>";
                        }
                        echo "</ul>";
                    }
                } else {
                    echo "No posts found for this category.";
                }
            }
        }
    }
    ?>
    </ul>
</div>
</body>
</html>
