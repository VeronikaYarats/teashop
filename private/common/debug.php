<?php
/*
 * Набор инструментов для отладки
 */

function dump($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function dbg_err($data)
{
    dump("ERROR:\n");
    dump($data);
}

function dbg_warn($data)
{
    dump("WARNING:\n");
    dump($data);
}

function dbg_notice($data)
{
    dump("NOTICE:\n");
    dump($data);
}

?>