<?php

namespace App\Http\Controllers;

use App\Postcode;
use App\Record;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Auth;
class RaportsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth',['except' => ['getRaportNewBaseWeek', 'getRaportNewBaseMonth','getRaportDayAPI','getRaportCityInfoAPI']]);
    }
// wystawienie danych nowych zgód tygodniowy
    public function getRaportNewBaseWeek()
    {
        $date_start = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-7,date("Y")));
        $date_stop = date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
        $wynik = DB::table('old_new_base')
            ->selectRaw('old_base,count(DISTINCT(id_record)) as count_record')
            ->where('add_date','>=',$date_start.' 00:00:00')
            ->where('add_date','<=',$date_stop.' 23:00:00')
            ->groupBy('old_base')
            ->get();
        return json_encode($wynik);
    }
// wystawienie danych nowych zgód miesięczny
    public function getRaportNewBaseMonth()
    {
        $date_start = date("Y-n-d", strtotime("first day of previous month"));
        $date_stop = date("Y-n-d", strtotime("last day of previous month"));
        $wynik = DB::table('old_new_base')
            ->selectRaw('old_base,count(DISTINCT(id_record)) as count_record')
            ->where('add_date','>=',$date_start.' 00:00:00')
            ->where('add_date','<=',$date_stop.' 23:00:00')
            ->groupBy('old_base')
            ->get();
        return json_encode($wynik);
    }
    // wystawienie raportu dziennego pobranej bazy

    public function getRaportDayAPI($id)
    {
//        $id == 1 dzienny,$id == 2 tygodniowy,$id == 3 miesieczny
        $tablica = array();
        if($id == 1)
        {
            $datajeden = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
            array_push($tablica,$datajeden);
            $this->setSingleRaport($tablica,1);
        }else if($id == 2)
        {
            $dataod = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-7,date("Y")));
            $datado = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
            array_push($tablica,$dataod);
            array_push($tablica,$datado);
            $this->setSingleRaport($tablica,2);
        }else if( $id == 3)
        {
            $dataod =  date("Y-n-d", strtotime("first day of previous month"));
            $datado =  date("Y-n-d", strtotime("last day of previous month"));
            array_push($tablica,$dataod);
            array_push($tablica,$datado);
            $this->setSingleRaport($tablica,2);
        }

        $data['overall_result'] = session()->get('resandship');
        $data['departments_statistic'] = session()->get('departamentres');
        $data['employee_statistic'] = session()->get('employeeres');
        $data['departamentship'] = session()->get('departamentship');
        $data['employeeship'] = session()->get('employeeship');

        return json_encode($data,JSON_UNESCAPED_UNICODE);

    }
    // Wystawienie ilości zgód na dane miasto
    public function getRaportCityInfoAPI($cityName = null){
        if($cityName != null){

            $city = mb_strtolower($cityName);
            $districtArray = ['warszawa','bydgoszcz','gdańsk','kraków','lublin','poznań','szczecin','wrocław','lódź'];
            if(in_array($city,$districtArray)){
                $city = $city.'%';
            }

            $infoAboutCity = Postcode::where('miasto','like',$city)->get();
            $data['zgody'] = 0;
            foreach ($infoAboutCity as $item){
                $data['zgody'] += $item->zgody;
                $data['zgody'] += $item->zgodyFromZgody;
            }
            return json_encode($data,JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode([],JSON_UNESCAPED_UNICODE);
        }
    }

    public function getRaport()
    {
        return view('raports.raport');
    }

    public function getRaportUser()
    {
        return view('raports.raportuser');
    }

    public function getRaportPlus()
    {
        return view('raports.raportplus');
    }

    public function getRaportUserPlus()
    {
        return view('raports.raportuserplus');
    }

    public function setRaportuserPlus(Request $request)
    {
        $datajeden = $request->input('datejeden');
        $dataod = $request->input('dateod');
        $datado = $request->input('datedo');
        $tablica = array();
        if($datajeden != '')
        {
            array_push($tablica,$datajeden);
            $this->setSingleRaportuserPlus($tablica,1);
        }else if($datajeden =='' && ($dataod !='' && $datado !=''))
        {
            array_push($tablica,$dataod);
            array_push($tablica,$datado);
            $this->setSingleRaportuserPlus($tablica,2);
        }else
        {
            return view('raports.raportuserplus');
        }
        return view('raports.raportuserplusPOST')
            ->with('dataraportu',$tablica)
            ->with('dane',session()->get('resandship'))
            ->with('oddzialy', session()->get('departamentres'))
            ->with('zapytanie',session()->get('departamentship'))
            ->with('employeeres',session()->get('employeeres'))
            ->with('cityres',session()->get('cityres'))
            ->with('cityship',session()->get('cityship'))
            ->with('employeeship',session()->get('employeeship'));

    }


    public function setRaportUser(Request $request)
    {
        $datajeden = $request->input('datejeden');
        $dataod = $request->input('dateod');
        $datado = $request->input('datedo');
        $tablica = array();
        if($datajeden != '')
        {
            array_push($tablica,$datajeden);
            $this->setSingleRaportUser($tablica,1);
        }else if($datajeden =='' && ($dataod !='' && $datado !=''))
        {
            if($dataod > $datado)
            {
                return view('raports.raportuser');
            }
            array_push($tablica,$dataod);
            array_push($tablica,$datado);
            $this->setSingleRaportUser($tablica,2);
        }else
        {
            return view('raports.raportuser');
        }
        return view('raports.raportuserPOST')
            ->with('dataraportu',$tablica)
            ->with('dane',session()->get('resandship'))
            ->with('oddzialy', session()->get('departamentres'))
            ->with('oddzialyWys',session()->get('departamentship'))
            ->with('employeeres',session()->get('employeeres'))
            ->with('employeeship',session()->get('employeeship'));

    }
    public function setRaport(Request $request)
    {
        $datajeden = $request->input('datejeden');
        $dataod = $request->input('dateod');
        $datado = $request->input('datedo');
        $tablica = array();
        if($datajeden != '')
        {
            array_push($tablica,$datajeden);
            $this->setSingleRaport($tablica,1);
        }else if($datajeden =='' && ($dataod !='' && $datado !=''))
        {
            if($dataod > $datado)
            {
                return view('raports.raport');
            }
            array_push($tablica,$dataod);
            array_push($tablica,$datado);
            $this->setSingleRaport($tablica,2);
        }else
        {
            return view('raports.raport');
        }
        return view('raports.raportPOST')
            ->with('dataraportu',$tablica)
            ->with('dane',session()->get('resandship'))
            ->with('oddzialy', session()->get('departamentres'))
            ->with('oddzialyWys',session()->get('departamentship'))
            ->with('employeeres',session()->get('employeeres'))
            ->with('employeeship',session()->get('employeeship'));

    }


    public function setRaportPlus(Request $request)
    {
        $datajeden = $request->input('datejeden');
        $dataod = $request->input('dateod');
        $datado = $request->input('datedo');
        $tablica = array();
        if($datajeden != '')
        {
            array_push($tablica,$datajeden);
            $this->setSingleRaportPlus($tablica,1);
        }else if($datajeden =='' && ($dataod !='' && $datado !=''))
        {
            array_push($tablica,$dataod);
            array_push($tablica,$datado);
            $this->setSingleRaportPlus($tablica,2);
        }else
        {
            return view('raports.raportplus');
        }
        return view('raports.raportplusPOST')
            ->with('dataraportu',$tablica)
            ->with('dane',session()->get('resandship'))
            ->with('oddzialy', session()->get('departamentres'))
            ->with('zapytanie',session()->get('departamentship'))
            ->with('employeeres',session()->get('employeeres'))
            ->with('cityres',session()->get('cityres'))
            ->with('cityship',session()->get('cityship'))
            ->with('employeeship',session()->get('employeeship'));

    }




//RAPORT DLA POJEDYNCZEJ OSOBY
    function setSingleRaportUser($date,$typ)
    {
///////////////////////////////////////BADANIA Wysylka/////////////////////////////////////////////
        $user = Auth::user();
        $id = $user->id;

        $resandship = DB::table('log_download')
            ->selectRaw('
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma,
                     baza')
            ->where('id_user', '>=', 1)
            ->where('id_user', '=', $id);
        if($typ == 1) {
            $resandship = $resandship
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('baza')
                ->get();
        }else {
            $resandship = $resandship
                ->whereBetween('date', [$date[0].'%',$date[1].' 23:59:59'])
                ->groupBy('baza')
                ->get();
        }

///////////////////////////////////////BADANIA Oddziały/////////////////////////////////////////////


         $departamentres = DB::table('log_download')
             ->selectRaw(
                 'departments_t.name,
                    departments_t.id,
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
             ->join('users_t', 'id_user', 'users_t.id')
             ->join('departments_t', 'users_t.dep_id', 'departments_t.id')
             ->where('baza', 'like', 'Badania')
             ->where('id_user', '=', $id);
        if($typ == 1) {
            $departamentres = $departamentres
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }else
        {
            $departamentres = $departamentres
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }

///////////////////////////////////////WYSYLKA Oddziały/////////////////////////////////////////////


           $departamentship = DB::table('log_download')
               ->selectRaw(
                   'departments_t.name,
                    departments_t.id,
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
               ->join('users_t', 'id_user', 'users_t.id')
               ->join('departments_t', 'users_t.dep_id', 'departments_t.id')
               ->where('baza', 'like', 'Wysylka')
               ->where('id_user', '=', $id);
        if($typ == 1) {
            $departamentship = $departamentship
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }else
        {
            $departamentship = $departamentship
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }

///////////////////////////////////////Badania Pracownicy/////////////////////////////////////////////
        $employeeres = DB::table('log_download')
            ->selectRaw('
                users_t.name,
                users_t.last, 
                users_t.dep_id, 
                users_t.id as user_id,
                sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->where('baza', 'like', 'Badania')
            ->where('users_t.id','=',$id);

        if($typ == 1) {
            $employeeres =  $employeeres
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }
        else
        {
            $employeeres =  $employeeres
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }

///////////////////////////////////////Wysylka Pracownicy/////////////////////////////////////////////



        $employeeship = DB::table('log_download')
            ->selectRaw('
                users_t.name,
                users_t.last, 
                users_t.dep_id, 
                users_t.id as user_id, 
                sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->where('baza', 'like', 'Wysylka')
            ->where('id_user', '=', $id);

        if($typ == 1) {
            $employeeship =  $employeeship
                ->where('date', 'like', $date[0] . '%')
                ->wherenotin('users_t.id',[1,105,127, 132, 134])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }
        else
        {
            $employeeship =  $employeeship
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->wherenotin('users_t.id',[1,105,127, 132, 134])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }


        session()->put('resandship',$resandship);
        session()->put('departamentres',$departamentres);
        session()->put('departamentship',$departamentship);
        session()->put('employeeres',$employeeres);
        session()->put('employeeship',$employeeship);

    }


    function setSingleRaport($date,$typ)
    {
///////////////////////////////////////BADANIA Wysylka/////////////////////////////////////////////
        $resandship = DB::table('log_download')
            ->selectRaw('
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma,
                     baza')
                ->where('id_user', '>=', 1)
                ->where('status', '=', 0);
        if($typ == 1) {
            $resandship = $resandship
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('baza')
                ->get();
        }else {
            $resandship = $resandship
                    ->whereBetween('date', [$date[0].'%',$date[1].' 23:59:59'])
                    ->groupBy('baza')
                    ->get();
        }


///////////////////////////////////////BADANIA Oddziały/////////////////////////////////////////////

        $departamentres = DB::table('log_download')
            ->selectRaw(
                'departments_t.name,
                    departments_t.id,
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->join('departments_t', 'users_t.dep_id', 'departments_t.id')
            ->where('baza', 'like', 'Badania')
            ->where('status', '=', 0);
        if($typ == 1) {
            $departamentres = $departamentres
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }else
        {
            $departamentres = $departamentres
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }

///////////////////////////////////////WYSYLKA Oddziały/////////////////////////////////////////////





            $departamentship = DB::table('log_download')
                ->selectRaw(
                    'departments_t.name,
                    departments_t.id,
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
                ->join('users_t', 'id_user', 'users_t.id')
                ->join('departments_t', 'users_t.dep_id', 'departments_t.id')
                ->where('baza', 'like', 'Wysylka')
                ->where('status', '=', 0);
        if($typ == 1) {
            $departamentship = $departamentship
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }else
        {
            $departamentship = $departamentship
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }

///////////////////////////////////////Badania Pracownicy/////////////////////////////////////////////
        $employeeres = DB::table('log_download')
            ->selectRaw('
                users_t.name,
                users_t.last, 
                users_t.dep_id, 
                users_t.id as user_id,
                sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->where('baza', 'like', 'Badania')
            ->where('status', '=', 0);

        if($typ == 1) {
            $employeeres =  $employeeres
                ->where('date', 'like', $date[0] . '%')
                ->wherenotin('users_t.id',[1,105,127, 132, 134])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }
        else
        {
            $employeeres =  $employeeres
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->wherenotin('users_t.id',[1,105,127, 132, 134])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }
///////////////////////////////////////Wysylka Pracownicy/////////////////////////////////////////////

            $employeeship = DB::table('log_download')
            ->selectRaw('
                users_t.name,
                users_t.last, 
                users_t.dep_id, 
                users_t.id as user_id, 
                sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->where('baza', 'like', 'Wysylka');

        if($typ == 1) {
            $employeeship =  $employeeship
                ->where('date', 'like', $date[0] . '%')
                ->wherenotin('users_t.id',[1,105,127, 132, 134])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }
        else
        {
            $employeeship =  $employeeship
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->wherenotin('users_t.id',[1,105,127, 132, 134])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }

        session()->put('resandship',$resandship);
        session()->put('departamentres',$departamentres);
        session()->put('departamentship',$departamentship);
        session()->put('employeeres',$employeeres);
        session()->put('employeeship',$employeeship);
    }



    function setSingleRaportPlus($date,$typ)
    {
///////////////////////////////////////BADANIA Wysylka/////////////////////////////////////////////
        $resandship = DB::table('log_download')
            ->selectRaw('
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma,
                     baza')
            ->where('id_user', '>=', 1)
            ->where('status', '=', 0);
        if($typ == 1) {
            $resandship = $resandship
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('baza')
                ->get();
        }else {
            $resandship = $resandship
                ->whereBetween('date', [$date[0].'%',$date[1].' 23:59:59'])
                ->groupBy('baza')
                ->get();
        }

///////////////////////////////////////BADANIA Oddziały/////////////////////////////////////////////
        $departamentres = DB::table('log_download')
            ->selectRaw(
                'departments_t.name,
                    departments_t.id,
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->join('departments_t', 'users_t.dep_id', 'departments_t.id')
            ->where('baza', 'like', 'Badania')
            ->where('status', '=', 0);
        if($typ == 1) {
            $departamentres = $departamentres
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }else
        {
            $departamentres = $departamentres
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }

///////////////////////////////////////WYSYLKA Oddziały/////////////////////////////////////////////
        $departamentship = DB::table('log_download')
            ->selectRaw(
                'departments_t.name,
                    departments_t.id,
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->join('departments_t', 'users_t.dep_id', 'departments_t.id')
            ->where('baza', 'like', 'Wysylka')
            ->where('status', '=', 0);
        if($typ == 1) {
            $departamentship = $departamentship
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }else
        {
            $departamentship = $departamentship
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }

///////////////////////////////////////Badania Pracownicy/////////////////////////////////////////////
        $employeeres = DB::table('log_download')
            ->selectRaw('
                users_t.name,
                users_t.last, 
                users_t.dep_id, 
                users_t.id as user_id,
                sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->where('baza', 'like', 'Badania')
            ->where('status', '=', 0);

        if($typ == 1) {
            $employeeres =  $employeeres
                ->where('date', 'like', $date[0] . '%')
                ->wherenotin('users_t.id',[1,105,127])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }
        else
        {
            $employeeres =  $employeeres
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->wherenotin('users_t.id',[1,105,127])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }

///////////////////////////////////////Wysylka Pracownicy/////////////////////////////////////////////
        $employeeship = DB::table('log_download')
            ->selectRaw('
                users_t.name,
                users_t.last, 
                users_t.dep_id,
                users_t.id as user_id, 
                sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->where('baza', 'like', 'Wysylka')
            ->where('status', '=', 0);

        if($typ == 1) {
            $employeeship =  $employeeship
                ->where('date', 'like', $date[0] . '%')
                ->wherenotin('users_t.id',[1,105,127])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }
        else
        {
            $employeeship =  $employeeship
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->wherenotin('users_t.id',[1,105,127])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }



        $cityres = DB::table('log_download')
            ->selectRaw('
                woj.woj,
                miasto,
                id_user,sum(baza8) as bisnode,
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
                ->join('woj', 'log_download.idwoj', 'woj.idwoj')
                ->where('baza', 'Badania')
                ->where('status', '=', 0);
        if($typ == 1) {
            $cityres = $cityres
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('miasto', 'id_user', 'woj.woj')
                ->get();

        }else {
            $cityres = $cityres
                -> whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->groupBy('miasto', 'id_user', 'woj.woj')
                ->get();
        }

        $cityship = DB::table('log_download')
                ->selectRaw('
                            woj.woj,
                            miasto,
                            id_user,sum(baza8) as bisnode,
                                sum(baza8) as bisnode,
                                sum(bazazg) as zgody,
                                sum(bazareszta) as reszta,
                                sum(bazaevent) as event,
                                sum(bazaexito) as exito,
                                
                                sum(baza8Zgody) as bisnodeZgody,
                                sum(bazazgZgody) as zgodyZgody,
                                sum(bazaresztaZgody) as resztaZgody,
                                sum(bazaeventZgody) as eventZgody,
                                sum(bazaexitoZgody) as exitoZgody,
                                                    
                                sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                                 + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
                ->join('woj', 'log_download.idwoj', 'woj.idwoj')
                ->where('baza', 'Badania')
                ->where('status', '=', 0);
                    if($typ == 1) {
                        $cityship = $cityship
                            ->where('date', 'like', $date[0] . '%')
                            ->groupBy('miasto', 'id_user', 'woj.woj')
                            ->get();

                    }else {
                        $cityship = $cityship
                            -> whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                            ->groupBy('miasto', 'id_user', 'woj.woj')
                            ->get();
                    }

        session()->put('resandship', $resandship);
        session()->put('departamentres', $departamentres);
        session()->put('departamentship', $departamentship);
        session()->put('employeeres', $employeeres);
        session()->put('employeeship', $employeeship);
        session()->put('cityres', $cityres);
        session()->put('cityship', $cityship);

    }


    function setSingleRaportuserPlus($date,$typ)
    {
        $user = Auth::user();
        $id = $user->id;
///////////////////////////////////////BADANIA Wysylka/////////////////////////////////////////////


        $resandship = DB::table('log_download')
            ->selectRaw('
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma,
                     baza')
            ->where('id_user', '=', $id)
            ->where('status', '=', 0);
        if($typ == 1) {
            $resandship = $resandship
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('baza')
                ->get();
        }else {
            $resandship = $resandship
                ->whereBetween('date', [$date[0].'%',$date[1].' 23:59:59'])
                ->groupBy('baza')
                ->get();
        }

///////////////////////////////////////BADANIA Oddziały/////////////////////////////////////////////
        $departamentres = DB::table('log_download')
            ->selectRaw(
                'departments_t.name,
                    departments_t.id,
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->join('departments_t', 'users_t.dep_id', 'departments_t.id')
            ->where('baza', 'like', 'Badania')
            ->where('id_user', '=', $id)
            ->where('status', '=', 0);
        if($typ == 1) {
            $departamentres = $departamentres
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }else
        {
            $departamentres = $departamentres
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }

///////////////////////////////////////WYSYLKA Oddziały/////////////////////////////////////////////
        $departamentship = DB::table('log_download')
            ->selectRaw(
                'departments_t.name,
                    departments_t.id,
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->join('departments_t', 'users_t.dep_id', 'departments_t.id')
            ->where('baza', 'like', 'Wysylka')
            ->where('id_user', '=', $id)
            ->where('status', '=', 0);
        if($typ == 1) {
            $departamentship = $departamentship
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }else
        {
            $departamentship = $departamentship
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->groupBy('departments_t.name')
                ->groupBy('departments_t.id')
                ->get();
        }

///////////////////////////////////////Badania Pracownicy/////////////////////////////////////////////


        $employeeres = DB::table('log_download')
            ->selectRaw('
                users_t.name,
                users_t.last, 
                users_t.dep_id, 
                users_t.id as user_id,
                sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->where('baza', 'like', 'Badania')
            ->where('id_user', '=', $id)
            ->where('status', '=', 0);

        if($typ == 1) {
            $employeeres =  $employeeres
                ->where('date', 'like', $date[0] . '%')
                ->wherenotin('users_t.id',[1,105,127])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }
        else
        {
            $employeeres =  $employeeres
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->wherenotin('users_t.id',[1,105,127])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }

///////////////////////////////////////Wysylka Pracownicy/////////////////////////////////////////////
        $employeeship = DB::table('log_download')
            ->selectRaw('
                users_t.name,
                users_t.last, 
                users_t.dep_id,
                users_t.id as user_id, 
                sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('users_t', 'id_user', 'users_t.id')
            ->where('baza', 'like', 'Wysylka')
            ->where('id_user', '=', $id)
            ->where('status', '=', 0);

        if($typ == 1) {
            $employeeship =  $employeeship
                ->where('date', 'like', $date[0] . '%')
                ->wherenotin('users_t.id',[1,105,127])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }
        else
        {
            $employeeship =  $employeeship
                ->whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->wherenotin('users_t.id',[1,105,127])
                ->groupBy('users_t.id')
                ->groupBy('users_t.dep_id')
                ->get();
        }

        $cityres = DB::table('log_download')
            ->selectRaw('
                woj.woj,
                miasto,
                id_user,sum(baza8) as bisnode,
                    sum(baza8) as bisnode,
                    sum(bazazg) as zgody,
                    sum(bazareszta) as reszta,
                    sum(bazaevent) as event,
                    sum(bazaexito) as exito,
                    
                    sum(baza8Zgody) as bisnodeZgody,
                    sum(bazazgZgody) as zgodyZgody,
                    sum(bazaresztaZgody) as resztaZgody,
                    sum(bazaeventZgody) as eventZgody,
                    sum(bazaexitoZgody) as exitoZgody,
                                        
                    sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                     + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('woj', 'log_download.idwoj', 'woj.idwoj')
            ->where('baza', 'Badania')
            ->where('id_user', '=', $id)
            ->where('status', '=', 0);
        if($typ == 1) {
            $cityres = $cityres
                ->where('date', 'like', $date[0] . '%')
                ->groupBy('miasto', 'id_user', 'woj.woj')
                ->get();

        }else {
            $cityres = $cityres
                -> whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                ->groupBy('miasto', 'id_user', 'woj.woj')
                ->get();
        }

        $cityship = DB::table('log_download')
            ->selectRaw('
                        woj.woj,
                        miasto,
                        id_user,sum(baza8) as bisnode,
                            sum(baza8) as bisnode,
                            sum(bazazg) as zgody,
                            sum(bazareszta) as reszta,
                            sum(bazaevent) as event,
                            sum(bazaexito) as exito,
                            
                            sum(baza8Zgody) as bisnodeZgody,
                            sum(bazazgZgody) as zgodyZgody,
                            sum(bazaresztaZgody) as resztaZgody,
                            sum(bazaeventZgody) as eventZgody,
                            sum(bazaexitoZgody) as exitoZgody,
                                                
                            sum(baza8)+sum(bazazg)+sum(bazareszta)+sum(bazaevent)+sum(bazaexito)
                             + sum(baza8Zgody)+sum(bazazgZgody)+sum(bazaresztaZgody)+sum(bazaeventZgody)+sum(bazaexitoZgody) as suma')
            ->join('woj', 'log_download.idwoj', 'woj.idwoj')
            ->where('baza', 'Badania')
            ->where('id_user', '=', $id)
            ->where('status', '=', 0);
                if($typ == 1) {
                    $cityship = $cityship
                        ->where('date', 'like', $date[0] . '%')
                        ->groupBy('miasto', 'id_user', 'woj.woj')
                        ->get();

                }else {
                    $cityship = $cityship
                        -> whereBetween('date', [$date[0] . '%', $date[1] . ' 23:59:59'])
                        ->groupBy('miasto', 'id_user', 'woj.woj')
                        ->get();
                }

        session()->put('resandship', $resandship);
        session()->put('departamentres', $departamentres);
        session()->put('departamentship', $departamentship);
        session()->put('employeeres', $employeeres);
        session()->put('employeeship', $employeeship);
        session()->put('cityres', $cityres);
        session()->put('cityship', $cityship);

    }

    function setArray($tab)
    {
        $tablica = array();
        $tablica2 = array();
        foreach ($tab as $item)
        {
            array_push($tablica,$item);
        }

        foreach ($tablica as $item)
        {
            foreach ($item as $value)
            {
                array_push($tablica2,$value);
            }
        }
        return $tablica2;
    }



    public function setdata()
    {
        return view('raports.raport');
    }



}
