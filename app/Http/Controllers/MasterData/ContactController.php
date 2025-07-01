<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.master_data.contact.index', []);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "contact_name" => "Jonathan",
                    "type" => "Pelanggan",
                    "company_name" => "-",
                    "phone" => "08123456789",
                    "email" => "jonathan@mail.com",
                    "group_text" => "Group A",
                ],
                [
                    "id" => 2,
                    "contact_name" => "William",
                    "type" => "Pelanggan",
                    "company_name" => "-",
                    "phone" => "08123456789",
                    "email" => "william@mail.com",
                    "group_text" => "Group A",
                ],
                [
                    "id" => 3,
                    "contact_name" => "Yustinus",
                    "type" => "Supplier",
                    "company_name" => "CV. Megah Sentosa",
                    "phone" => "08123456789",
                    "email" => "cv_megah@mail.com",
                    "group_text" => "Group B",
                ],
                [
                    "id" => 4,
                    "contact_name" => "Ivana",
                    "type" => "Supplier",
                    "company_name" => "CV. Matahari Indah",
                    "phone" => "08123456789",
                    "email" => "cv_indah@mail.com",
                    "group_text" => "Group B",
                ],
                [
                    "id" => 5,
                    "contact_name" => "Yustinus",
                    "type" => "Supplier",
                    "company_name" => "CV. Megah Sentosa",
                    "phone" => "08123456789",
                    "email" => "cv_megah@mail.com",
                    "group_text" => "Group B",
                ],
                [
                    "id" => 6,
                    "contact_name" => "Ivanka",
                    "type" => "Lainnya",
                    "company_name" => "CV. ABC",
                    "phone" => "08123456789",
                    "email" => "cv_abc@mail.com",
                    "group_text" => "Group B",
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        return view('pages.master_data.contact.add', []);
    }

    public function export()
    {
        $data = [
            ["no" => 1, "value" => "Nama Kontak"],
            ["no" => 2, "value" => "Tipe Kontak"],
            ["no" => 3, "value" => "Status Kontak"],
            ["no" => 4, "value" => "Jenis Identitas"],
            ["no" => 5, "value" => "No Identitas"],
            ["no" => 6, "value" => "Email"],
            ["no" => 7, "value" => "No Telepon"],
            ["no" => 8, "value" => "Nama Perusahaan"],
            ["no" => 9, "value" => "No NPWP"],
            ["no" => 10, "value" => "No Telepon Perusahaan"],
            ["no" => 11, "value" => "No Fax"],
            ["no" => 12, "value" => "Alamat Penagihan - Provinsi"],
            ["no" => 13, "value" => "Alamat Penagihan - Kota"],
            ["no" => 14, "value" => "Alamat Penagihan - Kecamatan"],
            ["no" => 15, "value" => "Alamat Penagihan - Kelurahan"],
            ["no" => 16, "value" => "Alamat Pengriman - Provinsi"],
            ["no" => 17, "value" => "Alamat Pengriman - Kota"],
            ["no" => 18, "value" => "Alamat Pengriman - Kecamatan"],
            ["no" => 19, "value" => "Alamat Pengriman - Kelurahan"],
            ["no" => 20, "value" => "Akun Hutang"],
            ["no" => 21, "value" => "Akun Piutang"],
            ["no" => 22, "value" => "Syarat Pembayaran"],
        ];
        return view('pages.master_data.contact.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.contact.import', []);
    }
}
