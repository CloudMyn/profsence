<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Concerns\HasStateBindingModifiers;

class MapInput extends Field
{
    use HasStateBindingModifiers;

    protected string $view = 'forms.components.map-input';

    protected array $props = [
        'height'        =>  '40vh',
        'draggable'     =>  true
    ];

    public function getProps()
    {
        return $this->props;
    }

    public function setHeight(string $height): self
    {
        $this->props['height'] = $height;

        return $this;
    }

    public function draggable(bool $enalble): self
    {
        $this->props['draggable'] = $enalble;

        return $this;
    }
}
