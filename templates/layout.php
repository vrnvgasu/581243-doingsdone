<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title><?=$arrTemplate['title']?></title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<?php if(!$_SESSION['login']): ?>
    <?=$arrTemplate['notLog']?>
<?php else: ?>
    <body class="<?=$arrTemplate['overlay']?>">
    <h1 class="visually-hidden">Дела в порядке</h1>
    <div class="page-wrapper">
        <div class="container container--with-sidebar">
            <header class="main-header">
                <a href="#">
                    <img src="img/logo.png" width="153" height="42" alt="Логотип Дела в порядке">
                </a>
            <div class="main-header__side">
                <a class="main-header__side-item button button--plus" href="?add=task">Добавить задачу</a>

                <div class="main-header__side-item user-menu">
                    <div class="user-menu__image">
                        <img src="img/user-pic.jpg" width="40" height="40" alt="Пользователь">
                    </div>

                    <div class="user-menu__data">
                        <p><?=$_SESSION['name']?></p>
                        <a href="/logout.php">Выйти</a>
                    </div>
                </div>
            </div>
        </header>

        <div class="content">
            <section class="content__side">
                <h2 class="content__side-heading">Проекты</h2>

                <nav class="main-navigation">
                    <ul class="main-navigation__list">
                        <?php $i = 0;?>
                        <?php foreach ($arrTemplate['projects'] as $project): ?>
                        <li class="main-navigation__list-item<?=($arrTemplate['projects'][0] == $project)? ' main-navigation__list-item--active':''?>">
                            <a class="main-navigation__list-item-link" href="<?='?numb='.$i++?>"><?=$project?></a>
                            <span class="main-navigation__list-item-count"><?=countItemsInProject ($project, $arrTemplate['con'],  $arrTemplate['userId'])?></span>
                        </li>

                        <?php endforeach; ?>
                    </ul>
                </nav>

                <a class="button button--transparent button--plus content__side-button" href="?add=project">Добавить проект</a>
            </section>

            <?=$arrTemplate['content']?>
            
        </div>
    </div>
</div>

<?php endif; ?>

<footer class="main-footer">
    <div class="container">
        <div class="main-footer__copyright">
            <p>© 2017, «Дела в порядке»</p>

            <p>Веб-приложение для удобного ведения списка дел.</p>
        </div>

        <a class="main-footer__button button button--plus" href="?add=task">Добавить задачу</a>

        <div class="main-footer__social social">
            <span class="visually-hidden">Мы в соцсетях:</span>
            <a class="social__link social__link--facebook" href="#">Facebook
                <svg width="27" height="27" viewBox="0 0 27 27" xmlns="http://www.w3.org/2000/svg">
            </a><span class="visually-hidden">
        ,</span>
            <a class="social__link social__link--twitter" href="#">Twitter
                <svg width="27" height="27" viewBox="0 0 27 27" xmlns="http://www.w3.org/2000/svg">
            </a><span class="visually-hidden">
        ,</span>
            <a class="social__link social__link--instagram" href="#">Instagram
                <svg width="27" height="27" viewBox="0 0 27 27" xmlns="http://www.w3.org/2000/svg">
            </a>
            <span class="visually-hidden">,</span>
            <a class="social__link social__link--vkontakte" href="#">Вконтакте
                <svg width="27" height="27" viewBox="0 0 27 27" xmlns="http://www.w3.org/2000/svg">
            </a>
        </div>

        <div class="main-footer__developed-by">
            <span class="visually-hidden">Разработано:</span>

            <a href="https://htmlacademy.ru/intensive/php">
                <img src="img/htmlacademy.svg" alt="HTML Academy" width="118" height="40">
            </a>
        </div>
    </div>
</footer>

<?=$arrTemplate['modal']?>

</body>
</html>