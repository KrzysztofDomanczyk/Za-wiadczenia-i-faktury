<?php

namespace App\Mylibs;

use App\Invoice;
use App\Contractor;
use App\Settings;
use Carbon\Carbon;
use App\Cours;

class Invoices
{
    private $createdInvoice;

    public static function getFromCurrentMonth()
    {
        $invoices = Invoice::whereMonth('created_at', '=', Carbon::now()->month)->orderBy('updated_at', 'DESC')->paginate(50);
        return  $invoices;
    }

    public static function getSearchingInvoices($search)
    {    
        $invoices = Invoice::whereHas('contractor', function ($query) use ($search) {
            $query->where('idInvoice', 'like', "%$search%")
                ->orWhere('companyName', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('created_at', 'like', "%$search%")
                ->orWhere('nip', 'like', "%$search%");
        })->paginate(50);
        return $invoices;
    }

    public static function getByMonth($filtr)
    {
        $invoices = Invoice::whereMonth('created_at', '=', $filtr)->orderBy('updated_at', 'DESC')->paginate(50);
        return $invoices;
    }
    public static function createInvoice($dataInvoice, $dataContractor)
    {
        $invoice = new InvoicesCreator($dataInvoice, $dataContractor);
        return $invoice->newInvoice;
    }
}
