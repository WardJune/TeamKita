<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function taskOwn()
    {
        return $this->hasMany(Task::class, 'id');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function subTaskOwn()
    {
        return $this->hasMany(SubTask::class);
    }

    public function subTasks()
    {
        return $this->belongsToMany(SubTask::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function getAvatarPathAttribute()
    {
        return asset(Storage::url($this->avatar));
    }
}
