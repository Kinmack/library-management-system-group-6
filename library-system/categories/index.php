<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

$categories = $pdo->query('SELECT category_id, cat_name, description FROM categories ORDER BY cat_name')->fetchAll();
$pageTitle = 'Categories';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">COLLECTION</p><h1>Categories</h1><p class="muted">Browse and manage book categories.</p></div><a class="button button-primary" href="<?= e(url('categories/create.php')) ?>">Add a category</a></section>
<div class="table-wrap"><table><thead><tr><th>Category</th><th>Description</th></tr></thead><tbody>
<?php if (!$categories): ?><tr><td class="empty-state" colspan="2">No categories found. Add a category to begin.</td></tr><?php endif; ?>
<?php foreach ($categories as $category): ?>
<tr><td class="primary-cell"><?= e($category['cat_name']) ?></td><td><?= e($category['description'] ?? '') ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
