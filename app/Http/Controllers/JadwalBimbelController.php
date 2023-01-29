<?php

namespace App\Http\Controllers;

use App\Models\jadwal_bimbel;
use App\Models\Kelas;
use App\Models\Program;
use App\Models\Programs_x_kelas;
use App\Models\Sesi;
use App\Models\Siswa;
use App\Models\Tentor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JadwalBimbelController extends Controller
{
    public function index(){
        $jadwal_bimbels =  Sesi::with('tentors.jadwal_bimbels.siswa','tentors.jadwal_bimbels.programs_x_kelas.program','tentors.jadwal_bimbels.programs_x_kelas.kelas')->get();


        return inertia('JadwalBimbel/Index',[
            'jadwal_bimbels' => $jadwal_bimbels
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $siswas = Siswa::all();
        $sesis = Sesi::all();
        $program_x_kelases = Programs_x_kelas::with('program','kelas')->get();
        $tentors = Tentor::all();

        return inertia('JadwalBimbel/Create',[
            'siswas' => $siswas,
            'sesis' => $sesis,
            'program_x_kelases' => $program_x_kelases,
            'tentors' => $tentors
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'siswa_id'          => 'required',
            'sesi_id'             => 'required',
            'programs_x_kelas_id'        => 'required',
            'tentor_id'        => 'required'

        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $checkSiswa = jadwal_bimbel::where([
            ['siswas_id',"=",$request->siswa_id],
            ['sesis_id',"=",$request->sesis_id]
        ])->first();

        $checkTentor = jadwal_bimbel::where([
            ['tentor_id',"=",$request->tentor_id],
            ['programs_x_kelas_id',"=",$request->programs_x_kelas_id]
        ])->first();

        if($checkTentor){
            return response()->json([
                'tentor_id' =>["mentor sudah mengajar di kelas/program lain pada sesi yang sama"],
                'programs_x_kelas_id' => ["mentor sudah mengajar di kelas/program lain pada sesi yang sama"]
            ], 422);
        }

        if($checkSiswa){
            return response()->json([
                'siswas_id' =>["siswa sudah mengambil mapel lain di sesi yang sama"],
                'sesis_id' => ["siswa sudah mengambil mapel lain di sesi yang sama"]
            ], 422);
        }



        $jadwal = jadwal_bimbel::create([
            'siswas_id'          => $request->siswa_id,
            'sesis_id'             =>  $request->sesi_id,
            'programs_x_kelas_id'        => $request->programs_x_kelas_id,
            'tentor_id'        => $request->tentor_id
        ]);

        return $jadwal;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tarif = Programs_x_kelas::findOrFail($id)->with('program','kelas');

        if($tarif){
            $tarif = Programs_x_kelas::with('program','kelas')->whereId($id)->first();
        }

        $programs = Program::all();
        $clases = Kelas::all();



        return inertia('Tarifs/Edit', [
            'tarif' => $tarif,
            'programs' => $programs,
            'clases' => $clases
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        $validator = Validator::make($request->all(), [
            'program_id'          => 'required',
            'kelas_id'             => 'required',
            'tarif_belajar'        => 'required',
            'tarif_tentor'        => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }




        $tarif = Programs_x_kelas::whereId($id)->first();

        $checkTarif = Programs_x_kelas::where([
            ['programs_id',"=",$request->program_id],
            ['kelas_id',"=",$request->kelas_id]
        ])->first();

        if($checkTarif){
//            return "ada yang sama";
            if($checkTarif->id == $id){
                $checkTarif->update([
                    'programs_id'          => $request->program_id,
                    'kelas_id'             => $request->kelas_id,
                    'tarif_belajar'        => $request->tarif_belajar,
                    'tarif_tentor'        => $request->tarif_tentor
                ]);

                return redirect()->route('tarifs.index')->with('success', 'Data Berhasil Diupdate!');
            }else{
                return response()->json([
                    'program_id' =>["duplicate combination program and kelas"],
                    'kelas_id' => ["duplicate combination program and kelas"]
                ], 422);

            }
        }





        //update kelas
        $tarif->update([
            'programs_id'          => $request->program_id,
            'kelas_id'             => $request->kelas_id,
            'tarif_belajar'        => $request->tarif_belajar,
            'tarif_tentor'        => $request->tarif_tentor
        ]);

        return redirect()->route('tarifs.index')->with('success', 'Data Berhasil Diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tarif = Programs_x_kelas::findOrFail($id);

        $tarif->delete();

        if($tarif){
            return redirect()->route('tarifs.index')->with('success', 'Data Berhasil Dihapus!');
        }
    }


}
