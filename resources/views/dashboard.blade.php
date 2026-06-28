<x-app-layout>
    <x-slot name="header">
        <h1 class="m-0">Dashboard</h1>
    </x-slot>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $customerCount }}</h3>
                    <p>Customers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('customers.index') }}" class="small-box-footer">
                    View all <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $leadCount }}</h3>
                    <p>Leads</p>
                </div>
                <div class="icon">
                    <i class="fas fa-funnel-dollar"></i>
                </div>
                <a href="{{ route('leads.index') }}" class="small-box-footer">
                    View all <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $taskCount }}</h3>
                    <p>Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <a href="{{ route('tasks.index') }}" class="small-box-footer">
                    View all <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $pendingTasks }}</h3>
                    <p>Pending Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('tasks.index') }}" class="small-box-footer">
                    View tasks <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body d-flex align-items-center">
            <x-user-avatar :user="Auth::user()" :size="64" class="mr-3" />
            <div>
                <h3 class="card-title mb-1">Welcome back, {{ Auth::user()->name }}</h3>
                <p class="text-muted mb-0">Use the sidebar to manage customers, leads, and tasks.</p>
            </div>
        </div>
    </div>
</x-app-layout>
