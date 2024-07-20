<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location: login.php');
    exit();
}

include 'conn.php';
$message = "";
$error = array(); 

$sql = "SELECT id, name FROM categories WHERE parent_id = '0'";
$result = mysqli_query($GLOBALS['conn'], $sql);

$categories = array();

if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $categories[$row['id']] = $row['name']; 
    }
}

$subcategories = array();

foreach ($categories as $catId => $catName) {
    $subcategoriesSql = "SELECT id, name FROM categories WHERE parent_id = '$catId'";
    $subResult = mysqli_query($GLOBALS['conn'], $subcategoriesSql);
    if(mysqli_num_rows($subResult) > 0) {
        while($subRow = mysqli_fetch_assoc($subResult)) {
            $subcategories[$catId][$subRow['id']] = $subRow['name'];
        }
    }
}

if(isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    if(isset($_POST['categories'])) {
        $selected_categories = $_POST['categories']; 
    } else {
        $selected_categories = array(); 
    }
    $status = $_POST['status'];

    if(empty($title)) {
        $error['title'] = "Please insert a title.";
    }

    if(empty($description)) {
        $error['description'] = "Please Insert a description.";
    }  

    if(empty($selected_categories) || in_array('select', $selected_categories)) {
        $error['categories'] = "Please select at least one category.";
    }

    if($status === 'select') {
        $error['status'] = "Please select a status.";
    }

    $upload_directory = "uploads/";
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];

    if (empty($image_name)) {
        $error['image'] = "Please select an image.";
    } elseif ($image_size > 5000000) { 
        $error['image'] = "Image size must be less than 5MB";
    } else {
        $target_file = $upload_directory . basename($image_name);
        if (!move_uploaded_file($image_tmp, $target_file)) {
            $error['image'] = "Error uploading image";
        }
    }

    if(empty($error)) {
        $categoryString = implode(',', $selected_categories);
        
        $sql = "INSERT INTO posts (title, description, category, status, image) VALUES ('$title', '$description', '$categoryString', '$status', '$image_name')";
        $result = mysqli_query($GLOBALS['conn'], $sql);
        
        if($result) {
            $message = "Post inserted successfully.";

            $postId = mysqli_insert_id($GLOBALS['conn']);

            foreach($selected_categories as $categoryId) {
                $insertPostsCategoriesQuery = "INSERT INTO posts_categories (post_id, category_id) VALUES ('$postId', '$categoryId')";
                mysqli_query($GLOBALS['conn'], $insertPostsCategoriesQuery);
            }
        } else {
            $message = "Error: " . mysqli_error($GLOBALS['conn']);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="create_post.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="download.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="topnav" id="myTopnav">
        <a href="http://localhost/crudByoops/home.php" class="active" required>Home</a>
        <a href="http://localhost/crudByoops/list.php" required>Category</a>
        <a href="http://localhost/crudByoops/view_post.php" required>Post</a>
        <a href="logout.php"><input type="submit" name="" value="logout"></a>
        <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
    </div>
    <script>
        function myFunction() {
            var x = document.getElementById("myTopnav");
            if (x.className === "topnav") {
                x.className += " responsive";
            } else {
                x.className = "topnav";
            }
        }
    </script>
    <div class="border container pb-5 pt-3 ml-1" style="text-align: center;">
        <h3>Create Posts</h3>
    </div>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" minlength="7">
            <span><?php echo isset($error['title']) ? $error['title'] : "";  ?></span>
        </div>
        <div class="mb-3">
            <label for="desc" class="form-label">Description</label>
            <textarea class="form-control" id="desc" name="description"></textarea>
            <span><?php echo isset($error['description']) ? $error['description'] : ""; ?></span>
        </div>
        
        <label for="category">Categories</label>
        <?php foreach($categories as $catId => $catName): ?>
            <div>
                <input type="checkbox" id="category<?php echo $catId; ?>" name="categories[]" value="<?php echo $catId; ?>">
                <label for="category<?php echo $catId; ?>"><?php echo $catName; ?></label>
                <?php if(isset($subcategories[$catId])): ?>
                    <div style="margin-left: 20px;">
                        <?php foreach($subcategories[$catId] as $subcatId => $subcatName): ?>
                            <div>
                                <input type="checkbox" id="subcategory<?php echo $subcatId; ?>" name="categories[]" value="<?php echo $subcatId; ?>">
                                <label for="subcategory<?php echo $subcatId; ?>"><?php echo $subcatName; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" id="image" name="image">
            <span><?=isset($error['image']) ? $error['image'] : ""?></span>
        </div>

        <label for="status">Status</label>
        <select name="status" id="status" required>
            <option value="select">Select</option>
            <option value="Draft">Draft</option>
            <option value="publish">Publish</option>
        </select>

        <input type="submit" value="Upload Post" name="submit" class="btn btn-primary butn batn">
        <a href="http://localhost/crudByoops/view_post.php"><input type="button" value="view post" name="view post" class="btn btn-primary butn"></a>
        <p class="message"><?php echo $message; ?></p>
    </form>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#desc").summernote({
                placeholder: "Enter Description",
                height: 300,
                callbacks: {
                    onImageUpload: function(files) {
                        uploadImage(files[0]);
                    }
                }
            });

            function uploadImage(file) {
                var formData = new FormData();
                formData.append('image', file);

                $.ajax({
                    url: 'upload_image.php', 
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        var imageUrl = response;
                        $('#desc').summernote('insertImage', imageUrl);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    </script>
</body>
</html>
