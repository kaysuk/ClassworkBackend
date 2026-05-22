<?php

// Выключаем отображение ошибок после отладки.
define('DISPLAY_ERRORS', 1);

// Папки со скриптами и модулями.
define('INCLUDE_PATH', './scripts' . PATH_SEPARATOR . './modules');

// Храним настройки в массиве чтоб легче было смотреть (print_r),
// хранить (serialize), оверрайдить и не плодить глобалов.
$conf = array(
  'sitename' => 'Drupal-Coder Portfolio',
  'theme' => './theme',
  'charset' => 'UTF-8',
  'clean_urls' => FALSE,
  'display_errors' => 1,
  'date_format' => 'Y.m.d',
  'basedir' => '/Web/Classwork2/Ex8/',
);

// Определения ресурсов для диспатчера.
$urlconf = array(
  '' => array('module' => 'front'),
  '/^admin$/' => array('module' => 'admin', 'auth' => 'auth/admin'),
  '/^portfolio$/' => array('module' => 'portfolio'),
  '/^about$/' => array('module' => 'about'),
  '/^contact$/' => array('module' => 'contact'),
);
