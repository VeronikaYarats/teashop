	<?php

/* общие библиотеки */
require_once "private/common/debug.php";
require_once "private/common/strontium_tpl.php";
require_once "private/common/base_sql.php";
require_once "private/common/common.php";
require_once "private/common/message_box.php";
require_once "private/common/auth_adm.php";

/* файлы различных сущностей */
require_once "private/articles.php";
require_once "private/products.php";

/* режимы страниц */
require_once "private/mods/m_adm_products.php";
require_once "private/mods/m_adm_articles.php";
require_once "private/mods/m_articles.php";
require_once "private/mods/m_products.php";
require_once "private/mods/m_product.php";
require_once "private/mods/m_adm_login.php";
require_once "private/common/images.php";

/* начальная инициализация системы */
require_once "private/init.php";
require_once "private/users.php";
session_start();
//$user = get_user_by_login_pass('veronika', '12345');
//dump(get_user_by_id($user[0]['id']));
/* Выбор режима работы */
$mod = "articles";
if(isset($_GET['mod']))
   $mod = $_GET['mod'];


/* Попытка запуска административных режимов работы */
$mod_content = '';
if (auth_get_admin())
    switch ($mod) {
        case 'adm_articles':
            $mod_content = m_adm_articles($_GET);
            break;
        case 'adm_products':
            $mod_content = m_adm_products($_GET);
            break;
            
        default:
        	$mod_content = m_articles($_GET);
        
    }

/* Попытка запуска публичных режимов работы */
switch ($mod) {
	case 'adm_login':
		if (auth_get_admin())
		    break;
		else
            $mod_content = m_adm_login($_GET);
        break;
    case 'articles':
        $mod_content = m_articles($_GET);
        break;
    case 'products':
        $mod_content = m_products($_GET);
        break;
    case 'product':
        $mod_content = m_product($_GET);
        break;
}

/* Если введен некорректный mode то вывод статьи по умолчанию */
if (!$mod_content)
	$mod_content = m_articles();

/* Заполнение главного шаблона */
$tpl = new strontium_tpl("private/tpl/skeleton.html", $global_marks, false);
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
 
?>