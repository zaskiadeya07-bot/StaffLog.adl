<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapKehadiranController extends Controller
{
    public function index()
    {
        $employees = Pengguna::where('role', 'karyawan')->get();
        return view('admin.rekap-karyawan', compact('employees'));
    }
    
    public function detail($id)
    {
        $employee = Pengguna::with('devisi')->findOrFail($id);
        return view('admin.detail-rekap', compact('employee'));  // ← detail-rekap (tanpa backend)
    }
    
    public function filter(Request $request)
    {
        $employeeId = $request->query('id');
        $bulan = $request->query('bulan', date('n'));
        $tahun = $request->query('tahun', date('Y'));
        
        $absensi = Presensi::where('id_pengguna', $employeeId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal', 'asc')
            ->get();
        
        $stats = [
            'hadir' => $absensi->where('status', 'Hadir')->count(),
            'izin' => $absensi->where('status', 'Izin')->count(),
            'sakit' => $absensi->where('status', 'Sakit')->count(),
            'alpha' => $absensi->where('status', 'Alpha')->count()
        ];
        
        $rows = [];
        foreach ($absensi as $item) {
            $hari = $item->hari ?? Carbon::parse($item->tanggal)->translatedFormat('l');
            
            $rows[] = [
                'id' => $item->id_presensi,
                'tanggal_formatted' => Carbon::parse($item->tanggal)->translatedFormat('d F Y'),
                'hari' => $hari,
                'jamMasuk' => $item->check_in ? Carbon::parse($item->check_in)->format('H:i') : '-',
                'jamPulang' => $item->check_out ? Carbon::parse($item->check_out)->format('H:i') : '-',
                'status' => $item->status,
                'keterangan' => $item->catatan_keterlambatan ?? '-',
                'latMasuk' => $item->check_in_lat,
                'lngMasuk' => $item->check_in_lng,
                'latPulang' => $item->check_out_lat,
                'lngPulang' => $item->check_out_lng
            ];
        }
        
        $employee = Pengguna::find($employeeId);
        
        return response()->json([
            'success' => true,
            'employee' => [
                'nama' => $employee->nama_lengkap,
                'divisi' => $employee->devisi->nama_devisi ?? '-'
            ],
            'stats' => $stats,
            'rows' => $rows
        ]);
    }
    
    public function detailAbsensi($id)
    {
        $absensi = Presensi::findOrFail($id);
        $hari = $absensi->hari ?? Carbon::parse($absensi->tanggal)->translatedFormat('l');
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $absensi->id_presensi,
                'tanggal_formatted' => Carbon::parse($absensi->tanggal)->translatedFormat('d F Y'),
                'hari' => $hari,
                'jamMasuk' => $absensi->check_in ? Carbon::parse($absensi->check_in)->format('H:i') : '-',
                'jamPulang' => $absensi->check_out ? Carbon::parse($absensi->check_out)->format('H:i') : '-',
                'status' => $absensi->status,
                'keterangan' => $absensi->catatan_keterlambatan ?? 'Tidak ada keterangan',
                'latMasuk' => $absensi->check_in_lat,
                'lngMasuk' => $absensi->check_in_lng,
                'latPulang' => $absensi->check_out_lat,
                'lngPulang' => $absensi->check_out_lng
            ]
        ]);
    }
    
    public function edit($id)
    {
        $employee = Pengguna::with('devisi')->findOrFail($id);
        $divisis = \App\Models\Devisi::all();
        return view('admin.edit-karyawan', compact('employee', 'divisis'));
    }
    
    public function update(Request $request, $id)
    {
        try {
            $employee = Pengguna::findOrFail($id);
            
            $request->validate([
                'nama' => 'required|string|max:255',
                'idKaryawan' => 'required|string|unique:pengguna,id_karyawan,' . $id . ',id_pengguna',
                'username' => 'required|string|unique:pengguna,username,' . $id . ',id_pengguna',
                'divisi' => 'required|exists:devisi,id_devisi',
                'alamat' => 'nullable|string',
                'phone' => 'nullable|string|max:15',
                'tanggalMulai' => 'nullable|date'
            ]);
            
            $updateData = [
                'id_karyawan' => $request->idKaryawan,
                'nama_lengkap' => $request->nama,
                'username' => $request->username,
                'divisi' => $request->divisi,
                'nomor_hp' => $request->phone,
                'tgl_mulai_kerja' => $request->tanggalMulai,
                'alamat' => $request->alamat,
            ];
            
            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }
            
            $employee->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil diupdate!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate karyawan: ' . $e->getMessage()
            ], 500);
        }
    }
}