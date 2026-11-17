<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$search = trim($_GET['q'] ?? '');
$statement = $pdo->prepare(
	'SELECT b.book_id, b.isbn, b.title, b.publisher, b.year_published,
			b.total_copies, b.available_copies, a.author_name, c.cat_name
	 FROM books b JOIN author a ON a.author_id = b.author_id
	 JOIN categories c ON c.category_id = b.category_id
	 WHERE b.title LIKE :title_search OR b.isbn LIKE :isbn_search OR a.author_name LIKE :author_search
	 ORDER BY b.title'
);
$searchTerm = '%' . $search . '%';
$statement->execute([
	'title_search' => $searchTerm,
	'isbn_search' => $searchTerm,
	'author_search' => $searchTerm,
]);
$books = $statement->fetchAll();
$pageTitle = 'Books';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading">
	<div>
		<p class="eyebrow">COLLECTION</p>
		<h1>Books</h1>
		<p class="muted">Catalog titles and track available copies.</p>
	</div>
	<a class="button button-primary" href="<?= e(url('books/create.php')) ?>">Add a book</a>
</section>

<div class="toolbar">
	<span class="muted" aria-live="polite" data-result-count data-singular="title" data-plural="titles"><?= count($books) ?> title<?= count($books) === 1 ? '' : 's' ?></span>
	<div class="search-form" role="search">
		<label class="sr-only" for="book-search">Search title, ISBN, or author</label>
		<input id="book-search" type="search" data-table-search="#books-table" data-count-target="[data-result-count]" placeholder="Search title, ISBN, author, or category" value="<?= e($search) ?>">
	</div>
</div>

<div class="table-wrap">
	<table id="books-table">
		<thead>
			<tr><th>Title / ISBN</th><th>Author</th><th>Category</th><th>Year</th><th>Copies</th><th>Publisher</th><th>Actions</th></tr>
		</thead>
		<tbody>
			<?php foreach ($books as $book): ?>
				<tr data-search-row>
					<td><span class="primary-cell"><?= e($book['title']) ?></span><br><small><?= e($book['isbn']) ?></small></td>
					<td><?= e($book['author_name']) ?></td>
					<td><?= e($book['cat_name']) ?></td>
					<td><?= e($book['year_published'] ?? '') ?></td>
					<td><?= (int) $book['available_copies'] ?> / <?= (int) $book['total_copies'] ?></td>
					<td><?= e($book['publisher'] ?? '') ?></td>
					<td>
						<div class="row-actions">
							<a href="<?= e(url('books/edit.php?id=' . $book['book_id'])) ?>">Edit</a>
							<form class="inline-form" method="post" action="<?= e(url('books/delete.php')) ?>" data-confirm="Delete this book?">
								<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
								<input type="hidden" name="id" value="<?= (int) $book['book_id'] ?>">
								<button class="link-button link-danger" type="submit">Delete</button>
							</form>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr data-filter-empty hidden>
				<td class="empty-state" colspan="7"><?= $books ? 'No books match your search.' : 'No books found. Add a title to start your catalog.' ?></td>
			</tr>
		</tbody>
	</table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
