<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'citizenship',
        'pan',
        'passport',
        'photo',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'citizenship_url',
        'pan_url',
        'passport_url',
        'photo_url',
    ];

    public function getCitizenshipUrlAttribute()
    {
        return url('/storage/'.$this->attributes['citizenship']);
    }

    public function getPanUrlAttribute()
    {
        return ($this->attributes['pan'] == null) ? null : url('/storage/'.$this->attributes['pan']);
    }

    public function getPassportUrlAttribute()
    {
        return ($this->attributes['passport'] == null) ? null : url('/storage/'.$this->attributes['passport']);
    }

    public function getPhotoUrlAttribute()
    {
        return url('/storage/'.$this->attributes['photo']);
    }
}
