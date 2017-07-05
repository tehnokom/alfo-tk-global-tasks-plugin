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

require_once(TKGT_ROOT . 'lib/settings.php');
require_once(TKGT_ROOT . 'lib/task.php');
require_once(TKGT_ROOT . 'lib/tasks.php');
require_once(TKGT_ROOT . 'lib/ajax_functions.php');

function tkgt_admin_menu()
{
    add_menu_page( _x('TK Global Tasks', 'Admin menu', 'tkgt'),
        _x('Global Tasks', 'Admin menu', 'tkgt'),
        'manage_options',
        TKGT_ROOT . 'admin/tasks_list.php',
        '',
        '',
        "16.2");

    add_options_page(_x('Tasks Settings', 'Admin menu', 'tkgt'),
        _x('TK Tasks', 'Admin menu', 'tkgt'),
        'manage_options',
        TKGT_ROOT . 'admin/settings.php',
        '');
}
add_action('admin_menu', 'tkgt_admin_menu');

function tkgt_registry_styles()
{
    if(has_action('admin_enqueue_scripts')) {
        wp_register_style('tkgt-admin-settings-css', TKGT_URL . 'admin/css/settings.css');
        wp_enqueue_style('tkgt-admin-settings-css');
    } else {

    }
}
add_action('admin_enqueue_scripts', 'tkgt_registry_styles');
add_action('wp_enqueue_scripts', 'tkgt_registry_styles');

function tkgt_create_page()
{
    global $wp_rewrite;
    $slug = TK_GTask::taskSettings(true)->slug;
    //file_put_contents(__FILE__ . '.log',get_post_type(). "\r\n\r\n", FILE_APPEND);
    add_rewrite_tag('%tkgt_page%', '([^&]+)');
    add_rewrite_rule('^projektoj/([^/]+)' . $slug,
         'index.php?post_type=projektoj&name=$matches[1]&tkgt_page=' . $slug,
        'top');
    /*add_rewrite_rule('^([^/]+)/' . $slug . '$',
        'index.php?name=$matches[0]&tkgt_page=' . $slug,
        'top');*/

    $wp_rewrite->flush_rules();
}
add_action('init', 'tkgt_create_page');

function tkgt_template_redirect()
{
    global $wp, $wp_query, $post;
    //file_put_contents(__FILE__ . '.log',serialize($wp->query_vars). "\r\n\r\n", FILE_APPEND);
    if(!empty($wp->query_vars['tkgt_page']) && !empty($post)) {

    }
}
add_action('template_redirect', 'tkgt_template_redirect');

function tkgt_template_include($template_path)
{
    if(in_array(get_post_type(),TK_GTask::taskSettings()['enabled_for'])) {
        $template_path = TKGT_ROOT . 'styles/default/page.php';
    }

    return $template_path;
}
add_filter('template_include', 'tkgt_template_include');