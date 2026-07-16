<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function exportPDF(Request $request)
    {
        $userId = auth()->id();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Ambil data transaksi
        $query = Transaction::where('user_id', $userId)->with('category')->orderBy('transaction_date', 'desc');

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        $transactions = $query->get();

        // Hitung total
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $saldo = $totalIncome - $totalExpense;

        // Tentukan periode untuk judul
        $period = 'Semua Waktu';
        if ($startDate && $endDate) {
            $period = \Carbon\Carbon::parse($startDate)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d M Y');
        } elseif ($startDate) {
            $period = 'Sejak ' . \Carbon\Carbon::parse($startDate)->format('d M Y');
        } elseif ($endDate) {
            $period = 'Sampai ' . \Carbon\Carbon::parse($endDate)->format('d M Y');
        }

        // Load view PDF
        $pdf = Pdf::loadView('reports.transactions-pdf', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'saldo',
            'period'
        ));

        // Download PDF
        return $pdf->download('Laporan-Transaksi-' . date('Y-m-d') . '.pdf');
    }
}
