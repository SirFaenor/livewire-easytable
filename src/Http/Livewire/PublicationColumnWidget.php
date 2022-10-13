<?php

namespace Sirfaenor\Leasytable\Http\Livewire;

use DateTime;
use Livewire\Component;

/**
 * Livewire component to render a publication widget
 */
class PublicationColumnWidget extends Component
{
    public $model;

    public $functionCode;


    public function render()
    {
        $publicStatus = $this->getPublicationStatus($this->model->public, $this->model->date_start, $this->model->date_end) ? 'Y' : 'N';

        $pubStartDate = $this->model->date_start ? DateTime::createFromFormat('Y-m-d', $this->model->date_start) : null;
        $pubEndDate = $this->model->date_end && $this->model->date_end != '2035-01-01' ? DateTime::createFromFormat('Y-m-d', $this->model->date_end) : null;

        $statusNClass = 'danger';

        $Now = new DateTime();

        $infoDepub = '';
        if ($publicStatus == 'Y' && $pubEndDate && $pubEndDate->format('Ymd') > $Now->format('Ymd')) {
            $infoDepub = 'Verrà disabilitato il '.$pubEndDate->format('d/m/Y');
        }
        $infoPub = '';
        if ($publicStatus == 'N' && $pubStartDate && $pubStartDate->format('Ymd') > $Now->format('Ymd')) {
            $infoPub = 'Verrà pubblicato il '.$pubStartDate->format('d/m/Y');
            $statusNClass = 'warning';
        }


        $link = app('UrlManager')->functionUrlFromCode($this->functionCode, "publication", ["id" => $this->model->id]);

        return
        '
            <div class="uk-inline field-control field-control-status field-control-public" data-current-status="'.$publicStatus.'">

            <a class="list_item_public_status uk-link-reset uk-flex" href="'.$link.'" data-func="'.$this->functionCode.'" data-id="'.$this->model->id.'" wire:click.prevent="toggleStatus()">
                <span class="label uk-label uk-label-success" data-status="Y">
                    Pubblicato
                </span>
                <span class="label uk-label uk-label-'.$statusNClass.'" data-status="N">
                    Disabilitato
                </span>
                <span class="mvi-icons-vertical uk-margin-small-left">
                    <i class="mvi mvi-triangle-up"></i>
                    <i class="mvi mvi-triangle-down"></i>
                </span>
                
            </a>
                                                
        ';
    }


    /**
     * "Minificazione"
     */
    protected function minify($str)
    {
        return str_replace(["\n", "\r", "  "], "", $str);
    }


    /**
     * Traduzione delle informazioni di pubblicazione memorizzate in DB
     *
     * @param string $pubStatus string [mandatory]
     * @param string $pubStartDate string [mandatory] (data inizio)
     * @param string $pubEndDate string [mandatory] (data fine)
     * @return boolean
     */
    public function getPublicationStatus($pubStatus, $pubStartDate, $pubEndDate)
    {
        $pubStartDate = strlen($pubStartDate) > 10 ? $pubStartDate : $pubStartDate.' 00:00:00';
        $pubEndDate = strlen($pubEndDate) > 10 ? $pubEndDate : $pubEndDate.' 00:00:00';

        $dateStart = Datetime::createFromFormat("Y-m-d H:i:s", $pubStartDate);
        $dateEnd = Datetime::createFromFormat("Y-m-d H:i:s", $pubEndDate);
        $now = new Datetime();

        // Se definito "pubblico": controllo le date
        if(strtoupper($pubStatus) == strtoupper('Y') && $dateStart <= $now && $now <=  $dateEnd) :
            return true;
        endif;

        return false;
    }



    /**
     * Toggle status
     */
    public function toggleStatus()
    {
        // recupero voce
        $item = $this->model;

        $rs = $item->toArray();

        // pubblicazione completa
        if (array_key_exists('public', $rs) && array_key_exists('date_end', $rs) && array_key_exists('date_start', $rs)) :
            if ($rs["public"] == 'N' || $rs["date_end"] < date('Y-m-d H:i:s') || $rs["date_start"] > date('Y-m-d H:i:s')) :

                $item->date_start = $rs["date_start"] == '0000-00-00' || $rs["date_start"] > date('Y-m-d H:i:s') ? date('Y-m-d H:i:s') : $rs["date_start"];
                $item->date_end = $rs["date_end"] < date('Y-m-d H:i:s') ? '2035-01-01' : $rs["date_end"];
                $item->public = 'Y';

            else :
                $item->public = 'N';
            endif;

        // solo campo pubblico
        elseif (array_key_exists('public', $rs)) :
            $item->public = $rs["public"] == 'Y' ? 'N' : 'Y';
        endif;

        // salvo
        $item->save();
    }
}
