<?php

require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\getCategoryBySlug;
use function CompanyName\Blog\getPostsCategoriesBySlug;
use function CompanyName\Blog\redirectToError;
use function CompanyName\Blog\savePost;

try {
   
$category = getCategoryBySlug($slug);
$posts = getPostsCategoriesBySlug($slug);

//C - Create
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = htmlspecialchars($_POST['name'] ?? '');
    $slug = htmlspecialchars($_POST['slug'] ?? '');
    $description = htmlspecialchars($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? null);

    $errors = [];

    //Валидация
    if (empty($name)) {
        $errors['name'] = 'Заполните поле name';
    }

    if (empty($slug)) {
        $errors['slug'] = 'Заполните поле slug';
    }

    if (empty($description)) {
        $errors['description'] = 'Заполните поле description';
    }
    
    if (empty($errors)) {
        $category = getCategoryBySlug($slug);
        $posts = getPostsCategoriesBySlug($slug);
        
        $posts[] = [
            'category_id' => $category_id,
            'name' => $name,
            'slug' => $slug,
            'description' => $description

        ];

        $lastKey = array_key_last($posts);
        $posts[$lastKey]['id'] = $lastKey;

        $posts[$lastKey] = array_merge(['id' => $lastKey], $posts[$lastKey]);

       
        // savePost($post);
        savePost();
        die();
    }

}
} catch (Throwable $th) {
    //throw $th;
    error_log($th->getMessage());
    http_response_code(500);
    throw $th;
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
<h2>Cоздать пост</h2>
<form action="" method="post" enctype="application/x-www-form-urlencoded">
    Категория:<br>
    <select name="category_id">
        <?php foreach ($categories as $category): ?>
            <option <?= ($category['id'] === $category_id) ? 'selected' : '' ?>
                    value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
        <?php endforeach; ?>
    </select><br>

    Категория поста:<br>
    <input type="text" name="name" value="<?= $name ?? '' ?>">
    <?php if (!empty($errors['name'])): ?>
        <p style="color:red"><?= $errors['name'] ?></p>
    <?php endif; ?>
    <br>
    Слаг поста:<br>
    <textarea name="slug"><?= $slug ?? '' ?></textarea>
    <?php if (!empty($errors['slug'])): ?>
        <p style="color:red"><?= $errors['slug'] ?></p>
    <?php endif; ?>
    <br>
    Описание поста:<br>
    <textarea name="description"><?= $description ?? '' ?></textarea>
    <?php if (!empty($errors['description'])): ?>
        <p style="color:red"><?= $errors['description'] ?></p>
    <?php endif; ?>
    <br><br>
    <input type="submit" value="Создать">

</form>
</body>
</html>
