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
    public string $content;

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
        // if there's a callback, the column is live editable
        if ($this->editable === true) {
            switch ($this->editableInput) {
                case 'input':

                    $this->content = '<input class="uk-input" type="text" wire:model.lazy="value" value="'.$this->content.'" /></span>';

                    break;

                case 'textarea':

                    $this->content = '<textarea rows="5" class="uk-textarea" wire:model.lazy="value">'.$this->content.'</textarea>';

                    break;

                default:
                    # code...
                    throw new Exception("Unknown input type [$this->editableInput]");

                    break;
            }
        }

        return '<span>'.$this->content.'</span>';
    }

    /**
     * When value is updated, emit event to parent component.
     * A callback cannot be stored as public property, so we must
     * forward call to parent that will get the callback from columns configuration.
     */
    public function updatedValue()
    {
        return $this->emitUp('columnEdited', $this->attribute, $this->value, $this->model->getAttribute('id'));
    }
}
