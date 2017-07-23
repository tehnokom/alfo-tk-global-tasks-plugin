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
        add_action('template_redirect', array($this, 'redirectToFullPage'));
        add_filter('tkgt_tasks_list', array($this, 'getTasksHtml'));
        add_filter('tkgt_menu', array($this, 'getMenuHtml'));
        add_filter('tkgt_menu_items', array($this, 'getMenuItems'));
        add_filter('tkgt_breadcrumbs', array($this, 'getBreadCrumbsHtml'));
        add_filter('tkgt_breadcrumbs_path_items', array($this, 'getBreadCrumbsItems'));

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
                $template_path = $this->getTemplateFor($template, $type);

                if (is_file($template_path)) {
                    $url = str_replace(ABSPATH, '/', $template_path);

                    if ($type === 'js') {
                        wp_register_script("tkgi-$template-js", $url, array('jquery'));
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
        $options = self::taskSettings();
        $slug = $options['slug'];

        add_rewrite_tag('%tkgt_pid%', '([^&]+)');
        add_rewrite_tag('%tkgt_piid%', '([^&]+)');
        add_rewrite_tag('%tkgt_tid%', '([^&]+)');
        add_rewrite_tag('%tkgt_tiid%', '([^&]+)');
        add_rewrite_tag('%tkgt_pt%', '([^&]+)');
        add_rewrite_tag('%tkgt_pts%', '([^&]+)');
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

        $url_template = $options['fullpage_url'];
        $this->fullPageRewrite($url_template);

        $url_template = $options['fullpage_home_url'];
        $this->fullPageRewrite($url_template);

        $wp_rewrite->flush_rules();
    }

    protected function fullPageRewrite($url_template)
    {
        $options = self::taskSettings();
        $flag = null;

        preg_match_all('/%[^%]+%/', $url_template, $params);

        $url_template = preg_replace('/%(task_id|task_iid|post_id|post_iid)%/', '([\\d]+)', $url_template);

        if (substr_count($url_template, '%post_type%')) {
            $flag = '%post_type%';
        } else if (substr_count($url_template, '%task_post_slug%')) {
            $flag = '%task_post_slug%';
        }

        $query = '';

        foreach ($params[0] as $index => $param) {
            if (!empty($query)) {
                $query .= '&';
            }

            switch ($param) {
                case '%task_id%':
                    $query .= 'tkgt_tid';
                    break;
                case '%task_iid%':
                    $query .= 'tkgt_tiid';
                    break;
                case '%post_id%':
                    $query .= 'tkgt_pid';
                    break;
                case '%post_iid%':
                    $query .= 'tkgt_piid';
                    break;
                case '%task_post_slug%':
                    $query .= 'tkgt_pts';
                    break;

                default:
                    $query .= 'tkgt_pt';
                    break;
            }

            $query .= '=$matches[' . ($index + 1) . ']';
        }

        $query = 'index.php?' . $query;

        if (isset($flag)) {
            $values = $flag === '%post_type%' ? $options['enabled_for'] : array_values($options['uri_slugs']);

            foreach ($values as $value) {
                $new_url = str_replace($flag, "($value)", $url_template);
                add_rewrite_rule('^' . $new_url . '$', $query, 'top');
            }
        } else {
            add_rewrite_rule('^' . $url_template . '$', $query, 'top');
        }
    }

    public function checkPage()
    {
        global $wp, $wp_query, $post;

        if (!empty($wp->query_vars['tkgt_page'])) {
            if (!empty($post) && in_array($post->post_type, self::taskSettings(true)->enabled_for)) {
                add_filter('template_include', array($this, 'includeTemplate'));
            } else {
                $wp_query->set_404();
                status_header(404);
                get_template_part(404);
                exit;
            }
        }
    }

    public function redirectToFullPage()
    {
        global $wp_query, $post;

        $vars = preg_grep('/^(tkgt_pid|tkgt_piid|tkgt_tid|tkgt_tiid|tkgt_pt|tkgt_pts)$/',
            array_keys($wp_query->query_vars));
        if (!empty($vars)) {
            $post_o = 0;
            $task_o = 0;
            $post_type = 0;

            if (in_array('tkgt_tid', $vars)) {
                $task_o = new TK_GTask($wp_query->query_vars['tkgt_tid']);
                $task_o = $task_o->isValid() ? $task_o : 0;
            }

            if (in_array('tkgt_pid', $vars)) {
                $post_o = get_post($wp_query->query_vars['tkgt_pid']);
                $post_o = is_object($post_o) ? $post_o : 0;
            }

            if (!$task_o) {
                if (!$post_o && in_array('tkgt_piid', $vars)
                    && (in_array('tkgt_pt', $vars) || in_array('tkgt_pts', $vars))) {
                    $post_type = in_array('tkgt_pt', $vars) ? $wp_query->query_vars['tkgt_pt'] : 0;

                    if (!$post_type && in_array('tkgt_pts', $vars)) {
                        $post_type = array_search($wp_query->query_vars['tkgt_pts'],
                            (array)self::taskSettings(true)->uri_slugs);
                    }

                    if ($post_type && in_array($post_type, (array)get_post_types())) {
                        $post_t = self::getPostID($wp_query->query_vars['tkgt_piid'], $post_type);
                        $post_o = get_post($post_t);
                        $post_o = is_object($post_o) ? $post_o : 0;
                    }
                }

                if ($post_o && in_array('tkgt_tiid', $vars)) {
                    $task_id = TK_GTask::getTaskByInternalId($wp_query->query_vars['tkgt_tiid']);
                    $task_o = $task_id ? new TK_GTask($task_id) : 0;
                }
            }

            if ((!$task_o && !$post_o)
                || (!$task_o && !empty(preg_grep('/(tkgt_tid|tkgt_tiid)/', $vars)))
                || (!$post_o && !empty(preg_grep('/(tkgt_pid|tkgt_piid)/', $vars)))) { //окончательная проверка
                $wp_query->set_404();
                status_header(404);
                get_template_part(404);
                exit;
            }

            $new_query = array('post_type' => $post_type,
                'name' => $post_o->post_name);

            if ($post_o) {
                $new_query['tkgt_pid'] = $post_o->ID;
            }

            if ($task_o) {
                global $tkgt_task;

                $tkgt_task = $task_o;
                $new_query['tkgt_tid'] = $task_o->task_id;
            }

            $wp_query->query($new_query);
            $wp_query->the_post();

            add_filter('template_include', array($this, 'includeTemplate'));
        }
    }

    static protected function getPostID($internal_id, $post_type)
    {
        if (class_exists('TK_GProject') && $post_type === 'projektoj') {
            return TK_GProject::idToPost($internal_id);
        }

        return $internal_id;
    }

    public function includeTemplate($template_path)
    {
        if (in_array(get_post_type(), self::taskSettings()['enabled_for'])) {
            global $wp_query;
            $type = self::isSingleFullPage() ? 'single-page' : 'page';

            $native_template = TKGT_ROOT . "styles/default/{$type}.php";

            if (!empty($wp_query->query_vars['tkgt_t'])) {
                return $native_template;
            }

            $dirs = scandir(dirname($template_path));

            if (in_array('tkgt_template', $dirs)) {
                if (substr_count(dirname($template_path), '/wp-content/plugins')) {
                    $this->template_dir['plugin'] = dirname($template_path) . "/tkgt_template/";
                }

                if (is_file(dirname($template_path) . "/tkgt_template/{$type}.php")) {
                    return dirname($template_path) . "/tkgt_template/{$type}.php";
                }
            }

            return $native_template;

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
            $template = $this->template_dir['plugin'] . "$subdir" . "$template_part_name.php";
        } else if (is_file($this->template_dir['template'] . "$subdir" . "$template_part_name.php")) {
            $template = $this->template_dir['template'] . "$subdir" . "$template_part_name.php";
        }

        return $template;
    }

    public function getTasksHtml($html)
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

    public function getBreadCrumbsHtml($html)
    {
        $tkgt_path_items = apply_filters('tkgt_breadcrumbs_path_items', array());
        $template_file = $this->getTemplateFor('breadcrumbs');

        if (is_array($tkgt_path_items) && is_file($template_file)) {
            ob_start();

            require_once($template_file);

            $html = ob_get_contents();
            ob_end_clean();
        }

        return $html;
    }

    public function getMenuHtml($html)
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
        $tasks_slug = self::taskSettings(true)->slug;
        $task_page = get_permalink() . $tasks_slug;
        $subpages = self::taskSettings(true)->subpages;

        $native = array(
            array('title' => __('Tasks', 'tkgt'),
                'href' => $task_page,
                'fullpage_href' => tkgt_tasks_fullpage_link(),
                'slug' => $tasks_slug),
            array('title' => __('Actions', 'tkgt'),
                'href' => $task_page . $subpages['actions'],
                'fullpage_href' => tkgt_tasks_fullpage_link() . $subpages['actions'],
                'slug' => $subpages['actions']),
            array('title' => __('Suggestions', 'tkgt'),
                'href' => $task_page . $subpages['suggestions'],
                'fullpage_href' => tkgt_tasks_fullpage_link() . $subpages['suggestions'],
                'slug' => $subpages['suggestions']),
            array('title' => __('Trash', 'tkgt'),
                'href' => $task_page . $subpages['trash'],
                'fullpage_href' => tkgt_tasks_fullpage_link() . $subpages['trash'],
                'slug' => $subpages['trash']),
        );

        if (empty($items)) {
            return $native;
        } else {
            $items = array_merge((array)$items, $native);
        }

        return $items;
    }

    public function getBreadCrumbsItems($items)
    {
        $native = array(
            array('title' => get_the_title(),
                'href' => get_permalink()),
            array('title' => __('Tasks', 'tkgt'),
                'href' => tkgt_is_fullpage() ? $this->getTaskFullpageLink() : $this->getTaskPagePermalink())
        );

        if (self::isSingleFullPage()) {
            global $tkgt_task;

            if (is_object($tkgt_task)) {
                $native[] = array('title' => __('View task', 'tkgt') . ' ',
                    'href' => tkgt_tasks_fullpage_link(null, $tkgt_task->task_id));
            }
        }

        if (!empty($items) && is_array($items)) {
            $items = array_merge($items, $native);
        } else {
            $items = $native;
        }

        return $items;
    }

    static function taskSettings($out_object = false)
    {
        $settings = get_option('tkgt_settings');

        if (!is_array($settings)) {
            $settings = array(
                'slug' => 'tasks',
                'enabled_for' => null,
                'subpages' => array('actions' => '/actions',
                    'suggestions' => '/sug',
                    'trash' => '/trash'),
                'fullpage_home_url' => 'tasks/%post_id%',
                'fullpage_url' => 'tasks/%post_id%/%task_id%'
            );
        } else {
            if (empty($settings['slug'])) {
                $settings['slug'] = 'tasks';
            }

            if (empty($settings['enabled_for'])) {
                $settings['enabled_for'] = array();
            } else {
                foreach ($settings['enabled_for'] as $post_type) {
                    if (empty($settings['uri_slugs'][$post_type])) {
                        $settings['uri_slugs'][$post_type] = $post_type;
                    }
                }
            }

            if (empty($settings['subpages'])) {
                $settings['subpages'] = array('actions' => '/actions',
                    'suggestions' => '/sug',
                    'trash' => '/trash');
            }

            if (empty($settings['fullpage_home_url'])) {
                $settings['fullpage_home_url'] = 'tasks/%post_id%';
            }

            if (empty($settings['fullpage_url'])) {
                $settings['fullpage_url'] = 'tasks/%post_id%/%task_id%';
            }
        }

        return ($out_object ? (object)$settings : $settings);
    }

    public function getTaskPagePermalink($post_id = null)
    {
        $post_type = get_post_type($post_id);

        if ($post_type && in_array($post_type, self::taskSettings(true)->enabled_for)) {
            $permalink = get_post_permalink($post_id);

            if (!is_object($permalink)) {
                $slug = self::taskSettings(true)->slug;

                return "$permalink$slug";
            }

        }

        return '';
    }

    public function getTaskFullpageLink($post_id = null, $task_id = null, $subpage = null)
    {
        $post_id = empty($post_id) ? get_post()->ID : $post_id;
        $post_type = get_post_type($post_id);
        $projects_slug = class_exists('TK_GProject') ? TK_GProject::slug : null;

        if ($post_type && in_array($post_type, self::taskSettings(true)->enabled_for)) {
            $options = self::taskSettings();
            $url_template = empty($task_id) ? $options['fullpage_home_url'] : $options['fullpage_url'];
            preg_match_all('/%[^%]+%/', $url_template, $vars);

            if (!empty($vars)) {
                foreach ($vars[0] as $var) {
                    $replacement = '';
                    switch ($var) {
                        case '%post_id%':
                            $replacement = $post_id;
                            break;

                        case '%post_iid%':
                            $replacement = $post_type === $projects_slug ? TK_GProject::postToId($post_id) : $post_id;
                            break;

                        case '%post_type%':
                            $replacement = $post_id;
                            break;

                        case '%task_post_slug%':
                            $replacement = $options['uri_slugs'][$post_type];
                            break;

                        case '%task_id%':
                            $replacement = $task_id;
                            break;

                        case '%task_iid%':
                            $replacement = TK_GTask::getInternalIdByTask($task_id);
                            break;

                        default:
                            continue;
                    }

                    $url_template = str_replace($var, $replacement, $url_template);
                }
                $url_template .= !empty($subpage) ? $subpage : '';

                return home_url("/$url_template");
            }
        }

        return '';
    }

    static public function isFullPage()
    {
        global $wp_query;
        $fullpage_pattern = '/(tkgt_pid|tkgt_piid|tkgt_tid|tkgt_tiid|tkgt_pt|tkgt_pts)/';

        return boolval(count(preg_grep($fullpage_pattern, array_keys($wp_query->query_vars))));
    }

    static public function isSingleFullPage()
    {
        global $wp_query;
        $fullpage_pattern = '/(tkgt_tid|tkgt_tiid)/';

        return boolval(count(preg_grep($fullpage_pattern, array_keys($wp_query->query_vars))));
    }
}