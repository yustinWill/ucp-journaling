<?php

namespace App\Domains\Contact\Actions;

use App\Domains\Contact\Services\ContactAddressService;
use App\Domains\Contact\Services\ContactAuditLogService;
use App\Domains\Contact\Services\ContactBankService;
use App\Domains\Contact\Services\ContactProductService;
use App\Domains\Contact\Services\ContactService;
use App\Enums\ActionTypes;
use App\Enums\ContactAddressTypes;
use Illuminate\Support\Facades\DB;

class UpdateContactAction
{
    public function execute(int $contact_id, array $data)
    {
        $old_data = null;
        $old_data['header'] = app(ContactService::class)->findOne($contact_id);
        $old_data['billing_address'] = app(ContactAddressService::class)->findOneBasedOnContactIdAndType($contact_id, ContactAddressTypes::BILLING);
        $old_data['shipping_address'] = app(ContactAddressService::class)->findOneBasedOnContactIdAndType($contact_id, ContactAddressTypes::SHIPPING);
        $old_data['bank'] = app(ContactBankService::class)->findAllBasedOnContactId($contact_id);
        $old_data['product'] = app(ContactProductService::class)->findAllBasedOnContactId($contact_id);

        return DB::transaction(function () use ($contact_id, $data, $old_data) {
            app(ContactService::class)->update($contact_id, $data);

            app(ContactAddressService::class)->update($contact_id, [
                'type' => ContactAddressTypes::BILLING,
                'province' => $data['billing_province'] ?? null,
                'city' => $data['billing_city'] ?? null,
                'district' => $data['billing_district'] ?? null,
                'subdistrict' => $data['billing_subdistrict'] ?? null,
                'address' => $data['billing_address'],
                'postal_code' => $data['billing_postal_code'] ?? null,
                'area' => $data['billing_area'] ?? null,
                'maps_url' => $data['billing_maps_url'] ?? null,
            ]);

            app(ContactAddressService::class)->update($contact_id, [
                'type' => ContactAddressTypes::SHIPPING,
                'province' => $data['shipping_province'] ?? null,
                'city' => $data['shipping_city'] ?? null,
                'district' => $data['shipping_district'] ?? null,
                'subdistrict' => $data['shipping_subdistrict'] ?? null,
                'address' => $data['shipping_address'],
                'postal_code' => $data['shipping_postal_code'] ?? null,
                'area' => $data['shipping_area'] ?? null,
                'maps_url' => $data['shipping_maps_url'] ?? null,
            ]);

            $current_contact = app(ContactBankService::class)->findAllBasedOnContactId($contact_id);

            $current_bank_ids = [];
            foreach ($current_contact as $row) {
                $current_bank_ids[] = $row->id;
            }

            if (isset($data['bank_accounts'])) {
                $submitted_bank_ids = [];
                foreach ($data['bank_accounts'] as $bank) {
                    if (isset($bank['bank_id'])) {
                        app(ContactBankService::class)->update([
                            'id' => $bank['bank_id'],
                            'f_bank_code' => $bank['bank_code'],
                            'branch_office' => $bank['branch_office'],
                            'account_holder' => $bank['account_holder'],
                            'account_number' => $bank['account_number'],
                        ]);

                        $submitted_bank_ids[] = $bank['bank_id'];
                    } else {
                        app(ContactBankService::class)->createMany([
                            [
                                'f_contact_id' => $contact_id,
                                'f_bank_code' => $bank['bank_code'],
                                'branch_office' => $bank['branch_office'],
                                'account_holder' => $bank['account_holder'],
                                'account_number' => $bank['account_number'],
                            ],
                        ]);
                    }
                }

                $bank_ids_to_delete = array_diff($current_bank_ids, $submitted_bank_ids);
                foreach ($bank_ids_to_delete as $delete_id) {
                    app(ContactBankService::class)->delete($contact_id, $delete_id);
                }
            }

            $current_product = app(ContactProductService::class)->findAllBasedOnContactId($contact_id);
            $current_product_ids = [];
            foreach ($current_product as $row) {
                $current_product_ids[] = $row->id;
            }
            if (isset($data['products'])) {
                $submitted_product_ids = [];
                foreach ($data['products'] as $product) {
                    if (isset($product['product_id'])) {
                        app(ContactProductService::class)->update([
                            'id' => $product['product_id'],
                            'f_contact_id' => $contact_id,
                            'f_product_code' => $product['product'],
                            'price_per_qty' => $product['price'],
                        ]);

                        $submitted_product_ids[] = $product['product_id'];
                    } else {
                        app(ContactProductService::class)->createMany([
                            [
                                'f_contact_id' => $contact_id,
                                'f_product_code' => $product['product'],
                                'price_per_qty' => $product['price'],
                            ],
                        ]);
                    }
                }

                $product_ids_to_delete = array_diff($current_product_ids, $submitted_product_ids);
                foreach ($product_ids_to_delete as $delete_id) {
                    app(ContactProductService::class)->delete($contact_id, $delete_id);
                }
            }

            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Perubahan Kontak',
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                    'comparison' => [
                        'name' => [
                            'label' => 'Nama Kontak',
                            'before' => $old_data['header']->name,
                            'after' => $data['contact_name'] ?? null,
                        ],
                        'group_name' => [
                            'label' => 'Group Kontak',
                            'before' => $old_data['header']->name,
                            'after' => $data['group_name'] ?? null,
                        ],
                        'contact_type' => [
                            'label' => 'Tipe Kontak',
                            'before' => $old_data['header']->type,
                            'after' => $data['contact_type'] ?? null,
                        ],
                        'contact_status' => [
                            'label' => 'Status Kontak',
                            'before' => $old_data['header']->status,
                            'after' => $data['contact_status'] ?? null,
                        ],
                        'identity_type' => [
                            'label' => 'Jenis Identitas',
                            'before' => $old_data['header']->f_identity_type_code,
                            'after' => $data['identity_type'] ?? null,
                        ],
                        'identity_number' => [
                            'label' => 'Nomor Identitas',
                            'before' => $old_data['header']->identity_number,
                            'after' => $data['identity_number'] ?? null,
                        ],
                        'email' => [
                            'label' => 'Email',
                            'before' => $old_data['header']->email,
                            'after' => $data['email'] ?? null,
                        ],
                        'phone' => [
                            'label' => 'Telepon',
                            'before' => $old_data['header']->phone,
                            'after' => $data['phone'] ?? null,
                        ],
                        'sales_user' => [
                            'label' => 'Sales PIC',
                            'before' => $old_data['header']->f_sales_pic_id,
                            'after' => $data['sales_user'] ?? null,
                        ],
                        'company_name' => [
                            'label' => 'Nama Perusahaan',
                            'before' => $old_data['header']->company_name,
                            'after' => $data['company_name'] ?? null,
                        ],
                        'npwp' => [
                            'label' => 'NPWP Perusahaan',
                            'before' => $old_data['header']->company_npwp,
                            'after' => $data['npwp'] ?? null,
                        ],
                        'company_phone' => [
                            'label' => 'Telepon Perusahaan',
                            'before' => $old_data['header']->company_phone,
                            'after' => $data['company_phone'] ?? null,
                        ],
                        'company_fax' => [
                            'label' => 'Fax Perusahaan',
                            'before' => $old_data['header']->company_fax_no,
                            'after' => $data['company_fax'] ?? null,
                        ],
                        'billing_province' => [
                            'label' => 'Provinsi Penagihan',
                            'before' => $old_data['billing_address']->province,
                            'after' => $data['billing_province'] ?? null,
                        ],
                        'billing_city' => [
                            'label' => 'Kota Penagihan',
                            'before' => $old_data['billing_address']->city,
                            'after' => $data['billing_city'] ?? null,
                        ],
                        'billing_district' => [
                            'label' => 'Kecamatan Penagihan',
                            'before' => $old_data['billing_address']->district,
                            'after' => $data['billing_district'] ?? null,
                        ],
                        'billing_subdistrict' => [
                            'label' => 'Kelurahan Penagihan',
                            'before' => $old_data['billing_address']->subdistrict,
                            'after' => $data['billing_subdistrict'] ?? null,
                        ],
                        'billing_postal_code' => [
                            'label' => 'Kode Pos Penagihan',
                            'before' => $old_data['billing_address']->postal_code,
                            'after' => $data['billing_postal_code'] ?? null,
                        ],
                        'billing_address' => [
                            'label' => 'Alamat Penagihan',
                            'before' => $old_data['billing_address']->address,
                            'after' => $data['billing_address'] ?? null,
                        ],
                        'shipping_province' => [
                            'label' => 'Provinsi Pengiriman',
                            'before' => $old_data['shipping_address']->province,
                            'after' => $data['shipping_province'] ?? null,
                        ],
                        'shipping_city' => [
                            'label' => 'Kota Pengiriman',
                            'before' => $old_data['shipping_address']->city,
                            'after' => $data['shipping_city'] ?? null,
                        ],
                        'shipping_district' => [
                            'label' => 'Kecamatan Pengiriman',
                            'before' => $old_data['shipping_address']->district,
                            'after' => $data['shipping_district'] ?? null,
                        ],
                        'shipping_subdistrict' => [
                            'label' => 'Kelurahan Pengiriman',
                            'before' => $old_data['shipping_address']->subdistrict,
                            'after' => $data['shipping_subdistrict'] ?? null,
                        ],
                        'shipping_address' => [
                            'label' => 'Alamat Pengiriman',
                            'before' => $old_data['shipping_address']->address,
                            'after' => $data['shipping_address'] ?? null,
                        ],
                        'shipping_postal_code' => [
                            'label' => 'Kode Pos Pengiriman',
                            'before' => $old_data['shipping_address']->postal_code,
                            'after' => $data['shipping_postal_code'] ?? null,
                        ],
                        'payable_account' => [
                            'label' => 'Akun Hutang',
                            'before' => $old_data['header']->f_coa_account_payable,
                            'after' => $data['f_coa_account_payable'] ?? null,
                        ],
                        'receivable_account' => [
                            'label' => 'Akun Piutang',
                            'before' => $old_data['header']->f_coa_account_receivable,
                            'after' => $data['f_coa_account_receivable'] ?? null,
                        ],
                        'payment_term' => [
                            'label' => 'Syarat Pembayaran',
                            'before' => $old_data['header']->f_payment_terms_id,
                            'after' => $data['payment_term'] ?? null,
                        ],

                    ],
                ]),
            ];
            app(ContactAuditLogService::class)->create($log_data, $contact_id);
        });
    }
}
