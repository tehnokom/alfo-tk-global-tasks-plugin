<?php
/** This file is part of TKGlobalTasks Plugin project
 *
 * @desc Core
 * @package TKGlobalTasks
 * @version 0.1a
 * @author Ravil Sarvaritdinov <ra9oaj@gmail.com>
 * @copyright 2017 Tehnokom.su
 * @license http://opensource.org/licenses/GPL-3.0 GPLv3
 * @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
 * @link https://github.com/tehnokom/alfo-tk-global-tasks-plugin
 */

require_once(TKGT_ROOT . 'lib/taskpage.php');
require_once(TKGT_ROOT . 'lib/settings.php');
require_once(TKGT_ROOT . 'lib/task.php');
require_once(TKGT_ROOT . 'lib/tasks.php');
require_once(TKGT_ROOT . 'lib/ajax_functions.php');

global $post;

$tkgt_core = new TK_GTaskPage();

function tkgt_the_tasks($post_id = null)
{
    if(!isset($post_id)) {
        global $post;

        if(empty($post)) {
            return;
        }

        $post_id = $post->ID;
    }

    echo apply_filters('tkgt_tasks_list', '', $post_id);
}

function tkgt_the_menu($post_id = null)
{
    if(!isset($post_id)) {
        global $post;

        if(empty($post)) {
            return;
        }

        $post_id = $post->ID;
    }

    echo apply_filters('tkgt_menu', $post_id);
}

function tkgt_before_header($data = null)
{
    if(!isset($data) || empty($data)) {
        $data = array('page', 'menu');
    }

    if(is_array($data)) {
        global $tkgt_core;
        $tkgt_core->regJS_CSS($data, 'js');
        $tkgt_core->regJS_CSS($data, 'css');
    }
}