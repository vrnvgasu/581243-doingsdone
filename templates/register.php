<body><!--class="overlay"-->
<h1 class="visually-hidden">Дела в порядке</h1>

<div class="page-wrapper">
    <div class="container container--with-sidebar">
        <header class="main-header">
            <a href="#">
                <img src="../img/logo.png" width="153" height="42" alt="Логитип Дела в порядке">
            </a>
        </header>

        <div class="content">
            <section class="content__side">
                <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

                <a class="button button--transparent content__side-button" href="?log=in">Войти</a>
            </section>

            <main class="content__main">
                <h2 class="content__main-heading">Регистрация аккаунта</h2>

                <form class="form" action="index.php" method="post">
                    <p class="error-message"><?=isset($arrTemplate['errorNameExist'])? $arrTemplate['errorNameExist']:''?></p>
                    <p class="error-message"><?=isset($arrTemplate['errorEmailExist'])? $arrTemplate['errorEmailExist']:''?></p>
                    <div class="form__row">
                        <label class="form__label" for="email">E-mail <sup>*</sup></label>

                        <input class="form__input <?=$arrTemplate['errorEmail']?>" type="email" name="email" id="email" value="" placeholder="Введите e-mail">

                        <p class="form__message<?=(!$arrTemplate['errorEmail'])? " hidden":""?>
                        ">E-mail введён некорректно</p>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="password">Пароль <sup>*</sup></label>

                        <input class="form__input <?=$arrTemplate['errorPassword']?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">

                        <p class="form__message<?=(!$arrTemplate['errorPassword'])? " hidden":""?>
                        ">Пароль введён некорректно</p>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="name">Имя <sup>*</sup></label>

                        <input class="form__input <?=$arrTemplate['errorName']?>" type="text" name="name" id="name" value="" placeholder="Введите имя">

                        <p class="form__message<?=(!$arrTemplate['errorName'])? " hidden":""?>
                        ">Имя введёно некорректно</p>
                    </div>

                    <div class="form__row form__row--controls">
                        <p class="error-message <?=(!$arrTemplate['errorName'] || !$arrTemplate['errorEmail'])? " hidden":""?>">Пожалуйста, исправьте ошибки в форме</p>

                        <input class="button" type="submit" name="submit_register" value="Зарегистрироваться">
                    </div>
                </form>
            </main>
        </div>
    </div>
</div>