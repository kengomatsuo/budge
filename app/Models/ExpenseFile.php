<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseFile extends Model
{
    protected $fillable = [
        'expense_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
