<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location: login_for_users.php');
    exit();
}

include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['parent_comment_id']) && isset($_POST['reply_text'])) {
        $parentCommentId = mysqli_real_escape_string($conn, $_POST['parent_comment_id']);
        $replyText = mysqli_real_escape_string($conn, $_POST['reply_text']);
        $category_id = mysqli_real_escape_string($conn, $_POST['category_id']); // Assuming you have category_id available here
        $replyers_name = $_SESSION['user_name']; // Assuming you have replyers_name available in session

        // Prepare and bind parameters
        $insert_query = "INSERT INTO posts_comments (post_id, comment_parent_id, comment_text, commenter_name) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "iiss", $category_id, $parentCommentId, $replyText, $replyers_name);

        // Execute the statement
        $insert_result = mysqli_stmt_execute($stmt);
        
        // Get the ID of the inserted row
        $inserted_id = mysqli_insert_id($conn);

        // Fetch the inserted data from the database
        $select_query = "SELECT * FROM posts_comments WHERE id = ?";
        $select_stmt = mysqli_prepare($conn, $select_query);
        mysqli_stmt_bind_param($select_stmt, "i", $inserted_id);
        mysqli_stmt_execute($select_stmt);
        $result = mysqli_stmt_get_result($select_stmt);
        $inserted_data = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt); // Close the statement
        mysqli_stmt_close($select_stmt); // Close the select statement

        // Check if the insertion was successful and data was fetched
        if ($insert_result && $inserted_data) {
            // Only include the inserted data in the response
            echo json_encode($inserted_data);
            exit();
        } else {
            $response = array('success' => false, 'error' => 'Failed to insert reply'); // Generic error message
            echo json_encode($response);
            exit();
        }
    }
}

// Invalid request
$response = array('success' => false, 'error' => 'Invalid request');
echo json_encode($response);
?>
