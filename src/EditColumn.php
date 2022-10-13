<?php

namespace Sirfaenor\Leasytable;

use Illuminate\Database\Eloquent\Model;

/**
 * Edit button for a row.
 */
class EditColumn extends Column
{
    protected $heading = 'Modifica';

    protected $classes = 'tools  edit';


    public function output(Model $model, $functionCode = null)
    {
        $link = route($functionCode.'.update', ["id" => $model["id"]]);

        return '<span class="row-tools-item"><a data-code="'.$functionCode.'" class="list_item_update uk-icon-link" href="'.$link.'" >
        <i class="mvi mvi-pencil"></i><span class="as_label">Modifica</span></a></span>';
    }
}
