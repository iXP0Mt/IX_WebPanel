<div class="container mt-5">
    <?php
    if (!empty($data['content']['error_msg'])) { ?>
        <div class="alert alert-danger mb-3">
            <?= $data['content']['error_msg'] ?>
        </div>
    <?php }
    if (!isset($data['content']['plugin'])) die;
    $plugin = $data['content']['plugin'];
    ?>
    <h1><?= $plugin['name'] ?></h1>
    <?php include 'plugins/' . $plugin['dir'] . '/index.php'; ?>
</div>

