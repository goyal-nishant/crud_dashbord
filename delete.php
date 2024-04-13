<?php
include 'conn.php';

// Check if the comment ID is provided in the POST request
if(isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];

    // Prepare a DELETE query to delete the comment from the database
    $delete_query = "DELETE FROM posts_comments WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);

    // Bind the comment ID parameter
    mysqli_stmt_bind_param($stmt, "i", $comment_id);

    // Execute the DELETE query
    if(mysqli_stmt_execute($stmt)) {
        // If deletion is successful, send a success response
        echo json_encode(array('status' => 'success'));
    } else {
        // If deletion fails, send an error response
        echo json_encode(array('status' => 'error', 'message' => 'Failed to delete comment'));
    }
} else {
    // If comment ID is not provided, send an error response
    echo json_encode(array('status' => 'error', 'message' => 'Comment ID not provided'));
}
?>
