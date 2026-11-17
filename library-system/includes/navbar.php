<?php if (isset($_SESSION['user_id'])): ?>
<header class="topbar">
	<a class="brand" href="<?= e(url('dashboard/index.php')) ?>">
		<span class="brand-mark" aria-hidden="true">L</span>
		<span>Library Desk</span>
	</a>
	<nav class="main-nav" aria-label="Main navigation">
		<a href="<?= e(url('dashboard/index.php')) ?>" <?= $currentSection === 'dashboard' ? 'aria-current="page"' : '' ?>>Overview</a>
		<a href="<?= e(url('books/index.php')) ?>" <?= $currentSection === 'books' ? 'aria-current="page"' : '' ?>>Books</a>
		<a href="<?= e(url('members/index.php')) ?>" <?= $currentSection === 'members' ? 'aria-current="page"' : '' ?>>Members</a>
		<a href="<?= e(url('loans/index.php')) ?>" <?= $currentSection === 'loans' ? 'aria-current="page"' : '' ?>>Loans</a>
		<a href="<?= e(url('authors/index.php')) ?>" <?= $currentSection === 'authors' ? 'aria-current="page"' : '' ?>>Authors</a>
		<a href="<?= e(url('categories/index.php')) ?>" <?= $currentSection === 'categories' ? 'aria-current="page"' : '' ?>>Categories</a>
	</nav>
	<div class="account">
		<span><?= e($_SESSION['full_name'] ?? $_SESSION['username']) ?></span>
		<a class="signout" href="<?= e(url('auth/logout.php')) ?>">Sign out</a>
	</div>
</header>
<?php endif; ?>
