<div class="container mt-5">
    <h1>Изменение плагина</h1>
    <?php
    if(!empty($data['content']['error_msg'])) { ?>
        <div class="alert alert-danger mb-3">
            <?= $data['content']['error_msg'] ?>
        </div>
    <?php } ?>
    <?php
    $plugin = $data['content']['plugin'] ?? [];
    if (empty($plugin)) { ?>
        <div>Ошибка загрузки плагина</div>
    <?php } else { ?>
    <p><b>Техническое название:</b> <?= $plugin['tech_name'] ?? null ?></p>
    <p><b>Версия:</b> <?= $plugin['version'] ?? null ?></p>
    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label"><b>Название плагина:</b></label>
            <input type="text" class="form-control" id="name" name="name" value="<?= $plugin['name'] ?? '' ?>" required>
        </div>
        <?php
        $settings = $plugin['settings'];
        foreach ($settings as $key => $setting): ?>
            <div class="mb-3">
                <label for="<?= $key ?>"><?= $setting['description'] ?></label>
                <input type="text" class="form-control" name="<?= $key ?>" maxlength="32" value="<?= $setting['value'] ?>" oninput="validateInputFlags(this)">
            </div>
        <?php endforeach; } ?>
        <button type="submit" class="btn btn-primary">Далее</button>
    </form>
</div>

<script>
    function validateInputFlags(input) {
        input.value = input.value.replace(/[^a-z]/g, '');
    }
</script>