<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        for ($k = 1; $k <= 10;$k++)
        {
            if ($k%2===0)
            {
                for ($month = 1; $month <= 2; $month++) {
                    for ($i = 1; $i <= 31; $i++) {
                        if ($i === 13 || $i === 14) {
                            \App\Models\Absensi::create([
                                'karyawan_id' => $k,
                                'in' => "2023-0{$month}-{$i} 09:02:21",
                                'out' => "2023-0{$month}-{$i} 17:12:01",
                                'status' => 'wfo',
                                'created_at' => "2023-0{$month}-{$i} 09:02:21",
                            ]);
                            if ($i === 13) {
                                $notif = \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title' => 'Test telah mengajukan cuti',
                                    'body' => 'Test telah mengajukan cuti selama kurun waktu berikut : ',
                                    'for' => 'admin',
                                    'is_read' => true,
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Request_Cuti::create([
                                    'karyawan_id' => $k,
                                    'approved_by_id' => 5,
                                    'notification_id' => $notif->id,
                                    'start' => "2023-0{$month}-18",
                                    'end' => "2023-0{$month}-19",
                                    'is_approved' => true,
                                    'type' => 'nikah',
                                    'desc' => 'Mau nikah dulu',
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title' => 'Pengajuan cutimu telah disetujui',
                                    'body' => 'Pengajuan cutimu telah disetujui oleh Testing',
                                    'for' => 'karyawan',
                                    'is_read' => true,
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                            } else {
                                $notif = \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title' => 'Test telah mengajukan cuti',
                                    'body' => 'Test telah mengajukan cuti selama kurun waktu berikut : ',
                                    'for' => 'admin',
                                    'is_read' => true,
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Request_Cuti::create([
                                    'karyawan_id' => $k,
                                    'approved_by_id' => 3,
                                    'notification_id' => $notif->id,
                                    'start' => "2023-0{$month}-25",
                                    'end' => "2023-0{$month}-26",
                                    'is_approved' => true,
                                    'type' => 'haid',
                                    'desc' => '',
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title' => 'Pengajuan cutimu telah disetujui',
                                    'body' => 'Pengajuan cutimu telah disetujui oleh Testing',
                                    'for' => 'karyawan',
                                    'is_read' => true,
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                            }
                        } else if ($i === 23) {
                            \App\Models\Absensi::create([
                                'karyawan_id' => $k,
                                'in' => null,
                                'out' => null,
                                'status' => 'sakit',
                                'created_at' => "2023-0{$month}-{$i} 09:02:21",
                            ]);
                            $notif = \App\Models\Notification::create([
                                'karyawan_id' => $k,
                                'title' => 'Test sakit',
                                'body' => 'Test hari ini tidak masuk karena sakit.',
                                'for' => 'admin',
                                'is_read' => true,
                                'created_at' => "2023-0{$month}-{$i} 09:02:21",
                            ]);
                            \App\Models\Request_Sakit::create([
                                'karyawan_id' => $k,
                                'notification_id' => $notif->id,
                                'image' => 'http://enkripa.test/storage/sakit-images/0Qh5sR6m8OdOMSfUYY7u5LnWx7koqdL97IALlfmx.jpg',
                                'created_at' => "2023-0{$month}-{$i} 09:02:21",
                            ]);
                        } else if (($i === 18 || $i === 19) || ($i === 25 || $i === 26)) {
                            \App\Models\Absensi::create([
                                'karyawan_id' => $k,
                                'in' => null,
                                'out' => null,
                                'status' => 'cuti',
                                'created_at' => "2023-0{$month}-{$i} 09:02:21",
                            ]);
                        } else {
                            $num = mt_rand(0, 2);
                            if ($num === 0) {
                                \App\Models\Absensi::create([
                                    'karyawan_id' => $k,
                                    'in' => "2023-0{$month}-{$i} 09:02:21",
                                    'out' => "2023-0{$month}-{$i} 17:12:01",
                                    'status' => 'wfo',
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                            } else if ($num === 2) {
                                \App\Models\Absensi::create([
                                    'karyawan_id' => $k,
                                    'in' => "2023-0{$month}-{$i} 09:02:21",
                                    'out' => "2023-0{$month}-{$i} 14:02:21",
                                    'status' => 'wfa',
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Absensi::create([
                                    'karyawan_id' => $k,
                                    'in' => "2023-0{$month}-{$i} 14:02:21",
                                    'out' => "2023-0{$month}-{$i} 17:12:21",
                                    'status' => 'wfo',
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                                $notif = \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title' => 'Test telah mengajukan WFA',
                                    'body' => 'Test telah mengajukan WFA untuk tanggal : ',
                                    'for' => 'admin',
                                    'is_read' => true,
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Request_WFA::create([
                                    'karyawan_id' => $k,
                                    'notification_id' => $notif->id,
                                    'location' => 'Data Center',
                                    'date' => "2023-0{$month}-{$i} 09:02:21",
                                    'is_approved' => true,
                                    'approved_by_id' => 3,
                                    'image' => 'http://enkripa.test/storage/sakit-images/0Qh5sR6m8OdOMSfUYY7u5LnWx7koqdL97IALlfmx.jpg',
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title' => 'Pengajuan WFAmu telah disetujui',
                                    'body' => 'Pengajuan WFAmu telah disetujui oleh Testing',
                                    'for' => 'karyawan',
                                    'is_read' => true,
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                            } else if ($num === 1) {
                                \App\Models\Absensi::create([
                                    'karyawan_id' => $k,
                                    'in' => "2023-0{$month}-{$i} 09:02:21",
                                    'out' => "2023-0{$month}-{$i} 17:12:01",
                                    'status' => 'wfh',
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                                $notif = \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title' => 'Test telah mengajukan WFH',
                                    'body' => 'Test telah mengajukan WFH untuk tanggal : ',
                                    'for' => 'admin',
                                    'is_read' => true,
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Request_WFH::create([
                                    'karyawan_id' => $k,
                                    'notification_id' => $notif->id,
                                    'is_approved' => true,
                                    'approved_by_id' => 3,
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title' => 'Pengajuan WFHmu telah disetujui',
                                    'body' => 'Pengajuan WFHmu telah disetujui oleh Testing',
                                    'for' => 'karyawan',
                                    'is_read' => true,
                                    'created_at' => "2023-0{$month}-{$i} 09:02:21",
                                ]);
                            }
                        }
                    }
                }
            } else {
                for ($month=1;$month<=2;$month++)
                {
                    for ($i = 1; $i <= 31; $i++) {
                        if ($i === 17||$i===18) {
                            \App\Models\Absensi::create([
                                'karyawan_id' => $k,
                                'in'=>"2023-0{$month}-{$i} 09:02:21",
                                'out'=>"2023-0{$month}-{$i} 17:12:01",
                                'status'=>'wfo',
                                'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                            ]);
                            if ($i===17) {
                                $notif = \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title'=>'Test telah mengajukan cuti',
                                    'body'=>'Test telah mengajukan cuti selama kurun waktu berikut : ',
                                    'for'=>'admin',
                                    'is_read'=>true,
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Request_Cuti::create([
                                    'karyawan_id' => $k,
                                    'approved_by_id' => 5,
                                    'notification_id'=>$notif->id,
                                    'start'=>"2023-0{$month}-17",
                                    'end'=>"2023-0{$month}-18",
                                    'is_approved'=>true,
                                    'type'=>'nikah',
                                    'desc'=>'Mau nikah dulu',
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title'=>'Pengajuan cutimu telah disetujui',
                                    'body'=>'Pengajuan cutimu telah disetujui oleh Testing',
                                    'for'=>'karyawan',
                                    'is_read'=>true,
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                            } 
                        } else if ($i === 10) {
                            \App\Models\Absensi::create([
                                'karyawan_id' => $k,
                                'in'=>null,
                                'out'=>null,
                                'status'=>'sakit',
                                'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                            ]);
                            $notif = \App\Models\Notification::create([
                                'karyawan_id' => $k,
                                'title'=>'Test sakit',
                                'body'=>'Test hari ini tidak masuk karena sakit.',
                                'for'=>'admin',
                                'is_read'=>true,
                                'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                            ]);
                            \App\Models\Request_Sakit::create([
                                'karyawan_id' => $k,
                                'notification_id'=>$notif->id,
                                'image'=>'http://enkripa.test/storage/sakit-images/0Qh5sR6m8OdOMSfUYY7u5LnWx7koqdL97IALlfmx.jpg',
                                'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                            ]);
                        } else if (($i === 3 || $i === 4)||($i === 26 || $i === 27)) {
                            \App\Models\Absensi::create([
                                'karyawan_id' => $k,
                                'in'=>null,
                                'out'=>null,
                                'status'=>'cuti',
                                'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                            ]);
                        } else {
                            $num = mt_rand(0, 2);
                            if ($num===0)
                            {
                                \App\Models\Absensi::create([
                                    'karyawan_id' => $k,
                                    'in'=>"2023-0{$month}-{$i} 09:02:21",
                                    'out'=>"2023-0{$month}-{$i} 17:12:01",
                                    'status'=>'wfo',
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                            }
                            else if ($num===2)
                            {
                                \App\Models\Absensi::create([
                                    'karyawan_id' => $k,
                                    'in'=>"2023-0{$month}-{$i} 09:02:21",
                                    'out'=>"2023-0{$month}-{$i} 14:02:21",
                                    'status'=>'wfa',
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Absensi::create([
                                    'karyawan_id' => $k,
                                    'in'=>"2023-0{$month}-{$i} 14:02:21",
                                    'out'=>"2023-0{$month}-{$i} 17:12:21",
                                    'status'=>'wfo',
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                                $notif = \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title'=>'Test telah mengajukan WFA',
                                    'body'=>'Test telah mengajukan WFA untuk tanggal : ',
                                    'for'=>'admin',
                                    'is_read'=>true,
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Request_WFA::create([
                                    'karyawan_id' => $k,
                                    'notification_id' =>$notif->id,
                                    'location'=>'Data Center',
                                    'date'=>"2023-0{$month}-{$i} 09:02:21",
                                    'is_approved'=>true,
                                    'approved_by_id'=>3,
                                    'image'=>'http://enkripa.test/storage/sakit-images/0Qh5sR6m8OdOMSfUYY7u5LnWx7koqdL97IALlfmx.jpg',
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title'=>'Pengajuan WFAmu telah disetujui',
                                    'body'=>'Pengajuan WFAmu telah disetujui oleh Testing',
                                    'for'=>'karyawan',
                                    'is_read'=>true,
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                            }
                            else if ($num===1)
                            {
                                \App\Models\Absensi::create([
                                    'karyawan_id' => $k,
                                    'in'=>"2023-0{$month}-{$i} 09:02:21",
                                    'out'=>"2023-0{$month}-{$i} 17:12:01",
                                    'status'=>'wfh',
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                                $notif = \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title'=>'Test telah mengajukan WFH',
                                    'body'=>'Test telah mengajukan WFH untuk tanggal : ',
                                    'for'=>'admin',
                                    'is_read'=>true,
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Request_WFH::create([
                                    'karyawan_id' => $k,
                                    'notification_id' =>$notif->id,
                                    'is_approved'=>true,
                                    'approved_by_id'=>3,
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                                \App\Models\Notification::create([
                                    'karyawan_id' => $k,
                                    'title'=>'Pengajuan WFHmu telah disetujui',
                                    'body'=>'Pengajuan WFHmu telah disetujui oleh Testing',
                                    'for'=>'karyawan',
                                    'is_read'=>true,
                                    'created_at'=>"2023-0{$month}-{$i} 09:02:21",
                                ]);
                            }
                        }
                    }
                }
            }
        }

        \App\Models\Admin::create([
            'karyawan_id' => 1,
        ]);
        \App\Models\Admin::create([
            'karyawan_id' => 3,
        ]);
        \App\Models\Admin::create([
            'karyawan_id' => 5,
        ]);

        \App\Models\Karyawan::factory(10)->create();
    }
}
