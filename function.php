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

function createItems($rows) {
    $i = 0;
    foreach ($rows as $item) {
        $items[$i]['title'] = $item[2];
        $items[$i]['date'] = $item[3];
        $items[$i]['category'] = $item[5];
        $items[$i]['id'] = $item[4];
        if ($item[1]) {
            $items[$i]['state'] = true;
        } else {
            $items[$i]['state'] = false;
        }

        $i++;
    }

    return $items;
}

function countItemsInProject ($projectName, $con, $userId) {
    if ($projectName === 'Все') {
        $sql = "SELECT `title` FROM `items` 
        WHERE `users_id` = '" . $userId . "'";
        $result = mysqli_query($con, $sql);
        $count = mysqli_num_rows($result);

    } else {
        $project = mysqli_real_escape_string($con, $projectName);

        $sql = "SELECT `title` FROM `items` `i`
        JOIN `projects` `p`
        ON `i`.`projects_id` =  `p`.`id`
        WHERE `p`.`project` = '" . $project . "' AND
        `i`.`users_id` = '" . $userId . "'";
        $result = mysqli_query($con, $sql);
        $count = mysqli_num_rows($result);
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
    $sql = "SELECT `project` FROM `projects` 
            WHERE `project` = '" . $project . "' AND
            `users_id` = '" . $userId . "'";
    $result = mysqli_query($con, $sql);
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
    $file_name = rand(1,10000).rand(1,10000).$_FILES['preview']['name'];
    $file_path = __DIR__ . '\\uploads\\' . $file_name;

    move_uploaded_file($_FILES['preview']['tmp_name'], $file_path);

    return $file_path;
}


