<?php
namespace App\Repositories\TodoList;

use App\Repositories\BaseRepository;
use App\Models\TodoList;

class TodoListRepository extends BaseRepository implements TodoListRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\TodoList::class;
    }

    /**
     * Filter
     * @param array $attributes
     * @return App\Models\TodoList
     */
    public function filter($attributes = []) {
        $todoList = TodoList::query();

        //filter by ID
        if(data_get($attributes, 'id')) {
            $todoList->where('id', data_get($attributes, 'id'));
        }

        //filter by user id
        if(data_get($attributes, 'user_id')) {
            $todoList->where('user_id', data_get($attributes, 'user_id'));
        }

        //filter by title
        if(data_get($attributes, 'title')) {
            $todoList->where('title', 'LIKE', '%' . data_get($attributes, 'title') . '%');
        }

        //filter by due date
        if(data_get($attributes, 'due_date')) {
            $todoList->whereDate('due_date', data_get($attributes, 'due_date'));
        }

        //filter by status
        if(data_get($attributes, 'status', '') != '') {
            $todoList->where('status', data_get($attributes, 'status'));
        }

        return $todoList;
    }

    /**
     * get first todo item of user
     *
     * @param $userId, $id
     * @return \App\Model\TodoItem
     */
    public function getFirstTodoItemOfUser($userId, $id) {
        $dataFilter = [];
        $dataFilter['id'] = $id;
        $dataFilter['user_id'] = $userId;

        return $this->filter($dataFilter)->first();
    }
}