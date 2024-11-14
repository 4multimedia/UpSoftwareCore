<?php

namespace Upsoftware\Core\Classes;

use Illuminate\Database\Eloquent\Model;

class ModelFilter
{
    protected $modelClass;
    protected $query;
    private $pagination = true;

    public function __construct($modelClass)
    {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("Podany parametr nie jest klasą modelu Eloquent.");
        }

        $this->modelClass = $modelClass;
        $this->query = $modelClass::query();
    }

    public function pagination($pagination) {
        $this->pagination = $pagination;
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
        $rowsPerPage = request()->input('rows_per_page', 10);
        $orderBy = request()->input('order_by', 'name');
        $direction = request()->descending === 'true' ? 'DESC' : 'ASC';

        $this->query->orderBy($orderBy, $direction);

        if ($this->pagination) {
            return $this->query->paginate($rowsPerPage);
        } else {
            return $this->query->get();
        }
    }
}
