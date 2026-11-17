<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Library Desk';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= e($pageTitle) ?> | Library Desk</title>
	<link rel="stylesheet" href="<?= e(url('assets/app.css')) ?>">
</head>
<body>
<?php require __DIR__ . '/navbar.php'; ?>
<main class="page-shell">
	<?php if ($flash = get_flash()): ?>
		<div class="notice notice-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
	<?php endif; ?>
