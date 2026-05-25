<?php
require __DIR__.'/vendor/autoload.php';
/*require __DIR__ . '/functions/config.php';
require __DIR__ . '/functions/categories.php';
require __DIR__ . '/functions/posts.php';*/

use function CompanyName\Blog\getPosts;
use function CompanyName\Blog\redirectToError;

const STATUSES = [
    'ok' => 'Пост успешно удален',
];
$success = isset($_GET['success']) ? (STATUSES[$_GET['success']] ?? null) : null;

try {

    $posts = getPosts();

    //D - Delete
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $id = $_GET['id'] ?? null;
        if ($id === null || !isset($posts[$id])) {
            throw new Exception("Пост с ID {$id} не найден.", 404);
        }

        //deletePost($id)
        unset($posts[$id]);
        file_put_contents(__DIR__ . '/data/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        if (isset($_GET['ajax'])) {
            if (empty($errors)) {
                $result = [
                    'status' => 'success'
                ];
            } else {
                $result = [
                    'status' => 'error'
                ];
            }

            header('Content-Type: application/json');
            echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        header("Location: /posts.php?success=ok");
        die();
    }

} catch (Exception $e) {
    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();

    $errorDetails = [
        'message' => $e->getMessage(),
        'errorId' => $errorId,
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    //Редирект
    redirectToError(500, $e->getMessage(), $errorId);

}
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
<script>
    // window.onload = function () {
    //     document.querySelectorAll('.deleteBtn').forEach(button => {
    //         button.onclick = function () {
    //             const id = this.getAttribute('data-id');
    //             (
    //                 async () => {
    //                     try {
    //                         const response = await fetch(`?action=delete&id=${id}&ajax`);
    //                         const result = await response.json();
    //                         switch (result.status) {
    //                             case 'success':
    //                                 document.getElementById(id).remove();
    //                                 break;
    //                             case 'error':
    //                                 console.error('Ошибка: не могу удалить');
    //                                 break;
    //                             default:
    //                                 console.error('Ошибка: не верный формат ответа');
    //                         }

    //                     } catch (error) {
    //                         console.error('Ошибка:', error);
    //                     }
    //                 }
    //             )();
    //         }
    //     });

    // }
// по модальному окну
 

    //     function deleteItem() {
    //          Swal.fire({
    //     title: 'Удалить пост?',
    //     showCancelButton: true,
    //     confirmButtonText: 'Удалить',
    // }).then((result) => {
    //     if (result.isConfirmed) {
    //         fetch(`/posts.php?action=delete&ajax=1&id=${postId}`)
    //             .then(response => response.json())
    //             .then(data => {
    //                 if (data.status === 'success') {
    //                     Swal.fire('Успешно!', 'Пост удален', 'success');
    //                     // Здесь можно удалить элемент из DOM, например: document.getElementById('post-' + postId).remove();
    //                 } else {
    //                     Swal.fire('Ошибка!', 'Не удалось удалить пост', 'error');
    //                 }
    //             });
    //     }
    // });
    //         closeModal();
    //     }

</script>
<script src="./script.js"></script>    
</body>
</html>