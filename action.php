<?php
require 'config.php';

$id = intval($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

if($id && in_array($action, ['borrow','return'])){
    $now = date('Y-m-d H:i:s'); // current timestamp

    if($action === 'borrow'){
        $stmt = $mysqli->prepare("UPDATE books SET is_borrowed=1, borrowed_at=?, returned_at=NULL WHERE id=?");
        $stmt->bind_param('si', $now, $id);
    } else { // return
        $stmt = $mysqli->prepare("UPDATE books SET is_borrowed=0, returned_at=? WHERE id=?");
        $stmt->bind_param('si', $now, $id);
    }

    $stmt->execute();
}

header('Location: index.php');
exit;
