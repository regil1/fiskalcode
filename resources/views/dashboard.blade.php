<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Dashboard Keuangan') }}
            </h2>

            {{-- TOMBOL TOGGLE DARK/LIGHT MODE --}}
            <button id="theme-toggle" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                <svg id="sun-icon" class="w-5 h-5 text-yellow-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                </svg>
                <svg id="moon-icon" class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
            </button>
        </div>
    </x-slot>

    {{-- Load CSS Dark/Light Theme --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard-dark.css') }}">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Notifikasi Sukses --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg dark:bg-green-900/30 dark:text-green-300 dark:border dark:border-green-500/30">
                    {{ session('success') }}
                </div>
            @endif

            {{-- NOTIFIKASI BUDGET --}}
            @php
                $budgetAlerts = [];
                $expenseCategories = \App\Models\Category::where('user_id', auth()->id())
                    ->where('type', 'expense')
                    ->whereNotNull('monthly_limit')
                    ->get();

                foreach($expenseCategories as $cat) {
                    $spent = \App\Models\Transaction::where('user_id', auth()->id())
                        ->where('category_id', $cat->id)
                        ->whereMonth('transaction_date', now()->month)
                        ->whereYear('transaction_date', now()->year)
                        ->sum('amount');

                    if ($cat->monthly_limit > 0) {
                        $percent = ($spent / $cat->monthly_limit) * 100;
                        if ($percent >= 80 && $percent < 100) {
                            $budgetAlerts[] = ['type' => 'warning', 'message' => "Budget '{$cat->name}' tinggal " . round(100 - $percent) . "% lagi!"];
                        } elseif ($percent >= 100) {
                            $budgetAlerts[] = ['type' => 'danger', 'message' => "Budget '{$cat->name}' sudah terlampaui!"];
                        }
                    }
                }
            @endphp

            @if(count($budgetAlerts) > 0)
                @foreach($budgetAlerts as $alert)
                    <div class="mb-4 p-4 rounded-lg border-l-4 {{ $alert['type'] === 'danger' ? 'bg-red-50 dark:bg-red-900/20 border-red-500 text-red-700 dark:text-red-300' : 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-500 text-yellow-700 dark:text-yellow-300' }}">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">{{ $alert['type'] === 'danger' ? '🚨' : '⚠️' }}</span>
                            <span class="font-medium">{{ $alert['message'] }}</span>
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- Filter Tanggal Laporan --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 theme-card">
                <div class="p-6">
                    <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark-input">
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Akhir</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark-input">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">🔍 Filter</button>
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg font-medium transition">Reset</a>
                        </div>
                    </form>
                    @if(request('start_date') || request('end_date'))
                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                            📅 Menampilkan data dari <strong>{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : 'awal' }}</strong> sampai <strong>{{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : 'sekarang' }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kartu Ringkasan Keuangan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg cursor-pointer transform transition hover:scale-105 theme-card" onclick="filterTransaksi('all')" id="card-saldo">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Saldo Saat Ini</div>
                        <div class="text-3xl font-bold text-indigo-600 dark:text-blue-400 mt-2">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-2">Klik untuk lihat semua transaksi</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg cursor-pointer transform transition hover:scale-105 theme-card" onclick="filterTransaksi('income')" id="card-income">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Pemasukan</div>
                        <div class="text-3xl font-bold text-emerald-600 dark:text-[#ff5e00] mt-2">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-2">Klik untuk lihat pemasukan</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg cursor-pointer transform transition hover:scale-105 theme-card" onclick="filterTransaksi('expense')" id="card-expense">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Pengeluaran</div>
                        <div class="text-3xl font-bold text-red-600 dark:text-[#7000ff] mt-2">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-2">Klik untuk lihat pengeluaran</div>
                    </div>
                </div>
            </div>

            {{-- FITUR BUDGET & TARGET TABUNGAN --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="theme-card rounded-xl p-6 shadow-lg">
                    <h3 class="text-lg font-semibold text-main mb-4 flex items-center gap-2"><span class="text-[#ff5e00]">🔥</span> Status Budget Bulan Ini</h3>
                    @php
                        $budgetCategories = \App\Models\Category::where('user_id', auth()->id())->whereNotNull('monthly_limit')->where('type', 'expense')->get();
                    @endphp
                    @if($budgetCategories->count() > 0)
                        <div class="space-y-4">
                            @foreach($budgetCategories as $cat)
                                @php
                                    $spent = \App\Models\Transaction::where('user_id', auth()->id())->where('category_id', $cat->id)->whereMonth('transaction_date', now()->month)->whereYear('transaction_date', now()->year)->sum('amount');
                                    $limit = $cat->monthly_limit;
                                    $percent = $limit > 0 ? min(($spent / $limit) * 100, 100) : 0;
                                    $colorClass = $percent >= 90 ? 'bg-red-500' : ($percent >= 70 ? 'bg-[#ff5e00]' : 'progress-gradient');
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-main font-medium">{{ $cat->name }}</span>
                                        <span class="text-muted">Rp {{ number_format($spent, 0, ',', '.') }} / {{ number_format($limit, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="w-full bg-elevated rounded-full h-2.5">
                                        <div class="h-2.5 rounded-full {{ $colorClass }}" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-sm text-center py-4">Belum ada budget yang diatur. Klik tombol ⚙️ di kanan bawah.</p>
                    @endif
                </div>

                <div class="theme-card rounded-xl p-6 shadow-lg">
                    <h3 class="text-lg font-semibold text-main mb-4 flex items-center gap-2"><span class="text-[#7000ff]">🎯</span> Target Tabungan</h3>
                    @php $goals = \App\Models\SavingsGoal::where('user_id', auth()->id())->get(); @endphp
                    @if($goals->count() > 0)
                        <div class="space-y-4">
                            @foreach($goals as $goal)
                                @php $percent = $goal->target_amount > 0 ? min(($goal->current_amount / $goal->target_amount) * 100, 100) : 0; @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-main font-medium">{{ $goal->name }}</span>
                                        <span class="text-muted">{{ round($percent) }}%</span>
                                    </div>
                                    <div class="w-full bg-elevated rounded-full h-2.5 mb-1">
                                        <div class="h-2.5 rounded-full progress-gradient" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <div class="text-xs text-muted text-right">Rp {{ number_format($goal->current_amount, 0, ',', '.') }} dari Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-sm text-center py-4">Belum ada target tabungan. Klik tombol ⚙️ di kanan bawah.</p>
                    @endif
                </div>
            </div>

            {{-- GRAFIK VISUALISASI --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="theme-card rounded-xl p-6 shadow-lg">
                    <h3 class="text-lg font-semibold text-main mb-4">📊 Persentase Pengeluaran per Kategori</h3>
                    <div style="position: relative; height:300px;"><canvas id="pieChart"></canvas></div>
                </div>
                <div class="theme-card rounded-xl p-6 shadow-lg">
                    <h3 class="text-lg font-semibold text-main mb-4">📈 Tren Pemasukan & Pengeluaran Bulanan</h3>
                    <div style="position: relative; height:300px;"><canvas id="barChart"></canvas></div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="mb-6 flex flex-wrap gap-3 items-center">
                <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm">+ Catat Transaksi Baru</a>
                <form method="GET" action="{{ route('export.pdf') }}" target="_blank" class="inline-block">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-red-700 transition shadow-sm">📄 Export Laporan PDF</button>
                </form>
            </div>

            {{-- Filter Indicator --}}
            <div class="mb-4">
                <h3 id="filter-title" class="text-lg font-semibold text-main">Semua Riwayat Transaksi</h3>
                <p id="filter-description" class="text-sm text-muted">Menampilkan semua transaksi</p>
            </div>

            {{-- Tabel Riwayat Transaksi --}}
            <div class="theme-card overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-main">
                    <div id="table-all" class="transaction-table">
                        @if($transactions->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-elevated">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Tanggal</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kategori</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Deskripsi</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Jenis</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($transactions as $trans)
                                            <tr class="hover:bg-elevated">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-main">{{ \Carbon\Carbon::parse($trans->transaction_date)->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-main">{{ $trans->category->name ?? '-' }}</td>
                                                <td class="px-6 py-4 text-sm text-main">{{ $trans->description ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($trans->type === 'income')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Pemasukan</span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Pengeluaran</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold {{ $trans->type === 'income' ? 'text-emerald-600 dark:text-[#ff5e00]' : 'text-red-600 dark:text-[#7000ff]' }}">
                                                    {{ $trans->type === 'income' ? '+' : '-' }} Rp {{ number_format($trans->amount, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-muted"><p>Belum ada transaksi.</p><p class="text-sm mt-2">Mulai catat transaksi pertama Anda!</p></div>
                        @endif
                    </div>

                    <div id="table-income" class="transaction-table hidden">
                        @php $incomeTransactions = $transactions->where('type', 'income'); @endphp
                        @if($incomeTransactions->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-elevated">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Tanggal</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kategori</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Deskripsi</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($incomeTransactions as $trans)
                                            <tr class="hover:bg-elevated">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-main">{{ \Carbon\Carbon::parse($trans->transaction_date)->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-main">{{ $trans->category->name ?? '-' }}</td>
                                                <td class="px-6 py-4 text-sm text-main">{{ $trans->description ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-emerald-600 dark:text-[#ff5e00]">+ Rp {{ number_format($trans->amount, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 p-4 bg-elevated rounded-lg"><p class="text-sm text-emerald-600 dark:text-[#ff5e00] font-semibold">Total Pemasukan: Rp {{ number_format($incomeTransactions->sum('amount'), 0, ',', '.') }}</p></div>
                        @else
                            <div class="text-center py-8 text-muted"><p>Belum ada transaksi pemasukan.</p></div>
                        @endif
                    </div>

                    <div id="table-expense" class="transaction-table hidden">
                        @php $expenseTransactions = $transactions->where('type', 'expense'); @endphp
                        @if($expenseTransactions->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-elevated">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Tanggal</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kategori</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Deskripsi</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($expenseTransactions as $trans)
                                            <tr class="hover:bg-elevated">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-main">{{ \Carbon\Carbon::parse($trans->transaction_date)->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-main">{{ $trans->category->name ?? '-' }}</td>
                                                <td class="px-6 py-4 text-sm text-main">{{ $trans->description ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-red-600 dark:text-[#7000ff]">- Rp {{ number_format($trans->amount, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 p-4 bg-elevated rounded-lg"><p class="text-sm text-red-600 dark:text-[#7000ff] font-semibold">Total Pengeluaran: Rp {{ number_format($expenseTransactions->sum('amount'), 0, ',', '.') }}</p></div>
                        @else
                            <div class="text-center py-8 text-muted"><p>Belum ada transaksi pengeluaran.</p></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL SETTINGS (Budget, Target & Kategori) --}}
    {{-- ========================================== --}}
    <div id="settings-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="theme-card rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-main">⚙️ Pengaturan</h3>
                <button onclick="closeSettingsModal()" class="text-muted hover:text-main text-2xl">&times;</button>
            </div>

            {{-- 1. Form Budget per Kategori --}}
            <div class="mb-6">
                <h4 class="text-lg font-medium text-main mb-4">💰 Budget Bulanan per Kategori</h4>
                @php $expenseCategories = \App\Models\Category::where('user_id', auth()->id())->where('type', 'expense')->get(); @endphp
                @if($expenseCategories->count() > 0)
                    <form method="POST" action="{{ route('budget.update') }}" class="space-y-3">
                        @csrf
                        @foreach($expenseCategories as $cat)
                            <div class="flex items-center gap-3">
                                <label class="text-sm text-main w-1/3">{{ $cat->name }}</label>
                                <input type="number" name="budgets[{{ $cat->id }}]" value="{{ $cat->monthly_limit ?? 0 }}" placeholder="0" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark-input">
                            </div>
                        @endforeach
                        <button type="submit" class="w-full mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan Budget</button>
                    </form>
                @else
                    <p class="text-muted text-sm">Belum ada kategori pengeluaran.</p>
                @endif
            </div>

            <hr class="border-gray-300 dark:border-gray-700 my-6">

            {{-- 2. Form Target Tabungan --}}
            <div class="mb-6">
                <h4 class="text-lg font-medium text-main mb-4">🎯 Target Tabungan</h4>
                @php $goals = \App\Models\SavingsGoal::where('user_id', auth()->id())->get(); @endphp
                @if($goals->count() > 0)
                    <div class="space-y-3 mb-4">
                        @foreach($goals as $goal)
                            <div class="flex items-center justify-between p-3 bg-elevated rounded-lg">
                                <div>
                                    <div class="text-main font-medium">{{ $goal->name }}</div>
                                    <div class="text-xs text-muted">Rp {{ number_format($goal->current_amount, 0, ',', '.') }} / Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</div>
                                </div>
                                <form method="POST" action="{{ route('savings.update', $goal->id) }}" class="flex gap-2">
                                    @csrf @method('PATCH')
                                    <input type="number" name="current_amount" value="{{ $goal->current_amount }}" placeholder="Jumlah" class="w-32 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark-input text-sm">
                                    <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700">Update</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('savings.store') }}" class="space-y-3">
                    @csrf
                    <input type="text" name="name" placeholder="Nama Target (misal: Beli Laptop)" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark-input">
                    <input type="number" name="target_amount" placeholder="Target Jumlah (Rp)" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark-input">
                    <button type="submit" class="w-full px-4 py-2 bg-[#7000ff] text-white rounded-lg hover:bg-[#5a00cc]">+ Tambah Target Baru</button>
                </form>
            </div>

            <hr class="border-gray-300 dark:border-gray-700 my-6">

            {{-- 3. FITUR C: MANAJEMEN KATEGORI --}}
            <div>
                <h4 class="text-lg font-medium text-main mb-4">🗂️ Manajemen Kategori</h4>
                <p class="text-xs text-muted mb-4">Edit atau hapus kategori yang sudah dibuat otomatis oleh sistem.</p>

                @php $allCategories = \App\Models\Category::where('user_id', auth()->id())->get(); @endphp

                @if($allCategories->count() > 0)
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                        @foreach($allCategories as $cat)
                            <div class="flex items-center justify-between p-3 bg-elevated rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="text-lg">@if($cat->type === 'income') 💰 @else 💸 @endif</span>
                                    <form method="POST" action="{{ route('categories.update', $cat->id) }}" class="flex-1 flex gap-2">
                                        @csrf @method('PATCH')
                                        <input type="text" name="name" value="{{ $cat->name }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm px-2 py-1 dark-input">
                                        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition">Simpan</button>
                                    </form>
                                </div>
                                <form method="POST" action="{{ route('categories.delete', $cat->id) }}" onsubmit="return confirm('Yakin hapus kategori ini? Transaksi terkait mungkin akan kehilangan kategori.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ml-2 px-3 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600 transition">Hapus</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-sm">Belum ada kategori.</p>
                @endif
            </div>

        </div>
    </div>

    {{-- Tombol Floating Settings --}}
    <button onclick="openSettingsModal()" class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-[#ff5e00] to-[#7000ff] text-white rounded-full shadow-lg hover:shadow-xl transform hover:scale-110 transition flex items-center justify-center text-2xl z-40">⚙️</button>

    {{-- ========================================== --}}
    {{-- JAVASCRIPT: THEME TOGGLE, FILTER & CHART.JS --}}
    {{-- ========================================== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function getChartColors(isDarkMode) {
            return isDarkMode ? { textColor: 'rgba(255, 255, 255, 0.8)', gridColor: 'rgba(255, 255, 255, 0.1)', tooltipBg: 'rgba(13, 15, 20, 0.95)', borderColor: 'rgba(255, 255, 255, 0.2)' }
                              : { textColor: 'rgba(31, 41, 55, 0.8)', gridColor: 'rgba(0, 0, 0, 0.05)', tooltipBg: 'rgba(255, 255, 255, 0.95)', borderColor: 'rgba(0, 0, 0, 0.1)' };
        }

        function initCharts(isDarkMode) {
            const colors = getChartColors(isDarkMode);
            Chart.defaults.color = colors.textColor;
            Chart.defaults.borderColor = colors.gridColor;

            const ctxPie = document.getElementById('pieChart');
            if (ctxPie) {
                if (window.pieChartInstance) window.pieChartInstance.destroy();
                window.pieChartInstance = new Chart(ctxPie, {
                    type: 'pie',
                    data: { labels: @json($pieLabels), datasets: [{ data: @json($pieData), backgroundColor: ['#ff5e00', '#7000ff', '#00d2ff', '#3a7bd5', '#f12711', '#f5af19'], borderWidth: 0 }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: colors.textColor, padding: 15 } }, tooltip: { backgroundColor: colors.tooltipBg, titleColor: colors.textColor, bodyColor: colors.textColor, borderColor: colors.borderColor, borderWidth: 1 } } }
                });
            }

            const ctxBar = document.getElementById('barChart');
            if (ctxBar) {
                if (window.barChartInstance) window.barChartInstance.destroy();
                window.barChartInstance = new Chart(ctxBar, {
                    type: 'bar',
                    data: { labels: @json($months), datasets: [{ label: 'Pemasukan', data: @json($monthlyIncome), backgroundColor: '#ff5e00', borderRadius: 4, barPercentage: 0.6 }, { label: 'Pengeluaran', data: @json($monthlyExpense), backgroundColor: '#7000ff', borderRadius: 4, barPercentage: 0.6 }] },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, grid: { color: colors.gridColor }, ticks: { color: colors.textColor, callback: v => 'Rp ' + v.toLocaleString('id-ID') } }, x: { grid: { display: false }, ticks: { color: colors.textColor } } }, plugins: { legend: { position: 'bottom', labels: { color: colors.textColor, usePointStyle: true, padding: 20 } }, tooltip: { backgroundColor: colors.tooltipBg, titleColor: colors.textColor, bodyColor: colors.textColor, borderColor: colors.borderColor, borderWidth: 1, callbacks: { label: function(context) { let label = context.dataset.label || ''; if (label) label += ': '; if (context.parsed.y !== null) label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y); return label; } } } } }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const sunIcon = document.getElementById('sun-icon');
            const moonIcon = document.getElementById('moon-icon');
            const body = document.body;
            const savedTheme = localStorage.getItem('fiskalcode-theme') || 'light';
            const isDark = savedTheme === 'dark';

            if (isDark) { body.classList.add('dark-theme'); body.classList.remove('light-theme'); sunIcon.classList.remove('hidden'); moonIcon.classList.add('hidden'); }
            else { body.classList.add('light-theme'); body.classList.remove('dark-theme'); sunIcon.classList.add('hidden'); moonIcon.classList.remove('hidden'); }

            initCharts(isDark);

            themeToggle.addEventListener('click', function() {
                const isCurrentlyDark = body.classList.contains('dark-theme');
                if (isCurrentlyDark) { body.classList.remove('dark-theme'); body.classList.add('light-theme'); sunIcon.classList.add('hidden'); moonIcon.classList.remove('hidden'); localStorage.setItem('fiskalcode-theme', 'light'); initCharts(false); }
                else { body.classList.remove('light-theme'); body.classList.add('dark-theme'); sunIcon.classList.remove('hidden'); moonIcon.classList.add('hidden'); localStorage.setItem('fiskalcode-theme', 'dark'); initCharts(true); }
            });

            filterTransaksi('all');
        });

        function filterTransaksi(type) {
            document.querySelectorAll('.transaction-table').forEach(table => table.classList.add('hidden'));
            document.querySelectorAll('[id^="card-"]').forEach(card => card.classList.remove('ring-2', 'ring-[#ff5e00]', 'ring-[#7000ff]', 'ring-white'));
            if (type === 'all') { document.getElementById('table-all').classList.remove('hidden'); document.getElementById('filter-title').textContent = 'Semua Riwayat Transaksi'; document.getElementById('filter-description').textContent = 'Menampilkan semua transaksi'; document.getElementById('card-saldo').classList.add('ring-2', 'ring-white'); }
            else if (type === 'income') { document.getElementById('table-income').classList.remove('hidden'); document.getElementById('filter-title').textContent = 'Riwayat Pemasukan'; document.getElementById('filter-description').textContent = 'Hanya menampilkan transaksi pemasukan'; document.getElementById('card-income').classList.add('ring-2', 'ring-[#ff5e00]'); }
            else if (type === 'expense') { document.getElementById('table-expense').classList.remove('hidden'); document.getElementById('filter-title').textContent = 'Riwayat Pengeluaran'; document.getElementById('filter-description').textContent = 'Hanya menampilkan transaksi pengeluaran'; document.getElementById('card-expense').classList.add('ring-2', 'ring-[#7000ff]'); }
        }

        function openSettingsModal() { document.getElementById('settings-modal').classList.remove('hidden'); document.getElementById('settings-modal').classList.add('flex'); }
        function closeSettingsModal() { document.getElementById('settings-modal').classList.add('hidden'); document.getElementById('settings-modal').classList.remove('flex'); }
    </script>
</x-app-layout>
