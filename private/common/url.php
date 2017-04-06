<?php

/* Функция парсит чистый URL */
function url_parser($url)
{
    $url_params = explode('/', $url);
    $result = array_diff($url_params, array(''));

    return $result;  
}

/**
 * Получить уровень вложенности htt_root_path от корня HTTP сервера
 */
function url_get_root_depth()
{
    $path = explode('/', global_conf()['http_root_path']);
    $result = array_diff($path, array(''));
    $depth = count($result);
    return $depth;
}