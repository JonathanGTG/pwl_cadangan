<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Ingredient;
use App\Models\IngredientStock;
use App\Models\Menu;
use App\Models\MenuIngredient;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch1 = Branch::updateOrCreate(
            ['name' => 'ELCO Banda Aceh'],
            [
                'address' => 'Jl. T. Daud Beureueh No. 1, Banda Aceh',
                'phone' => '0651-123456',
                'status' => 'active',
            ],
        );

        $branch2 = Branch::updateOrCreate(
            ['name' => 'ELCO Lhokseumawe'],
            [
                'address' => 'Jl. Merdeka No. 5, Lhokseumawe',
                'phone' => '0645-654321',
                'status' => 'active',
            ],
        );

        User::updateOrCreate(
            ['email' => 'manager@elco.com'],
            [
                'name' => 'Manager Pusat',
                'password' => bcrypt('password'),
                'role' => 'manager',
                'branch_id' => null,
            ],
        );

        User::updateOrCreate(
            ['email' => 'admin1@elco.com'],
            [
                'name' => 'Admin Banda Aceh',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'branch_id' => $branch1->id,
            ],
        );

        User::updateOrCreate(
            ['email' => 'kasir1@elco.com'],
            [
                'name' => 'Kasir Banda Aceh',
                'password' => bcrypt('password'),
                'role' => 'kasir',
                'branch_id' => $branch1->id,
            ],
        );

        MenuIngredient::query()->delete();
        BranchStock::query()->delete();
        IngredientStock::query()->delete();
        Ingredient::query()->delete();
        $existingMenus = Menu::withTrashed()->orderBy('id')->get();

        $branches = [$branch1, $branch2];

        $ingredients = [
            ['code' => 'BHN-001', 'name' => 'Biji Kopi Arabika', 'category' => 'Kopi', 'unit' => 'gram', 'stock' => 500, 'minimum' => 500],
            ['code' => 'BHN-002', 'name' => 'Biji Kopi Robusta', 'category' => 'Kopi', 'unit' => 'gram', 'stock' => 300, 'minimum' => 300],
            ['code' => 'BHN-003', 'name' => 'Susu Full Cream', 'category' => 'Susu & Dairy', 'unit' => 'ml', 'stock' => 1000, 'minimum' => 1000],
            ['code' => 'BHN-004', 'name' => 'Oat Milk', 'category' => 'Susu & Dairy', 'unit' => 'ml', 'stock' => 500, 'minimum' => 500],
            ['code' => 'BHN-005', 'name' => 'Heavy Cream', 'category' => 'Susu & Dairy', 'unit' => 'ml', 'stock' => 200, 'minimum' => 200],
            ['code' => 'BHN-006', 'name' => 'Sirup Gula Aren', 'category' => 'Sirup & Perisa', 'unit' => 'ml', 'stock' => 200, 'minimum' => 200],
            ['code' => 'BHN-007', 'name' => 'Sirup Vanilla', 'category' => 'Sirup & Perisa', 'unit' => 'ml', 'stock' => 100, 'minimum' => 100],
            ['code' => 'BHN-008', 'name' => 'Sirup Karamel', 'category' => 'Sirup & Perisa', 'unit' => 'ml', 'stock' => 100, 'minimum' => 100],
            ['code' => 'BHN-009', 'name' => 'Matcha Powder', 'category' => 'Sirup & Perisa', 'unit' => 'gram', 'stock' => 50, 'minimum' => 50],
            ['code' => 'BHN-010', 'name' => 'Taro Powder', 'category' => 'Sirup & Perisa', 'unit' => 'gram', 'stock' => 50, 'minimum' => 50],
            ['code' => 'BHN-011', 'name' => 'Coklat Bubuk', 'category' => 'Sirup & Perisa', 'unit' => 'gram', 'stock' => 100, 'minimum' => 100],
            ['code' => 'BHN-012', 'name' => 'Dark Chocolate Block', 'category' => 'Sirup & Perisa', 'unit' => 'gram', 'stock' => 100, 'minimum' => 100],
            ['code' => 'BHN-013', 'name' => 'Teh Hitam (teabag)', 'category' => 'Sirup & Perisa', 'unit' => 'pcs', 'stock' => 20, 'minimum' => 20],
            ['code' => 'BHN-014', 'name' => 'Bubuk Kopi Espresso', 'category' => 'Kopi', 'unit' => 'gram', 'stock' => 100, 'minimum' => 100],
            ['code' => 'BHN-015', 'name' => 'Mascarpone', 'category' => 'Susu & Dairy', 'unit' => 'gram', 'stock' => 100, 'minimum' => 100],
            ['code' => 'BHN-016', 'name' => 'Ladyfinger Biscuit', 'category' => 'Bahan Kering', 'unit' => 'gram', 'stock' => 50, 'minimum' => 50],
            ['code' => 'BHN-017', 'name' => 'Stroberi', 'category' => 'Buah', 'unit' => 'gram', 'stock' => 100, 'minimum' => 100],
            ['code' => 'BHN-018', 'name' => 'Lemon', 'category' => 'Buah', 'unit' => 'pcs', 'stock' => 5, 'minimum' => 5],
            ['code' => 'BHN-019', 'name' => 'Kopi Instan', 'category' => 'Kopi', 'unit' => 'gram', 'stock' => 50, 'minimum' => 50],
            ['code' => 'BHN-020', 'name' => 'Es Batu', 'category' => 'Bahan Kering', 'unit' => 'gram', 'stock' => 1000, 'minimum' => 1000],
            ['code' => 'BHN-021', 'name' => 'Air Mineral / Filtered', 'category' => 'Bahan Kering', 'unit' => 'ml', 'stock' => 2000, 'minimum' => 2000],
            ['code' => 'BHN-022', 'name' => 'Gula Pasir', 'category' => 'Bahan Kering', 'unit' => 'gram', 'stock' => 0, 'minimum' => 0],
        ];

        $ingredientMap = [];

        foreach ($ingredients as $ingredientData) {
            $ingredient = Ingredient::create([
                'kode_bahan' => $ingredientData['code'],
                'nama_bahan' => $ingredientData['name'],
                'kategori' => $ingredientData['category'],
                'satuan' => $ingredientData['unit'],
            ]);

            $ingredientMap[$ingredientData['code']] = $ingredient;

            foreach ($branches as $branch) {
                IngredientStock::create([
                    'branch_id' => $branch->id,
                    'ingredient_id' => $ingredient->id,
                    'stok_sekarang' => $ingredientData['stock'],
                    'stok_minimum' => $ingredientData['minimum'],
                ]);
            }
        }

        $menus = [
            ['code' => 'MNU-C001', 'name' => 'Espresso', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 18000, 'description' => 'Kopi espresso murni tanpa campuran'],
            ['code' => 'MNU-C002', 'name' => 'Americano', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 22000, 'description' => 'Espresso dengan air panas'],
            ['code' => 'MNU-C003', 'name' => 'Cappuccino', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 28000, 'description' => 'Espresso, steamed milk, foam'],
            ['code' => 'MNU-C004', 'name' => 'Latte', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 30000, 'description' => 'Espresso dengan susu lembut'],
            ['code' => 'MNU-C005', 'name' => 'Flat White', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 30000, 'description' => 'Double espresso, microfoam susu'],
            ['code' => 'MNU-C006', 'name' => 'Macchiato', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 25000, 'description' => 'Espresso dengan sedikit susu'],
            ['code' => 'MNU-C007', 'name' => 'Kopi Susu Gula Aren', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 28000, 'description' => 'Kopi susu khas dengan gula aren'],
            ['code' => 'MNU-C008', 'name' => 'Cold Brew', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 32000, 'description' => 'Kopi seduh dingin 12 jam'],
            ['code' => 'MNU-C009', 'name' => 'V60 Pour Over', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 35000, 'description' => 'Manual brew metode V60'],
            ['code' => 'MNU-C010', 'name' => 'Vietnam Drip', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 25000, 'description' => 'Kopi tetes ala Vietnam'],
            ['code' => 'MNU-N001', 'name' => 'Matcha Latte', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 30000, 'description' => 'Matcha premium dengan susu'],
            ['code' => 'MNU-N002', 'name' => 'Chocolate', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 28000, 'description' => 'Dark chocolate blend'],
            ['code' => 'MNU-N003', 'name' => 'Taro Latte', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 30000, 'description' => 'Taro creamy dengan susu'],
            ['code' => 'MNU-N004', 'name' => 'Oat Milk Latte', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 32000, 'description' => 'Latte berbasis oat milk'],
            ['code' => 'MNU-N005', 'name' => 'Lemon Tea', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 20000, 'description' => 'Teh segar dengan perasan lemon'],
            ['code' => 'MNU-N006', 'name' => 'Strawberry Smoothie', 'category' => 'minuman', 'stock_type' => 'bahan_baku', 'price' => 28000, 'description' => 'Smoothie stroberi segar'],
            ['code' => 'MNU-D001', 'name' => 'Croissant', 'category' => 'makanan', 'stock_type' => 'kuantitas_jadi', 'price' => 20000, 'description' => 'Croissant butter renyah'],
            ['code' => 'MNU-D002', 'name' => 'Muffin Coklat', 'category' => 'makanan', 'stock_type' => 'kuantitas_jadi', 'price' => 18000, 'description' => 'Muffin lembut rasa coklat'],
            ['code' => 'MNU-D003', 'name' => 'Cheesecake', 'category' => 'makanan', 'stock_type' => 'kuantitas_jadi', 'price' => 35000, 'description' => 'New York style cheesecake'],
            ['code' => 'MNU-D004', 'name' => 'Tiramisu', 'category' => 'makanan', 'stock_type' => 'kuantitas_jadi', 'price' => 38000, 'description' => 'Tiramisu klasik Italia'],
            ['code' => 'MNU-D005', 'name' => 'Waffle', 'category' => 'makanan', 'stock_type' => 'kuantitas_jadi', 'price' => 32000, 'description' => 'Waffle garing dengan topping'],
            ['code' => 'MNU-D006', 'name' => 'Banana Bread', 'category' => 'makanan', 'stock_type' => 'kuantitas_jadi', 'price' => 22000, 'description' => 'Roti pisang homemade'],
            ['code' => 'MNU-D007', 'name' => 'Cinnamon Roll', 'category' => 'makanan', 'stock_type' => 'kuantitas_jadi', 'price' => 25000, 'description' => 'Roti gulung kayu manis'],
            ['code' => 'MNU-D008', 'name' => 'Brownies', 'category' => 'makanan', 'stock_type' => 'kuantitas_jadi', 'price' => 20000, 'description' => 'Brownies fudgy coklat'],
            ['code' => 'MNU-D009', 'name' => 'Panna Cotta', 'category' => 'makanan', 'stock_type' => 'kuantitas_jadi', 'price' => 33000, 'description' => 'Puding susu Italia lembut'],
            ['code' => 'MNU-D010', 'name' => 'Crème Brûlée', 'category' => 'makanan', 'stock_type' => 'kuantitas_jadi', 'price' => 40000, 'description' => 'Custard karamel panggang'],
        ];

        $menuMap = [];

        foreach ($menus as $index => $menuData) {
            $menu = $existingMenus->get($index);

            if ($menu) {
                $menu->restore();
                $menu->update([
                    'name' => $menuData['name'],
                    'description' => $menuData['description'],
                    'category' => $menuData['category'],
                    'stock_type' => $menuData['stock_type'],
                    'base_price' => $menuData['price'],
                    'image' => null,
                    'is_available' => true,
                ]);
            } else {
                $menu = Menu::create([
                    'name' => $menuData['name'],
                    'description' => $menuData['description'],
                    'category' => $menuData['category'],
                    'stock_type' => $menuData['stock_type'],
                    'base_price' => $menuData['price'],
                    'is_available' => true,
                ]);
            }

            $menuMap[$menuData['code']] = $menu;

            foreach ($branches as $branch) {
                BranchStock::create([
                    'branch_id' => $branch->id,
                    'menu_id' => $menu->id,
                    'stock' => $menuData['stock_type'] === 'kuantitas_jadi' ? 20 : 0,
                    'custom_price' => null,
                ]);
            }
        }

        $existingMenus->slice(count($menus))->each(function (Menu $menu) {
            $menu->delete();
        });

        $recipes = [
            ['menu_code' => 'MNU-C001', 'ingredient_code' => 'BHN-001', 'amount' => 18],
            ['menu_code' => 'MNU-C001', 'ingredient_code' => 'BHN-021', 'amount' => 30],
            ['menu_code' => 'MNU-C002', 'ingredient_code' => 'BHN-001', 'amount' => 18],
            ['menu_code' => 'MNU-C002', 'ingredient_code' => 'BHN-021', 'amount' => 150],
            ['menu_code' => 'MNU-C002', 'ingredient_code' => 'BHN-020', 'amount' => 100],
            ['menu_code' => 'MNU-C003', 'ingredient_code' => 'BHN-001', 'amount' => 18],
            ['menu_code' => 'MNU-C003', 'ingredient_code' => 'BHN-003', 'amount' => 100],
            ['menu_code' => 'MNU-C004', 'ingredient_code' => 'BHN-001', 'amount' => 18],
            ['menu_code' => 'MNU-C004', 'ingredient_code' => 'BHN-003', 'amount' => 150],
            ['menu_code' => 'MNU-C004', 'ingredient_code' => 'BHN-020', 'amount' => 100],
            ['menu_code' => 'MNU-C005', 'ingredient_code' => 'BHN-001', 'amount' => 18],
            ['menu_code' => 'MNU-C005', 'ingredient_code' => 'BHN-003', 'amount' => 120],
            ['menu_code' => 'MNU-C006', 'ingredient_code' => 'BHN-001', 'amount' => 18],
            ['menu_code' => 'MNU-C006', 'ingredient_code' => 'BHN-003', 'amount' => 30],
            ['menu_code' => 'MNU-C006', 'ingredient_code' => 'BHN-020', 'amount' => 100],
            ['menu_code' => 'MNU-C007', 'ingredient_code' => 'BHN-001', 'amount' => 18],
            ['menu_code' => 'MNU-C007', 'ingredient_code' => 'BHN-003', 'amount' => 120],
            ['menu_code' => 'MNU-C007', 'ingredient_code' => 'BHN-006', 'amount' => 30],
            ['menu_code' => 'MNU-C007', 'ingredient_code' => 'BHN-020', 'amount' => 100],
            ['menu_code' => 'MNU-C008', 'ingredient_code' => 'BHN-002', 'amount' => 30],
            ['menu_code' => 'MNU-C008', 'ingredient_code' => 'BHN-021', 'amount' => 200],
            ['menu_code' => 'MNU-C008', 'ingredient_code' => 'BHN-020', 'amount' => 100],
            ['menu_code' => 'MNU-C009', 'ingredient_code' => 'BHN-001', 'amount' => 15],
            ['menu_code' => 'MNU-C009', 'ingredient_code' => 'BHN-021', 'amount' => 250],
            ['menu_code' => 'MNU-C010', 'ingredient_code' => 'BHN-002', 'amount' => 20],
            ['menu_code' => 'MNU-C010', 'ingredient_code' => 'BHN-003', 'amount' => 50],
            ['menu_code' => 'MNU-C010', 'ingredient_code' => 'BHN-021', 'amount' => 100],
            ['menu_code' => 'MNU-C010', 'ingredient_code' => 'BHN-020', 'amount' => 100],
            ['menu_code' => 'MNU-N001', 'ingredient_code' => 'BHN-009', 'amount' => 5],
            ['menu_code' => 'MNU-N001', 'ingredient_code' => 'BHN-003', 'amount' => 200],
            ['menu_code' => 'MNU-N001', 'ingredient_code' => 'BHN-022', 'amount' => 15],
            ['menu_code' => 'MNU-N001', 'ingredient_code' => 'BHN-020', 'amount' => 100],
            ['menu_code' => 'MNU-N002', 'ingredient_code' => 'BHN-011', 'amount' => 20],
            ['menu_code' => 'MNU-N002', 'ingredient_code' => 'BHN-003', 'amount' => 200],
            ['menu_code' => 'MNU-N002', 'ingredient_code' => 'BHN-022', 'amount' => 15],
            ['menu_code' => 'MNU-N002', 'ingredient_code' => 'BHN-020', 'amount' => 100],
            ['menu_code' => 'MNU-N003', 'ingredient_code' => 'BHN-010', 'amount' => 20],
            ['menu_code' => 'MNU-N003', 'ingredient_code' => 'BHN-003', 'amount' => 200],
            ['menu_code' => 'MNU-N003', 'ingredient_code' => 'BHN-022', 'amount' => 15],
            ['menu_code' => 'MNU-N003', 'ingredient_code' => 'BHN-020', 'amount' => 100],
            ['menu_code' => 'MNU-N004', 'ingredient_code' => 'BHN-001', 'amount' => 18],
            ['menu_code' => 'MNU-N004', 'ingredient_code' => 'BHN-004', 'amount' => 200],
            ['menu_code' => 'MNU-N004', 'ingredient_code' => 'BHN-020', 'amount' => 100],
            ['menu_code' => 'MNU-N005', 'ingredient_code' => 'BHN-013', 'amount' => 1],
            ['menu_code' => 'MNU-N005', 'ingredient_code' => 'BHN-018', 'amount' => 1],
            ['menu_code' => 'MNU-N005', 'ingredient_code' => 'BHN-022', 'amount' => 15],
            ['menu_code' => 'MNU-N005', 'ingredient_code' => 'BHN-020', 'amount' => 100],
            ['menu_code' => 'MNU-N006', 'ingredient_code' => 'BHN-017', 'amount' => 100],
            ['menu_code' => 'MNU-N006', 'ingredient_code' => 'BHN-003', 'amount' => 100],
            ['menu_code' => 'MNU-N006', 'ingredient_code' => 'BHN-022', 'amount' => 20],
            ['menu_code' => 'MNU-N006', 'ingredient_code' => 'BHN-020', 'amount' => 100],
        ];

        foreach ($recipes as $recipeData) {
            MenuIngredient::create([
                'menu_id' => $menuMap[$recipeData['menu_code']]->id,
                'ingredient_id' => $ingredientMap[$recipeData['ingredient_code']]->id,
                'jumlah_per_sajian' => $recipeData['amount'],
            ]);
        }
    }
}