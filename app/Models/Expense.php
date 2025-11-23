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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'is_shared' => 'boolean',
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
}
