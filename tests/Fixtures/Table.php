<?php

namespace RenokiCo\OctaneExporter\Test\Fixtures;

use Exception;
use Swoole\Table as SwooleTable;

class Table extends SwooleTable
{
    protected $rows = [];

    protected $schema = [];

    const TYPE_STRING = 'string';
    const TYPE_INT = 'int';
    const TYPE_FLOAT = 'float';

    public function __construct($bits)
    {
        //
    }

    public function create()
    {
        //
    }

    public function column($name, $type, $bits = 1000)
    {
        $this->schema[$name] = $type;
    }

    public function incr($key, $column, $incrby = 1)
    {
        $this->ensureKeys($key, $column);

        $this->rows[$key][$column]++;
    }

    public function decr($key, $column, $decrby = 1)
    {
        $this->ensureKeys($key, $column);

        $this->rows[$key][$column]--;
    }

    public function get($key, $column = null)
    {
        $this->ensureKeys($key, $column);

        return $column
            ? $this->rows[$key][$column]
            : $this->rows[$key];
    }

    protected function ensureKeys($key, $column = null)
    {
        if (! isset($this->schema[$column]) && ! is_null($column)) {
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
