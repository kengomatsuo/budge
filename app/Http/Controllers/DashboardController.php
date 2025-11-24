<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $totalExpensesMonth = Expense::where('user_id', $user->id)
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalBudget = Budget::where('user_id', $user->id)
            ->where('start_date', '<=', $now)
            ->where(function($query) use ($now) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $now);
            })
            ->sum('amount');

        $budgetRemaining = $totalBudget - $totalExpensesMonth;

        $expensesToday = Expense::where('user_id', $user->id)
            ->whereDate('expense_date', $now->toDateString())
            ->count();

        $spendingByCategory = Expense::select('categories.name', 'categories.color', DB::raw('SUM(expenses.amount) as total'))
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->where('expenses.user_id', $user->id)
            ->whereBetween('expenses.expense_date', [$startOfMonth, $endOfMonth])
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->get();

        // Build 7-day trend, always showing last 7 days
        $trendStart = $now->copy()->subDays(6)->startOfDay();
        $trendEnd = $now->copy()->endOfDay();
        $rawTrend = Expense::selectRaw('DATE(expense_date) as date, SUM(amount) as total')
            ->where('user_id', $user->id)
            ->whereBetween('expense_date', [$trendStart, $trendEnd])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $spendingTrend = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = $trendStart->copy()->addDays($i)->format('Y-m-d');
            $spendingTrend->push((object) [
                'date' => $date,
                'total' => isset($rawTrend[$date]) ? $rawTrend[$date]->total : 0,
            ]);
        }

        $recentExpenses = Expense::with('category')
            ->where('user_id', $user->id)
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
                $spent = Expense::where('user_id', $budget->user_id)
                    ->where('category_id', $budget->category_id)
                    ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

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
            'spendingByCategory',
            'spendingTrend',
            'recentExpenses',
            'budgetStatus'
        ));
    }
}

