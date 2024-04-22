<div class="livewire-dataTable-wrapper" data-component-name="{{ $this->getMarvinName() }}">
    <div>
        @if(
            count($filters) || 
            ($orderingMode === false && $showSearch === true) ||
            count($actions)
        )
        <div class="uk-grid uk-flex-middle uk-margin-top uk-margin-bottom uk-grid-medium" uk-grid>
            @foreach ($filters as $column)
            <div class="uk-width-1-3@m">
                <div class=" page-content-filterbar-item">
                    <label class="uk-form-label uk-text-nowrap">Filtra per {{ $column->filterLabel }}</label>
                    <select class="uk-select" name="{{ $column->attribute }}" wire:change="updateFilter('{{$column->attribute}}', $event.target.value)">
                        <option value="" @selected(!isset($activeFilters[$column->attribute]) || strlen($activeFilters[$column->attribute]) == 0)>-</option>
                        @foreach ($column->filterOptions as $value => $label)
                            <option value="{{ $value }}" @if(isset($activeFilters[$column->attribute]) && (string)$value == (string)$activeFilters[$column->attribute]) selected @endif
                                @disabled(in_array($value, $column->disabledFilterOptions))
                                >{{ $label}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endforeach

            @if($orderingMode === false && $showSearch === true)
            <div class="uk-width-1-3@m">
                <div class="uk-width-1-1 page-content-filterbar-item">    
                    <div>
                        <label class="uk-form-label uk-text-nowrap">Cerca ({{$searchLabel}})</label>

                        <div class="uk-flex uk-flex-middle">
                            <input type="search" class="uk-width-expand uk-input uk-text-small" placeholder="" wire:model.live.debounce.500ms="search">
                            @if(strlen($search))
                            <a class="uk-form-label uk-margin-small-left uk-text-nowrap" wire:click.prevent="$set('search', '')"><i class="mvi mvi-close" title="Clear"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($actions)
                @foreach($actions as $action)
                <div class="uk-width-1-3@m">
                    
                    <div class="uk-width-1-1 page-content-filterbar-item"> 
                        <label class="uk-form-label uk-text-nowrap">&nbsp;</label>
                        <div>
                        {!! $action !!}
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
            
        </div>{{-- grid --}}
        @endif

    </div>

    <div wire:loading.delay.long class="loader"></div>

    @if($orderingMode === false)
    <div class="uk-margin-top">
        @if($rows->isEmpty())
            <div class="uk-text-meta">
                {{ $this->emptyMessage ?: 'Nessun record da mostrare.' }}
            </div>
        @else
            
        {{-- tabella di lista voci --}}

        <div class="uk-overflow-auto">
            <table class="items-list items-list-table uk-table uk-table-hover uk-table-striped dataTable livewire-dataTable">
                <thead>
                    {{-- ciascuna colonna è un istanza di Http\Livewire\Column --}}
                    @foreach($columns as $column)
                    <th  
                        class="@if ($column->sortable) sortable @endif @if($sortAttribute == $column->attribute) active @endif {{$column->classes}}" 
                        @if ($column->sortable)
                            wire:click="sort('{{$column}}')"
                        @endif
                        data-current-sortattribute="{{$sortAttribute}}"
                        data-attribute="{{$column->attribute}}"
                    >

                        <span>
                            <span>{{$column->heading}}</span> @if($icon = $column->sortIcon($sortAttribute, $sortDirection))<span class="icon-wrapper">{!! $icon  !!}</span>@endif</span>
                        </span>
                    </th>
                    @endforeach
                </thead>

                @foreach($rows as $item)
                <tr>
                    @foreach($columns as $column)
                    <td class="{{ $column->classes }}">
                        {!! $column->output($item, $functionCode, $orderingMode) !!}
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </table>
        </div>
    
        @endif
    </div>

    <div class="row justify-content-between">
        <div class="col-auto">
            {{ $rows->links() }}
        </div>
    </div>

    {{-- ordering mode --}}
    @else 

        {{-- show list if there is no error --}}
        @if(!$orderAlert && count($rows))
        <div class="uk-margin-bottom uk-text-meta">Trascina le voci per posizionarle nell'ordine che preferisci.</div>
        <ul class="items-list-order" x-data="orderingList">
            @foreach($rows as $item)
            <li class="item" data-id="{{ $item->id }}">
                <div class="uk-flex uk-flex-middle">
                    <i class="mvi mvi-grid"></i>
                    @foreach(array_filter($columns, fn($column) => $column->showInOrderList === true) as $column)
                    <span class="uk-margin-left">{!! $column->setOrderingMode(true)->output($item, $functionCode) !!}</span>
                    @endforeach
                </div>
            </li>
            @endforeach
        </ul>
        @else
            <p class="uk-text-meta">{{ $orderAlert }}</p>
        @endif

        @if(!count($rows) && !$orderAlert)
            <p class="uk-text-meta">Nessun record da mostrare.</p>
        @endif

    @endif {{-- ordering mode --}}

    @include('leasytable::style')
</div>
