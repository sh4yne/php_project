<?php
require_once 'config.php';

$log_id = intval($_GET['log_id']);

$stmt = $mysqli->prepare("UPDATE borrow_log SET return_datetime = NOW() WHERE id = ? AND return_datetime IS NULL");
$stmt->bind_param("i", $log_id);

if ($stmt->execute()) {
    header("Location: index.php?message=returned");
    exit();
} else {
    echo "Error updating return date.";
}

$stmt->close();
$mysqli->close();
?>
