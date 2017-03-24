<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{    
    protected $table = 'users';
    protected $visible = ['id', 'name', 'facebook', 'role', 'points'];
    protected $fillable = ['name', 'facebook', 'email'];
}
