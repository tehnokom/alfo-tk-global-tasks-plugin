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
    private $template_dir;

    public function __construct($post_id = null)
    {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('admin_enqueue_scripts', array($this, 'regJS_CSS'));
        add_action('wp_enqueue_scripts', array($this, 'regJS_CSS'));
        add_action('template_redirect', array($this, 'checkPage'));
        add_filter('tkgt_tasks_list', array($this, 'getTasksHtml'), 10, 1);

        $this->template_dir = array('template' => TEMPLATEPATH . '/tkgt_template/');
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
        add_rewrite_tag('%tkgt_t%', '([^&]+)');

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
            global $wp;
            $native_template = TKGT_ROOT . 'styles/default/page.php';

            if(!empty($wp->query_vars['tkgt_t'])) {
                return $native_template;
            } else {
                $dirs = scandir(dirname($template_path));
                if (in_array('tkgt_template', $dirs)) {
                    if(substr_count(dirname($template_path), '/wp-content/plugins')) {
                        $this->template_dir['plugin'] = dirname($template_path) . '/tkgt_template/';
                    }

                    if (is_file(dirname($template_path) . '/tkgt_template/page.php')) {
                        return dirname($template_path) . '/tkgt_template/page.php';
                    }
                } else {
                    return $native_template;
                }
            }
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

    protected function getTemplateFor($template_part_name)
    {
        $template = TKGT_STYLES_DIR . 'default/' . "$template_part_name.php";

        if(is_file($this->template_dir['plugin'] . "$template_part_name.php")) {
            $template = $this->template_dir['plugin'] . "$template_part_name.php";
        } else if(is_file($this->template_dir['template'] . "$template_part_name.php")) {
            $template = $this->template_dir['template'] . "$template_part_name.php";
        }

        return $template;
    }

    public function getTasksHtml($html, $post_id = null)
    {
        $template_file = $this->getTemplateFor('tasks');

        if(is_file($template_file)) {
            ob_start();

            require_once($template_file);

            $html = ob_get_contents();
            ob_end_clean();
        }

        return $html;
    }

}