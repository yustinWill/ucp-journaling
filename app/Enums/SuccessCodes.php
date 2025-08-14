<?php

namespace App\Enums;

enum SuccessCodes: string
{
    public const OK = 200;

    case GENERAL_SUCCESS = 'GENERAL_SUCCESS';
    case LOGOUT_SUCCESS = 'LOGOUT_SUCCESS';
    case CREATE_TAG_SUCCESS = 'CREATE_TAG_SUCCESS';
    case CREATE_USER_SUCCESS = 'CREATE_USER_SUCCESS';
    case UPDATE_USER_SUCCESS = 'UPDATE_USER_SUCCESS';
    case CREATE_UNIT_SUCCESS = 'CREATE_UNIT_SUCCESS';
    case UPDATE_UNIT_SUCCESS = 'UPDATE_UNIT_SUCCESS';
    case CREATE_TAX_SUCCESS = 'CREATE_TAX_SUCCESS';
    case UPDATE_TAX_SUCCESS = 'UPDATE_TAX_SUCCESS';
    case CREATE_CATEGORY_SUCCESS = 'CREATE_CATEGORY_SUCCESS';
    case UPDATE_CATEGORY_SUCCESS = 'UPDATE_CATEGORY_SUCCESS';
    case CREATE_COA_SUCCESS = 'CREATE_COA_SUCCESS';
    case UPDATE_COA_SUCCESS = 'UPDATE_COA_SUCCESS';
    case CREATE_ROLE_SUCCESS = 'CREATE_ROLE_SUCCESS';
    case UPDATE_ROLE_SUCCESS = 'UPDATE_ROLE_SUCCESS';
    case CREATE_CONTACT_SUCCESS = 'CREATE_CONTACT_SUCCESS';
    case UPDATE_CONTACT_SUCCESS = 'UPDATE_CONTACT_SUCCESS';
    case CREATE_ACCOUNTING_JOURNAL_SUCCESS = 'CREATE_ACCOUNTING_JOURNAL_SUCCESS';
    case UPDATE_ACCOUNTING_JOURNAL_SUCCESS = 'UPDATE_ACCOUNTING_JOURNAL_SUCCESS';
    case CREATE_PRODUCT_SUCCESS = 'CREATE_PRODUCT_SUCCESS';
    case UPDATE_PRODUCT_SUCCESS = 'UPDATE_PRODUCT_SUCCESS';

    public function message(): string
    {
        return match ($this) {
            self::LOGOUT_SUCCESS => 'Anda berhasil logout.',
            self::CREATE_TAG_SUCCESS => 'Tag berhasil dibuat.',
            self::CREATE_USER_SUCCESS => 'User berhasil dibuat.',
            self::UPDATE_USER_SUCCESS => 'User berhasil diperbarui.',
            self::CREATE_UNIT_SUCCESS => 'Unit berhasil dibuat.',
            self::UPDATE_UNIT_SUCCESS => 'Unit berhasil diubah.',
            self::CREATE_TAX_SUCCESS => 'Pajak berhasil dibuat.',
            self::UPDATE_TAX_SUCCESS => 'Pajak berhasil diubah.',
            self::CREATE_CATEGORY_SUCCESS => 'Kategori berhasil dibuat.',
            self::UPDATE_CATEGORY_SUCCESS => 'Kategori berhasil diubah.',
            self::GENERAL_SUCCESS => 'Berhasil',
            self::CREATE_COA_SUCCESS => 'COA berhasil dibuat.',
            self::UPDATE_COA_SUCCESS => 'COA berhasil diperbarui.',
            self::CREATE_ROLE_SUCCESS => 'Role berhasil dibuat.',
            self::UPDATE_ROLE_SUCCESS => 'Role berhasil diperbarui.',
            self::CREATE_CONTACT_SUCCESS => 'Kontak berhasil dibuat.',
            self::UPDATE_CONTACT_SUCCESS => 'Kontak berhasil diperbarui.',
            self::CREATE_ACCOUNTING_JOURNAL_SUCCESS => 'Jurnal Akuntansi berhasil dibuat.',
            self::UPDATE_ACCOUNTING_JOURNAL_SUCCESS => 'Jurnal Akuntansi berhasil diperbarui.',
            self::CREATE_PRODUCT_SUCCESS => 'Produk berhasil dibuat.',
            self::UPDATE_PRODUCT_SUCCESS => 'Produk berhasil diperbarui.',
        };
    }

    public function httpStatus(): int
    {
        return match ($this) {
            self::LOGOUT_SUCCESS => self::OK,
            self::CREATE_TAG_SUCCESS => self::OK,
            self::CREATE_USER_SUCCESS => self::OK,
            self::UPDATE_USER_SUCCESS => self::OK,
            self::CREATE_UNIT_SUCCESS => self::OK,
            self::UPDATE_UNIT_SUCCESS => self::OK,
            self::CREATE_TAX_SUCCESS => self::OK,
            self::UPDATE_TAX_SUCCESS => self::OK,
            self::CREATE_CATEGORY_SUCCESS => self::OK,
            self::UPDATE_CATEGORY_SUCCESS => self::OK,
            self::GENERAL_SUCCESS => self::OK,
            self::CREATE_COA_SUCCESS => self::OK,
            self::UPDATE_COA_SUCCESS => self::OK,
            self::CREATE_ROLE_SUCCESS => self::OK,
            self::UPDATE_ROLE_SUCCESS => self::OK,
            self::CREATE_CONTACT_SUCCESS => self::OK,
            self::UPDATE_CONTACT_SUCCESS => self::OK,
            self::CREATE_ACCOUNTING_JOURNAL_SUCCESS => self::OK,
            self::UPDATE_ACCOUNTING_JOURNAL_SUCCESS => self::OK,
            self::CREATE_PRODUCT_SUCCESS => self::OK,
            self::UPDATE_PRODUCT_SUCCESS => self::OK,
        };
    }

    public function code(): string
    {
        return $this->value;
    }
}
