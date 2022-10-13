<?php

namespace Sirfaenor\Leasytable\Http\Livewire;

use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

/**
 * Column to show visibility widget in datatable columns list.
 * @author Emanuele Fornasier <emanuele@atrio.it>
 */
class PublicationColumn extends Column
{
    protected $heading = 'Visibilità';

    protected $classes = 'tools publication';

    /**
     * Return publication widget (livewire component)
     */
    public function render(Model $model, $functionCode = null)
    {
        // ritorno componente livewire
        return Livewire::mount('leasytable::publication_column_widget', [
            'model' => $model,
            'functionCode' => $functionCode,
        ])->html();
    }
}
