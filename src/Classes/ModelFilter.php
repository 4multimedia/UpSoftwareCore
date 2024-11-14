<?php

namespace Upsoftware\Core\Classes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ModelFilter
{
    protected Model $modelClass;
    protected Builder $query;
    private bool $pagination = true;
    private array $columns;
    private array $joinColumns = [];
    private $joinTable;
    private $joinCondition;

    public function __construct($modelClass)
    {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("Podany parametr nie jest klasą modelu Eloquent.");
        }

        $this->modelClass = $modelClass;
        $this->modelInstance = new $modelClass;
        $this->query = $this->modelInstance->newQuery();
        $this->columns = Schema::getColumnListing($this->modelInstance->getTable());
    }

    public function pagination($pagination) {
        $this->pagination = $pagination;
        return $this;
    }

    public function join($table, $foreignKey, $localKey)
    {
        return $this->setJoin('join', $table, $foreignKey, $localKey);
    }

    public function leftJoin($table, $foreignKey, $localKey)
    {
        return $this->setJoin('leftJoin', $table, $foreignKey, $localKey);
    }

    public function rightJoin($table, $foreignKey, $localKey)
    {
        return $this->setJoin('rightJoin', $table, $foreignKey, $localKey);
    }

    private function setJoin($type, $table, $foreignKey, $localKey)
    {
        $this->joinTable = $table;
        $this->joinCondition = [$foreignKey, '=', $localKey];
        $this->joinType = $type;

        $this->query->{$this->joinType}($table, ...$this->joinCondition);

        $this->joinColumns = Schema::getColumnListing($table);
        return $this;
    }

    public static function make($modelClass)
    {
        return new static($modelClass);
    }

    public function __call($method, $parameters)
    {
        $this->query = $this->query->$method(...$parameters);
        return $this;
    }

    public function search() {

        $request = request()->all();

        foreach ($request as $key => $value) {
            $column = $key;
            $operator = 'LIKE';
            $valuePattern = $value;

            if (str_starts_with($key, '_') && str_ends_with($key, '_')) {
                $column = trim($key, '_');
                $valuePattern = "%$value%";
            } elseif (str_starts_with($key, '_')) {
                $column = ltrim($key, '_');
                $valuePattern = "%$value";
            } elseif (str_ends_with($key, '_')) {
                $column = rtrim($key, '_');
                $valuePattern = "$value%";
            }

            if (in_array($column, $this->columns)) {
                $this->query->where($this->modelInstance->getTable() . '.' . $column, $operator, $valuePattern);
            } elseif (in_array($column, $this->joinColumns)) {
                $this->query->where($this->joinTable . '.' . $column, $operator, $valuePattern);
            }
        }

        $rowsPerPage = request()->input('rows_per_page', 10);
        $orderBy = request()->input('order_by', 'name');
        $direction = request()->descending === 'true' ? 'DESC' : 'ASC';

        if (in_array($orderBy, $this->columns)) {
            $this->query->orderBy($this->modelInstance->getTable() . '.' . $orderBy, $direction);
        } elseif (in_array($orderBy, $this->joinColumns)) {
            $this->query->orderBy($this->joinTable . '.' . $orderBy, $direction);
        }

        if ($this->pagination) {
            return $this->query->paginate($rowsPerPage);
        } else {
            return $this->query->get();
        }
    }
}
