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
            <th colspan="2">Статус</th>
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
                <?php
                switch ($plugin['enabled']) {
                    case 0: {
                        $str = "OFF";
                        $color = 'text-danger';
                        $manageable = true;
                        break;
                    }
                    case 1: {
                        $str = "ON";
                        $color = "text-success";
                        $manageable = true;
                        break;
                    }
                    default: {
                        $str = $plugin['enabled'];
                        $color = 'text-danger';
                        $manageable = false;
                        break;
                    }
                }
                ?>
                <td class="<?= $color ?>"><?= $str ?></td>
                <?php if($manageable) { ?>
                    <td><a href="/plugin/edit/<?= $plugin['id'] ?>">Изменение</a></td>
                <?php } ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
