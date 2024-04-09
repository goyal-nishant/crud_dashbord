<?php
include 'conn.php';

$id = $_GET['id'];

// Delete related records in posts_categories table first
$sqlDeleteCategories = "DELETE FROM posts_categories WHERE post_id='$id'";
$resultDeleteCategories = mysqli_query($GLOBALS['conn'], $sqlDeleteCategories);

if ($resultDeleteCategories) {
    // Once related records are deleted, delete the post
    $sqlDeletePost = "DELETE FROM posts WHERE id='$id' ";
    $resultDeletePost = mysqli_query($GLOBALS['conn'], $sqlDeletePost);

    if ($resultDeletePost) {
        header("location: view_post.php");
        exit();
    } else {
        echo "Failed to delete post: " . mysqli_error($GLOBALS['conn']);
    }
} else {
    echo "Failed to delete related categories: " . mysqli_error($GLOBALS['conn']);
}
?>
