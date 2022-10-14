<div class="livewire-dataTable-wrapper" data-component-name="{{ $this->getMarvinName() }}">
    <div>
        @if(
            count($filters) || 
            ($orderingMode === false && $showSearch === true)    
        )
        <div class="uk-grid uk-flex-middle uk-margin-top uk-margin-bottom" uk-grid>
            @foreach ($filters as $column)
            <div class="uk-width-auto page-content-filterbar-item">
                <select class="uk-select" name="{{ $column->attribute }}" wire:change="updateFilter('{{$column->attribute}}', $event.target.value)">
                    <option value="" @selected(!isset($activeFilters[$column->attribute]) || strlen($activeFilters[$column->attribute]) == 0)>{{ $column->filterLabel }}</option>
                    @foreach ($column->filterOptions as $value => $label)
                        <option value="{{ $value }}" @if(isset($activeFilters[$column->attribute]) && (string)$value == (string)$activeFilters[$column->attribute]) selected @endif>{{ $label}}</option>
                    @endforeach
                </select>
            </div>
            @endforeach

            @if($orderingMode === false && $showSearch === true)
            <div class="uk-width-1-5@m">
                <input type="search" class="uk-input" placeholder="Cerca" wire:model="search">
            </div>
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
                        data-sortattribute="{{$sortAttribute}}"
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
