<?php

declare(strict_types=1);

namespace Sirfaenor\Leasytable\Http\Livewire;

use Exception;
use Livewire\Component;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Sirfaenor\Leasytable\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Livewire datatable
 * @author Emanuele Fornasier <emanuele@atrio.it>
 */
abstract class Table extends Component
{
    /**
     * @see https://laravel-livewire.com/docs/2.x/pagination
     */
    use WithPagination;

    /**
     * @var Builder base eloquent query binded to this table
     */
    protected $query = null;

    /**
     * Events listening
     */
    protected $listeners = [
        'orderingModeToggle',
        'updatePositions',
        'refresh' => '$refresh',
        'columnEdited',
        'columnCopying',
        'columnDeleting',
        'restoreState'
    ];


    /**
     * Items total count
     */
    public $totalCount;


    /**
     * Current sorting values
     */
    public $sortAttribute = null;
    public $sortDirection = null;


    /**
     * Default sorting
     */
    public $defaultSortAttribute = 'position';
    public $defaultSortDirection = 'asc';


    /**
     * Ids that are in the current page
     */
    public array $currentPageIds;


    /**
     * Current selection
     */
    public array $selected = [];
    public bool $areAllSelected = false;


    /**
     * Search string
     */
    public string $search = '';


    /**
     * Active filters.
     */
    public array $activeFilters = [];


    /**
     * Message to show if there is no results.
     */
    public ?string $emptyMessage = null;


    /**
     * List of available mappings for export
     */
    public $mappingList;


    /**
     * Total rows count
     */
    public $totalRowsCount;


    /**
     * Page size
     * Default is loaded from config "leasytable.pagesize"
     */
    public $pageSize;


    /**
     * Ordering mode active flag
     */
    public $orderingMode = false;

    /**
     * Current function code.
     * Overwrites function code that is shared by ServiceProvider ("View::share")
     */
    public $functionCode;

    /**
     * We are going to store all query string params in this property.
     * It will be updated by filtering, sorting, etc..
     */
    public $state;

    /**
     * List of actions button to show on table heading (with search and filters)
     */
    public array $actions = [];

    /**
     * Store state in query string
     */
    protected $queryString = ['state'];


    /**
     * Mount
     * Load user's root folder
     */
    public function mount()
    {
        $this->restoreState();

        $this->totalRowsCount = count($this->loadRows(false));

        $this->pageSize = $this->pageSize ?: config('leasytable.pagesize');
    }


    /**
     * Restore internal state base on different criteria.
     * It is used on mount and it is also a listener from javascript to force restoring of a state
     * @param array $forceOverwrite state to force restoring from
     */
    public function restoreState(array $forceOverwrite = [])
    {
        /**
         * Clean all previous state
         */
        $this->state = [];
        $this->activeFilters = [];
        $this->sortAttribute = null;
        $this->sortDirection = null;
        $this->search = '';

        /**
         * Restore state from session
         */
        if (session()->has($this->functionCode.'.state')) {
            $this->state = session($this->functionCode.'.state');
        }

        /**
         * State in request overwrites session.
         * If request has state=0, reset state.
         */
        $requestState = request()->state;
        if (is_array($requestState)) {
            $this->state = $requestState;
            $this->saveState('');
        } elseif ((string)$requestState === "0") {
            $this->state = [];
            $this->saveState('');
        }


        /**
         * Overwrite if needed (e.g. from event restoreStateFromUrl)
         */
        if (count($forceOverwrite)) {
            $this->state = $forceOverwrite;
        }


        /**
         * Now state is set, so we're going to
         * update properties in order to reflect restored state
         */
        if (isset($this->state['filter'])) {
            foreach ($this->state['filter'] as $property => $value) {
                if ($value === null || !strlen((string)$value)) {
                    continue;
                }
                $this->activeFilters[$property] = $value;
            }
        }

        if (isset($this->state['sorting']) && strlen($this->state['sorting'])) {
            $values = explode("|", $this->state['sorting']);
            $this->sortAttribute = $values[0];
            $this->sortDirection = $values[1];
        }
        if (isset($this->state['search'])) {
            $this->search = $this->state['search'];
        }
    }


    /**
     * Save state to session in order to restore it later
     */
    public function saveState($action)
    {
        session()->put($this->functionCode.'.state', $this->state);
    }


