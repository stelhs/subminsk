<?php

/* Функция кодирует входные параметры в чистый url */
function url_encode($params)
{
    $clean_url = global_conf()['http_root_path'];

    switch ($params['mod']) {
    
    case 'products':
    case 'product':
        $clean_url .= 'products/';
        switch ($params['cat_id']) {
        case '1':
            $clean_url .= 'tea/';
            break;
        case '2':
           $clean_url .= 'coffee/';
            break; 
        }
        if(!isset($params['key']))
            return $clean_url;
            
        return $clean_url . $params['key'];
        
    case 'articles':
        switch($params['key']) {
        case 'welcome':
            return $clean_url;
        case 'contacts':
            return $clean_url . 'contacts/';
        }
        
    case 'adm_login':
        dump($clean_url =  $clean_url . 'admin/');
        break;
    case 'adm_products':
        $clean_url .= 'admin/products/';
        if (!isset($params['mode']))
            return $clean_url;
            
        switch ($params['mode']) {
        case 'edit_product':
            $clean_url .= 'edit/';
            if (!isset($params['key']))
                return $clean_url;
                
            return  $clean_url . $params['key'];
            
        case 'add_product':
            switch ($params['cat_id']) {
            case '1':
                return $clean_url . 'add/tea/';  
            case '2':
                return $clean_url . 'add/coffee/';       
            }
            
        case 'list_products':
            switch ($params['cat_id']) {
            case '1':
                return $clean_url . 'tea/';  
            case '2':
                return $clean_url . 'coffee/';
            }
        }
    
    case 'adm_articles':
        $clean_url .= 'admin/articles/';
        if (!isset($params['mode']))
            return $clean_url;
            
        switch ($params['mode']) {
        case 'edit_article':
            return $clean_url . 'edit/' . $params['id'];
        case 'add_article':
            return $clean_url . 'add/';
        }
    }
    return $clean_url;
}

/* Функиця анализирует чистый url и возвращает входные параметры сайта */
function url_decode($clean_url)
{
    $rows = url_parser($clean_url);
    $depth = url_get_root_depth();
    /* откидывается часть урла до корня сайта */
    array_splice($rows, 0, $depth);
    
    if (!$rows) {
        return array('mod' => 'articles', 
                    'key' => 'welcome');
    }
   
    switch ($rows[0]) {
    case 'contacts':
        return array('mod' => 'articles', 
                    'key' => 'contacts');
        
    case 'products':
        switch ($rows[1]) {
        case 'tea':
            if (isset($rows[2]))
                return array('mod' => 'product',
                            'cat_id' => '1',
                            'key' => $rows[2]);
                
            return array('mod' => 'products',
                        'cat_id' => '1');
        case 'coffee':
            if (isset($rows[2]))
                return array('mod' => 'product',
                        'cat_id' => '2',
                        'key' => $rows[2]);
                
            return array('mod' => 'products',
                        'cat_id' => '2');
        default:
            return array('mod' => '404');   
        }
        
    case 'admin':
        if (!isset($rows[1])) 
            return array('mod' => 'adm_login');   
       
        switch ($rows[1]) {
        case 'articles':
            if (!isset($rows[2]))
                return array('mod' =>'adm_articles');
                
            switch ($rows[2]) {
            case 'edit':
                return array('mod' => 'adm_articles',
                            'mode' => 'edit_article',
                            'id' => $rows[3]);
               
            case 'add':
                return array('mod' => 'adm_articles',
                            'mode' => 'add_article');
                
            default:
                return array('mod' => '404'); 
            }
            
        case 'products':
            if (!isset($rows[2]))
                return array('mod' => 'adm_products');
                
            switch ($rows[2]) {
            case 'edit':
                return array('mod' => 'adm_products',
                            'mode' => 'edit_product',
                            'key' => $rows[3]);
                
            case 'add':
                switch ($rows[3]) {
                case 'tea':
                    return array('mod' => 'adm_products',
                                'mode' => 'add_product',
                                'cat_id' => '1');
                case 'coffee':
                    return array('mod' => 'adm_products',
                                'mode' => 'add_product',
                                'cat_id' => '2');  
                default:
                    return array('mod' => '404');
                }
                
            case 'tea':
                return array('mod' => 'adm_products',
                            'cat_id' => '1');
                
            case 'coffee':
                return array('mod' => 'adm_products',
                            'cat_id' => '2');
                  
            default:
                return array('mod' => '404');
            }
            
        default: 
            return array('mod' => '404');
        }
        
    default:
        return array('mod' => '404');
    }
}


/* Функция для формирования URL в зависимости от значения настройки */
function mk_url($params)
{
    $query = (isset($params['get_query']) || 
                isset($params['post_query']));
    $clean_url_enable = global_conf()['clean_url_enable'];
    
    if (!$query && $clean_url_enable)
            return url_encode($params);
        
    $url = global_conf()['http_root_path'];
    if ($query)    
        $url .= "exchange.php?";
    else
        $url .= "index.php?";
        
    $separator = "";
    foreach ($params as $key => $value) {
        $url .= $separator;
        $url .= $key . "=" .$value;
        $separator = "&";   
    }
    return $url;
}
