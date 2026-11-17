<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$filter = $_GET['status'] ?? 'all';
$where = match ($filter) {
	'open' => 'WHERE l.date_returned IS NULL',
	'returned' => 'WHERE l.date_returned IS NOT NULL',
	default => '',
};
$loans = $pdo->query(
	'SELECT l.loan_id, l.date_issued, l.due_date, l.date_returned,
			b.title, b.isbn, m.full_name, m.member_number
	FROM loans l JOIN books b ON b.book_id = l.book_id
	JOIN members m ON m.member_id = l.members_id ' . $where . '
	 ORDER BY (l.date_returned IS NULL) DESC, l.due_date ASC, l.loan_id DESC'
)->fetchAll();
$pageTitle = 'Loans';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">CIRCULATION</p><h1>Loans</h1><p class="muted">Review due dates, returns, and current borrowing.</p></div><a class="button button-primary" href="<?= e(url('loans/issue.php')) ?>">Issue a book</a></section>
<div class="toolbar">
	<span class="muted" aria-live="polite" data-result-count data-singular="record" data-plural="records"><?= count($loans) ?> record<?= count($loans) === 1 ? '' : 's' ?></span>
	<div class="toolbar-controls">
		<div class="search-form" role="search">
			<label class="sr-only" for="loan-search">Search loans by book, member, or status</label>
			<input id="loan-search" type="search" data-table-search="#loans-table" data-count-target="[data-result-count]" placeholder="Search book, member, or status">
		</div>
		<form class="search-form" method="get">
			<label class="sr-only" for="loan-status">Filter loans</label>
			<select id="loan-status" name="status"><option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All loans</option><option value="open" <?= $filter === 'open' ? 'selected' : '' ?>>On loan</option><option value="returned" <?= $filter === 'returned' ? 'selected' : '' ?>>Returned</option></select>
			<button class="button button-secondary" type="submit">Filter</button>
		</form>
	</div>
</div>
<div class="table-wrap"><table id="loans-table"><thead><tr><th>Book</th><th>Member</th><th>Issued</th><th>Due</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach ($loans as $loan): $isOpen = $loan['date_returned'] === null; $isOverdue = $isOpen && $loan['due_date'] < date('Y-m-d'); ?>
<tr data-search-row><td><span class="primary-cell"><?= e($loan['title']) ?></span><br><small><?= e($loan['isbn']) ?></small></td><td><?= e($loan['full_name']) ?><br><small><?= e($loan['member_number']) ?></small></td><td><?= e($loan['date_issued']) ?></td><td><?= e($loan['due_date']) ?></td><td><span class="status <?= $isOverdue ? 'status-overdue' : ($isOpen ? 'status-open' : 'status-done') ?>"><?= $isOverdue ? 'Overdue' : ($isOpen ? 'On loan' : 'Returned') ?></span></td><td><?php if ($isOpen): ?><form class="inline-form" method="post" action="<?= e(url('loans/return.php')) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="loan_id" value="<?= (int) $loan['loan_id'] ?>"><button class="link-button" type="submit">Mark returned</button></form><?php else: ?><?= e($loan['date_returned']) ?><?php endif; ?></td></tr>
<?php endforeach; ?>
<tr data-filter-empty hidden><td class="empty-state" colspan="6"><?= $loans ? 'No loans match your search.' : 'No loans match this view.' ?></td></tr>
</tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
return.php