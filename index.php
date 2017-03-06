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
/* начальная инициализация системы */
require_once "private/init.php";
/* файлы различных сущностей */
require_once "private/clean_url.php";
require_once "private/site_menu.php";
require_once "private/articles.php";
require_once "private/products.php";

session_start();

$clean_url_enable = global_conf()['clean_url_enable'];
if($clean_url_enable) {
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

    case '404':
        require_once "private/mods/m_404.php";
       	$mod_content = m_404();
       	break;
    }

/* Попытка запуска публичных режимов работы */
switch ($mod) {
case 'adm_login':
    if (auth_get_admin()) {
        require_once "private/mods/m_adm_products.php";
        $mod_content = m_adm_products($arg_list);
    }
    else {
        require_once "private/mods/m_adm_login.php";
        $mod_content = m_adm_login($arg_list);
    }
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
case '404':
    require_once "private/mods/m_404.php";
    $mod_content = m_404();
    break;
}

/* Если введен некорректный mode то вывод 404 */
if (!$mod_content) { 
    require_once "private/mods/m_404.php";
    $mod_content = m_404();
    }

/* Заполнение главного шаблона */ 
$tpl = new strontium_tpl("private/tpl/skeleton.html", 
                        global_conf()['global_marks'], false);
$tpl->assign(NULL, array('title' => page_get_title(),
                         'mod_content' => $mod_content));

foreach($site_menu as $menu_item){
    if(auth_get_admin())
        $tpl->assign("menu", $menu_item);
    else
        if(!$menu_item['adm'])
            $tpl->assign("menu", $menu_item);
}
/* Вывод всплывающего сообщения, если нужно */
$win = message_box_check_for_display();
if($win) 
    $tpl->assign($win['block'], $win['data']);
   
echo $tpl->result();