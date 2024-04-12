<?php
include 'conn.php';

if(isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];

    // Proceed with the rest of your code
    // ...
} else {
    // Handle the case when "category_id" is not set
    echo "Category ID is not set.";
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_text'])) {
    // Retrieve comment text from the POST data
    $comment_text = $_POST['comment_text'];

    // Assuming you have session management already set up
    session_start();
    if (!isset($_SESSION['user_name'])) {
        // Handle the case where the user is not logged in
        // You can return an error message or handle it as needed
        echo "Please log in to submit a comment.";
        exit();
    }

    // Get the logged-in user's name
    $commenter_name = $_SESSION['user_name'];

    // Assuming you have already sanitized $category_id
    $category_id = $_POST['category_id'];

    // Prepare the SQL statement to insert the comment
    $insert_query = "INSERT INTO posts_comments (post_id, comment_text, commenter_name) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);

    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, "iss", $category_id, $comment_text, $commenter_name);
    $insert_result = mysqli_stmt_execute($stmt);

    // Check if the insertion was successful
    if ($insert_result) {
        // If the insertion was successful, return a success message
        echo "Comment submitted successfully.";
    } else {
        // If there was an error, return an error message
        echo "Error submitting comment: " . mysqli_error($conn);
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);

    // Close the database connection
    mysqli_close($conn);
}
?>
