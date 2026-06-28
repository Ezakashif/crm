<x-app-layout>
    <div class="py-6 max-w-3xl mx-auto">

        <h2 class="text-xl font-bold mb-6">Edit Task</h2>

        <form method="POST" action="{{ route('tasks.update', $task) }}"
              class="bg-white p-6 rounded-lg shadow space-y-4">

            @csrf
            @method('PUT')

            <input name="title" value="{{ $task->title }}"
                   class="w-full border p-2 rounded">

            <textarea name="description"
                      class="w-full border p-2 rounded">{{ $task->description }}</textarea>

            <select name="status" class="w-full border p-2 rounded">
                <option value="pending" @selected($task->status=='pending')>Pending</option>
                <option value="in_progress" @selected($task->status=='in_progress')>In Progress</option>
                <option value="completed" @selected($task->status=='completed')>Completed</option>
                <option value="cancelled" @selected($task->status=='cancelled')>Cancelled</option>
            </select>

            <select name="priority" class="w-full border p-2 rounded">
                <option value="low" @selected($task->priority=='low')>Low</option>
                <option value="medium" @selected($task->priority=='medium')>Medium</option>
                <option value="high" @selected($task->priority=='high')>High</option>
                <option value="urgent" @selected($task->priority=='urgent')>Urgent</option>
            </select>

            <input type="date" name="due_date"
                   value="{{ $task->due_date }}"
                   class="w-full border p-2 rounded">

            <button class="bg-green-600 text-white px-4 py-2 rounded">
                Update Task
            </button>

        </form>

    </div>
</x-app-layout>