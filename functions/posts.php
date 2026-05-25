<?php

namespace CompanyName\Blog;


// =функция удаления===============================
function deletePost(int $id): array
{



}




function updatePost(array $post):void
{
    $id = $post['id'];
    $posts = getPosts();


    $posts[$id] = [...$post, ...[
        'date' => $posts[$id]['date'],
        'author' => $posts[$id]['author']
    ]];

    if (!file_put_contents(dirname(__DIR__) . '/data/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
        throw new \Exception("Не удалось сохранить данные");
    }
}

function getPost(int $id): array
{
    $posts = getPosts();

    if (!isset($posts[$id])) {
        throw new \OutOfBoundsException("Пост не найден");
    }

    return $posts[$id];
}

function getPosts(): array
{
    $postsData = readFileData('posts.json');
    return decodeData($postsData);
}


function getPostsCategoriesBySlug(string $slug): array
{
    $category = getCategoryBySlug($slug);

    return getPostsCategoriesById($category['id']);
}


function getPostsCategoriesById(int $id): array
{
    $posts = getPosts();

    $filteredPosts = array_filter($posts, function ($post) use ($id) {
        return isset($post['category_id']) && $post['category_id'] === $id;
    });

    return array_values($filteredPosts);
}

