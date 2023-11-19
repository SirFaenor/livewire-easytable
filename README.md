Pacchetto per creazione di semplici tabelle livewire basate su una collezione di modelli Laravel Eloquent.

## Caratteristiche
- Utilizza query builder di ELoquent per fornire i dati
- Paginazione
- Ordinamento impostabile sulla colonna (una o più)
- Filtro automatico impostabile sulla colonna (una o più), con fornitura della lista delle opzioni e delle label.
- Ricerca automatica impostabile sulla colonna (una o più)
- Tipi di colonne disponibili:
    - standard (con live editing opzionale, personalizzabile per specifica colonna)
    - switch per toggling di un campo enum "Y"|"N" (azione gestita via Livewire)
    - edit per pulsante di modifica  (azione gestita via Livewire)
    - copy per pulsante di copia  (azione gestita via Livewire), con callback impostabile globalmente o per specifica colonna
    - delete per pulsante di cancellazione  (azione gestita via Livewire)

## Installation

You can install the package via composer:

```bash
composer require sirfaenor/livewire-easytable
```
You can publish configuration and view via commands
```bash
php artisan vendor:publish --tag=leasytable-config
php artisan vendor:publish --tag=leasytable-views
```

## Usage
- creare un componente che estenda Sirfaenor\Leasytable\Http\Livewire\Table
- includere il componente livewire dove desiderato
### Configurazione tabella

E' possibile agire sulle seguenti proprietà del componente per personalizzare la tabella:

- `$pageSize` numero di record per pagina specifico per la tabella. Di default viene presa la chiave di configurazione impostata in `pagesize`
- `$defaultSortAttribute` per impostare attributo che viene usato per l'ordinamento di default. Deve essere il nome / attributo di una delle colonne configurate
- `$defaultSortDirection` (asc / desc) per impostare direzione di default per l'ordinamento
- implementare il metodo query() per ritornare il builder di default che sarà usato per il recupero delle righe, es:
```php
    protected function query(): Builder
    {
        return Tag::byLang(1);
    }
```
- implementare il metodo columns() per definire l'elenco delle colonne che andranno in output. Il metodo deve ritornare un array `<int, \Sirfaenor\Leasytable\Column>`. Vedi sotto per configurazione delle singole colonne. Es:
```php
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
```

### Configurazione delle colonne.
Ciascuna colonna è creata col metodo statico `make()`a cui passare il nome/attributo della colonna e un array opzionale di configurazione.

Ci sono 5 tipi di colonne disponibili.

