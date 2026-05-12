<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit;
}

$loginError = isset($_COOKIE['login_error']) ? htmlspecialchars($_COOKIE['login_error']) : '';

if (!empty($loginError)) {
    setcookie("login_error", "", time() - 3600, "/");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Вход</title>
<style>
* {
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background: linear-gradient(135deg, #667eea, #764ba2);
    margin: 0;
    padding: 40px;
}

.container {
    max-width: 400px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

.error-section {
    background: #fee;
    border-left: 4px solid #f44;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    color: #c33;
}

label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
}

input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
    transition: 0.2s;
    box-sizing: border-box;
}

input:focus {
    border-color: #667eea;
    outline: none;
}

button {
    width: 100%;
    margin-top: 20px;
    padding: 12px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.2s;
}

button:hover {
    background: #5a67d8;
}

.links {
    text-align: center;
    margin-top: 20px;
}

.links a {
    margin: 0 10px;
    color: #667eea;
    text-decoration: none;
}

.links a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="container">
<h2>Вход в систему</h2>

<?php if ($loginError): ?>
    <div class="error-section">
        ✗ <?php echo $loginError; ?>
    </div>
<?php endif; ?>

<form method="POST" action="index.php?action=login">
    <label>Логин</label>
    <input type="text" name="login" required autofocus>
    
    <label>Пароль</label>
    <input type="password" name="password" required>
    
    <button type="submit">Вход</button>
</form>

<div class="links">
    <a href="form.php">← Регистрация</a>
</div>
</div>

</body>
</html>
