<?php
/*  код обслуживающий статьи */

function m_list_transactions($argv = array())
{
    $tpl = new strontium_tpl("private/tpl/m_list_transactions.html",
                             global_conf()['global_marks'], false);
    $admin_id = auth_get_admin();
    $users = users_get_list();
    $space_user_id = users_get_space_user()['id'];
    $subminsk_user_id = users_get_subminsk_user()['id'];

    /* Display balances */
    $tpl->assign("list_balances");
    foreach($users as $user) {
        if ($user['type'] == 'space')
            continue;

        $balance = users_get_balance($admin_id, $user['id']);
        $tpl->assign("user_balance", ['user' => $user['name'],
                                      'balance' => $balance]);
    }

    /* Display moneys_in_stock */
    $budget_sum = transactions_calc_budget();
    $tpl->assign("moneys_in_stock", ['total' => $budget_sum]);
    if (!$budget_sum) {
        $tpl->assign("no_money");
        if ($row_count)
            $tpl->assign("moneys_was");
    }


    /* Display common info */
    $common_info = [];
    $common_info['budget'] = $budget_sum;
    $common_info['transmit_to_budget'] = transactions_calc_sum_to_subminsk();
    $common_info['transmit_to_space'] = transactions_calc_sum_to_space();
    $common_info['transmit_to_space_from_subminsk'] = transactions_calc_sum_transmit_to_space_from_subminsk();
    $common_info['transmit_from_space_to_subminsk'] = transactions_calc_sum_transmit_to_subminsk_from_space();
    $common_info['subminsk_sum_to_users'] = transactions_calc_sum_subminsk_to_users();
    $tpl->assign("common_info", $common_info);


    /* Display users info */
    foreach($users as $user) {
        if ($user['type'] != 'user')
            continue;
        $tpl->assign("user_info_row", ['user' => $user['name'],
                                       'subminsk_sum' => users_get_transmit_sum($user['id'], $subminsk_user_id),
                                       'space_sum' => users_get_transmit_sum($user['id'], $space_user_id),
                                       'sum_fee' => users_get_transmit_sum($subminsk_user_id, $user['id'])]);
    }


    /* Display transactions */
    $transactions = transactions_get_list();
    if (is_array($transactions) && count($transactions)) {
        $tpl->assign("list_transaction");
        $row_count = 0;
        foreach ($transactions as $transaction) {
            $tpl->assign("transaction");
            $row_block_name = "transaction_from_users_to_users";

            if ($transaction['src_id'] == $subminsk_user_id)
                $row_block_name = "transaction_from_subminsk";

            if ($transaction['dst_id'] == $subminsk_user_id)
                $row_block_name = "transaction_to_subminsk";

            if ($transaction['dst_id'] == $space_user_id)
                $row_block_name = "transaction_to_space";

            $transaction['src_user'] = $users[$transaction['src_id']]['name'];
            $transaction['dst_user'] = $users[$transaction['dst_id']]['name'];
            $transaction['author_user'] = $users[$transaction['author_id']]['name'];
            $tpl->assign($row_block_name, $transaction);
            $row_count ++;
        }
    }


    $user = $users[$admin_id];
    if ($user['role_manager'] == 1) {
        $tpl->assign("transaction_add");
        foreach ($users as $user) {
            $tpl->assign("select_src_id", $user);
            $tpl->assign("dst_user", $user);
        }
    }

    page_set_title("list transactions");
    return $tpl->result();
}

?>