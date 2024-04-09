<?php
include 'connection.php';
include 'conn.php';

// Function to delete a category and its subcategories recursively
function deleteCategory($categoryId) {
    global $connect;

    // Delete entries from posts_categories table
    $deletePostsCategoriesSql = "DELETE FROM posts_categories WHERE category_id = ?";
    $stmt = mysqli_prepare($connect, $deletePostsCategoriesSql);
    mysqli_stmt_bind_param($stmt, 'i', $categoryId);
    mysqli_stmt_execute($stmt);

    // Update posts table to remove references to the deleted category
    $updatePostsSql = "UPDATE posts SET category = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', category, ','), ',$categoryId,', ',')) WHERE FIND_IN_SET('$categoryId', category) > 0";
    mysqli_query($connect, $updatePostsSql);
    
    // Delete subcategories recursively
    $subcategories = getSubcategories($categoryId);
    foreach ($subcategories as $subcategory) {
        // Recursively delete subcategory
        deleteCategory($subcategory['id']);
    }

    // Finally, delete the category from the categories table
    $deleteCategorySql = "DELETE FROM categories WHERE id = ?";
    $stmt = mysqli_prepare($connect, $deleteCategorySql);
    mysqli_stmt_bind_param($stmt, 'i', $categoryId);
    mysqli_stmt_execute($stmt);
}

// Function to retrieve subcategories of a given parent category
function getSubcategories($parentId) {
    global $connect;

    // Prepare and execute select query
    $sql = "SELECT * FROM categories WHERE parent_id = ?";
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $parentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch subcategories
    $subcategories = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $subcategories[] = $row;
    }
    return $subcategories;
}

// Handle category deletion
if(isset($_GET['id'])) {
    $category_id = $_GET['id'];

    deleteCategory($category_id);

    header("Location: list.php");
    exit;
}
?>
