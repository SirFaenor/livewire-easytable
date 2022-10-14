<?php

namespace Sirfaenor\Leasytable\Http\Livewire;

use Livewire\Component;

/**
 * Livewire component to render an "emun [Y,N]" field toggler
 */
class SwitchColumnWidget extends Component
{
    public $model;

    public $attribute;

    public $type;

    public function render()
    {
        $name = $this->model->{$this->attribute};

        // valori 'Y' multipli permessi ?
        $single = $this->type == 'switch_single' ? 'true' : 'false';

        $str = ' <div class="uk-inline field-control field-control-status field-control-switch" data-current-status="'.$name.'" data-group="'.$name.'" data-single="'.$single.'">

            <span class="order">'.$this->model->$name.'</span>

            <a wire:click.prevent="toggleStatus()" class="list_item_update_switch status uk-link-reset uk-flex" href="#">
                <span class="label uk-label uk-label-success" data-status="Y">
                    Sì
                </span>
                <span class="label uk-label uk-label-danger" data-status="N">
                    No
                </span>
                <span class="mvi-icons-vertical uk-margin-small-left">
                    <i class="mvi mvi-triangle-up"></i>
                    <i class="mvi mvi-triangle-down"></i>
                </span>
            </a>
            
        </div>';

        return $this->minify($str);
    }


    /**
     * "Minificazione"
     */
    protected function minify($str)
    {
        return str_replace(["\n", "\r", "  "], " ", $str);
    }


    /**
     * Toggle status
     */
    public function toggleStatus()
    {
        $isSingle = $this->type == 'single';
        $column = $this->attribute;

        //inverto la voce
        $this->model->{$column} = $this->model->{$column} == 'Y' ? 'N' : 'Y';
        $this->model->save();

        // disattivo le vecchie se ho uno switch singolo
        if ($this->model->{$column} == 'Y' && $isSingle === true) {
            $this->model->query()->where("id", "<>", $this->model->id)->update([
                $column => "N"
            ]);

            $this->emitUp('refresh');
        }
    }
}
