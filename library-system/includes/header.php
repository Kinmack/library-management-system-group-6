<?php
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Library Desk';
$currentSection = basename(dirname($_SERVER['SCRIPT_NAME'] ?? ''));
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= e($pageTitle) ?> | Library Desk</title>
	<link rel="stylesheet" href="<?= e(url('assets/app.css')) ?>">
	<script src="<?= e(url('assets/library.js')) ?>" defer></script>
</head>
<body>
<?php require __DIR__ . '/navbar.php'; ?>
<main class="page-shell">
	<?php if ($flash = get_flash()): ?>
		<div class="notice notice-<?= e($flash['type']) ?>" role="<?= $flash['type'] === 'error' ? 'alert' : 'status' ?>">
			<?= e($flash['message']) ?>
		</div>
	<?php endif; ?>
