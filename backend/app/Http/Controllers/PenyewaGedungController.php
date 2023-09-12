<?php

namespace App\Http\Controllers;

use App\Models\MitraPengelolaGedung;
use App\Models\User;
use App\Models\UserPenyewa;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PenyewaGedungController extends Controller
{
    public function saveDataAkunPenyewa(Request $request)
    {
        $user = User::where('statusenabled', 1)->where('email', $request->email)->first();

        $dataPenyewa = UserPenyewa::where('statusenabled', 1)->where('email', $request->email)->get();

        if (count($dataPenyewa) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah pernah digunakan!'
            ]);
        }

        try {
            $newUP = UserPenyewa::create([
                'statusenabled' => 1,
                'iduser' => $request['iduser'],
                'namalengkap' => $request['namalengkap'],
                'email' => $request['email'],
                'alamat' => $request['alamat'],
                'notelp' => $request['notelp'],
                'norekening' => $request['norekening'],
                'statusmitra' => 0
            ]);
            $response = [
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Pendaftaran Akun Berhasil',
                'data' => $newUP
            ];
            
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            return response()->json([
                'message' => 'Failed ' . $err->errorInfo
            ]);
        }
    }

    public function getListDataMitra(Request $request)
    {
        $dataMitra = DB::table('mitra_pengelola_gedungs as mpg')
            ->where('mpg.statusenabled', 1)
            ->where('mpg.statusmitra', 1);

        if (isset($request['namamitra']) && $request['namamitra'] != '' && $request['namamitra'] != 'undefined') {
            $dataMitra = $dataMitra->where('namamitra', 'like' , '%' . $request['namamitra'] . '%');
        }

        if (isset($request['lokasi']) && $request['lokasi'] != '' && $request['lokasi'] != 'undefined') {
            $dataMitra = $dataMitra->where('alamat', 'like' , '%' . $request['lokasi'] . '%');
        }

        if (isset($request['kapasitas']) && $request['kapasitas'] != '' && $request['kapasitas'] != 'undefined') {
            $minKapasitas = (int)$request['kapasitas'] - 300;
            $maxKapasitas = (int)$request['kapasitas'] + 300;
            $dataMitra = $dataMitra->where('makskapasitas', '>=' , $minKapasitas);
            $dataMitra = $dataMitra->where('makskapasitas', '<=' , $maxKapasitas);
        }

        $dataMitra = $dataMitra->get();

        $result = [
            'data' => $dataMitra,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function getDataMitra(Request $request)
    {
        $dataMitra = DB::table('mitra_pengelola_gedungs as mpg')
            ->where('mpg.statusenabled', 1)
            ->where('mpg.statusmitra', 1)
            ->where('mpg.id', $request['idmitra'])
            ->first();

        $result = [
            'data' => $dataMitra,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function getDataAsetMitra(Request $request)
    {
        $dataAset = DB::table('aset_mitras as am')
            ->where('am.statusenabled', 1)
            ->where('am.statusaset', 1)
            ->where('am.idmitra', $request['idmitra'])
            ->get();
        
        $idAset = [];
        foreach($dataAset as $item) {
            $idAset[] = $item->id;
        }

        $gambarAset = DB::table('gambar_asets as ga')
            ->where('ga.statusenabled', 1)
            ->whereIn('ga.idaset', $idAset)
            ->get();

        $result = [];
        foreach ($dataAset as $key) {
            $gambars = [];
            foreach ($gambarAset as $value) {
                if ($key->id == $value->idaset) {
                    $gambars[] = [
                        'idgambar' => $value->id,
                        'idaset' => $value->idaset,
                        'namafile' => $value->namafile,
                        'path' => $value->filepath
                    ];
                }
            }

            $result[] = [
                'asetgambar' => $gambars,
                'idaset' => $key->id,
                'idmitra' => $key->idmitra,
                'namaaset' => $key->namaaset,
                'deskripsi' => $key->deskripsi,
                'hargasewa' => $key->hargasewaaset,
                'hargadiskon' => $key->hargadiskonsewa,
                'maxjamsewa' => $key->maxjamsewa,
                'kapasitas' => $key->kapasitas,
            ];
        }

        $res = [
            'data' => $result,
            'as' => 'el'
        ];

        return response()->json($res, Response::HTTP_OK);
    }

    public function getDataAsetMitraDetail(Request $request)
    {
        $dataAset = DB::table('aset_mitras as am')
            ->where('am.statusenabled', 1)
            ->where('am.statusaset', 1)
            ->where('am.id', $request['idaset'])
            ->get();

        $gambarAset = DB::table('gambar_asets as ga')
            ->where('ga.statusenabled', 1)
            ->where('ga.idaset', $request['idaset'])
            ->get();

        $hargaTambahan = DB::table('harga_tambahan_asets as hta')
            ->where('hta.statusenabled', 1)
            ->where('hta.idaset', $request['idaset'])
            ->get();

        $result = [];
        foreach ($dataAset as $key) {
            $gambars = [];
            foreach ($gambarAset as $value) {
                if ($key->id == $value->idaset) {
                    $gambars[] = [
                        'idgambar' => $value->id,
                        'idaset' => $value->idaset,
                        'namafile' => $value->namafile,
                        'path' => $value->filepath
                    ];
                }
            }

            $hargas = [];
            foreach ($hargaTambahan as $item) {
                if ($key->id == $item->idaset) {
                    $hargas[] = [
                        'idharga' => $item->id,
                        'idaset' => $item->idaset,
                        'namatambahan' => $item->namatambahan,
                        'harga' => $item->hargatambahan,
                        'stok' => $item->sedia,
                    ];
                }
            }

            $result[] = [
                'asetgambar' => $gambars,
                'hargatambahan' => $hargas,
                'idaset' => $key->id,
                'idmitra' => $key->idmitra,
                'namaaset' => $key->namaaset,
                'deskripsi' => $key->deskripsi,
                'hargasewa' => $key->hargasewaaset,
                'hargadiskon' => $key->hargadiskonsewa,
                'maxjamsewa' => $key->maxjamsewa,
                'kapasitas' => $key->kapasitas,
            ];
        }

        $res = [
            'data' => $result,
            'as' => 'el'
        ];

        return response()->json($res, Response::HTTP_OK);
    }
}
