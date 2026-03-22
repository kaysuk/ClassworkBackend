<?php
// тут можно потом вставить обработку
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Форма заявки</title>

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
    max-width: 600px;
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

label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
}

input, select, textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
    transition: 0.2s;
}

input:focus, select:focus, textarea:focus {
    border-color: #667eea;
    outline: none;
}

.row {
    display: flex;
    gap: 15px;
}

.row div {
    flex: 1;
}

.radio-group {
    display: flex;
    gap: 20px;
    margin-top: 5px;
}

.checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 15px;
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
</style>

</head>
<body>

<div class="container">
<h2>Форма заявки</h2>

<form method="POST" action="index.php">

<label>ФИО</label>
<input type="text" name="name" required>

<div class="row">
    <div>
        <label>Телефон</label>
        <input type="tel" name="phone" required>
    </div>
    <div>
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
</div>

<label>Дата рождения</label>
<input type="date" name="birthdate" required>

<label>Пол</label>
<div class="radio-group">
    <label><input type="radio" name="gender" value="male"> Мужской</label>
    <label><input type="radio" name="gender" value="female"> Женский</label>
</div>

<label>Любимые языки программирования</label>
<select name="languages[]" multiple size="6">
    <option value="1">Pascal</option>
    <option value="2">C</option>
    <option value="3">C++</option>
    <option value="4">JavaScript</option>
    <option value="5">PHP</option>
    <option value="6">Python</option>
    <option value="7">Java</option>
    <option value="8">Haskel</option>
    <option value="9">Clojure</option>
    <option value="10">Prolog</option>
    <option value="11">Scala</option>
    <option value="12">Go</option>
</select>

<label>Биография</label>
<textarea name="bio" rows="4"></textarea>

<div class="checkbox">
    <input type="checkbox" name="contract">
    <span>С контрактом ознакомлен</span>
</div>

<button type="submit">Сохранить</button>

</form>
</div>

</body>
</html>