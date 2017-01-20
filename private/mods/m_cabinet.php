<?php
/*  код обслуживающий статьи */

function m_cabinet($argv = array())
{
    $tpl = new strontium_tpl("private/tpl/m_cabinet.html", array(), false);
    $admin_id = auth_get_admin();
    $users = users_get_list();
    $user = $users[$admin_id];

    $tpl->assign("change_password", array('login' => $user['login']));

    page_set_title("persobal cabinet");
    return $tpl->result();
}

?>