<?php
ini_set('error_reporting', E_ERROR);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once '/usr/local/lib/php/common.php';

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
    
    return array('global_marks' => array('http_root' => $http_root_path,
                                        'http_css' => $http_root_path . 'css/',
                                        'http_img' => $http_root_path . 'i/',
                                        'http_js' => $http_root_path . 'js/'),
                'http_root_path' => $http_root_path,
                'absolute_root_path' => $absolute_root_path,
                'clean_url_enable' => 0); // Включение чистых URL
}

function conf_db()
{
    static $config = NULL;
    if (!is_array($config))
        $config = parse_json_config('private/.database.json');

    return $config;
}

/* Открывает соединение с базой данных */
$err = db()->connect(conf_db());
if ($err < 0) {
    dbg_err("Database connection fault");
    exit;
}

?>
