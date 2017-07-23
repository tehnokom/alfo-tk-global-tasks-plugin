<?php

define('TKGT_STYLE_ROOT', plugin_dir_path(__FILE__));
define('TKGT_STYLE_URL', plugin_dir_url(__FILE__));

wp_register_style('tkgt-page-css', TKGT_STYLE_URL . 'css/page.css');
wp_register_script('tkgt-page-js', TKGT_STYLE_URL . 'js/page.js', array('jquery'));

wp_enqueue_style('tkgt-page-css');
wp_enqueue_script('tkgt-page-js');

get_header();
?>
    <div class="s_tasks">

        <div class="s_tasks__item">
            <button class="s_tasks__item__title">Получение общественной поддержки</button>
            <div class="s_tasks__item__content">
                <div class="s_tasks__tree">
                    <div class="s_tasks__tree__item">
                        <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Написать цель проекта</a>
                        <div class="s_tasks__tree__item__sub">
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Подзадача задачи<i></i></a>
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Вторая подзадача<i></i></a>
                        </div>
                        <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">
                            <span>Написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="s_tasks__item open">
            <button class="s_tasks__item__title">Создание проекта</button>
            <div class="s_tasks__item__content">
                <div class="s_tasks__tree">
                    <div class="s_tasks__tree__item">
                        <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Написать цель проекта</a>
                        <div class="s_tasks__tree__item__sub">
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Подзадача задачи<i></i></a>
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Вторая подзадача<i></i></a>
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Вторая подзадача<i></i></a>
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Вторая подзадача<i></i></a>
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Вторая подзадача<i></i></a>
                        </div>
                        <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="s_tasks__item">
            <button class="s_tasks__item__title">Этап оформления проекта</button>
            <div class="s_tasks__item__content">
                <div class="s_tasks__tree">
                    <div class="s_tasks__tree__item">
                        <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Написать цель проекта</a>
                        <div class="s_tasks__tree__item__sub">
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Подзадача задачи<i></i></a>
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Вторая подзадача<i></i></a>
                        </div>
                        <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">
                            <span>Написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="s_tasks__item">
            <button class="s_tasks__item__title">Этап формирования команды</button>
            <div class="s_tasks__item__content">
                <div class="s_tasks__tree">
                    <div class="s_tasks__tree__item">
                        <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Написать цель проекта</a>
                        <div class="s_tasks__tree__item__sub">
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Подзадача задачи<i></i></a>
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Вторая подзадача<i></i></a>
                        </div>
                        <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">
                            <span>Написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="s_tasks__item">
            <button class="s_tasks__item__title">Второе направление</button>
            <div class="s_tasks__item__content">
                <div class="s_tasks__tree">
                    <div class="s_tasks__tree__item">
                        <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Написать цель проекта</a>
                        <div class="s_tasks__tree__item__sub">
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Подзадача задачи<i></i></a>
                            <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">Вторая подзадача<i></i></a>
                        </div>
                        <a href="#" class="s_tasks__tree__item__title" data-type="project" data-type="circle">
                            <span>Написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований написать описание проекта, которое будет соответствовать всему базовому перечню требований</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php
get_footer();