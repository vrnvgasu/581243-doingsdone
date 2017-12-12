<?php
session_start();
date_default_timezone_set('Europe/Moscow');
require_once "function.php";
require_once "init.php";
require_once "userdata.php";

$title = 'Дела в порядке';

if (!empty($_SESSION['name'])) {
    $sql = "SELECT `id` FROM `users` 
        WHERE `name` = ?";
    $stmt = mysqli_stmt_init($con);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $errorBD = showErrorBD($con, false);
    }
    mysqli_stmt_bind_param($stmt, 's', $_SESSION['name']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_row($result);
    $userId = $rows[0];

    $countItemsInProject = countItemsInProject ($con, $userId);

    $sql = "SELECT `id`, `project` FROM `projects` `p`
        WHERE `users_id` = ?";
    $stmt = mysqli_stmt_init($con);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $errorBD = showErrorBD($con, false);
    }
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $projects[0]['project'] = 'Все';
    $projects[0]['id'] = 0;
    $contOfAllItems = 0;
    while ($row = mysqli_fetch_array($result)) {
        $project['id'] = $row['id'];
        $project['project'] = $row['project'];
        if (isset($countItemsInProject)) {
            foreach ($countItemsInProject as $countItems) {
                if ($project['id'] == $countItems['projectId']) {
                    $count = $countItems['itemsCount'];
                }
            }
        }

        if (isset($count)) {
            $project['countItems'] = $count;
        }else {
            $project['countItems'] = 0;
        }
        $contOfAllItems = $contOfAllItems+$project['countItems'];
        $projects[] = $project;
        $count = 0;
    }
    $projects[0]['countItems'] = $contOfAllItems;

    if (isset($_GET['task_is_done'])) {
        $taskId = mysqli_real_escape_string($con, $_GET['task_is_done']);
        $sql = "SELECT `date_done` FROM `items`
            WHERE `id` = ?";
        $stmt = mysqli_stmt_init($con);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $errorBD = showErrorBD($con, false);
        }
        mysqli_stmt_bind_param($stmt, 'i', $_GET['task_is_done']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $taskDateDone = mysqli_fetch_row($result);
        $taskDateDone = ($taskDateDone[0] != false) ? false : date("Y-m-d", strtotime('now'));

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

    if (isset($_POST['submit_task'])) {
        if (!$_POST['name']) {
            $taskErrorName = 'form__input--error';
            $error = true;
        }
        if (!$_POST['project']) {
            $taskErrorProject = 'form__input--error';
            $error = true;
        }
        if (isset($error)) {
            $add = 'task';
            $form = showForm($add, isset($taskErrorName)? $taskErrorName:false,
                isset($taskErrorProject)? $taskErrorProject:false, $projects,
                isset($repeat)? $repeat:false);
        } else {
            if ($_FILES['preview']['size'] > 0) {
                $url_file = addFile();
            }
            if ($_POST['date']) {
                $date = date("Y-m-d", strtotime($_POST['date']));
            }
            foreach ($projects as $project) {
                if ($project['project'] == $_POST['project']) {
                    $projectId = $project['id'];
                }
            }

            addNewTask($_POST['name'], $url_file, $date, $userId, $projectId, $con);
        }
    }
    if (isset($_POST['submit_project'])) {
        if (!$_POST['name']) {
            $taskErrorName = 'form__input--error';
            $error = true;
        }
        if (isset($error)) {
            $add = 'project';
            $form = showForm($add, isset($taskErrorName)? $taskErrorName:false,
                isset($taskErrorProject)? $taskErrorProject:false,
                isset($projects)? $projects:false, isset($repeat)? $repeat:false);
        } else {
            $repeat = addNewProject(htmlspecialchars($_REQUEST['name']), $con, $userId);
            if ($repeat) {
                $add = 'project';
                $form = showForm($add, isset($taskErrorName)? $taskErrorName:false,
                    isset($taskErrorProject)? $taskErrorProject:false, $projects, $repeat);
            }
        }
    }

    if ($_SESSION['name']) {
        if (isset($_GET['numb'])) {
            $numb = htmlspecialchars($_GET['numb']);
        }
        if (isset($_GET['item-filter'])) {
            $itemFilter = htmlspecialchars($_GET['item-filter']);
        } else {
            $itemFilter = 'all';
        }
        foreach ($projects as $project) {
            if (isset($numb)) {
                if ($project['id'] == $numb) {
                    $thisProjectId = $numb;
                }
            }
        }
        if (isset($numb) && $thisProjectId && ($thisProjectId !== $projects[0]['id'])) {
            $projectId = $numb;


                switch ($itemFilter) {
                    case 'today':
                        $sql = "SELECT `date_create`, `date_done`, `title`, DATE_FORMAT(`date_deadline`, '%d.%m.%Y'), `i`.`id`, `project`, `url_file`  FROM `items` `i`
                            JOIN `projects` `p`
                            ON `i`.`projects_id` =  `p`.`id`
                            WHERE `i`.`projects_id` = ? AND
                            `i`.`date_deadline` = CURDATE() ";
                        break;
                    case 'tomorrow':
                        $sql = "SELECT `date_create`, `date_done`, `title`, DATE_FORMAT(`date_deadline`, '%d.%m.%Y'), `i`.`id`, `url_file`, `project`  FROM `items` `i`
                            JOIN `projects` `p`
                            ON `i`.`projects_id` =  `p`.`id`
                            WHERE `i`.`projects_id` = ? AND
                            `i`.`date_deadline` = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
                        break;
                    case 'late':
                        $sql = "SELECT `date_create`, `date_done`, `title`, DATE_FORMAT(`date_deadline`, '%d.%m.%Y'), `i`.`id`, `url_file`, `project`  FROM `items` `i`
                            JOIN `projects` `p`
                            ON `i`.`projects_id` =  `p`.`id`
                            WHERE `i`.`projects_id` = ? AND
                            `i`.`date_deadline` < CURDATE()";
                        break;
                    default:
                        $sql = "SELECT `date_create`, `date_done`, `title`, DATE_FORMAT(`date_deadline`, '%d.%m.%Y'), `i`.`id`, `url_file`, `project`  FROM `items` `i`
                            JOIN `projects` `p`
                            ON `i`.`projects_id` =  `p`.`id`
                            WHERE `i`.`projects_id` = ?";
                }

            $stmt = mysqli_stmt_init($con);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                $errorBD = showErrorBD($con, false);
            }
            mysqli_stmt_bind_param($stmt, 'i', $projectId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_array($result)) {
                $items[] = createItems($row);
            }

        } else {
                switch ($itemFilter) {
                    case 'today':
                        $sql = "SELECT `date_create`, `date_done`, `title`, DATE_FORMAT(`date_deadline`, '%d.%m.%Y'), `id`, `url_file`   FROM `items` 
                            WHERE `users_id` = ? AND
                            `date_deadline` = CURDATE() ";
                        break;
                    case 'tomorrow':
                        $sql = "SELECT `date_create`, `date_done`, `title`, DATE_FORMAT(`date_deadline`, '%d.%m.%Y'), `id`, `url_file`  FROM `items` 
                            WHERE `users_id` = ? AND
                            `date_deadline` = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
                        break;
                    case 'late':
                        $sql = "SELECT `date_create`, `date_done`, `title`, DATE_FORMAT(`date_deadline`, '%d.%m.%Y'), `id`, `url_file`  FROM `items` 
                          WHERE `users_id` = ? AND
                            `date_deadline` < CURDATE()";
                        break;
                    default:
                        $sql = "SELECT `date_create`, `date_done`, `title`, DATE_FORMAT(`date_deadline`, '%d.%m.%Y'), `id`, `url_file`  FROM `items` 
                          WHERE `users_id` = ?";
                }

            $stmt = mysqli_stmt_init($con);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                $errorBD = showErrorBD($con, false);
            }
            mysqli_stmt_bind_param($stmt, 'i', $userId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_array($result)) {
                $items[] = createItems($row);
            }
        }

        if (!isset($items)) {
            $items = array();
        }
    }

    $content = include_template('templates/index.php', ['itemsForPrint' => $items, 'itemFilter'=>$itemFilter]);
    if (isset($_GET['numb'])) {
        $projectNumb = true;
        foreach ($projects as $project) {
            if ($project['id'] == htmlspecialchars($_GET['numb'])) {
                $projectNumb = false;
                }
        }
        if ($projectNumb) {
            $content = '<h1 class="error-message">error 404 / Такой страницы не существует:(</h1>';
        }
    }

    if (isset($_GET['add']) && !isset($_REQUEST['submit_task'])) {
        $add = htmlspecialchars($_GET['add']);
        $form = showForm($add, isset($taskErrorName)? $taskErrorName:false, isset($taskErrorProject)? $taskErrorProject:false, $projects, isset($repeat)? $repeat:false);
    }

        if (isset($_GET['show_completed'])) {
            if (isset($_COOKIE['show_completed'])) {
                unset($_COOKIE['show_completed']);
                setcookie('show_completed', '', time() - 3600);
            } else {
                setcookie('show_completed', 1, time() + 3600);
            }
            header("Location: index.php");

        }
}

