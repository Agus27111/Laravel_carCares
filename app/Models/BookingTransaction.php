<?php

namespace App\Models;

use App\Models\CarStore;
use App\Models\CarService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'trx_id',
        'phone_number',
        'is_paid',
        'proof',
        'total_amount',
        'started_at',
        'time_at',
        'car_store_id',
        'car_service_id',
    ];

    public function carStore():BelongsTo
    {
        return $this->belongsTo(CarStore::class, 'car_store_id');
    }
    public function carService():BelongsTo
    {
        return $this->belongsTo(CarService::class, 'car_service_id');
    }
}
