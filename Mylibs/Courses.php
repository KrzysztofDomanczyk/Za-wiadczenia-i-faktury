<?php

namespace App\Mylibs;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;


use App\Certificate;
use App\Settings;
use Carbon\Carbon;
use App\Cours;

class Courses
{
    protected $request;
    protected $fileName;

    function __construct($request)
    {
        $this->request = $request;
    }

    public function saveCours()
    {
        $this->storageFile();  
        $this->saveToDb();
    }

    public function storageFile()
    {
        if ($this->request->file('file') != null)
        {
            $this->fileName = $this->createNameFile($this->request->input('title'));
            $path = $this->request->file->storeAs('/timeTableCours', $this->fileName, 'public');
        } 
    }
    
    private function createNameFile($str)
    {
        $unwanted_array = ['ś'=>'s', 'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ź' => 'z', 'ż' => 'z',
            'Ś'=>'s', 'Ą' => 'a', 'Ć' => 'c', 'Ę' => 'e', 'Ł' => 'l', 'Ń' => 'n', 'Ó' => 'o', 'Ź' => 'z', 'Ż' => 'z']; // Polish letters for example

        $str = strtr( $str, $unwanted_array );
   
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $str)) . '.pdf';

        return $slug;    
    }

    public function saveToDb()
    {
        $cours = new Cours;
        $cours->title = $this->request->input('title');
        $cours->longTitle = $this->request->input('longTitle');
        $cours->legalBasis = $this->request->input('legalBasis');
        $cours->slug = $this->request->input('slug');
        $cours->timeTableFile = $this->fileName;
        $cours->save();
    }
    public function updateInDb()
    {



        $cours = Cours::findOrFail($this->request->id);
        $cours->title = $this->request->input('title');
        $cours->slug = $this->request->input('slug');
        if ($this->request->file('file') != null)
        {
            $cours->timeTableFile = $this->fileName;
        }
        $cours->save();
    }

}
