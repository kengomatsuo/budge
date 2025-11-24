<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\ExpenseFile;
use App\Models\SharedExpenseMember;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            User::factory()->count(3)->create();

            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);

            // reload users
            $users = User::all();
        }

        $faker = fake();
        $now = Carbon::now();

        $budgetsPerUser = 30;
        // increase expenses to simulate more realistic activity
        $expensesPerUser = 800;

        foreach ($users as $user) {
            $categories = $user->categories()->get();

            if ($categories->isEmpty()) {
                $categories = collect([$user->categories()->create(['name' => 'Misc'])]);
            }

            // Create budgets: avoid overlaps per user+category
            // For each category, create a sequence of non-overlapping budgets
            foreach ($categories as $cat) {
                $count = max(1, (int) floor($budgetsPerUser / max(1, $categories->count())) + $faker->numberBetween(-1, 2));
                $existingEnd = null;
                for ($b = 0; $b < $count; $b++) {
                    $period = Arr::random(['daily', 'weekly', 'monthly']);
                    // choose a start that comes after existingEnd (or random in past if none)
                    if ($existingEnd) {
                        $startDate = Carbon::parse($existingEnd)->addDay()->toDateString();
                    } else {
                        $start = $faker->dateTimeBetween('-2 years', '+6 months');
                        $startDate = Carbon::instance($start)->toDateString();
                    }

                    // duration in months for budget (0 means open-ended sometimes)
                    $durationMonths = $faker->numberBetween(0, 6);
                    $endDate = $durationMonths > 0 ? Carbon::parse($startDate)->addMonths($durationMonths)->toDateString() : null;

                    // pick currency distribution and compute amount with realistic ranges per currency
                    $currency = $this->pickCurrency($faker);
                    $amount = $this->randomAmountForCurrency($faker, $currency, true);

                    // If open-ended already exists for this category+user, skip creating another open-ended
                    if (is_null($endDate)) {
                        $hasOpen = Budget::where('user_id', $user->id)->where('category_id', $cat->id)->whereNull('end_date')->exists();
                        if ($hasOpen) {
                            // shift to a short fixed budget instead
                            $endDate = Carbon::parse($startDate)->addMonths($faker->numberBetween(1, 3))->toDateString();
                        }
                    }

                    // Ensure no overlap with existing budgets for this user+category
                    $attempts = 0;
                    while ($this->budgetOverlapExists($user->id, $cat->id, $startDate, $endDate) && $attempts < 5) {
                        // push start forward by a few days/months
                        $startDate = Carbon::parse($startDate)->addDays($faker->numberBetween(7, 60))->toDateString();
                        $endDate = $endDate ? Carbon::parse($startDate)->addMonths($durationMonths)->toDateString() : null;
                        $attempts++;
                    }

                    // If still overlapping after attempts, skip
                    if ($this->budgetOverlapExists($user->id, $cat->id, $startDate, $endDate)) {
                        continue;
                    }

                    Budget::create([
                        'user_id' => $user->id,
                        'category_id' => $cat->id,
                        'amount' => $amount,
                        'currency' => $currency,
                        'period_type' => $period,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]);

                    $existingEnd = $endDate ?? Carbon::parse($startDate)->addYears(1)->toDateString();
                }
            }

            // Create many expenses with varied, more human-like distribution
            $createdExpenses = [];
            for ($i = 0; $i < $expensesPerUser; $i++) {
                $cat = $categories->random();

                // Amount distribution: many small, some medium, occasional large
                    // pick a currency and generate a realistic amount for it
                    $currency = $this->pickCurrency($faker);
                    $amount = $this->randomAmountForCurrency($faker, $currency, false);

                $title = $faker->sentence($faker->numberBetween(2, 6));
                if ($faker->boolean(2)) {
                    $title = Str::limit($faker->sentence($faker->numberBetween(6, 12)), 120);
                }

                // Date distribution: bias towards recent activity but spread across ~24 months
                $p = $faker->numberBetween(1, 100);
                if ($p <= 50) {
                    // within last 30 days
                    $date = $faker->dateTimeBetween('-30 days', 'now');
                } elseif ($p <= 85) {
                    // within last 6 months
                    $date = $faker->dateTimeBetween('-6 months', 'now');
                } else {
                    // older up to 24 months
                    $date = $faker->dateTimeBetween('-24 months', '-6 months');
                }

                $expense = Expense::create([
                    'user_id' => $user->id,
                    'category_id' => $cat->id,
                    'title' => $title,
                    'description' => $faker->paragraphs($faker->numberBetween(0, 4), true),
                        'amount' => $amount,
                        'currency' => $currency,
                    'expense_date' => Carbon::instance($date)->format('Y-m-d'),
                    'payment_method' => Arr::random(['cash', 'debit_card', 'credit_card', 'e_wallet']),
                    'is_shared' => false,
                ]);

                $createdExpenses[] = $expense;
            }

            // Attach files to ~25% of expenses (1-3 files each)
            foreach ($createdExpenses as $exp) {
                if ($faker->boolean(25)) {
                    $fileCount = $faker->numberBetween(1, 3);
                    for ($f = 0; $f < $fileCount; $f++) {
                        ExpenseFile::create([
                            'expense_id' => $exp->id,
                            'file_name' => $faker->lexify('file_?????.') . Arr::random(['jpg', 'png', 'pdf']),
                            'file_path' => 'storage/seeded/' . $faker->uuid . '/' . $faker->word . '.' . Arr::random(['jpg', 'png', 'pdf']),
                            'file_type' => Arr::random(['image/jpeg', 'image/png', 'application/pdf']),
                            'file_size' => $faker->numberBetween(100, 2000000),
                        ]);
                    }

                }
            }

            // Create shared expenses for ~15% of the created expenses
            $otherUsers = User::where('id', '!=', $user->id)->get();
            if ($otherUsers->isNotEmpty()) {
                foreach ($faker->randomElements($createdExpenses, (int) floor(count($createdExpenses) * 0.15)) as $sharedExpense) {
                    // mark expense as shared
                    $sharedExpense->update(['is_shared' => true]);

                    // choose 1-4 other participants
                    $participants = $otherUsers->random(min($otherUsers->count(), $faker->numberBetween(1, 4)));
                    if ($participants instanceof User) {
                        $participants = collect([$participants]);
                    }

                    $participants = $participants->values();
                    $allParticipants = $participants->push($user);
                    $totalParticipants = $allParticipants->count();
                    $splitAmount = round($sharedExpense->amount / $totalParticipants, 2);

                    // owner
                    SharedExpenseMember::create([
                        'expense_id' => $sharedExpense->id,
                        'user_id' => $user->id,
                        'split_amount' => $splitAmount,
                        'is_paid' => $faker->boolean(50),
                    ]);

                    foreach ($participants as $p) {
                        SharedExpenseMember::create([
                            'expense_id' => $sharedExpense->id,
                            'user_id' => $p->id,
                            'split_amount' => $splitAmount,
                            'is_paid' => $faker->boolean(30),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Check whether a budget for given user/category overlaps an existing budget.
     */
    private function budgetOverlapExists($userId, $categoryId, $start, $end = null)
    {
        $newStart = $start;
        $newEnd = $end ?? Carbon::now()->copy()->addYears(1000)->toDateString();

        return Budget::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where(function($q) use ($newStart, $newEnd) {
                $q->where('start_date', '<=', $newEnd)
                  ->where(function($q2) use ($newStart) {
                      $q2->whereNull('end_date')
                         ->orWhere('end_date', '>=', $newStart);
                  });
            })->exists();
    }

    private function pickCurrency($faker)
    {
        // Weighted distribution: mostly IDR, some USD/EUR/JPY
        $r = $faker->numberBetween(1, 100);
        if ($r <= 80) return 'IDR';
        if ($r <= 90) return 'USD';
        if ($r <= 95) return 'EUR';
        return 'JPY';
    }

    private function randomAmountForCurrency($faker, $currency, $isBudget = false)
    {
        $currency = strtoupper($currency ?: 'IDR');

        // Budgets are typically higher than expenses
        if ($currency === 'IDR') {
            if ($isBudget) {
                return $faker->randomFloat(2, 100000, 20000000); // 100k to 20M IDR
            }
            // expense
            $r = $faker->numberBetween(1, 100);
            if ($r <= 60) return $faker->randomFloat(2, 5000, 50000);
            if ($r <= 90) return $faker->randomFloat(2, 50000, 500000);
            return $faker->randomFloat(2, 500000, 5000000);
        }

        if ($currency === 'USD') {
            if ($isBudget) {
                return $faker->randomFloat(2, 100, 5000); // $100 - $5k
            }
            $r = $faker->numberBetween(1, 100);
            if ($r <= 60) return $faker->randomFloat(2, 1, 20);
            if ($r <= 90) return $faker->randomFloat(2, 20, 200);
            return $faker->randomFloat(2, 200, 5000);
        }

        if ($currency === 'EUR') {
            if ($isBudget) {
                return $faker->randomFloat(2, 100, 4000);
            }
            $r = $faker->numberBetween(1, 100);
            if ($r <= 60) return $faker->randomFloat(2, 1, 20);
            if ($r <= 90) return $faker->randomFloat(2, 20, 200);
            return $faker->randomFloat(2, 200, 4000);
        }

        // JPY
        if ($currency === 'JPY') {
            if ($isBudget) {
                return $faker->randomFloat(2, 10000, 500000); // 10k - 500k JPY
            }
            $r = $faker->numberBetween(1, 100);
            if ($r <= 60) return $faker->randomFloat(2, 100, 2000);
            if ($r <= 90) return $faker->randomFloat(2, 2000, 20000);
            return $faker->randomFloat(2, 20000, 500000);
        }

        // default fallback
        return $faker->randomFloat(2, 10, 1000);
    }
}
