<?php
require_once "function.php";

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
$itemsForPrint=array();
if ($_GET['numb'] && $projects[$_GET['numb']]) {
    foreach ($items as $item) {
        if ($item['category'] === $projects[$_GET['numb']]) {
            $itemsForPrint[] = $item;
        }
    }
} else {
    foreach ($items as $item) {
        $itemsForPrint[] = $item;
    }
}

$title = 'Дела в порядке';

$content = include_template('templates/index.php', ['itemsForPrint' => $itemsForPrint, 'days_until_deadline'=>$days_until_deadline]);
if ($_GET['numb'] && !$projects[$_GET['numb']]) {
    $content = '<h1 class="error-message">error 404 / Такой страницы не существует:(</h1>';
}

$html = include_template('templates/layout.php', [
    'content'=>$content,
    'projects'=>$projects,
    'items' => $items,
    'title' => $title]);

print_r($html);

function countItemsInProject ($project, $items) {
    $count = 0;
    if ($project === 'Все') {
        return count($items);
    }
    foreach ($items as $item) {
        if ($item['category'] === $project) $count++;
    }

    return $count;
}
