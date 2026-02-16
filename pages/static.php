<?php
require_once __DIR__ . '/../config.php';
$slug = slugify($_GET['slug'] ?? 'about');
$page = static_page($slug);
?><!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?php echo e($page['title'] ?? 'Sayfa'); ?></title><link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>"></head><body>
<section class="container"><div class="card">
<h1><?php echo e($page['title'] ?? 'Sayfa bulunamadı'); ?></h1>
<?php if ($page): ?>
<div><?php echo $page['body_html']; ?></div>
<p class="muted">Son güncelleme: <?php echo e((string)$page['updated_at']); ?></p>
<?php else: ?><p>İçerik bulunamadı.</p><?php endif; ?>
</div></section></body></html>
