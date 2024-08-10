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
        'draggable'     =>  true,
        'markers'       =>  [
            [
                'label'     =>  'Kampus',
                'radius'    =>  100,
                'lat'       =>  '1.4184228589651209',
                'lng'       =>  '124.98596191406251',
            ],
            [
                'label'     =>  'Kampus',
                'radius'    =>  100,
                'lat'       =>  '1.417102680056996',
                'lng'       =>  '124.98322606086732',
            ],
        ],
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

    public function setMarkers(array $markers): self
    {
        $this->props['markers'] = $markers;

        return $this;
    }
}
