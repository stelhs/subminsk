<?php

/* Список стандартных кодов возврата */
define("EINVAL", -1); // Ошибка во входных аргументах
define("EBASE", -2); // Ошибка связи с базой
define("ESQL", -3); // Не корректный SQL запрос
define("ENOTUNIQUE", -4); // Ошибка добавления в базу, если такая запись уже существует

/*
 * Возвращает глобальные настройки системы
 */
function global_conf()
{
    $http_root_path = "/subminsk/"; // Внутренний путь к файлам
    $absolute_root_path = "/var/www/subminsk/"; // Абсолютный пусть к файлам
    
    return array('database' => array("host" => '127.0.0.1',
                                    "user" => 'subminsk',
                                    "pass" => 'kl2sfs567x23d',
                                    "database" => 'subminsk'),
                'global_marks' => array('http_root' => $http_root_path,
                                        'http_css' => $http_root_path . 'css/',
                                        'http_img' => $http_root_path . 'i/',
                                        'http_js' => $http_root_path . 'js/'),
                'http_root_path' => $http_root_path,
                'absolute_root_path' => $absolute_root_path,
                'clean_url_enable' => 0); // Включение чистых URL
}

/* Открывает соединение с базой данных */
$err = db()->connect(global_conf()['database']);
if ($err < 0) {
    dbg_err("Database connection fault");
    exit;
}

?>
