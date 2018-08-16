<?php


/* Возвращает информацию о пользователе по id */
function user_get_by_id($id)
{
    $query = "SELECT * FROM users WHERE id = " . (int)$id;
    return db()->query($query);
}

/* Возвращает информацию о пользователе если совпадают логин и пароль */
function user_get_by_pass($login, $pass)
{
    $query = "SELECT * FROM users WHERE `login` = '" . $login .
                "' AND `pass` = PASSWORD('" . $pass ."')";
    return db()->query($query);
}

function users_get_list()
{
    return db()->query_list("SELECT * FROM users");
}

function users_get_space_user()
{
    return db()->query("SELECT id FROM users WHERE type = 'space'");
}

function users_get_subminsk_user()
{
    return db()->query("SELECT id FROM users WHERE type = 'subminsk'");
}

function users_get_balance($from_user_id, $to_user_id)
{
    $row1 = db()->query("SELECT sum(sum) as sum " .
                        "FROM transactions " .
                        "WHERE src_id = " . (int)$from_user_id . " " .
                             "AND dst_id = ". (int)$to_user_id . " ");

    $row2 = db()->query("SELECT sum(sum) as sum " .
                        "FROM transactions " .
                        "WHERE src_id = " . (int)$to_user_id . " " .
                            "AND dst_id = ". (int)$from_user_id . " ");

    return sprintf("%.2f", round($row1['sum'] - $row2['sum'], 2));
}

function users_get_transmit_sum($from_user_id, $to_user_id)
{
    $row = db()->query("SELECT sum(sum) as sum " .
                       "FROM transactions " .
                       "WHERE src_id = " . (int)$from_user_id . " " .
                           "AND dst_id = ". (int)$to_user_id . " ");
    return sprintf("%.2f", round($row['sum'], 2));
}


function user_change_pass($user_id, $new_login, $new_pass)
{
    if (!is_numeric($user_id) || !isset($user_id) || !$new_pass)
        return EINVAL;

    $query = "UPDATE users " .
                    'SET login = "' . $new_login . '", ' .
                    '`pass` = PASSWORD("' . $new_pass . '") ' .
                    'WHERE id = '. (int)$user_id;

    $result = db()->query($query);

    if ($result == FALSE)
        return ESQL;

    return 0;
}

?>