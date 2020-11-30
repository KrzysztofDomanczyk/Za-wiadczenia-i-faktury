<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade as PDF;
use App\Certificate;
use PDFMerger;

class PDFCertificatesController extends Controller
{
    private $certificatePath;
    private $coursPath;
    private $data;

    public function downloadPDFCertificate($id)
    {
        $this->createCertificatePDF($id);
        $this->createPathsFiles();
        $this->mergeAndDownloadPdf();
    }

    private function createCertificatePDF($id)
    {
        $certificate = Certificate::findOrFail($id);
        $data = $certificate->toArray();
        $cours = $certificate->course->toArray();
        $data['created_at'] = $certificate->created_at->toDateString();
        $data['courses_id'] = $cours;
        $this->data = $data;
        $pdf = PDF::loadView('pdf.certificate', compact('data'));
        $pdf->save('certificates/' . $certificate->id . '.pdf');
        $this->createPathsFiles();
    }

    private function createPathsFiles()
    {
        $this->certificatePath = public_path('certificates\\' .   $this->data['id'] . '.pdf');
        $this->coursPath = public_path('timeTableCours\\' .   $this->data['courses_id']['timeTableFile']);
    }

    private function mergeAndDownloadPdf()
    {
        $pdf = new PDFMerger();
        $pdf->addPDF($this->certificatePath);
        $pdf->addPDF($this->coursPath);
        $pdf->merge('download', $this->data['idCertificate'] . '.pdf'); 
        Storage::disk('public')->delete('certificates/' . $this->data['id'] . '.pdf');
    }
}
