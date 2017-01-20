<?php

require_once("common/base_sql.php"); //файл для работы с базой даных

function user_get_by_id($id)
{
    if (!is_numeric($id) || !isset($id))
        return EINVAL;
    
    $query = "SELECT * FROM users WHERE id = " . $id;
    $result = db_query($query);
    
    if ($result == FALSE)
        return ESQL;
    else 
        return $result[0];
}

function user_get_by_pass($username, $password)
{
    $query = "SELECT * FROM users " .
                    'WHERE `login` = "' . addslashes($username) . '" ' . 
                    'AND `pass` = PASSWORD("' . addslashes($password) . '")';
    $result = db_query($query);
    
    if ($result == FALSE)  // если бд вернула 0 строк 
        return ESQL;
    else 
        return $result[0];
}

function users_get_list()
{
    $query = "SELECT * FROM users";
    $users = array();
    $rows = db_query($query);

    foreach ($rows as $row)
        $users[$row['id']] = $row;

    return $users;
}

function user_change_pass($user_id, $new_login, $new_pass)
{
    if (!is_numeric($user_id) || !isset($user_id) || !$new_pass)
        return EINVAL;

    $query = "UPDATE users " .
                    'SET login = "' . $new_login . '", ' .
                    '`pass` = PASSWORD("' . $new_pass . '") ' .
                    'WHERE id = '. (int)$user_id;

    $result = db_query($query);

    if ($result == FALSE)
        return ESQL;

    return 0;
}

?>