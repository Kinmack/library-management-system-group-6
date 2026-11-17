<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$stats = [
    'books' => (int) $pdo->query('SELECT COUNT(*) FROM books')->fetchColumn(),
    'copies' => (int) $pdo->query('SELECT COALESCE(SUM(available_copies), 0) FROM books')->fetchColumn(),
    'members' => (int) $pdo->query("SELECT COUNT(*) FROM members WHERE status = 'active'")->fetchColumn(),
    'loans' => (int) $pdo->query('SELECT COUNT(*) FROM loans WHERE date_returned IS NULL')->fetchColumn(),
];
$recentLoans = $pdo->query(
    'SELECT l.date_issued, l.due_date, b.title, m.full_name, l.date_returned
    FROM loans l JOIN books b ON b.book_id = l.book_id
    JOIN members m ON m.member_id = l.members_id
    ORDER BY l.date_issued DESC, l.loan_id DESC LIMIT 6'
)->fetchAll();

$pageTitle = 'Overview';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading">
    <div><p class="eyebrow">LIBRARY OPERATIONS</p><h1>Overview</h1><p class="muted">A clear view of your collection and circulation.</p></div>
    <a class="button button-primary" href="<?= e(url('loans/issue.php')) ?>">Issue a book</a>
</section>
<section class="stats-grid" aria-label="Library statistics">
    <article class="stat"><span>Titles in collection</span><strong><?= number_format($stats['books']) ?></strong><small>Distinct catalog records</small></article>
    <article class="stat stat-green"><span>Copies available</span><strong><?= number_format($stats['copies']) ?></strong><small>Ready to borrow</small></article>
    <article class="stat stat-orange"><span>Active members</span><strong><?= number_format($stats['members']) ?></strong><small>In good standing</small></article>
    <article class="stat stat-blue"><span>On loan</span><strong><?= number_format($stats['loans']) ?></strong><small>Not yet returned</small></article>
</section>
<section class="content-section">
    <div class="section-heading"><div><p class="eyebrow">RECENT ACTIVITY</p><h2>Latest loans</h2></div><a class="text-link" href="<?= e(url('loans/index.php')) ?>">All loans <span aria-hidden="true">&rarr;</span></a></div>
    <div class="table-wrap"><table><thead><tr><th>Book</th><th>Member</th><th>Issued</th><th>Due</th><th>Status</th></tr></thead><tbody>
    <?php if (!$recentLoans): ?><tr><td class="empty-state" colspan="5">No circulation activity yet.</td></tr><?php endif; ?>
    <?php foreach ($recentLoans as $loan): ?>
        <tr><td class="primary-cell"><?= e($loan['title']) ?></td><td><?= e($loan['full_name']) ?></td><td><?= e($loan['date_issued']) ?></td><td><?= e($loan['due_date']) ?></td><td><span class="status <?= $loan['date_returned'] === null ? 'status-open' : 'status-done' ?>"><?= $loan['date_returned'] === null ? 'On loan' : 'Returned' ?></span></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>