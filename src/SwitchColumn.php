<?php

namespace Sirfaenor\Leasytable;

use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

/**
 * Column to toggle an enum field.
 */
class SwitchColumn extends Column
{
    protected $classes = 'tools publication';

    protected $type = 'multiple';

    /**
     * If a record is set on 'Y', all others will be set to 'Y'
     */
    public function single(): self
    {
        $this->type = 'single';

        return $this;
    }


    /**
     * Allow  multiple records to be set on 'Y'
     */
    public function multiple(): self
    {
        $this->type = 'multiple';

        return $this;
    }

    /**
     * Return publication widget (livewire component)
     */
    public function output(Model $model, $functionCode = null)
    {
        // ritorno componente livewire
        return Livewire::mount('leasytable::switch_column_widget', [
            'model' => $model,
            'attribute' => $this->attribute,
            'type' => $this->type,
        ]);
    }
}
