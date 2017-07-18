<?php
/** This file is part of TKGlobalTasks Plugin project
 *
 * @desc TK_GTask class
 * @package TKGlobalTasks
 * @version 0.1a
 * @author Ravil Sarvaritdinov <ra9oaj@gmail.com>
 * @copyright 2017 Tehnokom.su
 * @license http://opensource.org/licenses/GPL-3.0 GPLv3
 * @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
 * @link https://github.com/tehnokom/alfo-tk-global-tasks-plugin
 */

class TK_GTask
{
    protected $wpdb;

    protected $opts;

    public function __construct($task_id)
    {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->wpdb->enable_nulls = true;

        $query = $this->wpdb->prepare("SELECT t.id, t.post_id, tt.description, t.status, tt.type, t.start_date, t.end_date, t.actual_end_date
FROM {$this->wpdb->prefix}tkgt_tasks t, {$this->wpdb->prefix}tkgt_tasks_types tt
WHERE t.task_type_id = tt.id
AND t.id = %d", $task_id);
        $result = $this->wpdb->get_results($query, ARRAY_A);
        if (!empty($result)) {
            $src = !empty($result[0]) ? $result[0] : $result;
            $this->opts['task_id'] = $src['id'];
            unset($src['id']);
            $this->opts = array_merge($this->opts, $src);
        }
    }

    /**
     * Magic method. It's Ma-a-a-gic :)
     * @param string $name
     * @return mixed | null
     */
    public function __get($name)
    {
        if (isset($this->opts[$name])) {
            return $this->opts[$name];
        }

        return null;
    }

    /**
     * Magic method. It's Ma-a-a-gic :)
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->opts[$name]);
    }

    /**
     * Returns TRUE when Task is exists, or FALSE
     * @return bool
     */
    public function isValid()
    {
        return isset($this->opts['task_id']);
    }

    public function setStatus($status)
    {
        if(!empty($status) && is_numeric($status)
            && intval($status) !== intval($this->status)) {

            $res = $this->wpdb->update("{$this->wpdb->prefix}tkgt_tasks",
                array('status' => $status),
                array('id' => $this->task_id),
                array('%d'),
                array('%d')
            );

            return boolval($res);
        }

        return false;
    }

    /**
     * Sets the specified fields to the specified values
     * @param array $args
     * @return bool
     */
    public function setFields($args)
    {
        $check_res = self::checkFields($args);

        if(!empty($check_res['fields'])) {
            unset($check_res['fields']['id']);
            $res = $this->wpdb->update("{$this->wpdb->prefix}tkgt_tasks",$check_res['fields'],
                array('id' => $this->task_id),
                $check_res['types'],
                array('%d'));

            return boolval($res);
        }

        return false;
    }

    /**
     * Checks the fields for the structure of the task data
     * @param $args
     * @return array
     */
    protected static function checkFields($args)
    {
       $fields = array();
       $types = array();

       if(is_array($args) && !empty($args)) {
           foreach ($args as $key => $value) {
               switch ($key) {
                   case 'task_type_id':
                   case 'status':
                       //0 - черновик, 1 - опубликовано, 4 - удалено
                       $types[] = '%d';
                       $fields[$key] = $value;
                       break;
                   case 'start_date':
                   case 'end_date':
                   case 'actual_end_date':
                       $types[] = '%s';
                       $fields[$key] = $value;
                       break;

                   default:
                       continue;
               }
           }
       }

       return array('fields' => $fields, 'types' => $types);
    }

    /**
     * Creates a new Task for a Project
     * @param int $post_id
     * @param array $data
     * @param null|int $parent_id
     * @return null|TK_GTask
     */
    public static function createTask($post_id, $data, $parent_id = null)
    {
        $post = get_post($post_id);

        if (!empty($post) && in_array($post->post_type, self::taskSettings()['enabled_for']) && is_array($data)) {
            $check_res = self::checkFields($data);
            $fields = $check_res['fields'];
            $field_type = $check_res['types'];

            if (!empty($fields['title'])) {
                global $wpdb;
                $wpdb->enable_nulls = true;

                $fields['post_id'] = $post_id;
                $field_type[] = '%d';
                $res = $wpdb->insert("{$wpdb->prefix}tkgt_tasks", $fields, $field_type);

                if ($res) {
                    $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}tkgt_tasks 
WHERE post_id = %d AND task_type_id = %d", $post_id, $fields['task_type_id']);

                    $task_id = $wpdb->get_var($query);
                    $task = new TK_GTask($task_id);
                    if ($task->isValid()) {

                        $parent_task = new TK_GTask(intval($parent_id));

                        if ($parent_task->isValid()) {
                            $wpdb->insert("{$wpdb->prefix}tkgt_tasks_links",
                                array('parent_id' => $parent_id,
                                    'child_id' => $task_id),
                                array('%d', '%d')
                            );
                        }

                        return $task;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Returns TRUE when this Task has children tasks
     * @return bool
     */
    public function have_children()
    {
        if($this->isValid()) {
            $query = $this->wpdb->prepare("SELECT 1 FROM {$this->wpdb->prefix}tkgt_tasks_links
WHERE parent_id = %d", $this->task_id);
            $res = $this->wpdb->get_var($query);
            return boolval($res);
        }

        return false;
    }

    /**
     * Returns array with ID of children tasks
     * @return array
     */
    public function get_children()
    {
        $sql = $this->wpdb->prepare("SELECT * FROM
(SELECT t.id, t.post_id, tl.parent_id, t.internal_id FROM {$this->wpdb->prefix}tkgt_tasks t
LEFT JOIN {$this->wpdb->prefix}tkgt_tasks_links tl ON tl.child_id = t.id) p
WHERE p.parent_id is NULL
AND p.post_id = %d AND p.id <> %d
UNION
SELECT * FROM
(SELECT t.id, t.post_id, tl.parent_id, t.internal_id FROM {$this->wpdb->prefix}tkgt_tasks t
INNER JOIN {$this->wpdb->prefix}tkgt_tasks_links tl ON tl.child_id = t.id) c
WHERE c.post_id = %d AND c.id <> %d;
",
            $this->post_id,
            $this->task_id,
            $this->post_id,
            $this->task_id);

        $res = $this->wpdb->get_results($sql, ARRAY_A);
        return (!empty($res) ? $this->buildTreeTasks($res) : array());
    }

    protected function buildTreeTasks(&$data, $max_level = 7, $current_level = 1, $parent_id = 0)
    {
        $parent_id = !$parent_id ? $this->task_id : $parent_id;
        $out = array();

        if (is_array($data)) {
            foreach ($data as $key => $task) {
                if (intval($task['parent_id']) === intval($parent_id)) {
                    $out['id_' . $task['id']] = $task;
                    unset($data[$key]);

                    if ($current_level < $max_level) {
                        $childs = $this->buildTreeTasks($data,
                            $max_level,
                            $current_level + 1,
                            intval($task['id']));

                        if (!empty($childs)) {
                            $out['id_' . $task['id']]['childs'] = $childs;
                        }
                    }
                }
            }

        }

        return $out;
    }
}

;
?>