<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $tasks = Task::when($search, function ($query) use ($search) {
            $query->where('title', 'like', "%$search%");
        })->latest()->get();

        $todo = $tasks->where('is_completed', false);
        $completed = $tasks->where('is_completed', true);

        $total = $tasks->count();
        $progress = $total > 0
            ? round(($completed->count() / $total) * 100)
            : 0;

        return view('dashboard', compact(
            'todo',
            'completed',
            'progress',
            'search'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'deadline' => 'required|date',
            'priority' => 'required'
        ]);

        Task::create([
            'title' => $request->title,
            'deadline' => $request->deadline,
            'priority' => $request->priority,
            'is_completed' => false
        ]);

        return redirect('/dashboard');
    }

    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return redirect('/dashboard');
    }

    public function complete($id)
    {
        $task = Task::findOrFail($id);
        $task->update(['is_completed' => true]);

        return redirect('/dashboard');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'deadline' => 'required|date',
            'priority' => 'required'
        ]);

        Task::findOrFail($id)->update([
            'title' => $request->title,
            'deadline' => $request->deadline,
            'priority' => $request->priority
        ]);

        return redirect('/dashboard');
    }
}
