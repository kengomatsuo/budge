<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\ExpenseFile;
use App\Models\User;
use App\Models\SharedExpenseMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'files', 'sharedMembers.user'])->where('user_id', auth()->id());

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15);
        $categories = Category::where('user_id', auth()->id())->get();

        return view('expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('user_id', auth()->id())->get();
        $users = User::where('id', '!=', auth()->id())->get();

        return view('expenses.create', compact('categories', 'users'));
    }

    public function store(Request $request)
    {
        $customMessages = [
            'is_shared.boolean' => __('messages.is_shared_boolean'),
            'title.required' => __('messages.title_required'),
            'amount.required' => __('messages.amount_required'),
            'amount.numeric' => __('messages.amount_numeric'),
            'amount.min' => __('messages.amount_min'),
            'currency.required' => __('messages.currency_required'),
            'currency.in' => __('messages.currency_in'),
            'category_id.required' => __('messages.category_required'),
            'category_id.exists' => __('messages.category_exists'),
            'expense_date.required' => __('messages.expense_date_required'),
            'expense_date.date' => __('messages.expense_date_date'),
            'expense_date.before_or_equal' => __('messages.expense_date_before_or_equal'),
            'payment_method.required' => __('messages.payment_method_required'),
            'payment_method.in' => __('messages.payment_method_in'),
            'receipt.mimes' => __('messages.receipt_mimes'),
            'receipt.max' => __('messages.receipt_max'),
            'shared_users.required_if' => __('messages.shared_users_required'),
            'shared_users.array' => __('messages.shared_users_array'),
            'shared_users.*.exists' => __('messages.shared_users_exists'),
            'shared_splits.*.numeric' => __('messages.shared_splits_numeric'),
        ];
        $customAttributes = [
            'is_shared' => __('messages.split_expense'),
            'title' => __('messages.title'),
            'amount' => __('messages.amount'),
            'currency' => __('messages.currency'),
            'category_id' => __('messages.category'),
            'expense_date' => __('messages.expense_date'),
            'payment_method' => __('messages.payment_method'),
            'shared_users' => __('messages.split_with'),
        ];

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,EUR,IDR,JPY',
            'category_id' => 'required|exists:categories,id',
            'expense_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'required|in:cash,debit_card,credit_card,e_wallet',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'is_shared' => 'sometimes|boolean',
            'split_type' => 'sometimes|in:equal,custom',
            'shared_users' => 'sometimes|required_if:is_shared,1|array|min:1',
            'shared_users.*' => ['exists:users,id', 'not_in:' . auth()->id()],
            'shared_splits' => 'sometimes|array',
            'shared_splits.*' => 'numeric|min:0',
        ], $customMessages, $customAttributes);

        $validated['user_id'] = auth()->id();
        $validated['currency'] = auth()->user()->preferred_currency;
        $validated['is_shared'] = $request->boolean('is_shared');

        $expense = Expense::create($validated);

        // handle shared members
        if ($validated['is_shared'] && $request->filled('shared_users')) {
            $members = array_values(array_filter((array) $request->input('shared_users')));
            $count = count($members);
            $splitType = $request->input('split_type', 'equal');

            if ($count > 0) {
                $amount = (float) $expense->amount;

                if ($splitType === 'custom' && $request->filled('shared_splits')) {
                    $splits = (array) $request->input('shared_splits');
                    // take only splits for selected members
                    $assigned = 0;
                    foreach ($members as $userId) {
                        $val = isset($splits[$userId]) ? (float) $splits[$userId] : 0.0;
                        $assigned += $val;
                    }

                    if (round($assigned, 2) !== round($amount, 2)) {
                        return back()->withErrors(['shared_splits' => __('messages.split_total_mismatch')])->withInput();
                    }

                    foreach ($members as $userId) {
                        $split = (float) ($splits[$userId] ?? 0);
                        SharedExpenseMember::create([
                            'expense_id' => $expense->id,
                            'user_id' => $userId,
                            'split_amount' => $split,
                            'is_paid' => false,
                        ]);
                    }
                } else {
                    $base = floor(($amount / $count) * 100) / 100; // two decimals floor
                    $totalAssigned = $base * $count;
                    $remainder = round($amount - $totalAssigned, 2);

                    foreach ($members as $i => $userId) {
                        $split = $base;
                        if ($i === 0) {
                            $split = round($split + $remainder, 2);
                        }

                        SharedExpenseMember::create([
                            'expense_id' => $expense->id,
                            'user_id' => $userId,
                            'split_amount' => $split,
                            'is_paid' => false,
                        ]);
                    }
                }
            }
        }

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $path = $file->store('receipts', 'public');

            ExpenseFile::create([
                'expense_id' => $expense->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return redirect()->route('expenses.index')->with('success', __('messages.success'));
    }

    public function show(Expense $expense)
    {
        abort_if($expense->user_id !== auth()->id(), 403);
        $expense->load('category', 'files', 'sharedMembers.user');
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        abort_if($expense->user_id !== auth()->id(), 403);
        $categories = Category::where('user_id', auth()->id())->get();
        $users = User::where('id', '!=', auth()->id())->get();
        $selected = $expense->sharedMembers()->pluck('user_id')->toArray();

        return view('expenses.edit', compact('expense', 'categories', 'users', 'selected'));
    }

    public function update(Request $request, Expense $expense)
    {
        abort_if($expense->user_id !== auth()->id(), 403);

        $customMessages = [
            'is_shared.boolean' => __('messages.is_shared_boolean'),
            'title.required' => __('messages.title_required'),
            'amount.required' => __('messages.amount_required'),
            'amount.numeric' => __('messages.amount_numeric'),
            'amount.min' => __('messages.amount_min'),
            'currency.required' => __('messages.currency_required'),
            'currency.in' => __('messages.currency_in'),
            'category_id.required' => __('messages.category_required'),
            'category_id.exists' => __('messages.category_exists'),
            'expense_date.required' => __('messages.expense_date_required'),
            'expense_date.date' => __('messages.expense_date_date'),
            'expense_date.before_or_equal' => __('messages.expense_date_before_or_equal'),
            'payment_method.required' => __('messages.payment_method_required'),
            'payment_method.in' => __('messages.payment_method_in'),
            'receipt.mimes' => __('messages.receipt_mimes'),
            'receipt.max' => __('messages.receipt_max'),
            'shared_users.required_if' => __('messages.shared_users_required'),
            'shared_users.array' => __('messages.shared_users_array'),
            'shared_users.*.exists' => __('messages.shared_users_exists'),
            'shared_splits.*.numeric' => __('messages.shared_splits_numeric'),
        ];
        $customAttributes = [
            'is_shared' => __('messages.split_expense'),
            'title' => __('messages.title'),
            'amount' => __('messages.amount'),
            'currency' => __('messages.currency'),
            'category_id' => __('messages.category'),
            'expense_date' => __('messages.expense_date'),
            'payment_method' => __('messages.payment_method'),
            'shared_users' => __('messages.split_with'),
        ];

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,EUR,IDR,JPY',
            'category_id' => 'required|exists:categories,id',
            'expense_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'required|in:cash,debit_card,credit_card,e_wallet',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'is_shared' => 'sometimes|boolean',
            'split_type' => 'sometimes|in:equal,custom',
            'shared_users' => 'sometimes|required_if:is_shared,1|array|min:1',
            'shared_users.*' => ['exists:users,id', 'not_in:' . auth()->id()],
            'shared_splits' => 'sometimes|array',
            'shared_splits.*' => 'numeric|min:0',
        ], $customMessages, $customAttributes);

        $validated['is_shared'] = $request->boolean('is_shared');

        $expense->update($validated);

        // update shared members: remove existing and recreate according to split type
        $expense->sharedMembers()->delete();
        if ($validated['is_shared'] && $request->filled('shared_users')) {
            $members = array_values(array_filter((array) $request->input('shared_users')));
            $count = count($members);
            $splitType = $request->input('split_type', 'equal');

            if ($count > 0) {
                $amount = (float) $expense->amount;

                if ($splitType === 'custom' && $request->filled('shared_splits')) {
                    $splits = (array) $request->input('shared_splits');
                    $assigned = 0;
                    foreach ($members as $userId) {
                        $assigned += isset($splits[$userId]) ? (float) $splits[$userId] : 0;
                    }

                    if (round($assigned, 2) !== round($amount, 2)) {
                        return back()->withErrors(['shared_splits' => __('messages.split_total_mismatch')])->withInput();
                    }

                    foreach ($members as $userId) {
                        $split = (float) ($splits[$userId] ?? 0);
                        SharedExpenseMember::create([
                            'expense_id' => $expense->id,
                            'user_id' => $userId,
                            'split_amount' => $split,
                            'is_paid' => false,
                        ]);
                    }
                } else {
                    $base = floor(($amount / $count) * 100) / 100;
                    $totalAssigned = $base * $count;
                    $remainder = round($amount - $totalAssigned, 2);

                    foreach ($members as $i => $userId) {
                        $split = $base;
                        if ($i === 0) {
                            $split = round($split + $remainder, 2);
                        }

                        SharedExpenseMember::create([
                            'expense_id' => $expense->id,
                            'user_id' => $userId,
                            'split_amount' => $split,
                            'is_paid' => false,
                        ]);
                    }
                }
            }
        }

        if ($request->hasFile('receipt')) {
            foreach ($expense->files as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }

            $file = $request->file('receipt');
            $path = $file->store('receipts', 'public');

            ExpenseFile::create([
                'expense_id' => $expense->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return redirect()->route('expenses.index')->with('success', __('messages.success'));
    }

    public function destroy(Expense $expense)
    {
        abort_if($expense->user_id !== auth()->id(), 403);

        foreach ($expense->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', __('messages.success'));
    }
}

