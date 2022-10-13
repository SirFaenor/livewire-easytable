<?php

namespace Sirfaenor\Leasytable\Http\Livewire;

use Illuminate\Database\Eloquent\Model;

/**
 * Column to link child widgets's list
 */
class WidgetsColumn extends Column
{
    protected $heading = 'Widgets';

    protected $classes = 'tools no-sort widgets';

    /**
     * Return publication widget (livewire component)
     */
    public function render(Model $model, $functionCode = null)
    {
        $link = route('widget.list', ["parent_id" => $model->id, "parent_code" => $functionCode]);

        $count = $model->widgets ? '('.count($model->widgets->where('public', '<>', 'P')).')' : '';

        return '<span class="row-tools-item"><a class="list_item_widget_editor uk-icon-link" href="'.$link.'"><i class="mvi mvi-image" style="font-size: 1.5em;"></i><span class="as_label">'.$this->heading.'</span></a><span class="count">'.$count.'</span></span>';
    }
}
