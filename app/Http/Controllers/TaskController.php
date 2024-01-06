<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;

class TaskController extends Controller
{
    public function index(){
        return view('welcome');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'description' => ['nullable'],
            'priority' => ['required', 'in:low,medium,high'],
            'image_name' => ['required']
        ]);

        if ($validator->errors()->isNotEmpty()) {
            return response()->json([
                'data' => $validator->errors()->first()
            ],403);
        }

        $data = Task::create([
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority,
            'image_name' => $request->image_name,
        ]);
        return response()->json([
            'data' => $data
        ],200);
    }

    public function fileUpload(Request $request){
        $image = $request->file('file');
        $file_name = "file_".time()."_".$image->getClientOriginalName();
        $image->move('photos',$file_name);        
        return $file_name;
    }

    public function list(){
        $tasks = Task::all();
        return response()->json([
            'data' => $tasks
        ],200);
    }

    public function update(Request $request){
        $task = Task::find($request->id);
        $task->priority = $request->priority;
        $task->save();
        return response()->json([
            'data' => $task
        ],200);
    }

    public function updateData(Request $request){
        $task = Task::find($request->id);
        $task->name = $request->name;
        $task->description = $request->description;
        $task->save();
        return response()->json([
            'data' => $task
        ],200);
    }

    public function delete(Request $request){
        $task = Task::find($request->id);
        $task->delete();
        return response()->json([
            'data' => "deleted"
        ],200);
    }
}
