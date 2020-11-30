<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use PDF;
use App\Invoice;
Use App\Mylibs\ConverterValues;

class PDFInvoicesController extends Controller
{
    private $id;
    private $pdf;
    private $pdfName;
    private $sumValueNetto;
    private $sumPriceVat;
    private $sumValueBrutto;
    private $bruttoToWords;

    public function downloadPDFInvoice($id)
    {
        $this->id = $id;
        $this->createInvoicePdf();
        return $this->pdf->download($this->pdfName);
    }

    private function createInvoicePdf()
    {
        $invoice = Invoice::findOrFail($this->id);
        $this->pdfName = $invoice->idInvoice . '.pdf';
        $this->additionProductsValues($invoice);
        $this->bruttoToWords = ConverterValues::getValuesToWords($this->sumValueBrutto);

        $pdf = PDF::loadView('pdf.invoice', [
            'invoice' => $invoice, 
            'contractor' => $invoice->contractor, 
            'products' => $invoice->products, 
            'sumValueNetto'=>$this->sumValueNetto,
            'sumPriceVat'=>$this->sumPriceVat,
            'sumValueBrutto'=>$this->sumValueBrutto,
            'bruttoToWords'=>$this->bruttoToWords,
        ]);
        
        $this->pdf = $pdf;
    }

    private function additionProductsValues($invoice)
    {
        foreach ($invoice->products as $product ) {
            $this->sumValueNetto += $product->valueNetto;
            $this->sumPriceVat += $product->vatPrice;
            $this->sumValueBrutto += $product->valueBrutto;
        }
        
    }
}
