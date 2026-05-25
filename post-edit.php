<?php
require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\getCategories;
use function CompanyName\Blog\getPost;
use function CompanyName\Blog\redirectToError;
use function CompanyName\Blog\updatePost;

try {


    $category_id = null;
    $post = [];

    /*
    C - Edit
    Процедура:
    1. Нажимаем "edit", передаем id-поста, который хотим изменить
    2. По этому id получаем даныые поста и выводим их в форму для правки и передаем этот id тоже, данные в $posts
    3. Принимаем измененные данные поста вместе с его id
        а) нет ошибок валидации, сохраняем изменения и делаем редирект на это пост по id
        б) ошибки есть, выводим их в эту же форму, данные формы в переменных $title и  т.п.

    */
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'edit':
            //показать форму и вывести на ней данные поста для правки
            $id = (int)$_GET['id'];
            $post = getPost($id);
            break;

        case 'save':
            //принять новые исправленные данные поста и сохранить его
            $id = (int)($_POST['id'] ?? null);
            $title = htmlspecialchars($_POST['title'] ?? '');
            $content = htmlspecialchars($_POST['content'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? null);

            //Валидация
            if (empty($title)) {
                $errors['title'] = 'Заполните поле title';
            }

            if (empty($content)) {
                $errors['content'] = 'Заполните поле text';
            }

            if (empty($errors)) {

                updatePost([
                    'id' => $id,
                    'category_id' => $category_id,
                    'title' => $title,
                    'content' => $content
                ]);

                header("Location: /post.php?id=$id&success=edit");
                die();
            }
            break;
        default:
            throw new OutOfBoundsException("Не верный action");

    }
    $categories = getCategories();
} catch (OutOfBoundsException $e) {

    // http_response_code(404);
    // $error = "404 пост не найден. " . $e->getMessage();

    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();

    $errorDetails = [
        'message' => $e->getMessage(),
        'errorId' => $errorId,
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    //Редирект
    redirectToError(404, $e->getMessage(), $errorId);

} catch (Exception $e) {
    $errorDetails = [
        'message' => $e->getMessage(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    redirectToError(500);
}


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/components/menu.php' ?>
<h2>Править пост пост</h2>
<form action="/post-edit.php?action=save" method="post" enctype="application/x-www-form-urlencoded">
    <input type="text" name="id" readonly hidden value="<?= $post['id'] ?? $id ?? '' ?>">
    Категория:<br>
    <select name="category_id">
        <?php foreach ($categories as $category): ?>
            <option <?= ($category['id'] == ($post['category_id'] ?? $category_id)) ? 'selected' : '' ?>
                    value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
        <?php endforeach; ?>
    </select><br>

    Заголовок поста:<br>
    <input type="text" name="title" value="<?= $post['title'] ?? $title ?? '' ?>">
    <?php if (!empty($errors['title'])): ?>
        <p style="color:red"><?= $errors['title'] ?></p>
    <?php endif; ?>
    <br>
    Текст поста:<br>
    <textarea name="content"><?= $post['content'] ?? $content ?? '' ?></textarea>
    <?php if (!empty($errors['content'])): ?>
        <p style="color:red"><?= $errors['content'] ?></p>
    <?php endif; ?>
    <br><br>
    <input type="submit" value="Изменить">


</form>
</body>
</html>
