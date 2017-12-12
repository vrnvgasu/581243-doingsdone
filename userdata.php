<?php

/**Аутентификация пользователя
* @param $User array данные от пользователя
* @param $con mysqli ресурс соединения
* @return string
    */
function authentication($User, $con) {
    $sql = "SELECT * FROM `users`";
    $result = mysqli_query($con, $sql);
    $errorBD = showErrorBD($con, $result);
    $usersRow = mysqli_fetch_all($result);

    foreach ($usersRow as $user) {
        if (($user[3] === $User['name']) &&
            password_verify($User['password'], $user[4]) ) {

            $_SESSION['login'] = true;
            $_SESSION['name'] = $user[3];
            header("Location: index.php");
        }
    }
    $notLog = include_template('templates/login.php', [
                'taskErrorPassword'=>'form__input--error',
                'taskErrorName'=>'form__input--error',
                'ErrorLogIn'=>true
            ]);
    return $notLog;
}