<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'description', 'category', 'stock_type',
        'base_price', 'image', 'is_available',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function branchStocks()
    {
        return $this->hasMany(BranchStock::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Resep bahan baku menu ini.
     * Hanya relevan jika stock_type = 'bahan_baku' (minuman).
     */
    public function ingredients()
    {
        return $this->hasMany(MenuIngredient::class);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    /** Apakah stok menu ini dihitung dari bahan baku (minuman)? */
    public function isIngredientBased(): bool
    {
        return $this->stock_type === 'bahan_baku';
    }

    /** Apakah stok menu ini dihitung per kuantitas produk jadi (makanan/snack)? */
    public function isQuantityBased(): bool
    {
        return $this->stock_type === 'kuantitas_jadi';
    }

    public function availablePortions(int $branchId): int
    {
        if (! $this->isIngredientBased()) {
            return 0;
        }

        $ingredients = $this->relationLoaded('ingredients')
            ? $this->ingredients
            : $this->ingredients()->get();

        if ($ingredients->isEmpty()) {
            return 0;
        }

        $ingredientStocks = IngredientStock::where('branch_id', $branchId)
            ->pluck('stok_sekarang', 'ingredient_id');

        $minPortions = null;
        foreach ($ingredients as $mi) {
            $perServing = (float) $mi->jumlah_per_sajian;
            if ($perServing <= 0) {
                return 0;
            }

            $available = (float) ($ingredientStocks[$mi->ingredient_id] ?? 0);
            $portions  = (int) floor($available / $perServing);
            $minPortions = $minPortions === null ? $portions : min($minPortions, $portions);
        }

        return max(0, (int) ($minPortions ?? 0));
    }

    /**
     * Cek apakah bahan baku di cabang tertentu mencukupi untuk qty porsi.
     * Hanya dipakai untuk menu berjenis bahan_baku.
     *
     * @param  int  $branchId
     * @param  int  $qty        jumlah porsi
     * @return array{ok: bool, kekurangan: array}
     */
    public function checkIngredients(int $branchId, int $qty): array
    {
        $kekurangan = [];

        foreach ($this->ingredients()->with('ingredient')->get() as $mi) {
            $dibutuhkan = $mi->jumlah_per_sajian * $qty;

            $stok = IngredientStock::where('branch_id', $branchId)
                ->where('ingredient_id', $mi->ingredient_id)
                ->first();

            $tersedia = $stok?->stok_sekarang ?? 0;

            if ($tersedia < $dibutuhkan) {
                $kekurangan[] = [
                    'bahan'     => $mi->ingredient->nama_bahan,
                    'dibutuhkan' => $dibutuhkan,
                    'tersedia'   => $tersedia,
                    'satuan'     => $mi->ingredient->satuan,
                ];
            }
        }

        return [
            'ok'         => empty($kekurangan),
            'kekurangan' => $kekurangan,
        ];
    }

    /**
     * Kurangi stok bahan baku di cabang setelah transaksi berhasil.
     * Dipanggil di dalam DB::transaction.
     */
    public function deductIngredients(int $branchId, int $qty): void
    {
        foreach ($this->ingredients()->with('ingredient')->get() as $mi) {
            $dibutuhkan = $mi->jumlah_per_sajian * $qty;

            IngredientStock::where('branch_id', $branchId)
                ->where('ingredient_id', $mi->ingredient_id)
                ->decrement('stok_sekarang', $dibutuhkan);
        }
    }

    /**
     * Kembalikan stok bahan baku ke cabang saat transaksi dibatalkan.
     * Dipanggil di dalam DB::transaction.
     */
    public function restoreIngredients(int $branchId, int $qty): void
    {
        foreach ($this->ingredients()->with('ingredient')->get() as $mi) {
            $jumlah = $mi->jumlah_per_sajian * $qty;

            IngredientStock::where('branch_id', $branchId)
                ->where('ingredient_id', $mi->ingredient_id)
                ->increment('stok_sekarang', $jumlah);
        }
    }
}
