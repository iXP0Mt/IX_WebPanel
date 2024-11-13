<div class="container mt-5">
    <h1>Управление плагинами</h1>
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
    <table class="table table-sm">
        <thead>
        <tr>
            <th>Модуль</th>
            <th>Название</th>
            <th>Статус</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $listNonInitPlugins = $data['content']['non_init_plugins'] ?? [];
        foreach ($listNonInitPlugins as $plugin): ?>
            <tr>
                <td><?= $plugin['tech_name'] ?></td>
                <td><?= $plugin['name'] ?></td>
                <td>Не инициализирован. (<a href="/plugin/init/<?= $plugin['tech_name'] ?>">Настроить</a>)</td>
            </tr>
        <?php endforeach; ?>
        <?php
        $listInitPlugins = $data['content']['init_plugins'] ?? [];
        foreach ($listInitPlugins as $plugin): ?>
            <tr>
                <td><?= $plugin['techName'] ?></td>
                <td><?= $plugin['name'] ?></td>
                <td><?= $plugin['enabled'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
