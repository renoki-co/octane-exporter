<?php

namespace RenokiCo\OctaneExporter\Test\Fixtures;

use Exception;
use Swoole\Table as SwooleTable;

class OpenSwooleTable extends SwooleTable
{
    protected $rows = [];

    protected $schema = [];

    const TYPE_STRING = 0;
    const TYPE_INT = 1;
    const TYPE_FLOAT = 2;

    public function __construct($bits)
    {
        //
    }

    public function create(): bool
    {
        return true;
    }

    public function column(string $name, int $type, int $size = 0): bool
    {
        $this->schema[$name] = $type;

        return true;
    }

    public function incr(string $key, string $column, int $incrBy = 1): int
    {
        $this->ensureKeys($key, $column);

        return $this->rows[$key][$column]++;
    }

    public function decr(string $key, string $column, int $decrBy = 1): int
    {
        $this->ensureKeys($key, $column);

        return $this->rows[$key][$column]--;
    }

    public function get(string $key, string $column = ''): array|string|int|float|bool
    {
        $this->ensureKeys($key, $column);

        return $column
            ? $this->rows[$key][$column]
            : $this->rows[$key];
    }

    protected function ensureKeys($key, $column = '')
    {
        if (! isset($this->schema[$column]) && $column !== '') {
            throw new Exception("Column {$column} not found in testing Swoole table.");
        }

        if (! isset($this->rows[$key])) {
            $this->rows[$key] = [];

            foreach ($this->schema as $columnName => $columnType) {
                $this->rows[$key][$columnName] = match ($columnType) {
                    static::TYPE_STRING => '',
                    static::TYPE_INT => 0,
                    static::TYPE_FLOAT => 0.0,
                };
            }
        }
    }
}
