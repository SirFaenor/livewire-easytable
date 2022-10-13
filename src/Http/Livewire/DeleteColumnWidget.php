<?php

namespace Sirfaenor\Leasytable\Http\Livewire;

use Livewire\Component;
use AtrioTeam\Marvin\App\Functions\Base\HandlerDeleteInterface;

/**
 * Livewire component to render a delete widget on a row.
 */
class DeleteColumnWidget extends Component
{
    public $model;

    public $functionCode;

    public function render()
    {
        return '<div x-data="deleteWidget">
                <span class="row-tools-item">
                    <a x-on:click="confirm()"
                    class="list_item_delete uk-icon-link"><i class="mvi mvi-trash-can"></i><span class="as_label">Elimina</span></a>
                </span>
        </div>';
    }


    /**
     * Execute deleteing
     */
    public function delete()
    {
        $handler = app()->make(HandlerDeleteInterface::class, [
            'function_code' => $this->functionCode
        ]);

        $handler->deleteItem([], $this->model);

        // refresh parent table
        $this->emitUp('refresh');
    }
}
