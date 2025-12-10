<?php
require 'config.php';

$id = intval($_GET['id'] ?? 0);
if(!$id){
    header('Location: index.php');
    exit;
}

// Fetch book
$stmt = $mysqli->prepare("SELECT * FROM books WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$book = $res->fetch_assoc();

if(!$book){
    header('Location: index.php');
    exit;
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $isbn = $_POST['isbn'] ?? null;
    $year = $_POST['year'] ?? null;
    $description = $_POST['description'] ?? null;

    if(trim($title) === '' || trim($author) === ''){
        die("Title and author are required.");
    }

    // Ensure uploads folder exists
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle new cover upload
    $cover = $book['cover']; // keep old by default
    if(isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK){
        $file = $_FILES['cover'];

        // Validate file type
        $allowed = ['image/jpeg','image/jpg','image/png','image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if(!in_array($mime, $allowed)){
            die("Invalid image type. Only JPG, PNG, GIF allowed.");
        }

        // Validate size (max 4MB)
        if($file['size'] > 4 * 1024 * 1024){
            die("File too large. Max 4MB.");
        }

        // Generate unique filename
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newCover = bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = $uploadDir . $newCover;

        if(!move_uploaded_file($file['tmp_name'], $dest)){
            die("Failed to upload new cover image.");
        }

        // Delete old cover if exists
        if($cover && file_exists($uploadDir . $cover)){
            unlink($uploadDir . $cover);
        }

        $cover = $newCover;
    }

    // Update database
    $stmt = $mysqli->prepare("UPDATE books SET title=?, author=?, isbn=?, year=?, description=?, cover=? WHERE id=?");
    $stmt->bind_param('sssissi', $title, $author, $isbn, $year, $description, $cover, $id);
    if(!$stmt->execute()){
        die("Update failed: " . $stmt->error);
    }

    header("Location: index.php");
    exit;
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Book</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <a href="index.php" class="btn btn-link">&larr; Back</a>
  <div class="card p-3">
    <h3>Edit Book</h3>
    <form method="post" enctype="multipart/form-data">
      <div class="mb-2">
        <label class="form-label">Title</label>
        <input name="title" required class="form-control" value="<?= e($book['title']) ?>">
      </div>
      <div class="mb-2">
        <label class="form-label">Author</label>
        <input name="author" required class="form-control" value="<?= e($book['author']) ?>">
      </div>
      <div class="mb-2">
        <label class="form-label">ISBN</label>
        <input name="isbn" class="form-control" value="<?= e($book['isbn']) ?>">
      </div>
      <div class="mb-2">
        <label class="form-label">Year</label>
        <input name="year" type="number" class="form-control" value="<?= e($book['year']) ?>">
      </div>
      <div class="mb-2">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"><?= e($book['description']) ?></textarea>
      </div>
      <div class="mb-2">
        <label class="form-label">Cover (leave blank to keep existing)</label>
        <input type="file" name="cover" accept="image/*" class="form-control">
        <?php if($book['cover'] && file_exists(__DIR__ . '/uploads/'.$book['cover'])): ?>
          <img src="uploads/<?= e($book['cover']) ?>" style="max-width:120px;margin-top:8px;">
        <?php endif; ?>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary">Save</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
