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
    $fields = array('sum', 'sum_usd', 'total', 'total_usd', 'payer_id', 'author_id', 'reason', 'date');
    foreach ($array_params as $key => $value)
        if (in_array($key, $fields))
            $data[$key] = $value;
            
    if(empty($data["sum"]) && empty($data["sum_usd"])) { 
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


/**
 * добавляет долги
 * @param $array_params - массив с данными
 * @param [who_id] - Кто одалживает
 * @param [whom_id] - Кому одалживает
 * @param [sum] - сумма в рублях
 * @param [sum_usd] - сумма в USD
 * @param [reason] - причина долга
 * @param [date] - дата одалживания
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL в случае некорретного sql запроса
 * @return id в случае успешного добавления
 */
function debts_insert($array_params)
{
    $data = array();

    /*  выбираем только нужные поля */
    $fields = array('who_id', 'whom_id', 'sum', 'sum_usd', 'date', 'reason');
    foreach ($array_params as $key => $value)
        if (in_array($key, $fields))
            $data[$key] = $value;
            
    if(empty($data["sum"]) && empty($data["sum_usd"])) { 
        dbg_err("sum is not set");    
        return EINVAL;    
    }

    if(empty($data["whom_id"])) {
        dbg_err("WHOM is not set");    
        return EINVAL;
    }
    
    if(empty($data["date"])) {
        dbg_err("date is not set");    
        return EINVAL;    
    }

    return db()->insert('debts', $data); 
}


function transactions_get_list()
{
    $query = "SELECT * FROM transactions ORDER BY created DESC";
    return db()->query_list($query);
}

/*
   Посчитать сумму по всем транзакциям
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL в случае некорретного sql запроса
 * @return array(sum, sum_usd) в случае успешного добавления    
*/
function transactions_calc_sum()
{
    $query = "SELECT sum(sum) as total, " .
                     "sum(sum_usd) as total_usd " .
                     "FROM transactions ";
    return db()->query($query);
}


function debts_get_list($filter = '')
{
    $query = "SELECT * FROM debts ";

    if ($filter)
        $query .= 'WHERE ' . $filter . ' ';

    $query .= " ORDER BY created DESC";
    return db()->query_list($query);
}

function debt_get_by_id($id)
{
    if (!is_numeric($id) || !isset($id))
        return EINVAL;
    
    $query = "SELECT * FROM debts WHERE id = " . $id;
    $result = db()->query($query);
    
    if ($result == FALSE)
        return ESQL;
    else 
        return $result[0];
}

function debt_remove($id)
{
    if (!is_numeric($id) || !isset($id))
        return EINVAL;

    return db()->update('debts', $id, array('repaid' => '1'));
}

function debt_change_sum($id, $new_sum, $new_sum_usd)
{
    if (!is_numeric($id) || !isset($id))
        return EINVAL;

    $set_arr = array();
    if ($new_sum)
        $set_arr['sum'] = $new_sum;

    if ($new_sum_usd)
        $set_arr['sum_usd'] = $new_sum_usd;

    return db()->update('debts', $id, $set_arr);
}


/**
 * объявить о взносе
 * @param $array_params - массив с данными
 * @param [author_id] - Кто одалживает
 * @param [sum] - сумма в рублях
 * @param [sum_usd] - сумма в USD
 * @param [reason] - причина взноса
 * @param [except] - список ID пользователей исключенных из взноса
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL в случае некорретного sql запроса
 * @return id в случае успешного добавления
 */
function pledged_insert($array_params)
{
    $data = array();

    /*  выбираем только нужные поля */
    $fields = array('author_id', 'sum', 'sum_usd', 'reason', 'except');
    foreach ($array_params as $key => $value)
        if (in_array($key, $fields))
            $data[$key] = $value;
            
    if(empty($data["sum"]) && empty($data["sum_usd"])) { 
        dbg_err("sum is not set");    
        return EINVAL;    
    }

    if(empty($data["author_id"])) {
        dbg_err("Author is not set");    
        return EINVAL;
    }

    if(empty($data["reason"])) {
        dbg_err("Reason is not set");    
        return EINVAL;    
    }

    return db()->insert('of_pledges', $data); 
}


?>