<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$authors = $pdo->query('SELECT author_id, author_name FROM author ORDER BY author_name')->fetchAll();
$categories = $pdo->query('SELECT category_id, cat_name FROM categories ORDER BY cat_name')->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$copies = filter_var($_POST['total_copies'] ?? null, FILTER_VALIDATE_INT);
	if (trim($_POST['title'] ?? '') === '' || trim($_POST['isbn'] ?? '') === '' || !$copies || $copies < 1) {
		$error = 'Enter a title, ISBN, and at least one copy.';
	} else {
		try {
			$statement = $pdo->prepare(
				'INSERT INTO books (isbn, title, author_id, category_id, publisher, year_published, total_copies, available_copies)
				 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
			);
			$statement->execute([
				trim($_POST['isbn']), trim($_POST['title']), (int) $_POST['author_id'],
				(int) $_POST['category_id'], trim($_POST['publisher'] ?? '') ?: null,
				trim($_POST['year_published'] ?? '') ?: null, $copies, $copies,
			]);
			set_flash('success', 'Book added to the catalog.');
			redirect('books/index.php');
		} catch (PDOException $exception) {
			$error = 'Could not save this book. Check that the ISBN is unique and the author and category exist.';
		}
	}
}

$book = $_POST;
$pageTitle = 'Add a book';
require __DIR__ . '/../includes/header.php';
?>
<section class="form-page"><p class="eyebrow">COLLECTION</p><h1>Add a book</h1><p class="muted">Add a title and its catalog details.</p><form class="form-panel" method="post">
<?php if ($error !== ''): ?><div class="notice notice-error" role="alert"><?= e($error) ?></div><?php endif; ?>
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-grid">
<label>Title<input name="title" required value="<?= e($book['title'] ?? '') ?>"></label><label>ISBN<input name="isbn" required value="<?= e($book['isbn'] ?? '') ?>"></label>
<label>Author<select name="author_id" required><?php foreach ($authors as $author): ?><option value="<?= (int) $author['author_id'] ?>" <?= (string) ($book['author_id'] ?? '') === (string) $author['author_id'] ? 'selected' : '' ?>><?= e($author['author_name']) ?></option><?php endforeach; ?></select></label>
<label>Category<select name="category_id" required><?php foreach ($categories as $category): ?><option value="<?= (int) $category['category_id'] ?>" <?= (string) ($book['category_id'] ?? '') === (string) $category['category_id'] ? 'selected' : '' ?>><?= e($category['cat_name']) ?></option><?php endforeach; ?></select></label>
<label>Publisher<input name="publisher" value="<?= e($book['publisher'] ?? '') ?>"></label><label>Year published<input type="number" name="year_published" min="1000" max="2155" value="<?= e($book['year_published'] ?? '') ?>"></label>
<label>Total copies<input type="number" name="total_copies" min="1" required value="<?= e($book['total_copies'] ?? '1') ?>"></label></div><div class="form-actions"><button class="button button-primary" type="submit">Save book</button><a class="button button-secondary" href="<?= e(url('books/index.php')) ?>">Cancel</a></div></form></section>
<?php require __DIR__ . '/../includes/footer.php'; ?><?php

$pageTitle = "Add New Book";
$currentPage = "books";

require_once "../includes/header.php";
require_once "../includes/navbar.php";

?>

<main class="container">

    <h2 class="page-title">Add New Book</h2>

    <div class="form-card">

        <form method="POST" action="">

            <div class="form-group">
                <label for="isbn">ISBN</label>
                <input type="text"
                       id="isbn"
                       name="isbn"
                       placeholder="Enter ISBN"
                       required>
            </div>

            <div class="form-group">
                <label for="title">Book Title</label>
                <input type="text"
                       id="title"
                       name="title"
                       placeholder="Enter book title"
                       required>
            </div>

            <div class="form-group">
                <label for="author_id">Author</label>
                <select id="author_id"
                        name="author_id"
                        required>
                    <option value="">Select Author</option>
                </select>
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id"
                        name="category_id"
                        required>
                    <option value="">Select Category</option>
                </select>
            </div>

            <div class="form-group">
                <label for="publisher">Publisher</label>
                <input type="text"
                       id="publisher"
                       name="publisher"
                       placeholder="Enter publisher">
            </div>

            <div class="form-group">
                <label for="year_published">
                    Year Published
                </label>
                <input type="number"
                       id="year_published"
                       name="year_published"
                       placeholder="Enter publication year">
            </div>

            <div class="form-group">
                <label for="total_copies">Total Copies</label>
                <input type="number"
                       id="total_copies"
                       name="total_copies"
                       min="1"
                       placeholder="Enter total copies"
                       required>
            </div>

            <div class="form-group">
                <label for="available_copies">
                    Available Copies
                </label>
                <input type="number"
                       id="available_copies"
                       name="available_copies"
                       min="0"
                       placeholder="Enter available copies"
                       required>
            </div>

            <button type="submit"
                    class="btn btn-success">
                Save Book
            </button>

            <a href="index.php"
               class="btn btn-danger">
                Cancel
            </a>

        </form>

    </div>

</main>

<?php require_once "../includes/footer.php"; ?>

