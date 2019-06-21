<?php
/* Универсальный обработчик входящих запросов от различных форм */

require_once "private/common/debug.php";
require_once "private/common/message_box.php";
require_once "private/common/auth_adm.php";
require_once "private/common/database.php";
require_once "private/common/url.php";
require_once "private/articles.php";
require_once "private/users.php";
require_once "private/transactions.php";
require_once "private/sallies.php";
require_once "private/init.php";
require_once "private/clean_url.php";
require_once '/usr/local/lib/php/common.php';

session_start();

/* Обработчик POST запросов */
if(isset($_POST['post_query']))
    switch ($_POST['post_query']) {

        /* Редактирование статьи */
        case "article_edit":
            $id = $_POST['article_id'];
            if(!auth_get_admin())
                continue;
            $public = isset($_POST['public']);

            $array = $_POST;
            $array["public"] = $public;
            $err = article_edit($id, $array);
            switch ($err) {
            	case 0:
            		$block = "message_article_success_edit";
                    $data = array('article_id' => $id);
                    break;

            	case EINVAL:
            		$block = "message_einval";
            		break;

            	case ESQL:
            		$block = "message_esql";
            		break;
            }
            message_box_display($block, $data);
            header('Location: ' . mk_url(array('mod' => 'adm_articles')));
            break;

        /* Добавление новой статьи */
        case "article_add":
        	if(!auth_get_admin())
                continue;
            $data = $_POST;
            $public = isset($_POST['public']);
            $data["public"] = $public;
            $article_id = article_add_new($data);
            switch ($article_id) {
            	case EINVAL:
                    $block = "message_einval";
                    break;

                case ESQL:
                    $block = "message_esql";
                    break;

                default:
                    $block = "message_article_success_add_new";
                    $data = array('article_id' => $article_id);
                    break;
            }
            message_box_display($block, $data);
            header('Location: ' . mk_url(array('mod' => 'adm_articles')));
            break;

        /* Добавление транзакции */
        case "transaction_add":
            $admin = auth_get_admin();
        	if(!$admin)
                continue;

            $sum = (float)$_POST['sum'];
            $src_user_id = (int)$_POST['src_user_id'];
            $dst_users_id = $_POST['dst_users_id'];
            $reason = addslashes($_POST['reason']);
            $date = $_POST['date'];

            if (!$src_user_id) {
                message_box_display("message_einval");
                header('Location: ' . mk_url(array('mod' => 'list_transactions')));
                break;
            }

            if (!count($dst_users_id)) {
                message_box_display("message_einval");
                header('Location: ' . mk_url(array('mod' => 'list_transactions')));
                break;
            }

            if ($sum < 0) {
                message_box_display("message_einval");
                header('Location: ' . mk_url(array('mod' => 'list_transactions')));
                break;
            }

            $list_new_transactions = [];
            foreach ($dst_users_id as $dst_user_id => $val) {
                $transaction_id = transactions_insert(array('sum' => $sum,
                                                            'src_id' => $src_user_id,
                                                            'dst_id' => $dst_user_id,
                                                            'reason' => $reason,
                                                            'author_id' => $admin,
                                                            'date' => $date));
                if ($transaction_id < 0) {
                    message_box_display("message_esql");
                    header('Location: ' . mk_url(array('mod' => 'list_transactions')));
                    break;
                }
                $list_new_transactions[] = $transaction_id;
            }

            message_box_display("message_transaction_success", array('list_id' => array_to_string($list_new_transactions)));
            header('Location: ' . mk_url(array('mod' => 'list_transactions')));
            break;


        case "sally_add":
            $admin = auth_get_admin();
            if(!$admin)
                continue;

            $reason = addslashes($_POST['reason']);
            $description = addslashes($_POST['description']);
            $need_to_take = addslashes($_POST['need_to_take']);
            $date = $_POST['date'];


            $sally_id = sally_insert(array('reason' => $reason,
                                           'description' => $description,
                                           'need_to_take' => $need_to_take,
                                           'author_id' => $admin,
                                           'date' => $date));

            if ($sally_id < 0) {
                message_box_display("message_esql");
                header('Location: index.php?mod=list_sallies');
                break;
            }

            message_box_display("message_sally_add_success", array('id' => $sally_id));
            header('Location: ' . mk_url(array('mod' => 'sallies', 'id' => $sally_id)));
            break;

        case "sally_edit":
            $admin = auth_get_admin();
            if(!$admin)
                continue;

            $reason = addslashes($_POST['reason']);
            $description = addslashes($_POST['description']);
            $need_to_take = addslashes($_POST['need_to_take']);
            $date = $_POST['date'];
            $sally_id = (int)$_POST['sally_id'];

            sally_update($sally_id, array('reason' => $reason,
                                          'description' => $description,
                                          'need_to_take' => $need_to_take,
                                          'author_id' => $admin,
                                          'date' => $date));

            message_box_display("message_sally_edit_success", array('id' => $sally_id));
            header('Location: ' . mk_url(array('mod' => 'sallies', 'id' => $sally_id)));
            break;


        /* Авторизация администратора сайта */
        case "adm_login":
            $user = user_get_by_pass($_POST['name'], $_POST['password']);
        	if(isset($user['id'])) {
         	    auth_store_admin($user['id']);
                header( 'Location: index.php');
            }
            else {
                message_box_display("message_adm_login_incorrect");
                header('Location: ' . mk_url(array('mod' => 'adm_login')));
            }
            break;

        case "change_pass":
            $admin = auth_get_admin();
            if(!$admin)
                continue;

            $new_login = addslashes($_POST['login']);
            $new_pass = addslashes($_POST['new_pass']);
            $new_pass_repeate = addslashes($_POST['new_pass_repeate']);
            if (!$new_pass || !$new_login || ($new_pass != $new_pass_repeate)) {
                message_box_display("message_einval");
                header('Location: ' . mk_url(array('mod' => 'cabinet')));
                break;
            }

            $rc = user_change_pass($admin, $new_login, $new_pass);
            if ($rc < 0) {
                    message_box_display("message_esql");
                    header('Location: ' . mk_url(array('mod' => 'cabinet')));
                    break;
            }

            message_box_display("message_change_pass_success");
            header('Location: ' . mk_url(array('mod' => 'cabinet')));
            break;
   }

/* Обработчик GET запросов */
if(isset($_GET['get_query']))
    switch ($_GET['get_query']) {

        /* Удаление статьи */
        case "del_article":
        	if(!auth_get_admin())
                continue;
            $id = $_GET['article_id'];
            $err = article_del($id);
            switch ($err) {
            	case 0:
            		$block = "message_article_success_del";
                    $data = array('article_id' => $id);
                    break;

                case EINVAL:
                    $block = "message_einval";
                    break;

                case ESQL:
                    $block = "message_esql";
                    break;
            }
            message_box_display($block, $data);
            header('Location: ' . mk_url(array('mod' => 'adm_articles')));
            break;


        /* Выход из режима администратора сайта */
        case "adm_logout":
        	auth_adm_remove();
        	header('Location: index.php');
        	break;
        }
?>
