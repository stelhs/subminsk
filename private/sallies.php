<?php

/* Функции для работа со статьями */


/**
 * Добавить вылазку
 * @param $array_params - массив с данными
 * @param [date] - дата вылазки
 * @param [reason] - причина вылазки
 * @param [description] - подробное описание вылазки
 * @param [need_to_take] - что нужно ссобой взять
 * @param [author_id] - ID пользователя благодаря которому была осуществлена транзакция
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL в случае некорретного sql запроса
 * @return id в случае успешного добавления
 */
function sally_insert($array_params)
{
    $data = array();

    /*  выбираем только нужные поля */
    $fields = array('description', 'need_to_take', 'author_id', 'reason', 'date');
    foreach ($array_params as $key => $value)
        if (in_array($key, $fields))
            $data[$key] = $value;
            
    if(empty($data["reason"])) {
        dbg_err("reason is not set");    
        return EINVAL;
    }
    
    if(empty($data["date"])) {
        dbg_err("date is not set");    
        return EINVAL;    
    }

    return db()->insert('sallies', $data); 
}



function sallies_get_list()
{
    $query = "SELECT * FROM sallies ORDER BY created DESC";
    return db()->query_list($query);
}


function sally_get_by_id($id)
{
    $query = "SELECT * FROM sallies WHERE id = " . $id;
    return db()->query($query);
}

/**
 * Отредактировать вылазку с идентификатором $id
 * @param $id идентификатор редактируемой записи
 * @param $array_params - массив с данными для редактироания
 * @param [date] - дата вылазки
 * @param [reason] - причина вылазки
 * @param [description] - подробное описание вылазки
 * @param [need_to_take] - что нужно ссобой взять
 * @param [author_id] - ID пользователя благодаря которому была осуществлена транзакция
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL в случае некорретного sql запроса
 * @return 0 в случае успешного редактирования
 */
function sally_update($id, $array_params)
{
    /*  выбираем только нужные поля */
    $fields = array('description', 'need_to_take', 'author_id', 'reason', 'date');
    foreach ($array_params as $key => $value)
        if (in_array($key, $fields))
            $data[$key] = $value;
            
    if(empty($data["reason"])) {
        dbg_err("reason is not set");    
        return EINVAL;
    }
    
    if(empty($data["date"])) {
        dbg_err("date is not set");    
        return EINVAL;    
    }
    
    if (sally_get_by_id($id) <= 0) {
        dbg_err("Sallys not found"); 
        return EINVAL;
    }
    
    return db()->update('sallies', $id, $data);
}


?>