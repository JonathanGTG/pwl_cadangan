<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use App\Models\Menu;
use App\Models\BranchStock;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $branch1 = Branch::create([
            'name'    => 'ELCO Banda Aceh',
            'address' => 'Jl. T. Daud Beureueh No. 1, Banda Aceh',
            'phone'   => '0651-123456',
            'status'  => 'active',
        ]);

        $branch2 = Branch::create([
            'name'    => 'ELCO Lhokseumawe',
            'address' => 'Jl. Merdeka No. 5, Lhokseumawe',
            'phone'   => '0645-654321',
            'status'  => 'active',
        ]);

        User::create([
            'name'      => 'Manager Pusat',
            'email'     => 'manager@elco.com',
            'password'  => bcrypt('password'),
            'role'      => 'manager',
            'branch_id' => null,
        ]);

        User::create([
            'name'      => 'Admin Banda Aceh',
            'email'     => 'admin1@elco.com',
            'password'  => bcrypt('password'),
            'role'      => 'admin',
            'branch_id' => $branch1->id,
        ]);

        User::create([
            'name'      => 'Kasir Banda Aceh',
            'email'     => 'kasir1@elco.com',
            'password'  => bcrypt('password'),
            'role'      => 'kasir',
            'branch_id' => $branch1->id,
        ]);

        $menus = [
            ['name' => 'Kopi Arabika Gayo',  'category' => 'minuman', 'base_price' => 28000],
            ['name' => 'Kopi Robusta',        'category' => 'minuman', 'base_price' => 22000],
            ['name' => 'Es Latte',            'category' => 'minuman', 'base_price' => 32000],
            ['name' => 'Matcha Latte',        'category' => 'minuman', 'base_price' => 35000],
            ['name' => 'Croissant',           'category' => 'makanan', 'base_price' => 25000],
            ['name' => 'Sandwich Tuna',       'category' => 'makanan', 'base_price' => 30000],
            ['name' => 'Cookies Coklat',      'category' => 'snack',   'base_price' => 15000],
        ];

        foreach ($menus as $menuData) {
            $menu = Menu::create([
                'name'         => $menuData['name'],
                'category'     => $menuData['category'],
                'base_price'   => $menuData['base_price'],
                'is_available' => true,
            ]);

            // Distribusi ke semua cabang
            foreach ([$branch1, $branch2] as $branch) {
                BranchStock::create([
                    'branch_id'    => $branch->id,
                    'menu_id'      => $menu->id,
                    'stock'        => rand(10, 50),
                    'custom_price' => null,
                ]);
            }
        }

    }
}
