<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reimbursement extends Model
{
    use SoftDeletes;

    protected $table = 't_reimbursements';

    protected $fillable = [
        'transaction_code',
        'reimbursement_name',
        'reimbursement_date',
        'amount',
        'description',
        'attachment',
        'status',
        'rejection_reason',
        'created_by',
        'f_budget_category_id',
        'payment_method_type',
        'transfer_bank',
        'f_kas_besar_id',
        'reviewed_by',
        'reviewed_at',
        'company_code',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reimbursement_date' => 'date',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** "Kas / Tunai · KB-…", "Transfer BCA", or null for approvals made before the source was recorded. */
    public function paymentSourceLabel(): ?string
    {
        return match ($this->payment_method_type) {
            'kas_tunai' => 'Kas / Tunai'.($this->kas_besar_number ? ' · '.$this->kas_besar_number : ''),
            'transfer' => trim('Transfer '.$this->transfer_bank),
            default => null,
        };
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