#### StandardColumn
Tipologia colonna di base, manda in output il valore dell'attributo recuperandolo dal modello della riga corrente.
Metodi disponibili (tutti i metodi ritornano l'istanza e sono quindi concatenabili):

| Metodo                                                       | Descrizione                                                  | Parametri                                                    | Esempio |
| ------------------------------------------------------------ | ------------------------------------------------------------ | ------------------------------------------------------------ | ------- |
| heading(mixed $heading)                                      | Imposta intestazione della colonna.                          | mixed $heading <br />stringa del titolo o callback per personalizzare l'output |         |
| editLink()                                                   | Activates edit link on cell.                                 | -                                                            |         |
| formatUsing(callable $formatCallback)                        | Sets a callback to format model attribute and customize cell's output. |                                                              |         |
| searchable(callable $callback = null)                        | Sets a columns as searchable and store a callback to modify query builder. | callable $callback = null <br />se null utilizza di default una condizione like sull'attributo associato alla colonna |         |
| sortable(callable $callback = null)                          | Sets a columns as sortable and store a callback to modify query builder. | callable $callback = null<br />se null utilizza di default un "orderBy" sull'attributo  associato alla colonna |         |
| filterable(string $filterLabel, Collection $options, callable $valueCallback, callable $labelCallback, callable $filterCallback) | Sets a columns as filterable and store a callback to modify query builder | - string $filterLabel label of filter<br />- Collection $options list of options<br />- callable $valueCallback callback to get option's value<br />- callable $labelCallback callback to get option's label<br />- callable $filterCallback callback that will be applied on query builder to filter records |         |
| editable(callable $editableCallback, string $inputType = 'input') | Sets the column as live editable and stores a callback to be used to update the model. | - callable $editableCallback callback to be used to update the model<br />- string $inputType input\|textarea type of input |         |
| classes(string $classes)                                     | Add custom classes to `<td>` tag                             | string $classes list of classes                              |         |
| showInOrderList()                                            | Set the column to be shown in order list                     | -                                                            |         |

##### Note su ordinamento, ricerca e filtri
Quando si agisce su una colonna "sortable", "searchable" o "filterable", la query stringa viene aggiornata e lo stato viene memorizzato; quando si ritorna successivamente all'url (dove il componente viene montato) lo stato viene ripristinato. 
Per resettare lo stato memorizzato, è possibile impostare forzatamente sull'url il parametro `state=0`.

#### EditColumn
Mostra icona di edit sulla riga.

#### CopyColumn
Mostra icona di copia sulla riga. Al suo interno viene creato un componente `Sirfaenor\Leasytable\Http\Livewire\CopyColumnWidget`.
Al click sull'icona, viene eseguito il callback di copia impostato sulla colonna.
Il callback si può impostare in due modi:

- globalmente con il metodo static `globalCopyCallback(callable $callback)`
- in maniera specifica per la colonna con il metodo `copyCallback(callable $callback)`

Al callback viene passato il modello Eloquent della riga corrispondente e l'array di configurazione ricevuto  dal costruttore della colonna.

#### DeleteColumn

Mostra icona di cancellazione sulla riga. Al suo interno viene creato un componente `Sirfaenor\Leasytable\Http\Livewire\DeleteColumnWidget`.
Al click sull'icona, viene eseguito il callback di cancellazione impostato sulla colonna.
Il callback si può impostare in due modi:

- globalmente con il metodo static `globalDeleteCallback(callable $callback)`
- in maniera specifica per la colonna con il metodo `deleteCallback(callable $callback)`

Al callback viene passato il modello Eloquent della riga corrispondente e l'array di configurazione ricevuto  dal costruttore della colonna.

#### SwitchColumn
Mostra un toggler enum 'Y'/'N' sulla tabella.  Al suo interno viene creato un componente `Sirfaenor\Leasytable\Http\Livewire\SwitchColumnWidget`.
Al click sul toggler viene cambiato sul model della riga l'attributo corrispondente della tabella.

Questa colonna espone dei metodi aggiuntivi:

| Metodo     | Descrizione                                                  | Parametri |      |
| ---------- | ------------------------------------------------------------ | --------- | ---- |
| single()   | Imposta lo switch come singolo. Se un modello ha l'attributo impostato a 'Y', tutti gli altri verranno impostati su 'N' | -         |      |
| multiple() | Imposta lo switch come multipli. Possono coesistere modelli multipli con l'attributo impostato a 'Y'. | -         |      |

### Modalità di ordinamento
La classe Table espone un metodo `orderingModeToggle` che consente di attivare / disattivare la modalità di ordinamento per le righe.
Quando la modalità di ordinamento viene attivata, prima di eseguire il render viene chiamato il metodo `checkOrderingMode` che controlla se la modalità di ordinamento è disponibile. Il metodo deve ritornare una stringa vuota se l'ordinamento è disponibile, o una stringa contenente il messaggio di errore se l'ordinamento è bloccato. 
Sovrascrivere il metodo in ciascuna tabella per imporre condizioni specifiche (v. ad esempio la funzione demo "Product", che verifica la presenza di un filtro sulla categoria).

### Pulsanti aggiuntivi
Per aggiungere pulsanti all'intestazione della tabella, passare il parametro `actions` in fase di inizializzazione del componente.
Esempio
```html
@livewire("customer::datatable", [
    'actions' => [
        '<a class="uk-button uk-button-primary">Esporta elenco</a>',
    ]
])
```

## Roadmap
- [ ] traduzione del readme
- [ ] eliminazione residui di features di Marvin
- [ ] decoupling da uikit

## Testing

### Security

If you discover any security related issues, please email dev@atrio.it instead of using the issue tracker.

## Credits

-   [Emanuele Fornasier](https://github.com/sirfaenor)
-   [àtrio](https://www.atrio.it)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
