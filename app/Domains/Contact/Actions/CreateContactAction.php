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

class CreateContactAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $contact_id = app(ContactService::class)->create($data);

            app(ContactAddressService::class)->createMany([
                [
                    'f_contact_id' => $contact_id,
                    'type' => ContactAddressTypes::BILLING,
                    'province' => $data['billing_province'] ?? null,
                    'city' => $data['billing_city'] ?? null,
                    'district' => $data['billing_district'] ?? null,
                    'subdistrict' => $data['billing_subdistrict'] ?? null,
                    'address' => $data['billing_address'],
                    'postal_code' => $data['billing_postal_code'] ?? null,
                    'area' => $data['billing_area'] ?? null,
                    'maps_url' => $data['billing_maps_url'] ?? null,
                ],
                [
                    'f_contact_id' => $contact_id,
                    'type' => ContactAddressTypes::SHIPPING,
                    'province' => $data['shipping_province'] ?? null,
                    'city' => $data['shipping_city'] ?? null,
                    'district' => $data['shipping_district'] ?? null,
                    'subdistrict' => $data['shipping_subdistrict'] ?? null,
                    'address' => $data['shipping_address'],
                    'postal_code' => $data['shipping_postal_code'] ?? null,
                    'area' => $data['shipping_area'] ?? null,
                    'maps_url' => $data['shipping_maps_url'] ?? null,
                ],
            ]);

            $contactBank = [];
            if (isset($data['bank_accounts'])) {
                foreach ($data['bank_accounts'] as $bank) {
                    $contactBank[] = [
                        'f_contact_id' => $contact_id,
                        'f_bank_code' => $bank['bank_code'],
                        'branch_office' => $bank['branch_office'],
                        'account_holder' => $bank['account_holder'],
                        'account_number' => $bank['account_number'],
                    ];
                }
            }
            if ($contactBank != null) {
                app(ContactBankService::class)->createMany($contactBank);
            }

            $contactProduct = [];
            if (isset($data['products'])) {
                foreach ($data['products'] as $product) {
                    $contactProduct[] = [
                        'f_contact_id' => $contact_id,
                        'f_product_code' => $product['product'],
                        'price_per_qty' => $product['price'],
                    ];
                }
            }
            if ($contactProduct != null) {
                app(ContactProductService::class)->createMany($contactProduct);
            }

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => 'Pembentukan Kontak',
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
            ];
            app(ContactAuditLogService::class)->create($log_data, $contact_id);
        });
    }
}
