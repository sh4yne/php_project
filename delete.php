<?php
require 'config.php';
$id = intval($_POST['id'] ?? 0);
if($id){
    // optionally delete cover file
    $stmt = $mysqli->prepare("SELECT cover FROM books WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if($res && $res['cover'] && file_exists('uploads/'.$res['cover'])){
        @unlink('uploads/'.$res['cover']);
    }

    $stmt = $mysqli->prepare("DELETE FROM books WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}
header('Location: index.php');
exit;
