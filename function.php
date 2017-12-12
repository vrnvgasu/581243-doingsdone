<?php
/**Функция шаблонизации
 * @param $dirTemplate string путь до шаблона
 * @param $arrTemplate array переменные с данными
 * @return string возвращает шаблон с подставленными значениями
 */
function include_template($dirTemplate, $arrTemplate) {
    ob_start();
    require_once "$dirTemplate";
    return ob_get_clean();
}

/**Выводит ошибку БД на страницу
 * @param $con mysqli ресурс соединения
 * @param $result bool
 * @return string
 */
function showErrorBD($con, $result) {
    if ( !$result ) {
       return $errorBD = mysqli_error($con);
    }
}
/**Выводит ошибку БД на страницу из другой функции
 * @param $con mysqli ресурс соединения
 * @param $result bool
 * @return string
 */
function showErrorBDInFunction($con, $result) {
    if ( !$result ) {
        $arrTemplate['errorBD'] = mysqli_error($con);
        require_once "templates/error.php";
    }
}

/**Создает массив с заданиями
 * @param $row array строка запроса из БД
 * @return array
 */
function createItems($row) {
    $item = [];
    $item['title'] = $row['title'];
    $item['date'] =  $row["DATE_FORMAT(`date_deadline`, '%d.%m.%Y')"];
    $item['category'] = isset($row['project'])?$row['project']:false;
    $item['file'] = $row['url_file'];
    $item['id'] = $row['id'];
    if ($row['date_done']) {
        $item['state'] = true;
    } else {
        $item['state'] = false;
    }

    return $item;
}

/**Возарщает количество задач по категории
 * @param $con mysqli ресурс соединения
 * @param $userId integer id пользователя
 * @return array
 */
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
        //var_dump($row);
    }
    if (isset($count)) {
        return $count;
    }
}

/**Выводит форму для добавления задачи/проекта
 * @param $add string тип формы
 * @param $taskErrorName string класс, указывающий на ошибку
 * @param $taskErrorProject string класс, указывающий на ошибку
 * @param $projects array существующие проекты
 * @param $repeat string название проекта
 * @return string
 */
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

/**Добавление в БД новой задачи
 * @param $title string название задачи
 * @param $url_file string ссылка на файл задачи
 * @param $date string дедлайн задачи
 * @param $users_id integer id пользователя
 * @param $projects_id integer id проекта
 * @param $con mysqli ресурс соединения
 */
function addNewTask($title, $url_file = false, $date = false, $users_id, $projects_id, $con) {
    $sql = "INSERT INTO `items` (`id`, `date_create`, `date_done`, `title`, `url_file`, `date_deadline`, `users_id`, `projects_id`)
            VALUES (NULL, NOW(), NULL, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssii', $title,  $url_file, $date, $users_id, $projects_id);
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
};

/**Добавление в БД нового проекта
 * @param $newProject string название проекта
 * @param $con mysqli ресурс соединения
 * @param $userId integer id пользователя
 */
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

/**Проверка и сохранение файла задачи на сервере
 *
 */
function addFile () {
    if (substr($_FILES['preview']['name'], -3) == 'php') {
        $file_name = getmypid() .date('U'). basename($_FILES['preview']['name'], 'php') . 'txt';
    } else if (substr($_FILES['preview']['name'], -2) == 'js') {
        $file_name = getmypid() .date('U'). basename($_FILES['preview']['name'], 'js') . 'txt';
    } else {
        $file_name = getmypid() .date('U'). $_FILES['preview']['name'];
    }

    if (!is_dir('uploads')) {
        mkdir ('uploads');
    }

    $file_path = __DIR__ . '/uploads/' . $file_name;

    move_uploaded_file($_FILES['preview']['tmp_name'], $file_path);
    return $file_path;
}
