<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $book_id = $_POST['book_id'];
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    // Insert borrow log
    $stmt = $mysqli->prepare("INSERT INTO borrow_log 
        (book_id, first_name, last_name, contact, email) 
        VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $book_id, $first, $last, $contact, $email);
    $stmt->execute();

    // Update book as borrowed
    $mysqli->query("UPDATE books SET is_borrowed = 1 WHERE id = $book_id");

    echo "<script>alert('Borrow Successful!'); window.location='index.php';</script>";
}
?>
