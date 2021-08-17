<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoList extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'due_date',
        'status',
    ];

    protected $table = 'todo_list';

    // constants
    const STATUS_NOT_FINISH = 0;
    const STATUS_FINISHED = 1;
}
