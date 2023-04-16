<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Land extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'location',
        'kitta',
        'area',
        'price_per_area',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function landdocument()
    {
        return $this->hasMany(ClinetLandDocument::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['location'] ?? null, function ($query, $location) {
            $query->where('location', 'like', '%'.$location.'%');
        })
            ->when($filters['kitta'] ?? null, function ($query, $kitta) {
                $query->where('kitta', 'like', '%'.$kitta.'%');
            });
    }
}
