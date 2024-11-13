<div class="container">
    <h1>Изменение данных <?= empty($data['content']['login']) ? "NULL" : $data['content']['login'] ?></h1>
    <?php
    if(!empty($data['content']['error_msg'])) { ?>
        <div class="alert alert-danger mb-3">
            <?= $data['content']['error_msg'] ?>
        </div>
    <?php } ?>
    <form method="post" action="">
        <div class="row">
            <div class="col mb-3">
                <label for="username" class="form-label">Логин</label>
                <input type="text" class="form-control" name="username" value="<?= empty($data['content']['login']) ? "" : $data['content']['login'] ?>">
            </div>
        </div>
        <div class="row">
            <div class="col mb-3">
                <label for="password" class="form-label">Новый пароль</label>
                <input type="password" class="form-control" name="password">
            </div>
            <div class="col mb-3">
                <label for="password_confirm" class="form-label">Подтверждение пароля</label>
                <input type="password" class="form-control" name="password_confirm">
            </div>
        </div>
        <div class="row">
            <div class="col mb-3">
                <label for="flags" class="form-label">Флаги доступа</label>
                <input type="text" class="form-control" name="flags" id="flagsInput" value="<?= empty($data['content']['flags']) ? "" : $data['content']['flags'] ?>" oninput="validateInputFlags(this)">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Подтвердить</button>
    </form>
</div>

<script>
    function validateInputFlags(input) {
        input.value = input.value.replace(/[^a-z]/g, '');
    }
</script>

