<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MitraPengelolaGedung;
use App\Models\User;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class MitraPengelolaGedungController extends Controller
{
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
}
