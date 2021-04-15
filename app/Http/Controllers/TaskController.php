<?php

namespace App\Http\Controllers;

use App\Models\StudyType;
use App\Models\Task;
use App\Models\TaskUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function add()
    {
        $study_types = StudyType::all();
        return view('tasks.add')->with('study_types', $study_types);
    }

    public function create(Request $request)
    {
        $owner = Auth::user();
        $study_type = StudyType::whereId($request->input('study_type_id'))->first();

        $task = new Task;
        $task->owner()->associate($owner);
        $task->studyType()->associate($study_type);
        $task->title = $request->input('title');
        $task->title_in_english = $request->input('title_in_english');
        $task->task = $request->input('task');

        $task->save();

        return redirect('/tasks')->with('success', 'Task created successfully!');
    }

    public function index()
    {
        $role_name = auth()->user()->role->name;
        $tasks = array();
        $thesis_reserved = false;

        if ($role_name == 'Teacher') {
            $tasks = Task::where('owner_id', Auth::id())->orderBy('title', 'asc')->get();
        } else if ($role_name == 'Student') {
            $user_tasks = TaskUsers::where('user_id', Auth::id())->get();

            if (count($user_tasks) == 1) {
                $check_tasks = Task::whereId($user_tasks[0]->task_id)->get();
                //Ako je korisnik rezervirao već temu može vidjeti samo tu temu koju je rezervirao
                if ($check_tasks->first()->is_reserved) {
                    $thesis_reserved = true;
                    $tasks = $check_tasks;
                }
            }
            if (!$thesis_reserved) {
                //Ako korisniku nijedna tema još nije odobrena može rezervirati novu
                $tasks = Task::where('is_reserved', '==', 0)->orderBy('title', 'asc')->get();
            }
        } else {
            $tasks = Task::all()->orderBy('title', 'asc')->get();
        }

        foreach ($tasks as $task) {
            $task['study_type'] = $task->studyType->name;
            if ($role_name == 'Student') {
                //Ako je korisnik odabrao temu pisat će mu da je odabrana za moguću temu
                $task['is_selected'] = count(TaskUsers::where('user_id', Auth::id())->where('task_id', $task->id)->get())  > 0;
            } else if ($role_name == 'Teacher' && $task['is_reserved']) {
                //Ako je uloga profesor i rad je rezerviran pisat će ime tog studenta
                $task['student_name'] = $task->users()->first()->name;
            }
        }
        return view('tasks.home', compact('tasks', 'thesis_reserved'));
    }

    public function update($id)
    {
        $task = Task::find($id);
        if ($task == null) {
            return redirect('/');
        }
        $study_types = StudyType::all();
        $selected_type = $task->study_type_id;

        return view('tasks.update', compact('task', 'study_types', 'selected_type'));
    }

    public function edit(Request $request, $id)
    {
        $task = Task::find($id);

        $task->title = $request->input('title');
        $task->title_in_english = $request->input('title_in_english');
        $task->task = $request->input('task');
        $task->study_type_id = $request->input('study_type_id');
        $task->save();
        return redirect('/tasks')->with('success', 'Task updated successfully!');
    }

    public function delete($id)
    {
        $task = Task::find($id);
        if ($task != null && ($task->owner_id == Auth::id() || auth()->user()->role->name == 'Administrator')) {
            Task::where('id', $id)->delete();
            return redirect('/tasks')->with('success', 'Task deleted succesffully!');
        }
        return redirect('/tasks')->with('warning', 'Only task owner or admin can delete the task');
    }

    public function select($id)
    {
        $task = Task::find($id);
        if ($task == null) {
            redirect('/tasks')->with('warning', "Task doesn't exist!");
        }
        if (auth()->user()->role->name == 'Student') {
            //Provjera ako je user vec selectao ovu temu
            if (count(TaskUsers::where('user_id', Auth::id())->where('task_id', $task->id)->get()) > 0) {
                $task_users = TaskUsers::where('task_id', $task->id)->orderBy('sort_id', 'asc')->get()->pluck('task_id', 'user_id');
                foreach ($task_users as $key => $user) {
                    if ($key == Auth::id()) {
                        //Izbrisi user select sa taska
                        unset($task_users[$key]);
                    }
                }
                $task->users()->sync(array_keys($task_users->toArray()));
                $task->save();

                //Ispravi poredak
                $new_task_users = TaskUsers::where('user_id', Auth::id())->get();
                $count = 1;
                foreach ($new_task_users as $task_user) {
                    $task_user->sort_id = $count;
                    $task_user->save();
                    $count++;
                }

                return redirect('/tasks')->with('success', 'Successfully deselected!');
            } else {
                $old_task_users = TaskUsers::where('task_id', $task->id)->get()->pluck('task_id', 'user_id');

                //Prije dodavanja usera na ovu temu provjera jel ispunio maksimalno 5 tema za rezervaciju
                $tasks_count = count(TaskUsers::where('user_id', Auth::id())->get());
                if ($tasks_count == 5) {
                    return redirect('/tasks')->with('warning', 'Maximum selected tasks reached: [5]');
                }
                $next_sort_id = $tasks_count + 1;

                //Novi task user
                $task_user = new TaskUsers();
                $task_user->user_id = Auth::id();
                $task_user->task_id = $task->id;
                $task_user->sort_id = $next_sort_id;
                $task_user->save();

                //Ako su vec neki useri odabrali ovu temu
                if (count($old_task_users) > 0) {
                    $old_task_users->put(auth()->user()->id, $task->id);
                    $task->users()->sync(array_keys($old_task_users->toArray()));
                } else {
                    //Ako nema usera da su selectali ovu temu                    
                    $task->users()->sync(array(Auth::id()));
                }
                $task->save();

                return redirect('/tasks')->with('success', 'Successfully selected!');
            }
        } else return redirect('/tasks')->with('warning', 'Only student can apply to this task!');
    }

    public function show($id)
    {
        $task = Task::find($id);
        if ($task == null) {
            return redirect('/tasks')->with('warning', 'Task not found!');
        }
        $task['study_type_name'] = $task->studyType->name;

        $students = $task->users;
        foreach($students as $key => $student){
            $task_user = TaskUsers::where('user_id', $student->id)->where('task_id', $task->id)->first();
            if($task_user->sort_id != 1){
                //Sakrij studente kojima ovaj rad nije prioritet 1
                unset($students[$key]);
            }
        }
        $students = $students->pluck('name', 'id');
        return view('tasks.choose', compact('task', 'students'));
    }

    public function choose(Request $request, $id)
    {
        $task = Task::find($id);
        if ($task == null || $task->is_reserved == 1) {
            return redirect('/tasks')->with('warning', "Task not found or it's already reserved!");
        }
        TaskUsers::where('user_id', $request->input('student_id'))->delete();
        //Spremi TaskUser podatak
        $task_user = new TaskUsers;
        $task_user->user_id = $request->input('student_id');
        $task_user->task_id = $id;
        $task_user->sort_id = 1;
        $task_user->save();
        //Syncaj novi podataka i povezi sa Task tablicom
        $task->users()->sync(array($request->input('student_id')));
        $task->is_reserved = 1;
        $task->save();

        return redirect('/tasks')->with('success', 'Successfully selected a thesis student!');
    }

    public function sort()
    {
        if (count(TaskUsers::where('user_id', Auth::id())->get()) > 1) {
            $task_users = TaskUsers::where('user_id', Auth::id())->orderBy('sort_id', 'asc')->get();
            $user = auth()->user();
            $tasks = array();

            foreach ($task_users as $task_user) {
                //Pronadi temu i postavi prioritet
                $task = Task::whereId($task_user->task_id)->first();
                $task['sort_id'] = $task_user->sort_id;
                $task['study_type'] = $task->studyType->name;
                $tasks[] = $task;
            }
            return view('tasks.sort', compact('tasks'));
        } else {
            return redirect('/tasks')->with('warning', 'You need to select at least 2 tasks to sort them!');
        }
    }
    public function updateSort(Request $request)
    {
        $task_users = TaskUsers::where('user_id', Auth::id())->get();

        foreach ($task_users as $task_user) {
            foreach ($request->task as $task) {
                if ($task['task_id'] == $task_user->task_id) {
                    $task_user->sort_id = $task['sort_id'];
                    $task_user->save();
                }
            }
        }
        return response('Update Successfully.', 200);
    }
}
