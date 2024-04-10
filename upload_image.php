<?php
// Check if image file is uploaded
if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    
    // Move uploaded image to desired directory
    $upload_directory = 'uploads/'; // Directory to store images
    $target_file = $upload_directory . basename($image_name);

    if(move_uploaded_file($image_tmp, $target_file)) {
        // Image uploaded successfully, return its URL
        echo $target_file;
    } else {
        echo "Error uploading image.";
    }
}
?>
