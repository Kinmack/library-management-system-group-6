<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$statement = $pdo->prepare('SELECT * FROM members WHERE member_id = ?');
$statement->execute([$id]);
$member = $statement->fetch();
if (!$member) {
	set_flash('error', 'Member not found.');
	redirect('members/index.php');
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$name = trim($_POST['full_name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = 'Enter a member name and a valid email address.';
		$member = array_merge($member, $_POST);
	} else {
		try {
			$update = $pdo->prepare('UPDATE members SET full_name = ?, email = ?, phone = ?, status = ? WHERE member_id = ?');
			$update->execute([$name, $email, trim($_POST['phone'] ?? '') ?: null, $_POST['status'] === 'suspended' ? 'suspended' : 'active', $id]);
			set_flash('success', 'Member details updated.');
			redirect('members/index.php');
		} catch (PDOException $exception) {
			$error = 'Could not update this member. The email address may already be registered.';
		}
	}
}

$pageTitle = 'Edit member';
require __DIR__ . '/../includes/header.php';
?>
<section class="form-page"><p class="eyebrow">PEOPLE / <?= e($member['member_number']) ?></p><h1>Edit member</h1><form class="form-panel" method="post">
<?php if ($error !== ''): ?><div class="notice notice-error" role="alert"><?= e($error) ?></div><?php endif; ?><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $id ?>"><div class="form-grid">
<label>Full name<input name="full_name" required value="<?= e($member['full_name']) ?>"></label><label>Email<input type="email" name="email" required value="<?= e($member['email']) ?>"></label><label>Phone<input name="phone" value="<?= e($member['phone'] ?? '') ?>"></label><label>Status<select name="status"><option value="active" <?= $member['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="suspended" <?= $member['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option></select></label>
</div><div class="form-actions"><button class="button button-primary" type="submit">Update member</button><a class="button button-secondary" href="<?= e(url('members/index.php')) ?>">Cancel</a></div></form></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
