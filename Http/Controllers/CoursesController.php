<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use App\Cours;
use App\Mylibs\Courses;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Cours::all()->where('active', '==', true);
        return view('pages.courses.show-courses', ['courses' => $courses]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.courses.create-cours');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'slug' => 'required|string|min:2|max:2',
            'file' => 'required',
        ]);
        $cours = new Courses($request);
        $cours->saveCours();
        return redirect(route('courses'))->with('success', 'Nowe szkolenie zostało dodane.');   
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cours = Cours::findOrFail($id);
        return view('pages.courses.edit-course', ['cours' => $cours]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $cours = new Courses($request);
        $cours->storageFile();
        $cours->updateInDb();
        return redirect(route('courses'))->with('success', 'Szkolenie zostało zaktualizowane.');   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cours = Cours::findOrFail($id);
        $cours->active = false;
        $cours->save();
        return redirect(route('courses'))->with('success', 'Szkolenie zostało usunięte. :(');   

    }

    public function showTimeTable($id)
    {
        $cours = Cours::findOrFail($id);
        $file = public_path('timeTableCours\\' .  $cours->timeTableFile);
        $headers = array('Content-Type: application/pdf');
        return Response::download($file, $cours->timeTableFile . '.pdf', $headers);
        // return redirect()->back()->download(public_path('timeTableCours\\' .  $cours->timeTableFile));
        
    }
}
