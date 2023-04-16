<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinetLandDocument extends Model
{
    use HasFactory;

    public $table = 'client_lands_document';

    protected $fillable = [
        'land_id',
        'document',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'document_url',
    ];

    public function getDocumentUrlAttribute()
    {
        return url('/storage/'.$this->attributes['document']);
    }
}
