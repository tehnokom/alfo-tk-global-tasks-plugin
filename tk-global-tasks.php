<?php
/*
* Plugin Name: TehnoKom Global Tasks
* Plugin URI: https://github.com/tehnokom/alfo-tk-global-tasks-plugin
* Text Domain: tkgt
* Description: Allows you to create a task plan for certain types of posts.
* Version: 0.1a
* Author: Ravil Sarvaritdinov <ra9oaj@gmail.com>
* Author URI: http://github.com/RA9OAJ/
* License: GPLv3
* Text Domain: tkgt
*/

define('TKGT_ROOT', plugin_dir_path(__FILE__));
define('TKGT_URL', plugin_dir_url(__FILE__));
define('TKGT_STYLES_DIR', TKGT_ROOT . 'styles/');
define('TKGT_STYLES_URL', TKGT_URL . 'styles/');

require_once(ABSPATH . 'wp-admin/includes/template.php');
require_once(TKGT_ROOT . 'lib/settings.php');
require_once(TKGT_ROOT . 'lib/task.php');
require_once(TKGT_ROOT . 'lib/tasks.php');
require_once(TKGT_ROOT . 'lib/ajax_functions.php');

function tkgt_registry_plugin()
{

}
add_action('activate_plugin', 'tkgt_registry_plugin');

function tkgt_unregistry_plugin()
{

}
add_action('deactivate_plugin', 'tkgt_unregistry_plugin');

function tkgt_init()
{
    load_plugin_textdomain('tkgt', false, dirname(plugin_basename(__FILE__)) . '/locales/');
    load_plugin_textdomain('tkgt-style', false, dirname(plugin_basename(__FILE__)) .  '/styles/default/locales/');

    tkgt_add_options();
}
add_action('init', 'tkgt_init');

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