<?php
/* Мод обслуживающий авторизацию */

function m_adm_login()
{
    page_set_title("Авторизация");
    $tpl = new strontium_tpl("private/tpl/m_login.html",
                              array(), false);
    $tpl->assign("adm_login");
    return $tpl->result();
}