<div class="container mt-5">
    <div class="card-body text-center">
        <h4 class="card-title text-danger">Вы уверены, что хотите удалить администратора <?= empty($data['content']['login']) ? "NULL" : $data['content']['login'] ?>?</h4>
        <p class="card-text">Это действие нельзя отменить.</p>

        <form method="post" action="">
            <div class="d-flex justify-content-center gap-3 mt-4">
                <button type="submit" name="delete" class="btn btn-danger">Удалить!</button>
                <a href="/admins" class="btn btn-secondary">Назад</a>
            </div>
        </form>
    </div>
</div>
