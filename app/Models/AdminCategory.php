<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminCategory extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'status',
        'sort_order',
    ];

    public function parent()
    {
        return $this->belongsTo(AdminCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(AdminCategory::class, 'parent_id');
    }
}