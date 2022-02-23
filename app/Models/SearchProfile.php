<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchProfile extends Model
{
    use HasFactory;
    // add has many relationship with search profile fields
    public function searchProfileFields()
    {
        return $this->hasMany(SearchProfileField::class, 'search_profile_id', 'id');
    }
}
