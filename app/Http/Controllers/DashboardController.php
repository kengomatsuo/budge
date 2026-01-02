<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $expensesMonth = Expense::with('sharedMembers')
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('sharedMembers', function($sq) use ($user) {
                      $sq->where('user_id', $user->id);
                  });
            })
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->get();

        $totalExpensesMonth = $expensesMonth->sum(function($e) use ($user) {
            return convert_currency($e->my_share, $e->currency ?? 'IDR', $user->preferred_currency);
        });

        $budgets = Budget::where('user_id', $user->id)
            ->where('start_date', '<=', $now)
            ->where(function($query) use ($now) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $now);
            })->get();

        // Sum budgets converted to user's preferred currency
        $totalBudget = $budgets->sum(function($b) use ($user) {
            return convert_currency($b->amount, $b->currency ?? 'IDR', $user->preferred_currency);
        });

        $budgetRemaining = $totalBudget - $totalExpensesMonth;

        $expensesToday = Expense::with('sharedMembers')
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('sharedMembers', function($sq) use ($user) {
                      $sq->where('user_id', $user->id);
                  });
            })
            ->whereDate('expense_date', $now->toDateString())
            ->get()
            ->sum(function($e) use ($user) {
                return convert_currency($e->my_share, $e->currency ?? 'IDR', $user->preferred_currency);
            });

        // Calculate total amount user owes to others (unpaid shared expenses)
        // Group by expense owner to show breakdown
        $unpaidDebts = DB::table('shared_expense_members')
            ->join('expenses', 'shared_expense_members.expense_id', '=', 'expenses.id')
            ->join('users', 'expenses.user_id', '=', 'users.id')
            ->where('shared_expense_members.user_id', $user->id)
            ->where('shared_expense_members.is_paid', false)
            ->select(
                'users.id as owner_id',
                'users.name as owner_name',
                DB::raw('SUM(shared_expense_members.split_amount) as total_owed'),
                'expenses.currency'
            )
            ->groupBy('users.id', 'users.name', 'expenses.currency')
            ->get()
            ->groupBy('owner_id')
            ->map(function($debts) use ($user) {
                $ownerName = $debts->first()->owner_name;
                $totalOwed = $debts->sum(function($debt) use ($user) {
                    return convert_currency($debt->total_owed, $debt->currency ?? 'IDR', $user->preferred_currency);
                });
                return (object) [
                    'owner_name' => $ownerName,
                    'total_owed' => $totalOwed
                ];
            })
            ->sortByDesc('total_owed')
            ->values();

        $iOweOthers = $unpaidDebts->sum('total_owed');

        // Build spending by category converted to user's preferred currency
        $rawByCategory = Expense::with(['category', 'sharedMembers'])
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('sharedMembers', function($sq) use ($user) {
                      $sq->where('user_id', $user->id);
                  });
            })
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function($item) {
                return $item->category->name ?? 'Unknown';
            })
            ->map(function($items, $name) use ($user) {
                $first = $items->first();
                $total = $items->sum(function($e) use ($user) {
                    return convert_currency($e->my_share, $e->currency ?? 'IDR', $user->preferred_currency);
                });
                return (object) [
                    'name' => $name,
                    'color' => $first->category->color ?? null,
                    'total' => $total,
                ];
            })->values();

        $spendingByCategory = $rawByCategory;

        // Build 7-day trend, always showing last 7 days
        $trendStart = $now->copy()->subDays(6)->startOfDay();
        $trendEnd = $now->copy()->endOfDay();
        $rawExpensesTrend = Expense::with('sharedMembers')
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('sharedMembers', function($sq) use ($user) {
                      $sq->where('user_id', $user->id);
                  });
            })
            ->whereBetween('expense_date', [$trendStart, $trendEnd])
            ->get()
            ->groupBy(function($e) {
                return Carbon::parse($e->expense_date)->format('Y-m-d');
            });

        $spendingTrend = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = $trendStart->copy()->addDays($i)->format('Y-m-d');
            $total = 0;
            if (isset($rawExpensesTrend[$date])) {
                $total = $rawExpensesTrend[$date]->sum(function($e) use ($user) {
                    return convert_currency($e->my_share, $e->currency ?? 'IDR', $user->preferred_currency);
                });
            }
            $spendingTrend->push((object) [
                'date' => $date,
                'total' => $total,
            ]);
        }

        $recentExpenses = Expense::with(['category', 'sharedMembers'])
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('sharedMembers', function($sq) use ($user) {
                      $sq->where('user_id', $user->id);
                  });
            })
            ->orderBy('expense_date', 'desc')
            ->take(10)
            ->get();

        $budgetStatus = Budget::with('category')
            ->where('user_id', $user->id)
            ->where('start_date', '<=', $now)
            ->where(function($query) use ($now) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $now);
            })
            ->get()
            ->map(function($budget) use ($startOfMonth, $endOfMonth) {
                $expenses = Expense::where('user_id', $budget->user_id)
                    ->where('category_id', $budget->category_id)
                    ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
                    ->get();

                $spent = $expenses->sum(function($e) use ($budget) {
                    return convert_currency($e->amount, $e->currency ?? 'IDR', $budget->currency ?? 'IDR');
                });

                $budget->spent = $spent;
                $budget->percentage = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;
                $budget->status = $budget->percentage >= 100 ? 'over_budget' :
                                 ($budget->percentage >= 80 ? 'warning' : 'on_track');

                return $budget;
            });

        return view('dashboard', compact(
            'totalExpensesMonth',
            'totalBudget',
            'budgetRemaining',
            'expensesToday',
            'iOweOthers',
            'unpaidDebts',
            'spendingByCategory',
            'spendingTrend',
            'recentExpenses',
            'budgetStatus'
        ));
    }
}

