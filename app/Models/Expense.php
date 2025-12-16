<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'amount',
        'currency',
        'expense_date',
        'payment_method',
        'is_shared',
        'split_type',
        'ocr_data',
        'subtotal',
        'tax_amount',
        'service_charge',
        'tip_amount',
        'discount_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'tip_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'expense_date' => 'date',
        'is_shared' => 'boolean',
        'ocr_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function files()
    {
        return $this->hasMany(ExpenseFile::class);
    }

    public function sharedMembers()
    {
        return $this->hasMany(SharedExpenseMember::class);
    }

    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }

    public function getMyShareAttribute()
    {
        if (!$this->is_shared) {
            return $this->amount;
        }

        $mySplit = $this->sharedMembers->where('user_id', auth()->id())->first();
        return $mySplit ? $mySplit->split_amount : $this->amount;
    }
}
