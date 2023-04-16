<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_types_id',
        'name',
        'phone_no',
        'phone_no_1',
        'citizenship_no',
        'passport_no',
        'license_no',
        'permanent_address',
        'temporary_address',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        //'phone_no' => 'array',
    ];

    public function clientType() : BelongsTo
    {
        return $this->belongsTo(ClientType::class, 'client_types_id', 'id');
    }

    public function clientDocument() : BelongsTo
    {
        return $this->belongsTo(ClientDocument::class, 'id', 'client_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
