<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\StoreService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CarService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'about',
        'photo',
        'icon',
        'duration_in_hour',
    ];

    public function setNameAttribute($value)
   {
    $this->attributes['name']=$value;
    $this->attributes['slug']=Str::slug($value);

   }

    // public function storeServices():HasMany
    // {
    //     return $this->hasMany(StoreService::class, 'car_service_id');
    // }

    public function carStores(): BelongsToMany
    {
        return $this->belongsToMany(
            CarStore::class,
            'store_services',
            'car_service_id',
            'car_store_id'
        );
    }

}
