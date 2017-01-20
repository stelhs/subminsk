<?php

/* Список стандартных кодов возврата */
define("EINVAL", -1); // Ошибка во входных аргументах
define("EBASE", -2); // Ошибка связи с базой
define("ESQL", -3); // Не корректный SQL запрос
define("ENOTUNIQUE", -4); // Ошибка добавления в базу, если такая запись уже существует
define('HTTP_ROOT_PATH', '/pobory/'); //путь к файлам

/*  Глобальный массив параметров для соединения с бд */
$db_connection_settings = array("host" => '127.0.0.1  ',
                                "user" => 'root',
                                "pass" => 'nuclear63',
                                "database" => 'underminsk_pobory');


/* Открывает соединение с базой данных */
$err = db_init($db_connection_settings);
if ($err < 0) {
    dbg_err("Database connection fault");
    exit;
}


/* Глобальные метки для путей к файлам */
$global_marks = array('http_root' => HTTP_ROOT_PATH,
                      'http_css' => HTTP_ROOT_PATH . 'css/',
                      'http_img' => HTTP_ROOT_PATH . 'i/',
                      'http_js' => HTTP_ROOT_PATH . 'js/');

?>