<?php
require 'config.php';

if (!isset($_GET['id'])) die("Invalid request");

$id = intval($_GET['id']);

// Update book as borrowed
$mysqli->query("
    UPDATE books 
    SET is_borrowed = 1, borrowed_at = NOW(), returned_at = NULL
    WHERE id = $id
");

// Insert into borrow_log
$mysqli->query("
    INSERT INTO borrow_log (book_id, borrow_datetime)
    VALUES ($id, NOW())
");

header("Location: books.php");
exit;
?>
