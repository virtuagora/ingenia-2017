<?php
namespace App\Util;

class Installer {

    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function up() {
        $this->db->schema()->create('options', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type'); //integer, string, text, hidden
            $table->string('group');
            $table->boolean('autoload');
        });
        $this->db->schema()->create('users', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('facebook')->unique();
            $table->string('password')->nullable();
            $table->string('name');
            $table->string('role'->default('usr'));
            $table->integer('points')->default(0);
            $table->boolean('banned')->default(false);
            $table->string('trace')->nullable();
            $table->timestamps();
        });
        $this->db->schema()->create('user_meta', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('key');
            $table->text('value');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('key');
        });
        $this->db->schema()->create('projects', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('jotform')->unique();
            $table->boolean('has_image')->default(false);

            $table->string('group');
            $table->text('description');
            $table->text('foundation');
            $table->string('category');
            $table->text('execution')->nullable();
            $table->string('place');
            $table->json('schedule');
            $table->json('budget');
            $table->string('total_budget');
            $table->string('organization')->nullable();

            $table->integer('likes')->default(0);
            $table->string('name_trace')->nullable();
            $table->string('group_trace')->nullable();

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
        $this->db->schema()->create('project_user', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->boolean('is_public');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->timestamps();
        });
        $this->db->schema()->create('comments', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('content');
            $table->integer('votes')->default(0);
            $table->text('meta')->default('{}');
            $table->integer('project_id')->unsigned()->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
        $this->db->schema()->create('comment_rater', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('value');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('comment_id')->unsigned();
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            $table->timestamps();
        });

        $this->db->schema()->create('pages', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('link')->nullable();
            $table->text('meta')->default('{}');
            $table->string('slug');
            $table->integer('order')->default(0);
            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('pages')->onDelete('set null');
            $table->integer('node_id')->unsigned()->nullable();
            $table->foreign('node_id')->references('id')->on('nodes')->onDelete('cascade');
        });

    }
}
