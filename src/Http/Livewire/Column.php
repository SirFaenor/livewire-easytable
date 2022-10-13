<?php

namespace Sirfaenor\Leasytable\Http\Livewire;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * Single column in datatable columns list.
 * @author Emanuele Fornasier <emanuele@atrio.it>
 */
class Column extends Component
{
    protected $heading;
    public $attribute;
    protected $searchable = false;
    protected $sortable = false;
    protected $sortCallback = null;
    protected $searchCallback = null;
    protected $formatCallback = null;
    protected $classes = '';


    /**
     * @var array
     */
    protected $config;


    /**
     * Properties related to filter
     */
    protected $filterable = false;
    protected string $filterLabel = '';
    protected ?array $filterOptions = null;
    protected $filterCallback = null;


    /**
     * @var bool
     * If true, enable edit on column
     */
    protected $editLink = false;


    /**
     * Flag to show the column in ordering mode
     */
    protected $showInOrderList = false;


    /**
     * @param string $attribute column name and model attribute
     * @param array $config extra parameters for specific column types
     */
    public function __construct(string $attribute = null, array $config = [])
    {
        $this->attribute = $attribute;
        $this->config = $config;
    }


    /**
     * Accessor
     */
    public function __get($property)
    {
        switch ($property):
            case 'heading':
                return is_callable($this->heading) ? call_user_func($this->heading) : $this->heading;
            default:
                return $this->$property;
        endswitch;
    }


    /**
     * Set column's heading.
     * It could be a raw string or a callback to make
     * whatever.
     * To access, $column->heading
     */
    public function heading(mixed $heading)
    {
        $this->heading = $heading;

        return $this;
    }


    /**
     * Wrap cell content with link to edit record.
     */
    public function editLink()
    {
        $this->editLink = true;

        return $this;
    }


    /**
     * Sets a columns as searchable and store a callback
     * to modify query builder
     */
    public function searchable(callable $callback = null)
    {
        $this->searchable = true;

        $this->searchCallback = $callback;

        return $this;
    }


    /**
     * Modify query builder to search.
     */
    public function search(Builder $query, string $search = null)
    {
        if (!$this->searchable) {
            return $query;
        }

        // default search logic on attribute
        if (!$this->searchCallback) {
            return $query->orWhere($this->attribute, 'like', '%' . $search . '%');
        }

        return call_user_func($this->searchCallback, $query, $search);
    }

    /**
     * Sets a columns as sortable and store a callback
     * to modify query builder
     */
    public function sortable(callable $callback = null)
    {
        $this->sortable = true;

        $this->sortCallback = $callback;

        return $this;
    }


    /**
     * Modify query builder to set sorting on it.
     */
    public function sort(Builder $query, string $direction)
    {
        if (!$this->sortable) {
            throw new Exception("Column $this->attribute is not sortable");
        }

        // default order logic on attribute
        if (!$this->sortCallback) {
            return $query->orderBy($this->attribute, $direction);
        }

        return call_user_func($this->sortCallback, $query, $direction);
    }


    /**
     * Controlla se l'ordinamento è attivo per il dato attributo e restituisce l'html
     * corrispondente
     */
    public function sortIcon(string $attribute = null, string $direction = null): string
    {
        if (!$this->sortable) {
            return '';
        }

        if ($attribute !== $this->attribute || !$direction) {
            return '<i class="mvi mvi-arrow-up"></i><i class="mvi mvi-arrow-down"></i>';
        }

        return $direction == 'asc' ? '<i class="mvi mvi-arrow-up"></i><i class="mvi mvi-arrow-down uk-invisible"></i>' : '<i class="mvi mvi-arrow-down"></i><i class="mvi mvi-arrow-up uk-invisible"></i>';
    }


    /**
     * Sets a columns as filterable and store a callback
     * to modify query builder
     * @param string $filterLabel label of filter
     * @param Collection $options list of options
     * @param callable $valueCallback callback to get option's value from
     * @param callable $labelCallback callback to get option's label from
     * @param callable $filterCallback callback that will be applied on parent query builder
     */
    public function filterable(string $filterLabel, Collection $options, callable $valueCallback, callable $labelCallback, callable $filterCallback)
    {
        $this->filterable = true;

        $this->filterOptions = [];

        $this->filterLabel = $filterLabel;

        $this->filterCallback = $filterCallback;

        foreach ($options as $item) {
            $this->filterOptions[call_user_func($valueCallback, $item)] = call_user_func($labelCallback, $item);
        }

        return $this;
    }


    /**
     * Modify query builder to set filtering on it.
     * @param Builder $query query builder used by parent Table
     * @param mixed $value current value of filter applied on this column
     */
    public function filter(Builder $query, $value)
    {
        if (!$this->filterable) {
            throw new Exception("Column $this->attribute is not filterable");
        }

        return call_user_func($this->filterCallback, $query, $value);
    }


    /**
     * Sets a callback to format model attribute
     */
    public function formatUsing(callable $formatCallback)
    {
        $this->formatCallback = $formatCallback;

        return $this;
    }


    /**
     * Return value from model, using formatting logic.
     */
    public function render(Model $model, $functionCode = null)
    {
        // default order logic on attribute
        if (!$this->formatCallback) {
            $content = $model->getAttribute($this->attribute);
        } else {
            $content = call_user_func($this->formatCallback, $model, $this->attribute);
        }

        if ($this->editLink === true) {
            return '<a href="'.route($functionCode.'.update', [$model->getAttribute('id')]).'">'.$content.'</a>';
        }

        return $content;
    }


    /**
     * Add custom classes to <td> tag
     */
    public function classes(string $classes)
    {
        $this->classes = $classes;

        return $this;
    }



    public function __toString()
    {
        return $this->attribute;
    }


    /**
     * Set column to be shown in order list
     */
    public function showInOrderList()
    {
        $this->showInOrderList = true;

        return $this;
    }
}
