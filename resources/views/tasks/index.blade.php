<x-app-layout>

    <div class="py-6 max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Tasks</h2>

            <a href="{{ route('tasks.create') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded">
                + Add Task
            </a>
        </div>

        <div class="grid grid-cols-4 gap-6">

            @foreach($statuses as $statusKey => $statusTitle)

                <div class="rounded-xl border p-4 shadow {{ $statusColors[$statusKey] ?? 'bg-gray-100 border-gray-300' }}">

                    <!-- Fixed Header -->
                    <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-300">

                        <h3 class="font-bold text-lg">
                            {{ $statusTitle }}
                        </h3>

                        <span class="bg-white rounded-full px-3 py-1 text-xs font-semibold">
                            {{ $tasks->where('status', $statusKey)->count() }}
                        </span>

                    </div>

                    <!-- ONLY THIS DIV IS DRAGGABLE -->
                    <div class="task-column min-h-[350px] space-y-3"
                        data-status="{{ $statusKey }}">
                  
                        @php
    $statusColors = [
        'pending'     => 'bg-yellow-100 border-yellow-300',
        'in_progress' => 'bg-blue-100 border-blue-300',
        'completed'   => 'bg-green-100 border-green-300',
        'cancelled'   => 'bg-pink-100 border-pink-300',
    ];
@endphp

                        @foreach($tasks->where('status', $statusKey) as $task)

                            <div class="task-card bg-white rounded-xl shadow-sm hover:shadow-md transition p-4 cursor-move"
                                data-task-id="{{ $task->id }}">

                                <h4 class="font-semibold text-gray-800">
                                    {{ $task->title }}
                                </h4>

                                @if($task->description)
                                    <p class="text-sm text-gray-500 mt-2">
                                        {{ Str::limit($task->description, 80) }}
                                    </p>
                                @endif

                                <div class="flex justify-between items-center mt-4 text-xs">

                                    <span class="text-gray-600">
                                        👤 {{ optional($task->assignee)->name ?? 'Unassigned' }}
                                    </span>

                                    <span class="text-gray-500">
                                        📅
                                        {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M') : '-' }}
                                    </span>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            @endforeach

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('.task-column').forEach(column => {

                new Sortable(column, {

                    group: 'kanban',

                    animation: 200,

                    draggable: '.task-card',

                    ghostClass: 'opacity-50',

                    onEnd: function (evt) {

                        const taskId = evt.item.dataset.taskId;
                        const status = evt.to.dataset.status;
                        const sortOrder = evt.newIndex + 1;

                        fetch("{{ route('tasks.board.update') }}", {

                            method: "POST",

                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },

                            body: JSON.stringify({
                                task_id: taskId,
                                status: status,
                                sort_order: sortOrder
                            })

                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) {
                                alert('Unable to update task.');
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            alert('Something went wrong.');
                        });

                    }

                });

            });

        });
    </script>

</x-app-layout>