<?php

// пользователи для аутентификации
$users = [
    [
        'email' => 'ignat.v@gmail.com',
        'name' => 'Игнат',
        'password' => '$2y$10$bwvf3UtZ/M1mf7GMLFykXulFin7X1vUsrdHWJREanNAUjWONTWDIK'
    ],
    [
        'email' => 'kitty_93@li.ru',
        'name' => 'Леночка',
        'password' => '$2y$10$tFCdmGLLKZzajzqYmoGql.hRg2UzuX5unEuClNW5QaYtrhfqIqDYC'
    ],
    [
        'email' => 'warrior07@mail.ru',
        'name' => 'Руслан',
        'password' => '$2y$10$mBmmtofi0pUU.fLmUyfbQO.s2H5TZfii1jAXaff2tA6ILFdYu/AG6'
    ]
];

function authentication($User) {
    global $users;
    foreach ($users as $user) {
        if (($user['name'] === $User['name']) &&
            password_verify($User['password'], $user['password']) ) {

            $_SESSION['login'] = true;
            $_SESSION['name'] = $user['name'];
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