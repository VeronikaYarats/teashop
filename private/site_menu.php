<?php
$site_menu = array(array(   
                          'adm' => 0,
                          'name' => 'Главная',
                          'url' => mk_url(array('mod' => 'articles', 
                                                'key' => 'welcome'))
                        ),
                        array(
                          'adm' => 0,
                          'name' => 'Чай',
                          'url' => mk_url(array('mod' => 'products', 
                                            'cat_id' => '1'))
                        ),
                        array(
                          'adm' => 0,
                          'name' => 'Кофе',
                          'url' => mk_url(array('mod' => 'products', 
                                            'cat_id' => '2'))
                        ),
                        array(
                          'adm' => 0,
                          'name' => 'Контакты',
                          'url' => mk_url(array('mod' => 'articles', 
                                            'key' => 'contacts'))
                        ),
                        array(
                          'adm' => 1,
                          'name' => 'Статьи',
                          'url' => mk_url(array('mod' => 'adm_articles'))
                        ),
                         array(
                          'adm' => 1,
                          'name' => 'Продукты',
                          'url' => mk_url(array('mod' => 'adm_products'))
                        ),
                         array(
                          'adm' => 1,
                          'name' => 'Выход',
                          'url' => mk_url(array('get_query' => 'adm_logout'))
                        )
);                        