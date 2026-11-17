<?php if (isset($_SESSION['user_id'])): ?>
<header class="topbar">
	<a class="brand" href="<?= e(url('index.php')) ?>"><span class="brand-mark">L</span><span>Library Desk</span></a>
	<nav class="main-nav" aria-label="Main navigation">
		<a href="<?= e(url('dashboard/index.php')) ?>">Overview</a>
		<a href="<?= e(url('books/index.php')) ?>">Books</a>
		<a href="<?= e(url('members/index.php')) ?>">Members</a>
		<a href="<?= e(url('loans/index.php')) ?>">Loans</a>
	</nav>
	<div class="account"><span><?= e($_SESSION['full_name'] ?? $_SESSION['username']) ?></span><a class="signout" href="<?= e(url('auth/logout.php')) ?>">Sign out</a></div>
</header>
<?php endif; ?>
