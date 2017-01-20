<?php
/* Универсальный обработчик входящих запросов от различных форм */

require_once "private/common/debug.php";
require_once "private/common/message_box.php";
require_once "private/common/auth_adm.php";
require_once "private/articles.php";
require_once "private/users.php";
require_once "private/transactions.php";
require_once "private/init.php";

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
            header('Location: index.php?mod=adm_articles');
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
            header('Location: index.php?mod=adm_articles');
            break;

        /* Добавление транзакции */
        case "transaction_add":
            $admin = auth_get_admin();
        	if(!$admin)
                continue;

            $sum = (int)$_POST['sum'];
            $sum_usd = (int)$_POST['sum_usd'];
            $payer_id = (int)$_POST['payer_id'];
            $by_debtor_id = (int)$_POST['by_debtor_id'];
            $reason = addslashes($_POST['reason']);
            $date = $_POST['date'];

            if (($sum > 0 && $sum_usd < 0) || ($sum < 0 && $sum_usd > 0)) {
                message_box_display("message_einval");
                header('Location: index.php?mod=list_transactions');
                break;
            }

            $positive = 0;
            if ($sum && $sum > 0)
                $positive = 1;

            if ($sum_usd && $sum_usd > 0)
                $positive = 1;

            /* Underminsk can`t make contribution */
            if ($positive && $payer_id == 0) {
                message_box_display("message_einval");
                header('Location: index.php?mod=list_transactions');
                break;
            }

            $transaction_id = transactions_insert(array('sum' => $sum,
                                                        'sum_usd' => $sum_usd,
                                                        'payer_id' => $payer_id,
                                                        'reason' => $reason,
                                                        'author_id' => $admin,
                                                        'date' => $date));

            if ($transaction_id < 0) {
                message_box_display("message_esql");
                header('Location: index.php?mod=list_transactions');
                break;
            }

            /* Attempt to release Underminsk debt */
            $debt_sum = $sum;
            $debt_sum_usd = $sum_usd;

            /* Search for minimal debt in RUB */
            $debts = debts_get_list('repaid = 0 AND who_id = 0 AND sum > 0 AND whom_id = ' . $admin);
            if (is_array($debts) && count($debts)) {
                foreach ($debts as $debt) {
                    if (($debt['sum'] > 0) && ($debt['sum'] <= $debt_sum)) {
                        $debt_sum -= $debt['sum'];
                        dump('debt_remove');
                        debt_remove($debt['id']);
                        if ($debt_sum == 0)
                            break;
                    }
                }

                $debts = debts_get_list('repaid = 0 AND who_id = 0 AND sum > 0 AND whom_id = ' . $admin);
                if ($debt_sum) {
                    foreach ($debts as $debt) {
                        debt_change_sum($debt['id'], $debt['sum'] - $debt_sum, 0);
                        break;
                    }
                }
            }

            /* Search for minimal debt in USD */
            $debts = debts_get_list('repaid = 0 AND who_id = 0 AND sum_usd > 0 AND whom_id = ' . $admin);
            if (is_array($debts) && count($debts)) {
                /* Search for minimal debt in USD */
                foreach ($debts as $debt) {
                    if (($debt['sum_usd'] > 0) && ($debt['sum_usd'] <= $debt_sum_usd)) {
                        $debt_sum_usd -= $debt['sum_usd'];
                        debt_remove($debt['id']);
                        if ($debt_sum_usd == 0)
                            break;
                    }
                }

                $debts = debts_get_list('repaid = 0 AND who_id = 0 AND sum_usd > 0 AND whom_id = ' . $admin);
                if ($debt_sum_usd) {
                    foreach ($debts as $debt) {
                        dump($debt);
                        debt_change_sum($debt['id'], 0, $debt['sum_usd'] - $debt_sum_usd);
                        break;
                    }
                }
            }

            /* Add debt if needed */
            if ($by_debtor_id && $positive) {
                $debt_id = debts_insert(array('who_id' => $by_debtor_id,
                                              'whom_id' => $payer_id,
                                              'sum' => $sum,
                                              'sum_usd' => $sum_usd,
                                              'reason' => $reason,
                                              'date' => $date));
                if ($debt_id < 0) {
                    message_box_display("message_esql");
                    header('Location: index.php?mod=list_transactions');
                    break;
                }
            }

            message_box_display("message_transaction_success", array('id' => $transaction_id));
            header('Location: index.php?mod=list_transactions');
            break;

        case "pledged_add":
            $users = users_get_list();
            $admin = auth_get_admin();
            if(!$admin)
                continue;

            $sum = (int)$_POST['sum'];
            $sum_usd = (int)$_POST['sum_usd'];
            $author_id = (int)$_POST['author_id'];
            $reason = addslashes($_POST['reason']);

            if (($sum <= 0 && $sum_usd <= 0) || !$author_id) {
                message_box_display("message_einval");
                header('Location: index.php?mod=list_transactions');
                break;
            }

            $pledged_id = pledged_insert(array('author_id' => $author_id,
                                               'sum' => $sum,
                                               'sum_usd' => $sum_usd,
                                               'reason' => $reason));

            foreach ($users as $user) {
                 $debt_id = debts_insert(array('who_id' => 0,
                                               'whom_id' => $user['id'],
                                               'sum' => $sum,
                                               'sum_usd' => $sum_usd,
                                               'reason' => $reason,
                                               'date' => date('Y-m-d')));
            }

            message_box_display("message_pledged_success", array('id' => $pledged_id));
            header('Location: index.php?mod=list_transactions');
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
                header( 'Location: index.php?mod=adm_login');
            }
            break;

            /* Выбор списка продуктов по категории */
        case "get_category":
        	if(!isset($_POST["category_name"]))
        	   $cat_id = 1;
        	else
        	$cat_id = $_POST["category_name"];
        	header('Location: index.php?mod=adm_products&mode=list_products&cat_id='.$cat_id);
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
            header('Location: index.php?mod=adm_articles');
            break;

        /* Cписание долга */
        case "del_debt":
            $admin_id = auth_get_admin();
            $users = users_get_list();
            if(!$admin_id)
                continue;

            $user = $users[$admin_id];

            $debt_id = (int)$_GET['id'];
            $debt = debt_get_by_id($debt_id);
            if ($debt < 0 || !is_array($debt) || $admin_id != $debt['who_id']) {
                message_box_display("message_esql");
                header('Location: index.php?mod=list_transactions');
                break;
            }

            $rc = debt_remove($debt_id);
            if ($rc) {
                message_box_display("message_esql");
                header('Location: index.php?mod=list_transactions');
                break;
            }

            message_box_display('message_debt_removed', array('name' => $users[$debt['whom_id']]['name'],
                                                              'id' => $debt_id));
            header('Location: index.php?mod=list_transactions');
            break;

        /* Выход из режима администратора сайта */
        case "adm_logout":
        	auth_adm_remove();
        	header('Location: index.php');
        	break;
        }
?>