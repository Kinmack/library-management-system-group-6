<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$name = trim($_POST['cat_name'] ?? '');
	$description = trim($_POST['description'] ?? '');

	if ($name === '') {
		$error = 'Enter a category name.';
	} else {
		try {
			$statement = $pdo->prepare('INSERT INTO categories (cat_name, description) VALUES (?, ?)');
			$statement->execute([$name, $description !== '' ? $description : null]);
			set_flash('success', 'Category added successfully.');
			redirect('categories/index.php');
		} catch (PDOException $exception) {
			$error = 'Could not save this category. The name may already be in use.';
		}
	}
}

$pageTitle = 'Add a category';
require __DIR__ . '/../includes/header.php';
?>
<section class="form-page"><p class="eyebrow">COLLECTION</p><h1>Add a category</h1><p class="muted">Enter a category name and optional description.</p><form class="form-panel" method="post">
<?php if ($error !== ''): ?><div class="notice notice-error" role="alert"><?= e($error) ?></div><?php endif; ?>
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-grid">
<label>Name<input name="cat_name" required value="<?= e($_POST['cat_name'] ?? '') ?>"></label>
<label>Description<input name="description" value="<?= e($_POST['description'] ?? '') ?>"></label>
</div><div class="form-actions"><button class="button button-primary" type="submit">Save category</button><a class="button button-secondary" href="<?= e(url('categories/index.php')) ?>">Cancel</a></div></form></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