    /**
     * Render
     */
    public function render()
    {
        // load rows
        $rows = $this->loadRows();

        // se non files, torno a pagina 1
        if ($rows->isEmpty() && $this->getPage() > 1) {
            $this->gotoPage(1);
            $rows = $this->loadRows();
        }
        $columns = $this->columns();

        $orderAlert = '';
        if ($this->orderingMode === true) {
            // se non ho risultati, mostro messaggio, altrimenti lascio decidere a classe figlia
            if ($this->totalRowsCount == 0) {
                $orderAlert = 'Nessun record da mostrare.';
            } else {
                $orderAlert = $this->checkOrderingMode();
            }
        }

        // output filters
        // filter columns that are flagged as "filterable"
        $filters = array_filter($this->columns(), fn ($column) => $column->filterable === true);

        // empty all filters if there are no rows or no active filters
        // if(!$rows->count() && count($this->activeFilters) == 0 && $this->forceFilters === false) {
        //     $filters = [];
        // }

        // output search (if there are searchable columns)
        $showSearch = false;
        $searchableColumns = array_filter($this->columns(), fn ($column) => $column->searchable === true);
        $showSearch = count($searchableColumns) > 0;

        // build search label (get heading from each searchable column)
        $searchLabel = implode(', ', array_map(fn ($column) => $column->heading, $searchableColumns));


        return view('leasytable::datatable', compact('rows', 'columns', 'filters', 'orderAlert', 'showSearch', 'searchLabel'));
    }


    /**
     * Return base query builder.
     */
    abstract protected function query(): Builder;


    /**
     * Setup columns list.
     */
    protected function columns(): array
    {
        return [

            Column::make('id')
                ->heading('ID')
                ->sortable(fn (Builder $query, string $direction) => $query->orderBy('id', $direction))
                ->classes('uk-text-nowrap'),
            Column::make('title')
                ->heading('Titolo')
                ->formatUsing(fn ($item) => $item->current_lang->title)
                ->sortable(function (Builder $query, string $direction) {
                    // your custom sorting condition
                })
                ->classes('uk-text-nowrap'),
        ];
    }


    /**
     * Load rows
     */
    protected function loadRows($paginate = true): mixed
    {
        $query = $this->query();

        // count files, without any filter
        $this->totalCount = $query->count();

        // sort by requested attribute or default
        if ($this->sortAttribute && $this->sortDirection) {
            // get subject column and let it to setup ordering
            $this->getColumnByAttribute($this->sortAttribute)->sort($query, $this->sortDirection);
        } else {
            $query->orderBy($this->defaultSortAttribute, $this->defaultSortDirection);
        }

        // in order mode, force position and disable ordering
        if ($this->orderingMode) {
            $paginate = false;
            $query->reorder()->orderBy('position', 'asc');
        }

        // search
        if (strlen($this->search)) {
            $query->where(function (Builder $query) {
                // let each column to setup its custom logic on query
                foreach ($this->columns() as $column) {
                    $column->search($query, $this->search);
                }
            });
        }

        // retrieve all columns that have a filter set on them
        foreach ($this->activeFilters as $attribute => $value) {
            $this->getColumnByAttribute($attribute)->filter($query, $value);
        }


        // get files and sort with folders first
        $rows = $paginate ? $query->paginate($this->pageSize) : $query->get();
        //Log::info("query log", DB::getQueryLog());

        $this->currentPageIds = $paginate ? array_column($rows->items(), 'id') : array_column($rows->all(), 'id');

        return $rows;
    }



    /**
     * Get specific column from attribute names
     */
    protected function getColumnByAttribute(string $attribute): ?Column
    {
        $filtered = array_filter($this->columns(), fn (Column $column) => $column->attribute === $attribute);

        return $filtered ? current($filtered) : null;
    }


    /**
     * Set a sorting attribute.
     */
    public function sort(string $attribute)
    {
        // reset paginazione
        $this->resetPage();

        // reset sort direction if attribute has changed
        if ($attribute != $this->sortAttribute) {
            $this->sortDirection = null;
        }

        $this->sortAttribute = $attribute;
        switch ($this->sortDirection) {
            case null:
                $this->sortDirection = 'asc';

                break;

            case 'asc':

                $this->sortDirection = 'desc';

                break;

            default:

                $this->sortDirection = null;

                break;
        }

        // reset current sort attribute
        if ($attribute == $this->sortAttribute && $this->sortDirection == null) {
            $this->sortAttribute = null;
        }

        // store sorting state
        if ($this->sortAttribute) {
            $this->state['sorting'] = $this->sortAttribute.'|'.$this->sortDirection;
        } else {
            $this->state['sorting'] = '';
        }

        // save state
        $this->saveState('sorting');
    }


    /**
     * Reset sorting
     */
    protected function resetSorting()
    {
        $this->sort('');
    }


