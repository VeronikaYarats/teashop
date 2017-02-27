<?php

function not_found()
{
    $tpl = new strontium_tpl("private/tpl/404.html",
                            array(), false);
    $tpl->assign("not_found");
    return $tpl->result();
}