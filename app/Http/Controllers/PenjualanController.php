<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\UserModel;
use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class PenjualanController extends Controller
{
    public function index()
    {
        $activeMenu = 'penjualan';
        $breadcrumb = (object) [
            'title' => 'Data Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar penjualan yang terdaftar dalam sistem'
        ];

        $penjualan = PenjualanModel::select('penjualan_id', 'penjualan_tanggal')->get();
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();
        $user = UserModel::select('user_id', 'nama')->get();
        return view('penjualan.index', ['activeMenu' => $activeMenu, 'breadcrumb' => $breadcrumb, 'page' => $page, 'penjualan' => $penjualan, 'barang' => $barang, 'user' => $user]);
    }

    public function list(Request $request)
    {
        $penjualan = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
            ->with('user');
        // Filter data penjualan berdasarkan penjualan_tanggal
        $penjualan_tanggal = $request->input('filter_penjualan');
        if (!empty($penjualan_tanggal)) {
            $penjualan->where('penjualan_tanggal', $penjualan_tanggal);
        }

        return DataTables::of($penjualan)->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom:DT_RowIndex)
            ->addColumn('aksi', function ($penjualan) { // menambahkan kolom aksi
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id .
                    '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id .
                    '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id .
                    '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) 
            ->make(true);
    }

    public function create_ajax()
    {
        $penjualan = PenjualanModel::all();
        $user = UserModel::all();
        $barang = BarangModel::all();
        return view('penjualan.create_ajax', ['penjualan' => $penjualan, 'user' => $user, 'barang' => $barang]);
    }

    public function getHargaBarang($id)
    {
        // Cari barang berdasarkan id
        $barang = BarangModel::find($id);

        // Periksa apakah barang ditemukan
        if ($barang) {
            return response()->json([
                'status' => true,
                'harga_jual' => $barang->harga_jual
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Barang tidak ditemukan'
            ]);
        }
    }
    public function store_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'pembeli' => 'required|string|min:3|max:20',
            'penjualan_kode' => 'required|string|min:3|max:100|unique:t_penjualan,penjualan_kode',
            'penjualan_tanggal' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'numeric',
            'harga' => 'required|array',
            'harga.*' => 'numeric',
            'jumlah' => 'required|array',
            'jumlah.*' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors()
            ]);
        }

        try {
            // Simpan data penjualan
            $penjualan = new PenjualanModel();
            $penjualan->user_id = $request->user_id;
            $penjualan->pembeli = $request->pembeli;
            $penjualan->penjualan_kode = $request->penjualan_kode;
            $penjualan->penjualan_tanggal = $request->penjualan_tanggal;
            $penjualan->save();

            // Simpan detail barang
            foreach ($request->barang_id as $index => $barangId) {
                $penjualan_detail = new PenjualanDetailModel();
                $penjualan_detail->penjualan_id = $penjualan->penjualan_id;
                $penjualan_detail->barang_id = $barangId;
                $penjualan_detail->harga = $request->harga[$index];
                $penjualan_detail->jumlah = $request->jumlah[$index];
                $penjualan_detail->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan, data gagal disimpan!',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit_ajax(string $penjualan_id)
    {
        $penjualan = PenjualanModel::findOrFail($penjualan_id); // Pastikan gagal dengan benar jika tidak ditemukan
        $user = UserModel::all(); // Mengambil semua pengguna

        return view('penjualan.edit_ajax', ['penjualan' => $penjualan, 'user' => $user]);
    }

    public function update_ajax(Request $request, string $penjualan_id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'pembeli' => 'required|string',
                'penjualan_kode' => 'required|string',
                'penjualan_tanggal' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ]);
            }

            $penjualan = PenjualanModel::findOrFail($penjualan_id);

            $penjualan->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diupdate'
            ]);
        }

        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);

        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }

    public function delete_ajax(Request $request, string $penjualan_id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            // Cari data penjualan
            $penjualan = PenjualanModel::findOrFail($penjualan_id);


            if ($penjualan) {
                try {
                    // Hapus semua detail penjualan yang terkait
                    PenjualanDetailModel::where('penjualan_id', $penjualan_id)->delete();

                    // Hapus data penjualan
                    $penjualan->delete();

                    return response()->json([
                        'status' => true,
                        'message' => 'Data penjualan berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data gagal dihapus karena masih terkait dengan data lain'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak ditemukan'
                ]);
            }
        }

        return redirect('/');  // Jika bukan ajax request, redirect ke halaman utama
    }

    public function show_ajax(string $id)
    {

        $penjualan = PenjualanModel::with(['user', 'penjualan_detail.barang'])->find($id);


        if ($penjualan) {

            return view('penjualan.show_ajax', [
                'penjualan' => $penjualan
            ]);
        } else {

            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }


    public function import()
    {
        return view('penjualan.import');
    }

    // Function untuk proses import data melalui AJAX
    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            // Validasi file
            $rules = [
                'file_penjualan' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            // Mengambil file dari request
            $file = $request->file('file_penjualan');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            // Mengubah data sheet menjadi array
            $data = $sheet->toArray(null, false, true, true);

            $insertPenjualan = [];
            $insertPenjualanDetail = [];
            $penjualanKodeMap = [];

            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris > 1) {
                        // Cek apakah penjualan_kode sudah ada di array penjualan yang akan dimasukkan
                        if (!isset($penjualanKodeMap[$value['C']])) {
                            // Jika belum ada, tambahkan ke dalam array dan siapkan untuk insert ke t_penjualan
                            $penjualan_tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['D'])->format('Y-m-d H:i:s');

                            // Masukkan data ke t_penjualan
                            $penjualan = PenjualanModel::create([
                                'user_id' => $value['A'],
                                'pembeli' => $value['B'],
                                'penjualan_kode' => $value['C'],
                                'penjualan_tanggal' => $penjualan_tanggal,
                            ]);

                            // Simpan penjualan_id yang di-generate oleh database
                            $penjualanKodeMap[$value['C']] = $penjualan->penjualan_id;
                        }

                        // Masukkan data ke t_penjualan_detail dengan menghubungkan penjualan_id
                        $insertPenjualanDetail[] = [
                            'penjualan_id' => $penjualanKodeMap[$value['C']],
                            'barang_id' => $value['E'],
                            'harga' => $value['G'],
                            'jumlah' => $value['F'],
                            'created_at' => now(),
                        ];
                    }
                }

                // Insert ke t_penjualan_detail secara batch
                if (count($insertPenjualanDetail) > 0) {
                    PenjualanDetailModel::insert($insertPenjualanDetail);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil diimport'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }
        return redirect('/');
    }
    // Function untuk export data penjualan ke Excel
    public function export_excel()
    {
        // Ambil data penjualan yang akan diexport
        $penjualan = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
            ->with(['user', 'penjualan_detail.barang']) // Gunakan relasi 'penjualanDetail' sesuai model
            ->orderBy('penjualan_tanggal')
            ->get();

        // Load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // Ambil sheet yang aktif

        // Set header untuk penjualan
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal Penjualan');
        $sheet->setCellValue('C1', 'User ID');
        $sheet->setCellValue('D1', 'Nama Pembeli');
        $sheet->setCellValue('E1', 'Kode Penjualan');
        $sheet->setCellValue('F1', 'Barang ID');
        $sheet->setCellValue('G1', 'Nama Barang');
        $sheet->setCellValue('H1', 'Jumlah');
        $sheet->setCellValue('I1', 'Harga');

        $sheet->getStyle('A1:I1')->getFont()->setBold(true); // Bold header

        $no = 1;  // Nomor data dimulai dari 1
        $baris = 2; // Baris data dimulai dari baris ke 2

        // Loop untuk setiap penjualan
        foreach ($penjualan as $penj) {
            // Loop untuk setiap detail penjualan
            foreach ($penj->penjualan_detail as $detail) {
                $sheet->setCellValue('A' . $baris, $no);
                $sheet->setCellValue('B' . $baris, $penj->penjualan_tanggal); // Tanggal penjualan
                $sheet->setCellValue('C' . $baris, $penj->user->nama); // Nama user
                $sheet->setCellValue('D' . $baris, $penj->pembeli); // Nama pembeli
                $sheet->setCellValue('E' . $baris, $penj->penjualan_kode); // Kode penjualan
                $sheet->setCellValue('F' . $baris, $detail->barang_id); // Barang ID
                $sheet->setCellValue('G' . $baris, $detail->barang->barang_nama); // Nama barang
                $sheet->setCellValue('H' . $baris, $detail->jumlah); // Jumlah barang
                $sheet->setCellValue('I' . $baris, $detail->harga); // Harga per barang

                $baris++;
                $no++;
            }
        }

        // Set auto size untuk kolom
        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set title sheet
        $sheet->setTitle('Data Penjualan');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Penjualan ' . date('Y-m-d H:i:s') . '.xlsx';

        // Pengaturan header untuk download file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }
    public function export_pdf()
    {
        // Ambil data penjualan yang akan diexport
        $penjualan = PenjualanModel::select('penjualan_id', 'penjualan_kode', 'penjualan_tanggal', 'user_id', 'pembeli')
            ->with(['Penjualan_detail.barang', 'user']) // Pastikan relasi sudah terdefinisi
            ->orderBy('penjualan_tanggal')
            ->get();

        // Load view untuk PDF
        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);

        $pdf->setPaper('a4', 'landscape'); // Set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // Set true jika ada gambar dari URL
        $pdf->render();

        return $pdf->stream('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
