<?php
/*  код обслуживающий статьи */

function m_sallies($argv = array())
{
    $tpl = new strontium_tpl("private/tpl/m_sallies.html", 
                             global_conf()['global_marks'], false);
    $admin_id = auth_get_admin();
    $users = users_get_list();
    $sally_id = (int)$argv['id'];

    if (!$sally_id) {
        /* Display sallies */
        $sallies = sallies_get_list();

        if (is_array($sallies) && count($sallies)) {
            $tpl->assign("list_sallies");
            $row_count = 0;
            foreach ($sallies as $sally) {
                $sally['author'] = $users[$sally['author_id']]['name'];
                $sally['link_to_detail'] = mk_url(array('mod' => 'sallies', 
                                                        'id' => $sally['id']));
                $tpl->assign('sally', $sally);
            }
        }

        $user = $users[$admin_id];
        if ($user['role_manager'] == 1) {
            $tpl->assign("sally_add_edit", array('post_query' => 'sally_add'));
            $tpl->assign("sally_add_button");
            $tpl->assign("sally_add_new_header");
        }
        page_set_title("list sallies");
        return $tpl->result();
    }

    $sally = sally_get_by_id($sally_id);
    $sally['post_query'] = 'sally_edit';
    $tpl->assign("sally_add_edit", $sally);
    $tpl->assign("sally_edit_button");
    $tpl->assign("sally_edit_header");
    
    page_set_title("list sallies");
    return $tpl->result();
}

?>