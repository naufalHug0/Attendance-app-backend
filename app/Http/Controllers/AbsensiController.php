<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use DateTimeZone;
use Carbon\Carbon;
use App\Helpers\Time;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Notification;
use App\Models\Request_Cuti;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use App\Helpers\ArrayUtilities;
use App\Models\Request_Sakit;
use Illuminate\Http\Response;
use App\Helpers\GetKaryawanData;
use Illuminate\Http\RedirectResponse;
use App\Helpers\NotificationFormatter;
use App\Helpers\Numbers;
use App\Helpers\Utilities;
use App\Http\Requests\StoreAbsensiRequest;
use App\Http\Requests\UpdateAbsensiRequest;

class AbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $Date = Time::getTimestamp();

        try 
        {
            $karyawan = Karyawan::find($request->id);
            
            if ($karyawan) {
                if ($request->IN) {
                    $wfo = Absensi::attendanceByKaryawanId($karyawan->id)->wfo()->filterByToday()->get();

                    Absensi::create([
                        'karyawan_id'=>$karyawan->id,
                        'in'=>$Date,
                        'out'=>null,
                        'status'=>$request->type,
                    ]);

                    if ($request->type==='lembur'&&$wfo)
                    {
                        $wfo[0]->out = $Date;
                        $wfo[0]->save();
                    }

                    return ApiFormatter::createApi(201,'Inserted Successfully');
                } else {
                    $absensi = Absensi::attendanceByKaryawanId($karyawan->id)->status($request->type)->filterByToday()->get();

                    if (count($absensi)>0) {
                        //wfo to wfa
                        $absensi = $absensi[0];

                        if ($absensi->out===null)
                        {
                            $work_time = Time::calculateWorkingHours($absensi->in);

                            if ($request->type==='wfa') 
                            {
                                $wfa = Absensi::attendanceByKaryawanId($karyawan->id)->wfa()->filterByToday()->get();

                                $wfa[0]->out = $Date;
                                $wfa[0]->save();
                            }

                            if ($request->overtime)
                            {
                                Absensi::create([
                                    'karyawan_id'=>$karyawan->id,
                                    'in'=>$Date,
                                    'out'=>null,
                                    'status'=>'lembur',
                                ]);
                                return ApiFormatter::createApi(201,'Inserted Successfully');
                            }

                            if ((int)explode(':',$work_time)[0]<8) {
                                if($request->type==='wfo'||$request->type==='wfh')
                                return ApiFormatter::createApi(403,'User not allowed to checkout',[
                                    'work_time'=>$work_time
                                ]);
                            } else {
                                $absensi->out = $Date;
                                $absensi->save();
                            }
                            return ApiFormatter::createApi(201,'Inserted Successfully');

                            // wfo to wfa, total < 8 jam
                            // maka wfa end, wfo lanjut
                            // jam wfo normal (in out)

                            // if wfo + wfa > 8 jam then 
                            // jam out wfo = jam in wfa

                        } else return ApiFormatter::createApi(400,'Already checkout');

                        // return $start->diff(new Carbon($Date))->format('%H:%I');
                    } else {
                        // wfh
                        if ($request->type==='wfh')
                        {
                            $wfh = Absensi::attendanceByKaryawanId($karyawan->id)->wfh()->filterByToday()->get();

                            if (count($wfh)>0)
                            {
                                $work_time = Time::calculateWorkingHours($wfh->in);

                                if ((int)explode(':',$work_time)[0]<8) {
                                    return ApiFormatter::createApi(403,'User not allowed to checkout',[
                                        'work_time'=>$work_time
                                    ]);
                                } 
                                $wfh->out = $Date;
                                $wfh->save();
                                return ApiFormatter::createApi(201,'Inserted Successfully');
                            }
                        }
                        // wfa to wfo
                        if ($request->type==='wfa')
                        {
                            $wfa = Absensi::attendanceByKaryawanId($karyawan->id)->wfa()->filterByToday()->get();

                            if (count($wfa)>0)
                            {
                                $work_time = Time::calculateWorkingHours($wfa->in);

                                if ($request->overtime)
                                {
                                    Absensi::create([
                                        'karyawan_id'=>$karyawan->id,
                                        'in'=>$Date,
                                        'out'=>null,
                                        'status'=>'lembur',
                                    ]);
                                    return ApiFormatter::createApi(201,'Inserted Successfully');
                                }

                                if ((int)explode(':',$work_time)[0]<8) {
                                    return ApiFormatter::createApi(403,'User not allowed to checkout',[
                                        'work_time'=>$work_time
                                    ]);
                                } 

                                $wfa->out = $Date;
                                $wfa->save();
                                return ApiFormatter::createApi(201,'Inserted Successfully');
                            }
                        }
                    }
                }
            }

        } catch(\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
        return ApiFormatter::createApi(201,'Inserted Successfully');
    }

    public function getToday(Request $request) {
        try {
            $data = (isset($request->status))? 
            ((is_array($request->status))?Absensi::attendanceByKaryawanId($request->id)->filterByToday()->statusAll($request->status)->get()
            :Absensi::attendanceByKaryawanId($request->id)->filterByToday()->status($request->status)->get())
            :Absensi::attendanceByKaryawanId($request->id)->filterByToday()->orUncheckedOutLembur()->get();

            if (count($data) > 0)
            {
                if (isset($request->status)) return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'], $data);

                else {
                    $duration = Time::calculateWorkingHours($data[0]->in);

                    $duration = [
                        'duration' => $duration,
                        'hours' => intval(explode(':', $duration)[0])
                    ];

                    return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'], [
                        'data'=>$data,
                        'duration'=>$duration
                    ]);
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
        
        return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'], []);
    }

    public function getMonthAttendance(Request $request) {
        try {
            $data = [
                'hadir' => [
                    'data' => Absensi::attendanceByKaryawanId($request->id)->filterByMonth($request->year, $request->month)->hadir()->get(),
                    'count' => Absensi::attendanceByKaryawanId($request->id)->filterByMonth($request->year, $request->month)->hadir()->get()->groupBy(fn ($absen) => Carbon::parse($absen->in)->format('Y-m-d'))->count(),
                ],
                'cuti' => [
                    'data' => Absensi::attendanceByKaryawanId($request->id)->filterByMonth($request->year, $request->month)->cuti()->get(),
                    'requests'=>Request_Cuti::filterById($request->id)->startsIn($request->year, $request->month)->orEndsIn($request->year, $request->month)->get(),
                    'count'=>Absensi::attendanceByKaryawanId($request->id)->filterByMonth($request->year, $request->month)->cuti()->get()->count()
                ],
                'sakit' => [
                    'data'=>Absensi::attendanceByKaryawanId($request->id)->filterByMonth($request->year, $request->month)->sakit()->get(),
                    'requests'=>Request_Sakit::karyawan($request->id)->month($request->month, $request->year)->get(),
                    'count'=>Absensi::attendanceByKaryawanId($request->id)->filterByMonth($request->year, $request->month)->sakit()->get()->count()
                ],
                'tanpa_keterangan' => [
                    'data'=>Absensi::attendanceByKaryawanId($request->id)->filterByMonth($request->year, $request->month)->tanpaKeterangan()->get(),
                    'count'=>Absensi::attendanceByKaryawanId($request->id)->filterByMonth($request->year, $request->month)->tanpaKeterangan()->get()->count()
                ],
            ];

            return ApiFormatter::createApi(200, ApiFormatter::$successMessages['get'], $data);

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    public function getWorkingHours(Request $request)
    {
        try {
            $absensi = Absensi::attendanceByKaryawanId($request->id)->wfo()->filterByToday()->get();
            $wfa = Absensi::attendanceByKaryawanId($request->id)->wfa()->filterByToday()->get();
            $wfh = Absensi::attendanceByKaryawanId($request->id)->wfh()->filterByToday()->get();

            if (count($absensi) > 0) return ApiFormatter::createApi(200,ApiFormatter::$successMessages['get'],
            [
                'time'=>Time::calculateWorkingHours($absensi[0]->in),
                'hours'=>intval(explode(':', Time::calculateWorkingHours($absensi[0]->in))[0])
            ]);
            else if (count($wfa) > 0) return ApiFormatter::createApi(200,ApiFormatter::$successMessages['get'],
            [
                'time'=>Time::calculateWorkingHours($wfa[0]->in),
                'hours'=>intval(explode(':', Time::calculateWorkingHours($wfa[0]->in))[0])
            ]);
            else if (count($wfh) > 0) return ApiFormatter::createApi(200,ApiFormatter::$successMessages['get'],
            [
                'time'=>Time::calculateWorkingHours($wfh[0]->in),
                'hours'=>intval(explode(':', Time::calculateWorkingHours($wfh[0]->in))[0])
            ]);
            
            else return ApiFormatter::createApi(404,'Data not found');
        } catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    public static function getTotalAttendanceToday()
    {
        $hadir = Absensi::hadir()->filterByToday()->haventCheckout()->get()->groupBy('karyawan_id');
        $activeUsers = Absensi::with(['karyawans' => function ($q) {$q->select('id','nama','profile_image');}])->filterByToday()->get()->groupBy('karyawan_id')->map(fn($user)=>$user[0]);

        $total = $hadir->count();
        $percent = ($total / Karyawan::all()->count()) * 100;
        $percent = Numbers::is_decimal($percent) ? round($percent, 2) : $percent;

        return ApiFormatter::createApi(200,ApiFormatter::$successMessages['get'],[
            'active'=>$activeUsers,
            'total'=>$total,
            'percent'=>$percent
        ]);
    }


    public static function getAttendanceReport(Request $request)
    {
        $request->validate([
            'ids'=>'required',
            'status'=>'required',
            'start'=>'required|date',
            'end'=>'required|date|after:start'
        ],[
            'required'=>'Wajib diisi',
            'end.after'=>'Tanggal harus setelah tanggal mulai',
        ]);
        try {
            // $status = ($request->status === 'tanpa keterangan')?'':$request->status;
            $index_of_alpha = array_search(null, $request->status);
            $req_status = $request->status;

            if ($index_of_alpha) $req_status[$index_of_alpha] = '';
            
            $result = Absensi::with(['karyawans' => function($q) {
                $q->select('id','nama','email','alamat','jabatan');
            }])->karyawansWithIds($request->ids)->statusAll($req_status)->between($request->start, $request->end)->get();
            // $result = Absensi::with(['karyawans' => function($q) {
            //     $q->select('id','nama','email','alamat','jabatan');
            // }])->karyawansWithIds($request->ids)->status($status)->between($request->start, $request->end)->get();

            if ($result)
            {
                $result = ArrayUtilities::groupBy($result->toArray(), fn($data)=>$data['karyawan_id']);

                $result = collect($result)->map(function ($data) {
                    return ArrayUtilities::groupBy($data,fn($data)=>$data['status']);
                });

                // return $result;


                $result = collect($result)->map(function ($data) use ($request) {
                    $first_element = reset($data);
                    $first_element = reset($first_element);

                    $index = 0;
                    foreach($data as $status) {
                        $formattedData[$index] = [
                            ['id'=>$first_element['karyawans']['id']],
                            ['nama'=>$first_element['karyawans']['nama']],
                            ['jabatan'=>$first_element['karyawans']['jabatan']],
                            ['status'=>$status[0]['status']===''?'Tanpa Keterangan':$status[0]['status']],
                        ];

                        $currTimestamp = new Carbon($request->start);
    
                        $endTimestamp = new Carbon($request->end);
                        
                        $endTimestamp = $endTimestamp->addDays(1);
                        
                        $currDate=intval(Time::getDateFromTimestamp($currTimestamp));
                        $currMonth=intval(Time::getMonthFromTimestamp($currTimestamp));
                        $currYear=intval(Time::getYearFromTimestamp($currTimestamp));
    
                        for ($i = 0; $currTimestamp->ne($endTimestamp); $i++) {
                            if ($i < count($status)) {
                                if ($status[$i]['in'] === null) {
                                    
                                        $attendDate = intval(Time::getDateFromTimestamp($status[$i]['created_at']));
                                        $attendMonth=intval(Time::getMonthFromTimestamp($status[$i]['created_at']));
                                        $attendYear=intval(Time::getYearFromTimestamp($status[$i]['created_at']));
                                } else {
                                
                                        $attendDate = intval(Time::getDateFromTimestamp($status[$i]['in']));
                                        $attendMonth=intval(Time::getMonthFromTimestamp($status[$i]['in']));
                                        $attendYear=intval(Time::getYearFromTimestamp($status[$i]['in']));
                                }
                            }
    
                            while (
                                ($attendDate!==$currDate
                                ||
                                $attendMonth!==$currMonth
                                ||
                                $attendYear!==$currYear)
                                &&
                                $currTimestamp->ne($endTimestamp)
                                )
                            {
                                $formattedData[$index][] = ["$currDate" => '-'];
                                // if ($currDate===11&&$currMonth===3) {
                                //     return [
                                //         'curr'=>$currDate,
                                //         'timestamp'=>$currTimestamp,
                                //         'attend'=>$status
                                //     ];
                                // }
                                $currDate = $currDate+1 !== intval(Time::getDateFromTimestamp($currTimestamp)) ? intval(Time::getDateFromTimestamp(Time::addDays($currTimestamp, 1))):$currDate+1;

                                $currMonth=intval(Time::getMonthFromTimestamp(Time::addDays($currTimestamp, 1)));

                                $currYear=intval(Time::getYearFromTimestamp(Time::addDays($currTimestamp, 1)));

                                $currTimestamp = Time::addDays($currTimestamp, 1);
                            }
    
                            if (($attendDate===$currDate
                            ||
                            $attendMonth===$currMonth
                            ||
                            $attendYear===$currYear)&&$i < count($status))
                            {
                                if ($status[$i]['status']==='sakit')
                                {
                                    $formattedData[$index][] = ["$currDate" => 'SAKIT'];
                                } else if ($status[$i]['status']==='cuti') {
                                    $formattedData[$index][] = ["$currDate" => 'CUTI'];
                                } else if ($status[$i]['status']==='') {
                                    $formattedData[$index][] = ["$currDate" => 'ALPHA'];
                                }
                                else {
                                    $formattedData[$index][] = ["$currDate" => $status[$i]['out']===null?'-': Time::getHourMinuteFromTimestamp($status[$i]['in']) . '-' . Time::getHourMinuteFromTimestamp($status[$i]['out'])];
                                }
                            }
    
                            if ($currTimestamp->eq($endTimestamp))
                                break;
    
                            $currDate=intval(Time::getDateFromTimestamp(Time::addDays($currTimestamp, 1)));
                            $currMonth=intval(Time::getMonthFromTimestamp(Time::addDays($currTimestamp, 1)));
                            $currYear=intval(Time::getYearFromTimestamp(Time::addDays($currTimestamp, 1)));
    
                            $currTimestamp = Time::addDays($currTimestamp, 1);
                        }
                        $index++;
                    }
                    return $formattedData;
                });
                // $result = ArrayUtilities::groupBy($result->toArray(), fn($data)=>$data['karyawan_id']);

                // $result = collect($result)->map(function ($data) use ($request,$status) {
                //     $formattedData = [
                //         ['id'=>$data[0]['karyawans']['id']],
                //         ['nama'=>$data[0]['karyawans']['nama']],
                //         ['jabatan'=>$data[0]['karyawans']['jabatan']],
                //         ['status'=>$data[0]['status']],
                //     ];

                //     $currTimestamp = new Carbon($request->start);

                //     $endTimestamp = new Carbon($request->end);
                    
                //     $endTimestamp = $endTimestamp->addDays(1);
                    
                //     $currDate=intval(Time::getDateFromTimestamp($currTimestamp));

                //     for ($i = 0; $currTimestamp->ne($endTimestamp); $i++) {
                //         if ($i < count($data)) {
                //             $attendDate = ($data[$i]['in']===null)?intval(Time::getDateFromTimestamp($data[$i]['created_at'])):intval(Time::getDateFromTimestamp($data[$i]['in']));
                //         }

                //         while ($attendDate!==$currDate&&$currTimestamp->ne($endTimestamp))
                //         {
                //             $formattedData[] = ["$currDate" => '-'];
                //             $currDate++;
                //             $currTimestamp = Time::addDays($currTimestamp, 1);
                //         }

                //         if ($attendDate===$currDate&&$i < count($data))
                //         {
                //             if ($request->status==='sakit')
                //             {
                //                 $formattedData[] = ["$currDate" => 'SAKIT'];
                //             } else if ($request->status==='cuti') {
                //                 $formattedData[] = ["$currDate" => 'CUTI'];
                //             } else if ($status==='') {
                //                 $formattedData[] = ["$currDate" => 'ALPHA'];
                //             }
                //             else {
                //                 $formattedData[] = ["$currDate" => Time::getHourMinuteFromTimestamp($data[$i]['in']) . '-' . Time::getHourMinuteFromTimestamp($data[$i]['out'])];
                //             }
                //         }

                //         if ($currTimestamp->eq($endTimestamp))
                //             break;

                //         $currDate=intval(Time::getDateFromTimestamp(Time::addDays($currTimestamp, 1)));

                //         $currTimestamp = Time::addDays($currTimestamp, 1);
                //     }

                //     return $formattedData;
                // });

                return ApiFormatter::createApi(200,ApiFormatter::$successMessages['get'],$result);
            }

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
        return ApiFormatter::createApi(404,'Data not found');
    }

    public static function checkHaventCheckout(Request $request)
    {
        try {
            $haventCheckout = Absensi::attendanceByKaryawanId($request->id)->yesterday()->haventCheckout()->hadir()->notLembur()->get()->count()>0;

            if ($haventCheckout) return ApiFormatter::createApi(403, 'User not allowed to access');

            return ApiFormatter::createApi(200,'User allowed to access');

        } catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    public static function confirm (Request $request)
    {
        $request->validate([
            'alasan'=>'required'
        ],[
            'required'=>'Wajib diisi'
        ]);
        try {
            $karyawan = Karyawan::find($request->id);

            if ($karyawan)
            {
                $format = NotificationFormatter::confirmCheckout($karyawan->nama,$request->alasan);

                Notification::create([
                    'karyawan_id' => $karyawan->id,
                    'title'=>$format['title'],
                    'body'=>$format['body'],
                    'is_read'=>false,
                    'for'=>'admin'
                ]);

                if (isset($request->hour))
                {
                    $minute = isset($request->minute) ? $request->minute : '0';
                    $absensi = Absensi::attendanceByKaryawanId($request->id)->yesterday()->haventCheckout()->hadir()->notLembur()->get();

                    $absensi->map(function ($item) use ($request,$minute) {
                        $date = new Carbon($item->in);
                        $item->out = $date->setHour(intval($request->hour))->setMinute(intval($minute));
                        $item->save();
                    });
                }

                return ApiFormatter::createApi(201,'Created successfully');
            }

            return ApiFormatter::createApi(404, 'User not found');
        } 
        catch (\Illuminate\Database\QueryException $e) {
            return ApiFormatter::createApi(500,'Internal Server Error',$e);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(Absensi $absensi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absensi $absensi)
    {
        //
    }
}
