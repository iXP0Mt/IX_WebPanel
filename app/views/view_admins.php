<div class="container mt-5">
    <h1>Управление админами</h1>
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
    <a href="admins/add" class="btn btn-success">
        + Новый админ
    </a>
    <table class="table table-sm">
        <thead>
        <tr>
            <th>ID</th>
            <th>Login</th>
            <th>Flags</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $listAdmins = empty($data['content']['listAdmins']) ? [] : $data['content']['listAdmins'];
        foreach ($listAdmins as $admin): ?>
            <tr>
                <td>
                    <?php echo $admin['id'] ?>
                </td>
                <td>
                    <?php echo $admin['login'] ?>
                </td>
                <td>
                    <?php echo $admin['flags'] ?>
                </td>
                <td>
                    <a href="admins/edit/<?= $admin['id'] ?>">Изменить</a>
                </td>
                <td>
                    <a href="admins/delete/<?= $admin['id'] ?>">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>