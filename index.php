<?php
session_start();
date_default_timezone_set('Europe/Moscow');
require_once "function.php";
require_once "init.php";
require_once "userdata.php";

$title = 'Дела в порядке';

if ($_SESSION['name']) {
    $sql = "SELECT `id` FROM `users` 
        WHERE `name` = '" . $_SESSION['name'] . "'";
    $result = mysqli_query($con, $sql);
    $errorBD = showErrorBD($con, $result);
    $userId = mysqli_fetch_row($result);
    $userId = $userId[0];


    $sql = "SELECT `project` FROM `projects` `p`
        WHERE `users_id` = '" . $userId . "'";
    $result = mysqli_query($con, $sql);
    $errorBD = showErrorBD($con, $result);
    $rows = mysqli_fetch_all($result);
    $projects[] = 'Все';
    foreach ($rows as $project) {
        $projects[] = $project[0];
    }


    if ($_GET['task_is_done']) {
        $taskId = mysqli_real_escape_string($con, $_GET['task_is_done']);
        $sql = "SELECT `date_done` FROM `items`
            WHERE `id` = '" . $taskId . "'";
        $result = mysqli_query($con, $sql);
        $errorBD = showErrorBD($errorBD, $con, $result);
        $taskDateDone = mysqli_fetch_row($result);
        $taskDateDone = ($taskDateDone[0] != false) ? false : date("Y-m-d", strtotime(now));

        if ($taskDateDone[0] == false) {
            $sql = "UPDATE `items`
            SET `date_done` = DEFAULT 
            WHERE `id` = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'i', intval($_GET['task_is_done']));
            mysqli_stmt_execute($stmt);
        } else {
            $sql = "UPDATE `items`
            SET `date_done` = ?
            WHERE `id` = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'si', $taskDateDone, intval($_GET['task_is_done']));
            mysqli_stmt_execute($stmt);
        }
    }

    if ($_POST['submit_task']) {
        if (!$_POST['name']) {
            $taskErrorName = 'form__input--error';
            $error = true;
        }
        if (!$_POST['project']) {
            $taskErrorProject = 'form__input--error';
            $error = true;
        }
        if ($error) {
            $add = 'task';
            $form = showForm($add, $taskErrorName, $taskErrorProject, $projects, $repeat);
        } else {
            if ($_FILES['preview']['size'] > 0) {
                $url_file = addFile();
            }
            if ($_POST['date']) {
                $date = date("Y-m-d", strtotime($_POST['date']));
            }
            $project = mysqli_real_escape_string($con, $_POST['project']);
            $sql = "SELECT `id` FROM `projects`
                WHERE `project` = '" . $project . "' AND
                `users_id` = '" . $userId . "'";
            $result = mysqli_query($con, $sql);
            $errorBD = showErrorBD($con, $result);
            $projectId = mysqli_fetch_row($result);
            $projectId = $projectId[0];
            addNewTask($_POST['name'], $url_file, $date, $userId, $projectId, $con);
        }
    }
    if ($_POST['submit_project']) {
        if (!$_POST['name']) {
            $taskErrorName = 'form__input--error';
            $error = true;
        }
        if ($error) {
            $add = 'project';
            $form = showForm($add, $taskErrorName, $taskErrorProject, $projects, $repeat);
        } else {
            $repeat = addNewProject(htmlspecialchars($_REQUEST['name']), $con, $userId);
            if ($repeat) {
                $add = 'project';
                $form = showForm($add, $taskErrorName, $taskErrorProject, $projects, $repeat);
            }
        }
    }

