<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
class TaskController extends Controller
{
    public function index()
    {
       $tasks = Task::with(['assignee', 'customer', 'lead'])
    ->orderBy('status')
    ->orderBy('sort_order')
    ->get();

    $statuses = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

return view('tasks.index', compact('tasks', 'statuses'));
    }

    public function create()
    {
        $users = User::all();
         return view('tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required',
        ]);

        Task::create([
            'created_by' => auth()->id(),
            'assigned_to' => $request->assigned_to,
            'customer_id' => $request->customer_id,
            'lead_id' => $request->lead_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'pending',
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully');
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->all());

        // Auto timestamp when completed
        if ($request->status === 'completed' && !$task->completed_at) {
            $task->completed_at = now();
            $task->save();
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully');
    }

    public function updateBoard(Request $request)
{
    $request->validate([
        'task_id' => 'required|exists:tasks,id',
        'status' => 'required|in:pending,in_progress,completed,cancelled',
        'sort_order' => 'required|integer|min:0',
    ]);

    $task = Task::findOrFail($request->task_id);

    $task->status = $request->status;
    $task->sort_order = $request->sort_order;

    if ($request->status === 'completed') {
        $task->completed_at = now();
    } else {
        $task->completed_at = null;
    }

    $task->save();

    return response()->json([
        'success' => true
    ]);
}

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully');
    }
    // Implement the markComplete method to mark a task as completed

    public function markComplete(Task $task)
{
    $task->update([
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    return back()->with('success', 'Task completed');
}

public function changeStatus(Request $request, Task $task)
{
    $request->validate([
        'status' => 'required|in:pending,in_progress,completed'
    ]);

    $task->update([
        'status' => $request->status,
        'completed_at' => $request->status === 'completed' ? now() : null,
    ]);

    return back()->with('success', 'Task status updated');
}
}
