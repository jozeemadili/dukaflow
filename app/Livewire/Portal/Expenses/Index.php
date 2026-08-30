<?php

namespace App\Livewire\Portal\Expenses;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portal', ['title' => 'Expenses'])]
class Index extends Component
{
    use WithPagination;

    public string $category = 'other';

    public string $amount = '';

    public string $description = '';

    public string $expense_date;

    public function mount()
    {
        $this->expense_date = now()->toDateString();
    }

    public function save()
    {
        $this->validate([
            'category' => ['required', 'in:'.implode(',', Expense::CATEGORIES)],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
        ]);

        Expense::create([
            'merchant_id' => Auth::user()->merchant_id,
            'category' => $this->category,
            'amount' => $this->amount,
            'description' => $this->description,
            'expense_date' => $this->expense_date,
            'recorded_by' => Auth::id(),
        ]);

        $this->reset(['amount', 'description']);
        $this->category = 'other';
        $this->expense_date = now()->toDateString();
        session()->flash('status', 'Expense recorded.');
    }

    public function render()
    {
        $expenses = Expense::where('merchant_id', Auth::user()->merchant_id)
            ->latest('expense_date')
            ->paginate(15);

        return view('livewire.portal.expenses.index', [
            'expenses' => $expenses,
            'categories' => Expense::CATEGORIES,
        ]);
    }
}
