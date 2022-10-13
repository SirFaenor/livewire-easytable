<?php

namespace Sirfaenor\Leasytable\Http\Livewire;

use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

/**
 * Delete button for a row.
 */
class DeleteColumn extends Column
{
    protected $heading = 'Cancella';

    protected $classes = 'tools delete';

    /**
     * Return edit widget (livewire component)
     */
    public function render(Model $model, $functionCode = null)
    {
        // ritorno componente livewire
        return Livewire::mount('leasytable::delete_column_widget', [
            'model' => $model,
            'functionCode' => $functionCode,
        ])->html();
    }
}
