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
    private $columns;
    private $joinColumns = [];
    private $joinTable;
    private $joinCondition;

    public function __construct($modelClass)
    {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("Podany parametr nie jest klasą modelu Eloquent.");
        }

        $this->modelClass = $modelClass;
        $this->query = $modelClass::query();
        $this->columns = Schema::getColumnListing((new $modelClass)->getTable());
    }

    public function pagination($pagination) {
        $this->pagination = $pagination;
        return $this;
    }

    public function join($table, $foreignKey, $localKey)
    {
        $this->joinTable = $table;
        $this->joinCondition = [$foreignKey, '=', $localKey];
        $this->query->join($table, ...$this->joinCondition);

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
            if (in_array($key, $this->columns)) {
                $this->query->where($this->modelClass::getTable() . '.' . $key, 'LIKE', "%$value%");
            } elseif (in_array($key, $this->joinColumns)) {
                $this->query->where($this->joinTable . '.' . $key, 'LIKE', "%$value%");
            }
        }

        $rowsPerPage = request()->input('rows_per_page', 10);
        $orderBy = request()->input('order_by', 'name');
        $direction = request()->descending === 'true' ? 'DESC' : 'ASC';

        if (in_array($orderBy, $this->columns)) {
            $this->query->orderBy($this->modelClass::getTable() . '.' . $orderBy, $direction);
        } elseif (in_array($orderBy, $this->joinColumns)) {
            $this->query->orderBy($this->joinTable . '.' . $orderBy, $direction);
        }

        if ($this->pagination) {
            return $this->query->paginate($rowsPerPage, 100);
        } else {
            return $this->query->get();
        }
    }
}
