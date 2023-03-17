<?php

namespace App\Http\Controllers\Admin\Tasks\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\Task;
use App\Repositories\SearchRepo;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\ControllerTrait;

class TaskController extends Controller
{
    
    /**
     *  Controller Trait
     */
    use ControllerTrait;

    /**
     * return task's index view
     */
    public function index() {
        return view($this->folder.'task', []);
    }

    /**
     * store task
     */
    public function storeTask() {
        request()->validate($this->getValidationFields());
        $data = \request()->all();
        if(!isset($data['user_id'])) {
            if (Schema::hasColumn('tasks', 'user_id'))
                $data['user_id'] = request()->user()->id;
        }
         if(\request()->id){
             $action = "updated";
          }else{
            $action = "saved";
         }
        $this->autoSaveModel($data);
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Task '.$action.' successfully']);
    }

    /**
     * return task values
     */
    public function listTasks() {
        $tasks = Task::where([]);

        if(\request('all')) {
            if (Schema::hasColumn('tasks', 'status')) return $tasks->where('status', 1)->get();
            else return $tasks->get();
        }
        
        return SearchRepo::of($tasks)
            ->addColumn('action', function($task) {
                $str = '';
                $json = json_encode($task);
                $str .= '<a href="javascript:void" data-model="'.htmlentities($json, ENT_QUOTES, 'UTF-8').'" onclick="prepareEdit(this,\'task_modal\');" class="btn badge btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>';
            //    $str .= '&nbsp;&nbsp;<a href="javascript:void" onclick="deleteItem(\''.url(request()->user()->role.'/tasks/delete').'\',\''.$task->id.'\');" class="btn badge btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                return $str;
            })->make();
    }

    /**
     * toggle task status
     */
    public function toggleTaskStatus($task_id)
    {
        $task = Task::findOrFail($task_id);        
        $state = $task->status == 1 ? 'Deactivated' : 'Activated';
        $task->status = $task->status == 1 ? 0 : 1;
        $task->save();
        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Task #'.$task->id.' has been '.$state]);
    }
    
    /**
     * delete task
     */
    public function destroyTask($task_id)
    {
        $task = Task::findOrFail($task_id);
        $task->delete();
        return redirect()->back()->with('notice', ['type' => 'success','message' => 'Task deleted successfully']);
    }

}
