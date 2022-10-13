<?php

namespace Sirfaenor\Leasytable\Http\Livewire;

use Livewire\Component;
use AtrioTeam\Marvin\App\Functions\Base\HandlerCopyInterface;
use AtrioTeam\Marvin\App\Functions\Base\HandlerDeleteInterface;

/**
 * Livewire component to render a copy widget on a row.
 */
class CopyColumnWidget extends Component
{
    public $model;

    public $functionCode;

    public $heading;

    public function render()
    {
        return '<div x-data="copyWidget">
                <span class="row-tools-item">
                    <a x-on:click="confirm()"
                    class="list_item_copy uk-icon-link"><i class="mvi mvi-copy"></i><span class="as_label">'.$this->heading.'</span></a>
                </span>
        </div>';
    }


    /**
     * Execute copy
     */
    public function copy()
    {
        $Handler = app()->make(HandlerCopyInterface::class, [
            'function_code' => $this->functionCode
        ]);

        $model = $this->model;


        /**
         * Copia record principale
         */
        $newModel = $Handler->copyItem($model);


        /**
         * Copia dei record lingua
         */
        $langs = $Handler->copyItemLangs($model, $newModel, []);


        /**
         * Copia gallery
         */
        $galleries = $Handler->copyGalleries($model, $newModel);

        /**
         * Copia widgets
         */
        $widgets = $Handler->copyWidgets($model, $newModel);

        // refresh parent table
        $this->emitUp('refresh');
    }
}
