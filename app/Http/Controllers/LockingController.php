<?php

namespace App\Http\Controllers;

use App\LockHistory;
use App\Record;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LockingController extends Controller
{
    //

    public function lockGet()
    {
        return view('lock.lock');
    }

    public function lockPost(Request $request)
    {
        $lockHistory = new LockHistory;
        $response = '';
        $record = Record::where('telefon', '=', $request->telefon)->first();
        if ($record == null) {
            $record = new Record;
            $record->telefon = $request->telefon;
            $record->idbaza = 0;
            $record->lock = 1;
            $lockHistory->id_baza = $record->idbaza;
            $response = '.new';
        } else {
            if ($record->lock == 1) {
                return 'already locked';
            }
            $lockHistory->id_baza = $record->idbaza;
            $record->imie = "";
            $record->nazwisko = "";
            $record->ulica = "";
            $record->nrdomu = "";
            $record->nrmieszkania = "";
            $record->miasto = "";
            $record->idkod = 0;
            $record->idbaza = 30;
            $record->lock = 1;
            $record->save();
        }

        $lockHistory->telefon = $record->telefon;
        $lockHistory->save();

        return 'locked' . $response;
    }

    public function datatableLockAjax(Request $request)
    {
        $id_baza = $request->id_baza;
        $telefon = $request->telefon;
        $created_at = $request->created_at;

        $id_baza = $id_baza === null ? '%' : $id_baza;
        $telefon = $telefon === null ? '%' : $telefon;
        $created_at = $created_at === null ? '%' : $created_at;

        $lockHistory = LockHistory::where([
            ['id_baza', 'like', $id_baza],
            ['telefon', 'like', $telefon],
            ['created_at', 'like', $created_at.'%']
        ])->get();

        return datatables($lockHistory)->make(true);
    }

    public function lockMultiAjax(Request $request){
        $file = $request->file('fileWithPhoneNumbers');
        $fileOpened = fopen($file->getPathname(),'r');
        $count = 0;
        $tempDataArr = [];
        $dataArr = [];
        while( $data = fgetcsv($fileOpened)){
            if($count < 5){
                array_push($tempDataArr, $data);
            }
            /* if($count > 0 && $count < 1000000){
                 array_push($dataArr, $data[0]);
             }*/
            $count++;
        }
        fclose($fileOpened);

        return ['dataArr' => $tempDataArr, 'count' => $count];//, 'data' => $dataArr];

    }

    public function lockMultiSecondAjax(Request $request){
        $file = $request->file('fileWithPhoneNumbers');
        $fileOpened = fopen($file->getPathname(),'r');
        $actualRecord = $request->actualRecord;
        $step = 10000;
        $count = 1;
        $data = fgetcsv($fileOpened);
        $phoneIndex = null;
        foreach ($data as $index => $column){
            if(strtolower($column) == 'telefon'){
                $phoneIndex = $index;
                break;
            }
        };
        $phonesArray = [];
        while( $data = fgetcsv($fileOpened)){
            if($count >= $actualRecord){
                if($count < $actualRecord + $step){
                    array_push($phonesArray,intval($data[$phoneIndex]));
                }else{
                    break;
                }
            }
            $count++;
        }
        fclose($fileOpened);
        $records = Record::whereIn('telefon', $phonesArray)->get();//->select('telefon','idbaza', DB::raw('"'.time().'"'), DB::raw('"'.time().'"'));

        $notFoundPhonesToBlock = collect(array_map(function ($value){
            return (object)['telefon' => $value];
        }, $phonesArray))->whereNotIn('telefon', $records->pluck('telefon')->toArray())->pluck('telefon')->toArray();

        $notFoundPhonesToBlockCount = count($notFoundPhonesToBlock);
        if($notFoundPhonesToBlockCount > 0){
            $lockHistoryStringValues = implode(",", array_map(function($v){
                return '('.$v.',0,"'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'")';
            },$notFoundPhonesToBlock));
            $done = [];
            $recordStringValues = implode(",", array_map(function($v){
                return '('.$v.', 1)';
            },$notFoundPhonesToBlock));
            array_push($done, DB::insert('INSERT INTO lock_history (telefon, id_baza, created_at, updated_at) VALUES '.$lockHistoryStringValues,
                []));
            array_push($done, DB::insert('INSERT INTO rekordy (telefon, rekordy.lock) VALUES '.$recordStringValues,
                []));
        }

        $foundPhonesToBlock = $records->where('lock',0)->pluck('telefon')->toArray();
        $foundPhonesToBlockCount = count($foundPhonesToBlock);
        if($foundPhonesToBlockCount > 0){
            DB::insert('INSERT INTO lock_history (telefon, id_baza, created_at, updated_at) ('.Record::whereIn('telefon', $foundPhonesToBlock)
                    ->select('telefon','idbaza', DB::raw('"'.date('Y-m-d H:i:s').'"'), DB::raw('"'.date('Y-m-d H:i:s').'"'))
                    ->toSql().')',
                $foundPhonesToBlock);
            Record::whereIn('telefon', $foundPhonesToBlock)->update([
                'imie' => '',
                'nazwisko' => '',
                'ulica' => '',
                'nrdomu' => '',
                'nrmieszkania' => '',
                'miasto' => '',
                'idkod' => 0,
                'idbaza' => 30,
                'lock' => 1
            ]);
        }
        if($phoneIndex !== null){
            return ['actualRecord' => $count,
                'notFoundPhonesToBlockCount' => $notFoundPhonesToBlockCount,
                'foundPhonesToBlockCount' => $foundPhonesToBlockCount];
        }else{
            return 'error';
        }
    }
}
