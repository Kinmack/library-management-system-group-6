<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$name = trim($_POST['author_name'] ?? '');
	$country = trim($_POST['country'] ?? '');

	if ($name === '') {
		$error = 'Enter an author name.';
	} else {
		try {
			$statement = $pdo->prepare('INSERT INTO author (author_name, country) VALUES (?, ?)');
			$statement->execute([$name, $country !== '' ? $country : 'Unknown']);
			set_flash('success', 'Author added successfully.');
			redirect('authors/index.php');
		} catch (PDOException $exception) {
			$error = 'Could not save this author. Please try again.';
		}
	}
}

$pageTitle = 'Add an author';
require __DIR__ . '/../includes/header.php';
?>
<section class="form-page"><p class="eyebrow">COLLECTION</p><h1>Add an author</h1><p class="muted">Enter the author's name and country.</p><form class="form-panel" method="post">
<?php if ($error !== ''): ?><div class="notice notice-error" role="alert"><?= e($error) ?></div><?php endif; ?>
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-grid">
<label>Name<input name="author_name" required value="<?= e($_POST['author_name'] ?? '') ?>"></label>
<label>Country<input name="country" value="<?= e($_POST['country'] ?? '') ?>" placeholder="Unknown"></label>
</div><div class="form-actions"><button class="button button-primary" type="submit">Save author</button><a class="button button-secondary" href="<?= e(url('authors/index.php')) ?>">Cancel</a></div></form></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
