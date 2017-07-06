<?php
/** This file is part of TKGlobalTasks Plugin project
 *
 * @desc TK_GTaskPage class
 * @package TKGlobalTasks
 * @version 0.1a
 * @author Ravil Sarvaritdinov <ra9oaj@gmail.com>
 * @copyright 2017 Tehnokom.su
 * @license http://opensource.org/licenses/GPL-3.0 GPLv3
 * @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
 * @link https://github.com/tehnokom/alfo-tk-global-tasks-plugin
 */

class TK_GTaskPage
{
    public function __construct($post_id)
    {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('admin_enqueue_scripts', array($this, 'regJS_CSS'));
        add_action('wp_enqueue_scripts', array($this, 'regJS_CSS'));
        add_action('template_redirect', array($this, 'checkPage'));
    }

    public function init()
    {
        $this->regRewriteRules();
    }

    public function addAdminMenu()
    {
        add_menu_page(_x('TK Global Tasks', 'Admin menu', 'tkgt'),
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

    public function regJS_CSS()
    {
        $this->regScripts();
        $this->regStyles();
    }

    protected function regRewriteRules()
    {
        global $wp_rewrite;
        $slug = TK_GTask::taskSettings(true)->slug;

        add_rewrite_tag('%tkgt_page%', '([^&]+)');

        //WP Posts
        add_rewrite_rule('^([^/]+)/([^/]+)' . $slug . '$',
            'index.php?post_type=$matches[1]&name=$matches[2]&tkgt_page=' . $slug,
            'top');

        //WP Static pages
        add_rewrite_rule('^([^/]+)' . $slug . '$',
            'index.php?pagename=$matches[1]&tkgt_page=' . $slug,
            'top');

        $wp_rewrite->flush_rules();
    }

    public function checkPage() {
        global $wp, $wp_query, $post;

        if (!empty($wp->query_vars['tkgt_page'])) {
            if (!empty($post) && in_array($post->post_type, TK_GTask::taskSettings(true)->enabled_for)) {
                add_filter('template_include', array($this, 'includeTemplate'));
            } else {
                $wp_query->set_404();
                status_header(404);
                get_template_part(404);
                exit;
            }
        }
    }

    public function includeTemplate($template_path)
    {
        if (in_array(get_post_type(), TK_GTask::taskSettings()['enabled_for'])) {

            $template_path = TKGT_ROOT . 'styles/default/page.php';
        }

        return $template_path;
    }

    protected function regStyles()
    {
        if (has_action('admin_enqueue_scripts')) {
            wp_register_style('tkgt-admin-settings-css', TKGT_URL . 'admin/css/settings.css');
            wp_enqueue_style('tkgt-admin-settings-css');
        } else {

        }
    }

    protected function regScripts()
    {
        if (has_action('admin_enqueue_scripts')) {

        } else {

        }
    }

}