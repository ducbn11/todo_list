<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Auth;
use Log;
use App\Models\User;
use App\Repositories\TodoList\TodoListRepositoryInterface;

class TodoListController extends Controller
{
    protected $todoListRepository;

    public function __construct(TodoListRepositoryInterface $todoListRepository)
    {
        $this->todoListRepository = $todoListRepository;
    }

    /**
     * get view todo list
     *
     * @param Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $currentUrl = url('/') . \Request::getRequestUri();
        $currentUrl = explode("&page=", $currentUrl)[0];
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $todoList = $this->todoListRepository->filter($data)->paginate(config('app.pagination_limit'));
        return view('todo_list.index', compact('user', 'todoList', 'data', 'currentUrl'));
    }

    /**
     * get view todo list
     *
     * @param Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function exportTodoList(Request $request) {
        $data = $request->all();
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $todoList = $this->todoListRepository->filter($data);
        $fileName = "todo_list_". time() .".csv";

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('ID', 'Title', 'Due Date', 'Status', 'Created');

        $file = fopen(storage_path('todo_list.csv'), 'w');
        fputcsv($file, $columns);
        $todoList->chunk(100, function($todos) use ($file) {
            foreach ($todos as $todo) {
                $status = "";
                if($todo->status == \App\Models\TodoList::STATUS_NOT_FINISH) {
                    $status = "Not finish";
                } else if ($todo->status == \App\Models\TodoList::STATUS_FINISHED) {
                    $status = "Finished";
                }

                fputcsv($file, array($todo->id, $todo->title, $todo->due_date, $status, $todo->created_at));
            }
        });
            
        fclose($file);
        return response()->download(storage_path('todo_list.csv'), $fileName, $headers)->deleteFileAfterSend(true);
    }

    /**
     * update todo status
     *
     * @param Illuminate\Http\Request
     * @return Json
     */
    public function updateTodoStatus(Request $request) {
        $id = $request->input('id');
        $status = $request->input('status');
        $todo = $this->todoListRepository->find($id);
        if($todo) {
            $todo->status = $status;
            $update = $todo->save();
            if($update) {
                return response()->json([
                    'status' => 1,
                    'data' => $todo,
                ]);
            }
        }

        return response()->json([
            'status' => 0,
        ]);
    }

    /**
     * go to todo item page
     *
     * @param Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function todoItem(Request $request) {
        $id = $request->id;
        $todo = [];
        if($id) {
            $todo = $this->todoListRepository->find($id);
        }
        return view('todo.index', compact('todo'));
    }

    /**
     * validate before save todo item
     *
     * @param Illuminate\Http\Request
     * @return Json
     */
    public function validateSaveTodoItem(Request $request) {
        try {
            $data = $request->all();
            $this->validate($request, [
                'title' => 'required',
                'due_date' => 'required',
                'status' => 'required',
            ]);

            return response()->json([
                'status' => 1
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 0,
                'errors' => $e->errors(),
            ]);
        }
    }

    /**
     * handle save todo item
     *
     * @param Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function saveTodoItem(Request $request) {
        try {
            $data = $request->all();

            $this->validate($request, [
                'title' => 'required',
                'due_date' => 'required',
                'status' => 'required',
            ]);

            $user = Auth::user();
            $data['user_id'] = $user->id;
            unset($data['id']);

            $id = $request->input('id', '');
            if($id) {
                $this->todoListRepository->update($id, $data);
            } else {
                $this->todoListRepository->create($data);
            }

            return redirect()->route('todo_list.index');
        } catch (ValidationException $e) {
            Log::info("saveTodoItem ValidationException " . $e->getMessage());
            return redirect()->back()->with('message', "Save fail!");
        } catch (Exception $e) {
            Log::info("saveTodoItem Exception " . $e->getMessage());
            return redirect()->back()->with('message', "Save fail!");
        }
    }

    /**
     * handle delete todo item
     *
     * @param Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function deleteTodoItem(Request $request) {
        try {
            $id = $request->id;
            if($id) {
                $todo = $this->todoListRepository->find($id);
                if($todo) {
                    $todo->delete();
                    return redirect()->back()->with('message', "Delete successfully!");
                }
            }

            return redirect()->back()->with('message', "Delete fail!");
        } catch (Exception $e) {
            Log::info("deleteTodoItem Exception " . $e->getMessage());
            return redirect()->back()->with('message', "Delete fail!");
        }
    }
}