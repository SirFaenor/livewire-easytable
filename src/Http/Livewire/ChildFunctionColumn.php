<?php

namespace Sirfaenor\Leasytable\Http\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Column to link list of a child function.
 */
class ChildFunctionColumn extends Column
{
    protected $classes = 'tools no-sort widgets';


    /**
     * Render link to child function
     */
    public function render(Model $model, $functionCode = null)
    {
        $link = route($this->attribute.'.list', ["parent_id" => $model->id, "parent_code" => $functionCode]);

        /**
         * presuppone esistenza,nel modello, di una relazione nominata come $config["name] al plurale
         * (se "name" è "product" la relazione sottointesa sarà "products")
         */
        $relationName = Str::plural($this->attribute);
        $count = $model->$relationName ? '('.count($this->model->$relationName).')' : '';

        /**
         * Sovrascrittura count da fuori
         */
        if (isset($this->config['count']) && is_callable($this->config['count'])) {
            $count = '('.call_user_func($this->config['count'], $model).')';
        }

        return '<span class="row-tools-item"><a class="uk-icon-link" href="'.$link.'"><i class="mvi mvi-list"></i><span class="as_label">'.$this->heading.'</span></a><span class="count">'.$count.'</span></span>';
    }
}
