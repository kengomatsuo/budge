<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $categoryId = $request->get('category_id');

        [$startDate, $endDate] = $this->getDateRange($period);

        $expensesQuery = Expense::with('category')->where('user_id', auth()->id())
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($categoryId) {
            $expensesQuery->where('category_id', $categoryId);
        }

        $expenses = $expensesQuery->get();

        $userPreferred = auth()->user()->preferred_currency;

        // total spent in user's preferred currency
        $totalSpent = $expenses->sum(function($e) use ($userPreferred) {
            return convert_currency($e->amount, $e->currency ?? 'IDR', $userPreferred);
        });

        $expenseCount = $expenses->count();

        // spending by category (converted)
        $spendingByCategory = $expenses->groupBy('category_id')
            ->map(function($items) use ($userPreferred) {
                $first = $items->first();
                $total = $items->sum(function($e) use ($userPreferred) {
                    return convert_currency($e->amount, $e->currency ?? 'IDR', $userPreferred);
                });
                return (object) [
                    'name' => $first->category->name ?? 'Unknown',
                    'color' => $first->category->color ?? null,
                    'total' => $total,
                ];
            })->values();

        // spending trend per date
        $spendingTrend = $expenses->groupBy(function($e) {
            return Carbon::parse($e->expense_date)->format('Y-m-d');
        })->map(function($items) use ($userPreferred) {
            return (object) [
                'date' => Carbon::parse($items->first()->expense_date)->format('Y-m-d'),
                'total' => $items->sum(function($e) use ($userPreferred) {
                    return convert_currency($e->amount, $e->currency ?? 'IDR', $userPreferred);
                }),
            ];
        })->sortBy('date')->values();

        // largest expense by converted amount
        $largestExpense = $expenses->map(function($e) use ($userPreferred) {
            $e->converted_amount = convert_currency($e->amount, $e->currency ?? 'IDR', $userPreferred);
            return $e;
        })->sortByDesc('converted_amount')->first();

        $categories = Category::where('user_id', auth()->id())->get();

        $averageSpending = $expenseCount > 0 ? $totalSpent / $expenseCount : 0;

        return view('reports.index', compact(
            'totalSpent',
            'averageSpending',
            'spendingByCategory',
            'spendingTrend',
            'largestExpense',
            'categories',
            'period'
        ));
    }

    private function getDateRange($period)
    {
        $now = now();

        return match($period) {
            'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }
}

