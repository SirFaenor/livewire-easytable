<?php

namespace Sirfaenor\Leasytable;

use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

/**
 * Copy button for a row.
 */
class CopyColumn extends Column
{
    public $attribute = 'copy';

    protected $heading = 'Copia';

    protected $classes = 'tools copy';

    /**
     * Extra configuration.
     * It will be forwarded to callback.
     */
    public $config;

    /**
     * Global callback to be used in "copy" action.
     */
    protected static $globalCopyCallback;

    /**
     * Specific column callback to be used in "copy" action.
     */
    protected $copyCallback;


    /**
     * Return copy comuns widget (livewire)
     */
    public function output(Model $model, $functionCode = null)
    {
        // ritorno componente livewire
        return Livewire::mount('leasytable::copy_column_widget', [
            'model' => $model,
            'heading' => $this->heading,
            'attribute' => $this->attribute,
        ])->html();
    }

    /**
     * Set global callback to be used in "copy" action.
     * To override per specific column, use copyCallback method during
     * column configuration
     */
    public static function globalCopyCallback(callable $callback): void
    {
        static::$globalCopyCallback = $callback;
    }


    /**
     * Set specific callback to be used in "copy" action.
     */
    public function copyCallback(callable $callback): self
    {
        $this->copyCallback = $callback;

        return $this;
    }


    /**
     * Get stored callback
     */
    public function getCopyCallback(): callable
    {
        return $this->copyCallback ?? static::$globalCopyCallback;
    }
}
