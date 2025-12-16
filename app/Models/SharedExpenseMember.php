<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedExpenseMember extends Model
{
    protected $fillable = [
        'expense_id',
        'user_id',
        'split_amount',
        'assigned_item_ids',
        'is_paid',
    ];

    protected $casts = [
        'split_amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'assigned_item_ids' => 'array',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedItems()
    {
        return ExpenseItem::whereIn('id', $this->assigned_item_ids ?? [])->get();
    }
}
