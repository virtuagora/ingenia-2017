<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{    
    protected $table = 'projects';
    protected $visible = ['id', 'name', 'jotform', 'description', 'has_image', 'likes', 'user'];
    protected $fillable = ['name', 'jotform', 'description', 'user_id'];
    protected $with = ['user'];
    
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function voters()
    {
        return $this->belongsToMany('App\Model\User', 'project_user', 'project_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Model\Comment', 'project_id');
    }
}
