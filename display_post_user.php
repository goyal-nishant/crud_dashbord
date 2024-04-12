<?php
include 'conn.php';
include 'header_logout_user.php';

session_start();
if (!isset($_SESSION['user_name'])) {
    header('location: login_for_users.php');
    exit();
}

$replyers_name = $_SESSION['user_name'];

if(isset($_GET['id'])){
    $category_id = $_GET['id'];
    $query = "SELECT * FROM posts WHERE id = '$category_id'";
    $result = mysqli_query($GLOBALS['conn'], $query);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
        } else {
            echo "No post found for the given ID.";
        }
    } else {
        echo "Error fetching post data: " . mysqli_error($GLOBALS['conn']);
    }
}

if(isset($_POST['submit_comment'])) {
    $comment_text = $_POST['comment_text'];

    $insert_query = "INSERT INTO posts_comments (post_id, comment_text, commenter_name) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($GLOBALS['conn'], $insert_query);
    mysqli_stmt_bind_param($stmt, "iss", $category_id, $comment_text, $replyers_name);
    $insert_result = mysqli_stmt_execute($stmt);
    var_dump($insert_result);

    if (!$insert_result) {
        echo "Error inserting comment: " . mysqli_error($GLOBALS['conn']);
    }
}

if(isset($_POST['submit_reply'])) {
    $parent_comment_id = $_POST['parent_comment_id'];
    $reply_text = $_POST['reply_text'];

    $insert_reply_query = "INSERT INTO posts_comments (post_id, comment_parent_id, comment_text, commenter_name) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($GLOBALS['conn'], $insert_reply_query);
    mysqli_stmt_bind_param($stmt, "iiss", $category_id, $parent_comment_id, $reply_text, $replyers_name);
    $insert_reply_result = mysqli_stmt_execute($stmt);

    if (!$insert_reply_result) {
        echo "Error inserting reply: " . mysqli_error($GLOBALS['conn']);
    }
}

if(isset($_POST['update_comment'])) {
    $comment_id = $_POST['comment_id'];
    
    if (isset($_POST['updated_comment_text'])) {
        $updated_comment_text = $_POST['updated_comment_text'];

        $update_query = "UPDATE posts_comments SET comment_text = ? WHERE id = ?";
        $stmt = mysqli_prepare($GLOBALS['conn'], $update_query);
        mysqli_stmt_bind_param($stmt, "si", $updated_comment_text, $comment_id);
        $update_result = mysqli_stmt_execute($stmt);

        if (!$update_result) {
            echo "Error updating comment: " . mysqli_error($GLOBALS['conn']);
        }
    } else{
        echo "Updated comment text is missing.";
    }
}

