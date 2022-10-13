<?php

namespace Sirfaenor\Leasytable;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

/**
 * Column to link list of a child function.
 */
class ChildFunctionColumn extends Column
{
    protected $classes = 'tools no-sort widgets';


    /**
     * Render link to child function
     */
    public function output(Model $model, $functionCode = null)
    {
        if ($this->attribute === null) {
            throw new Exception("Missing attribute on column");
        }
        $link = route($this->attribute.'.list', ["parent_id" => $model->getAttribute('id'), "parent_code" => $functionCode]);

        /**
         * presuppone esistenza,nel modello, di una relazione nominata come $config["name] al plurale
         * (se "name" è "product" la relazione sottointesa sarà "products")
         */
        $relationName = Str::plural($this->attribute);
        $count = $model->$relationName ? '('.count($model->$relationName).')' : '';

        /**
         * Sovrascrittura count da fuori
         */
        if (isset($this->config['count']) && is_callable($this->config['count'])) {
            $count = '('.call_user_func($this->config['count'], $model).')';
        }

        return '<span class="row-tools-item"><a class="uk-icon-link" href="'.$link.'"><i class="mvi mvi-list"></i><span class="as_label">'.$this->heading.'</span></a><span class="count">'.$count.'</span></span>';
    }
}
