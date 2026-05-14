<?php
session_start();
require_once 'db.php';
require_once 'auth.php';
require_once 'validation.php';

initDB();

// Обработка выхода из HTTP Auth
if (isset($_GET['logout'])) {
    header('HTTP/1.0 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin Area"');
    die('Вы вышли из системы. Закройте браузер или введите новые учетные данные.');
}

// HTTP Basic Auth проверка
requireHttpAuth();

$action = $_GET['action'] ?? 'list';
$apps = getAllApplications();
$languages = getAllLanguages();
$stats = getLanguageStats();
$editApp = null;

// Обработка удаления
if ($action === 'delete' && $_POST) {
    $appId = $_POST['app_id'] ?? null;
    if ($appId) {
        deleteApplication($appId);
        header("Location: admin.php");
        exit;
    }
}

// Загрузка приложения для редактирования
if ($action === 'edit') {
    $appId = $_GET['app_id'] ?? null;
    if ($appId) {
        $editApp = getApplicationWithLanguages($appId);
    }
}

// Обновление приложения
if ($action === 'update' && $_POST) {
    $appId = $_POST['app_id'] ?? null;
    
    if ($appId) {
        $data = [
            'name' => $_POST['name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'birthdate' => $_POST['birthdate'] ?? '',
            'gender' => $_POST['gender'] ?? '',
            'languages' => $_POST['languages'] ?? [],
            'bio' => $_POST['bio'] ?? '',
            'contract' => isset($_POST['contract']) ? 1 : 0
        ];
        
        $validation = validateFormData($data);
        
        if ($validation['valid']) {
            $pdo = getDB();
            
            try {
                $pdo->beginTransaction();
                
                $stmt = $pdo->prepare("
                    UPDATE applications 
                    SET name = ?, phone = ?, email = ?, birthdate = ?, gender = ?, bio = ?, contract_agreed = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $data['name'],
                    $data['phone'],
                    $data['email'],
                    $data['birthdate'],
                    $data['gender'],
                    $data['bio'],
                    $data['contract'],
                    $appId
                ]);
                
                $stmt = $pdo->prepare("DELETE FROM application_languages WHERE application_id = ?");
                $stmt->execute([$appId]);
                
                $stmt = $pdo->prepare("
                    INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)
                ");
                foreach ($data['languages'] as $langId) {
                    $stmt->execute([$appId, $langId]);
                }
                
                $pdo->commit();
                
                $_SESSION['admin_success'] = "Данные обновлены";
                header("Location: admin.php");
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['admin_error'] = $e->getMessage();
            }
        } else {
            $_SESSION['admin_errors'] = $validation['errors'];
            header("Location: admin.php?action=edit&app_id=" . $appId);
            exit;
        }
    }
}

$adminSuccess = $_SESSION['admin_success'] ?? null;
$adminError = $_SESSION['admin_error'] ?? null;
$adminErrors = $_SESSION['admin_errors'] ?? [];

unset($_SESSION['admin_success']);
unset($_SESSION['admin_error']);
unset($_SESSION['admin_errors']);

$rules = getValidationRules();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Администраторская панель</title>
<style>
* {
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background: #f5f5f5;
    margin: 0;
    padding: 20px;
}

.container {
    max-width: 1200px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

.admin-info {
    text-align: right;
    font-size: 14px;
}

.tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    border-bottom: 2px solid #eee;
}

.tab {
    padding: 10px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    color: #666;
    transition: 0.2s;
}

.tab.active {
    color: #667eea;
    border-bottom: 3px solid #667eea;
}

.tab:hover {
    color: #667eea;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.message {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 6px;
}

.message.success {
    background: #efe;
    border-left: 4px solid #4c4;
    color: #4c4;
}

.message.error {
    background: #fee;
    border-left: 4px solid #f44;
    color: #c33;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background: #f0f0f0;
    font-weight: bold;
}

tr:hover {
    background: #f9f9f9;
}

.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    margin-right: 5px;
}

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5a67d8;
}

.btn-danger {
    background: #c33;
    color: white;
}

.btn-danger:hover {
    background: #a22;
}

