<?php 
/*
 * Функции для отображения всплывающего окна с сообщением 
 */


/**
 * Подготовка к выводу всплывающего окна с сообщением
 * @param $block название блока из файла message_boxes.html с шаблоном сообщения
 * @param $data массив меток для шаблона с сообщением
 */
function message_box_display($block, $data = array())
{
    $_SESSION['display_window'] = array('name' => $block, 'data' => $data);
}

/**
 * Функция возвращает данные всплывающего окна,
 * если ранее была запущена функция message_box_display().
 * Используется в index.php
 */
function message_box_check_for_display()
{
    $block = $_SESSION['display_window']["name"];
    $data = $_SESSION['display_window']["data"];
    unset($_SESSION['display_window']);
    return array('block' => $block, 'data' => $data);
}
?>