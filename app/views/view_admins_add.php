<div class="container mt-5">
    <h1>Добавление нового админа</h1>
    <?php
    if(!empty($data['content']['error_msg'])) { ?>
        <div class="alert alert-danger mb-3">
            <?= $data['content']['error_msg'] ?>
        </div>
    <?php } ?>
    <?php
    if(!empty($data['content']['success_msg'])) { ?>
        <div class="alert alert-success mb-3">
            <?= $data['content']['success_msg'] ?>
        </div>
    <?php } ?>
    <form method="post" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Логин</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="password_confirm" class="form-label">Подтверждение пароля</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
        </div>
        <div class="mb-3">
            <label for="flags" class="form-label">Флаги доступа</label>
            <input type="text" class="form-control" id="flags" name="flags" oninput="validateInputFlags(this)">
        </div>
        <button type="submit" class="btn btn-primary">Далее</button>
    </form>
</div>

<script>
    function validateInputFlags(input) {
        input.value = input.value.replace(/[^a-z]/g, '');
    }
</script>