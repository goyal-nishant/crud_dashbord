<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location: login_for_users.php');
    exit();
}
include 'connection.php';
include 'header_logout_user.php';

function showcategory($parentid, $level = 0) {
    $output = ""; // Initialize $output variable
    $sql = "SELECT * FROM categories WHERE parent_id = $parentid";
    $result = mysqli_query($GLOBALS['connect'], $sql);
    
    // Initialize $parentCount variable
    $parentCount = 0;

    // Start the container for parent categories
    if ($level === 0) {
        $output .= "<div class='containers'>\n";
    }
    
    while($data = mysqli_fetch_array($result)) {
        if ($level === 0 && $parentCount % 2 === 0) {
            $output .= "<div class='row'>\n";
        }

        $output .= "<div class='col-md-6'>\n";
        
        $output .= "<div class='category-box'>\n";
        $output .= "<div class='category-header'><h4><a href='view_posts_by_category_for_uses.php?category_id=".$data['id']."'>" . $data["name"] . "</a></h4></div>\n";
        
        $output .= "<div class='category-content'>\n";
        $output .= showcategory($data['id'], $level + 1); 
        $output .= "</div>\n";
        
        $output .= "</div>\n"; 
        
        $output .= "</div>\n"; 

        if ($level === 0 && $parentCount % 2 === 1) {
            $output .= "</div>\n";
        }

        $parentCount++;
    }
    
    if ($level === 0) {
        if ($parentCount % 2 === 1) {
            $output .= "</div>\n";
        }
        $output .= "</div>\n";
    }
    
    return $output;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category list</title>
    <link rel="stylesheet" href="list.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="view_category_public.css">
    <link rel="icon" type="image/x-icon" href="download.png">

</head>
<body>
<div class="border container pb-5 pt-3 ml-1" style="text-align: center;">
    <h3>Categories</h3>
</div>

<?=showcategory(0);?>

<script>
function confirmDelete() {
    return confirm("Are you sure you want to delete this category?");
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
