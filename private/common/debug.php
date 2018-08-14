<?php
/*
 * Набор инструментов для отладки
 */

/**
 * Вывод сообщения об ошибке
 */
function dbg_err($data)
{
    dump("ERROR:\n");
    dump($data);
}

/**
 * Вывод сообщения о предупреждении
 */
function dbg_warn($data)
{
    dump("WARNING:\n");
    dump($data);
}

/**
 * Вывод сообщения уведомления
 * 
 */
function dbg_notice($data)
{
    dump("NOTICE:\n");
    dump($data);
}

?>