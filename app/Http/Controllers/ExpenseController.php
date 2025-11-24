<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\ExpenseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('category')->where('user_id', auth()->id());

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
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['currency'] = auth()->user()->preferred_currency;

        $expense = Expense::create($validated);

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
        $expense->load('category', 'files');
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        abort_if($expense->user_id !== auth()->id(), 403);
        $categories = Category::where('user_id', auth()->id())->get();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        abort_if($expense->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $expense->update($validated);

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

