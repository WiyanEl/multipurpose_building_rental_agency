<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use App\Models\MitraPengelolaGedung;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class AdminAplikasiController extends Controller
{
    public function getDataMitraNonvalidasi(Request $request)
    {
        $data = DB::table('mitra_pengelola_gedungs AS mpg')
            ->where('statusenabled', 1)
            ->where('statusmitra', $request['status']);

        if (isset($request['userid']) && $request['userid'] != '' && $request['userid'] != 'undefined') {
            $data = $data->where('userid', $request['userid']);
        }    
        if (isset($request['namamitra']) && $request['namamitra'] != '' && $request['namamitra'] != 'undefined') {
            $data = $data->where('namamitra', 'like', '%' . $request['namamitra'] . '%');
        }    
        if (isset($request['email']) && $request['email'] != '' && $request['email'] != 'undefined') {
            $data = $data->where('email', 'like', '%' . $request['email'] . '%');
        } 

        $data = $data->orderBy('updated_at');
        $data = $data->limit(50);
        $data = $data->get();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function getDetailMitraNonvalidasi(Request $request)
    {
        $data = DB::table('mitra_pengelola_gedungs')->where('statusenabled', true)->where('email', $request['email'])->where('statusmitra', 0)->first();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function sendEmailToMitra(Request $request)
    {
        $data = [
            'name' => "Admin App O'MBRA",
            'body' => $request['body']
        ];

        Mail::to($request['emailtujuan'])->send(new SendEmail($data));
        
        $result = [
            'message' => 'Email Berhasil Dikirim',
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function saveValidasiMitra(Request $request)
    {
        $dataCalon = DB::table('mitra_pengelola_gedungs')->where('statusenabled', true)->where('email', $request['email'])->first();

        if ($dataCalon->statusmitra != 0) {
            return response()->json([
                'message' => 'Mitra sudah divalidasi / dihapus',
                'as' => 'el'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $data = DB::table('mitra_pengelola_gedungs')->where('statusenabled', true)->where('email', $request['email'])->update([
                'statusmitra' => $request['statusmitra']
            ]);
            $user = DB::table('users')->where('statusenabled', true)->where('email', $request['email'])->update([
                'user_role' => $request['userrole']
            ]);

            $response = [
                'success' => true,
                'message' => 'Simpan Validasi Berhasil',
                'as' => 'epic'
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $err) {
            return response()->json([
                'message' => 'Failed ' . $err->errorInfo
            ]);
        }
    }
}
