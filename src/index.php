<?php
require 'utils.php';
redirect_if_not_logged_in();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $new_password = $_POST['password'];

    $updates = [];
    $params = [];

    if ($name !== $user['name']) {
        $updates[] = "name = ?";
        $params[] = $name;
    }
    if ($phone !== $user['phone']) {
        $updates[] = "phone = ?";
        $params[] = $phone;
    }
    if ($email !== $user['email']) {
        $updates[] = "email = ?";
        $params[] = $email;
    }
    if (!empty($new_password)) {
        $updates[] = "password = ?";
        $params[] = password_hash($new_password, PASSWORD_DEFAULT);
    }

    if ($updates) {
        $params[] = $_SESSION['user_id'];
        $stmt = $pdo->prepare("UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?");
        $stmt->execute($params);
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
    <style>
        body {
            font-family: sans-serif;
            background: #eef1f5;
            display: flex;
            justify-content: center;
            padding-top: 50px;
        }
        form {
            background: white;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
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
            background: #17a2b8;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout {
            margin-top: 15px;
            display: block;
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>
    <form method="post">
        <h2>Ваш профиль</h2>
        <div>
            <label for="name">Имя</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div>
            <label for="phone">Телефон</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
        </div>
        <div>
            <label for="email">Почта</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div>
            <label for="password">Новый пароль (не обязательно)</label>
            <input type="password" name="password" id="password">
        </div>
        <div>
            <button type="submit">Сохранить изменения</button>
        </div>
        <a class="logout" href="logout.php">Выйти</a>
    </form>
</body>
</html>
