<?php
require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['user_id'])) {
	redirect('index.php');
}

$userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$isSetup = $userCount === 0;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();

	if ($isSetup) {
		$fullName = trim($_POST['full_name'] ?? '');
		$username = trim($_POST['username'] ?? '');
		$password = $_POST['password'] ?? '';

		if ($fullName === '' || $username === '' || strlen($password) < 8) {
			$error = 'Enter your name, a username, and a password of at least 8 characters.';
		} else {
			$statement = $pdo->prepare('INSERT INTO users (username, password_hash, full_name, role) VALUES (?, ?, ?, ?)');
			$statement->execute([$username, password_hash($password, PASSWORD_DEFAULT), $fullName, 'admin']);
			session_regenerate_id(true);
			$_SESSION['user_id'] = (int) $pdo->lastInsertId();
			$_SESSION['username'] = $username;
			$_SESSION['full_name'] = $fullName;
			redirect('index.php');
		}
	} else {
		$username = trim($_POST['username'] ?? '');
		$statement = $pdo->prepare('SELECT user_id, username, full_name, password_hash FROM users WHERE username = ?');
		$statement->execute([$username]);
		$user = $statement->fetch();

		if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
			session_regenerate_id(true);
			$_SESSION['user_id'] = (int) $user['user_id'];
			$_SESSION['username'] = $user['username'];
			$_SESSION['full_name'] = $user['full_name'];
			redirect('index.php');
		}

		$error = 'The username or password was not recognized.';
	}
}

$pageTitle = $isSetup ? 'Set up your library' : 'Sign in';
require __DIR__ . '/../includes/header.php';
?>
<section class="auth-layout">
	<div class="auth-intro">
		<p class="eyebrow">COLLECTIONS, IN GOOD ORDER</p>
		<h1><?= $isSetup ? 'Your library starts here.' : 'Welcome back.' ?></h1>
		<p><?= $isSetup ? 'Create the first administrator account to open your library workspace.' : 'Sign in to manage books, members, and circulation.' ?></p>
	</div>
	<form class="form-panel auth-form" method="post">
		<h2><?= $isSetup ? 'Create administrator' : 'Sign in' ?></h2>
		<?php if ($error !== ''): ?><div class="notice notice-error" role="alert"><?= e($error) ?></div><?php endif; ?>
		<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
		<?php if ($isSetup): ?>
			<label>Full name<input name="full_name" autocomplete="name" required value="<?= e($_POST['full_name'] ?? '') ?>"></label>
		<?php endif; ?>
		<label>Username<input name="username" autocomplete="username" required autofocus value="<?= e($_POST['username'] ?? '') ?>"></label>
		<label>Password<input type="password" name="password" autocomplete="<?= $isSetup ? 'new-password' : 'current-password' ?>" required minlength="<?= $isSetup ? '8' : '1' ?>"></label>
		<button class="button button-primary" type="submit"><?= $isSetup ? 'Create workspace' : 'Sign in' ?></button>
	</form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php
$currentPage = $currentPage ?? '';
?>

<nav class="navbar">

    <a href="<?= BASE_URL ?>/dashboard/index.php"
       class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
        Dashboard
    </a>

    <a href="<?= BASE_URL ?>/books/index.php"
       class="<?= $currentPage === 'books' ? 'active' : '' ?>">
        Books
    </a>

    <a href="<?= BASE_URL ?>/members/index.php"
       class="<?= $currentPage === 'members' ? 'active' : '' ?>">
        Members
    </a>

    <a href="<?= BASE_URL ?>/loans/index.php"
       class="<?= $currentPage === 'loans' ? 'active' : '' ?>">
        Loans
    </a>

    <a href="<?= BASE_URL ?>/authors/index.php">
        Authors
    </a>

    <a href="<?= BASE_URL ?>/categories/index.php">
        Categories
    </a>

    <a href="<?= BASE_URL ?>/auth/logout.php">
        Logout
    </a>

</nav>
