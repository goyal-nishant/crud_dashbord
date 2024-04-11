<?php
include 'conn.php';
include 'header_logout_user.php';

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
    $commenter_name = $_POST['commenter_name'];

    $insert_query = "INSERT INTO posts_comments (post_id, comment_text, commenter_name) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($GLOBALS['conn'], $insert_query);
    mysqli_stmt_bind_param($stmt, "iss", $category_id, $comment_text, $commenter_name);
    $insert_result = mysqli_stmt_execute($stmt);

    if (!$insert_result) {
        echo "Error inserting comment: " . mysqli_error($GLOBALS['conn']);
    }
}

if(isset($_POST['submit_reply'])) {
    $parent_comment_id = $_POST['parent_comment_id'];
    $reply_text = $_POST['reply_text'];
    $replyer_name = $_POST['replyer_name'];

    $insert_reply_query = "INSERT INTO posts_comments (post_id, comment_parent_id, comment_text, commenter_name) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($GLOBALS['conn'], $insert_reply_query);
    mysqli_stmt_bind_param($stmt, "iiss", $category_id, $parent_comment_id, $reply_text, $replyer_name);
    $insert_reply_result = mysqli_stmt_execute($stmt);

    if (!$insert_reply_result) {
        echo "Error inserting reply: " . mysqli_error($GLOBALS['conn']);
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
    <style>
        .comment {
            margin-top: 20px;
        }

        .comment h3 {
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .comment form {
            margin-top: 10px;
        }

        .comment p {
            margin-bottom: 5px;
        }

        .comment .reply {
            margin-left: 20px; /* Indent replies */
        }

        .comment input[type="text"],
        .comment textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }

        .comment input[type="submit"] {
            padding: 8px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .comment input[type="submit"]:hover {
            background-color: #45a049;
        }

        .reply-form {
            display: none;
        }
    </style>
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

    <div class="comment">
        <h3>Add a Comment</h3>
        <form method="post">
            <label for="commenter_name">Your Name:</label><br>
            <input type="text" id="commenter_name" name="commenter_name"><br>
            <label for="comment_text">Your Comment:</label><br>
            <textarea id="comment_text" name="comment_text"></textarea><br>
            <input type="submit" name="submit_comment" value="Submit Comment">
        </form>

        <h3>Comments</h3>
        <?php
        $comments_query = "SELECT * FROM posts_comments WHERE post_id = $category_id ORDER BY created_at ASC";
        $comments_result = mysqli_query($GLOBALS['conn'], $comments_query);

        if ($comments_result && mysqli_num_rows($comments_result) > 0) {
            while($comment = mysqli_fetch_assoc($comments_result)) {
                if ($comment['comment_parent_id'] == 0) {
                    echo "<p><strong>{$comment['commenter_name']}</strong>: {$comment['comment_text']} <br>at: {$comment['created_at']}</p>";

                    $comment_id = $comment['id'];

                    // Fetch and display reply comments
                    $replies_query = "SELECT * FROM posts_comments WHERE comment_parent_id = $comment_id ORDER BY created_at ASC";
                    $replies_result = mysqli_query($GLOBALS['conn'], $replies_query);

                    if ($replies_result && mysqli_num_rows($replies_result) > 0) {
                        while ($reply = mysqli_fetch_assoc($replies_result)) {
                            echo "<p class='reply'><strong>{$reply['commenter_name']}</strong>: {$reply['comment_text']} <br>at: {$reply['created_at']}</p>";
                        }
                    }

                    echo "<button class='reply-button' data-comment-id='{$comment_id}'>Reply</button>";

                    echo "<form class='reply-form' id='reply-form-{$comment_id}' method='post' style='display: none;'>";
                    echo "<input type='hidden' name='parent_comment_id' value='{$comment['id']}'>";
                    echo "<label for='replyer_name'>Your Name:</label><br>";
                    echo "<input type='text' id='replyer_name' name='replyer_name'><br>";
                    echo "<label for='reply_text'>Your Reply:</label><br>";
                    echo "<textarea id='reply_text' name='reply_text'></textarea><br>";
                    echo "<input type='submit' name='submit_reply' value='Submit Reply'>";
                    echo "</form>";
                }
            }
        } else {
            echo "No comments found.";
        }
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
    </script>
</body>
</html>

