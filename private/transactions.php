<?php

/* Функции для работа со статьями */

require_once("common/base_sql.php"); //файл для работы с базой даных

/**
 * добавляет транзакцию
 * @param $array_params - массив с данными
 * @param [sum] - сумма
 * @param [sum_usd] - сумма в USD
 * @param [user_id] - ID пользователя
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
    $fields = array('sum', 'sum_usd', 'payer_id', 'reason', 'date');
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

    return db_insert('transactions', $data); 
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

    return db_insert('debts', $data); 
}

function transactions_get_list()
{
    $query = "SELECT * FROM transactions ORDER BY created DESC";
    return db_query($query);
}

function debts_get_list($filter = '')
{
    $query = "SELECT * FROM debts ";

    if ($filter)
        $query .= 'WHERE ' . $filter . ' ';

    $query .= " ORDER BY created DESC";
    return db_query($query);
}

function debt_get_by_id($id)
{
    if (!is_numeric($id) || !isset($id))
        return EINVAL;
    
    $query = "SELECT * FROM debts WHERE id = " . $id;
    $result = db_query($query);
    
    if ($result == FALSE)
        return ESQL;
    else 
        return $result[0];
}

function debt_remove($id)
{
    if (!is_numeric($id) || !isset($id))
        return EINVAL;

    return db_update('debts', $id, array('repaid' => '1'));
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

    return db_update('debts', $id, $set_arr);
}


/**
 * объявить о взносе
 * @param $array_params - массив с данными
 * @param [author_id] - Кто одалживает
 * @param [sum] - сумма в рублях
 * @param [sum_usd] - сумма в USD
 * @param [reason] - причина взноса
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL в случае некорретного sql запроса
 * @return id в случае успешного добавления
 */
function pledged_insert($array_params)
{
    $data = array();

    /*  выбираем только нужные поля */
    $fields = array('author_id', 'sum', 'sum_usd', 'reason');
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

    return db_insert('of_pledges', $data); 
}


?>