    /**
     * Update search query.
     * Reset current folder in order to search through all folders
     * and subfolders.
     */
    public function updatedSearch($value)
    {
        $this->search = trim($value);

        // reset filters
        $this->resetFilters();

        // store search state
        $this->state['search'] = $value;

        // save state
        $this->saveState('updatedSearch');
    }


    /**
     * Reset current search
     */
    protected function resetSearch(): void
    {
        $this->search = '';
        $this->state['search'] = $this->search;

        // save state
        $this->saveState('resetSearch');
    }


    /**
     * Set view for pagination rendering
     */
    public function paginationView()
    {
        return 'leasytable::livewire-paginator';
    }


    /**
     * Collect filterable columns
     */
    protected function resetFilters()
    {
        $this->activeFilters = [];
        foreach ($this->activeFilters as $key => $value) {
            $this->activeFilters[$key] = null;
        }
        $this->state['filter'] = [];

        // save state
        $this->saveState('resetFilters');
    }


    /**
     * Listen for change event on a filter.
     * Store filter in internal array, to use it later in query builder setup.
     */
    public function updateFilter(mixed $attribute, mixed $value)
    {
        // reset paging
        $this->resetPage();

        // reset search
        $this->resetSearch();


        // if value is null, reset this filter
        if (!strlen((string)$value)) {
            unset($this->activeFilters[$attribute]);
            unset($this->state['filter'][$attribute]);
            $this->saveState('updateFilter');
            return;
        }

        // store new state
        $this->state['filter'][$attribute] = $value;

        $this->activeFilters[$attribute] = $value;

        // save state
        $this->saveState('updateFilter');
    }


    /**
     * Get name of the component as used in list.blade of the current function.
     */
    public function getMarvinName()
    {
        return $this->functionCode.'::datatable';
    }



    /**
     * Toggle order mode.
     * @param bool $requestStatus true to enable, false to disable
     */
    public function orderingModeToggle(bool $requestStatus)
    {
        if ($requestStatus === true) {
            $this->orderingMode = true;
        } else {
            $this->orderingMode = false;

            $this->resetSorting();

            $this->resetSearch();
        }

        // notify frontend
        $this->emit('orderingModeChange', $this->orderingMode);

        return;
    }


    /**
     * When items are sorted, store new positions
     */
    public function updatePositions(array $ids)
    {
        foreach ($ids as $position => $id) {
            $this->query()->findOrFail($id)->forceFill(['position' => $position])->save();
        }

        $this->dispatchBrowserEvent('updatedPositions');
    }


    /**
     * Check if order is available (e.g. depending on filters status).
     * @return string errormessage or empty if order is available
     */
    protected function checkOrderingMode(): string
    {
        /**
         * Eg, enable mode if there is an active filter on category, else return anr error message.
         * return ! array_key_exists('category', $this->activeFilters) ? 'Seleziona una categoria' : '';
         */
        return '';
    }


    /**
     * Listen for change on an "editable" column
     */
    public function columnEdited($attribute, $value, int $modelId)
    {
        $column = $this->getColumnByAttribute($attribute);

        $model = $this->query()->getModel()->findOrFail($modelId);

        return $column->edit($model, $value);
    }


    /**
     * Listen for a click on "copy" button widget.
     * Event is emitted from Livewire/CopyColumnWidget
     */
    public function columnCopying($attribute, int $modelId)
    {
        /**
         * @var \Sirfaenor\Leasytable\CopyColumn
         */
        $column = $this->getColumnByAttribute($attribute);

        $model = $this->query()->getModel()->findOrFail($modelId);

        // ensure that 'functionCode' exists in column's configuration
        $columnConfig = $column->config;
        if(!array_key_exists('functionCode', $columnConfig)) {
            $columnConfig['functionCode'] = $this->functionCode;
        }

        call_user_func($column->getCopyCallback(), $model, $columnConfig);

        $this->emit('refresh');
    }

    /**
     * Listen for a click on "delete" button widget.
     * Event is emitted from Livewire/CopyColumnWidget
     */
    public function columnDeleting($attribute, int $modelId)
    {
        /**
         * @var \Sirfaenor\Leasytable\DeleteColumn
         */
        $column = $this->getColumnByAttribute($attribute);

        $model = $this->query()->getModel()->findOrFail($modelId);

        // ensure that 'functionCode' exists in column's configuration
        $columnConfig = $column->config;
        if(!array_key_exists('functionCode', $columnConfig)) {
            $columnConfig['functionCode'] = $this->functionCode;
        }

        call_user_func($column->getDeleteCallback(), $model, $columnConfig);

    }
}
