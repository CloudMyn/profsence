<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLocation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeGetLocations($_): array
    {
        $locations      =   $this->get();

        $marker_data    =   [];

        foreach ($locations as $location) {
            $marker_data[] = [
                'id'        =>  $location->id,
                'label'     =>  $location->name,
                'radius'    =>  $location->radius,
                'lat'       =>  $location->latitude,
                'lng'       =>  $location->longitude,
                'color'     =>  $location->color,
            ];
        }

        return $marker_data;
    }
}
