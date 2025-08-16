<?php

namespace App\Http\Controllers\MasterData;

//Main
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Exceptions\AppException;
use App\Helpers\ValidationHelper;
use App\Helpers\DiscordNotifier;
//Actions
use App\Domains\Contact\Actions\CreateContactAction;
use App\Domains\Contact\Actions\UpdateContactAction;

//Services
use App\Domains\IdentityTypes\Services\IdentityTypesService;
use App\Domains\Coa\Services\CoaService;
use App\Domains\Bank\Services\BankService;
use App\Domains\Contact\Services\ContactAddressService;
use App\Domains\Contact\Services\ContactBankService;
use App\Domains\Contact\Services\ContactProductService;
use App\Domains\PaymentTerm\Services\PaymentTermService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Contact\Services\ContactService;
use App\Domains\Contact\Services\ContactTypeService;
use App\Domains\Contact\Services\ContactAuditLogService;
use App\Domains\SalesPerson\Services\SalesPersonService;
use App\Domains\Product\Services\ProductService;

//Enums
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Enums\AuditLogTypes;
use App\Enums\ContactAddressTypes;

class ContactController extends Controller
{
    public function index()
    {
        $contact_types = app(ContactTypeService::class)->findAll();
        return view('pages.master_data.contact.index', [
            'contact_types' => $contact_types
        ]);
    }

