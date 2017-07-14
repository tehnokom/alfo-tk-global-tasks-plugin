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

require_once(TKGT_ROOT . 'lib/db_init.php');
require_once(TKGT_ROOT . 'lib/core.php');

function tkgt_registry_plugin()
{
    tkgt_check_version();
}
add_action('activate_plugin', 'tkgt_registry_plugin');

function tkgt_unregistry_plugin()
{

}
add_action('deactivate_plugin', 'tkgt_unregistry_plugin');

function tkgt_init()
{
    tkgt_check_version();

    load_plugin_textdomain('tkgt', false, dirname(plugin_basename(__FILE__)) . '/locales/');
    load_plugin_textdomain('tkgt-style', false, dirname(plugin_basename(__FILE__)) .  '/styles/default/locales/');

    tkgt_add_options();
}
add_action('init', 'tkgt_init');
