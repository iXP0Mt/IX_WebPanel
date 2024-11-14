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

    /**
     * Получает список плагинов, которые занесены в базу данных
     *
     * @return array|null
     */
    public static function selectPlugins(): ?array
    {
        try {
            $stmt = self::pdo()->prepare("SELECT * FROM WP_Modules");
            $stmt->execute();

            $plugins = [];
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $row['settings'] = json_decode($row['settings'], true);
                    $plugins[] = $row;
                }
            }
        } catch (PDOException $e) {
            error_log("ERROR: selectPlugins" . $e->getMessage());
            return null;
        }

        return $plugins;
    }

    /**
     * Получает плагин по его техническому названию.
     *
     * @param string $techName
     * @return array|null
     */
    public static function selectPluginByTechName(string $techName): ?array
    {
        try {
            $stmt = self::pdo()->prepare("SELECT * FROM WP_Modules WHERE techName = :techName");
            $stmt->bindValue(":techName", $techName);
            $stmt->execute();

            $plugin = [];
            if ($stmt->rowCount()) {
                $plugin = $stmt->fetch(PDO::FETCH_ASSOC);
                $plugin['settings'] = json_decode($plugin['settings'], true);
            }
        } catch (PDOException $e) {
            error_log("ERROR: selectPluginByTechName" . $e->getMessage());
            return null;
        }

        return $plugin;
    }

    /**
     * Создаёт новую запись о плагине в базу данных, возвращая ID созданной записи.
     *
     * @param string $techName
     * @param string $name
     * @param string $version
     * @param string $settings
     * @return int|null Возвращает ID нового плагина, null если ошибка.
     */
    public static function insertPlugin(string $techName, string $name, string $version, string $settings): ?int
    {
        try {
            $stmt = self::pdo()->prepare('INSERT INTO WP_Modules(techName,name,version,settings) VALUES (:techName, :name, :version, :settings)');
            $stmt->bindValue(":techName", $techName);
            $stmt->bindValue(":name", $name);
            $stmt->bindValue(":version", $version);
            $stmt->bindValue(":settings", $settings);
            $stmt->execute();

            return self::pdo()->lastInsertId();
        } catch (PDOException $e) {
            error_log("ERROR: insertPlugin" . $e->getMessage());
        }

        return null;
    }

    /**
     * Получает информацию о плагине по его ID в базе данных.
     *
     * @param int $id
     * @return array|null
     */
    public static function selectPluginById(int $id): ?array
    {
        try {
            $stmt = self::pdo()->prepare("SELECT * FROM WP_Modules WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $plugin = [];
            if ($stmt->rowCount()) {
                $plugin = $stmt->fetch(PDO::FETCH_ASSOC);
                $plugin['settings'] = json_decode($plugin['settings'], true);
            }
        } catch (PDOException $e) {
            error_log("ERROR: selectPluginById" . $e->getMessage());
            return null;
        }

        return $plugin;
    }

    /**
     * Изменяет плагин в базе данных по входящим параметрам.
     *
     * @param int $pluginId
     * @param string|null $name
     * @param string|null $version
     * @param string|null $settings
     * @return bool|null Возвращает null если входящие параметры не заданы или ошибка.
     */
    public static function updatePluginById(int $pluginId, string $name = null, string $version = null, string $settings = null, int $enabled = null): ?bool
    {
        if(
            empty($name) &&
            empty($version) &&
            empty($settings) &&
            empty($enabled)
        ) return null;

        $qParts = [];
        if(!empty($name)) $qParts[] = "name = '$name'";
        if(!empty($version)) $qParts[] = "version = '$version'";
        if(!empty($settings)) $qParts[] = "settings = '$settings'";
        if(!empty($enabled)) $qParts[] = "enabled = '$enabled'";
        $query = "UPDATE WP_Modules SET ".implode(', ', $qParts)." WHERE id = '$pluginId'";

        try {
            $stmt = self::pdo()->prepare($query);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("ERROR: updatePluginById" . $e->getMessage());
            return null;
        }
    }
}