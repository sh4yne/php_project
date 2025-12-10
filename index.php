<?php
require_once 'config.php';

// search
$q = $_GET['q'] ?? '';
$q_param = "%$q%";

$stmt = $mysqli->prepare("SELECT id, title, author, isbn, year, description, cover, is_borrowed 
    FROM books
    WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ?
    ORDER BY created_at DESC");
$stmt->bind_param('sss', $q_param, $q_param, $q_param);
$stmt->execute();
$res = $stmt->get_result();
$books = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Digital Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .cover-thumb { width: 100px; height: 140px; object-fit: cover; border:1px solid #ddd; }
    .card-grid { display:grid; grid-template-columns: repeat(auto-fill,minmax(280px,1fr)); gap:1rem; }
  </style>
</head>
<body class="bg-light">
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Digital Library</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Book</button>
  </div>

  <form class="mb-3" method="get">
    <div class="input-group">
      <input type="search" name="q" class="form-control" placeholder="Search by title, author, ISBN"
             value="<?= e($q) ?>">
      <button class="btn btn-outline-secondary" type="submit">Search</button>
      <a class="btn btn-outline-secondary" href="index.php">Reset</a>
    </div>
  </form>

  <?php if(empty($books)): ?>
    <div class="alert alert-info">No books found.</div>
  <?php else: ?>
    <div class="card-grid">
      <?php foreach($books as $b): ?>
        <div class="card">
          <div class="row g-0">
            <div class="col-auto p-2">
              <?php if($b['cover'] && file_exists(__DIR__ . '/uploads/'.$b['cover'])): ?>
                <img src="uploads/<?= e($b['cover']) ?>" alt="" class="cover-thumb">
              <?php else: ?>
                <div class="cover-thumb d-flex align-items-center justify-content-center text-muted bg-white">No cover</div>
              <?php endif; ?>
            </div>
            <div class="col">
              <div class="card-body">
                <h5 class="card-title mb-1"><?= e($b['title']) ?></h5>
                <p class="mb-1"><small class="text-muted"><?= e($b['author']) ?> • <?= e($b['year']) ?></small></p>
                <p class="mb-2 small"><?= e(strlen($b['description'])>150? substr($b['description'],0,150).'...': $b['description']) ?></p>

                <div class="d-flex gap-2">
                  <a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?= $b['id'] ?>">Edit</a>

                  <form method="post" action="delete.php" onsubmit="return confirm('Delete this book?');" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $b['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>

                  <?php if(!$b['is_borrowed']): ?>
                    <button class="btn btn-sm btn-success"
                            data-bs-toggle="modal"
                            data-bs-target="#borrowModal"
                            data-book-id="<?= $b['id'] ?>">
                      Borrow
                    </button>
                  <?php else: ?>
                    <form method="post" action="action.php" style="display:inline;">
                      <input type="hidden" name="id" value="<?= $b['id'] ?>">
                      <input type="hidden" name="action" value="return">
                      <button class="btn btn-sm btn-warning">Return</button>
                    </form>
                  <?php endif; ?>
                </div>

              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>


  <!-- Add Modal -->
  <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" method="post" action="add.php" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Add Book</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Title</label>
            <input name="title" required class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label">Author</label>
            <input name="author" required class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label">ISBN</label>
            <input name="isbn" class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label">Year</label>
            <input name="year" type="number" class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
          </div>
          <div class="mb-2">
            <label class="form-label">Cover (jpg/png, max 4MB)</label>
            <input type="file" name="cover" accept="image/*" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Add</button>
        </div>
      </form>
    </div>
  </div>


  <!-- Borrow Modal -->
  <div class="modal fade" id="borrowModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" method="post" action="borrow_save.php">
        <div class="modal-header">
          <h5 class="modal-title">Borrow Book</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="book_id" id="borrow_book_id">

          <div class="mb-2">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" required class="form-control">
          </div>

          <div class="mb-2">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" required class="form-control">
          </div>

          <div class="mb-2">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact" class="form-control">
          </div>

          <div class="mb-2">
            <label class="form-label">Email</label>
            <input type="email" name="email" required class="form-control">
          </div>

          <div class="mb-2">
            <label class="form-label">Return Date & Time</label>
            <input type="datetime-local" name="return_datetime" required class="form-control">
          </div>

        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Confirm Borrow</button>
        </div>
      </form>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
var borrowModal = document.getElementById('borrowModal');
borrowModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var bookId = button.getAttribute('data-book-id');
    document.getElementById('borrow_book_id').value = bookId;
});
</script>

</body>
</html>
