require_once 'config.php';

$book_id = $_POST['book_id'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$contact = $_POST['contact'];
$email = $_POST['email'];
$return_datetime = $_POST['return_datetime']; // NEW FIELD

$stmt = $mysqli->prepare("INSERT INTO borrow_log 
    (book_id, first_name, last_name, contact, email, borrow_datetime, return_datetime)
    VALUES (?, ?, ?, ?, ?, NOW(), ?)");
$stmt->bind_param("isssss", $book_id, $first_name, $last_name, $contact, $email, $return_datetime);

if ($stmt->execute()) {
    // Mark the book as borrowed in `books` table
    $update = $mysqli->prepare("UPDATE books SET is_borrowed = 1 WHERE id = ?");
    $update->bind_param("i", $book_id);
    $update->execute();
}

header("Location: index.php");
exit;
