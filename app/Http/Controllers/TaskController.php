<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with('user')->where('user_id', Auth::id())->paginate(10);

        return response()->json([
            'message' => 'Following are the Task with user',
            'data' => $tasks
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Find the user by ID
        $user = User::find($request->user_id);

        // Check if the user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = $request->all();
        Task::create($data);

        return response()->json(['message' => 'Task created successfully'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::with('user')->find($id);

        return response()->json([
            'message' => 'Following is the Task with user',
            'data' => $task
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Find the task by ID
        $task = Task::find($id);

        // Check if the task exists
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        // Update the task with the new data
        $task->update($request->all());

        return response()->json(['message' => 'Task updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::find($id);

        // Check if the task exists
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
