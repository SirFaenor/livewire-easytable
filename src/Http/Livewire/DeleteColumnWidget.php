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

    public $attribute;

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
     * Execute deleting.
     * When button is clicked, emit event to parent component.
     * A callback cannot be stored as public property of a livewire component, so we must
     * forward call to parent that will retrieve the callback from columns configuration.
     */
    public function delete()
    {
        // notify parent table to execute deleting for this row
        $this->emitUp('columnDeleting', $this->attribute, $this->model->getAttribute('id'));
    }
}
