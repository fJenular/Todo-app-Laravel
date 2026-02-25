@extends('layouts.app')

@section('content')
<div class="container py-4">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
    /* Buttons */
    .btn-complete{border-radius:999px;box-shadow:0 2px 8px rgba(0,0,0,0.08);transition:transform .15s ease,box-shadow .15s ease,background-color .15s ease}
    .btn-complete .ri-checkbox-circle-line{transition:transform .15s ease}
    .btn-complete:hover{transform:translateY(-2px) scale(1.02);box-shadow:0 6px 18px rgba(0,0,0,0.12)}
    .btn-sm.btn-complete{padding-left:.9rem;padding-right:.9rem}
    .btn-ghost{transition:opacity .15s ease,transform .15s ease}
    .btn-ghost:hover{opacity:.95;transform:translateY(-1px)}

    /* Icons */
    .ri-edit-line,.ri-delete-bin-6-line,.ri-checkbox-circle-line{vertical-align:middle}

    /* Table row animation */
    @keyframes fadeInUp{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
    .animate-row{animation:fadeInUp .35s ease both;transition:box-shadow .15s ease,transform .15s ease}
    .animate-row:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,0.04)}

    /* Progress bar */
    .progress-bar{transition:width .7s cubic-bezier(.2,.9,.3,1)}

    /* Small helpers */
    .text-muted-soft{color:rgba(0,0,0,0.55)}
    /* Modern table: rounded, minimal, subtle hover */
    .table-card{border-radius:12px;overflow:hidden}
    .table-modern{border-collapse:separate;border-spacing:0;background:transparent}
    .table-modern thead th{background:transparent;color:#111;font-weight:600;border-bottom:none;padding:.9rem 1rem}
    .table-modern td,.table-modern th{padding:.75rem 1rem;vertical-align:middle;border-top:none}
    .table-modern tbody tr{transition:background .18s ease,transform .18s ease}
    .table-modern tbody tr:hover{background:rgba(13,110,253,0.04);transform:translateY(-2px)}
    .table-modern tbody tr:nth-child(odd) td{background:rgba(0,0,0,0.02)}
    .table-modern .badge{border-radius:999px;padding:.35em .6em}
    .table-responsive{overflow:hidden;border-radius:12px}
    /* soften card */
    .card.table-card{border-radius:12px}

    /* Dark mode styles */
    .dark-mode{background:#071022;color:#e6eef8}
    .dark-mode .container{color:#e6eef8}
    .dark-mode .card{background:#0b1220 !important;color:var(--muted)}
    .dark-mode .table-modern td,.dark-mode .table-modern th{color:#dbeafe}
    .dark-mode .table-modern tbody tr:hover{background:rgba(255,255,255,0.03)}
    .dark-mode .table-modern tbody tr:nth-child(odd) td{background:rgba(255,255,255,0.02)}
    .dark-mode .badge{box-shadow:none}
    .dark-mode .badge.bg-warning{background:#f59e0b;color:#0f1724}
    .dark-mode .badge.bg-success{background:#16a34a;color:#ecfdf5}
    .dark-mode .btn-complete{box-shadow:0 6px 20px rgba(2,6,23,0.6)}
    .dark-mode .btn-outline-primary,.dark-mode .btn-outline-danger,.dark-mode .btn-outline-secondary{border-color:rgba(255,255,255,0.06);color:#dbeafe}
    .dark-mode .progress{background:rgba(255,255,255,0.04)}
    .dark-mode .progress-bar.bg-info{background:#38bdf8}
    .dark-mode .ri-edit-line,.dark-mode .ri-delete-bin-6-line,.dark-mode .ri-checkbox-circle-line{color:inherit}
    </style>

    <h2 class="fw-bold mb-4">Dashboard</h2>

    {{-- ANALYTICS --}}
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 bg-primary-subtle">
                <small>Total Tasks</small>
                <h3>{{ $todo->count() + $completed->count() }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 bg-warning-subtle">
                <small>Pending</small>
                <h3 class="text-warning">{{ $todo->count() }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 bg-success-subtle">
                <small>Completed</small>
                <h3 class="text-success">{{ $completed->count() }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 bg-info-subtle">
                <small>Progress</small>
                <h4>{{ $progress }}%</h4>
                <div class="progress" style="height:6px;">
                    <div class="progress-bar bg-info"
                         style="width: {{ $progress }}%">
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- SEARCH + ADD --}}
    <div class="d-flex justify-content-between mb-3">
        <form method="GET" class="w-50">
            <input type="text" name="search"
                   value="{{ $search }}"
                   class="form-control"
                   placeholder="Search task...">
        </form>

        <div class="d-flex align-items-center gap-2">
            <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary d-flex align-items-center"
                    type="button"
                    aria-pressed="false"
                    title="Toggle dark mode">
                <i class="ri-moon-line"></i>
            </button>

            <button class="btn btn-dark d-flex align-items-center gap-2"
                    data-bs-toggle="modal"
                    data-bs-target="#addTaskModal">
                <i class="ri-add-line"></i>
                <span>Add Task</span>
            </button>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm table-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-borderless table-modern">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Deadline</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>

                @foreach($todo as $task)
                <tr class="animate-row">
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->deadline }}</td>
                    <td>
                        <span class="badge
                            @if($task->priority=='high') bg-danger
                            @elseif($task->priority=='medium') bg-warning
                            @else bg-secondary
                            @endif">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                    <td class="d-flex gap-2">

                        <form action="/tasks/{{ $task->id }}/complete" method="POST" class="me-1">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-success btn-complete d-flex align-items-center gap-2 fw-semibold px-3 text-nowrap"
                                    title="Mark as complete"
                                    aria-label="Mark task {{ $task->title }} as complete">
                                <i class="ri-checkbox-circle-line fs-5"></i>
                                <span class="fw-semibold">Complete</span>
                            </button>
                        </form>

                        <button class="btn btn-sm btn-outline-primary btn-ghost me-1 d-flex align-items-center"
                                data-bs-toggle="modal"
                                data-bs-target="#edit{{ $task->id }}"
                                title="Edit task">
                            <i class="ri-edit-line fs-6"></i>
                        </button>

                        <form action="/tasks/{{ $task->id }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger btn-ghost d-flex align-items-center"
                                    title="Delete task">
                                <i class="ri-delete-bin-6-line fs-6"></i>
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach

                @foreach($completed as $task)
                <tr class="table-light animate-row">
                    <td class="text-decoration-line-through">
                        {{ $task->title }}
                    </td>
                    <td>{{ $task->deadline }}</td>
                    <td><span class="badge bg-success">Done</span></td>
                    <td><span class="badge bg-success">Completed</span></td>
                    <td>
                        <form action="/tasks/{{ $task->id }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger d-flex align-items-center">
                                <i class="ri-delete-bin-6-line"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addTaskModal">
    <div class="modal-dialog">
        <form method="POST" action="/tasks" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5>Add Task</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="title" class="form-control mb-3" placeholder="Title" required>
                <input type="date" name="deadline" class="form-control mb-3" required>
                <select name="priority" class="form-select">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
@foreach($todo as $task)
<div class="modal fade" id="edit{{ $task->id }}">
    <div class="modal-dialog">
        <form method="POST" action="/tasks/{{ $task->id }}" class="modal-content">
            @csrf
            @method('PATCH')
            <div class="modal-header">
                <h5>Edit Task</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="title"
                       value="{{ $task->title }}"
                       class="form-control mb-3" required>
                <input type="date" name="deadline"
                       value="{{ $task->deadline }}"
                       class="form-control mb-3" required>
                <select name="priority" class="form-select">
                    <option value="low" {{ $task->priority=='low'?'selected':'' }}>Low</option>
                    <option value="medium" {{ $task->priority=='medium'?'selected':'' }}>Medium</option>
                    <option value="high" {{ $task->priority=='high'?'selected':'' }}>High</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark">Update</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection

<script>
(function(){
    const key = 'darkMode';
    const toggle = document.getElementById('darkModeToggle');
    const cls = 'dark-mode';
    function applyMode(isDark){
        if(isDark) document.documentElement.classList.add(cls);
        else document.documentElement.classList.remove(cls);
        if(toggle){
            toggle.innerHTML = isDark ? '<i class="ri-sun-line"></i>' : '<i class="ri-moon-line"></i>';
            toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            toggle.title = isDark ? 'Switch to light mode' : 'Switch to dark mode';
        }
    }
    try{
        const saved = localStorage.getItem(key);
        const prefers = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        let isDark = (saved === null) ? prefers : saved === '1';
        applyMode(isDark);
        if(toggle){
            toggle.addEventListener('click', function(){
                isDark = !document.documentElement.classList.contains(cls);
                applyMode(isDark);
                localStorage.setItem(key, isDark ? '1' : '0');
            });
        }
    }catch(e){console.warn('dark mode init failed', e)}
})();
</script>
