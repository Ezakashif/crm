<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|string',
        ]);

        $tasks = Task::visibleTo(auth()->user())
            ->with(['assignee', 'customer', 'lead'])
            ->search($filters['search'] ?? null)
            ->status($filters['status'] ?? null)
            ->priority($filters['priority'] ?? null)
            ->assignedTo($filters['assigned_to'] ?? null)
            ->orderBy('status')
            ->orderBy('sort_order')
            ->get();

        $statuses = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $priorities = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];

        $users = auth()->user()->canViewAllTasks()
            ? User::active()->orderBy('name')->get()
            : collect();

        return view('tasks.index', compact('tasks', 'statuses', 'priorities', 'filters', 'users'));
    }

    public function create()
    {
        $this->authorize('create', Task::class);

        $users = User::active()->orderBy('name')->get();

        return view('tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $task = Task::create([
            'created_by' => auth()->id(),
            'assigned_to' => $validated['assigned_to'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'status' => 'pending',
            'due_date' => $validated['due_date'] ?? null,
        ]);

        ActivityLogger::log('task.created', $task, [
            'title' => $task->title,
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully');
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        $users = auth()->user()->canViewAllTasks()
            ? User::active()->orderBy('name')->get()
            : collect();

        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
        ];

        if (auth()->user()->can('assign', $task)) {
            $rules['assigned_to'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        $previousStatus = $task->status;

        $task->fill($validated);

        if ($validated['status'] === 'completed') {
            $task->completed_at = $task->completed_at ?? now();
        } else {
            $task->completed_at = null;
        }

        $task->save();

        ActivityLogger::log('task.updated', $task, [
            'title' => $task->title,
        ]);

        if ($previousStatus !== $task->status) {
            ActivityLogger::log('task.status_changed', $task, [
                'from' => $previousStatus,
                'to' => $task->status,
            ]);
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

        $this->authorize('update', $task);

        $previousStatus = $task->status;

        $task->status = $request->status;
        $task->sort_order = $request->sort_order;
        $task->completed_at = $request->status === 'completed' ? now() : null;
        $task->save();

        if ($previousStatus !== $task->status) {
            ActivityLogger::log('task.status_changed', $task, [
                'from' => $previousStatus,
                'to' => $task->status,
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        ActivityLogger::log('task.deleted', $task, [
            'title' => $task->title,
        ]);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully');
    }

    public function markComplete(Task $task)
    {
        $this->authorize('update', $task);

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Task completed');
    }

    public function changeStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $previousStatus = $task->status;

        $task->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        if ($previousStatus !== $request->status) {
            ActivityLogger::log('task.status_changed', $task, [
                'from' => $previousStatus,
                'to' => $request->status,
            ]);
        }

        return back()->with('success', 'Task status updated');
    }
}
