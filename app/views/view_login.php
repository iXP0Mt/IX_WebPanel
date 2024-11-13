<head><?php include_once 'public/css/bootstrap.php'; ?></head>

<div class="container mt-5">
    <h1>Авторизация</h1>
    <?php
    if(!empty($data['content']['error_msg'])) { ?>
        <div class="alert alert-danger mb-3">
            <?= $data['content']['error_msg'] ?>
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
        <button type="submit" class="btn btn-primary">Далее</button>
    </form>
</div>