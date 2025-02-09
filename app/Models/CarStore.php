<?php

namespace App\Models;

use App\Models\City;
use App\Models\StorePhoto;
use Illuminate\Support\Str;
use App\Models\StoreService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CarStore extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        'name',
        'slug',
        'thumbnail',
        'is_open',
        'is_full',
        'address',
        'phone_number',
        'cs_name',
        'city_id',
    ];


    public function setNameAttribute($value)
   {
    $this->attributes['name']=$value;
    $this->attributes['slug']=Str::slug($value);
   }

    public function city():BelongsTo 
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function storePhotos():HasMany
    {
        return $this->hasMany(StorePhoto::class);
    }

    public function storeServices():HasMany
    {
        return $this->hasMany(StoreService::class, 'car_store_id');
    }
}