if (!isset($_POST['submit_register'])&&!isset($_POST['submit_login'])) {
    //$guest = include_template('templates/guest.php', []);
    $register = include_template('templates/register.php', []);
    $login = include_template('templates/login.php', []);
}

if (!isset($_SESSION['login'])) {
    if(isset($_GET['log'])) {
        if (htmlspecialchars($_GET['log']) === 'in') {
            $notLog = $login;
        } else if (htmlspecialchars($_GET['log']) === 'register') {
            $notLog = $register;
        }
    }else {
        $notLog = include_template('templates/guest.php', []);;
    }
}
if (isset($_POST['submit_register'])) {
    if(!htmlspecialchars($_POST['email'])) {
        $errorEmail = 'form__input--error';
        $error = true;
    } else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $errorEmailExist = 'Неправильный формат почты';
        $error = true;
    } else {

        $sql = "SELECT `email` FROM `users`
                WHERE `email` = ?";
        $stmt = mysqli_stmt_init($con);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $errorBD = showErrorBD($con, false);
        }
        mysqli_stmt_bind_param($stmt, 's', $_POST['email']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $userEmail = mysqli_fetch_row($result);
        if (isset($userEmail[0])) {
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
        $sql = "SELECT `name` FROM `users`
                WHERE `name` = ?";
        $stmt = mysqli_stmt_init($con);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $errorBD = showErrorBD($con, false);
        }
        mysqli_stmt_bind_param($stmt, 's', $_POST['name']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $userName = mysqli_fetch_row($result);
        if (isset($userName[0])) {
            $errorNameExist = 'Такое имя уже зарегистрировано:(';
            $error = true;
        }
    }
    if (isset($error)) {
        $notLog = include_template('templates/register.php', [
            'errorPassword'=>(isset($errorPassword)? $errorPassword:false),
            'errorName'=>(isset($errorName)? $errorName:false),
            'errorEmail'=>(isset($errorEmail)? $errorEmail:false),
            'errorEmailExist'=>(isset($errorEmailExist)? $errorEmailExist:false),
            'errorNameExist'=>(isset($errorNameExist)? $errorNameExist:false)
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

if (isset($_POST['submit_login'])) {
    if(!htmlspecialchars($_POST['name'])) {
        $taskErrorName = 'form__input--error';
        $error = true;
    }
    if(!htmlspecialchars($_POST['password'])) {
        $taskErrorPassword = 'form__input--error';
        $error = true;
    }
    if (isset($error)) {
        $notLog = include_template('templates/login.php', [
            'taskErrorPassword'=>(isset($taskErrorPassword)? $taskErrorPassword:false),
            'taskErrorName'=>(isset($taskErrorName)? $taskErrorName:false)
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
    print_r("Ошибка есть");
}



$html = include_template('templates/layout.php', [
    'content'=>(isset($content)? $content:false),
    'modal'=>(isset($form['modal'])? $form['modal']:false),
    'projects'=>(isset($projects)? $projects:false),
    'title'=>(isset($title)? $title:false),
    'numb'=>(isset($numb)? $numb:false),
    'overlay'=>(isset($form['overlay'])? $form['overlay']:false),
    'notLog'=>(isset($notLog)? $notLog:false),
    'session'=>(isset($session)? $session:false),
    'con'=>(isset($con)? $con:false),
    'userId'=>(isset($userId)? $userId:false)]);

print_r($html);
