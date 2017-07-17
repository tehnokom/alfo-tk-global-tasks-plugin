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
        add_action('admin_enqueue_scripts', array($this, 'regAdminJS_CSS'));
        add_action('wp_enqueue_scripts', array($this, 'regAdminJS_CSS'));
        add_action('template_redirect', array($this, 'checkPage'));
        add_filter('tkgt_tasks_list', array($this, 'getTasksHtml'), 10, 2);
        add_filter('tkgt_menu', array($this, 'getMenuHtml'), 10, 2);
        add_filter('tkgt_menu_items', array($this, 'getMenuItems'));

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

    public function regJS_CSS($data, $type)
    {
        if (!empty($data)) {
            foreach ($data as $template) {
                $template_path = $this->getTemplateFor('menu', $type);

                if (is_file($template_path)) {
                    $url = str_replace(ABSPATH,'/', $template_path);

                    if($type === 'js') {
                        wp_register_script("tkgi-$template-js", $url);
                        wp_enqueue_script("tkgi-$template-js");
                    } else {
                        wp_register_style("tkgi-$template-css", $url);
                        wp_enqueue_style("tkgi-$template-css");
                    }
                }
            }
        }
    }

    protected function regRewriteRules()
    {
        global $wp_rewrite;
        $options = TK_GTask::taskSettings();
        $slug = $options['slug'];

        add_rewrite_tag('%tkgt_page%', '([^&]+)');
        add_rewrite_tag('%tkgt_t%', '([^&]+)');

        //WP Posts
        add_rewrite_rule('^([^/]+)/([^/]+)' . $slug . '$',
            'index.php?post_type=$matches[1]&name=$matches[2]&tkgt_page=' . $slug,
            'top');

        foreach ($options['subpages'] as $value) {
            add_rewrite_rule('^([^/]+)/([^/]+)(' . $slug . ')(' . $value . ')$',
                'index.php?post_type=$matches[1]&name=$matches[2]&tkgt_page=$matches[4]',
                'top');
        }

        if (in_array('page', (array)$options['enabled_for'])) {
            //WP Static pages
            add_rewrite_rule('^([^/]+)' . $slug . '$',
                'index.php?pagename=$matches[1]&tkgt_page=' . $slug,
                'top');

            foreach ($options['subpages'] as $value) {
                add_rewrite_rule('^([^/]+)(' . $slug . ')(' . $value . ')$',
                    'index.php?pagename=$matches[1]&tkgt_page=$matches[3]',
                    'top');
            }
        }

        $wp_rewrite->flush_rules();
    }

    public function checkPage()
    {
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

    public function includeTemplate($template_path, $type = 'page')
    {


        if (in_array(get_post_type(), TK_GTask::taskSettings()['enabled_for'])) {
            global $wp;
            $native_template = TKGT_ROOT . "styles/default/page.php";

            if (!empty($wp->query_vars['tkgt_t'])) {
                return $native_template;
            } else {
                $dirs = scandir(dirname($template_path));
                if (in_array('tkgt_template', $dirs)) {
                    if (substr_count(dirname($template_path), '/wp-content/plugins')) {
                        $this->template_dir['plugin'] = dirname($template_path) . "/tkgt_template/";
                    }

                    if (is_file(dirname($template_path) . "/tkgt_template/page.php")) {
                        return dirname($template_path) . "/tkgt_template/page.php";
                    }
                } else {
                    return $native_template;
                }
            }
        }

        return $template_path;
    }

    public function regAdminJS_CSS()
    {
        if (has_action('admin_enqueue_scripts')) {
            wp_register_style('tkgt-admin-settings-css', TKGT_URL . 'admin/css/settings.css');
            wp_enqueue_style('tkgt-admin-settings-css');
        } else if (has_action('admin_enqueue_scripts')) {

        }
    }

    protected function getTemplateFor($template_part_name, $type = 'page')
    {
        $subdir = '';

        switch ($type) {
            case 'css':
                $subdir = 'css/';
                break;

            case 'js':
                $subdir = 'js/';
                break;

            case 'page':
            default:
                $type = 'php';
                break;
        }

        $template = TKGT_STYLES_DIR . "default/$subdir" . "$template_part_name.$type";

        if (is_file($this->template_dir['plugin'] . "$subdir" . "$template_part_name.php")) {
            $template = $this->template_dir['plugin'] . "$subdir"  . "$template_part_name.php";
        } else if (is_file($this->template_dir['template'] . "$subdir" . "$template_part_name.php")) {
            $template = $this->template_dir['template'] . "$subdir" . "$template_part_name.php";
        }

        return $template;
    }

    public function getTasksHtml($html, $post_id = null)
    {
        $template_file = $this->getTemplateFor('tasks');

        if (is_file($template_file)) {
            ob_start();

            require_once($template_file);

            $html = ob_get_contents();
            ob_end_clean();
        }

        return $html;
    }

    public function getMenuHtml($html, $post_id = null)
    {
        $tkgt_menu_items = apply_filters('tkgt_menu_items', array());

        $template_file = $this->getTemplateFor('menu');

        if (is_file($template_file)) {
            ob_start();

            require_once($template_file);

            $html = ob_get_contents();
            ob_end_clean();
        }

        return $html;
    }

    public function getMenuItems($items)
    {
        $tasks_slug = TK_GTask::taskSettings(true)->slug;
        $task_page = get_permalink() . $tasks_slug;
        $subpages = TK_GTask::taskSettings(true)->subpages;

        $native = array(
            array('title' => __('Tasks', 'tkgt'),
                'href' => $task_page,
                'slug' => $tasks_slug),
            array('title' => __('Actions', 'tkgt'),
                'href' => $task_page . $subpages['actions'],
                'slug' => $subpages['actions']),
            array('title' => __('Suggestions', 'tkgt'),
                'href' => $task_page . $subpages['suggestions'],
                'slug' => $subpages['suggestions']),
            array('title' => __('Trash', 'tkgt'),
                'href' => $task_page . $subpages['trash'],
                'slug' => $subpages['trash']),
        );

        if (empty($items)) {
            return $native;
        } else {
            $items = array_merge((array)$items, $native);
        }

        return $items;
    }

}