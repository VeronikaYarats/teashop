<?php
/* Функции для работы с пользователями */

/* Возвращает информацию о пользователе по id */
function user_get_by_id($id)
{
    $query = "SELECT * FROM users WHERE id = " . $id;
    return db()->query($query);
}
/* Возвращает информацию о пользователе если совпадают логин и пароль */
function user_get_by_pass($login, $pass)
{
    $query = "SELECT * FROM users WHERE `login` = '" . $login .
                "' AND `pass` = '" . $pass ."'";
    return db()->query($query);
}