<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi - FiskalCode</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #1a1a1a;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            margin-bottom: 20px;
        }
        .summary-box {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            text-align: center;
        }
        .income {
            background-color: #d4edda;
            color: #155724;
        }
        .expense {
            background-color: #f8d7da;
            color: #721c24;
        }
        .balance {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #333;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-income {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-expense {
            background-color: #f8d7da;
            color: #721c24;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FISKALCODE</h1>
        <p>Laporan Transaksi Keuangan</p>
        <p>Periode: {{ $period }}</p>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-box income">
            <div><strong>Total Pemasukan</strong></div>
            <div>Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box expense">
            <div><strong>Total Pengeluaran</strong></div>
            <div>Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box balance">
            <div><strong>Saldo</strong></div>
            <div>Rp {{ number_format($saldo, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Jenis</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trans)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($trans->transaction_date)->format('d/m/Y') }}</td>
                    <td>{{ $trans->category->name ?? '-' }}</td>
                    <td>{{ $trans->description ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $trans->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                            {{ $trans->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                        </span>
                    </td>
                    <td class="text-right">
                        {{ $trans->type === 'income' ? '+' : '-' }} Rp {{ number_format($trans->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem FiskalCode</p>
    </div>
</body>
</html>
