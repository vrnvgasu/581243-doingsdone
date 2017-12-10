        <main class="content__main">
                <h2 class="content__main-heading">Список задач</h2>

                <form class="search-form" action="index.html" method="post">
                    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">
                    <input class="search-form__submit" type="submit" name="" value="Искать">
                </form>

                <div class="tasks-controls">
                    <nav class="tasks-switch">
                        <a href="/" class="tasks-switch__item<?=(!$arrTemplate['itemFilter'])? " tasks-switch__item--active":""?>">Все задачи</a>
                        <a href="/?item-filter=today" class="tasks-switch__item<?=($arrTemplate['itemFilter']=='today')? " tasks-switch__item--active":""?>">Повестка дня</a>
                        <a href="/?item-filter=tomorrow" class="tasks-switch__item<?=($arrTemplate['itemFilter']=='tomorrow')? " tasks-switch__item--active":""?>">Завтра</a>
                        <a href="/?item-filter=late" class="tasks-switch__item<?=($arrTemplate['itemFilter']=='late')? " tasks-switch__item--active":""?>">Просроченные</a>
                    </nav>

                    <label class="checkbox">
                        <a href="?show_completed=1">
                            <input class="checkbox__input visually-hidden" type="checkbox"<?=($_COOKIE['show_completed'] == 1)? "checked":''?>>
                            <span class="checkbox__text">Показывать выполненные</span>
                        </a>
                    </label>
                </div>

                <table class="tasks">
                    <?php foreach ($arrTemplate['itemsForPrint'] as $task): ?>
                        <?php
                        $task_deadline_ts = strtotime($task['date']); // метка времени даты выполнения задачи
                        $current_ts = strtotime('now midnight'); // текущая метка времени
                        $days_until_deadline = floor(($current_ts-$task_deadline_ts)/86400);
                        ?>
                        <tr class="tasks__item task <?=($days_until_deadline >= 0)?" task--important":''?><?=($task['state'])?" task--completed":''?><?=(!$_COOKIE['show_completed'] && $task['state'])?" hidden":''?>">
                        <td class="task__select">
                            <label class="checkbox task__checkbox">
                                <input class="checkbox__input visually-hidden" type="checkbox">
                                <a href="?task_is_done=<?=$task['id']?>"><span class="checkbox__text"><?=$task['title']?></span></a>
                            </label>
                        </td>

                        <td class="task__file">
                            <a class="download-link" href="/download.php?fileName=<?=basename($task['file'])?>" <?=(basename($task['file']))? :" hidden"?> target="_blank"><?=basename($task['file'])?></a>
                        </td>

                            <td class="task__date"><?=$task['date']?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </main>