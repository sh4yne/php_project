<?php
require 'config.php';

// Fetch borrow log
$result = $mysqli->query("
    SELECT b.title, b.author, l.borrow_datetime, l.return_datetime, l.id as log_id
    FROM borrow_log l
    JOIN books b ON l.book_id = b.id
    ORDER BY l.borrow_datetime DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Borrow Log</title>
</head>
<body>
<h1>Borrow Log</h1>
<table border="1" cellpadding="5">
<tr>
    <th>Title</th>
    <th>Author</th>
    <th>Borrowed At</th>
    <th>Returned At</th>
    <th>Action</th>
</tr>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['title']) ?></td>
    <td><?= htmlspecialchars($row['author']) ?></td>
    <td><?= $row['borrow_datetime'] ?></td>
    <td><?= $row['return_datetime'] ?? "-" ?></td>
    <td>
        <?php if (!$row['return_datetime']): ?>
            <a href="return_book.php?id=<?= $row['log_id'] ?>">Return</a>
        <?php else: ?>
            Returned
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
<br>
<a href="books.php">Back to Books</a>
</body>
</html>
