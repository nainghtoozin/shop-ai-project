<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DeliveryType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'charge_type',
        'extra_charge',
        'description',
        'is_active',
    ];

    protected $casts = [
        'extra_charge' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isPercent(): bool
    {
        return $this->charge_type === 'percent';
    }
}
