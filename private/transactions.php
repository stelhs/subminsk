<?php

/* Функции для работа со статьями */

/**
 * добавляет транзакцию
 * @param $array_params - массив с данными
 * @param [sum] - сумма
 * @param [sum_usd] - сумма в USD
 * @param [total] - общая сумма до транзакции
 * @param [total_usd] - общая сумма до транзакции в USD
 * @param [payer_id] - ID пользователя благодаря которому была осуществлена транзакция
 * @param [author_id] - ID пользователя благодаря которому была осуществлена транзакция
 * @param [reason] - причина транзакции
 * @param [date] - дата транзакции
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL в случае некорретного sql запроса
 * @return id в случае успешного добавления
 */
function transactions_insert($array_params)
{
    $data = array();

    /*  выбираем только нужные поля */
    $fields = array('sum', 'src_id', 'dst_id', 'author_id', 'reason', 'date');
    foreach ($array_params as $key => $value)
        if (in_array($key, $fields))
            $data[$key] = $value;

    if(!isset($data["sum"])) {
        dbg_err("sum is not set");
        return EINVAL;
    }

    if(empty($data["reason"])) {
        dbg_err("reason is not set");
        return EINVAL;
    }

    if(empty($data["date"])) {
        dbg_err("date is not set");
        return EINVAL;
    }

    return db()->insert('transactions', $data);
}



function transactions_get_list()
{
    $query = "SELECT * FROM transactions ORDER BY id DESC";
    return db()->query_list($query);
}

/*
   Посчитать сумму по всем транзакциям
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL в случае некорретного sql запроса
 * @return array(sum, sum_usd) в случае успешного добавления
*/
function transactions_calc_budget()
{
    $sum_total = db()->query("SELECT sum(sum) as sum " .
                             "FROM transactions WHERE dst_id = (" .
                                "SELECT id FROM users WHERE type='subminsk'" .
                             ") ");

    $sum_space = db()->query("SELECT sum(sum) as sum " .
                             "FROM transactions WHERE dst_id = (" .
                                 "SELECT id FROM users WHERE type='space'" .
                             ") AND src_id = (" .
                                 "SELECT id FROM users WHERE type='subminsk')");


    return sprintf("%.2f", round($sum_total['sum'] - $sum_space['sum'], 2));
}

function transactions_calc_sum_to_subminsk()
{
    $row = db()->query("SELECT sum(sum) as sum " .
                       "FROM transactions WHERE dst_id = (" .
                           "SELECT id FROM users WHERE type='subminsk'" .
                       ") ");

    return sprintf("%.2f", round($row['sum'], 2));
}

function transactions_calc_sum_to_space()
{
    $row = db()->query("SELECT sum(sum) as sum " .
                       "FROM transactions WHERE dst_id = (" .
                           "SELECT id FROM users WHERE type='space'" .
                       ") ");

    return sprintf("%.2f", round($row['sum'], 2));
}

function transactions_calc_sum_transmit_to_space_from_subminsk()
{
    $row = db()->query("SELECT sum(sum) as sum " .
                       "FROM transactions WHERE dst_id = (" .
                       "SELECT id FROM users WHERE type='space'" .
                       ") AND src_id = (" .
                       "SELECT id FROM users WHERE type='subminsk')");

    return sprintf("%.2f", round($row['sum'], 2));
}

function transactions_calc_sum_transmit_to_subminsk_from_space()
{
    $row = db()->query("SELECT sum(sum) as sum " .
                       "FROM transactions WHERE dst_id = (" .
                       "SELECT id FROM users WHERE type='subminsk'" .
                       ") AND src_id = (" .
                       "SELECT id FROM users WHERE type='space')");

    return sprintf("%.2f", round($row['sum'], 2));
}


function transactions_calc_sum_subminsk_to_users()
{
    $row = db()->query("SELECT sum(sum) as sum " .
                       "FROM transactions " .
                       "WHERE src_id = (SELECT id FROM users WHERE type='subminsk') AND " .
                             "dst_id != (SELECT id FROM users WHERE type='space')");

    return sprintf("%.2f", round($row['sum'], 2));
}

?>
