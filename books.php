<?php
require 'config.php';

// Fetch all books
$result = $mysqli->query("SELECT * FROM books ORDER BY title ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Books</title>
</head>
<body>
<h1>Books</h1>
<table border="1" cellpadding="5">
<tr>
    <th>Title</th>
    <th>Author</th>
    <th>Status</th>
    <th>Action</th>
</tr>
<?php while($b = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($b['title']) ?></td>
    <td><?= htmlspecialchars($b['author']) ?></td>
    <td><?= $b['is_borrowed'] ? "Borrowed" : "Available" ?></td>
    <td>
        <?php if (!$b['is_borrowed']): ?>
            <a href="borrow_book.php?id=<?= $b['id'] ?>">Borrow</a>
        <?php else: ?>
            <a href="return_book.php?id=<?= $b['id'] ?>">Return</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
<br>
<a href="borrow_log.php">View Borrow Log</a>
</body>
</html>