.btn-small {
    padding: 4px 8px;
    font-size: 11px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.stat-card h3 {
    margin: 0;
    font-size: 14px;
}

.stat-card .number {
    font-size: 32px;
    font-weight: bold;
    margin-top: 10px;
}

.edit-form {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.edit-form label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
}

.edit-form input, .edit-form select, .edit-form textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.edit-form input:focus, .edit-form select:focus, .edit-form textarea:focus {
    border-color: #667eea;
    outline: none;
}

.row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.delete-form {
    display: inline;
}

.delete-btn {
    background: #c33;
    color: white;
}

.delete-btn:hover {
    background: #a22;
}

.logout-link {
    color: #c33;
    text-decoration: none;
    font-weight: bold;
    border: 1px solid #ccc;
    padding: 8px 12px;
    border-radius: 4px;
    display: inline-block;
    transition: 0.2s;
}

.logout-link:hover {
    background: #fee;
    border-color: #c33;
}
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h1>Администраторская панель</h1>
        </div>
        <div class="admin-info">
            <p>Авторизованы как администратор</p>
            <a href="admin.php?logout=1" class="logout-link">Выход</a>
        </div>
    </div>

    <?php if ($adminSuccess): ?>
        <div class="message success"><?php echo htmlspecialchars($adminSuccess); ?></div>
    <?php endif; ?>
    
    <?php if ($adminError): ?>
        <div class="message error"><?php echo htmlspecialchars($adminError); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($adminErrors)): ?>
        <div class="message error">
            <strong>Ошибки:</strong>
            <ul>
                <?php foreach ($adminErrors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="tabs">
        <button class="tab active" onclick="switchTab('applications')">Заявки (<?php echo count($apps); ?>)</button>
        <button class="tab" onclick="switchTab('statistics')">Статистика</button>
    </div>

    <!-- TAB: Заявки -->
    <div id="applications" class="tab-content active">
        <?php if ($action === 'edit' && $editApp): ?>
            <!-- Форма редактирования -->
            <div class="edit-form">
                <h2>Редактирование заявки</h2>
                
                <form method="POST" action="admin.php?action=update">
                    <input type="hidden" name="app_id" value="<?php echo $editApp['id']; ?>">
                    
                    <label>ФИО</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($editApp['name']); ?>">
                    
                    <div class="row">
                        <div>
                            <label>Телефон</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($editApp['phone']); ?>">
                        </div>
                        <div>
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($editApp['email']); ?>">
                        </div>
                    </div>
                    
                    <label>Дата рождения</label>
                    <input type="date" name="birthdate" value="<?php echo $editApp['birthdate']; ?>">
                    
                    <label>Пол</label>
                    <select name="gender">
                        <option value="male" <?php echo $editApp['gender'] === 'male' ? 'selected' : ''; ?>>Мужской</option>
                        <option value="female" <?php echo $editApp['gender'] === 'female' ? 'selected' : ''; ?>>Женский</option>
                    </select>
                    
                    <label>Языки программирования</label>
                    <select name="languages[]" multiple size="6">
                        <?php foreach ($languages as $lang): ?>
                            <option value="<?php echo $lang['id']; ?>" 
                                    <?php echo in_array($lang['id'], $editApp['languages']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lang['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label>Биография</label>
                    <textarea name="bio" rows="4"><?php echo htmlspecialchars($editApp['bio']); ?></textarea>
                    
                    <label>
                        <input type="checkbox" name="contract" <?php echo $editApp['contract_agreed'] ? 'checked' : ''; ?>>
                        С контрактом ознакомлен
                    </label>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Сохранить</button>
                </form>
                
                <a href="admin.php" style="display: inline-block; margin-top: 10px; color: #667eea;">← Отменить</a>
            </div>
        <?php else: ?>
            <!-- Таблица заявок -->
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Пол</th>
                        <th>Дата рождения</th>
                        <th>Логин пользователя</th>
                        <th>Создано</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($apps as $app): ?>
                        <tr>
                            <td><?php echo $app['id']; ?></td>
                            <td><?php echo htmlspecialchars($app['name']); ?></td>
                            <td><?php echo htmlspecialchars($app['phone']); ?></td>
                            <td><?php echo htmlspecialchars($app['email']); ?></td>
                            <td><?php echo $app['gender'] === 'male' ? 'М' : 'Ж'; ?></td>
                            <td><?php echo $app['birthdate']; ?></td>
                            <td><?php echo $app['login'] ? htmlspecialchars($app['login']) : '—'; ?></td>
                            <td><?php echo date('d.m.Y', strtotime($app['created_at'])); ?></td>
                            <td>
                                <a href="admin.php?action=edit&app_id=<?php echo $app['id']; ?>" class="btn btn-primary btn-small">Редактировать</a>
                                <form method="POST" action="admin.php?action=delete" style="display: inline;" onsubmit="return confirm('Вы уверены?')">
                                    <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-small">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- TAB: Статистика -->
    <div id="statistics" class="tab-content">
        <h2>Статистика по языкам программирования</h2>
        
        <div class="stats-grid">
            <?php foreach ($stats as $stat): ?>
                <div class="stat-card">
                    <h3><?php echo htmlspecialchars($stat['name']); ?></h3>
                    <div class="number"><?php echo $stat['count']; ?></div>
                    <p style="margin: 5px 0 0 0; font-size: 12px;">пользователей</p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Язык программирования</th>
                    <th>Количество пользователей</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats as $stat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($stat['name']); ?></td>
                        <td><?php echo $stat['count']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Скрыть все вкладки
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
    
    // Показать выбранную вкладку
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
}
</script>

</body>
</html>
