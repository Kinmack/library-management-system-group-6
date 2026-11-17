<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	redirect('books/index.php');
}
verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
try {
	$statement = $pdo->prepare('DELETE FROM books WHERE book_id = ?');
	$statement->execute([$id]);
	set_flash($statement->rowCount() ? 'success' : 'error', $statement->rowCount() ? 'Book deleted.' : 'Book not found.');
} catch (PDOException $exception) {
	set_flash('error', 'This book cannot be deleted while it has loan records.');
}
redirect('books/index.php');