if(isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];

    $delete_query = "DELETE FROM posts_comments WHERE id = ?";
    $stmt = mysqli_prepare($GLOBALS['conn'], $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $comment_id);
    $delete_result = mysqli_stmt_execute($stmt);

    if (!$delete_result) {
        echo "Error deleting comment: " . mysqli_error($GLOBALS['conn']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>
    <link rel="stylesheet" href="display_posts.css">
    <link rel="stylesheet" href="display_user_post.css">  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
            <p>No post found.</p>
        <?php endif ?>
    </div>
    <button class="comment-head btn btn-primary" onclick="showHide()">Add comment</button>
    <div class="comment gradient-custom container" style="display: none;">
        <h3 style="color: white;">Add a Comment</h3>
        <form id="comment-form" method="post">
            <input type="hidden" name="user_name" value="<?php echo $_SESSION['user_name']; ?>">
            <textarea name="comment_text"></textarea>
            <input type="submit" name="submit_comment" value="Submit Comment">
        </form>
        
        <?php
       function display_comments($parent_id = 0, $level = 0) {
        global $category_id, $replyers_name;
        $comments_query = "SELECT * FROM posts_comments WHERE post_id = $category_id AND comment_parent_id = $parent_id ORDER BY created_at ASC";
        $comments_result = mysqli_query($GLOBALS['conn'], $comments_query);
    
        if ($comments_result && mysqli_num_rows($comments_result) > 0) {
            while ($comment = mysqli_fetch_assoc($comments_result)) {
                echo "<div id='comments-container'  class='comment' style='margin-left: " . ($level * 20) . "px;background-color:white;padding:30px'>";
                echo "<p class='container' style='height:auto;'><strong>{$comment['commenter_name']}</strong>: {$comment['comment_text']} <br>at: {$comment['created_at']}</p>";
                
    
                if ($replyers_name === $comment['commenter_name']) {
                    // Display delete button only if the commenter is the currently logged-in user
                    echo "<div class='comment-actions'>";
                    echo "<form method='post' action='' onsubmit = return myfunc();>";
                    echo "<input type='hidden' name='comment_id' value='{$comment['id']}'>";
                    echo "<input type='submit' class='comment-action-button' name='delete_comment' value='Delete' onclick='return confirm(\"Are you sure you want to delete this comment?\")'>";
                    echo "</form>";
                    echo "</div>";
                }
    
                echo "<button class='reply-button' data-comment-id='{$comment['id']}'>Reply</button>";
                echo "<form class='reply-form' id='reply-form-{$comment['id']}' method='post' style='display: none;'>";
                echo "<input type='hidden' name='commenter_name' value='{$_SESSION['user_name']}'>"; // Use $_SESSION['user_name'] directly
                echo "<input type='hidden' name='parent_comment_id' value='{$comment['id']}'>";
                echo "<label for='reply_text'>Your Reply:</label><br>";
                echo "<textarea id='reply_text' name='reply_text'></textarea><br>";
                echo "<input type='submit' name='submit_reply' value='Submit Reply'>";
                echo "</form>";
    
                // Update comment form
               // echo "<button class='update-comment-button'>Update Comment</button>";
                echo "<form class='update-comment-form' id='update-form-{$comment['id']}' method='post' style='display: none;'>";
                echo "<input type='hidden' name='comment_id' value='{$comment['id']}'>";
                echo "<label for='updated_comment_text'>Updated Comment:</label><br>";
                echo "<textarea id='updated_comment_text' name='updated_comment_text'></textarea><br>";
                echo "<input type='submit' name='update_comment' value='Update Comment'>";
                echo "</form>";
    
                display_comments($comment['id'], $level + 1);
    
                echo "</div>";
                }
            }
        }

        display_comments(); 
        ?>
    </div>

    <script>
        document.querySelectorAll('.reply-button').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.getAttribute('data-comment-id');
                const replyForm = document.querySelector(`#reply-form-${commentId}`);
                if (replyForm) {
                    replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
                } else {
                    console.error('Reply form not found for comment ID:', commentId);
                }
            });
        });

        document.querySelectorAll('.update-comment-button').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.nextElementSibling.querySelector('input[name="comment_id"]').value;
                const updateForm = document.querySelector(`#update-form-${commentId}`);
                if (updateForm) {
                    updateForm.style.display = updateForm.style.display === 'none' ? 'block' : 'none';
                } else {
                    console.error('Update form not found for comment ID:', commentId);
                }
            });
        });
    </script>
    <script>
             let comment = document.querySelector('.comment-head');
             let comment_text  = document.querySelector('.comment');
             let isShow = true;
             function showHide() {
                if(isShow) {
                    comment_text.style.display = "none";
                    isShow = false;
                }else{
                    comment_text.style.display = "block";
                    isShow = true;
                }
                
             }

    </script>
    <script>
        // Add an event listener to the comment form submission
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('#comment-form').addEventListener('submit', function (event) {
                // Prevent the default form submission behavior
                event.preventDefault();

                // Serialize the form data
                var formData = new FormData(this);

                // Send the serialized data to the server using AJAX
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'comment_submission_script.php', true);
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 400) {
                        // Handle the response from the server
                        console.log(xhr.responseText);

                        // Check if the response indicates successful comment submission
                        if (xhr.responseText === "Comment submitted successfully!") {
                            // Update the UI to indicate success (e.g., display a success message)
                            alert("Comment submitted successfully!");

                            // Optionally, clear the comment input field
                            document.getElementById('comment_text').value = '';
                        } else {
                            // Handle other responses or errors
                        }
                    } else {
                        console.error('Error: ' + xhr.status);
                    }
                };
                xhr.onerror = function () {
                    console.error('Request failed');
                };
                xhr.send(formData);
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script>
        //document.querySelector('')
    </script>
</body>
</html>