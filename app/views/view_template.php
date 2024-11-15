<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Главная</title>
    <!--<link rel="stylesheet" type="text/css" href="/css/style.css" />-->
    <script src="/js/jquery-1.6.2.js" type="text/javascript"></script>
    <?php include_once 'public/css/bootstrap.php'; ?>
    <style>
        .sidebar {
            min-height: 100vh;
            border-right: 1px solid #ddd;
        }
    </style>
</head>
<header class="navbar navbar-expand-lg navbar-light bg-light" style="border-bottom: 1px solid #ddd; padding-left: 21rem;">
    <nav class="navbar-brand">
        <a href="/" class="navbar-brand">Панель</a>
    </nav>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <?php
            if (isset($data['template']['isVisibleItems']) && $data['template']['isVisibleItems'] === true) {?>
                <li class="nav-item">
                    <a class="nav-link" href="/admins">Управление админами</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/plugin">Управление плагинами</a>
                </li>
            <?php } ?>
            <li class="nav-item">
                <a class="nav-link" href="/exit">Выход</a>
            </li>
        </ul>
    </div>
</header>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="sidebar-sticky pt-3">
                <h4>Модули</h4>
                <ul class="nav flex-column">
                    <?php
                    $listPlugins = $data['template']['plugins'] ?? [];
                    foreach ($listPlugins as $plugin) {
                        ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="/plugin/<?= $plugin['id']; ?>">
                                <?php echo $plugin['name']; ?>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </nav>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            <div>
                <?php include $viewContentName; ?>
            </div>
        </main>
    </div>
</div>


</body>
</html>