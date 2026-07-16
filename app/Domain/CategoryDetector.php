<?php

namespace App\Domain;

class CategoryDetector
{
    // Peta kata kunci untuk kategori pengeluaran
    private array $expenseKeywords = [
        'makanan' => ['makan', 'nasi', 'warteg', 'restoran', 'kafe', 'kopi', 'minum', 'lapar', 'makanan', 'snack'],
        'transportasi' => ['bensin', 'transport', 'ojek', 'grab', 'gojek', 'taxi', 'bus', 'kereta', 'parkir', 'tol'],
        'edukasi' => ['buku', 'kuliah', 'sekolah', 'kursus', 'belajar', 'pendidikan', ' Modul', 'fotocopy'],
        'hiburan' => ['nonton', 'bioskop', 'game', 'streaming', 'netflix', 'spotify', 'hiburan', 'jalan-jalan'],
        'belanja' => ['belanja', 'baju', 'pakaian', 'shopping', 'mall', 'toko', 'online shop'],
        'kesehatan' => ['obat', 'dokter', 'rs', 'rumah sakit', 'klinik', 'vitamin', 'kesehatan'],
        'tagihan' => ['listrik', 'pln', 'air', 'pdam', 'internet', 'wifi', 'pulsa', 'kuota', 'tagihan'],
    ];

    // Peta kata kunci untuk kategori pemasukan
    private array $incomeKeywords = [
        'gaji' => ['gaji', 'salary', 'upah', 'honorer'],
        'bonus' => ['bonus', 'insentif', 'thr', 'reward'],
        'investasi' => ['dividen', 'investasi', 'saham', 'deposito', 'bunga'],
        'lainnya' => ['hadiah', 'kado', 'hibah', 'donasi'],
    ];

    /**
     * Deteksi kategori berdasarkan deskripsi dan tipe transaksi
     * POLIMORFISME: Method ini akan return kategori yang berbeda
     * berdasarkan input yang diberikan
     */
    public function detectCategory(string $description, string $type): string
    {
        $description = strtolower($description);

        if ($type === 'income') {
            return $this->detectIncomeCategory($description);
        } else {
            return $this->detectExpenseCategory($description);
        }
    }

    /**
     * ENCAPSULATION: Method private untuk deteksi pemasukan
     */
    private function detectIncomeCategory(string $description): string
    {
        foreach ($this->incomeKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($description, $keyword) !== false) {
                    return $category;
                }
            }
        }
        return 'lainnya'; // Default category
    }

    /**
     * ENCAPSULATION: Method private untuk deteksi pengeluaran
     */
    private function detectExpenseCategory(string $description): string
    {
        foreach ($this->expenseKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($description, $keyword) !== false) {
                    return $category;
                }
            }
        }
        return 'lainnya'; // Default category
    }

    /**
     * ABSTRACTION: Method untuk mendapatkan semua kategori
     */
    public function getAllCategories(): array
    {
        return [
            'income' => array_keys($this->incomeKeywords),
            'expense' => array_keys($this->expenseKeywords),
        ];
    }
}
