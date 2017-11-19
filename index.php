<?php
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

$days = rand(-3, 3);
$task_deadline_ts = strtotime("+" . $days . " day midnight"); // метка времени даты выполнения задачи
$current_ts = strtotime('now midnight'); // текущая метка времени

// запишите сюда дату выполнения задачи в формате дд.мм.гггг
$date_deadline = date("d.m.Y", $task_deadline_ts);

// в эту переменную запишите кол-во дней до даты задачи
$days_until_deadline = floor(($current_ts-$task_deadline_ts)/86400);

$projects = ['Все', 'Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$items = [
        [
            'title' => 'Собеседование в IT компании',
            'date' => '01.06.2018',
            'category' => 'Работа',
            'state' => false
        ],
        [
            'title' => 'Выполнить тестовое задание',
            'date' => '25.05.2018',
            'category' => 'Работа',
            'state' => false
        ],
        [
            'title' => 'Сделать задание первого раздела',
            'date' => '21.04.2018',
            'category' => 'Учеба',
            'state' => true
        ],
        [
            'title' => 'Встреча с другом',
            'date' => '22.04.2018',
            'category' => 'Входящие',
            'state' => false
        ],
        [
            'title' => 'Купить корм для кота',
            'date' => false,
            'category' => 'Домашние дела',
            'state' => false
        ],
        [
            'title' => 'Заказать пиццу',
            'date' => false,
            'category' => 'Домашние дела',
            'state' => false
        ]
    ];

    function countItemsInProject ($project, $items) {
        $count = 0;
        foreach ($items as $item) {
            if ($item['category'] === $project) $count++;
        }

        return $count;
    }

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Дела в порядке</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body><!--class="overlay"-->
<h1 class="visually-hidden">Дела в порядке</h1>

<div class="page-wrapper">
    <div class="container container--with-sidebar">
        <header class="main-header">
            <a href="#">
                <img src="img/logo.png" width="153" height="42" alt="Логотип Дела в порядке">
            </a>

            <div class="main-header__side">
                <a class="main-header__side-item button button--plus" href="#">Добавить задачу</a>

                <div class="main-header__side-item user-menu">
                    <div class="user-menu__image">
                        <img src="img/user-pic.jpg" width="40" height="40" alt="Пользователь">
                    </div>

                    <div class="user-menu__data">
                        <p>Константин</p>

                        <a href="#">Выйти</a>
                    </div>
                </div>
            </div>
        </header>

        <div class="content">
            <section class="content__side">
                <h2 class="content__side-heading">Проекты</h2>

                <nav class="main-navigation">
                    <ul class="main-navigation__list">
                        <?php foreach ($projects as $project): ?>
                        <li class="main-navigation__list-item<?php if ($projects[0] == $project) echo ' main-navigation__list-item--active'; ?>">
                            <a class="main-navigation__list-item-link" href="#"><?=$project?></a>
                            <span class="main-navigation__list-item-count"><?=countItemsInProject ($project, $items)?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>

                <a class="button button--transparent button--plus content__side-button" href="#">Добавить проект</a>
            </section>

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
                    <?php foreach ($items as $task): ?>
                    <?php /*if ($show_complete_tasks == 1):*/?><!--
                    <tr class="tasks__item task task--completed">
                        <td class="task__select">
                            <label class="checkbox task__checkbox">
                                <input class="checkbox__input visually-hidden" type="checkbox" checked>
                                <span class="checkbox__text">Записаться на интенсив "Базовый PHP"</span>
                            </label>
                        </td>
                        <td class="task__date">10.04.2017</td>

                        <td class="task__controls">
                        </td>
                    </tr>
                    --><?php /*endif; */?>

<!--                    Добавьте класс task--important, если до выполнения задачи меньше дня-->

                        <tr class="tasks__item task
                            <?php if ($days_until_deadline >= 0) echo " task--important"; ?>">
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
        </div>
    </div>
</div>

<footer class="main-footer">
    <div class="container">
        <div class="main-footer__copyright">
            <p>© 2017, «Дела в порядке»</p>

            <p>Веб-приложение для удобного ведения списка дел.</p>
        </div>

        <a class="main-footer__button button button--plus">Добавить задачу</a>

        <div class="main-footer__social social">
            <span class="visually-hidden">Мы в соцсетях:</span>
            <a class="social__link social__link--facebook" href="#">Facebook
                <svg width="27" height="27" viewBox="0 0 27 27" xmlns="http://www.w3.org/2000/svg">
            </a><span class="visually-hidden">
        ,</span>
            <a class="social__link social__link--twitter" href="#">Twitter
                <svg width="27" height="27" viewBox="0 0 27 27" xmlns="http://www.w3.org/2000/svg">
            </a><span class="visually-hidden">
        ,</span>
            <a class="social__link social__link--instagram" href="#">Instagram
                <svg width="27" height="27" viewBox="0 0 27 27" xmlns="http://www.w3.org/2000/svg">
            </a>
            <span class="visually-hidden">,</span>
            <a class="social__link social__link--vkontakte" href="#">Вконтакте
                <svg width="27" height="27" viewBox="0 0 27 27" xmlns="http://www.w3.org/2000/svg">
            </a>
        </div>

        <div class="main-footer__developed-by">
            <span class="visually-hidden">Разработано:</span>

            <a href="https://htmlacademy.ru/intensive/php">
                <img src="img/htmlacademy.svg" alt="HTML Academy" width="118" height="40">
            </a>
        </div>
    </div>
</footer>

<div class="modal" hidden>
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Добавление задачи</h2>

    <form class="form"  action="index.html" method="post">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input" type="text" name="name" id="name" value="" placeholder="Введите название">
        </div>

        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select" name="project" id="project">
                <option value="">Входящие</option>
            </select>
        </div>

        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>

            <input class="form__input form__input--date" type="date" name="date" id="date" value="" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
        </div>

        <div class="form__row">
            <label class="form__label" for="preview">Файл</label>

            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="preview" id="preview" value="">

                <label class="button button--transparent" for="preview">
                    <span>Выберите файл</span>
                </label>
            </div>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</div>

<div class="modal" hidden>
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Добавление проекта</h2>

    <form class="form"  action="index.html" method="post">
        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input" type="text" name="name" id="project_name" value="" placeholder="Введите название проекта">
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</div>
</body>
</html>