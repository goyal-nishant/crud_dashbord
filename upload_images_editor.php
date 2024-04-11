<?php
$upload_directory = "uploads/"; // Directory where you want to store uploaded images

if ($_FILES['image']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['image']['tmp_name'])) {
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];

    // Check file size
    if ($image_size > 5000000) { // 5MB
        echo 'Image size must be less than 5MB';
        exit();
    }

    // Check file type
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
    $file_extension = pathinfo($image_name, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $allowed_extensions)) {
        echo 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.';
        exit();
    }

    // Generate a unique filename to prevent overwriting existing files
    $unique_filename = uniqid('image_') . '.' . $file_extension;
    $target_file = $upload_directory . $unique_filename;

    // Move the uploaded image to the target directory
    if (move_uploaded_file($image_tmp, $target_file)) {
        $image_url = $upload_directory . $unique_filename;
        echo $image_url; // Return the URL of the uploaded image
    } else {
        echo 'Error uploading image';
    }
} else {
    echo 'Invalid image';
}
?>
