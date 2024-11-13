<?php
//include 'plugins/'.$data['content'][''].'/index.php';
?>

<div class="container mt-5">
    <h1>Инициализация плагина</h1>
    <?php
    if(!empty($data['content']['error_msg'])) { ?>
        <div class="alert alert-danger mb-3">
            <?= $data['content']['error_msg'] ?>
        </div>
    <?php } ?>
    <?php
    $plugin = $data['content']['plugin'] ?? [];
    if (empty($plugin)) { ?>
        <div>Ошибка инициализации плагина</div>
    <?php } else { ?>
    <p><b>Техническое название:</b> <?= $plugin['tech_name'] ?? null ?></p>
    <p><b>Версия:</b> <?= $plugin['version'] ?? null ?></p>
    <p><b>Название плагина:</b> <?= $plugin['name'] ?? null ?></p>

    <form action="" method="post">
        <?php
        $settings = $plugin['settings'];
        foreach ($settings as $setting => $desc): ?>
            <div class="mb-3">
                <label for="<?= $setting ?>"><?= $desc ?></label>
                <input type="text" class="form-control" name="<?= $setting ?>" maxlength="32">
            </div>
        <?php endforeach; } ?>
        <button type="submit" class="btn btn-primary">Далее</button>
    </form>
</div>
