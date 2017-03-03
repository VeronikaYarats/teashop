<?php

function m_404()
{
    $tpl = new strontium_tpl("private/tpl/404.html",
                            array(), false);
    $tpl->assign("not_found");
    return $tpl->result();
}