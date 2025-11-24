<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $categoryId = $request->get('category_id');

        [$startDate, $endDate] = $this->getDateRange($period);

        $query = Expense::where('user_id', auth()->id())
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $totalSpent = $query->sum('amount');
        $expenseCount = $query->count();

        $spendingByCategory = Expense::select('categories.name', 'categories.color', DB::raw('SUM(expenses.amount) as total'))
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->where('expenses.user_id', auth()->id())
            ->whereBetween('expenses.expense_date', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderBy('total', 'desc')
            ->get();

        $spendingTrend = Expense::selectRaw('DATE(expense_date) as date, SUM(amount) as total')
            ->where('user_id', auth()->id())
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $largestExpense = Expense::where('user_id', auth()->id())
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('amount', 'desc')
            ->first();

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

