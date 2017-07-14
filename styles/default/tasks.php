<div class="s_tasks">
    <?php

    $tasks = new TK_GTasks(intval($_POST['post_id']));
    if ($tasks->isValid()) {
        //$tasks->setStatuses(1);
        $tasks->createPage();

        if ($tasks->have_tasks()) {
            while ($tasks->next_task()) {
                $cur_task = $tasks->get_task();

                show_tasks_list($cur_task);
            }
        }
    }

    function show_tasks_list($task, $level = 1, $max_level = 4)
    {
        if (is_object($task)) {
            switch ($task->type) {
                case '0':
                    ?>
                    <div class="s_tasks__item">
                        <button class="s_tasks__item__title">
                            <?php echo apply_filters('the_content',$task->description) ?>
                        </button>
                        <?php if ($task->have_children() && $level <= $max_level) {
                            foreach ($task->get_children() as $child_task_id) {
                                $cur_task = new TK_GTask($child_task_id);

                                if ($cur_task->isValid()) {
                                    ?>
                                    <div class="s_tasks__item__content">
                                        <div class="s_tasks__tree">
                                            <div class="s_tasks__tree__item">
                                                <?php
                                                show_tasks_list($cur_task, $level + 1, $max_level);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                    <?php
                    break;

                case '1':
                    if ($level < 3) {
                        ?>
                        <div class="s_tasks__item">
                            <button class="s_tasks__item__title stage">
                                <?php echo apply_filters('the_content',$task->description) ?>
                            </button>
                            <?php if ($task->have_children() && $level <= $max_level) {
                                foreach ($task->get_children() as $child_task_id) {
                                    $child = new TK_GTask($child_task_id);

                                    if ($child->isValid()) {
                                        ?>
                                        <div class="s_tasks__item__content">
                                            <div class="s_tasks__tree">
                                                <div class="s_tasks__tree__item">
                                                    <?php
                                                    show_tasks_list($child, $level + 1, $max_level);
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                        <?php
                    } else {
                        ?>
                        <a href="#" class="s_tasks__tree__item__title" data-type="project"
                           data-type="circle"><span>
                                <?php echo apply_filters('the_content',$task->description) ?>
                            </span></a>
                        <?php
                        if ($task->have_children() && $level <= $max_level) {
                            foreach ($task->get_children() as $child_task_id) {
                                $child = new TK_GTask($child_task_id);

                                if ($child->isValid()) {
                                    ?>
                                    <div class="s_tasks__tree__item__sub">
                                        <?php
                                        show_tasks_list($child, $level + 1, $max_level);
                                        ?>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                    break;

                default:
                    ?>
                    <a href="#" class="s_tasks__tree__item__title" data-type="project"
                       data-type="circle"><span><?php echo apply_filters('the_content',$task->description) ?></span></a>
                    <?php
                    if ($task->have_children() && $level <= $max_level) {
                        foreach ($task->get_children() as $child_task_id) {
                            $child = new TK_GTask($child_task_id);

                            if ($child->isValid()) {
                                ?>
                                <div class="s_tasks__tree__item__sub">
                                    <?php
                                    show_tasks_list($child, $level + 1, $max_level);
                                    ?>
                                </div>
                                <?php
                            }
                        }
                    }

                    break;
            }
        }
    }

    ?>
</div>
