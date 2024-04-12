<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location: login_for_users.php');
    exit();
}
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment']) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $comment_text = $_POST['comment_text'];
    $category_id = $_POST['category_id'];
    $user_name = $_SESSION['user_name']; // Retrieve user name from session

    $insert_query = "INSERT INTO posts_comments (post_id, comment_text, commenter_name) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "iss", $category_id, $comment_text, $user_name);
    $insert_result = mysqli_stmt_execute($stmt);

    if ($insert_result) {
        // Output the newly inserted comment
        $comment_id = mysqli_insert_id($conn); // Retrieve the ID of the newly inserted comment
        $select_query = "SELECT * FROM posts_comments WHERE id = ?";
        $select_stmt = mysqli_prepare($conn, $select_query);
        mysqli_stmt_bind_param($select_stmt, "i", $comment_id);
        mysqli_stmt_execute($select_stmt);
        $comment_row = mysqli_fetch_assoc(mysqli_stmt_get_result($select_stmt));

        // Output the comment as JSON
        echo json_encode($comment_row);
    } else {
        echo "Error inserting comment: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
