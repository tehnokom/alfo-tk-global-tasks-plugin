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
$tkgt_task = null;

function tkgt_the_tasks($post_id = null)
{
    if (!isset($post_id)) {
        global $post;

        if (empty($post)) {
            return;
        }

        $post_id = $post->ID;
    }

    echo apply_filters('tkgt_tasks_list', '', $post_id);
}

function tkgt_the_menu()
{
    if (!isset($post_id)) {
        global $post;

        if (empty($post)) {
            return;
        }

        $post_id = $post->ID;
    }

    echo apply_filters('tkgt_menu', '');
}

function tkgt_before_header($data = null)
{
    if (!isset($data) || empty($data)) {
        $data = array('page', 'menu', 'breadcrumbs', 'single-page');
    }

    if (is_array($data)) {
        global $tkgt_core;
        $tkgt_core->regJS_CSS($data, 'js');
        $tkgt_core->regJS_CSS($data, 'css');
    }
}

function tkgt_tasks_page_permalink()
{
    global $tkgt_core;

    return $tkgt_core->getTaskPagePermalink();
}

function tkgt_tasks_fullpage_link($post_id = null, $task_id = null, $subpage = null)
{
    global $tkgt_core;

    return $tkgt_core->getTaskFullpageLink($post_id, $task_id, $subpage);
}

function tkgt_get_task_by_internal_id($internal_id)
{
    return false;
}

function tkgt_get_breadcrumbs()
{
    echo apply_filters('tkgt_breadcrumbs', '');
}

function tkgt_get_task_id_by_internal_id($internal_id)
{
    return TK_GTask::getTaskByInternalId($internal_id);
}

function tkgt_is_fullpage()
{
    return TK_GTaskPage::isFullPage();
}

function tkgt_is_single()
{
    return TK_GTaskPage::isSingleFullPage();
}

function tkgt_the_description()
{
    global $tkgt_task;

    if ($tkgt_task) {
        echo apply_filters('the_content', $tkgt_task->description);
    }
}

function tkgt_the_task_type()
{
    global $tkgt_task;
    $out = '';

    if ($tkgt_task) {
        switch (intval($tkgt_task->type)) {
            case 0:
                $out = __('Direction', 'tkgt');
                break;

            case 1:
                $out = __('Stage', 'tkgt');
                break;

            case 2:
                $out = __('Task', 'tkgt');
                break;

            default:
                break;
        }
    }

    echo $out;
}

function tkgt_the_parent_stage()
{
    $stage = tkgt_get_parent_stage();

    if ($stage) {
        $desc = $stage->description;

        if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
            $desc = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($desc);
        }

        echo $desc;
    } else {
        echo "---";
    }
}

function tkgt_the_parent_direction()
{
    $direction = tkgt_get_parent_direction();

    if ($direction) {
        $desc = $direction->description;

        if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
            $desc = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($desc);
        }

        echo $desc;
    } else {
        echo "---";
    }
}

function tkgt_get_parent_stage()
{
    global $tkgt_task;

    if ($tkgt_task) {
        $stage_id = $tkgt_task->getParentStage();

        if ($stage_id) {
            $stage = new TK_GTask($stage_id);

            if ($stage->isValid()) {
                return $stage;
            }
        }
    }

    return false;
}

function tkgt_get_parent_direction()
{
    global $tkgt_task;

    if ($tkgt_task) {
        $direction_id = $tkgt_task->getParentDirection();

        if ($direction_id) {
            $direction = new TK_GTask($direction_id);

            if ($direction->isValid()) {
                return $direction;
            }
        }
    }

    return false;
}

function tkgt_the_creation_date($format = 'd.m.Y')
{
    global $tkgt_task;

    if ($tkgt_task) {
        $date = date_create($tkgt_task->creation_date);
        echo date_format($date, $format);
    }
}

function tkgt_the_start_date($format = 'd.m.Y')
{
    global $tkgt_task;

    if ($tkgt_task && $tkgt_task->start_date) {
        $date = date_create($tkgt_task->start_date);
        echo date_format($date, $format);
    }
}

function tkgt_the_end_date($format = 'd.m.Y')
{
    global $tkgt_task;

    if ($tkgt_task && $tkgt_task->end_date) {
        $date = date_create($tkgt_task->end_date);
        echo date_format($date, $format);
    }
}

function tkgt_comments()
{
    echo __('No comments', 'tkgt');
}