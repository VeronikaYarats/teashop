<?php

/* общие библиотеки */
require_once "private/common/debug.php";
require_once "private/common/strontium_tpl.php";
require_once "private/common/database.php";
require_once "private/common/common.php";
require_once "private/common/message_box.php";
require_once "private/common/auth_adm.php";
require_once "private/common/images.php";
require_once "private/common/url.php";
/* файлы различных сущностей */
require_once "private/articles.php";
require_once "private/products.php";

/* начальная инициализация системы */
require_once "private/init.php";

session_start();

$clean_url = global_conf()['clean_url'];

if($clean_url) {
    $url = $_SERVER['REDIRECT_URL'];
    $arg_list = url_decode($url);
}
else
    $arg_list = $_GET;

    
/* Выбор режима работы */
$mod = "articles";
if(isset($arg_list['mod']))
    $mod = $arg_list['mod'];


/* Попытка запуска административных режимов работы */
$mod_content = '';
if (auth_get_admin())
    switch ($mod) {
    case 'adm_articles':
        require_once "private/mods/m_adm_articles.php";
        $mod_content = m_adm_articles($arg_list);
        break;
    case 'adm_products':
        require_once "private/mods/m_adm_products.php";
        $mod_content = m_adm_products($arg_list);
        break;

    default:
        require_once "private/mods/m_articles.php";
       	$mod_content = m_articles($arg_list);
       	break;
    }

/* Попытка запуска публичных режимов работы */
switch ($mod) {
case 'adm_login':
    require_once "private/mods/m_adm_login.php";
    if (auth_get_admin())
        break;
    else
        $mod_content = m_adm_login($arg_list);
    break;
case 'articles':
    require_once "private/mods/m_articles.php";
    $mod_content = m_articles($arg_list);
    break;
case 'products':
    require_once "private/mods/m_products.php";
    $mod_content = m_products($arg_list);
    break;
case 'product':
    require_once "private/mods/m_product.php";
    $mod_content = m_product($arg_list);
    break;
/*default:
    require_once "private/mods/404.php";
    $mod_content = not_found();
    break;*/
}

/* Если введен некорректный mode то вывод статьи по умолчанию */
if (!$mod_content) { 
    require_once "private/mods/m_articles.php";
    $mod_content = m_articles($arg_list);
    }

/* Заполнение главного шаблона */ 
$welcome_url = mk_url(array('mod' => 'articles', 'key' => 'welcome'));
$coffee_url = mk_url(array('mod' => 'products', 'cat_id' => '2'));
$contacts_url = mk_url(array('mod' => 'articles', 'key' => 'contacts')); 
$tea_url = mk_url(array('mod' => 'products', 'cat_id' => '1')   );

$tpl = new strontium_tpl("private/tpl/skeleton.html", 
                        global_conf()['global_marks'], false);
                        
$tpl->assign(NULL, array('title' => page_get_title(),
                        'mod_content' => $mod_content, 
                        'welcome_url' => $welcome_url,
                        'tea_url' => $tea_url,
                        'coffee_url' => $coffee_url,
                        'contacts_url' => $contacts_url));

/* Вывод всплывающего сообщения, если нужно */
$win = message_box_check_for_display();
if($win)
    $tpl->assign($win['block'], $win['data']);

/* Вывод меню администратора если автозирован */   
if(auth_get_admin()) {
    $adm_articles = mk_url(array('mod' => 'adm_articles'));
    $adm_products = mk_url(array('mod' => 'adm_products'));
    $adm_logout = mk_url(array('get_query' => 'adm_logout')); 
    
    $tpl->assign("admin_menu", array('adm_articles' => $adm_articles,
                                    'adm_products' => $adm_products,
                                    'adm_logout' => $adm_logout ));
}

echo $tpl->result();