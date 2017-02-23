<?php

/* общие библиотеки */
require_once "private/common/debug.php";
require_once "private/common/strontium_tpl.php";
require_once "private/common/database.php";
require_once "private/common/common.php";
require_once "private/common/message_box.php";
require_once "private/common/auth_adm.php";
require_once "private/common/images.php";

/* файлы различных сущностей */
require_once "private/articles.php";
require_once "private/products.php";

/* начальная инициализация системы */
require_once "private/init.php";

session_start();
/* Выбор режима работы */
$mod = "articles";
if(isset($_GET['mod']))
    $mod = $_GET['mod'];


/* Попытка запуска административных режимов работы */
$mod_content = '';
if (auth_get_admin())
    switch ($mod) {
    case 'adm_articles':
        require_once "private/mods/m_adm_articles.php";
        $mod_content = m_adm_articles($_GET);
        break;
    case 'adm_products':
        require_once "private/mods/m_adm_products.php";
        $mod_content = m_adm_products($_GET);
        break;

    default:
        require_once "private/mods/m_articles.php";
       	$mod_content = m_articles($_GET);
    }

/* Попытка запуска публичных режимов работы */
switch ($mod) {
case 'adm_login':
    require_once "private/mods/m_adm_login.php";
    if (auth_get_admin())
        break;
    else
        $mod_content = m_adm_login($_GET);
    break;
case 'articles':
    require_once "private/mods/m_articles.php";
    $mod_content = m_articles($_GET);
    break;
case 'products':
    require_once "private/mods/m_products.php";
    $mod_content = m_products($_GET);
    break;
case 'product':
    require_once "private/mods/m_product.php";
    $mod_content = m_product($_GET);
    break;
}

/* Если введен некорректный mode то вывод статьи по умолчанию */
if (!$mod_content)
    $mod_content = m_articles($_GET);

/* Заполнение главного шаблона */
$tpl = new strontium_tpl("private/tpl/skeleton.html", global_conf()['global_marks'], false);
$tpl->assign(NULL, array('title' => page_get_title(),
                         'mod_content' => $mod_content,
));

/* Вывод всплывающего сообщения, если нужно */
$win = message_box_check_for_display();
if($win)
    $tpl->assign($win['block'], $win['data']);

/* Вывод меню администратора если автозирован */   
if(auth_get_admin())
    $tpl->assign("admin_menu");

echo $tpl->result();