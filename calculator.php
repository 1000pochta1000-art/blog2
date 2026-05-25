<?php
require __DIR__ . '/vendor/autoload.php';

$errors = [];

$arg1 = 0;
$arg2 = 0;
$result = 0;
$operation = '';
$textResult = '';

if (!empty($_GET)) {
    $arg1 = $_GET["arg1"] ?? '';
    $arg2 = $_GET["arg2"] ?? '';
    $operation = $_GET["operation"] ?? '';

    //валидация ввод
    if ($arg1 === '' || $arg2 === '') {
        $errors[] = "Поле не должно быть пустым";
    }

    if (!(is_numeric($arg1) && is_numeric($arg2))) {
        $errors[] = "Введите число, а не строку";
    }

    if (!in_array($operation, ["+", "-", "*", "/"])) {
        $errors[] = "Не верная операция";
    }

    /*    if (!$errors) {
            header('Location: calculator.php?operation=' . $operation . '&arg1=' . $arg1 . '&arg2=' . $arg2&err);
            die();
        }*/

    if (empty($errors)) {
        $arg1 = (float)$arg1;
        $arg2 = (float)$arg2;

        $result = match ($operation) {
            '+' => $arg1 + $arg2,
            '-' => $arg1 - $arg2,
            '*' => $arg1 * $arg2,
            '/' => ($arg2 !== 0.0) ? $arg1 / $arg2 : "Деление на 0",
            default => throw new Exception("Ошибка"),
        };

        if (is_numeric($result)) {
            $result = round($result, 2);
        }

        $textResult = "$arg1 $operation $arg2 = $result";
    }
}

if (isset($_GET['ajax'])) {
    if (empty($errors)) {
        $result = [
            'status' => 'success',
            'data' => [
                'result' => $result,
                'textResult' => $textResult,
            ]
        ];
    } else {
        $result = [
            'status' => 'error',
            'errors' => $errors,
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Калькулятор</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/components/menu.php' ?>
<h2>Калькулятор</h2>
<form action="" method="get">
    <input id="arg1" type="text" name="arg1" value="<?= htmlspecialchars($arg1) ?>">
    <button data-operation="+" class="calcButton" type="button" style="width: 50px;height: 30px; cursor:pointer">+
    </button>
    <button data-operation="-" class="calcButton" type="button" style="width: 50px;height: 30px; cursor:pointer">-
    </button>
    <button data-operation="*" class="calcButton" type="button" style="width: 50px;height: 30px; cursor:pointer">*
    </button>
    <button data-operation="/" class="calcButton" type="button" style="width: 50px;height: 30px; cursor:pointer">/
    </button>
    <br>

    <input style="width: 50px;height: 30px; cursor:pointer;border: 2px solid <?= $operation === '+' ? 'green' : 'white' ?>"
           type="submit" name="operation"
           value="+">
    <input style="width: 50px;height: 30px; cursor:pointer;border: 2px solid <?= $operation === '-' ? 'green' : 'white' ?>"
           type="submit" name="operation"
           value="-">
    <input style="width: 50px;height: 30px; cursor:pointer;border: 2px solid <?= $operation === '*' ? 'green' : 'white' ?>"
           type="submit" name="operation"
           value="*">
    <input style="width: 50px;height: 30px; cursor:pointer;border: 2px solid <?= $operation === '/' ? 'green' : 'white' ?>"
           type="submit" name="operation"
           value="/">
    <input id="arg2" type="text" name="arg2" value="<?= htmlspecialchars($arg2) ?>">
    =
    <input id="result" type="text" readonly value="<?= htmlspecialchars($result) ?>">
</form>
<?php if (!$errors): ?>
    <p id="textResult">Результат: <?= htmlspecialchars($textResult) ?></p>
<?php else: ?>
    <?php foreach ($errors as $error): ?>
        <p style="color:red">Ошибка: <?= htmlspecialchars($error) ?></p>
    <?php endforeach; ?>
<?php endif; ?>
<p style="color:red" id="errors"></p>
<script>
    window.onload = function () {
        document.querySelectorAll('.calcButton').forEach(button => {
            button.onclick = function () {
                const arg1 = document.getElementById('arg1').value;
                const arg2 = document.getElementById('arg2').value;
                const operation = this.getAttribute('data-operation');
                //сделать запрос calculator.php?arg1=2&operation=%2B&arg2=3
                (
                    async () => {
                        try {
                            const response = await fetch(`?arg1=${arg1}&operation=${encodeURIComponent(operation)}&arg2=${arg2}&ajax`);
                            const result = await response.json();
                            switch (result.status) {
                                case 'success':
                                    document.getElementById('errors').innerText = '';
                                    document.getElementById('result').value = result.data.result;
                                    document.getElementById('textResult').innerText = 'Результат: ' + result.data.textResult;
                                    break;
                                case 'error':
                                    document.getElementById('result').value = '';
                                    document.getElementById('textResult').innerText = 'Результат: ';
                                    document.getElementById('errors').innerText = 'Ошибка: ' + result.errors;
                                    break;
                                default:
                                    console.error('Ошибка: не верный формат ответа');
                            }

                        } catch (error) {
                            console.error('Ошибка:', error);
                        }
                    }
                )();
            }
        });

    }
</script>
</body>
</html>

