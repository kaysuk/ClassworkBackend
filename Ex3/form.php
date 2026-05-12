<?php
session_start();

// Получаем ошибки и значения из Cookies
$errors = isset($_COOKIE['form_errors']) ? json_decode($_COOKIE['form_errors'], true) : [];
$errorFields = isset($_COOKIE['form_error_fields']) ? json_decode($_COOKIE['form_error_fields'], true) : [];
$savedName = isset($_COOKIE['form_name']) ? htmlspecialchars($_COOKIE['form_name']) : '';
$savedPhone = isset($_COOKIE['form_phone']) ? htmlspecialchars($_COOKIE['form_phone']) : '';
$savedEmail = isset($_COOKIE['form_email']) ? htmlspecialchars($_COOKIE['form_email']) : '';
$savedBirthdate = isset($_COOKIE['form_birthdate']) ? htmlspecialchars($_COOKIE['form_birthdate']) : '';
$savedGender = isset($_COOKIE['form_gender']) ? htmlspecialchars($_COOKIE['form_gender']) : '';
$savedBio = isset($_COOKIE['form_bio']) ? htmlspecialchars($_COOKIE['form_bio']) : '';
$savedLanguages = isset($_COOKIE['form_languages']) ? json_decode($_COOKIE['form_languages'], true) : [];

// Проверяем успех
$success = isset($_GET['success']);

// Удаляем Cookies с ошибками после отображения
if (!empty($errors)) {
    setcookie("form_errors", "", time() - 3600, "/");
    setcookie("form_error_fields", "", time() - 3600, "/");
}
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

.error-section {
    background: #fee;
    border-left: 4px solid #f44;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.error-section h3 {
    margin: 0 0 10px 0;
    color: #c33;
}

.error-section ul {
    margin: 0;
    padding-left: 20px;
    color: #c33;
}

.success-section {
    background: #efe;
    border-left: 4px solid #4c4;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    color: #4c4;
}

.field-error-hint {
    font-size: 12px;
    color: #f44;
    margin-top: 5px;
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

input.field-error, select.field-error, textarea.field-error {
    border-color: #f44;
    background-color: #fff5f5;
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

<?php if (!empty($errors)): ?>
    <div class="error-section">
        <h3>Ошибки при заполнении формы:</h3>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success-section">
        ✓ Данные успешно сохранены! Они будут использованы как значения по умолчанию при следующем посещении.
    </div>
<?php endif; ?>

<form method="POST" action="index.php">

<label>ФИО</label>
<input type="text" name="name" value="<?php echo $savedName; ?>" 
       class="<?php echo isset($errorFields['name']) ? 'field-error' : ''; ?>">
<?php if (isset($errorFields['name'])): ?>
    <div class="field-error-hint"><?php echo htmlspecialchars($errorFields['name']); ?></div>
<?php endif; ?>

<div class="row">
    <div>
        <label>Телефон</label>
        <input type="tel" name="phone" value="<?php echo $savedPhone; ?>" 
               class="<?php echo isset($errorFields['phone']) ? 'field-error' : ''; ?>">
        <?php if (isset($errorFields['phone'])): ?>
            <div class="field-error-hint"><?php echo htmlspecialchars($errorFields['phone']); ?></div>
        <?php endif; ?>
    </div>
    <div>
        <label>Email</label>
        <input type="email" name="email" value="<?php echo $savedEmail; ?>" 
               class="<?php echo isset($errorFields['email']) ? 'field-error' : ''; ?>">
        <?php if (isset($errorFields['email'])): ?>
            <div class="field-error-hint"><?php echo htmlspecialchars($errorFields['email']); ?></div>
        <?php endif; ?>
    </div>
</div>

<label>Дата рождения</label>
<input type="date" name="birthdate" value="<?php echo $savedBirthdate; ?>" 
       class="<?php echo isset($errorFields['birthdate']) ? 'field-error' : ''; ?>">
<?php if (isset($errorFields['birthdate'])): ?>
    <div class="field-error-hint"><?php echo htmlspecialchars($errorFields['birthdate']); ?></div>
<?php endif; ?>

<label>Пол</label>
<div class="radio-group">
    <label>
        <input type="radio" name="gender" value="male" 
               <?php echo $savedGender === 'male' ? 'checked' : ''; ?>> Мужской
    </label>
    <label>
        <input type="radio" name="gender" value="female" 
               <?php echo $savedGender === 'female' ? 'checked' : ''; ?>> Женский
    </label>
</div>
<?php if (isset($errorFields['gender'])): ?>
    <div class="field-error-hint">Это поле обязательно</div>
<?php endif; ?>

<label>Любимые языки программирования</label>
<select name="languages[]" multiple size="6" 
        class="<?php echo isset($errorFields['languages']) ? 'field-error' : ''; ?>">
    <option value="1" <?php echo in_array('1', $savedLanguages) ? 'selected' : ''; ?>>Pascal</option>
    <option value="2" <?php echo in_array('2', $savedLanguages) ? 'selected' : ''; ?>>C</option>
    <option value="3" <?php echo in_array('3', $savedLanguages) ? 'selected' : ''; ?>>C++</option>
    <option value="4" <?php echo in_array('4', $savedLanguages) ? 'selected' : ''; ?>>JavaScript</option>
    <option value="5" <?php echo in_array('5', $savedLanguages) ? 'selected' : ''; ?>>PHP</option>
    <option value="6" <?php echo in_array('6', $savedLanguages) ? 'selected' : ''; ?>>Python</option>
    <option value="7" <?php echo in_array('7', $savedLanguages) ? 'selected' : ''; ?>>Java</option>
    <option value="8" <?php echo in_array('8', $savedLanguages) ? 'selected' : ''; ?>>Haskel</option>
    <option value="9" <?php echo in_array('9', $savedLanguages) ? 'selected' : ''; ?>>Clojure</option>
    <option value="10" <?php echo in_array('10', $savedLanguages) ? 'selected' : ''; ?>>Prolog</option>
    <option value="11" <?php echo in_array('11', $savedLanguages) ? 'selected' : ''; ?>>Scala</option>
    <option value="12" <?php echo in_array('12', $savedLanguages) ? 'selected' : ''; ?>>Go</option>
</select>
<?php if (isset($errorFields['languages'])): ?>
    <div class="field-error-hint">Необходимо выбрать хотя бы один язык</div>
<?php endif; ?>

<label>Биография</label>
<textarea name="bio" rows="4" 
          class="<?php echo isset($errorFields['bio']) ? 'field-error' : ''; ?>"><?php echo $savedBio; ?></textarea>
<?php if (isset($errorFields['bio'])): ?>
    <div class="field-error-hint"><?php echo htmlspecialchars($errorFields['bio']); ?></div>
<?php endif; ?>

<div class="checkbox">
    <input type="checkbox" name="contract" 
           <?php echo isset($errorFields['contract']) ? 'class="field-error"' : ''; ?>>
    <span>С контрактом ознакомлен</span>
</div>
<?php if (isset($errorFields['contract'])): ?>
    <div class="field-error-hint">Это поле обязательно</div>
<?php endif; ?>

<button type="submit">Сохранить</button>

</form>
</div>

</body>
</html>