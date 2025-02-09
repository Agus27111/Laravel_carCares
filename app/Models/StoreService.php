<?php

namespace App\Models;

use App\Models\CarStore;
use App\Models\CarService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreService extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store_services';
    
    protected $fillable = [
        'car_service_id',
        'car_store_id',
    ];

    public function carStore():BelongsTo
    {
        return $this->belongsTo(CarStore::class,'car_store_id');
    }
    public function carService():BelongsTo
    {
        return $this->belongsTo(CarService::class,'car_service_id');
    }
}
