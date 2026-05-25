<?php
$errorConfig = [
    404 => [
        'title' => 'Страница не найдена',
        'message' => 'Запрашиваемая страница не существует или была перемещена.',
        'suggestions' => [
            'Проверьте правильность URL адреса',
            'Вернитесь на главную страницу',
            'Воспользуйтесь поиском по сайту'
        ]
    ],
    500 => [
        'title' => 'Внутренняя ошибка сервера',
        'message' => 'На сервере произошла техническая ошибка.',
        'suggestions' => [
            'Попробуйте обновить страницу через несколько минут',
            'Очистите кэш браузера',
            'Сообщите об ошибке администратору',
            'Попробуйте зайти позже'
        ]
    ]
];

$errorCode = isset($_GET['code']) ? (int)$_GET['code'] : 404;
$errorMessage = isset($_GET['message']) ? urldecode($_GET['message']) : null;
$errorId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;

if (!array_key_exists($errorCode, $errorConfig)) {
    $errorCode = 404;
}

$config = $errorConfig[$errorCode] ?? $errorConfig[404];
if ($errorMessage) {
    $config['message'] = htmlspecialchars($errorMessage);
}

http_response_code($errorCode);

header('X-Robots-Tag: noindex, nofollow');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ошибка <?= $errorCode ?> - <?= htmlspecialchars($config['title']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: silver;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-code {
            font-size: 120px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            line-height: 1;
        }

        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
        }

        .error-message {
            color: #666;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .suggestions {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: left;
            margin-bottom: 30px;
        }

        .suggestions h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #333;
        }

        .suggestions ul {
            margin-left: 20px;
        }

        .suggestions li {
            margin: 10px 0;
            color: #555;
        }

        .error-id {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #856404;
            word-break: break-all;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .btn-secondary {
            background: #48bb78;
        }

        .btn-secondary:hover {
            background: #38a169;
        }

        @media (max-width: 600px) {
            .error-container {
                padding: 30px;
            }

            .error-code {
                font-size: 80px;
            }

            h1 {
                font-size: 24px;
            }

            .buttons {
                flex-direction: column;
            }
        }
    </style>

</head>
<body>

<div class="error-container">
    <div class="error-code"><?= $errorCode ?></div>
    <h1><?= htmlspecialchars($config['title']) ?></h1>
    <p class="error-message"><?= nl2br(htmlspecialchars($config['message'])) ?></p>

    <?php if (isset($errorId)): ?>
        <div class="error-id">
            <strong>Код ошибки:</strong> <?= htmlspecialchars($errorId) ?>
            <br>
            <small>Пожалуйста, сообщите этот код в службу поддержки</small>
        </div>
    <?php endif; ?>

    <div class="suggestions">
        <h3>Что можно сделать:</h3>
        <ul>
            <?php foreach ($config['suggestions'] as $suggestion): ?>
                <li><?= $suggestion ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="buttons">
        <a href="/" class="btn">На главную</a>
        <button onclick="history.back()" class="btn btn-secondary">Назад</button>
    </div>
</body>
</html>
