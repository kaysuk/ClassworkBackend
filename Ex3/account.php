<?php
session_start();
require_once 'db.php';
require_once 'auth.php';
require_once 'validation.php';

initDB();

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$userId = getCurrentUserId();
$applications = getUserApplications($userId);
$editAppId = $_GET['edit_app'] ?? null;
$editApp = null;

$editErrors = $_SESSION['edit_errors'] ?? [];
$editErrorFields = $_SESSION['edit_error_fields'] ?? [];
$editSuccess = $_SESSION['edit_success'] ?? false;

unset($_SESSION['edit_errors']);
unset($_SESSION['edit_error_fields']);
unset($_SESSION['edit_success']);

if ($editAppId) {
    $editApp = getApplicationWithLanguages($editAppId);
    if (!$editApp || $editApp['user_id'] != $userId) {
        header("Location: account.php");
        exit;
    }
}

$rules = getValidationRules();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Личный кабинет</title>
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
    max-width: 900px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #eee;
    padding-bottom: 20px;
}

h1 {
    margin: 0;
}

.logout-btn {
    background: #c33;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.logout-btn:hover {
    background: #a22;
}

.applications-list {
    margin-bottom: 30px;
}

.app-card {
    border: 1px solid #ddd;
    padding: 20px;
    margin-bottom: 15px;
    border-radius: 8px;
    background: #f9f9f9;
}

.app-card h3 {
    margin-top: 0;
}

.app-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 15px;
}

.detail-item {
    font-size: 14px;
}

.detail-item strong {
    display: block;
    margin-bottom: 5px;
}

