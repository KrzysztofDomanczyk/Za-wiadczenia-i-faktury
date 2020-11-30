<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Certificate;
use App\Cours;
use Carbon\Carbon;
use App\Mylibs\Certificates;

class CertificatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $certificates = Certificates::getFromCurrentMonth();
        $currentDate = Carbon::now();
        return view('pages.certificates.archive-certificates', ['certificates'=>$certificates, 'currentDate' => $currentDate]);
    }

    public function search(Request $request)
    {
        $certificates = Certificates::getSearchingcertificates($request->input('search'));
        $currentDate = Carbon::now();

        return view('pages.certificates.archive-certificates', ['certificates'=>$certificates, 'currentDate' => $currentDate]);
    }

    public function filtr(Request $request)
    {
        $certificates = Certificates::getByMonth($request->input('filtrMonth'));
        $currentDate = Carbon::now();

        return view('pages.certificates.archive-certificates', ['certificates'=>$certificates, 'currentDate' => $currentDate]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $courses = Cours::all()->where('active', "==", true);
        $currentDate = Carbon::parse(now())->format('Y-m-d');
        return view('pages.certificates.create-certificate', ['currentDate' => $currentDate, 'courses' => $courses]);
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
            'name' => 'required',
            'lastName' => 'required',
            'dateBirthday' => 'required',
            'dateOfIssue' => 'required',
            'dateOfEnd' => 'required',
            'dateOfStart' => 'required',
            'courses_id' => 'required', 
        ]);
        
        $id = new Certificates();
        $certificate = new Certificate;
        $certificate->name = $request->input('name');
        $certificate->created_at = Carbon::parse($request->input('dateOfIssue'));
        $certificate->lastName = $request->input('lastName');
        $certificate->idCertificate = $id->assignIdCertificate($request->input('courses_id'));
        $certificate->dateBirthday = Carbon::parse($request->input('dateBirthday'));
        $certificate->placeOfBirth = $request->input('placeOfBirth');
        $certificate->email = $request->input('email');
        $certificate->dateOfStart = Carbon::parse($request->input('dateOfStart'));
        $certificate->dateOfEnd = Carbon::parse($request->input('dateOfEnd'));
        $certificate->courses_id = $request->input('courses_id');
        $certificate->save();  

        return redirect(route('showArchive-certificates'))->with('success', 'Zaświadczenie zostało utworzone');   
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $courses = Cours::all();
        $certificate = Certificate::findOrFail($id);
        
        return view('pages.certificates.edit-certificate', ['courses' => $courses, 'certificate' => $certificate]);
    }

 
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'lastName' => 'required',
            'dateBirthday' => 'required',
            'dateOfIssue' => 'required',
            'dateOfEnd' => 'required',
            'dateOfStart' => 'required',
            'courses_id' => 'required', 
        ]);
        
        $id = new Certificates();

        $certificate = Certificate::find($request->input('id'));
        $certificate->name = $request->input('name');
        $certificate->created_at = Carbon::parse($request->input('dateOfIssue'));
        $certificate->lastName = $request->input('lastName');
        $certificate->idCertificate = $id->getIdCertificate($request->input('courses_id'));
        $certificate->dateBirthday = Carbon::parse($request->input('dateBirthday'));
        $certificate->placeOfBirth = $request->input('placeOfBirth');
        if ($request->input('email') != null) {
            $certificate->email = $request->input('email');
        }
        $certificate->dateOfStart = Carbon::parse($request->input('dateOfStart'));
        $certificate->dateOfEnd = Carbon::parse($request->input('dateOfEnd'));
        $certificate->courses_id = $request->input('courses_id');
        $certificate->save();

        return redirect(route('showArchive-certificates'))->with('success', 'Zaświadczenie zostało zmodyfikowane');   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    
}
