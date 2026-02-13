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
        'amount',
        'description',
        'status',
        'rejection_reason',
        'created_by',
        'f_budget_category_id',
        'reviewed_by',
        'reviewed_at',
        'company_code',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
