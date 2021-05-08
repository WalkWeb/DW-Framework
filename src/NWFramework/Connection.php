<?php

namespace NW;

use mysqli;

final class Connection
{
    /** @var mysqli */
    private $conn;

    /**@var string ошибки */
    private $error = '';

    /** @var Connection подключение к бд */
    private static $instance;

    /** @var int Количество запросов */
    private $queryNumber = 0;

    /**
     * Само подключение
     *
     * Connection constructor.
     */
    private function __construct()
    {
        $this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        or die('Невозможно подключиться к MySQL');

        // Проверка соединения
        if (mysqli_connect_errno()) {
            $this->error = 'Соединение не установлено: ' . mysqli_connect_error() . "\n";
        } else {
            $this->conn->query('SET NAMES utf8');
            $this->conn->set_charset('utf8');
        }
    }

    /**
     * Возвращает объект работы с БД
     * Если его нет - создает, если существует - возвращает текущий
     *
     * @return Connection
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Закрывает соединение с бд
     */
    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    /**
     * Возвращает true если все ок, и false - если есть ошибки
     *
     * @return bool
     */
    public function success(): bool
    {
        $error = $this->getError();
        return ($error === '' || $error === null);
    }

    /**
     * Возвращает ошибку
     *
     * @return string
     */
    public function getError(): string
    {
        if ($this->error) {
            return $this->error;
        }

        return $this->conn->error;
    }

    /**
     * Обработка и выполнение запроса
     *
     * @param $sql
     * @param array $params
     * @param bool $single
     * @return array|bool|mixed
     * @throws Exception
     */
    public function query($sql, $params = [], $single = false): array
    {
        if ($single) {
            $sql .= ' LIMIT 1';
        }

        Logs::setLogs($sql);
        $param_arr = null;

        if (count($params) > 0) {
            $param_types = '';
            $param_arr = [0 => ''];
            foreach ($params as $key => $val) {
                $param_types .= $val['type'];
                $param_arr[] = &$params[$key]['value']; // Передача значений осуществляется по ссылке.
            }
            $param_arr[0] = $param_types;
        }

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            $this->error = 'Ошибка подготовки SQL: ' . $this->conn->errno . ' ' . $this->conn->error . '. SQL: ' . $sql;
        } else {
            // Если параметры не пришли - то bind_param не требуется
            if (count($params) > 0) {
                call_user_func_array([$stmt, 'bind_param'], $param_arr);
            }
            if ($stmt->execute()) {
                $res = $stmt->get_result();
                if ($res !== false) {
                    $result = [];
                    $i = 0;
                    while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                        $result[] = $row;
                        $i++;
                    }
                    if ($single && ($i === 1)) {
                        $result = $result[0];
                    }
                }
            } else {
                $this->error = 'Ошибка выполнения SQL: ' . $stmt->errno . ' ' . $stmt->error . '. SQL: ' . $sql;
            }
        }

        if (!$this->success()) {
            throw new Exception($this->getError());
        }

        $this->queryNumber++;

        return $result ?? [];
    }

    /**
     * Возвращает ID добавленной записи
     *
     * @return int|string
     */
    public function insertId()
    {
        return mysqli_insert_id($this->conn);
    }

    /**
     * Отключает автокоммит (для включения транзакции на несколько запросов)
     *
     * @param $mode boolean True - автовыполнение коммита, False - отключение автокоммита
     * @return bool
     */
    public function autocommit(bool $mode): bool
    {
        return $this->conn->autocommit($mode);
    }

    /**
     * Закрыть транзакцию в случае успешного выполнения запросов, и применить все изменения
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->conn->commit();
    }

    /**
     * Закрыть транзакцию и откатить все изменения (для вариантов, когда что-то пошло не так)
     *
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->conn->rollback();
    }

    /**
     * Возвращает количество сделанных запросов
     *
     * @return int
     */
    public function getQueryNumber(): int
    {
        return $this->queryNumber;
    }
}
