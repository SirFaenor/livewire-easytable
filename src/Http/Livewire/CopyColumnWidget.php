<?php

namespace Sirfaenor\Leasytable\Http\Livewire;

use Livewire\Component;

/**
 * Livewire component to render a copy widget on a row.
 */
class CopyColumnWidget extends Component
{
    public $model;

    public $heading;

    public $attribute;

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
     * Execute copying.
     * When button is clicked, emit event to parent component.
     * A callback cannot be stored as public property of a livewire component, so we must
     * forward call to parent that will retrieve the callback from columns configuration.
     */
    public function copy()
    {
        // get stored callback and execute it
        $this->emitUp('columnCopying', $this->attribute, $this->model->getAttribute('id'));
    }
}
