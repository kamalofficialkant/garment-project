<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'rank',
        'volume',
        'avail',
    ];

    //Mutator
    public function setVolumeAttribute($value)
    {
        switch ($value) {
            case 'rack1':
                $this->attributes['volume'] = 10;
                break;
            case 'rack2':
                $this->attributes['volume'] = 20;
                break;
            case 'rack3':
                $this->attributes['volume'] = 12;
                break;
            case 'rack4':
                $this->attributes['volume'] = 8;
                break;
            case 'rack5':
                $this->attributes['volume'] = 20;
                break;
        }        
    }
}
