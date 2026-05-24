<?php
/**
 * DB — lightweight PDO wrapper.
 */
declare(strict_types=1);

class DB
{
    private static ?PDO $pdo = null;

    public static function init(array $cfg): void
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $cfg['host'], $cfg['port'] ?? 3306, $cfg['name'], $cfg['charset'] ?? 'utf8mb4'
        );
        try {
            self::$pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ]);
        } catch (Throwable $e) {
            error_log('[DB] ' . $e->getMessage());
            http_response_code(500);
            die('Database connection error.');
        }
    }

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            throw new RuntimeException('DB not initialized');
        }
        return self::$pdo;
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function all(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function row(string $sql, array $params = []): ?array
    {
        $r = self::query($sql, $params)->fetch();
        return $r === false ? null : $r;
    }

    public static function value(string $sql, array $params = [])
    {
        $r = self::query($sql, $params)->fetchColumn();
        return $r === false ? null : $r;
    }

    public static function insert(string $table, array $data): int
    {
        $cols = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $cols);
        $sql = 'INSERT INTO `' . $table . '` (`' . implode('`,`', $cols) . '`) VALUES (' . implode(',', $placeholders) . ')';
        self::query($sql, $data);
        return (int) self::pdo()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $sets = [];
        foreach (array_keys($data) as $col) {
            $sets[] = "`$col` = :$col";
        }
        $sql = 'UPDATE `' . $table . '` SET ' . implode(',', $sets) . ' WHERE ' . $where;
        return self::query($sql, array_merge($data, $whereParams))->rowCount();
    }

    public static function delete(string $table, string $where, array $params = []): int
    {
        return self::query("DELETE FROM `$table` WHERE $where", $params)->rowCount();
    }

    public static function tx(callable $fn)
    {
        $pdo = self::pdo();
        $pdo->beginTransaction();
        try {
            $result = $fn();
            $pdo->commit();
            return $result;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
