<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'priority', //['low', 'medium', 'high']
        'image_name',
        'is_completed'// bool
    ];

    public function getPhotoUrlAttribute(){
        return config('app.url')."/photos/".$this->photo;
    }
}
