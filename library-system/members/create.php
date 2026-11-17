<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$name = trim($_POST['full_name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = 'Enter a member name and a valid email address.';
	} else {
		try {
			$nextId = (int) $pdo->query('SELECT COALESCE(MAX(member_id), 0) + 1 FROM members')->fetchColumn();
			$number = 'MEM' . str_pad((string) $nextId, 3, '0', STR_PAD_LEFT);
			$statement = $pdo->prepare('INSERT INTO members (member_number, full_name, email, phone, status) VALUES (?, ?, ?, ?, ?)');
			$statement->execute([$number, $name, $email, trim($_POST['phone'] ?? '') ?: null, $_POST['status'] === 'suspended' ? 'suspended' : 'active']);
			set_flash('success', 'Member added with number ' . $number . '.');
			redirect('members/index.php');
		} catch (PDOException $exception) {
			$error = 'Could not save this member. The email address may already be registered.';
		}
	}
}

$member = $_POST;
$pageTitle = 'Add a member';
require __DIR__ . '/../includes/header.php';
?>
<section class="form-page"><p class="eyebrow">PEOPLE</p><h1>Add a member</h1><p class="muted">New member numbers are assigned automatically.</p><form class="form-panel" method="post">
<?php if ($error !== ''): ?><div class="notice notice-error" role="alert"><?= e($error) ?></div><?php endif; ?><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-grid">
<label>Full name<input name="full_name" required value="<?= e($member['full_name'] ?? '') ?>"></label><label>Email<input type="email" name="email" required value="<?= e($member['email'] ?? '') ?>"></label><label>Phone<input name="phone" value="<?= e($member['phone'] ?? '') ?>"></label><label>Status<select name="status"><option value="active">Active</option><option value="suspended" <?= ($member['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option></select></label>
</div><div class="form-actions"><button class="button button-primary" type="submit">Save member</button><a class="button button-secondary" href="<?= e(url('members/index.php')) ?>">Cancel</a></div></form></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
