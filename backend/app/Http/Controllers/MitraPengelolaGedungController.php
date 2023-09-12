<?php

namespace App\Http\Controllers;

use App\Models\AsetMitra;
use App\Models\DiskonMitra;
use App\Models\GambarAset;
use App\Models\HargaTambahanAset;
use Illuminate\Http\Request;
use App\Models\MitraPengelolaGedung;
use App\Models\PesananSewa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class MitraPengelolaGedungController extends Controller
{
    public function getDataMitra(Request $request)
    {
        $data = DB::table('mitra_pengelola_gedungs')->where('statusenabled', true)
            ->where('email', $request['email'])
            ->where('userid', $request['iduser'])
            ->first();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function savePendaftaranMitra(Request $request)
    {
        $user = User::where('statusenabled', 1)->where('email', $request->email)->first();

        $dataMPG = MitraPengelolaGedung::where('statusenabled', 1)->where('email', $request->email)->get();

        if (count($dataMPG) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah pernah digunakan!'
            ]);
        }

        $imageName = $user->email . '_samplegambarmitra.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        try {
            $newMPG = MitraPengelolaGedung::create([
                'statusenabled' => 1,
                'userid' => $user->id,
                'namalengkap' => $user->fullname,
                'namamitra' => $request->namamitra,
                'email' => $request->email,
                'alamat' => $request->alamat,
                'linkmapsalamat' => $request->linkmapsalamat,
                'notelp' => $request->notelp,
                'norekening' => $request->norekening,
                'makskapasitas' => $request->kapasitas,
                'fasilitas' => $request->fasilitas,
                'gambar' => $imageName,
                'path' => 'images/' . $imageName,
                'statusmitra' => 0
            ]);
            $response = [
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Pendaftaran berhasil dikirim mohon menunggu validasi pendaftaran oleh admin',
                'data' => $newMPG
            ];
            
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            return response()->json([
                'message' => 'Failed ' . $err->errorInfo
            ]);
        }
    }

    public function getDataAsetMitra(Request $request)
    {
        $data = DB::table('aset_mitras as am')
            ->join('mitra_pengelola_gedungs as mpg', 'mpg.id', '=', 'am.idmitra')
            ->select('am.id as idaset', 'mpg.id as idmitra', 'am.namaaset', 'am.deskripsi', 'am.hargasewaaset', 'am.hargadiskonsewa', DB::raw("CASE WHEN am.statusaset = 1 THEN 'Aktif' ELSE 'Tidak Aktif' END AS statusaset"))
            ->where('am.statusenabled', 1);
        
        if (isset($request['idmitra']) && $request['idmitra'] != '' && $request['idmitra'] != 'undefined') {
            $data = $data->where('mpg.id', $request['idmitra']);
        }

        if (isset($request['idaset']) && $request['idaset'] != '' && $request['idaset'] != 'undefined') {
            $data = $data->where('am.id', $request['idaset']);
        }

        if (isset($request['namaaset']) && $request['namaaset'] != '' && $request['namaaset'] != 'undefined') {
            $data = $data->where('am.namaaset', 'like' , '%' . $request['namaaset'] . '%');
        }

        $data = $data->orderBy('am.id');
        $data = $data->limit(50);
        $data = $data->get();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function saveDataAset(Request $request)
    {
        $idAset = $request['id_aset'];
        $fasilitas = $request['fasilitastambaan'];
        DB::beginTransaction();
        try {
            if ($idAset == '') {
                $newAset = new AsetMitra();
                $newAset->idmitra = $request['id_mitra'];
                $newAset->statusenabled = 1;
            } else {
                $newAset = AsetMitra::where('id', $idAset);
                $newAset->idmitra = $request['id_mitra'];
                $newAset->statusenabled = 1;

                HargaTambahanAset::where('idaset', $idAset)->where('statusenabled', 1)->delete();
            }
            $newAset->namaaset = $request['namaaset'];
            $newAset->deskripsi = $request['deskripsi'];
            $newAset->hargasewaaset = (int)$request['hargasewa'];
            $newAset->hargadiskonsewa = (int)$request['hargadiskon'];
            $newAset->maxjamsewa = (int)$request['maxjamsewa'];
            $newAset->kapasitas = (int)$request['kapasitas'];
            $newAset->statusaset = $request['statusaset'];
            $newAset->save();

            if (count($fasilitas) > 0) {
                foreach ($fasilitas as $key) {
                    $newFasil = new HargaTambahanAset();
                    $newFasil->idaset = $newAset->id;
                    $newFasil->statusenabled = 1;
                    $newFasil->namatambahan = $key['namafasilitas'];
                    $newFasil->hargatambahan = $key['hargafasilitas'];
                    $newFasil->statustambahan = $key['statusfasilitas'];
                    $newFasil->sedia = $key['tersedia'];
                    $newFasil->save();
                }
            }

            $response = [
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Simpan Aset Berhasil',
                'data' => $newAset
            ];

            DB::commit();
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed ' . $err->errorInfo
            ]);
        }
    }

    public function saveGambarAset(Request $request)
    {
        $fileImages = $request['images'];
        DB::beginTransaction();
        try {
            $user = User::where('statusenabled', 1)->where('email', $request->emailuser)->first();
            $dataMPG = MitraPengelolaGedung::where('statusenabled', 1)->where('email', $request->emailuser)->first();
            
            $index = 1;
            foreach ($fileImages as $key) {
                $imageName = $dataMPG->id . $dataMPG->userid . '-' . $index++ . '_gambaraset.' . $key->extension();
                $key->move(public_path('images'), $imageName);

                $newImgAset = new GambarAset();
                $newImgAset->statusenabled = 1;
                $newImgAset->idaset = $request['idaset'];
                $newImgAset->namafile = $imageName;
                $newImgAset->filepath = 'images/' . $imageName;
                $newImgAset->save();
            }

            $response = [
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Simpan Aset Gambar Berhasil'
            ];

            DB::commit();
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed ' . $err->errorInfo
            ]);
        }
    }

    public function updateStatusAset(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataAset = AsetMitra::where('statusenabled', 1)->where('id', $request['idaset'])->update([
                'statusaset' => $request['statusaset']
            ]);

            $response = [
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Status Aset Diperbarui'
            ];

            DB::commit();
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed ' . $err->errorInfo
            ]);
        }
    }

    public function hapusDataAset(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataAset = AsetMitra::where('statusenabled', 1)->where('id', $request['idaset'])->update([
                'statusenabled' => 0
            ]);

            $response = [
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Hapus Aset Berhasil'
            ];

            DB::commit();
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed ' . $err->errorInfo
            ]);
        }
    }

    public function getHargaTambahanAset(Request $request)
    {
        $data = DB::table('harga_tambahan_asets as hta')
        ->join('aset_mitras as am', 'am.id', '=', 'hta.idaset')
        ->select('hta.*')
        ->where('am.id', $request['idaset'])
        ->get();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function getDataDiskonMitra(Request $request)
    {
        $data = DB::table('diskon_mitras as dm')
            ->join('mitra_pengelola_gedungs as mpg', 'mpg.id', '=', 'dm.idmitra')
            ->select('dm.id as iddiskon', 'mpg.id as idmitra', 'dm.namadiskon', 'dm.hargadiskon', 'dm.persendiskon', 'dm.tglawaldiskon', 'dm.tglakhirdiskon', DB::raw("CASE WHEN dm.statusdiskon = 1 THEN 'Aktif' ELSE 'Tidak Aktif' END AS statusdiskon"))
            ->where('dm.statusenabled', 1);
        
        if (isset($request['tglAwal']) && $request['tglAwal'] != '' && $request['tglAwal'] != 'undefined') {
            $data = $data->where('dm.tglawaldiskon', '>=', $request['tglAwal']);
        }

        if (isset($request['tglAkhir']) && $request['tglAkhir'] != '' && $request['tglAkhir'] != 'undefined') {
            $data = $data->where('dm.tglakhirdiskon', '<=', $request['tglAkhir']);
        }

        if (isset($request['namadiskon']) && $request['namadiskon'] != '' && $request['namadiskon'] != 'undefined') {
            $data = $data->where('dm.namadiskon', 'like' , '%' . $request['namadiskon'] . '%');
        }

        $data = $data->orderBy('dm.id');
        $data = $data->limit(50);
        $data = $data->get();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function getDataDiskonMitraById(Request $request)
    {
        $data = DB::table('diskon_mitras')->where('statusenabled', 1)
            ->where('id', $request['iddiskon'])
            ->first();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function saveDiskonMitra(Request $request)
    {
        $idDiskon = $request['iddiskon'];
        DB::beginTransaction();
        try {
            if ($idDiskon == '') {
                $newDsk = new DiskonMitra();
                $newDsk->idmitra = $request['idmitra'];
                $newDsk->statusenabled = 1;
            } else {
                $newDsk = DiskonMitra::where('id', $idDiskon);
                $newDsk->idmitra = $request['idmitra'];
                $newDsk->statusenabled = 1;
            }
            $newDsk->namadiskon = $request['namadiskon'];
            $newDsk->tglawaldiskon = $request['tglawaldiskon'];
            $newDsk->tglakhirdiskon = $request['tglakhirdiskon'];
            if (isset($request['persendiskon'])) {
                $newDsk->persendiskon = $request['persendiskon'];
            }
            if (isset($request['hargadiskon'])) {
                $newDsk->hargadiskon = $request['hargadiskon'];
            }
            $newDsk->statusdiskon = $request['statusdiskon'];
            $newDsk->save();

            $response = [
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Simpan Diskon Berhasil',
                'data' => $newDsk
            ];

            DB::commit();
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed ' . $err->errorInfo
            ]);
        }
    }

    public function updateStatusDiskonMitra(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataAset = DiskonMitra::where('statusenabled', 1)->where('id', $request['iddiskon'])->update([
                'statusdiskon' => $request['statusdiskon']
            ]);

            $response = [
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Status Diskon Diperbarui'
            ];

            DB::commit();
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed ' . $err->errorInfo
            ]);
        }
    }

    public function getDataPesananMitra(Request $request)
    {
        $data = DB::table('pesanan_sewas as ps')
            ->leftJoin('user_penyewas as up', 'up.id', '=', 'ps.idpenyewa')
            ->leftJoin('mitra_pengelola_gedungs as mpg', 'mpg.id', '=', 'ps.idmitra')
            ->leftJoin('aset_mitras as am', 'am.id', '=', 'ps.idaset')
            ->leftJoin('status_pesanans as sp', 'sp.id', '=', 'ps.statuspesanan')
            ->select('ps.id as idpesanan','ps.nosewa as nopesanan', 'up.id as idpenyewa', 'up.iduser', 'up.namalengkap', 'up.notelp', 'up.email', 'up.notelp', 'up.alamat', 'am.id as idaset', 'am.namaaset', 'ps.tglawalsewa', 'ps.tglakhirsewa', 'ps.totaltagihan', 'ps.hargadibayar', 'ps.hargasewa', 'ps.sisatagihansewa', 'ps.tgljatuhtempo', 'sp.id as idstatuspesanan', 'sp.namastatus', 'mpg.id as idmitra');

        if (isset($request['tglawal']) && $request['tglawal'] != '' && $request['tglawal'] != 'undefined') {
            $data = $data->where('ps.tglawalsewa', '>=', $request['tglawal']);
        }

        if (isset($request['tglakhir']) && $request['tglakhir'] != '' && $request['tglakhir'] != 'undefined') {
            $data = $data->where('ps.tglakhirsewa', '<=', $request['tglakhir']);
        }
        
        if (isset($request['idmitra']) && $request['idmitra'] != '' && $request['idmitra'] != 'undefined') {
            $data = $data->where('mpg.id', $request['idmitra']);
        }

        if (isset($request['nopesanan']) && $request['nopesanan'] != '' && $request['nopesanan'] != 'undefined') {
            $data = $data->where('ps.nosewa', 'ilike', '%' . $request['nopesanan'] . '%');
        }

        if (isset($request['namapemesan']) && $request['namapemesan'] != '' && $request['namapemesan'] != 'undefined') {
            $data = $data->where('up.namalengkap', 'ilike', '%' . $request['namapemesan'] . '%');
        }

        $data = $data->orderBy('ps.id');
        $data = $data->limit(50);
        $data = $data->get();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function getJadwalSewaMitra(Request $request)
    {
        $data = DB::table('pesanan_sewas as ps')
        ->join('aset_mitras as am', 'am.id', '=', 'ps.idaset')
        ->select('am.*','ps.*')
        ->where('ps.statusenabled', 1);

        if (isset($request['idmitra']) && $request['idmitra'] != '' && $request['idmitra'] != 'undefined') {
            $data = $data->where('ps.idmitra', $request['idmitra']);
        }

        if (isset($request['idaset']) && $request['idaset'] != '' && $request['idaset'] != 'undefined') {
            $data = $data->where('ps.idaset', $request['idaset']);
        }

        if (isset($request['tglAwal']) && $request['tglAwal'] != '' && $request['tglAwal'] != 'undefined') {
            $data = $data->where('ps.tglawalsewa', '>=', $request['tglAwal']);
        }

        if (isset($request['tglAkhir']) && $request['tglAkhir'] != '' && $request['tglAkhir'] != 'undefined') {
            $data = $data->where('ps.tglakhirsewa', '<=', $request['tglAkhir']);
        }

        $data = $data->get();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function saveJadwalSewaManual(Request $request) 
    {
        DB::beginTransaction();
        try {
            $dataMPG = MitraPengelolaGedung::where('statusenabled', 1)->where('id', $request['idmitra'])->first();
            $dataAM = AsetMitra::where('statusenabled', 1)->where('id', $request['idaset'])->where('idmitra', $request['idmitra'])->first();

            $tglAwalSewa = explode(' ', $request['tglawalsewa']);
            $tglAkhirSewa = explode(' ', $request['tglakhirsewa']);
            $tglAwal = $tglAwalSewa[0] . ' 00:00';
            $tglAkhir = $tglAkhirSewa[0] . ' 23:59';

            $dataPesanan = DB::table('pesanan_sewas')
                ->select('tglawalsewa', 'tglakhirsewa')
                ->where('statusenabled', 1)
                ->where('idaset', $request['idaset'])
                ->where('statuspesanan', '<>', 5)
                ->where('tglawalsewa', '>=', $tglAwal)
                ->where('tglawalsewa', '<=', $tglAkhir)
                ->get();

            $tersedia = true;
            if (count($dataPesanan) > 0) {
                foreach ($dataPesanan as $value) {
                    if (($request['tglawalsewa'] >= $value->tglawalsewa) && ($request['tglawalsewa'] <= $value->tglakhirsewa)) {
                        $tersedia = false;
                        break;
                    }
                    if (($request['tglakhirsewa'] >= $value->tglawalsewa) && ($request['tglakhirsewa'] <= $value->tglakhirsewa)) {
                        $tersedia = false;
                        break;
                    }
                }
            }

            if ($tersedia == false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal Sewa Tidak Tersedia',
                    'code' => Response::HTTP_ALREADY_REPORTED
                ], Response::HTTP_ALREADY_REPORTED);
            }
            
            if ($request['nopesanan'] == '') {
                $now = new Carbon();
                $dateNow = $now->day . $now->month . $now->year;
                $countPesanan = DB::table('pesanan_sewas')->where('idmitra', $request['idmitra'])->get();
                $noPesanan = '';
                $noUrut = 1;
                if (count($countPesanan) == 0) {
                    $noPesanan = 'OMBRA/' . $dateNow . '/' . $noUrut;
                } else {
                    $lastNoUrut = ((int)$noUrut + count($countPesanan));
                    $noPesanan = 'OMBRA/' . $dateNow . '/' . $lastNoUrut;
                }
                $newPesanan = new PesananSewa();
                $newPesanan->statusenabled = 1;
                $newPesanan->idmitra = $request['idmitra'];
                $newPesanan->idaset = $request['idaset'];
                $newPesanan->nosewa = $noPesanan;
            } else {
                $newPesanan = PesananSewa::where('nosewa', $request['nosewa'])->where('statusenabled', 1)->where('idaset', $request['idaset'])->first();
            }

            $newPesanan->tglawalsewa = $request['tglawalsewa'];
            $newPesanan->tglakhirsewa = $request['tglakhirsewa'];
            $newPesanan->statuspesanan = $request['statuspesanan'];
            $newPesanan->save();

            $response = [
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Simpan Jadwal Berhasil',
                'data' => $newPesanan
            ];

            DB::commit();
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal Simpan Jadwal'
            ]);
        }
    }

    public function getListAsetByIdMitra(Request $request)
    {
        $data = DB::table('aset_mitras')
            ->where('statusenabled', 1)
            ->where('statusaset', 1)
            ->where('idmitra', $request['idmitra'])
            ->orderBy('namaaset')
            ->get();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function getDataSewaById(Request $request)
    {
        $data = DB::table('pesanan_sewas')
            ->where('statusenabled', 1)
            ->where('id', $request['idsewa'])
            ->first();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function getDataPembayaranById(Request $request)
    {
        $data = DB::table('struk_pembayaran_sewas')
            ->where('statusenabled', 1)
            ->where('idpesanan', $request['idsewa'])
            ->get();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function getDataPesananTambahan(Request $request)
    {
        $data = DB::table('pesanan_tamabahan_sewas as pts')
            ->join('aset_mitras as am', 'am.id', '=', 'pts.idaset')
            ->join('harga_tambahan_asets as hta', 'hta.idaset', '=', 'am.id')
            ->select('pts.id', 'am.id as idaset', 'hta.id as idtambahan', 'pts.idpesanan', 'hta.namatambahan', 'hta.hargatambahan', 'pts.jumlah')
            ->where('pts.statusenabled', 1)
            ->where('pts.idpesanan', $request['idsewa'])
            ->get();

        $result = [
            'data' => $data,
            'as' => 'el'
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    public function saveBatalPesananSewa(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataAset = PesananSewa::where('statusenabled', 1)->where('id', $request['idpesanan'])->update([
                'statuspesanan' => $request['statuspesanan']
            ]);

            $response = [
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Pesanan Dibatalkan'
            ];

            DB::commit();
            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed ' . $err->errorInfo
            ]);
        }
    }
}
