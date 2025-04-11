<?php
require 'utils.php';

$env = parse_ini_file('.env');

define('SMARTCAPTCHA_SERVER_KEY', $env['SECRET_KEY']);

if (is_logged_in()) {
    header("Location: index.php");
    exit;
}

function check_captcha($token) {
    $ch = curl_init("https://smartcaptcha.yandexcloud.net/validate");
    $args = [
        "secret" => SMARTCAPTCHA_SERVER_KEY,
        "token" => $token
    ];
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        return false;
    }

    $resp = json_decode($server_output);
    return isset($resp->status) && $resp->status === "ok";
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $captcha_token = $_POST['smart-token'] ?? '';

    if (!check_captcha($captcha_token)) {
        $errors[] = "Неверная CAPTCHA.";
    } else {
        $user = get_user_by_login($login);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Неверный логин или пароль.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
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
            background: #007bff;
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
    <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
</head>
<body>
    <form action="login.php" method="post">
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?= implode("<br>", $errors) ?>
            </div>
        <?php endif; ?>

        <div>
            <label for="login">Email или Телефон</label>
            <input type="text" name="login" id="login" required>
        </div>
        <div>
            <label for="password">Пароль</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div>
            <div id="captcha-container"
                class="smart-captcha"
                data-sitekey="ysc1_ejo0Ehc3GBei3HtwHkeKrzhPdN2uBjwGbcFCzlJf12f40d98">
                <input type="hidden" name="smart-token" id="smart-token">
            </div>
        </div>
        <div>
            <button type="submit">Авторизоваться</button>
        </div>
        <a href="register.php">Еще нет аккаунта? Зарегистрируйтесь</a>
    </form>

    <script>
        window.smartCaptchaCallback = function() {
            window.smartCaptcha.render("captcha-container", {
                onReady: function() {},
                onSuccess: function(token) {
                    document.getElementById("smart-token").value = token;
                }
            });
        };
    </script>
</body>
</html>

