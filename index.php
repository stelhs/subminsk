	<?php

/* общие библиотеки */
require_once "private/common/debug.php";
require_once "private/common/strontium_tpl.php";
require_once "private/common/base_sql.php";
require_once "private/common/common.php";
require_once "private/common/message_box.php";
require_once "private/common/auth_adm.php";
require_once "private/transactions.php";


/* файлы различных сущностей */
require_once "private/articles.php";
require_once "private/users.php";

/* режимы страниц */
require_once "private/mods/m_adm_articles.php";
require_once "private/mods/m_articles.php";
require_once "private/mods/m_list_transactions.php";
require_once "private/mods/m_adm_login.php";

/* начальная инициализация системы */
require_once "private/init.php";

session_start();
 

/* Выбор режима работы */
$mod = "list_transactions";
if(isset($_GET['mod']))
   $mod = $_GET['mod'];


/* Попытка запуска административных режимов работы */
$mod_content = '';
if (auth_get_admin())
    switch ($mod) {
        case 'adm_articles':
            $mod_content = m_adm_articles($_GET);
            break;
            
        case 'list_transactions':
            $mod_content = m_list_transactions($_GET);
            break;
        default:
        	$mod_content = m_articles();
        
    }

/* Попытка запуска публичных режимов работы */
switch ($mod) {
	case 'adm_login':
		if (auth_get_admin())
		    break;
		else
            $mod_content = m_adm_login($_GET);
        break;
    case 'articles':
        $mod_content = m_articles($_GET);
        break;
}

/* Если введен некорректный mode то вывод статьи по умолчанию */
if (!$mod_content)
      $mod_content = m_articles();

/* Заполнение главного шаблона */
$tpl = new strontium_tpl("private/tpl/skeleton.html", $global_marks, false);
$tpl->assign(NULL, array('title' => page_get_title(),
                         'mod_content' => $mod_content,
                         ));  
                                                
/* Вывод всплывающего сообщения, если нужно */
$win = message_box_check_for_display();
if($win)
   $tpl->assign($win['block'], $win['data']);
   
/* Вывод меню администратора если автозирован */   
$admin_id = auth_get_admin();
if($admin_id) {
    $tpl->assign("admin_menu");   
    $tpl->assign("admin_greeting", user_get_by_id($admin_id));
} else
    $tpl->assign("public_menu");   


echo $tpl->result();

?>