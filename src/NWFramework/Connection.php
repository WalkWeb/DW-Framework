<?php

namespace NW;

use mysqli;
use Throwable;

final class Connection
{
    public const ERROR_CONNECT = 'Невозможно подключиться к MySQL: ';
    public const ERROR_PREPARE = 'Ошибка выполнения SQL: ';

    private mysqli $connection;

    private string $error = '';

    /**
     * @var int Количество запросов
     */
    private int $queryNumber = 0;

    private Logger $logger;

    /**
     * Создает подключение к БД
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param Logger|null $logger
     * @throws AppException
     */
    public function __construct(
        string $host,
        string $user,
        string $password,
        string $database,
        ?Logger $logger = null
    )
    {
        $this->logger = $logger ?? new Logger();

        // TODO Вынести подключение к базе в отдельный метод и логировать ошибку

        try {
            $this->connection = mysqli_connect($host, $user, $password, $database);
        } catch (Throwable $e) {
            throw new AppException(self::ERROR_CONNECT . $e->getMessage());
        }

        // Проверка соединения
        if (mysqli_connect_errno()) {
            throw new AppException(self::ERROR_CONNECT . mysqli_connect_error());
        }

        $this->connection->query('SET NAMES utf8');
        $this->connection->set_charset('utf8');
    }

    /**
     * Закрывает соединение с бд
     */
    public function __destruct()
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    /**
     * Возвращает true если все ок, и false - если есть ошибки
     *
     * @return bool
     */
    public function isSuccess(): bool
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

        return $this->connection->error;
    }

    /**
     * Обработка и выполнение запроса
     *
     * @param $sql
     * @param array $params
     * @param bool $single
     * @return array|bool|mixed
     * @throws AppException
     */
    public function query($sql, $params = [], $single = false): array
    {
        if ($single) {
            $sql .= ' LIMIT 1';
        }

        if ($this->logger) {
            $this->logger->addLog($sql);
        }

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

        $stmt = $this->connection->prepare($sql);
        if ($stmt === false) {
            $this->error = self::ERROR_PREPARE . $this->connection->errno . ' ' . $this->connection->error . '. SQL: ' . $sql;
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
                $this->error = '' . $stmt->errno . ' ' . $stmt->error . '. SQL: ' . $sql;
            }
        }

        if (!$this->isSuccess()) {
            throw new AppException($this->getError());
        }

        $this->queryNumber++;

        return $result ?? [];
    }

    /**
     * Возвращает ID добавленной записи
     *
     * @return int|string
     */
    public function getInsertId()
    {
        return mysqli_insert_id($this->connection);
    }

    /**
     * Отключает автокоммит (для включения транзакции на несколько запросов)
     *
     * @param $mode boolean True - автовыполнение коммита, False - отключение автокоммита
     * @return bool
     */
    public function autocommit(bool $mode): bool
    {
        return $this->connection->autocommit($mode);
    }

    /**
     * Закрыть транзакцию в случае успешного выполнения запросов, и применить все изменения
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Закрыть транзакцию и откатить все изменения (для вариантов, когда что-то пошло не так)
     *
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->connection->rollback();
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
