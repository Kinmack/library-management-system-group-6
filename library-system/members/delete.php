<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	redirect('members/index.php');
}
verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
try {
	$statement = $pdo->prepare('DELETE FROM members WHERE member_id = ?');
	$statement->execute([$id]);
	$deleted = $statement->rowCount() > 0;
	set_flash($deleted ? 'success' : 'error', $deleted ? 'Member deleted.' : 'Member not found.');
} catch (PDOException $exception) {
	set_flash('error', 'This member cannot be deleted while loan records refer to them.');
}
redirect('members/index.php');
