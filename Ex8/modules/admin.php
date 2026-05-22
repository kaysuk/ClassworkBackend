<?php

function admin_get($request) {
    require_once __DIR__ . '/../scripts/db.php';
    initDB();
    $requests = getContactRequests();
    return render_admin($requests);
}

function getContactRequests() {
    $pdo = getDB();
    $stmt = $pdo->query('SELECT * FROM contact_requests ORDER BY created_at DESC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function render_admin($requests) {
    $rows = '';
    foreach ($requests as $request) {
        $rows .= '<tr>' .
            '<td>' . htmlspecialchars($request['id']) . '</td>' .
            '<td>' . htmlspecialchars($request['name']) . '</td>' .
            '<td>' . htmlspecialchars($request['phone']) . '</td>' .
            '<td>' . htmlspecialchars($request['email']) . '</td>' .
            '<td>' . nl2br(htmlspecialchars($request['message'])) . '</td>' .
            '<td>' . ($request['agreed'] ? 'Да' : 'Нет') . '</td>' .
            '<td>' . htmlspecialchars($request['created_at']) . '</td>' .
        '</tr>';
    }

    if ($rows === '') {
        $rows = '<tr><td colspan="7" style="padding: 1rem; text-align:center;">Нет новых заявок.</td></tr>';
    }

    return '<section class="admin-panel" style="padding: 2rem;">
      <h1>Администраторская панель</h1>
      <p>Для доступа используйте логин <strong>admin</strong> и пароль <strong>admin</strong>.</p>
      <p><a href="./index.php">Вернуться на сайт</a></p>
      <div style="overflow-x:auto;">
        <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse:collapse;">
          <thead style="background:#f2f2f2;">
            <tr>
              <th>ID</th>
              <th>Имя</th>
              <th>Телефон</th>
              <th>E-mail</th>
              <th>Сообщение</th>
              <th>Согласие</th>
              <th>Дата</th>
            </tr>
          </thead>
          <tbody>' . $rows . '</tbody>
        </table>
      </div>
    </section>';
}
