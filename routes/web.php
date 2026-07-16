<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| 1. ROUTE TAMU (GUEST) - Tidak perlu login
|--------------------------------------------------------------------------
*/

// Redirect halaman utama ke login
Route::get('/', function () {
    return redirect('/login');
});

// Tampilkan halaman Login Custom
Route::get('/login', function () {
    return view('auth.login-custom');
})->name('login');

// Proses Login (POST)
Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (auth()->attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
});

// Tampilkan halaman Register Custom
Route::get('/register', function () {
    return view('auth.register-custom');
})->name('register');

// Proses Register (POST)
Route::post('/register', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'unique:users'],
        'password' => ['required', 'min:8', 'confirmed'],
    ]);

    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
    ]);

    auth()->login($user);

    return redirect('/dashboard');
});

// Proses Logout (POST)
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');


/*
|--------------------------------------------------------------------------
| 2. ROUTE TERPROTEKSI - Wajib Login (Middleware Auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', [TransactionController::class, 'index'])->name('dashboard');

    // Export Laporan ke PDF
    Route::get('/export-pdf', [ReportController::class, 'exportPDF'])->name('export.pdf');

    // Profile Routes (Bawaan Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Transaction Routes (CRUD)
    Route::get('/transaksi/tambah', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transaksi', [TransactionController::class, 'store'])->name('transactions.store');

    // ==========================================
    // 3. ROUTE BARU: Budget & Target Tabungan
    // ==========================================

    // Update Budget per Kategori
    Route::post('/budget/update', function (\Illuminate\Http\Request $request) {
        $budgets = $request->input('budgets', []);

        foreach ($budgets as $categoryId => $amount) {
            \App\Models\Category::where('id', $categoryId)
                ->where('user_id', auth()->id())
                ->update(['monthly_limit' => $amount > 0 ? $amount : null]);
        }

        return back()->with('success', 'Budget berhasil diperbarui!');
    })->name('budget.update');

    // Tambah Target Tabungan Baru
    Route::post('/savings/store', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
        ]);

        \App\Models\SavingsGoal::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'target_amount' => $validated['target_amount'],
            'current_amount' => 0,
        ]);

        return back()->with('success', 'Target tabungan berhasil ditambahkan!');
    })->name('savings.store');

    // Update Progress Target Tabungan
    Route::patch('/savings/update/{id}', function (\Illuminate\Http\Request $request, $id) {
        $validated = $request->validate([
            'current_amount' => 'required|numeric|min:0',
        ]);

        $goal = \App\Models\SavingsGoal::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $goal->update(['current_amount' => $validated['current_amount']]);

        return back()->with('success', 'Progress tabungan berhasil diperbarui!');
    })->name('savings.update');

    // ==========================================
    // 4. ROUTE BARU: Manajemen Kategori (FITUR C)
    // ==========================================

    // Update Nama Kategori
    Route::patch('/categories/{id}', function (\Illuminate\Http\Request $request, $id) {
        $cat = \App\Models\Category::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate(['name' => 'required|string|max:50']);
        $cat->update(['name' => $request->name]);

        return back()->with('success', 'Nama kategori berhasil diubah!');
    })->name('categories.update');

    // Hapus Kategori
    Route::delete('/categories/{id}', function ($id) {
        $cat = \App\Models\Category::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $cat->delete();

        return back()->with('success', 'Kategori berhasil dihapus!');
    })->name('categories.delete');

});
