<?php

namespace App\Mylibs;

use App\Certificate;
use App\Settings;
use Carbon\Carbon;
use App\Cours;

class Certificates
{

    private $now;
    
    function __construct()
    {
        $this->now = Carbon::now();
    }
   
    public static function getFromCurrentMonth()
    {
            $certificates = Certificate::whereMonth('created_at', '=', Carbon::now()->month)->orderBy('updated_at', 'DESC')->paginate(50);
            return  $certificates;
    }

    public static function getSearchingcertificates($search)
    {    
        $certificates = Certificate::where('idCertificate', 'like', "%$search%")
            ->orWhere('name', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->orWhere('created_at', 'like', "%$search%")->paginate(50);

        return $certificates;
    }

    public static function getByMonth($filtr)
    {
        $certificates = Certificate::whereMonth('created_at', '=', $filtr)->orderBy('updated_at', 'DESC')->paginate(50);
        return $certificates;
    }

    public function assignIdCertificate($courses_id)
    {
        $coursSlug = Cours::find($courses_id)->slug;
        $settings = Settings::find(1);
        $counter = $this->doesYearReset($settings->currentYear) ? 0 : $settings->counter + 1 ;
        $idCertificate = $this->createIdCertificate($counter, $coursSlug);
        $settings->update(['counter' => $counter ]);;
        $settings->save();   
        return $idCertificate;
    }
   

    private function doesYearReset($settingsYear)
    {   
        if ($settingsYear != $this->now->year) {
            $settings = Settings::find(1);
            $settings->currentYear = $this->now->year;
            $settings->save();
            return true;
        } else {
            return false;
        }
    }

    private function createIdCertificate($counter, $coursSlug) 
    {
        $formatted_number = sprintf("%04d", $counter);
        return $id = $coursSlug . "/" . $formatted_number . "/" . $this->now->year;
    }
}
