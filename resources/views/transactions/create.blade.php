<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Transaksi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Notifikasi Sukses --}}
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Info Auto-Categorization --}}
                    <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700">
                        <p class="font-semibold">✨ Kategorisasi Otomatis</p>
                        <p class="text-sm mt-1">Sistem akan otomatis mendeteksi kategori berdasarkan kata kunci di deskripsi.</p>
                    </div>

                    <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6">
                        @csrf

                        {{-- 1. Jenis Transaksi --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Transaksi</label>
                            <div class="mt-2 flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="type" value="income" class="text-emerald-600 focus:ring-emerald-500" checked>
                                    <span class="ml-2 text-gray-700">Pemasukan (Uang Masuk)</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="type" value="expense" class="text-red-600 focus:ring-red-500">
                                    <span class="ml-2 text-gray-700">Pengeluaran (Uang Keluar)</span>
                                </label>
                            </div>
                        </div>

                        {{-- 2. Deskripsi (PENTING untuk auto-categorization) --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Deskripsi Transaksi <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="description"
                                   id="description"
                                   value="{{ old('description') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Contoh: Makan siang di warteg, Beli bensin motor, Gaji bulan Juli"
                                   required>
                            <p class="text-xs text-gray-500 mt-1">
                                💡 Tip: Gunakan kata kunci seperti "makan", "bensin", "gaji", "belanja" untuk kategorisasi otomatis
                            </p>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- 3. Nominal & Tanggal --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                                <input type="number"
                                       name="amount"
                                       id="amount"
                                       value="{{ old('amount') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       placeholder="Contoh: 50000"
                                       required>
                                @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="transaction_date" class="block text-sm font-medium text-gray-700">Tanggal</label>
                                <input type="date"
                                       name="transaction_date"
                                       id="transaction_date"
                                       value="{{ old('transaction_date', date('Y-m-d')) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       required>
                                @error('transaction_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <hr class="border-gray-200">

                        {{-- 4. Field Khusus OOP --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg">
                            <div>
                                <label for="sumber_dana" class="block text-sm font-medium text-gray-700">
                                    Sumber Dana <span class="text-xs text-gray-500">(Khusus Pemasukan)</span>
                                </label>
                                <input type="text"
                                       name="sumber_dana"
                                       id="sumber_dana"
                                       value="{{ old('sumber_dana') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                                       placeholder="Misal: Bank BCA, Cash">
                            </div>
                            <div>
                                <label for="tingkat_urgensi" class="block text-sm font-medium text-gray-700">
                                    Tingkat Urgensi <span class="text-xs text-gray-500">(Khusus Pengeluaran)</span>
                                </label>
                                <select name="tingkat_urgensi"
                                        id="tingkat_urgensi"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                    <option value="low">Rendah (Keinginan)</option>
                                    <option value="medium" selected>Sedang (Kebutuhan)</option>
                                    <option value="high">Tinggi (Darurat)</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Simpan Transaksi
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
