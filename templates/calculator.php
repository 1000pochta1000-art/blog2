<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Калькулятор</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?= require __DIR__ . '/vendor/autoload.php'; ?>
<h2>Калькулятор</h2>
<form action="" method="post">
    <input type="text" name="arg1" value="<?=$arg1 ?? 0?>">
    <input type="submit" name="operation" value="+">
    <input style="border: 2px solid green" type="submit" name="operation" value="-">
    <input type="text" name="arg2" value="<?=$arg2 ?? 0?>">
    =
    <input type="text" readonly value="<?=$result ?? 0?>">
</form>
</body>
</html>
