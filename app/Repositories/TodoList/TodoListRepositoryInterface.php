<?php
namespace App\Repositories\TodoList;

use App\Repositories\RepositoryInterface;

interface TodoListRepositoryInterface extends RepositoryInterface
{
    /**
     * Filter
     * @param array $attributes
     * @return App\Models\TodoList
     */
    public function filter($attributes = []);

     /**
     * get first todo item of user
     *
     * @param $userId, $id
     * @return \App\Model\TodoItem
     */
    public function getFirstTodoItemOfUser($userId, $id);
}