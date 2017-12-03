<?php
session_start();
require_once "function.php";
require_once "userdata.php";
require_once "init.php";

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');



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

$title = 'Дела в порядке';

if ($_POST['submit_task']) {
    if(!$_POST['name']) {
        $taskErrorName = 'form__input--error';
        $error = true;
    }
    if(!$_POST['project']) {
        $taskErrorProject = 'form__input--error';
        $error = true;
    }
    if ($error) {
        $add = 'task';
        $form = showForm($add, $taskErrorName, $taskErrorProject, $projects);
    } else {
        addNewTask(htmlspecialchars($_REQUEST['name']), $_REQUEST['date'], $_REQUEST['project']);
        if ($_FILES) {
            addFile();
        }
    }
}
if ($_POST['submit_project']) {
    if(!$_POST['name']) {
        $taskErrorName = 'form__input--error';
        $error = true;
    }
    if ($error) {
        $add = 'project';
        $form = showForm($add, $taskErrorName, $taskErrorProject, $projects);
    } else {
        addNewProject(htmlspecialchars($_REQUEST['name']));
    }
}

if (isset($_GET['numb'])) {
    $numb = htmlspecialchars($_GET['numb']);
}

$itemsForPrint = itemsForPrint ();

$content = include_template('templates/index.php', ['itemsForPrint'=>$itemsForPrint]);
if ($_GET['numb'] && !$projects[$_GET['numb']]) {
    $content = '<h1 class="error-message">error 404 / Такой страницы не существует:(</h1>';
}

if (isset($_GET['add']) && !$_REQUEST['submit_task']) {
    $add = htmlspecialchars($_GET['add']);
    $form = showForm($add, $taskErrorName, $taskErrorProject, $projects);
}

if (htmlspecialchars($_GET['show_completed']) == 1) {
    $show_completed = htmlspecialchars($_GET['show_completed']);
    if (isset($_COOKIE['show_completed'])) {
        unset($_COOKIE['show_completed']);
        setcookie ('show_completed','', time()-3600);
    } else {
        setcookie ('show_completed', 1, time()+3600);
    }
    header("Location: index.php");
}

if (!$_POST['submit_register']&&!$_POST['submit_login']) {
    $guest = include_template('templates/guest.php', []);
    $register = include_template('templates/register.php', []);
    $login = include_template('templates/login.php', []);
}


if (!isset($_SESSION['login'])) {
    if (htmlspecialchars($_GET['log']) === 'in') {
        $notLog = $login;
    } else if (htmlspecialchars($_GET['log']) === 'register') {
        $notLog = $register;
    } else {
        $notLog = $guest;
    }
}
if ($_POST['submit_register']) {
    if(!htmlspecialchars($_POST['email'])) {
        $taskErrorEmail = 'form__input--error';
        $error = true;
    }
    if(!htmlspecialchars($_POST['password'])) {
        $taskErrorPassword = 'form__input--error';
        $error = true;
    }
    if(!htmlspecialchars($_POST['name'])) {
        $taskErrorName = 'form__input--error';
        $error = true;
    }
    if ($error) {
        $notLog = include_template('templates/register.php', [
            'taskErrorPassword'=>$taskErrorPassword,
            'taskErrorName'=>$taskErrorName
        ]);
    } else {
        $newUser = [
            'email' => htmlspecialchars($_POST['email']),
            'name' => htmlspecialchars($_POST['name']),
            'password' => password_hash( htmlspecialchars($_POST['password']), PASSWORD_DEFAULT)
        ];
        $users[] = $newUser;
        $User = [
            'name' => htmlspecialchars($_POST['name']),
            'password' => htmlspecialchars($_POST['password'])
        ];
        authentication($User);
    }
}
if ($_POST['submit_login']) {
    //print_r('asldkfjasldkfj');
    if(!htmlspecialchars($_POST['name'])) {
        $taskErrorName = 'form__input--error';
        $error = true;
    }
    if(!htmlspecialchars($_POST['password'])) {
        $taskErrorPassword = 'form__input--error';
        $error = true;
    }
    if ($error) {
        $notLog = include_template('templates/login.php', [
            'taskErrorPassword'=>$taskErrorPassword,
            'taskErrorName'=>$taskErrorName
        ]);
    } else {
        $User = [
            'name' => htmlspecialchars($_POST['name']),
            'password' => htmlspecialchars($_POST['password'])
        ];
        $notLog = authentication($User);
    }
}

if (isset($errorBD)) {
    print_r($errorBD);
    $content = include_template('templates/error.php', [
        'errorBD'=>$errorBD,
    ]);
}

$html = include_template('templates/layout.php', [
    'content'=>$content,
    'modal'=>$form['modal'],
    'projects'=>$projects,
    'items'=>$items,
    'title'=>$title,
    'numb'=>$numb,
    'overlay'=>$form['overlay'],
    'notLog'=>$notLog,
    'session'=>$session]);

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

function showForm($add, $taskErrorName, $taskErrorProject, $projects) {
    $overlay = 'overlay';
    $modal = include_template('templates/form.php',
            ['add'=>$add,
            'taskErrorProject'=>$taskErrorProject,
            'taskErrorName'=>$taskErrorName,
            'projects'=>$projects]);
    $form = ['overlay'=>$overlay, 'modal'=>$modal];
    return $form;
}

function addNewTask($title, $date = false, $category, $state = false) {
    $newTask = [
        'title' => $title,
        'date' => $date,
        'category' => $category,
        'state' => $state
    ];
    global $items;
    array_unshift($items, $newTask);

};

function addNewProject($newProject) {
    global $projects;
    array_splice($projects, 1, 0, $newProject);
}

function itemsForPrint () {
    global $numb;
    global $projects;
    global $items;
    $itemsForPrint = array();
    if (isset($numb) && $projects[$numb] && ($projects[$numb] !== $projects[0])) {
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
    return $itemsForPrint;
}

function addFile () {
    $file_name = $_FILES['preview']['name'];
    $file_path = __DIR__ . '/uploads/';

    move_uploaded_file($_FILES['preview']['tmp_name'], $file_path.$file_name);
}
