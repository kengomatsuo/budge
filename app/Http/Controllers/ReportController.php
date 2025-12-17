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
        $type = $request->get('type', 'monthly');
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');
        $categoryId = $request->get('category_id');

        if ($type === 'custom' && $startDateInput && $endDateInput) {
            $startDate = Carbon::parse($startDateInput)->startOfDay();
            $endDate = Carbon::parse($endDateInput)->endOfDay();
        } elseif ($type === 'yearly') {
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $endDate = Carbon::createFromDate($year, 1, 1)->endOfYear();
        } else {
            // Default to Monthly
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        }

        $expensesQuery = Expense::with(['category', 'sharedMembers'])
            ->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhereHas('sharedMembers', function($sq) {
                      $sq->where('user_id', auth()->id());
                  });
            })
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                $expensesQuery->whereHas('category', function($q) use ($category) {
                    $q->where('name', $category->name);
                });
            }
        }

        $expenses = $expensesQuery->get();

        $userPreferred = auth()->user()->preferred_currency;

        // total spent in user's preferred currency
        $totalSpent = $expenses->sum(function($e) use ($userPreferred) {
            return convert_currency($e->my_share, $e->currency ?? 'IDR', $userPreferred);
        });

        $expenseCount = $expenses->count();

        // spending by category (converted)
        $spendingByCategory = $expenses->groupBy(function($item) {
                return $item->category->name ?? 'Unknown';
            })
            ->map(function($items, $name) use ($userPreferred) {
                $first = $items->first();
                $total = $items->sum(function($e) use ($userPreferred) {
                    return convert_currency($e->my_share, $e->currency ?? 'IDR', $userPreferred);
                });
                return (object) [
                    'name' => $name,
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
                    return convert_currency($e->my_share, $e->currency ?? 'IDR', $userPreferred);
                }),
            ];
        })->sortBy('date')->values();

        // largest expense by converted amount
        $largestExpense = $expenses->map(function($e) use ($userPreferred) {
            $e->converted_amount = convert_currency($e->my_share, $e->currency ?? 'IDR', $userPreferred);
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
            'type',
            'year',
            'month',
            'startDate',
            'endDate'
        ));
    }
}

