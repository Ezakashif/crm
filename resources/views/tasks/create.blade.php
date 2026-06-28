<x-app-layout>
    <div class="py-6 max-w-3xl mx-auto">

        <h2 class="text-xl font-bold mb-6">Create Task</h2>

        <form method="POST" action="{{ route('tasks.store') }}"
              class="bg-white p-6 rounded-lg shadow space-y-4">

            @csrf

            <input name="title" placeholder="Task Title"
                   class="w-full border p-2 rounded">

            <textarea name="description" placeholder="Description"
                      class="w-full border p-2 rounded"></textarea>

            <select name="priority" class="w-full border p-2 rounded">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
            </select>

            <input type="date" name="due_date"
                   class="w-full border p-2 rounded">

            <select name="assigned_to" class="w-full border p-2 rounded">
                <option value="">-- Select User --</option>

                @foreach($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                        @if(isset($user->role))
                            - {{ ucfirst($user->role) }}
                        @endif
                    </option>
                @endforeach
            </select>

            <button class="bg-indigo-600 text-white px-4 py-2 rounded">
                Save Task
            </button>

        </form>

    </div>
</x-app-layout>