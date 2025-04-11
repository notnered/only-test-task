<?php
require 'utils.php';

if (is_logged_in()) {
    header("Location: index.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['fullname']);
    $phone = trim($_POST['phonenum']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if ($password !== $password2) {
        $errors[] = "Пароли не совпадают.";
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$email, $phone]);
    if ($stmt->fetch()) {
        $errors[] = "Пользователь с таким email или телефоном уже существует.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $email, $hashed_password]);
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            padding-top: 50px;
        }
        form {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
            width: 300px;
        }
        div {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 6px;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #aaa;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }
        .error {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        a {
            display: block;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <form action="register.php" method="post">
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?= implode("<br>", $errors) ?>
            </div>
        <?php endif; ?>

        <div>
            <label for="fullname">Имя</label>
            <input type="text" name="fullname" id="fullname" required>
        </div>
        <div>
            <label for="phonenum">Номер телефона</label>
            <input type="text" name="phonenum" id="phonenum" required>
        </div>
        <div>
            <label for="email">Электронная почта</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="password">Пароль</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div>
            <label for="password2">Повторите пароль</label>
            <input type="password" name="password2" id="password2" required>
        </div>
        <div>
            <button type="submit">Зарегистрироваться</button>
        </div>
        <a href="login.php">Уже есть аккаунт? Авторизируйтесь</a>
    </form>
</body>
</html>
