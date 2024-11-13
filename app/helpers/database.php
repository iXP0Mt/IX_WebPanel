<?php

namespace app\helpers;

use PDO;
use PDOException;

class Database
{
    static private function pdo(): PDO
    {
        static $pdo;

        if (!$pdo) {
            $config = include 'config/database.php';
            $dsn = 'mysql:dbname=' . $config['db_name'] . ';host=' . $config['db_host'];
            try {
                $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("ERROR #0002: ".$e->getMessage());
            }
        }
        return $pdo;
    }

    /**
     * Получает количество существующих админов из базы данных.
     *
     * @return int|null Возвращает число админов, либо null при ошибке запроса.
     */
    public static function selectValueAdmins(): ?int
    {
        try {
            $stmt = self::pdo()->prepare('SELECT COUNT(*) as count FROM WP_Admins;');
            $stmt->execute();
            if ($stmt->rowCount()) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['count'];
            }
        } catch (PDOException $e) {
            error_log("ERROR: selectValueAdmins" . $e->getMessage());
        }

        return null;
    }

    /**
     * Создаёт новую запись админа в таблице базы данных.
     *
     * @param string $username
     * @param string $password
     * @param string $flags
     * @return int|null Возвращает ID добавленного админа или null, если ошибка.
     */
    public static function insertAdmin(string $username, string $password, string $flags): ?int
    {
        try {
            $stmt = self::pdo()->prepare('INSERT INTO WP_Admins (login, password, flags) VALUES (:username, :password, :flags);');
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':password', $password);
            $stmt->bindValue(':flags', $flags);
            $stmt->execute();

            return self::pdo()->lastInsertId();
        } catch (PDOException $e) {
            error_log("ERROR: insertAdmin" . $e->getMessage());
        }

        return null;
    }


    /**
     * Получает список админов из базы данных.
     *
     * @param int $adminsPerPage Количество получаемых админов за раз. (0 - Получить всех админов)
     * @param int $numberPage Номер страницы, когда $adminsPerPage не равен 0. (0 - то же, что и $numberPage = 1)
     * @return array|null Возвращает массив с админами, либо null, если ошибка при получении.
     */
    public static function selectAdmins(int $adminsPerPage = 0, int $numberPage = 0): ?array
    {
        if($numberPage == 0) $numberPage = 1;
        $offset = ($numberPage - 1) * $adminsPerPage;

        try {
            $stmt = self::pdo()->prepare("SELECT id,login,flags FROM WP_Admins LIMIT :adminsPerPage OFFSET :offset");
            $stmt->bindValue("adminsPerPage", $adminsPerPage, PDO::PARAM_INT);
            $stmt->bindValue("offset", $offset, PDO::PARAM_INT);
            $stmt->execute();

            $admins = [];
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $admins[] = $row;
                }
            }
        } catch (PDOException $e) {
            error_log("ERROR: selectAdmins" . $e->getMessage());
            return null;
        }

        return $admins;
    }

    /**
     * Получает информацию об админе по его ID из базы данных.
     *
     * @param int $adminId
     * @return array|null Возвращает null если ошибка при запросе.
     */
    public static function selectAdminById(int $adminId): ?array
    {
        try {
            $stmt = self::pdo()->prepare("SELECT * FROM WP_Admins WHERE id = :id");
            $stmt->bindValue(':id', $adminId);
            $stmt->execute();

            $admin = [];
            if ($stmt->rowCount()) {
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log("ERROR: selectAdminById" . $e->getMessage());
            return null;
        }

        return $admin;
    }

    /**
     * Получает информацию об админе по его логину из базы данных.
     *
     * @param string $login
     * @return array|null Возвращает null если ошибка при запросе.
     */
    public static function selectAdminByLogin(string $login): ?array
    {
        try {
            $stmt = self::pdo()->prepare("SELECT id, password FROM WP_Admins WHERE login = :login");
            $stmt->bindValue(':login', $login);
            $stmt->execute();

            $admin = [];
            if ($stmt->rowCount()) {
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log("ERROR: selectAdminByLogin" . $e->getMessage());
            return null;
        }

        return $admin;
    }

    /**
     * Обновляет данные админы по входящим аргументам функции.
     *
     * @param int $adminId
     * @param string|null $login
     * @param string|null $hashPassword
     * @param string|null $flags
     * @return bool|null Возвращает true если запрос успешен.
     */
    public static function updateAdminById(int $adminId, string $login = null, string $hashPassword = null, string $flags = null): ?bool
    {
        if(empty($login) && empty($hashPassword) && empty($flags)) {
            return null;
        }

        $qParts = [];
        if(!empty($login)) $qParts[] = "login = '$login'";
        if(!empty($hashPassword)) $qParts[] = "password = '$hashPassword'";
        if(!empty($flags)) $qParts[] = "flags = '$flags'";
        $query = "UPDATE WP_Admins SET ".implode(', ', $qParts)." WHERE id = '$adminId'";

        try {
            $stmt = self::pdo()->prepare($query);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("ERROR: updateAdminById" . $e->getMessage());
            return null;
        }
    }

    /**
     * Удаляет админа из базы данных по его ID.
     *
     * @param int $adminId
     * @return bool|null Возвращает true если запрос успешен.
     */
    public static function deleteAdminById(int $adminId): ?bool
    {
        try {
            $stmt = self::pdo()->prepare("DELETE FROM WP_Admins WHERE id = :adminId");
            $stmt->bindValue(':adminId', $adminId);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("ERROR: deleteAdminById" . $e->getMessage());
            return null;
        }
    }
}