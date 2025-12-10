<?php
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_FILES['cover'])){
        $file = $_FILES['cover'];
        $target = __DIR__ . '/uploads/' . $file['name'];
        if(move_uploaded_file($file['tmp_name'], $target)){
            echo "Upload successful!<br>";
            echo "<img src='uploads/" . htmlspecialchars($file['name']) . "' style='max-width:200px;'>";
        } else {
            echo "Failed to move uploaded file. Check folder permissions.";
        }
    } else {
        echo "No file detected! Did you include enctype='multipart/form-data'?";
    }
}
?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="cover">
    <button>Upload</button>
</form>
