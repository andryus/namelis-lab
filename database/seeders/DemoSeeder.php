<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        foreach (['admin', 'lab', 'dentist', 'courier'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $admin = User::firstOrCreate(['email' => 'admin@namelis.pt'], [
            'name' => 'Admin Namelis', 'password' => Hash::make('demo1234'),
        ]);
        $admin->assignRole('admin');

        $lab = User::firstOrCreate(['email' => 'lab@namelis.pt'], [
            'name' => 'Técnico Laboratório', 'password' => Hash::make('demo1234'),
        ]);
        $lab->assignRole('lab');

        $dentist = User::firstOrCreate(['email' => 'dentista@clinica-demo.pt'], [
            'name' => 'Dr. António Ferreira', 'password' => Hash::make('demo1234'),
        ]);
        $dentist->assignRole('dentist');

        $courier = User::firstOrCreate(['email' => 'estafeta@namelis.pt'], [
            'name' => 'Miguel Estafeta', 'password' => Hash::make('demo1234'),
        ]);
        $courier->assignRole('courier');

        // Work categories
        $categories = [
            ['name' => 'Prótese Fixa', 'children' => [
                ['name' => 'Coroa Total',  'materials' => ['Zircónia Multilayer', 'Ceramo-Metálica', 'Full Zircon']],
                ['name' => 'Faceta',       'materials' => ['Cerâmica', 'Compósito de Laboratório']],
                ['name' => 'Inlay/Onlay',  'materials' => ['Zircónia', 'Cerâmica']],
            ]],
            ['name' => 'Prótese Removível', 'children' => [
                ['name' => 'Esquelética',  'materials' => ['Com grampos CoCr', 'Com ataches de precisão']],
                ['name' => 'Acrílica',     'materials' => ['Total superior', 'Total inferior']],
            ]],
            ['name' => 'Implantologia', 'children' => [
                ['name' => 'Coroa sobre implante', 'materials' => ['Zircónia Multilayer', 'Full Zircon']],
            ]],
            ['name' => 'Ortodontia', 'children' => [
                ['name' => 'Contenção', 'materials' => ['Fixa (fibra de vidro)', 'Removível (acrílica)']],
            ]],
        ];

        $prices = ['Zircónia Multilayer' => 95.00, 'Ceramo-Metálica' => 65.00, 'Full Zircon' => 80.00, 'default' => 55.00];

        foreach ($categories as $catData) {
            $cat = \App\Models\WorkCategory::firstOrCreate(
                ['name' => $catData['name'], 'parent_id' => null],
                ['level' => 1, 'sort_order' => 0]
            );
            foreach ($catData['children'] as $typeData) {
                $type = \App\Models\WorkCategory::firstOrCreate(
                    ['name' => $typeData['name'], 'parent_id' => $cat->id],
                    ['level' => 2, 'sort_order' => 0]
                );
                foreach ($typeData['materials'] as $mat) {
                    \App\Models\WorkCategory::firstOrCreate(
                        ['name' => $mat, 'parent_id' => $type->id],
                        ['level' => 3, 'base_price' => $prices[$mat] ?? $prices['default'], 'sort_order' => 0]
                    );
                }
            }
        }

        // Clinic
        $clinic = \App\Models\Clinic::firstOrCreate(['user_id' => $dentist->id], [
            'name' => 'Clínica Dentária Demo', 'nif' => '123456789',
            'address' => 'Rua da Dentística, 42', 'city' => 'Porto',
            'phone' => '+351 220 000 000', 'doctor_name' => 'Dr. António Ferreira',
            'license_number' => 'OMD-12345', 'approved' => true,
        ]);

        // Sample orders
        $statuses = ['at_lab', 'in_production', 'awaiting_delivery', 'delivered'];
        foreach (range(1, 6) as $i) {
            \App\Models\Order::firstOrCreate(
                ['reference' => 'NML-2025-' . str_pad($i, 5, '0', STR_PAD_LEFT)],
                [
                    'clinic_id' => $clinic->id, 'assigned_tech_id' => $lab->id,
                    'patient_ref' => 'PAC-' . strtoupper(substr(md5($i), 0, 6)),
                    'impression_type' => $i % 2 === 0 ? 'digital' : 'physical',
                    'status' => $statuses[($i - 1) % count($statuses)],
                    'requested_delivery_date' => now()->addDays($i * 3)->toDateString(),
                    'estimated_price' => 95.00,
                    'barcode' => 'NML' . str_pad($i, 8, '0', STR_PAD_LEFT),
                    'created_at' => now()->subDays(rand(5, 60)),
                ]
            );
        }
    }
}
