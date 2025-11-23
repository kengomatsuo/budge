<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'preferred_language',
        'preferred_currency',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $defaultCategories = [
                ['name' => 'Food', 'icon' => '🍔', 'color' => '#FF6B6B', 'is_default' => true],
                ['name' => 'Transportation', 'icon' => '🚗', 'color' => '#4ECDC4', 'is_default' => true],
                ['name' => 'Shopping', 'icon' => '🛍️', 'color' => '#45B7D1', 'is_default' => true],
                ['name' => 'Entertainment', 'icon' => '🎬', 'color' => '#FFA07A', 'is_default' => true],
                ['name' => 'Healthcare', 'icon' => '🏥', 'color' => '#98D8C8', 'is_default' => true],
                ['name' => 'Bills & Utilities', 'icon' => '💡', 'color' => '#F7DC6F', 'is_default' => true],
                ['name' => 'Education', 'icon' => '📚', 'color' => '#BB8FCE', 'is_default' => true],
                ['name' => 'Housing', 'icon' => '🏠', 'color' => '#85C1E2', 'is_default' => true],
                ['name' => 'Personal Care', 'icon' => '💅', 'color' => '#F8B4D9', 'is_default' => true],
                ['name' => 'Other', 'icon' => '📝', 'color' => '#95A5A6', 'is_default' => true],
            ];

            foreach ($defaultCategories as $category) {
                $user->categories()->create($category);
            }
        });
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function sharedExpenses()
    {
        return $this->hasMany(SharedExpenseMember::class);
    }
}
