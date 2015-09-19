<?php
require_once "exchange.php";
// общие библиотеке
require_once "private/common/debug.php";
require_once "private/common/strontium_tpl.php";
require_once "private/common/base_sql.php";
require_once "private/common/common.php";

//файлы сущностей
require_once "private/articles.php";
require_once "private/products.php";

//режимы страниц
require_once "private/mods/m_adm_products.php";
require_once "private/mods/m_adm_articles.php";
require_once "private/mods/m_articles.php";
require_once "private/mods/m_products.php";

//инициализация бд
require_once "private/init.php";


session_start();  
       
$mod = "main";
$tpl = new strontium_tpl("private/tpl/skeleton.html", $global_marks, false);

$tpl->assign();

$win = check_for_window();
if($win)
    $tpl->assign($win['block'], $win['data']);  

if(isset($_GET['mod']))
    $mod = $_GET['mod'];
switch ($mod) {
   case 'adm_articles':
		$tpl->assign("", array("page_content" => m_adm_articles()));
        break;
}   
$tpl->assign("admin_menu");

echo $tpl->result();
unset($_SESSION['display_window']);

?>