<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Nilambar\NepaliDate\NepaliDate;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'land_id',
        'price_per_anna',
        'nepali_date',
        'type',
        'income',
        'expenses',
        'total_paid_amount',
        'commission_rate',
        'total_commission',
        'cheque_exchange_date',
        'photo',
        'total_commision_after_rate',
        'descriptions',
        'ischeque',
        'cheque_no',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class, 'land_id');
    }

    public function scopeLand($query, array $filters)
    {
        $query->when($filters['land_id'] ?? null, function ($query, $landId) {
            $query->whereRelation('land', 'id', 'like', '%'.$landId.'%');
        });
    }

    public function scopeClient($query, array $filters)
    {
        $query->when($filters['client_id'] ?? null, function ($query, $clientId) {
            $query->whereRelation('client', 'id', 'like', '%'.$clientId.'%');
        });
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['nepali_date'] ?? null, function ($query, $nepaliDate) {
            $query->where('nepali_date', 'like', '%'.$nepaliDate.'%');
        })
            ->when($filters['type'] ?? null, function ($query, $type) {
                $query->where('type', 'like', '%'.$type.'%');
            });
    }

    public function scopeReconsilation($query, array $filters)
    {
        $query->when($filters['type'] ?? null, function ($query, $type) {
            $query->where('type', '=', $type);
        })->when($filters['year_month'] ?? null, function ($query, $yearMonth) {
            $query->where('nepali_date', 'like', '%'.$yearMonth.'%');
        });
    }

    public function getPhotoUrlAttribute()
    {
        return ($this->attributes['photo'] == null) ? null : url('/storage/'.$this->attributes['photo']);
    }

    public function setChequeExchangeDateAttribute($value)
    {
        $obj = new NepaliDate();

        if (isset($value)) {
            $nepDate = explode('/', $value);
            $date = $obj->convertBsToAd($nepDate[0], $nepDate[1], $nepDate[2]);
            $engDate = "{$date['year']}/{$date['month']}/{$date['day']}";
            $this->attributes['cheque_exchange_date'] = $engDate;
        } else {
            $this->attributes['cheque_exchange_date'] = null;
        }
    }

    public function getChequeExchangeDateAttribute()
    {
        $obj = new NepaliDate();
        if (isset($this->attributes['cheque_exchange_date'])) {
            $engDate = explode('/', $this->attributes['cheque_exchange_date']);
            $date = $obj->convertAdToBs($engDate[0], $engDate[1], $engDate[2]);

            return "{$date['year']}/{$date['month']}/{$date['day']}";
        } else {
            return  null;
        }
    }
}
