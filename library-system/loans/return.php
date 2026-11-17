<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	redirect('loans/index.php');
}
verify_csrf();
$loanId = filter_var($_POST['loan_id'] ?? null, FILTER_VALIDATE_INT);

try {
	$pdo->beginTransaction();
	$loanQuery = $pdo->prepare('SELECT book_id FROM loans WHERE loan_id = ? AND date_returned IS NULL FOR UPDATE');
	$loanQuery->execute([$loanId]);
	$bookId = $loanQuery->fetchColumn();
	if ($bookId === false) {
		throw new RuntimeException('This loan was already returned or could not be found.');
	}
	$updateLoan = $pdo->prepare('UPDATE loans SET date_returned = CURRENT_DATE WHERE loan_id = ?');
	$updateLoan->execute([$loanId]);
	$updateBook = $pdo->prepare('UPDATE books SET available_copies = LEAST(total_copies, available_copies + 1) WHERE book_id = ?');
	$updateBook->execute([$bookId]);
	$pdo->commit();
	set_flash('success', 'Book marked as returned.');
} catch (RuntimeException $exception) {
	if ($pdo->inTransaction()) {
		$pdo->rollBack();
	}
	set_flash('error', $exception->getMessage());
} catch (PDOException $exception) {
	if ($pdo->inTransaction()) {
		$pdo->rollBack();
	}
	set_flash('error', 'The return could not be recorded. Please try again.');
}
redirect('loans/index.php');
