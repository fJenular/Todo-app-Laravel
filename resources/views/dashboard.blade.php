@extends('layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
@endpush

@section('content')

<div class="h-screen flex flex-col bg-gray-100 dark:bg-gray-900 transition-colors duration-300">

    <!-- ================= HEADER + STATS (FIXED) ================= -->
    <div class="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

        <div class="px-10 py-8">

            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
                        Task Dashboard
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Organize your workflow with clarity and focus.
                    </p>
                </div>

                <!-- Dark Mode Toggle -->
                <button id="darkToggle" type="button"
                    class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:scale-105 transition flex items-center justify-center"
                    aria-pressed="false" title="Toggle dark mode">
                    <i id="iconMoon" class="ri-moon-line text-lg text-gray-700" aria-hidden="true"></i>
                    <i id="iconSun" class="ri-sun-line text-lg text-yellow-400" aria-hidden="true" style="display:none"></i>
                </button>
            </div>

            <!-- Stats -->
            <div class="grid md:grid-cols-4 gap-6">

                <!-- PRIMARY CARD -->
                <div class="p-6 rounded-2xl bg-indigo-600 text-white shadow-lg">
                    <p class="text-xs uppercase tracking-wider opacity-80">Total Tasks</p>
                    <h2 class="text-3xl font-bold mt-3">
                        {{ $todo->count() + $completed->count() }}
                    </h2>
                </div>

                <!-- Secondary -->
                <div class="p-6 rounded-2xl bg-white dark:bg-gray-800 border dark:border-gray-700 shadow">
                    <p class="text-xs uppercase tracking-wider text-gray-500">Pending</p>
                    <h2 class="text-2xl font-semibold mt-3 text-yellow-500">
                        {{ $todo->count() }}
                    </h2>
                </div>

                <div class="p-6 rounded-2xl bg-white dark:bg-gray-800 border dark:border-gray-700 shadow">
                    <p class="text-xs uppercase tracking-wider text-gray-500">Completed</p>
                    <h2 class="text-2xl font-semibold mt-3 text-emerald-500">
                        {{ $completed->count() }}
                    </h2>
                </div>

                <div class="p-6 rounded-2xl bg-white dark:bg-gray-800 border dark:border-gray-700 shadow">
                    <p class="text-xs uppercase tracking-wider text-gray-500">Progress</p>
                    <div class="mt-4">
                        <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full">
                            <div class="h-2 bg-indigo-600 rounded-full transition-all duration-500"
                                style="width: {{ $progress }}%">
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-3">{{ $progress }}%</p>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- ================= TABLE AREA (SCROLLABLE) ================= -->

    <div class="flex-1 overflow-auto px-10 py-8">

        <!-- Search + Add -->
            <div class="flex justify-between items-center mb-6">

            <form method="GET" class="w-1/3">
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Search task..."
                    class="w-full px-5 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </form>

            <button id="openAddModal" type="button"
                class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-full shadow transition">
                <i class="ri-add-line mr-2"></i> Add Task
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow overflow-hidden">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">Title</th>
                        <th class="px-6 py-4 text-left">Deadline</th>
                        <th class="px-6 py-4 text-left">Priority</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                    @foreach($todo as $task)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-5 font-medium text-gray-800 dark:text-gray-100">
                            {{ $task->title }}
                        </td>

                        <td class="px-6 py-5 text-gray-500">
                            {{ $task->deadline }}
                        </td>

                        <td class="px-6 py-5">
                            <span class="px-3 py-1 text-xs rounded-full bg-indigo-100 text-indigo-600">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>

                        <td class="px-6 py-5">
                            <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-600">
                                Pending
                            </span>
                        </td>

                        <!-- ACTION HIERARCHY -->
                        <td class="px-6 py-5 text-center">
                            <div class="flex justify-center items-center gap-3">

                                <!-- PRIMARY -->
                                <form action="/tasks/{{ $task->id }}/complete" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="px-4 py-1.5 text-xs bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                                        Done
                                    </button>
                                </form>

                                <!-- ICON EDIT -->
                                <button type="button"
                                    class="text-blue-500 hover:scale-110 transition openEditModal"
                                    data-id="{{ $task->id }}"
                                    data-title="{{ htmlspecialchars($task->title, ENT_QUOTES) }}"
                                    data-deadline="{{ $task->deadline }}"
                                    data-priority="{{ $task->priority }}">
                                    <i class="ri-edit-line text-lg" aria-hidden="true"></i>
                                </button>

                                <!-- ICON DELETE -->
                                <form action="/tasks/{{ $task->id }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:scale-110 transition">
                                        <i class="ri-delete-bin-6-line text-lg" aria-hidden="true"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @endforeach

                    @foreach($completed as $task)
                    <tr class="opacity-60">
                        <td class="px-6 py-5 line-through text-gray-400">
                            {{ $task->title }}
                        </td>

                        <td class="px-6 py-5 text-gray-400">
                            {{ $task->deadline }}
                        </td>

                        <td class="px-6 py-5">
                            <span class="px-3 py-1 text-xs rounded-full bg-emerald-100 text-emerald-600">
                                Done
                            </span>
                        </td>

                        <td class="px-6 py-5">
                            <span class="px-3 py-1 text-xs rounded-full bg-emerald-100 text-emerald-600">
                                Completed
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <form action="/tasks/{{ $task->id }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-500 hover:scale-110 transition">
                                    <i class="ri-delete-bin-6-line text-lg" aria-hidden="true"></i>
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

<!-- ADD TASK MODAL (Tailwind) -->
<div id="addTaskModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" data-close="true"></div>
    <div class="modal-panel bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-2xl shadow-2xl max-w-lg w-full z-10 p-6 mx-auto transform transition-all duration-200 ease-out scale-95 opacity-0">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <i class="ri-add-line text-xl text-indigo-600"></i>
                <span>Add Task</span>
            </h3>
                <button type="button" data-close="true" class="text-gray-400 dark:text-gray-300 hover:text-gray-600 dark:hover:text-gray-100 rounded-md p-1">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <form method="POST" action="/tasks">
            @csrf
            <div class="space-y-3">
                <input name="title" type="text" required placeholder="Title" class="w-full px-3 py-2 rounded border border-gray-200 dark:border-gray-700 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-600">
                <input name="deadline" type="date" required class="w-full px-3 py-2 rounded border border-gray-200 dark:border-gray-700 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-600">
                <select name="priority" class="w-full px-3 py-2 rounded border border-gray-200 dark:border-gray-700 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-600">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-close="true" class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white text-sm shadow hover:shadow-md">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT TASK MODAL (reused) -->
<div id="editTaskModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" data-close="true"></div>
    <div class="modal-panel bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-2xl shadow-2xl max-w-lg w-full z-10 p-6 mx-auto transform transition-all duration-200 ease-out scale-95 opacity-0">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <i class="ri-edit-line text-xl text-yellow-500"></i>
                <span>Edit Task</span>
            </h3>
            <button type="button" data-close="true" class="text-gray-400 dark:text-gray-300 hover:text-gray-600 dark:hover:text-gray-100 rounded-md p-1">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <form id="editForm" method="POST" action="/tasks/0">
            @csrf
            @method('PATCH')
            <div class="space-y-3">
                <input id="editTitle" name="title" type="text" required placeholder="Title" class="w-full px-3 py-2 rounded border border-gray-200 dark:border-gray-700 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-600">
                <input id="editDeadline" name="deadline" type="date" required class="w-full px-3 py-2 rounded border border-gray-200 dark:border-gray-700 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-600">
                <select id="editPriority" name="priority" class="w-full px-3 py-2 rounded border border-gray-200 dark:border-gray-700 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-600">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-close="true" class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white text-sm shadow hover:shadow-md">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function(){
    const btn = document.getElementById('darkToggle');
    if(!btn) return;
    const moon = document.getElementById('iconMoon');
    const sun = document.getElementById('iconSun');

    // read saved preference, fall back to system preference
    function prefersDark() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    function isSavedDark(){
        try{ return localStorage.getItem('darkMode') === '1'; }catch(e){return null}
    }

    function setDark(on, persist=true){
        if(on) document.documentElement.classList.add('dark');
        else document.documentElement.classList.remove('dark');
        if(persist){
            try{ localStorage.setItem('darkMode', on ? '1' : '0'); }catch(e){}
        }
        // update icons / aria
        moon.style.display = on ? 'none' : 'inline-block';
        sun.style.display = on ? 'inline-block' : 'none';
        btn.setAttribute('aria-pressed', on ? 'true' : 'false');
    }

    // initialize: default to dark mode when no saved preference
    const saved = isSavedDark();
    if(saved === true) setDark(true, false);
    else if(saved === false) setDark(false, false);
    else setDark(true, false);

    btn.addEventListener('click', function(){
        const now = document.documentElement.classList.contains('dark');
        setDark(!now, true);
    });
})();
</script>
<script>
// Modal handling (add / edit)
(function(){
    const modalTransitionMs = 220;
    function openModal(modal){
        if(!modal) return;
        const panel = modal.querySelector('.modal-panel');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        // allow DOM to update then animate
        requestAnimationFrame(function(){
            if(panel){
                panel.classList.remove('opacity-0','scale-95');
                panel.classList.add('opacity-100','scale-100');
            }
        });
        document.body.style.overflow = 'hidden';
    }
    function closeModal(modal){
        if(!modal) return;
        const panel = modal.querySelector('.modal-panel');
        if(panel){
            panel.classList.remove('opacity-100','scale-100');
            panel.classList.add('opacity-0','scale-95');
        }
        // wait for transition then hide
        setTimeout(function(){
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, modalTransitionMs);
    }

    // global close handlers
    document.querySelectorAll('[data-close]').forEach(function(btn){
        btn.addEventListener('click', function(e){
            // find parent modal
            const modal = btn.closest('#addTaskModal, #editTaskModal');
            closeModal(modal);
        });
    });

    // overlay click
    document.querySelectorAll('#addTaskModal [data-close], #editTaskModal [data-close]').forEach(function(el){
        el.addEventListener('click', function(e){
            const modal = el.closest('#addTaskModal, #editTaskModal');
            closeModal(modal);
        });
    });

    // open add modal
    const openAdd = document.getElementById('openAddModal');
    if(openAdd){
        openAdd.addEventListener('click', function(){
            const m = document.getElementById('addTaskModal');
            // reset form
            m.querySelector('form').reset();
            openModal(m);
        });
    }

    // open edit modal buttons
    document.querySelectorAll('.openEditModal').forEach(function(btn){
        btn.addEventListener('click', function(){
            const id = btn.getAttribute('data-id');
            const title = btn.getAttribute('data-title') || '';
            const deadline = btn.getAttribute('data-deadline') || '';
            const priority = btn.getAttribute('data-priority') || 'low';

            const m = document.getElementById('editTaskModal');
            const form = document.getElementById('editForm');
            form.action = '/tasks/' + id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editDeadline').value = deadline;
            document.getElementById('editPriority').value = priority;
            openModal(m);
        });
    });

    // close on ESC
    window.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){
            ['addTaskModal','editTaskModal'].forEach(function(id){
                const m = document.getElementById(id);
                if(m && m.classList.contains('flex')) closeModal(m);
            });
        }
    });
})();
</script>
@endpush
