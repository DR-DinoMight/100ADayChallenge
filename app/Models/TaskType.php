<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'daily_goal',
        'is_built_in',
    ];

    protected $casts = [
        'daily_goal' => 'integer',
        'is_built_in' => 'boolean',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function scopeBuiltIn($query)
    {
        return $query->where('is_built_in', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_built_in', false);
    }
}
