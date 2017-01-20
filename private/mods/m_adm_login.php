<?php
/* Мод обслуживающий авторизацию */

function m_adm_login($argv = array())
{
    $tpl = new strontium_tpl("private/tpl/m_login.html",
                             $global_marks, false); 
    $tpl->assign("adm_login");
    return $tpl->result();
}

?>
