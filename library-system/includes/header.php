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
<?php
// Shared header for the Library Management System

if (!defined('BASE_URL')) {
    define('BASE_URL', '/library-system');
}

$pageTitle = $pageTitle ?? 'Library Management System';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($pageTitle) ?>
        | Library Management System
    </title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/style.css">
</head>

<body>

<header class="header">
    <div>
        <h1>Library Management System</h1>
        <p>Manage Books, Members and Loans</p>
    </div>

    <div>
        <p>Librarian Portal</p>
    </div>
</header>