// Формируем данные для основного шаблона
    if ($_SESSION['name']) {
        if (isset($_GET['numb'])) {
            $numb = htmlspecialchars($_GET['numb']);
        }

        if (isset($numb) && $projects[$numb] && ($projects[$numb] !== $projects[0])) {
            $project = mysqli_real_escape_string($con, $projects[$numb]);
            $sql = "SELECT `id` FROM `projects`
                WHERE `project` = '" . $project . "' AND
                `users_id` = '" . $userId . "'";
            $result = mysqli_query($con, $sql);
            $errorBD = showErrorBD($con, $result);
            $projectId = mysqli_fetch_row($result);
            $projectId = $projectId[0];

            $sql = "SELECT `date_create`, `date_done`, `title`, DATE_FORMAT(`date_deadline`, '%d.%m.%Y'), `i`.`id`, `project`  FROM `items` `i`
        JOIN `projects` `p`
        ON `i`.`projects_id` =  `p`.`id`
        WHERE `i`.`projects_id` = '" . $projectId . "'";
            $result = mysqli_query($con, $sql);
            $errorBD = showErrorBD($con, $result);
            $rows = mysqli_fetch_all($result);
            $items = createItems($rows);

        } else {
            $sql = "SELECT `date_create`, `date_done`, `title`, DATE_FORMAT(`date_deadline`, '%d.%m.%Y'), `id`  FROM `items` 
        WHERE `users_id` = '" . $userId . "'";
            $result = mysqli_query($con, $sql);
            $errorBD = showErrorBD($con, $result);
            $rows = mysqli_fetch_all($result);
            $items = createItems($rows);
        }

        if (!$items) {
            $items = array();
        }
    }


    $content = include_template('templates/index.php', ['itemsForPrint' => $items]);
    if ($_GET['numb'] && !$projects[$_GET['numb']]) {
        $content = '<h1 class="error-message">error 404 / Такой страницы не существует:(</h1>';
    }

    if (isset($_GET['add']) && !$_REQUEST['submit_task']) {
        $add = htmlspecialchars($_GET['add']);
        $form = showForm($add, $taskErrorName, $taskErrorProject, $projects, $repeat);
    }

    if (htmlspecialchars($_GET['show_completed']) == 1) {
        $show_completed = htmlspecialchars($_GET['show_completed']);
        if (isset($_COOKIE['show_completed'])) {
            unset($_COOKIE['show_completed']);
            setcookie('show_completed', '', time() - 3600);
        } else {
            setcookie('show_completed', 1, time() + 3600);
        }
        header("Location: index.php");
    }
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
        $errorEmail = 'form__input--error';
        $error = true;
    } else {
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $sql = "SELECT `email` FROM `users`
                WHERE `email` = '" . $email . "'";
        $result = mysqli_query($con, $sql);
        $errorBD = showErrorBD($con, $result);
        $userEmail = mysqli_fetch_row($result);
        if ($userEmail[0]) {
            $errorEmailExist = 'Такой email уже зарегистрирован:(';
            $error = true;
        }
    }
    if(!htmlspecialchars($_POST['password'])) {
        $errorPassword = 'form__input--error';
        $error = true;
    }
    if(!htmlspecialchars($_POST['name'])) {
        $errorName = 'form__input--error';
        $error = true;
    } else {
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $sql = "SELECT `name` FROM `users`
                WHERE `name` = '" . $name . "'";
        $result = mysqli_query($con, $sql);
        $errorBD = showErrorBD($con, $result);
        $userName = mysqli_fetch_row($result);
        if ($userName[0]) {
            $errorNameExist = 'Такое имя уже зарегистрировано:(';
            $error = true;
        }
    }
    if ($error) {
        $notLog = include_template('templates/register.php', [
            'errorPassword'=>$errorPassword,
            'errorName'=>$errorName,
            'errorEmail'=>$errorEmail,
            'errorEmailExist'=>$errorEmailExist,
            'errorNameExist'=>$errorNameExist
        ]);
    } else {
        $newUser = [
            'email' => htmlspecialchars($_POST['email']),
            'name' => htmlspecialchars($_POST['name']),
            'password' => password_hash( htmlspecialchars($_POST['password']), PASSWORD_DEFAULT)
        ];

        $sql = "INSERT INTO `users` (`id`, `register_date`, `email`, `name`, `password`, `contacts`)
            VALUES (NULL, NOW(), ?, ?,'" . $newUser['password'] . "', ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'sss', $newUser['email'], $newUser['name'], $newUser['contacts']);
        mysqli_stmt_execute($stmt);

        $users[] = $newUser;
        $User = [
            'name' => htmlspecialchars($_POST['name']),
            'password' => htmlspecialchars($_POST['password'])
        ];
        authentication($User, $con);
    }
}

if ($_POST['submit_login']) {
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
        $notLog = authentication($User, $con);
    }
}

if (isset($errorBD)) {
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
    'session'=>$session,
    'con'=>$con,
    'userId'=>$userId]);

print_r($html);




