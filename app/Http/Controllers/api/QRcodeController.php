<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\QRcodeResource;
use App\Linkqr;
use App\Presensi;
use App\QRcode;
use Carbon\Carbon;
use Faker\Factory as Faker;

class QRcodeController extends Controller
{
    public function refresh($link,Request $request)
    {
        $linkqr = Linkqr::where('link','=',$link)->first();
        $linkqr->update(['latitude'=>$request->latitude,'longitude'=>$request->longitude]);
        $qrcode = QRcode::where('linkqr_id','=',$linkqr->id)->first();
        return response()->json([
            'qrcode' => $qrcode->qrcode,
            'latitude'=> $request->latitude,
            'longitude'=> $request->longitude,
        ]);
        //return new QRcodeResource($qrcode);
    }

    public function verif(Request $request)
    {
        $data = $request->validate([
            'qrcode' => 'required',
            'user' => 'required'
        ]);
        $mytime = Carbon::now();
        $presensi = Presensi::where('karyawan_id', '=', $request->user)->Where('tanggal', '=', $mytime->toDateString())->first();
        $qrcode = QRcode::where('qrcode','=',$request->qrcode)->first();
        $linkqr = Linkqr::find($qrcode->linkqr_id);
        $lokasi_user = $request->latitude.",".$request->longitude;
        $lokasi_qr = $linkqr->latitude.",".$linkqr->longitude;
        $selisih = (new QRcodeController)->distance($request->latitude,$request->longitude,$linkqr->latitude,$linkqr->longitude);
        if ($qrcode) {
            if (!$presensi) {
                Presensi::insert([
                    'karyawan_id' => $request->user,
                    'tanggal' => $mytime->toDateString(),
                    'jam_hadir'=>$mytime->toTimeString(),
                    'lokasi_user_hadir'=>$lokasi_user,
                    'lokasi_qrcode_hadir'=>$lokasi_qr,
                    'selisih'=>$selisih,
                    'validasi'=>'ya',
                    'cache'=>'pulang',
                    'linkqr'=>$qrcode->linkqr_id
                ]);
                (new QRcodeController)->qrcodeRefresh($request->qrcode);
                return response()->json([
                    'hasil'=>true,
                ]);
            }elseif ($presensi->cache=='pulang') {

                $startTime = Carbon::parse($presensi->jam_hadir);
                $finishTime = Carbon::parse($mytime);
                $totalDuration1 =floatval($startTime->diff($finishTime)->format('%h.%i')) ;
                $totalDuration2 = $startTime->diff($finishTime)->format('%h jam %i menit');
                $presensi = \DB::table('presensis')
                ->where('karyawan_id', '=', $request->user)
                ->Where('tanggal', '=', $mytime->toDateString())
                ->update([
                    'jam_pulang'=>$mytime->toTimeString(),
                    'cache'=>'selesai',
                    'durasi_kerja'=>$totalDuration1,
                    'lokasi_user_pulang'=>$lokasi_user,
                    'lokasi_qrcode_pulang'=>$lokasi_qr,
                ]);
                (new QRcodeController)->qrcodeRefresh($request->qrcode);
                return response()->json([
                    'hasil'=>true,
                ]);
            }elseif($presensi->cache=='selesai'){
                return response()->json([
                    'hasil'=>false,
                    'pesan'=>"Anda sudah selesai melakukan presensi hari ini"
                ]);
            }

        }
        return response()->json([
            'hasil'=>false,
            'pesan'=>"masih gagal, silahkan coba lagi"
        ]);
    }

    private function qrcodeRefresh($oldqr)
    {
        # code...
        $faker = Faker::create('id_ID');
        $secret = $faker->regexify('[A-Z0-9_%+-]+[A-Z0-9.-]+\[A-Z]{2,4}');
        $qrcode = QRcode::where('qrcode','=',$oldqr)->update(['qrcode'=>$secret]);
    }

    public function distance($lat1,$lon1,$lat2,$lon2){

        // $lat1=-0.504118;
        // $lon1=117.113558;
        // $lat2=-0.503656;
        // $lon2=117.113703;
        $unit="m";
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
          return 0;
        }
        else {
          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $unit = strtoupper($unit);

          if ($unit == "K") {
            return ($miles * 1.609344);
          } else if ($unit == "N") {
            return ($miles * 0.8684);
          } else {
            return ($miles*1609.34);
          }
        }
      }
}
