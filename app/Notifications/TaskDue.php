<?php

namespace App\Notifications;

use App\Models\Task;
use App\Services\UserNotificationPreferenceService;
use Illuminate\Notifications\Notification;

class TaskDue extends Notification
{
    public function __construct(
        public Task $task,
        public string $tier,
    ) {}

    public function via(object $notifiable): array
    {
        return app(UserNotificationPreferenceService::class)->isEnabled($notifiable, self::class, 'database')
            ? ['database']
            : [];
    }

    public function toArray(object $notifiable): array
    {
        $isOverdue = $this->tier === 'overdue';
        $dueDate = $this->task->due_date?->format('M j, Y') ?? 'unknown date';

        return [
            'subject' => $isOverdue ? 'Task overdue' : 'Task due today',
            'message' => $isOverdue
                ? 'Your task '.$this->task->title.' was due on '.$dueDate.'.'
                : 'Your task '.$this->task->title.' is due today ('.$dueDate.').',
            'url' => route('tasks.show', $this->task, false),
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'due_date' => $this->task->due_date?->toDateString(),
            'tier' => $this->tier,
            'is_overdue' => $isOverdue,
        ];
    }
}
