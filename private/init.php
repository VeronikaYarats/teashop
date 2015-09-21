<?php

/* Список стандартных кодов возврата */
define("EINVAL", -1); // Ошибка во входных аргументах
define("EBASE", -2); // Ошибка связи с базой
define("ENOTUNIQUE", -4); // Ошибка добавления в базу, если такая запись уже существует
define('HTTP_ROOT_PATH', '/veroshop/'); //путь к файлам


$host = '127.0.0.1  ';
$user = 'admin';
$pass = '13941';
$database = 'veroshop';
$port = 3306;

/* Открывает соединение с базой данных */
db_init($host, $user, $pass, $database, $port);



/* Глобальные метки для путей к файлам */
$global_marks = array('http_root' => HTTP_ROOT_PATH,
                      'http_css' => HTTP_ROOT_PATH . 'css/',
                      'http_img' => HTTP_ROOT_PATH . 'i/',
                      'http_js' => HTTP_ROOT_PATH . 'js/');

?>