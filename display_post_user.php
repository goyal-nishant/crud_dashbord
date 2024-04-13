<?php
include 'conn.php';
include 'header_logout_user.php';
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location: login_for_users.php');
    exit();
}
$replyers_name = $_SESSION['user_name'];
if (isset($_GET['id'])) {
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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>
    <link rel="stylesheet" href="display_posts.css">
    <link rel="stylesheet" href="display_user_post.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384- QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <div class="container">
        <?php if (isset($data)) : ?>
            <h2 class="post-title"><?= $data['title']; ?></h2>
            <div class="post-content">
                <div class="image">
                    <img src='uploads/<?= $data["image"] ?>' style='max-width: 220px; max-height: 220px;' alt='Post Image'>
                </div>
                <div class="description">
                    <?= $data['description'] ?>
                </div>
            </div>
        <?php else : ?>
            <p>No post found.</p>
        <?php endif ?>
    </div>
    <button class="comment-head btn btn-primary" onclick="showHide()">Add
        comment</button>
    <div class="comment gradient-custom container my_comment" style="display: none;">
        <h3 style="color: white;">Add a Comment</h3>
        <form id="comment-form" method="post">
            <input type="hidden" name="user_name" value="<?php echo
                                                            $_SESSION['user_name']; ?>">
            <textarea name="comment_text" id="text_data"></textarea>
            <small class='text-danger hide' style='display: none' id='showContext'>Please
                Enter the data into that </small>
            <input type="button" id="show_data" name="submit_comment" class="btn btn-success" value="Submit Comment">
        </form>
        <?php
        function display_comments($parent_id = 0, $level = 0)
        {
            global $category_id, $replyers_name;
            $comments_query = "SELECT * FROM posts_comments WHERE post_id = $category_id AND comment_parent_id = $parent_id ORDER BY created_at DESC";
            $comments_result = mysqli_query($GLOBALS['conn'], $comments_query);
            if ($comments_result && mysqli_num_rows($comments_result) > 0) {
        ?>
                <div class="outer-comment-container">
                    <?php while ($comment = mysqli_fetch_assoc($comments_result)) : ?>
                        <div id='comment-<?php echo $comment['id']; ?>' class='comment' style='margin-left: <?php echo ($level * 20); ?>px;background-color:white;padding:30px'>
                            <p class='container custom_data' style='height:auto;'>
                                <strong><?php echo $comment['commenter_name']; ?></strong>: <?php echo $comment['comment_text']; ?><br>
                                at: <?php echo $comment['created_at']; ?>
                            </p>
                            <?php if ($replyers_name === $comment['commenter_name']) : ?>
                                <div class='comment-actions'>
                                    <button class='delete-comment-button btn btn-danger' data-comment-id='<?php echo $comment['id']; ?>'>Delete</button>
                                </div>
                            <?php endif; ?>
                            <button class='reply-button' data-comment-id='<?php echo $comment['id']; ?>'>Reply</button>
                            <form class='reply-form' id='reply-form-<?php echo $comment['id']; ?>' method='post' style='display: none;'>
                                <input type='hidden' name='commenter_name' value='<?php echo $_SESSION['user_name']; ?>'>
                                <input type='hidden' name='parent_comment_id' value='<?php echo $comment['id']; ?>'>
                                <label for='reply_text'>Your Reply:</label><br>
                                <textarea id='reply_text-<?php echo $comment['id']; ?>' name='reply_text'></textarea><br>
                                <input type='submit' name='submit_reply' class='btn btn-success' value='Submit Reply'>
                            </form>
                            <form class='update-comment-form' id='update-form-<?php echo $comment['id']; ?>' method='post' style='display: none;'>
                                <input type='hidden' name='comment_id' value='<?php echo $comment['id']; ?>'>
                                <label for='updated_comment_text'>Updated Comment:</label><br>
                                <textarea id='updated_comment_text-<?php echo $comment['id']; ?>' name='updated_comment_text'></textarea><br>
                                <input type='submit' name='update_comment' class='btn btn-primary' value='Update Comment'>
                            </form>
                            <?php display_comments($comment['id'], $level + 1); ?>
                        </div>
                    <?php endwhile; ?>
                </div>

    </div>
<?php
            }
        }
        display_comments();
?>

</div>
<script>
    $(document).ready(function() {
        $('.delete-comment-button').click(function() {
            var commentId = $(this).data('comment-id');

            var confirmation = confirm("Are you sure you want to delete this comment?");

            // If user confirms deletion
            if (confirmation) {
                $.ajax({
                    type: 'POST',
                    url: 'delete.php',
                    data: {
                        comment_id: commentId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Remove the comment from the DOM
                            $('#comment-' + commentId).remove();
                        } else {
                            alert('Failed to delete comment. Please try again later.');
                        }
                    },
                    error: function() {
                        alert('Failed to delete comment due to an internal error. Please try again later.');
                    }
                });
            }
        });
    });

    document.querySelectorAll('.reply-button').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const replyForm = document.querySelector(`#reply-form-${commentId}`);
            if (replyForm) {
                replyForm.style.display = replyForm.style.display === 'none' ? 'block' :
                    'none';
            } else {
                console.error('Reply form not found for comment ID:', commentId);
            }
        });
    });
    document.querySelectorAll('.update-comment-button').forEach(button => {
        button.addEventListener('click', function() {
            const commentId =
                this.nextElementSibling.querySelector('input[name="comment_id"]').value;
            const updateForm = document.querySelector(`#update-form- ${commentId}`);
            if (updateForm) {
                updateForm.style.display = updateForm.style.display === 'none' ?
                    'block' : 'none';
            } else {
                console.error('Update form not found for comment ID:', commentId);
            }
        });
    });
