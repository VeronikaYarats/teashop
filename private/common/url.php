<?php

/* Функция парсит URL, откидывая пуcтые значения */
function url_parser($url)
{
    $url_params = explode('/', $url);
    $result = array_diff($url_params, array(''));

    return $result;  
}


/* Находим глубину htt_root_path */
function get_root_depth($http_root_path)
{
    $path = explode('/', $http_root_path);
    $result = array_diff($path, array(''));
    $depth = count($result);
    return $depth;
}


/* Функиця декодирует чистый url */
function url_decode($clean_url)
{
    $url_parsed = url_parser($clean_url);
    $depth = get_root_depth(global_conf()['http_root_path']);
    /* откидываем элементы массива на глубину htt_root_path*/
    array_splice($url_parsed, 0, $depth);
    $params = array();
    
    if(!$url_parsed) {
        $params['mod'] = 'article';
        $params['key'] = 'welcome';
    }
   
    switch($url_parsed[0]) {
    case 'contacts':
        $params['mod'] = 'article';
        $params['key'] = 'contacts';
        break;
        
    case 'products':
        $params['mod'] = 'products';
        switch($url_parsed[1]) {
        case 'tea':
           $params['cat_id'] = '1';
           break; 
        case 'coffee':
           $params['cat_id'] = '2';
           break;
           
        default:
            $params['mod'] = '404';
        }
        if($params['mod'] !== '404')
            if($url_parsed[2]) {
                $params['mod'] = 'product';
                $params['id'] = $url_parsed[2];
            }
        break;
        
    case 'admin':
        if(!$url_parsed[1])
            $params['mod'] = 'adm_login';
        else {
            switch ($url_parsed[1]) {
            case 'articles':
                $params['mod'] = 'adm_articles';
                break;
            case 'products':
                $params['mod'] = 'adm_products';
                break;
            default:
                $params['mod'] = '404';
            }  
        }
        
    case 'logout':
       $params['get_query'] = 'adm_logout';
       break;  
    
    default:
         $params['mod'] = '404';
      
    }
    //dump($params);
    return $params;
}


/* Функция кодирует входные параметры в чистый url */
function url_code($params)
{
    $clean_url = "/veroshop";
    foreach($params as $key => $value) {
        switch($key) {
        case 'mod':
            switch($value) {
            case 'products':
            case 'product':
                $clean_url .= '/products';
                break;
            case 'articles':
                $clean_url .= '/';
                break;
            case 'adm_login':
                $clean_url .= '/admin';
                break;
            case 'adm_products':
                $clean_url .= '/admin/products';
                break;
            case 'adm_articles':
                $clean_url .= '/admin/articles';
                break;    
            }
            
            break;
        
            
        case 'cat_id':
            switch($value) {
            case '1':
                $clean_url .= '/tea';
                break;
            case '2':
                $clean_url .= '/coffee';
                break;
            }
            break;
            
        case 'id': 
            $clean_url .= '/' . $value; 
            break; 
            
        case 'key':
            switch($value) {
            case 'welcome':
                break;
            case 'contacts':
                $clean_url .= 'contacts';
                break;
            }
            
        case 'get_query':
                switch($value) {
                case 'adm_logout':
                    $clean_url .= '/logout';
                    break;
                }
                break;
        }
    }
    return $clean_url;
}

/* Функция для формирования URL в зависимости от значения настройки */
function mk_url($params)
{
    $clean_url = global_conf()['clean_url'];
    if($clean_url)
        $url = url_code($params);
    else {  
        if(array_key_exists('get_query', $params))
            $url ="exchange.php?";
        else
            $url ="index.php?";
        
        $separator = "";
        foreach($params as $key => $value) {
            $url .= $separator;
            $url .= $key . "=" .$value;
            $separator = "&";   
        }
    }
    return $url;
}



