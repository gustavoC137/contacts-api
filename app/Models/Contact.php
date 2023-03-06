<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['contact', 'contact_type'];

    public function contacts(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