.app-buttons {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 8px 16px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn:hover {
    background: #5a67d8;
}

.btn-danger {
    background: #c33;
}

.btn-danger:hover {
    background: #a22;
}

.edit-form {
    background: #f0f0f0;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
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
    box-sizing: border-box;
}

input:focus, select:focus, textarea:focus {
    border-color: #667eea;
    outline: none;
}

input.field-error, select.field-error, textarea.field-error {
    border-color: #f44;
    background-color: #fff5f5;
}

.field-error-hint {
    font-size: 12px;
    color: #f44;
    margin-top: 5px;
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

button[type="submit"] {
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

button[type="submit"]:hover {
    background: #5a67d8;
}
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h1>Личный кабинет</h1>
            <p>Логин: <strong><?php echo htmlspecialchars($_SESSION['user_login']); ?></strong></p>
        </div>
        <a href="index.php?action=logout" class="logout-btn">Выход</a>
    </div>

    <?php if ($editApp): ?>
        <div class="edit-form">
            <h2>Редактирование заявки</h2>
            
            <?php if (!empty($editErrors)): ?>
                <div class="error-section">
                    <h3>Ошибки при заполнении формы:</h3>
                    <ul>
                        <?php foreach ($editErrors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($editSuccess): ?>
                <div class="success-section">
                    ✓ Данные успешно обновлены!
                </div>
            <?php endif; ?>
            
            <form method="POST" action="index.php?action=edit">
                <input type="hidden" name="app_id" value="<?php echo $editApp['id']; ?>">
                
                <label>ФИО</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($editApp['name']); ?>" 
                       class="<?php echo isset($editErrorFields['name']) ? 'field-error' : ''; ?>">
                <?php if (isset($editErrorFields['name'])): ?>
                    <div class="field-error-hint"><?php echo htmlspecialchars($editErrorFields['name']); ?></div>
                <?php endif; ?>
                
                <div class="row">
                    <div>
                        <label>Телефон</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($editApp['phone']); ?>" 
                               class="<?php echo isset($editErrorFields['phone']) ? 'field-error' : ''; ?>">
                        <?php if (isset($editErrorFields['phone'])): ?>
                            <div class="field-error-hint"><?php echo htmlspecialchars($editErrorFields['phone']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($editApp['email']); ?>" 
                               class="<?php echo isset($editErrorFields['email']) ? 'field-error' : ''; ?>">
                        <?php if (isset($editErrorFields['email'])): ?>
                            <div class="field-error-hint"><?php echo htmlspecialchars($editErrorFields['email']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <label>Дата рождения</label>
                <input type="date" name="birthdate" value="<?php echo $editApp['birthdate']; ?>" 
                       class="<?php echo isset($editErrorFields['birthdate']) ? 'field-error' : ''; ?>">
                <?php if (isset($editErrorFields['birthdate'])): ?>
                    <div class="field-error-hint"><?php echo htmlspecialchars($editErrorFields['birthdate']); ?></div>
                <?php endif; ?>
                
                <label>Пол</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="gender" value="male" 
                               <?php echo $editApp['gender'] === 'male' ? 'checked' : ''; ?>> Мужской
                    </label>
                    <label>
                        <input type="radio" name="gender" value="female" 
                               <?php echo $editApp['gender'] === 'female' ? 'checked' : ''; ?>> Женский
                    </label>
                </div>
                
                <label>Любимые языки программирования</label>
                <select name="languages[]" multiple size="6" 
                        class="<?php echo isset($editErrorFields['languages']) ? 'field-error' : ''; ?>">
                    <option value="1" <?php echo in_array('1', $editApp['languages']) ? 'selected' : ''; ?>>Pascal</option>
                    <option value="2" <?php echo in_array('2', $editApp['languages']) ? 'selected' : ''; ?>>C</option>
                    <option value="3" <?php echo in_array('3', $editApp['languages']) ? 'selected' : ''; ?>>C++</option>
                    <option value="4" <?php echo in_array('4', $editApp['languages']) ? 'selected' : ''; ?>>JavaScript</option>
                    <option value="5" <?php echo in_array('5', $editApp['languages']) ? 'selected' : ''; ?>>PHP</option>
                    <option value="6" <?php echo in_array('6', $editApp['languages']) ? 'selected' : ''; ?>>Python</option>
                    <option value="7" <?php echo in_array('7', $editApp['languages']) ? 'selected' : ''; ?>>Java</option>
                    <option value="8" <?php echo in_array('8', $editApp['languages']) ? 'selected' : ''; ?>>Haskel</option>
                    <option value="9" <?php echo in_array('9', $editApp['languages']) ? 'selected' : ''; ?>>Clojure</option>
                    <option value="10" <?php echo in_array('10', $editApp['languages']) ? 'selected' : ''; ?>>Prolog</option>
                    <option value="11" <?php echo in_array('11', $editApp['languages']) ? 'selected' : ''; ?>>Scala</option>
                    <option value="12" <?php echo in_array('12', $editApp['languages']) ? 'selected' : ''; ?>>Go</option>
                </select>
                
                <label>Биография</label>
                <textarea name="bio" rows="4" 
                          class="<?php echo isset($editErrorFields['bio']) ? 'field-error' : ''; ?>"><?php echo htmlspecialchars($editApp['bio']); ?></textarea>
                <?php if (isset($editErrorFields['bio'])): ?>
                    <div class="field-error-hint"><?php echo htmlspecialchars($editErrorFields['bio']); ?></div>
                <?php endif; ?>
                
                <div class="checkbox">
                    <input type="checkbox" name="contract" <?php echo $editApp['contract_agreed'] ? 'checked' : ''; ?>>
                    <span>С контрактом ознакомлен</span>
                </div>
                
                <button type="submit">Сохранить изменения</button>
            </form>
            
            <a href="account.php" style="display: inline-block; margin-top: 10px; color: #667eea; text-decoration: none;">← Отменить</a>
        </div>
    <?php endif; ?>

    <div class="applications-list">
        <h2>Ваши заявки (<?php echo count($applications); ?>)</h2>
        
        <?php if (empty($applications)): ?>
            <p>У вас еще нет заявок. <a href="form.php">Создать новую</a></p>
        <?php else: ?>
            <?php foreach ($applications as $app): ?>
                <div class="app-card">
                    <h3><?php echo htmlspecialchars($app['name']); ?></h3>
                    <div class="app-details">
                        <div class="detail-item">
                            <strong>Телефон:</strong>
                            <?php echo htmlspecialchars($app['phone']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Email:</strong>
                            <?php echo htmlspecialchars($app['email']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Дата рождения:</strong>
                            <?php echo $app['birthdate']; ?>
                        </div>
                        <div class="detail-item">
                            <strong>Пол:</strong>
                            <?php echo $app['gender'] === 'male' ? 'Мужской' : 'Женский'; ?>
                        </div>
                        <div class="detail-item">
                            <strong>Биография:</strong>
                            <?php echo htmlspecialchars(substr($app['bio'], 0, 100)); ?>...
                        </div>
                        <div class="detail-item">
                            <strong>Создано:</strong>
                            <?php echo date('d.m.Y H:i', strtotime($app['created_at'])); ?>
                        </div>
                    </div>
                    <div class="app-buttons">
                        <a href="account.php?edit_app=<?php echo $app['id']; ?>" class="btn">Редактировать</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
