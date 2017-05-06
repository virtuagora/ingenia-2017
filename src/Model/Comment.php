<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{    
    protected $table = 'comments';
    protected $visible = ['id', 'content', 'votes', 'created_at', 'deleted_at', 'user', 'replies'];
    protected $with = ['user', 'replies'];
    
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Model\Project', 'project_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Model\Comment', 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany('App\Model\Comment', 'parent_id');
    }

    public function raters()
    {
        return $this->belongsToMany(
            'App\Model\User', 'comment_rater', 'comment_id', 'user_id'
        )->withPivot('value')->withTimestamps();
    }
}
