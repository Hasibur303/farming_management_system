<?php
declare(strict_types=1);

/**
 * PDO-backed compatibility classes for the existing page-oriented application.
 * New code should use DatabaseConnection::pdo() and native PDO statements.
 */
final class DatabaseResult
{
    private int $position = 0;
    public int $num_rows;

    public function __construct(private array $rows)
    {
        $this->num_rows = count($rows);
    }

    public function fetch_assoc(): ?array
    {
        return $this->rows[$this->position++] ?? null;
    }

    public function fetch_all(int $mode = 1): array
    {
        if ($this->position === 0) {
            $this->position = count($this->rows);
            return $this->rows;
        }
        $remaining = array_slice($this->rows, $this->position);
        $this->position = count($this->rows);
        return $remaining;
    }
}

final class DatabaseStatement
{
    private array $boundValues = [];
    private array $boundResults = [];
    public int $affected_rows = 0;
    public string $error = '';

    public function __construct(private PDOStatement $statement)
    {
    }

    public function bind_param(string $types, &...$values): bool
    {
        $this->boundValues = [];
        foreach ($values as &$value) {
            $this->boundValues[] =& $value;
        }
        return true;
    }

    public function execute(?array $params = null): bool
    {
        $values = $params ?? array_map(static fn (&$value) => $value, $this->boundValues);
        $ok = $this->statement->execute(array_values($values));
        $this->affected_rows = $this->statement->rowCount();
        return $ok;
    }

    public function get_result(): DatabaseResult
    {
        return new DatabaseResult($this->statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function bind_result(&...$values): bool
    {
        $this->boundResults = [];
        foreach ($values as &$value) {
            $this->boundResults[] =& $value;
        }
        return true;
    }

    public function fetch(): bool
    {
        $row = $this->statement->fetch(PDO::FETCH_NUM);
        if ($row === false) {
            return false;
        }
        foreach ($this->boundResults as $index => &$target) {
            $target = $row[$index] ?? null;
        }
        return true;
    }

    public function fetch_assoc(): ?array
    {
        $row = $this->statement->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public function close(): void
    {
        $this->statement->closeCursor();
    }
}

final class DatabaseConnection
{
    public string $error = '';
    public function __construct(private PDO $connection)
    {
    }

    public function pdo(): PDO
    {
        return $this->connection;
    }

    public function prepare(string $query): DatabaseStatement
    {
        return new DatabaseStatement($this->connection->prepare($query));
    }

    public function query(string $query): DatabaseResult|false
    {
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return new DatabaseResult($statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function execute(string $query, array $parameters = []): DatabaseStatement
    {
        $statement = new DatabaseStatement($this->connection->prepare($query));
        $statement->execute($parameters);
        return $statement;
    }

    public function begin_transaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollback(): bool
    {
        return $this->connection->inTransaction() ? $this->connection->rollBack() : false;
    }

    public function close(): void
    {
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'insert_id' => (int) $this->connection->lastInsertId(),
            default => null,
        };
    }
}

