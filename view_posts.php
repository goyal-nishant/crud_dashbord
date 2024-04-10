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
    <?php
    function excerpt($title) {
        $new = substr($title, 0, 27);

        if (strlen($title) > 30) {
            return $new.'...';
        } else {
            return $title;
        }
    }
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
                $row_count = 0;

                if (!empty($post_ids)) {
                    $post_ids_str = implode(",", $post_ids); 
                    $sql_posts = "SELECT * FROM posts WHERE id IN ($post_ids_str)";
                    $result_posts = mysqli_query($GLOBALS['connect'], $sql_posts);
                    
                    if ($result_posts === false) {
                        echo "Error: " . mysqli_error($GLOBALS['connect']);
                    } else {
                        while ($post_data = mysqli_fetch_assoc($result_posts)) {
                            if (!isset($row_count) || $row_count % 2 == 0) {
                                echo "<div class='row'>";
                            }
                            echo "<div class='post-card'>";
                            echo "<a href='display_posts.php?id={$post_data['id']}'>";
                            echo "<img src='uploads/$post_data[image]' style='max-width: 100px; max-height: 100px;' alt='Post Image'>";
                            echo "<h3>{$post_data['title']}</h3>";
                            echo "<p>" . excerpt($post_data['description']) . "</p>";
                            echo "</a>";
                            echo "</div>";
                            if ($row_count % 2 == 1 || $row_count == mysqli_num_rows($result_posts) - 1) {
                                echo "</div>"; // Close row if it's the end of row or last post
                            }
                            $row_count++;
                        }
                    }
                } else {
                    echo "No posts found for this category.";
                }
            }
        }
    }
    ?>
</div>
</body>
</html>
