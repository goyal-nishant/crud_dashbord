<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location: login_for_users.php');
    exit();
}
$replyers_name = $_SESSION['user_name'];

include 'conn.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the POST request
    $commenter_name = $_POST['commenter_name'];
    $parent_comment_id = $_POST['parent_comment_id'];
    $reply_text = $_POST['reply_text'];

    // Prepare and execute an SQL query to insert the reply into the database
    $insert_reply_query = "INSERT INTO posts_comments (post_id, comment_parent_id, comment_text, commenter_name) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_reply_query);
    mysqli_stmt_bind_param($stmt, "iiss", $category_id, $parent_comment_id, $reply_text, $commenter_name);
    $insert_reply_result = mysqli_stmt_execute($stmt);

    // Check if the insertion was successful
    if ($insert_reply_result) {
        // Construct an array containing the reply information
        $reply_data = array(
            'commenter_name' => $commenter_name,
            'reply_text' => $reply_text,
            'created_at' => date("Y-m-d H:i:s") // Assuming you have a timestamp field in your database for comment creation time
        );

        // Convert the array to JSON format and output it
        echo json_encode($reply_data);
    } else {
        // If insertion failed, return an error message
        echo json_encode(array('error' => 'Failed to insert reply into database.'));
    }
} else {
    // If the request method is not POST, return an error message
    echo json_encode(array('error' => 'Invalid request method.'));
}
?>
