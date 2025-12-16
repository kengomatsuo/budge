<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    protected $fillable = [
        'expense_id',
        'name',
        'quantity',
        'unit_price',
        'total_price',
        'tax_rate',
        'tax_amount',
        'tax_category',
        'line_number',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function assignedToMembers()
    {
        return $this->belongsToMany(
            SharedExpenseMember::class,
            'assigned_item_ids'
        );
    }
}
