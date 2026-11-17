<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$members = $pdo->query("SELECT member_id, member_number, full_name FROM members WHERE status = 'active' ORDER BY full_name")->fetchAll();
$books = $pdo->query('SELECT book_id, title, isbn, available_copies FROM books WHERE available_copies > 0 ORDER BY title')->fetchAll();
$error = '';
$dueDate = $_POST['due_date'] ?? date('Y-m-d', strtotime('+14 days'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$memberId = filter_var($_POST['member_id'] ?? null, FILTER_VALIDATE_INT);
	$bookId = filter_var($_POST['book_id'] ?? null, FILTER_VALIDATE_INT);
	$parsedDueDate = DateTime::createFromFormat('Y-m-d', $dueDate);

	if (!$memberId || !$bookId || !$parsedDueDate || $parsedDueDate->format('Y-m-d') !== $dueDate || $dueDate < date('Y-m-d')) {
		$error = 'Choose an active member, an available book, and a valid due date.';
	} else {
		try {
			$pdo->beginTransaction();
			$memberQuery = $pdo->prepare("SELECT status FROM members WHERE member_id = ? FOR UPDATE");
			$memberQuery->execute([$memberId]);
			$memberStatus = $memberQuery->fetchColumn();
			$bookQuery = $pdo->prepare('SELECT available_copies FROM books WHERE book_id = ? FOR UPDATE');
			$bookQuery->execute([$bookId]);
			$available = $bookQuery->fetchColumn();

			if ($memberStatus !== 'active' || $available === false || (int) $available < 1) {
				throw new RuntimeException('That member or book is no longer available for a loan.');
			}
			$insert = $pdo->prepare('INSERT INTO loans (members_id, book_id, date_issued, due_date) VALUES (?, ?, CURRENT_DATE, ?)');
			$insert->execute([$memberId, $bookId, $dueDate]);
			$update = $pdo->prepare('UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ?');
			$update->execute([$bookId]);
			$pdo->commit();
			set_flash('success', 'Book issued successfully.');
			redirect('loans/index.php');
		} catch (RuntimeException $exception) {
			if ($pdo->inTransaction()) {
				$pdo->rollBack();
			}
			$error = $exception->getMessage();
		} catch (PDOException $exception) {
			if ($pdo->inTransaction()) {
				$pdo->rollBack();
			}
			$error = 'The loan could not be saved. Refresh the page and try again.';
		}
	}
}

$pageTitle = 'Issue a book';
require __DIR__ . '/../includes/header.php';
?>
<section class="form-page"><p class="eyebrow">CIRCULATION</p><h1>Issue a book</h1><p class="muted">Only active members and available copies can be selected.</p><form class="form-panel" method="post">
<?php if ($error !== ''): ?><div class="notice notice-error" role="alert"><?= e($error) ?></div><?php endif; ?><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-grid">
<label>Member<select name="member_id" required><option value="">Choose a member</option><?php foreach ($members as $member): ?><option value="<?= (int) $member['member_id'] ?>" <?= (string) ($_POST['member_id'] ?? '') === (string) $member['member_id'] ? 'selected' : '' ?>><?= e($member['full_name'] . ' · ' . $member['member_number']) ?></option><?php endforeach; ?></select></label>
<label>Book<select name="book_id" required><option value="">Choose an available book</option><?php foreach ($books as $book): ?><option value="<?= (int) $book['book_id'] ?>" <?= (string) ($_POST['book_id'] ?? '') === (string) $book['book_id'] ? 'selected' : '' ?>><?= e($book['title'] . ' · ' . $book['available_copies'] . ' available') ?></option><?php endforeach; ?></select></label>
<label>Due date<input type="date" name="due_date" required min="<?= e(date('Y-m-d')) ?>" value="<?= e($dueDate) ?>"></label>
</div><div class="form-actions"><button class="button button-primary" type="submit">Confirm issue</button><a class="button button-secondary" href="<?= e(url('loans/index.php')) ?>">Cancel</a></div></form></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
