<?php

namespace App\Console;

use App\Models\Request_Cuti;
use Carbon\Carbon;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $karyawan_hadir = Absensi::filterByToday()->select('id')->get();
            $karyawan_tidak_hadir = Karyawan::whereNotIn('id', $karyawan_hadir)->select('id')->get();

            $today = new Carbon();
            $is_weekend = ($today->dayOfWeek === Carbon::SATURDAY) || ($today->dayOfWeek === Carbon::SUNDAY);
            
            if (count($karyawan_tidak_hadir)>0 && !$is_weekend)
            {
                $karyawan_tidak_hadir->map(function ($id) {
                    Absensi::create([
                        'karyawan_id' => $id,
                        'in' => null,
                        'out' => null,
                        'status' => '',
                    ]);
                }
                );
            }
        })->dailyAt('23:59');

        $schedule->call(function () {
            $karyawan_cuti = Request_Cuti::select('id')->onCuti()->approved()->get();

            if (count($karyawan_cuti)>0)
            {
                $karyawan_cuti->map(function ($id) {
                    Absensi::create([
                        'karyawan_id' => $id,
                        'in' => null,
                        'out' => null,
                        'status' => 'cuti',
                    ]);
                });
            }
            
        })->dailyAt('00:01');

        $schedule->call(function () {
            $karyawans = Karyawan::all();

            $karyawans->map(function ($karyawan) {
                $karyawan->total_cuti_tahunan = 12;
                $karyawan->save();
            });

        })->yearly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
