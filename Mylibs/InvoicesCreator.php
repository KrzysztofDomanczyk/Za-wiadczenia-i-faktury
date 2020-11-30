<?php

namespace App\Mylibs;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use App\Invoice;
use App\Settings;
use App\Contractor;

class InvoicesCreator
{
    private $requestInvoice;
    private $requestContractor;
    private $contractor;
    private $settings;
    private $now;
    public $newInvoice;
    
    function __construct($dataInvoice, $dataContractor)
    {
        $this->requestInvoice = $dataInvoice;
        $this->requestContractor = $dataContractor;
        $this->settings = Settings::where('key', 'invoices')->first();
        $this->now = Carbon::now();
        $this->createInvoice();
        
        return $this->newInvoice;
    }

    public function getNewInvoice()
    {
        return $this->newInvoice;
    }

    private function createInvoice()
    {
        $this->assignContractor();
        $this->storeInvoiceInDb();
    }

    private function assignContractor()
    {
        
        $foundContractor = Contractor::where('nip', '=', $this->requestContractor['nip'])->get()->first();
        $this->contractor = $foundContractor != null ? $foundContractor : $this->createNewContractor();
        // dd($this->contractor);
    }

    private function createNewContractor() 
    {
        $newContractor = new Contractor;
        $newContractor->companyName = $this->requestContractor['companyName'];
        $newContractor->nip = $this->requestContractor['nip'];
        $newContractor->email = $this->requestContractor['email'];
        $newContractor->street = $this->requestContractor['street'];
        $newContractor->houseNumber = $this->requestContractor['houseNumber'];
        $newContractor->postNumber = $this->requestContractor['postNumber'];
        $newContractor->city = $this->requestContractor['city'];
        $newContractor->save();
        return $newContractor;
    }

    private function storeInvoiceInDb()
    {
        $newInvoice = new Invoice;
        $newInvoice->created_at = Carbon::parse($this->requestInvoice['dateOfIssue']);
        $newInvoice->idInvoice = $this->assignIdCertificate();
        $newInvoice->dateOfService = Carbon::parse($this->requestInvoice['dateOfService']);
        $newInvoice->dateOfPayment = Carbon::parse($this->requestInvoice['dateOfPayment']);
        $newInvoice->methodPayment = $this->requestInvoice['methodPayment'];
        $newInvoice->paid = $this->requestInvoice['paid'];
        $newInvoice->streetShip = $this->requestInvoice['streetShip'];
        $newInvoice->houseNumberShip = $this->requestInvoice['houseNumberShip'];
        $newInvoice->postNumberShip = $this->requestInvoice['postNumberShip'];
        $newInvoice->cityShip = $this->requestInvoice['cityShip'];
        $newInvoice->comments = $this->requestInvoice['comments'];
        $newInvoice->contractor_id = $this->contractor->id;
        $newInvoice->save();
        $this->newInvoice = $newInvoice;
    }


    private function assignIdCertificate()
    {
        $counter = $this->doResetYear() ? 0 : $this->settings->counter + 1 ;
        $idInvoice = $this->createIdInvoice($counter);
        $this->updateSettings($counter);
        return $idInvoice;
    }
   

    private function doResetYear()
    {   
        if ($this->settings->currentYear != $this->now->year) {
            return true;
        } else {
            return false;
        }
    }

    private function createIdInvoice($counter) 
    {
        $formatted_number = sprintf("%04d", $counter);
        return $id = "FF/" . $formatted_number . "/" . $this->now->year;
    }
    
    private function updateSettings($counter)
    {
        $this->settings->update(['counter' => $counter ]);;
        $this->settings->currentYear = $this->now->year;
        $this->settings->save();
    }

}
