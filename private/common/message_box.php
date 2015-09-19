<?php 
/*
 *функции для отображения всплывающего окна с сообщением 
 */




/**
 * @param $block название блока включения
 * @param $data массив данных
 */
function display_message_box($block, $data)
{
     $_SESSION['display_window'] = array('name' => $block, 'data' => $data);
}


/**
 * Возвращает массив ($block - название блока
 *                    $data - массив данных)
 */
function check_for_window()
{
    $block = $_SESSION['display_window']["name"];
    $data = $_SESSION['display_window']["data"];
    return array('block' => $block, 'data' => $data);
}
?>