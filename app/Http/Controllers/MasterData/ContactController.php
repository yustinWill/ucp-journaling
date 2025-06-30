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

    public function add(){
        return view('pages.master_data.contact.add', []);
    }
}
