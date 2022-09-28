<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class TasksController extends Controller
{
    public function index()
    {
        $allTasks = Task::with('project')->orderBy('priority','asc')->get();
        $allProjects = Project::get();
        $data = [
            'tasks' => $allTasks,
            'projects' => $allProjects
        ];
        return Response::json([
            'initialData' => $data,            
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $dataValidation = Validator::make($data, [
            'name' => 'required',
            'project' => 'required'
        ]);
        
        if ($dataValidation->fails()) {
            return Response::json([
                'status' => 400,
                'message' => 'Please provide the name and the project'            
            ]);
        }

        try {
            DB::beginTransaction();
            $lastTaskSaved = Task::latest()->first();
            $taskPriority = $lastTaskSaved
             ? $lastTaskSaved->priority + 1
             : 1;
            
            $newTask = [
                'id' => Str::uuid()->toString(),
                'name' => $data['name'],
                'project_id' => $data['project'],
                'priority' => $taskPriority,
                'created_at' => now(),
                'updated_at' => now()
            ];

            Task::insert($newTask);
            DB::commit();
            return Response::json([
                'status' => 201,
                'message' => 'Task created successfully'            
            ]);

        } catch (\Throwable $th) {
            DB::rollback();
            return Response::json([
                'status' => 500,
                'message' => 'An error occured...please try again'            
            ]);
        }
    }

    public function show($id)
    {
        $taskToEdit = Task::with('project')->find($id);
        return $taskToEdit ? 
        
        Response::json([
            'status' => 200,
            'task' => $taskToEdit            
        ])
        :
        Response::json([
            'status' => 404,
            'message' => 'Task Not Found'            
        ]);           
    }

    public function update(Request $request, $id)
    {
        $taskToEdit = Task::with('project')->find($id);
        $data = $request->all();       
        try {
            DB::beginTransaction();
            Task::find($id)->update([
                'name' => ($data && $data['name']) ? $data['name'] : $taskToEdit->name,
                'project_id' => ($data && $data['project']) ? $data['project'] : $taskToEdit->project_id,
            ]);
            DB::commit();
            return Response::json([
                'status' => 200,
                'message' => 'Task updated successfully'            
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return Response::json([
                'status' => 500,
                'message' => 'An error occured...please try again'            
            ]);
        }
    }

    public function destroy($id)
    {
        $taskToDelete = Task::with('project')->find($id);
        // deleting task and update priorities from the tasks with higher priority
        $tasksWithHigherPriority = Task::where('priority','>', $taskToDelete->priority)->get();
        try {
            if (count($tasksWithHigherPriority) > 0) {
                foreach ($tasksWithHigherPriority as $task) {
                    $task->update([
                        'priority' => $task->priority - 1
                    ]);
                }
            }
            $taskToDelete->delete();
            return Response::json([
                'status' => 200,
                'message' => 'Task deleted successfully and priorities updated'            
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return Response::json([
                'status' => 500,
                'message' => 'An error occured...please try again'            
            ]);
        }
    }
}
