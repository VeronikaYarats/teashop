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
        $clean_url .= $params['key'];
        break;

    case 'articles':
        switch($params['key']) {
        case 'welcome':
            break;
        case 'contacts':
            $clean_url .= 'contacts/';
            break;
        }
        break;
        
    case 'adm_login':
        $clean_url .= 'admin/';
        break;
        
    case 'adm_products':
        $clean_url .= 'admin/products/';
        if(!isset($params['mode']))
            return $clean_url;
        switch($params['mode']) {
        case 'edit_product':
            $clean_url .= 'edit/';
            if(!isset($params['key']))
                return $params;
            $clean_url .= $params['key'];
            break;
        case 'add_product':
            $clean_url .= 'add/';
            switch($params['cat_id']) {
            case '1':
                $clean_url .= 'tea/';
                break;  
            case '2':
                $clean_url .= 'coffee/';
                break;         
            }
            break; 
        case 'list_products':
            switch($params['cat_id']) {
            case '1':
                $clean_url .= 'tea/';
                break;  
            case '2':
                $clean_url .= 'coffee/';
                break; 
            }
        }
        break;
    
    case 'adm_articles':
        $clean_url .= 'admin/articles/';
        if(!isset($params['mode']))
            return $clean_url;
        switch($params['mode']) {
        case 'edit_article':
            $clean_url .= 'edit/';
            $clean_url .=  $params['id'];
            break;
        case 'add_article':
            $clean_url .= 'add/';
            break;
        }
        break;
    }
    return $clean_url; 
}

/* Функиця анализирует чистый url и возвращает входные параметры сайта */
function url_decode($clean_url)
{
    $rows = url_parser($clean_url);
    $depth = url_get_root_depth();
    /* откидываются элементы массива на глубину htt_root_path */
    array_splice($rows, 0, $depth);
    $params = array();
    
    if(!$rows) {
        $params['mod'] = 'articles';
        $params['key'] = 'welcome';
        return $params;
    }
   
    switch($rows[0]) {
    case 'contacts':
        $params['mod'] = 'articles';
        $params['key'] = 'contacts';
        break;
        
    case 'products':
        $params['mod'] = 'products';
        switch($rows[1]) {
        case 'tea':
           $params['cat_id'] = '1';
           break; 
        case 'coffee':
           $params['cat_id'] = '2';
           break;
           
        default:
            $params['mod'] = '404';
            return $params;
        }
        if(isset($rows[2])) {
            $params['mod'] = 'product';
            $params['key'] = $rows[2];
            }
        break;
        
    case 'admin':
        if(!isset($rows[1])) {
            $params['mod'] = 'adm_login';
            return $params;   
        }
        switch ($rows[1]) {
        case 'articles':
            $params['mod'] = 'adm_articles';
            if(!isset($rows[2]))
                return $params;
                
            switch ($rows[2]) {
            case 'edit':
                $params['mode'] = 'edit_article';
                $params['id'] = $rows[3];
                break;
            case 'add':
                $params['mode'] = 'add_article';
                break;
            default:
                $params['mod'] = '404'; 
            }
            break;
        case 'products':
            $params['mod'] = 'adm_products';
            if(!isset($rows[2]))
                return $params;
                
            switch ($rows[2]) {
            case 'edit':
                $params['mode'] = 'edit_product';
                $params['key'] = $rows[3];
                break;
                
            case 'add':
                $params['mode'] = 'add_product';
                switch ($rows[3]) {
                case 'tea':
                    $params['cat_id'] = '1';
                    break;
                case 'coffee':
                    $params['cat_id'] = '2'; 
                    break;  
                default:
                    $params['mod'] = '404';
                }
                break;
            
            case 'tea':
                $params['cat_id'] = '1';
                break;
            case 'coffee':
                $params['cat_id'] = '2'; 
                break; 
            default:
                $params['mod'] = '404';
            break;
            } 
            break;
        default: 
            $params['mod'] = '404';
        }
    break;
    default:
        $params['mod'] = '404';
    }
    return $params;
}


/* Функция для формирования URL в зависимости от значения настройки */
function mk_url($params)
{
    $query = (array_key_exists('get_query', $params) || 
                array_key_exists('post_query', $params));
    $clean_url_enable = global_conf()['clean_url_enable'];
    
    if(!$query)
        if($clean_url_enable)
            return url_encode($params);
        
    $url = global_conf()['http_root_path'];
    if($query)    
        $url .= "exchange.php?";
    else
        $url .= "index.php?";
    
    $separator = "";
    foreach($params as $key => $value) {
        $url .= $separator;
        $url .= $key . "=" .$value;
        $separator = "&";   
    }
    return $url;
}
