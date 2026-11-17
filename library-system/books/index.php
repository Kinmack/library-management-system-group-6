<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$search = trim($_GET['q'] ?? '');
$statement = $pdo->prepare(
	'SELECT b.book_id, b.isbn, b.title, b.publisher, b.year_published,
			b.total_copies, b.available_copies, a.author_name, c.cat_name
	 FROM books b JOIN author a ON a.author_id = b.author_id
	 JOIN categories c ON c.category_id = b.category_id
	 WHERE b.title LIKE :search OR b.isbn LIKE :search OR a.author_name LIKE :search
	 ORDER BY b.title'
);
$statement->execute(['search' => '%' . $search . '%']);
$books = $statement->fetchAll();
$pageTitle = 'Books';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">COLLECTION</p><h1>Books</h1><p class="muted">Catalog titles and track available copies.</p></div><a class="button button-primary" href="<?= e(url('books/create.php')) ?>">Add a book</a></section>
<div class="toolbar"><span class="muted"><?= count($books) ?> title<?= count($books) === 1 ? '' : 's' ?></span><form class="search-form" method="get"><input type="search" name="q" placeholder="Search title, ISBN, author" value="<?= e($search) ?>"><button class="button button-secondary" type="submit">Search</button></form></div>
<div class="table-wrap"><table><thead><tr><th>Title / ISBN</th><th>Author</th><th>Category</th><th>Copies</th><th>Publisher</th><th>Actions</th></tr></thead><tbody>
<?php if (!$books): ?><tr><td class="empty-state" colspan="6">No books found. Add a title to start your catalog.</td></tr><?php endif; ?>
<?php foreach ($books as $book): ?>
<tr><td><span class="primary-cell"><?= e($book['title']) ?></span><br><small><?= e($book['isbn']) ?></small></td><td><?= e($book['author_name']) ?></td><td><?= e($book['cat_name']) ?></td><td><?= (int) $book['available_copies'] ?> / <?= (int) $book['total_copies'] ?></td><td><?= e($book['publisher'] ?? '') ?></td><td><div class="row-actions"><a href="<?= e(url('books/edit.php?id=' . $book['book_id'])) ?>">Edit</a><form class="inline-form" method="post" action="<?= e(url('books/delete.php')) ?>" onsubmit="return confirm('Delete this book?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $book['book_id'] ?>"><button class="link-button" type="submit">Delete</button></form></div></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
