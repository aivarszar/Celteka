<?php
/**
 * Database - Datubāzes savienojuma un vaicājumu klase
 * Izmanto PDO ar prepared statements drošībai
 */

class Database {
    private static $instance = null;
    private $pdo;
    private $config;

    private function __construct($config) {
        $this->config = $config;

        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s",
            $config['host'],
            $config['dbname'],
            $config['charset']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            $errorMsg = "Datubāzes savienojuma kļūda: " . $e->getMessage();
            error_log($errorMsg);

            // Ja debug režīms, rādīt detalizētu kļūdu
            if (defined('DEBUG') && DEBUG) {
                die("<h3>Database Connection Error</h3><pre>" . htmlspecialchars($errorMsg) . "\n\nDSN: " . htmlspecialchars($dsn) . "</pre>");
            }

            die("Datubāzes savienojuma kļūda. Lūdzu pārbaudiet konfigurāciju.");
        }
    }

    public static function getInstance($config = null) {
        if (self::$instance === null) {
            if ($config === null) {
                die("Datubāzes konfigurācija nav norādīta");
            }
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Detalizēts error logging
            $errorMsg = "SQL kļūda: " . $e->getMessage();
            $errorMsg .= "\nSQL: " . $sql;
            $errorMsg .= "\nParams: " . json_encode($params);
            $errorMsg .= "\nStack trace: " . $e->getTraceAsString();
            error_log($errorMsg);

            // Ja debug režīms, parādīt detalizētu kļūdu
            if (defined('DEBUG') && DEBUG) {
                echo "<h3>Database Query Error</h3>";
                echo "<pre>";
                echo "Error: " . htmlspecialchars($e->getMessage()) . "\n\n";
                echo "SQL: " . htmlspecialchars($sql) . "\n\n";
                echo "Params: " . htmlspecialchars(json_encode($params, JSON_PRETTY_PRINT)) . "\n\n";
                echo "Stack trace:\n" . htmlspecialchars($e->getTraceAsString());
                echo "</pre>";
                exit;
            }

            throw $e;
        }
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchColumn($sql, $params = []) {
        return $this->query($sql, $params)->fetchColumn();
    }

    public function insert($table, $data) {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_map(fn($k) => ":$k", $keys));

        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
        $this->query($sql, $data);

        return $this->pdo->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach (array_keys($data) as $key) {
            $set[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $set);

        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $params = array_merge($data, $whereParams);

        return $this->query($sql, $params)->rowCount();
    }

    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params)->rowCount();
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }

    public function getPdo() {
        return $this->pdo;
    }
}
