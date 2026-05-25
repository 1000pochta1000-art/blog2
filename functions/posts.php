<?php

namespace CompanyName\Blog;

function deletePost(array &$posts, array $getParams): void 
{
    if (!isset($getParams['action']) || $getParams['action'] !== 'delete') {
        return;
    }

    $id = $getParams['id'] ?? null;

    try {
        
        if ($id === null || !isset($posts[$id])) {
            throw new Exception("Пост с ID {$id} не найден.", 404);
        }

        unset($posts[$id]);
       
        file_put_contents(__DIR__ . '/data/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        if (isset($getParams['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        header("Location: /posts.php?success=ok");
        die();

    } catch (Exception $e) {
        $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();
        $errorDetails = [
            'message' => $e->getMessage(),
            'errorId' => $errorId,
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        
        error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        redirectToError(500, $e->getMessage(), $errorId);
    }
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

