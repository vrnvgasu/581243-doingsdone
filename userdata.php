<?php

// пользователи для аутентификации
$users = [
    [
        'email' => 'ignat.v@gmail.com',
        'name' => 'Игнат',
        'password' => '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka'
    ],
    [
        'email' => 'kitty_93@li.ru',
        'name' => 'Леночка',
        'password' => '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa'
    ],
    [
        'email' => 'warrior07@mail.ru',
        'name' => 'Руслан',
        'password' => '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW'
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