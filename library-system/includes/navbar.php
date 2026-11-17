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
