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
    case CREATE_WEARHOUSE_SUCCESS = 'CREATE_WEARHOUSE_SUCCESS';
    case UPDATE_WEARHOUSE_SUCCESS = 'UPDATE_WEARHOUSE_SUCCESS';
    case CREATE_STOCK_ADJUSTMENT_SUCCESS = 'CREATE_STOCK_ADJUSTMENT_SUCCESS';
    case UPDATE_STOCK_ADJUSTMENT_SUCCESS = 'UPDATE_STOCK_ADJUSTMENT_SUCCESS';
    case CREATE_PURCHASE_ORDER_SUCCESS = 'CREATE_PURCHASE_ORDER_SUCCESS';
    case UPDATE_PURCHASE_ORDER_SUCCESS = 'UPDATE_PURCHASE_ORDER_SUCCESS';
    case CREATE_PURCHASE_DELIVERY_SUCCESS = 'CREATE_PURCHASE_DELIVERY_SUCCESS';
    case UPDATE_PURCHASE_DELIVERY_SUCCESS = 'UPDATE_PURCHASE_DELIVERY_SUCCESS';
    case CREATE_PURCHASE_INVOICE_SUCCESS = 'CREATE_PURCHASE_INVOICE_SUCCESS';
    case UPDATE_PURCHASE_INVOICE_SUCCESS = 'UPDATE_PURCHASE_INVOICE_SUCCESS';
    case CREATE_STOCK_OPNAME_SUCCESS = 'CREATE_STOCK_OPNAME_SUCCESS';
    case UPDATE_STOCK_OPNAME_SUCCESS = 'UPDATE_STOCK_OPNAME_SUCCESS';
    case CREATE_SALES_ORDER_SUCCESS = 'CREATE_SALES_ORDER_SUCCESS';
    case UPDATE_SALES_ORDER_SUCCESS = 'UPDATE_SALES_ORDER_SUCCESS';
    case CREATE_SALES_DELIVERY_SUCCESS = 'CREATE_SALES_DELIVERY_SUCCESS';
    case UPDATE_SALES_DELIVERY_SUCCESS = 'UPDATE_SALES_DELIVERY_SUCCESS';
    case CREATE_PURCHASE_RETURN_SUCCESS = 'CREATE_PURCHASE_RETURN_SUCCESS';
    case UPDATE_SALES_RETURN_SUCCESS = 'UPDATE_SALES_RETURN_SUCCESS';
    case CREATE_SALES_INVOICE_SUCCESS = 'CREATE_SALES_INVOICE_SUCCESS';
    case UPDATE_SALES_INVOICE_SUCCESS = 'UPDATE_SALES_INVOICE_SUCCESS';
    case CREATE_STOCK_TRANSFER_SUCCESS = 'CREATE_STOCK_TRANSFER_SUCCESS';
    case CREATE_EXPENSE_SUCCESS = 'CREATE_EXPENSE_SUCCESS';

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
            self::CREATE_WEARHOUSE_SUCCESS => 'Gudang berhasil dibuat.',
            self::UPDATE_WEARHOUSE_SUCCESS => 'Gudang berhasil diperbarui.',
            self::CREATE_STOCK_ADJUSTMENT_SUCCESS => 'Penyesuaian Stok berhasil dibuat.',
            self::UPDATE_STOCK_ADJUSTMENT_SUCCESS => 'Penyesuaian Stok berhasil diperbarui.',
            self::CREATE_PURCHASE_ORDER_SUCCESS => 'Purchase Order berhasil dibuat.',
            self::UPDATE_PURCHASE_ORDER_SUCCESS => 'Purchase Order berhasil diperbarui.',
            self::CREATE_PURCHASE_DELIVERY_SUCCESS => 'Purchase Delivery berhasil dibuat.',
            self::UPDATE_PURCHASE_DELIVERY_SUCCESS => 'Purchase Delivery berhasil diperbarui.',
            self::CREATE_STOCK_OPNAME_SUCCESS => 'Stock Opname berhasil dibuat.',
            self::UPDATE_STOCK_OPNAME_SUCCESS => 'Stock Opname berhasil diperbarui.',
            self::CREATE_PURCHASE_INVOICE_SUCCESS => 'Purchase Invoice berhasil dibuat.',
            self::UPDATE_PURCHASE_INVOICE_SUCCESS => 'Purchase Invoice berhasil diperbarui.',
            self::CREATE_SALES_ORDER_SUCCESS => 'Sales Order berhasil dibuat.',
            self::UPDATE_SALES_ORDER_SUCCESS => 'Sales Order berhasil diperbarui.',
            self::CREATE_SALES_DELIVERY_SUCCESS => 'Sales Delivery berhasil dibuat.',
            self::UPDATE_SALES_DELIVERY_SUCCESS => 'Sales Delivery berhasil diperbarui.',
            self::CREATE_PURCHASE_RETURN_SUCCESS => 'Purchase Return berhasil dibuat.',
            self::UPDATE_SALES_RETURN_SUCCESS => 'Sales Return berhasil diperbarui.',
            self::CREATE_SALES_INVOICE_SUCCESS => 'Sales Invoice berhasil dibuat.',
            self::UPDATE_SALES_INVOICE_SUCCESS => 'Sales Invoice berhasil diperbarui.',
            self::CREATE_STOCK_TRANSFER_SUCCESS => 'Stock Transfer berhasil dibuat.',
            self::CREATE_EXPENSE_SUCCESS => 'Expense berhasil dibuat.',
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
            self::CREATE_WEARHOUSE_SUCCESS => self::OK,
            self::UPDATE_WEARHOUSE_SUCCESS => self::OK,
            self::CREATE_STOCK_ADJUSTMENT_SUCCESS => self::OK,
            self::UPDATE_STOCK_ADJUSTMENT_SUCCESS => self::OK,
            self::CREATE_PURCHASE_ORDER_SUCCESS => self::OK,
            self::UPDATE_PURCHASE_ORDER_SUCCESS => self::OK,
            self::CREATE_PURCHASE_DELIVERY_SUCCESS => self::OK,
            self::UPDATE_PURCHASE_DELIVERY_SUCCESS => self::OK,
            self::CREATE_STOCK_OPNAME_SUCCESS => self::OK,
            self::UPDATE_STOCK_OPNAME_SUCCESS => self::OK,
            self::CREATE_PURCHASE_INVOICE_SUCCESS => self::OK,
            self::UPDATE_PURCHASE_INVOICE_SUCCESS => self::OK,
            self::CREATE_SALES_ORDER_SUCCESS => self::OK,
            self::UPDATE_SALES_ORDER_SUCCESS => self::OK,
            self::CREATE_SALES_DELIVERY_SUCCESS => self::OK,
            self::UPDATE_SALES_DELIVERY_SUCCESS => self::OK,
            self::CREATE_PURCHASE_RETURN_SUCCESS => self::OK,
            self::UPDATE_SALES_RETURN_SUCCESS => self::OK,
            self::CREATE_SALES_INVOICE_SUCCESS => self::OK,
            self::UPDATE_SALES_INVOICE_SUCCESS => self::OK,
            self::CREATE_STOCK_TRANSFER_SUCCESS => self::OK,
            self::CREATE_EXPENSE_SUCCESS => self::OK,
        };
    }

    public function code(): string
    {
        return $this->value;
    }
}
