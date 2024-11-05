<?php

namespace Upsoftware\Core\Classes;

use Illuminate\Database\Eloquent\Model;

class ModelFilter
{
    protected $modelClass;

    private $pagination = false;

    public function __construct($modelClass)
    {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \InvalidArgumentException("Podany parametr nie jest klasą modelu Eloquent.");
        }

        $this->modelClass = $modelClass;
    }

    public static function __callStatic($method, $args)
    {
        $instance = new static($args[0]);

        if (method_exists($instance, $method)) {
            return $instance->$method();
        }

        throw new \BadMethodCallException("Metoda {$method} nie istnieje.");
    }

    public function search() {
        $rowsPerPage = request()->input('rows_per_page', 10);
        $orderBy = request()->input('order_by', 'name');
        $direction = request()->input('direction', 'asc');

        $items = $this->modelClass::orderBy($orderBy, $direction);
        if ($this->pagination) {
            return $items->paginate($rowsPerPage);
        } else {
            return $items->get();
        }

        return $items;
    }
}
