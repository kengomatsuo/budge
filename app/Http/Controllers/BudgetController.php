<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $now = now();
        $budgets = Budget::with('category')
            ->where('user_id', auth()->id())
            ->where('start_date', '<=', $now)
            ->where(function($query) use ($now) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $now);
            })
            ->get()
            ->map(function($budget) {
                $expenses = Expense::where('user_id', $budget->user_id)
                    ->where('category_id', $budget->category_id)
                    ->whereBetween('expense_date', [$budget->start_date, $budget->end_date ?? now()])
                    ->get();

                // Sum expenses converted to the budget's currency
                $spent = $expenses->sum(function($e) use ($budget) {
                    return convert_currency($e->amount, $e->currency ?? 'IDR', $budget->currency ?? 'IDR');
                });

                $budget->spent = $spent;
                $budget->percentage = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;
                $budget->status = $budget->percentage >= 100 ? 'over_budget' :
                                 ($budget->percentage >= 80 ? 'warning' : 'on_track');
                return $budget;
            });

        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        $categories = Category::where('user_id', auth()->id())->get();
        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'period_type' => 'required|in:daily,weekly,monthly',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['currency'] = auth()->user()->preferred_currency;

        // Prevent overlapping budgets for same user + category
        $start = $validated['start_date'];
        $end = $validated['end_date'] ?? null;
        if ($this->hasOverlap($validated['user_id'], $validated['category_id'], $start, $end)) {
            return back()
                ->withErrors(['start_date' => __('messages.budget_overlap')])
                ->withInput();
        }

        Budget::create($validated);

        return redirect()->route('budgets.index')->with('success', __('messages.success'));
    }

    public function edit(Budget $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);
        $categories = Category::where('user_id', auth()->id())->get();
        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'period_type' => 'required|in:daily,weekly,monthly',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Prevent overlapping budgets for same user + category, exclude current budget
        $start = $validated['start_date'];
        $end = $validated['end_date'] ?? null;
        if ($this->hasOverlap($budget->user_id, $validated['category_id'], $start, $end, $budget->id)) {
            return back()
                ->withErrors(['start_date' => __('messages.budget_overlap')])
                ->withInput();
        }

        $budget->update($validated);

        return redirect()->route('budgets.index')->with('success', __('messages.success'));
    }

    /**
     * Check whether a budget for given user/category overlaps an existing budget.
     * If $exceptId provided, ignore that budget (useful for updates).
     */
    private function hasOverlap($userId, $categoryId, $start, $end = null, $exceptId = null)
    {
        // Treat null end as open-ended (far future)
        $newStart = $start;
        $newEnd = $end ?? now()->copy()->addYears(1000)->toDateString();

        $query = Budget::where('user_id', $userId)
            ->where('category_id', $categoryId);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        // Overlap exists when existing.start_date <= newEnd AND (existing.end_date IS NULL OR existing.end_date >= newStart)
        $query->where(function($q) use ($newStart, $newEnd) {
            $q->where('start_date', '<=', $newEnd)
              ->where(function($q2) use ($newStart) {
                  $q2->whereNull('end_date')
                     ->orWhere('end_date', '>=', $newStart);
              });
        });

        return $query->exists();
    }

    public function destroy(Budget $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);
        $budget->delete();
        return redirect()->route('budgets.index')->with('success', __('messages.success'));
    }
}