    public function index_data_table()
    {
        $data = app(ContactService::class)->findAllDatatable();
        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $identity_types = app(IdentityTypesService::class)->findAll();
        $contact_types = app(ContactTypeService::class)->findAll();
        $coa_accounts = app(CoaService::class)->findAll();
        $banks = app(BankService::class)->findAll();
        $payment_terms = app(PaymentTermService::class)->findAll();
        $tags = app(TagService::class)->findAll();
        $sales_users = app(SalesPersonService::class)->findAll();
        $products = app(ProductService::class)->findAll();

        $selectData = [
            'identity_types' => $identity_types,
            'sales_users' => $sales_users,
            'payable_accounts' => $coa_accounts,
            'receivable_accounts' => $coa_accounts,
            'payment_terms' => $payment_terms,
            'banks' => $banks,
            'contact_types' => $contact_types,
            'products' => $products,
            'tags' => $tags,
            'canAddTags' => true // Mock permission check
        ];

        return view('pages.master_data.contact.add', $selectData);
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'contact_name' => 'required|string|max:255',
                'group_name' => 'nullable|string|max:255',
                'contact_type' => 'required|in:CUSTOMER,SUPPLIER,EMPLOYEE,OTHERS',
                'contact_status' => 'required|in:ACTIVE,INACTIVE',
                'identity_type' => 'required|string|exists:m_identity_types,code',
                'identity_number' => 'required|string|max:16',
                'email' => 'nullable|email|max:64',
                'phone' => 'nullable|string|max:20',
                'sales_user' => 'required_if:contact_type,CUSTOMER|max:64',
                'company_name' => 'required|string|max:255',
                'npwp' => 'nullable|string|max:16',
                'company_phone' => 'nullable|string|max:20',
                'company_fax' => 'nullable|string|max:20',
                'billing_province' => 'nullable|string|max:255',
                'billing_city' => 'nullable|string|max:255',
                'billing_district' => 'nullable|string|max:255',
                'billing_subdistrict' => 'nullable|string|max:255',
                'billing_address' => 'required|string|max:255',
                'billing_postal_code' => 'nullable|string|max:5',
                'shipping_province' => 'nullable|string|max:255',
                'shipping_city' => 'nullable|string|max:255',
                'shipping_district' => 'nullable|string|max:255',
                'shipping_subdistrict' => 'nullable|string|max:255',
                'shipping_address' => 'required|string|max:255',
                'shipping_postal_code' => 'nullable|string|max:5',
                'payable_account' => 'required|exists:m_coas,code',
                'receivable_account' => 'required|exists:m_coas,code',
                'payment_term' => 'required|exists:m_payment_terms,code',
                'tags' => 'nullable|array',
                'tags.*' => 'nullable|exists:m_tags,id',

                // Bank repeater
                'bank_accounts' => 'required_if:contact_type,CUSTOMER|array',
                'bank_accounts.*.bank_code' => 'required_if:contact_type,CUSTOMER|exists:m_banks,code',
                'bank_accounts.*.branch_office' => 'required_if:contact_type,CUSTOMER|string|max:255',
                'bank_accounts.*.account_holder' => 'required_if:contact_type,CUSTOMER|string|max:255',
                'bank_accounts.*.account_number' => 'required_if:contact_type,CUSTOMER|string|max:255',

                // Produk repeater
                'products' => 'nullable|array',
                'products.*.product' => 'nullable|string|max:64',
                'products.*.price' => 'nullable|numeric|min:0',
            ]);

            app(CreateContactAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_CONTACT_SUCCESS->message()
            ], SuccessCodes::CREATE_CONTACT_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);
            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function edit($id)
    {
        $contact_header = app(ContactService::class)->findOne($id);

        if ($contact_header == null) {
            abort(404);
        }

        $contact_bank = app(ContactBankService::class)->findAllBasedOnContactId($id);
        $contact_product = app(ContactProductService::class)->findAllBasedOnContactId($id);

        $billing_contact_address = app(ContactAddressService::class)->findOneBasedOnContactIdAndType($id, ContactAddressTypes::BILLING);
        $shipping_contact_address = app(ContactAddressService::class)->findOneBasedOnContactIdAndType($id, ContactAddressTypes::SHIPPING);

        $identity_types = app(IdentityTypesService::class)->findAll();
        $contact_types = app(ContactTypeService::class)->findAll();
        $coa_accounts = app(CoaService::class)->findAll();
        $banks = app(BankService::class)->findAll();
        $payment_terms = app(PaymentTermService::class)->findAll();
        $tags = app(TagService::class)->findAll();
        $sales_users = app(SalesPersonService::class)->findAll();

        $product =  [
            ['value' => 'prod_a', 'label' => 'Produk A'],
            ['value' => 'prod_b', 'label' => 'Produk B'],
            ['value' => 'prod_c', 'label' => 'Produk C'],
            ['value' => 'prod_d', 'label' => 'Produk D'],
            ['value' => 'prod_e', 'label' => 'Produk E']
        ];

        return view('pages.master_data.contact.edit', [
            'contact' => $contact_header,
            'contact_bank' => $contact_bank,
            'contact_product' => $contact_product,
            'billing_contact_address' => $billing_contact_address,
            'shipping_contact_address' => $shipping_contact_address,
            'identity_types' => $identity_types,
            'contact_types' => $contact_types,
            'coa_accounts' => $coa_accounts,
            'banks' => $banks,
            'payment_terms' => $payment_terms,
            'tags' => $tags,
            'sales_users' => $sales_users,
            'payable_accounts' => $coa_accounts,
            'receivable_accounts' => $coa_accounts,
            'canAddTags' => true, // Mock permission check
            'products' => $product
        ]);
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'contact_id' => 'required|exists:m_contacts,id',
                'contact_name' => 'required|string|max:255',
                'group_name' => 'nullable|string|max:255',
                'contact_type' => 'required|in:CUSTOMER,SUPPLIER,EMPLOYEE,OTHERS',
                'contact_status' => 'required|in:ACTIVE,INACTIVE',
                'identity_type' => 'required|string|exists:m_identity_types,code',
                'identity_number' => 'required|string|max:16',
                'email' => 'nullable|email|max:64',
                'phone' => 'nullable|string|max:20',
                'sales_user' => 'required_if:contact_type,CUSTOMER|max:64',
                'company_name' => 'required|string|max:255',
                'npwp' => 'nullable|string|max:16',
                'company_phone' => 'nullable|string|max:20',
                'company_fax' => 'nullable|string|max:20',
                'billing_province' => 'nullable|string|max:255',
                'billing_city' => 'nullable|string|max:255',
                'billing_district' => 'nullable|string|max:255',
                'billing_subdistrict' => 'nullable|string|max:255',
                'billing_address' => 'required|string|max:255',
                'billing_postal_code' => 'nullable|string|max:5',
                'shipping_province' => 'nullable|string|max:255',
                'shipping_city' => 'nullable|string|max:255',
                'shipping_district' => 'nullable|string|max:255',
                'shipping_subdistrict' => 'nullable|string|max:255',
                'shipping_address' => 'required|string|max:255',
                'shipping_postal_code' => 'nullable|string|max:5',
                'payable_account' => 'required|exists:m_coas,code',
                'receivable_account' => 'required|exists:m_coas,code',
                'payment_term' => 'required|exists:m_payment_terms,code',
                'tags' => 'nullable|array',
                'tags.*' => 'nullable|exists:m_tags,id',

                // Bank repeater
                'bank_accounts' => 'required_if:contact_type,CUSTOMER|array',
                'bank_accounts.*.bank_id' => 'required_if:contact_type,CUSTOMER|exists:m_contact_banks,id',
                'bank_accounts.*.bank_code' => 'required_if:contact_type,CUSTOMER|exists:m_banks,code',
                'bank_accounts.*.branch_office' => 'required_if:contact_type,CUSTOMER|string|max:255',
                'bank_accounts.*.account_holder' => 'required_if:contact_type,CUSTOMER|string|max:255',
                'bank_accounts.*.account_number' => 'required_if:contact_type,CUSTOMER|string|max:255',

                // Produk repeater
                'products' => 'nullable|array',
                'products.*.product_id' => 'nullable|exists:m_contact_products,id',
                'products.*.product' => 'nullable|string|max:64',
                'products.*.price' => 'nullable|numeric|min:0',
            ]);

            app(UpdateContactAction::class)->execute($request->contact_id, $validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_CONTACT_SUCCESS->message()
            ], SuccessCodes::UPDATE_CONTACT_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);
            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
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

    public function detail($id)
    {
        $contact_header = app(ContactService::class)->findOne($id);

        if ($contact_header == null) {
            abort(404);
        }

        $contact_bank = app(ContactBankService::class)->findAllBasedOnContactId($id);
        $contact_product = app(ContactProductService::class)->findAllBasedOnContactId($id);
        $billing_contact_address = app(ContactAddressService::class)->findOneBasedOnContactIdAndType($id, ContactAddressTypes::BILLING);
        $shipping_contact_address = app(ContactAddressService::class)->findOneBasedOnContactIdAndType($id, ContactAddressTypes::SHIPPING);

        $audit_log_data = app(ContactAuditLogService::class)->findAllBasedOnContactId($id);

        return view('pages.master_data.contact.detail', [
            'contact' => $contact_header,
            'contact_bank' => $contact_bank,
            'contact_product' => $contact_product,
            'billing_contact_address' => $billing_contact_address,
            'shipping_contact_address' => $shipping_contact_address,
            'audit_log_data' => $audit_log_data,
            'auditLogType' => AuditLogTypes::MASTER_DATA_CONTACT->code()
        ]);
    }

    public function getProfilData($id)
    {
        // Return the same contact data for profile tab
        $contact = [
            'id' => $id,
            'contact_name' => 'Anwar',
            'company_name' => 'PT Maju Jaya Sentosa',
            'type' => 'Pelanggan',
            'group' => '',
            'email' => 'pelanggan1@example.com',
            'phone' => '081234567890',
            'identity_type' => 'KTP',
            'identity_number' => '3175091203940000.00',
            'billing_address' => '',
            'shipping_address' => 'Ciomas, Bogor',
            'other_info' => '-',
            'landline' => '',
            'fax' => '',
            'npwp' => '01.234.567.8-999.000',
            'bank_name' => 'Bank Central Asia',
            'bank_branch' => 'Bogor',
            'account_holder' => 'Anwar',
            'account_number' => '541-002-7891',
            'debt_account' => '(2-20100) Hutang',
            'receivable_account' => '(1-20100) Piutang',
            'payment_terms' => ''
        ];

        return response()->json($contact);
    }

    public function getTransactionData($id)
    {
        $data = [
            'summary' => [
                'monthly_invoiced' => 5,
                'total_amount' => 5,
                'delivery_receivables' => 0
            ],
            'transactions' => [
                [
                    'date' => '12/12/2024',
                    'transaction_number' => 'Sales Invoice #100002',
                    'due_date' => '2/12/2024',
                    'status' => 'Paid',
                    'total' => 'Rp 5,000,000.00'
                ],
                [
                    'date' => '8/12/2024',
                    'transaction_number' => 'Purchase Order #10001',
                    'due_date' => '3/1/2025',
                    'status' => 'Open',
                    'total' => 'Rp 3,000,000.00'
                ]
            ]
        ];

        return response()->json($data);
    }

    public function getCreditMemoData($id)
    {
        $data = [
            'credit_memos' => [
                [
                    'date' => '5/5/2024',
                    'credit_memo_number' => 'Credit Memo #100001',
                    'original_amount' => 'Rp 5,000,000.00',
                    'used_amount' => 'Rp 500,000.00',
                    'remaining_balance' => 'Rp 4,500,000.00',
                    'selected' => true
                ],
                [
                    'date' => '5/5/2024',
                    'credit_memo_number' => 'Credit Memo #100002',
                    'original_amount' => 'Rp 4,000,000.00',
                    'used_amount' => 'Rp 4,000,000.00',
                    'remaining_balance' => 'Rp -',
                    'selected' => false
                ],
                [
                    'date' => '5/5/2024',
                    'credit_memo_number' => 'Credit Memo #100003',
                    'original_amount' => 'Rp 500,000.00',
                    'used_amount' => 'Rp 500,000.00',
                    'remaining_balance' => 'Rp -',
                    'selected' => false
                ]
            ]
        ];

        return response()->json($data);
    }

    private function getIndexSelectData()
    {
        return [
            'groupFilters' => [
                ['value' => 'CUSTOMER_WARUNG', 'label' => 'CUSTOMER_WARUNG - Customer Warung'],
                ['value' => 'CUSTOMER_RETAIL', 'label' => 'CUSTOMER_RETAIL - Customer Retail'],
                ['value' => 'CUSTOMER_WHOLESALE', 'label' => 'CUSTOMER_WHOLESALE - Customer Grosir'],
                ['value' => 'SUPPLIER_KEMASAN', 'label' => 'SUPPLIER_KEMASAN - Supplier Kemasan'],
                ['value' => 'SUPPLIER_MAINTENANCE', 'label' => 'SUPPLIER_MAINTENANCE - Supplier Maintenance'],
                ['value' => 'SUPPLIER_LOGISTIK', 'label' => 'SUPPLIER_LOGISTIK - Supplier Logistik'],
            ],
            'typeFilters' => [
                ['value' => 'PELANGGAN', 'label' => 'PELANGGAN - Pelanggan'],
                ['value' => 'SUPPLIER', 'label' => 'SUPPLIER - Supplier'],
                ['value' => 'KARYAWAN', 'label' => 'KARYAWAN - Karyawan'],
                ['value' => 'LAINNYA', 'label' => 'LAINNYA - Lainnya'],
            ]
        ];
    }
}