</script>

<script>
    let comment = document.querySelector('.comment-head');
    let comment_text = document.querySelector('.comment');
    let isShow = true;

    function showHide() {
        if (isShow) {
            comment_text.style.display = "none";
            isShow = false;
        } else {
            comment_text.style.display = "block";
            isShow = true;
        }
    }
    $(document).ready(function() {
        function addNewComment(comment) {
            $('.outer-comment-container').prepend("<div id='comment-" + comment.id + "' class='comment' style='margin-left: 12px; background-color: white; padding: 30px;'>" +
                "<p class='container custom_data' style='height:auto;'><strong>" + comment.commenter_name + "</strong>: " + comment.comment_text + "<br>" + "at: " + comment.created_at + "</p></div>");
        }

        $("#show_data").click(function() {
            console.log("first comment")
            var text = $("#text_data").val();
            var category_id = <?php echo $category_id ?>;
            if (text) {
                $.ajax({
                    type: 'POST',
                    url: 'comment_submission_script.php',
                    data: {
                        comment_text: text,
                        category_id: category_id
                    },
                    success: function(data) {
                        if (data) {
                            let response = JSON.parse(data);
                            addNewComment(response);
                            $('#comment-form')[0].reset();
                        } else {
                            // If there are no existing comments, add the first one directly
                            var response = {
                                id: 0, // Set a dummy ID for the first comment
                                commenter_name: '<?php echo $_SESSION['user_name']; ?>',
                                comment_text: text,
                                created_at: new Date().toLocaleString() // Use current time as the creation time
                            };
                            addNewComment(response);
                            $('#comment-form')[0].reset();
                        }
                    }
                });
            } else {
                $('#showContext').show();
            }
        });
    });

    function addNewChildComment(comment) {
        const parentCommentId = comment.comment_parent_id;
        const parentCommentElement = $('#comment-' + parentCommentId);

        if (parentCommentElement.length > 0) {
            parentCommentElement.append("<div id='comment-" + comment.id + "' class='comment' style='margin-left: 12px; background-color: white; padding: 30px;'>" +
                "<p class='container custom_data' style='height:auto;'><strong>" + comment.commenter_name + "</strong>: " + comment.comment_text + "<br>" + "at: " + comment.created_at + "</p></div>");
        } else {
            console.error("Parent comment element not found for comment ID: " + parentCommentId);
        }
    }


    $('.reply-form').submit(function(event) {
        event.preventDefault();

        var formData = $(this).serialize();
        var categoryId = $(this).find('input[name="category_id"]').val();
        var replyText = $("#reply_text").val(); // Assuming reply_text is the ID of your input field

        formData += '&category_id=<?php echo $category_id; ?>';

        $.ajax({
            type: 'POST',
            url: 'reply_handler.php',
            data: formData,
            success: function(response) {
                if (response) {
                    let data = JSON.parse(response);
                    addNewChildComment(data);
                    $('#comment-form')[0].reset();
                    $('#reply-form-' + data.comment_parent_id).hide();
                    console.log('Reply form hidden for parent comment ID:', data.comment_parent_id);
                } else {
                    alert('Failed to add comment. Please try again later.');
                }
            },
            error: function() {
                // Handle errors
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-
    I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384- 0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
</script>
<script>
    //document.querySelector('') 
</script>
</body>

</html>