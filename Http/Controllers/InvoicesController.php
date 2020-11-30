<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
Use App\Invoice;
Use App\Contractor;
Use App\Product;
Use App\Mylibs\InvoicesCreator;
Use App\Mylibs\Invoices;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoices::getFromCurrentMonth();
        $currentDate = Carbon::now();
        return view('pages.invoices.archive-invoices', ['invoices'=>$invoices, 'currentDate' => $currentDate]);
    }

    public function search(Request $request)
    {
        $invoices = Invoices::getSearchingInvoices($request->input('search'));
        $currentDate = Carbon::now();

        return view('pages.invoices.archive-invoices', ['invoices'=>$invoices, 'currentDate' => $currentDate]);
    }

    public function filtr(Request $request)
    {
        $invoices = Invoices::getByMonth($request->input('filtrMonth'));
        $currentDate = Carbon::now();

        return view('pages.invoices.archive-invoices', ['invoices'=>$invoices, 'currentDate' => $currentDate]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currentDate = Carbon::now();
        return view('pages.invoices.create-invoice', ['currentDate' => $currentDate]);
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
            'dateOfService' => 'required',
            'dateOfPayment' => 'required',
            'methodPayment' => 'required',
            'paid' => 'required',
            'streetShip' => 'required',
            'houseNumberShip' => 'required',
            'postNumberShip' => 'required', 
            'cityShip' => 'required', 
            'street' => 'required',
            'houseNumber' => 'required',
            'postNumber' => 'required', 
            'city' => 'required', 
            "companyName" => 'required', 
            "nip" => 'required', 
            "products" => 'required', 
        ]);
        $dataInvoice = [
            "dateOfIssue" => $request->input('dateOfIssue'),
            "dateOfService" => $request->input('dateOfService'),
            "dateOfPayment" => $request->input('dateOfPayment'),
            "methodPayment" => $request->input('methodPayment'),
            "paid" => $request->input('paid'), 
            "streetShip" => $request->input('streetShip'),
            "houseNumberShip" => $request->input('houseNumberShip'),
            "postNumberShip" => $request->input('postNumberShip'),
            "comments" => $request->input('comments'),
            "cityShip" => $request->input('cityShip'),
        ];
        $dataContractor = [
            "companyName" => $request->input('companyName'),
            "nip" => $request->input('nip'),
            "email" => $request->input('email'),
            "street" => $request->input('street'),
            "houseNumber" => $request->input('houseNumber'), 
            "postNumber" => $request->input('postNumber'),
            "city" => $request->input('city')
        ];
        $newInvoice = Invoices::createInvoice($dataInvoice, $dataContractor);
        $products = $request->input('products');
        
        foreach ($products as $product) {
            $newProduct = new Product;
            $newProduct->name = $product['name'];
            $newProduct->quantity = $product['quantity'];
            $newProduct->unit = $product['unit'];
            $newProduct->nettoPrice = $product['nettoPrice'];
            $newProduct->vat = $product['vat'];
            $newProduct->bruttoPrice = $product['bruttoPrice'];
            $newProduct->valueNetto = $product['valueNetto'];
            $newProduct->vatPrice = $product['vatPrice'];
            $newProduct->valueBrutto = $product['valueBrutto'];
            $newProduct->invoice_id = $newInvoice->id;
            $newProduct->save();
        }

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
        //
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
        //
    }

    public function liveSearch(Request $request)
    {    

      
        $term =  $request->get('term');

        $contractor = Contractor::where("companyName", "LIKE", "%$term%")
            ->orWhere("nip", "LIKE", "%$term%")
            ->get();

        foreach ($contractor as $query)
        {
            $results[] = [ 
            'id' => $query->id,
            'companyName' => $query->companyName, 
            'nip' => $query->nip,
            'email' => $query->email,
            'street' => $query->street,
            'houseNumber' => $query->houseNumber,
            'postNumber' => $query->postNumber,
            'city' => $query->city
        ];
        }
        return response()->json( $results);  
    }
}



































        //    $dataInvoice = [
        //     "dateOfIssue" => "2019-03-17",
        //     "dateOfService" => "2019-03-17",
        //     "dateOfPayment" => "2019-03-24",
        //     "methodPayment" => "Gotówka",
        //     "paid" => 1, 
        //     "streetShip" => "Kalinowa 11, 34",
        //     "houseNumberShip" => "Kalinowa 11",
        //     "postNumberShip" => "Wysyłka - Kod pocztowy",
        //     "cityShip" => "Wysyłka - miejscowość", 
        //    ];

        //    $dataContractor = [
        //     "companyName" => "Nazwa firmy",
        //     "nip" => "153",
        //     "email" => "kylo1997120@gmail.com",
        //     "street" => "Kalinowa 11, 34",
        //     "houseNumber" => "Kalinowa 11",
        //     "postNumber" => "21-040",
        //     "city" => "Świdnik",
        //     ];

           

            // dd($dataContractor);

        

        // $dataInvoice = [
        //     "dateOfIssue" => $request->input('dateOfIssue'),
        //     "dateOfService" => $request->input('dateOfService'),
        //     "dateOfPayment" => $request->input('dateOfPayment'),
        //     "methodPayment" => $request->input('methodPayment'),
        //     "paid" => $request->input('paid'), 
        //     "streetShip" => $request->input('streetShip'),
        //     "houseNumberShip" => $request->input('houseNumberShip'),
        //     "postNumberShip" => $request->input('postNumberShip'),
        //     "cityShip" => $request->input('cityShip'),
        // ];
       
        // $dataContractor = [
        //     "companyName" => $request->input('companyName'),
        //     "nip" => $request->input('nip'),
        //     "email" => $request->input('email'),
        //     "street" => $request->input('street'),
        //     "houseNumber" => $request->input('houseNumber'), 
        //     "postNumber" => $request->input('postNumber'),
        //     "city" => $request->input('city')
        // ];

        

        // $data = [
        //     "dateOfIssue" => "2019-03-17",
        //     "dateOfService" => "2019-03-17",
        //     "dateOfPayment" => "2019-03-24",
        //     "methodPayment" => "Gotówka",
        //     "paid" => "Tak", 
        //     "contractor_id" => 1, 

        //     "companyName" => "Nazwa firmy",
        //     "nip" => "1234567891",
        //     "email" => "kylo1997120@gmail.com",
        //     "street" => "Kalinowa 11, 34",
        //     "houseNumber" => "Kalinowa 11",
        //     "postNumber" => "21-040",
        //     "city" => "Świdnik",
        //     "streetShip" => "Kalinowa 11, 34",
        //     "houseNumberShip" => "Kalinowa 11",
        //     "postNumberShip" => "Wysyłka - Kod pocztowy",
        //     "cityShip" => "Wysyłka - miejscowość",
           
        // ];