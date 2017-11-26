        <main class="content__main">
                <h2 class="content__main-heading">Список задач</h2>

                <form class="search-form" action="index.html" method="post">
                    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

                    <input class="search-form__submit" type="submit" name="" value="Искать">
                </form>

                <div class="tasks-controls">
                    <nav class="tasks-switch">
                        <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
                        <a href="/" class="tasks-switch__item">Повестка дня</a>
                        <a href="/" class="tasks-switch__item">Завтра</a>
                        <a href="/" class="tasks-switch__item">Просроченные</a>
                    </nav>

                    <label class="checkbox">
                        <a href="/">
                            <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
                            <input class="checkbox__input visually-hidden" type="checkbox"
                                <?php if ($show_complete_tasks == 1) echo "checked"?> >
                            <span class="checkbox__text">Показывать выполненные</span>
                        </a>
                    </label>
                </div>

                <table class="tasks">
                    <?php foreach ($arrTemplate['itemsForPrint'] as $task): ?>
<!--                    Добавьте класс task--important, если до выполнения задачи меньше дня-->
                        <tr class="tasks__item task
                            <?php
                                $task_deadline_ts = strtotime($task['date']); // метка времени даты выполнения задачи
                                $current_ts = strtotime('now midnight'); // текущая метка времени

                                // в эту переменную запишите кол-во дней до даты задачи
                                $days_until_deadline = floor(($current_ts-$task_deadline_ts)/86400);
                                if ($days_until_deadline >= 0) echo " task--important";
                            ?>">
                        <td class="task__select">
                            <label class="checkbox task__checkbox">
                                <input class="checkbox__input visually-hidden" type="checkbox">
                                <a href="/"><span class="checkbox__text"><?=$task['title']?></span></a>
                            </label>
                        </td>

                        <td class="task__file">
                        </td>

                            <td class="task__date"><?=$task['date']?></td>
                    </tr>
                    <?php endforeach; ?>

                    <!--показывать следующий тег <tr/>, если переменная равна единице-->
                    <tr class="tasks__item task task--completed">
                        <td class="task__select">
                            <label class="checkbox task__checkbox">
                                <input class="checkbox__input visually-hidden" type="checkbox" checked>
                                <a href="/"><span class="checkbox__text">Сделать главную страницу Дела в порядке</span></a>
                            </label>

                        </td>

                        <td class="task__file">
                            <a class="download-link" href="#">Home.psd</a>
                        </td>

                        <td class="task__date"><!--выведите здесь дату выполнения задачи--></td>
                    </tr>
                </table>
            </main>