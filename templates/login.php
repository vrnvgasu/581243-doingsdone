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
                <p class="content__side-info">Если у вас нет аккаунта, зарегистрируйтесь на сайте</p>

                <a class="button button--transparent content__side-button" href="?log=register">Регистрация</a>
            </section>

            <main class="content__main">
                <h2 class="content__main-heading">Войти в аккаунт</h2>
                <?php if ($arrTemplate['ErrorLogIn']): ?>
                <p class="error-message">Данные указаны неверно:(</p>
                <?php endif; ?>

                <form class="form" action="index.php" method="post">
                    <div class="form__row">
                        <label class="form__label" for="name">Имя <sup>*</sup></label>

                        <input class="form__input <?=$arrTemplate['taskErrorName']?>" type="text" name="name" id="name" value="" placeholder="Введите имя">

                        <p class="form__message <?php if (!$arrTemplate['taskErrorName']) echo " hidden"; ?>">Имя введёно некорректно</p>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="password">Пароль <sup>*</sup></label>

                        <input class="form__input <?=$arrTemplate['taskErrorPassword']?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">

                        <p class="form__message <?php if (!$arrTemplate['taskErrorPassword']) echo " hidden"; ?>">Пароль введён некорректно</p>
                    </div>

                    <div class="form__row form__row--controls">
                        <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>

                        <input class="button" type="submit" name="submit_login" value="Войти в аккаунт">
                    </div>
                </form>
            </main>
        </div>
    </div>
</div>