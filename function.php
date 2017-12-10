<?php
function include_template($dirTemplate, $arrTemplate) {
    ob_start();
    require_once "$dirTemplate";
    return ob_get_clean();
}

function showErrorBD($con, $result) {
    if ( !$result ) {
       return $errorBD = mysqli_error($con);
    }
}
function showErrorBDInFunction($con, $result) {
    if ( !$result ) {
        $arrTemplate['errorBD'] = mysqli_error($con);
        require_once "templates/error.php";
    }
}

function createItems($rows) {
    $i = 0;
    foreach ($rows as $item) {
        $items[$i]['title'] = $item[2];
        $items[$i]['date'] = $item[3];
        $items[$i]['category'] = $item[6];
        $items[$i]['id'] = $item[4];
        if ($item[1]) {
            $items[$i]['state'] = true;
        } else {
            $items[$i]['state'] = false;
        }
        $items[$i]['file'] = $item[5];

        $i++;
    }

    return $items;
}

function countItemsInProject ($con, $userId) {
    $sql = "SELECT `projects_id`, COUNT(*) FROM `items`
        WHERE `users_id` = ?
        GROUP BY `projects_id`";
    $stmt = mysqli_stmt_init($con);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        showErrorBDInFunction($con, false);
    }
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($result);

    $i=0;
    foreach ($rows as $row) {
        $count[$i]['projectId'] = $row[0];
        $count[$i]['itemsCount'] = $row[1];
        $i++;
    }


   return $count;
}

function showForm($add, $taskErrorName, $taskErrorProject, $projects, $repeat) {
    $overlay = 'overlay';
    $modal = include_template('templates/form.php',
        ['add'=>$add,
            'taskErrorProject'=>$taskErrorProject,
            'taskErrorName'=>$taskErrorName,
            'projects'=>$projects,
            'repeat'=>$repeat]);
    $form = ['overlay'=>$overlay, 'modal'=>$modal];
    return $form;
}

function addNewTask($title, $url_file = false, $date = false, $users_id, $projects_id, $con) {
    $sql = "INSERT INTO `items` (`id`, `date_create`, `date_done`, `title`, `url_file`, `date_deadline`, `users_id`, `projects_id`)
            VALUES (NULL, NOW(), NULL, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssii', $title,  $url_file, $date, $users_id, $projects_id);
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
};

function addNewProject($newProject, $con, $userId) {
    $project = mysqli_real_escape_string($con, $newProject);
    $id = mysqli_real_escape_string($con, $userId);
    $sql = "SELECT `project` FROM `projects` 
            WHERE `project` = '" . $project . "' AND
            `users_id` = '" . $id . "'";
    $result = mysqli_query($con, $sql);
    showErrorBDInFunction($con, $result);
    $count = mysqli_num_rows($result);
    if ($count) {
        return 'У вас уже есть такой проект';
    }

    $sql = "INSERT INTO `projects` (`id`, `project`, `users_id`)
            VALUES (NULL, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $newProject, $userId);
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
}


function addFile () {
    if (substr($_FILES['preview']['name'], -3) == 'php') {
        $file_name = getmypid() .date('U'). basename($_FILES['preview']['name'], 'php') . 'txt';
    } else if (substr($_FILES['preview']['name'], -2) == 'js') {
        $file_name = getmypid() .date('U'). basename($_FILES['preview']['name'], 'js') . 'txt';
    } else {
        $file_name = getmypid() .date('U'). $_FILES['preview']['name'];
    }

    $file_path = __DIR__ . '\\uploads\\' . $file_name;
    move_uploaded_file($_FILES['preview']['tmp_name'], $file_path);
    return $file_path;
}
