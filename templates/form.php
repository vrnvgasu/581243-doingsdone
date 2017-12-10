<div class="modal"<?=($arrTemplate['add'] !== 'task')? " hidden":''?> >
    <a href="/index.php"><button class="modal__close" type="button" name="button">Закрыть</button></a>

    <h2 class="modal__heading">Добавление задачи</h2>

    <form class="form"  action="index.php" enctype="multipart/form-data" method="post">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input <?=$arrTemplate['taskErrorName']?>" type="text" name="name" id="name" value="" placeholder="Введите название">
        </div>

        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select <?=$arrTemplate['taskErrorProject']?>" name="project" id="project">
                <?php foreach ($arrTemplate['projects'] as $project): ?>
                <option value="<?=($project['project'] !== 'Все')?$project['project']:""?>"><?=$project['project']?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>

            <input class="form__input form__input--date" type="date" name="date" id="date" value="" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
        </div>

        <div class="form__row">
            <label class="form__label" for="preview">Файл</label>

            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="preview" id="preview" value="">

                <label class="button button--transparent" for="preview">
                    <span>Выберите файл</span>
                </label>
            </div>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="submit_task" value="Добавить">
        </div>
    </form>
</div>

<div class="modal"<?=($arrTemplate['add'] !== 'project')? "hidden":''?> >
    <a href="/index.php"><button class="modal__close" type="button" name="button">Закрыть</button></a>

    <h2 class="modal__heading">Добавление проекта</h2>

    <form class="form"  action="index.php" method="post">
        <p class="error-message"><?=$arrTemplate['repeat']?></p>
        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input <?=$arrTemplate['taskErrorName']?>" type="text" name="name" id="project_name" value="" placeholder="Введите название проекта">
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="submit_project" value="Добавить">
        </div>
    </form>
</div>
