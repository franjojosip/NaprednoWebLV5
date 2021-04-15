<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $table = 'tasks';

    public function owner() {
        return $this->belongsTo(User::class);
    }

    public function studyType() {
        return $this->belongsTo(StudyType::class);
    }
    
    public function users() {
        return $this->belongsToMany(User::class, 'task_users', 'task_id', 'user_id');
    }

    public function asignedUsers() {
        return $this->hasMany(User::class);
    }
}
