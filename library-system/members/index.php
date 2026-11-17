<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$search = trim($_GET['q'] ?? '');
$statement = $pdo->prepare(
	'SELECT member_id, member_number, full_name, email, phone, status, date_joined
	 FROM members WHERE full_name LIKE :name_search OR member_number LIKE :number_search OR email LIKE :email_search
	 ORDER BY full_name'
);
$searchTerm = '%' . $search . '%';
$statement->execute([
	'name_search' => $searchTerm,
	'number_search' => $searchTerm,
	'email_search' => $searchTerm,
]);
$members = $statement->fetchAll();
$pageTitle = 'Members';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">PEOPLE</p><h1>Members</h1><p class="muted">Manage member details and account standing.</p></div><a class="button button-primary" href="<?= e(url('members/create.php')) ?>">Add a member</a></section>
<div class="toolbar">
	<span class="muted" aria-live="polite" data-result-count data-singular="member" data-plural="members"><?= count($members) ?> member<?= count($members) === 1 ? '' : 's' ?></span>
	<div class="search-form" role="search">
		<label class="sr-only" for="member-search">Search members by name, number, or email</label>
		<input id="member-search" type="search" data-table-search="#members-table" data-count-target="[data-result-count]" placeholder="Search name, number, or email" value="<?= e($search) ?>">
	</div>
</div>
<div class="table-wrap"><table id="members-table"><thead><tr><th>Member</th><th>Email</th><th>Phone</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($members as $member): ?>
<tr data-search-row><td><span class="primary-cell"><?= e($member['full_name']) ?></span><br><small><?= e($member['member_number']) ?></small></td><td><?= e($member['email']) ?></td><td><?= e($member['phone'] ?? '') ?></td><td><?= e($member['date_joined']) ?></td><td><span class="status <?= $member['status'] === 'active' ? 'status-done' : 'status-suspended' ?>"><?= e(ucfirst($member['status'])) ?></span></td><td><div class="row-actions"><a href="<?= e(url('members/edit.php?id=' . $member['member_id'])) ?>">Edit</a><form class="inline-form" method="post" action="<?= e(url('members/delete.php')) ?>" data-confirm="Delete this member?"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $member['member_id'] ?>"><button class="link-button link-danger" type="submit">Delete</button></form></div></td></tr>
<?php endforeach; ?>
<tr data-filter-empty hidden><td class="empty-state" colspan="6"><?= $members ? 'No members match your search.' : 'No members found. Add a member to begin.' ?></td></tr>
</tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
