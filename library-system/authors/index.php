<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$authors = $pdo->query('SELECT author_id, author_name, country FROM author ORDER BY author_name')->fetchAll();
$pageTitle = 'Authors';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">COLLECTION</p><h1>Authors</h1><p class="muted">Browse and manage book authors.</p></div><a class="button button-primary" href="<?= e(url('authors/create.php')) ?>">Add an author</a></section>
<div class="table-wrap"><table><thead><tr><th>Author</th><th>Country</th></tr></thead><tbody>
<?php if (!$authors): ?><tr><td class="empty-state" colspan="2">No authors found. Add an author to begin.</td></tr><?php endif; ?>
<?php foreach ($authors as $author): ?>
<tr><td class="primary-cell"><?= e($author['author_name']) ?></td><td><?= e($author['country']) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
