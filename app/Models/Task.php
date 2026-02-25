<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
    'title',
    'description',
    'deadline',
    'priority',
    'is_completed'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function getIsUrgentAttribute()
{
return !$this->is_completed &&
now()->diffInHours($this->deadline, false) <= 24;
}
}
