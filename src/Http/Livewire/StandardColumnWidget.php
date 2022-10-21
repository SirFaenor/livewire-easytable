<?php

namespace Sirfaenor\Leasytable\Http\Livewire;

use Exception;
use Livewire\Component;
use Illuminate\Database\Eloquent\Model;

/**
 * Livewire component to render a standard cell content.
 */
class StandardColumnWidget extends Component
{
    /**
     * value of the cell
     */
    public ?string $content;

    /**
     * Attribute (name) of the column
     */
    public string $attribute;

    /**
     * Flag to activate live editing on column
     */
    public bool $editable;

    /**
     * Type of input to render when column is editable
     * @var string input|textarea
     */
    public ?string $editableInput = null;

    /**
     * Model (row) associated to this column
     */
    public Model $model;

    /**
     * Property to be live updated
     */
    public $value;

    /**
     * Store content in the property
     */
    public function mount()
    {
        $this->value = $this->content;
    }

    public function render()
    {
        // setup input field if the column is live editable
        if ($this->editable === true) {
            switch ($this->editableInput) {
                case 'input':

                    $this->content = '<input wire:loading.delay.attr="disabled" wire:loading.delay.class="loading" class="uk-input" type="text" wire:model.lazy="value" value="'.$this->content.'" /></span>';

                    break;

                case 'textarea':

                    $this->content = '<textarea wire:loading.delay.attr="disabled" wire:loading.delay.class="loading" rows="5" class="uk-textarea" wire:model.lazy="value">'.$this->content.'</textarea>';

                    break;

                default:
                    # code...
                    throw new Exception("Unknown input type [$this->editableInput]");

                    break;
            }
        }

        return view('leasytable::columns.standard');
    }

    /**
     * When value is updated, emit event to parent component.
     * A callback cannot be stored as public property, so we must
     * forward call to parent that will retrieve the callback from columns configuration.
     */
    public function updatedValue()
    {
        return $this->emitUp('columnEdited', $this->attribute, $this->value, $this->model->getAttribute('id'));
    }
}
