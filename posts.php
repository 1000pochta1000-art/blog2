<?php
require __DIR__.'/vendor/autoload.php';
/*require __DIR__ . '/functions/config.php';
require __DIR__ . '/functions/categories.php';
require __DIR__ . '/functions/posts.php';*/

use function CompanyName\Blog\getPosts;
use function CompanyName\Blog\redirectToError;
use function CompanyName\Blog\deletePost;

$posts = getPosts();
$statusMessage = '';

deletePost($posts, $_GET);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/components/menu.php' ?>
<a href="/post-create.php"> <button>Создать пост</button></a>
<h2>Посты</h2>
<?php if (!empty($success)): ?>
    <p style="color:green"><?=$success?></p>
<?php endif; ?>

<?php if (!isset($error)): ?>
    <?php foreach ($posts as $post): ?>
        <div id="<?=$post['id']?>">
            <h3>
                <a href="/post.php?id=<?= $post['id'] ?>">
                    <?= htmlspecialchars($post['title']) ?>
                </a>
                &nbsp;&nbsp;&nbsp;
                <a href="/post-edit.php?action=edit&id=<?=$post['id']?>">[edit]</a>
                &nbsp;&nbsp;&nbsp;
                <button data-id="<?=$post['id']?>" type="button" class="deleteBtn">Удалить</button>
            </h3>
            <p><?= htmlspecialchars($post['date']) ?></p>
            <p><?= htmlspecialchars($post['author']) ?></p>
        </div>
        
    <?php endforeach; ?>
<?php else: ?>
    <?= htmlspecialchars($error) ?>
<?php endif; ?>
<!-- Модальное окно -->
        <div id="deleteModal" class="modal-overlay">
        <div class="modal-box">
            <h2>Вы уверены?</h2>
            <p>Вы действительно хотите удалить этот элемент? Это действие нельзя будет отменить.</p>
            <div class="modal-buttons">
                <button class="btnm btn-cancel">Отмена</button>
                <button class="btnm btn-danger" data-id="<?=$post['id']?>">Удалить</button>
            </div>
        </div>
    </div>
<script src="./script.js"></script>    
</body>
</html>
