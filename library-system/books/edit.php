
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$statement = $pdo->prepare('SELECT * FROM books WHERE book_id = ?');
$statement->execute([$id]);
$book = $statement->fetch();
if (!$book) {
	set_flash('error', 'Book not found.');
	redirect('books/index.php');
}
$authors = $pdo->query('SELECT author_id, author_name FROM author ORDER BY author_name')->fetchAll();
$categories = $pdo->query('SELECT category_id, cat_name FROM categories ORDER BY cat_name')->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$copies = filter_var($_POST['total_copies'] ?? null, FILTER_VALIDATE_INT);
	$activeLoans = $pdo->prepare('SELECT COUNT(*) FROM loans WHERE book_id = ? AND date_returned IS NULL');
	$activeLoans->execute([$id]);
	$borrowed = (int) $activeLoans->fetchColumn();

	if (trim($_POST['title'] ?? '') === '' || trim($_POST['isbn'] ?? '') === '' || !$copies || $copies < $borrowed) {
		$error = 'Enter a title and ISBN, and ensure total copies are not fewer than copies currently on loan.';
		$book = array_merge($book, $_POST);
	} else {
		try {
			$update = $pdo->prepare(
				'UPDATE books SET isbn = ?, title = ?, author_id = ?, category_id = ?, publisher = ?, year_published = ?, total_copies = ?, available_copies = ? WHERE book_id = ?'
			);
			$update->execute([
				trim($_POST['isbn']), trim($_POST['title']), (int) $_POST['author_id'],
				(int) $_POST['category_id'], trim($_POST['publisher'] ?? '') ?: null,
				trim($_POST['year_published'] ?? '') ?: null, $copies, $copies - $borrowed, $id,
			]);
			set_flash('success', 'Book details updated.');
			redirect('books/index.php');
		} catch (PDOException $exception) {
			$error = 'Could not update this book. Check that the ISBN is unique and selected records exist.';
		}
	}
}

$pageTitle = 'Edit book';
require __DIR__ . '/../includes/header.php';
?>
<section class="form-page"><p class="eyebrow">COLLECTION</p><h1>Edit book</h1><form class="form-panel" method="post">
<?php if ($error !== ''): ?><div class="notice notice-error" role="alert"><?= e($error) ?></div><?php endif; ?>
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $id ?>"><div class="form-grid">
<label>Title<input name="title" required value="<?= e($book['title']) ?>"></label><label>ISBN<input name="isbn" required value="<?= e($book['isbn']) ?>"></label>
<label>Author<select name="author_id" required><?php foreach ($authors as $author): ?><option value="<?= (int) $author['author_id'] ?>" <?= (string) $book['author_id'] === (string) $author['author_id'] ? 'selected' : '' ?>><?= e($author['author_name']) ?></option><?php endforeach; ?></select></label>
<label>Category<select name="category_id" required><?php foreach ($categories as $category): ?><option value="<?= (int) $category['category_id'] ?>" <?= (string) $book['category_id'] === (string) $category['category_id'] ? 'selected' : '' ?>><?= e($category['cat_name']) ?></option><?php endforeach; ?></select></label>
<label>Publisher<input name="publisher" value="<?= e($book['publisher'] ?? '') ?>"></label><label>Year published<input type="number" name="year_published" min="1000" max="2155" value="<?= e($book['year_published'] ?? '') ?>"></label>
<label>Total copies<input type="number" name="total_copies" min="1" required value="<?= e($book['total_copies']) ?>"></label></div><div class="form-actions"><button class="button button-primary" type="submit">Update book</button><a class="button button-secondary" href="<?= e(url('books/index.php')) ?>">Cancel</a></div></form></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php

$pageTitle = "Edit Book";
$currentPage = "books";

require_once "../includes/header.php";
require_once "../includes/navbar.php";

?>

<main class="container">

    <h2 class="page-title">Edit Book Details</h2>

    <div class="form-card">

        <form method="POST" action="">

            <div class="form-group">
                <label>ISBN</label>
                <input type="text"
                       name="isbn"
                       placeholder="Book ISBN"
                       required>
            </div>

            <div class="form-group">
                <label>Book Title</label>
                <input type="text"
                       name="title"
                       placeholder="Book title"
                       required>
            </div>

            <div class="form-group">
                <label>Author</label>
                <select name="author_id" required>
                    <option value="">Select Author</option>
                </select>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id" required>
                    <option value="">Select Category</option>
                </select>
            </div>

            <div class="form-group">
                <label>Publisher</label>
                <input type="text"
                       name="publisher"
                       placeholder="Publisher">
            </div>

            <div class="form-group">
                <label>Year Published</label>
                <input type="number"
                       name="year_published">
            </div>

            <div class="form-group">
                <label>Total Copies</label>
                <input type="number"
                       name="total_copies"
                       min="1"
                       required>
            </div>

            <div class="form-group">
                <label>Available Copies</label>
                <input type="number"
                       name="available_copies"
                       min="0"
                       required>
            </div>

            <button type="submit"
                    class="btn btn-success">
                Update Book
            </button>

            <a href="index.php"
               class="btn btn-danger">
                Cancel
            </a>

        </form>

    </div>

</main>

<?php require_once "../includes/footer.php"; ?>
