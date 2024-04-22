<?php

namespace Sirfaenor\Leasytable;

use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

/**
 * Delete button for a row.
 */
class DeleteColumn extends Column
{
    protected $heading = 'Cancella';

    protected $classes = 'tools delete';

    /**
     * Default attribute
     */
    public $attribute = 'delete';

    /**
     * Extra configuration.
     * It will be forwarded to callback.
     */
    public $config;

    /**
     * Global callback to be used in "copy" action.
     */
    protected static $globalDeleteCallback;

    /**
     * Specific column callback to be used in "copy" action.
     */
    protected $deleteCallback;

    /**
     * Return delete widget (livewire component)
     */
    public function output(Model $model, $functionCode = null)
    {
        // ritorno componente livewire
        return Livewire::mount('leasytable::delete_column_widget', [
            'model' => $model,
            'attribute' => $this->attribute,
        ]);
    }

    /**
     * Set global callback to be used in "delete" action.
     * To override per specific column, use deleteCallback method during
     * column configuration
     */
    public static function globalDeleteCallback(callable $callback): void
    {
        static::$globalDeleteCallback = $callback;
    }


    /**
     * Set specific callback to be used in "copy" action.
     */
    public function deleteCallback(callable $callback): self
    {
        $this->deleteCallback = $callback;

        return $this;
    }


    /**
     * Get stored callback
     */
    public function getDeleteCallback(): callable
    {
        return $this->deleteCallback ?? static::$globalDeleteCallback;
    }
}
