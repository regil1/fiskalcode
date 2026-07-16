<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Domain\Pemasukan;
use App\Domain\Pengeluaran;
use App\Domain\CategoryDetector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
         public function index(Request $request)
    {
        $userId = auth()->id();

        // Ambil filter dari request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filterType = $request->input('filter_type', 'all'); // all, daily, weekly, monthly

        // Query dasar
        $query = Transaction::where('user_id', $userId)->with('category');

        // Terapkan filter tanggal
        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        // Hitung total berdasarkan filter
        $totalIncomeQuery = Transaction::where('user_id', $userId)->where('type', 'income');
        $totalExpenseQuery = Transaction::where('user_id', $userId)->where('type', 'expense');

        if ($startDate) {
            $totalIncomeQuery->whereDate('transaction_date', '>=', $startDate);
            $totalExpenseQuery->whereDate('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $totalIncomeQuery->whereDate('transaction_date', '<=', $endDate);
            $totalExpenseQuery->whereDate('transaction_date', '<=', $endDate);
        }

        $totalIncome = $totalIncomeQuery->sum('amount');
        $totalExpense = $totalExpenseQuery->sum('amount');
        $saldo = $totalIncome - $totalExpense;

        // Ambil transaksi dengan limit 10
        $transactions = $query->orderBy('transaction_date', 'desc')->limit(10)->get();

        // ============================================
        // DATA PIE CHART (dengan filter)
        // ============================================
        $expenseByCategoryQuery = Transaction::where('transactions.user_id', $userId)
            ->where('transactions.type', 'expense')
            ->join('categories', 'transactions.category_id', '=', 'categories.id');

        if ($startDate) {
            $expenseByCategoryQuery->whereDate('transactions.transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $expenseByCategoryQuery->whereDate('transactions.transaction_date', '<=', $endDate);
        }

        $expenseByCategory = $expenseByCategoryQuery
            ->select('categories.name as category_name', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.name')
            ->get();

        $pieLabels = $expenseByCategory->pluck('category_name')->toArray();
        $pieData = $expenseByCategory->pluck('total')->map(fn($item) => (float) $item)->toArray();

        // ============================================
        // DATA BAR CHART PER BULAN (dengan filter)
        // ============================================
        $allTransactionsQuery = Transaction::where('user_id', $userId)
            ->select('transaction_date', 'type', 'amount');

        if ($startDate) {
            $allTransactionsQuery->whereDate('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $allTransactionsQuery->whereDate('transaction_date', '<=', $endDate);
        }

        $allTransactions = $allTransactionsQuery->get();

        $transactionsByMonth = $allTransactions->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->transaction_date)->format('M Y');
        });

        $months = $transactionsByMonth->keys()->toArray();
        $monthlyIncome = [];
        $monthlyExpense = [];

        foreach ($months as $month) {
            $monthlyIncome[] = (float) $transactionsByMonth[$month]->where('type', 'income')->sum('amount');
            $monthlyExpense[] = (float) $transactionsByMonth[$month]->where('type', 'expense')->sum('amount');
        }

        // ============================================
        // DATA UNTUK EXPORT (semua transaksi tanpa limit)
        // ============================================
        $allTransactionsForExport = $query->orderBy('transaction_date', 'desc')->get();

        return view('dashboard', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'saldo',
            'pieLabels',
            'pieData',
            'months',
            'monthlyIncome',
            'monthlyExpense',
            'startDate',
            'endDate',
            'allTransactionsForExport'
        ));
    }
    /**
     * Menampilkan form tambah transaksi
     */
    public function create()
    {
        return view('transactions.create');
    }

    /**
     * Menyimpan transaksi dengan Logika OOP
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'sumber_dana' => 'nullable|string',
            'tingkat_urgensi' => 'nullable|in:low,medium,high',
        ]);

        $user = auth()->user();

        // Hitung saldo saat ini
        $saldoSaatIni = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->sum('amount') -
            Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->sum('amount');

        try {
            // AUTO-CATEGORIZATION
            $detector = new CategoryDetector();
            $categoryName = $detector->detectCategory(
                $validated['description'],
                $validated['type']
            );

            // Cari atau buat kategori di database
            $category = \App\Models\Category::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => ucfirst($categoryName),
                    'type' => $validated['type'],
                ],
                [
                    'budget_limit' => $validated['type'] === 'expense' ? 1000000 : null,
                ]
            );

            // Buat objek OOP berdasarkan tipe
            $transaksiObj = null;
            if ($validated['type'] === 'income') {
                $transaksiObj = new Pemasukan(
                    (float) $validated['amount'],
                    $validated['transaction_date'],
                    $validated['description'],
                    $validated['sumber_dana'] ?? 'Umum'
                );
            } else {
                $transaksiObj = new Pengeluaran(
                    (float) $validated['amount'],
                    $validated['transaction_date'],
                    $validated['description'],
                    $validated['tingkat_urgensi'] ?? 'medium'
                );
            }

            // POLIMORFISME: Proses transaksi
            $saldoBaru = $transaksiObj->prosesTransaksi($saldoSaatIni);

            // Simpan ke database
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'transaction_date' => $validated['transaction_date'],
            ]);

            return redirect()->route('dashboard')->with('success',
                "Transaksi berhasil disimpan! Kategori: " . ucfirst($categoryName)
            );

        } catch (\Exception $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }
    }
}
