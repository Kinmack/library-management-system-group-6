<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$search = trim($_GET['q'] ?? '');
$statement = $pdo->prepare(
	'SELECT member_id, member_number, full_name, email, phone, status, date_joined
	 FROM members WHERE full_name LIKE :search OR member_number LIKE :search OR email LIKE :search
	 ORDER BY full_name'
);
$statement->execute(['search' => '%' . $search . '%']);
$members = $statement->fetchAll();
$pageTitle = 'Members';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">PEOPLE</p><h1>Members</h1><p class="muted">Manage member details and account standing.</p></div><a class="button button-primary" href="<?= e(url('members/create.php')) ?>">Add a member</a></section>
<div class="toolbar"><span class="muted"><?= count($members) ?> member<?= count($members) === 1 ? '' : 's' ?></span><form class="search-form" method="get"><input type="search" name="q" placeholder="Search name, number, email" value="<?= e($search) ?>"><button class="button button-secondary" type="submit">Search</button></form></div>
<div class="table-wrap"><table><thead><tr><th>Member</th><th>Email</th><th>Phone</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php if (!$members): ?><tr><td class="empty-state" colspan="6">No members found. Add a member to begin.</td></tr><?php endif; ?>
<?php foreach ($members as $member): ?><tr><td><span class="primary-cell"><?= e($member['full_name']) ?></span><br><small><?= e($member['member_number']) ?></small></td><td><?= e($member['email']) ?></td><td><?= e($member['phone'] ?? '') ?></td><td><?= e($member['date_joined']) ?></td><td><span class="status <?= $member['status'] === 'active' ? 'status-done' : 'status-suspended' ?>"><?= e(ucfirst($member['status'])) ?></span></td><td><div class="row-actions"><a href="<?= e(url('members/edit.php?id=' . $member['member_id'])) ?>">Edit</a><form class="inline-form" method="post" action="<?= e(url('members/delete.php')) ?>" onsubmit="return confirm('Delete this member?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $member['member_id'] ?>"><button class="link-button" type="submit">Delete</button></form></div></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
