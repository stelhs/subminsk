<?php
/*  код обслуживающий статьи */

function m_list_transactions($argv = array())
{
    $tpl = new strontium_tpl("private/tpl/m_list_transactions.html",
                             global_conf()['global_marks'], false);
    $admin_id = auth_get_admin();
    $users = users_get_list();

    /* Display my debts */
    $debts = debts_get_list('repaid = 0 AND whom_id = ' . $admin_id);
    if (is_array($debts) && count($debts)) {
        $tpl->assign("list_my_debts");
        foreach ($debts as $row) {
            $row['who'] = $users[$row['who_id']]['name'];
            if ($row['who_id'] == 0)
                $row['who'] = 'Underminsk';
            $tpl->assign('row_my_debt', $row);
        }
    }

    /* Display me debts */
    $debts = debts_get_list('repaid = 0 AND who_id = ' . $admin_id);
    if (is_array($debts) && count($debts)) {
        $tpl->assign("list_me_debts");
        foreach ($debts as $row) {
            $row['whom'] = $users[$row['whom_id']]['name'];
            if ($row['whom_id'] == 0)
                $row['whom'] = 'Underminsk';
            $tpl->assign('row_me_debt', $row);
        }
    }

    /* Display transactions */
    $transactions = transactions_get_list();
    if (is_array($transactions) && count($transactions)) {
        $tpl->assign("list_transaction");
        $row_count = 0;
        foreach ($transactions as $transaction) {
            $tpl->assign("transaction");
            $positive = 0;
            if ($transaction['sum'] && $transaction['sum'] > 0)
                $positive = 1;

            if ($transaction['sum_usd'] && $transaction['sum_usd'] > 0)
                $positive = 1;

            $positive ? $row_color = "transaction_increase" : $row_color = "transaction_decrease";

            $transaction['user'] = $users[$transaction['payer_id']]['name'];
            if ($transaction['payer_id'] == 0)
                $transaction['user'] = 'Underminsk';
            $transaction['author'] = $users[$transaction['author_id']]['name'];
            $tpl->assign($row_color, $transaction);
            $row_count ++;
        }
    }

    $sum_data = transactions_calc_sum();
    $tpl->assign("moneys_in_stock", array('total' => (float)$sum_data['total'], 'total_usd' => (float)$sum_data['total_usd']));

    if (!$sum_data['total'] && !$sum_data['total_usd']) {
        $tpl->assign("no_money");
        if ($row_count)
            $tpl->assign("moneys_was");
    }

    $user = $users[$admin_id];
    if ($user['role_manager'] == 1) {
        $tpl->assign("transaction_add");
        $tpl->assign("pledged_add", array('author_id' => $admin_id));
        $users = users_get_list();
        foreach ($users as $user) { 
            $tpl->assign("select_payer_id", $user);
            $tpl->assign("select_by_debtor_id", $user);
            $tpl->assign("pledged_except_user", $user);
        }
    }

    page_set_title("list transactions");
    return $tpl->result();
}

?>