<?php
// delete.php
include_once 'Database.php';
include_once 'Book.php';

$database = new Database();
$db = $database->getConnection();

$bookId = isset($_GET['id']) ? $_GET['id'] : die('ID inválido');

$book = Book::find($bookId, $db);

if($book->delete()) {
    header("Location: index.php?status=deleted");
} else {
    header("Location: index.php?status=error");
}
?>