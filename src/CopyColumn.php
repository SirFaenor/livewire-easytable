<?php

namespace Sirfaenor\Leasytable;

use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

/**
 * Copy button for a row.
 */
class CopyColumn extends Column
{
    protected $heading = 'Copia';

    protected $classes = 'tools copy';


    /**
     * Return copy comuns widget (livewire)
     */
    public function output(Model $model, $functionCode = null)
    {
        // ritorno componente livewire
        return Livewire::mount('leasytable::copy_column_widget', [
            'model' => $model,
            'functionCode' => $functionCode,
            'heading' => $this->heading,
        ])->html();
    }
}
