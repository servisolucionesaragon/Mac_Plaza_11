<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Cliente;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuarios ─────────────────────────────────────────────────────
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@tienda.com',
            'password' => Hash::make('password'),
            'rol'      => 'admin',
        ]);

        User::create([
            'name'     => 'Juan Vendedor',
            'email'    => 'vendedor@tienda.com',
            'password' => Hash::make('password'),
            'rol'      => 'vendedor',
        ]);

        User::create([
            'name'     => 'Carlos Técnico',
            'email'    => 'tecnico@tienda.com',
            'password' => Hash::make('password'),
            'rol'      => 'tecnico',
        ]);

        // ── Categorías ───────────────────────────────────────────────────
        $cats = ['Smartphones', 'Tablets', 'Accesorios', 'Audífonos', 'Cargadores', 'Cases y Fundas', 'Repuestos'];
        foreach ($cats as $cat) {
            Categoria::create(['nombre' => $cat]);
        }

        // ── Marcas ───────────────────────────────────────────────────────
        $marcas = ['Samsung', 'Apple', 'Xiaomi', 'Motorola', 'Huawei', 'OPPO', 'Realme', 'OnePlus'];
        foreach ($marcas as $marca) {
            Marca::create(['nombre' => $marca]);
        }

        // ── Productos de ejemplo ─────────────────────────────────────────
        $productos = [
            ['codigo'=>'SAM-A54-128', 'nombre'=>'Samsung Galaxy A54', 'categoria'=>1, 'marca'=>1, 'modelo'=>'A54', 'almacenamiento'=>'128GB', 'ram'=>'8GB', 'precio_compra'=>650, 'precio_venta'=>899, 'stock'=>15],
            ['codigo'=>'SAM-S24-256', 'nombre'=>'Samsung Galaxy S24', 'categoria'=>1, 'marca'=>1, 'modelo'=>'S24', 'almacenamiento'=>'256GB', 'ram'=>'12GB', 'precio_compra'=>950, 'precio_venta'=>1299, 'stock'=>8],
            ['codigo'=>'APP-IPH15-128','nombre'=>'iPhone 15', 'categoria'=>1, 'marca'=>2, 'modelo'=>'15', 'almacenamiento'=>'128GB', 'ram'=>'6GB', 'precio_compra'=>2500, 'precio_venta'=>3499, 'stock'=>5],
            ['codigo'=>'XIA-13T-256', 'nombre'=>'Xiaomi 13T', 'categoria'=>1, 'marca'=>3, 'modelo'=>'13T', 'almacenamiento'=>'256GB', 'ram'=>'12GB', 'precio_compra'=>700, 'precio_venta'=>999, 'stock'=>12],
            ['codigo'=>'MOT-G84-256', 'nombre'=>'Motorola Moto G84', 'categoria'=>1, 'marca'=>4, 'modelo'=>'G84', 'almacenamiento'=>'256GB', 'ram'=>'12GB', 'precio_compra'=>480, 'precio_venta'=>699, 'stock'=>10],
            ['codigo'=>'AUD-SAM-TW', 'nombre'=>'Samsung Galaxy Buds2', 'categoria'=>4, 'marca'=>1, 'modelo'=>'Buds2', 'precio_compra'=>120, 'precio_venta'=>199, 'stock'=>20],
            ['codigo'=>'CAR-USB-C-65', 'nombre'=>'Cargador USB-C 65W', 'categoria'=>5, 'marca'=>3, 'precio_compra'=>18, 'precio_venta'=>35, 'stock'=>50],
            ['codigo'=>'CASE-IPH15', 'nombre'=>'Case iPhone 15 Pro', 'categoria'=>6, 'marca'=>2, 'precio_compra'=>8, 'precio_venta'=>25, 'stock'=>30],
        ];

        foreach ($productos as $p) {
            Producto::create([
                'codigo'        => $p['codigo'],
                'nombre'        => $p['nombre'],
                'categoria_id'  => $p['categoria'] ?? 3,
                'marca_id'      => $p['marca'],
                'modelo'        => $p['modelo'] ?? null,
                'almacenamiento'=> $p['almacenamiento'] ?? null,
                'ram'           => $p['ram'] ?? null,
                'precio_compra' => $p['precio_compra'],
                'precio_venta'  => $p['precio_venta'],
                'stock'         => $p['stock'],
                'stock_minimo'  => 3,
                'condicion'     => 'nuevo',
            ]);
        }

        // ── Clientes de ejemplo ──────────────────────────────────────────
        $clientes = [
            ['nombre'=>'María',   'apellido'=>'García',   'email'=>'maria.garcia@gmail.com',   'telefono'=>'987654321', 'dni'=>'45123456'],
            ['nombre'=>'Carlos',  'apellido'=>'López',    'email'=>'carlos.lopez@gmail.com',    'telefono'=>'965432187', 'dni'=>'32145678'],
            ['nombre'=>'Ana',     'apellido'=>'Martínez', 'email'=>'ana.martinez@hotmail.com',  'telefono'=>'974561230', 'dni'=>'56789012'],
            ['nombre'=>'Pedro',   'apellido'=>'Sánchez',  'email'=>'pedro.sanchez@outlook.com', 'telefono'=>'912345678', 'dni'=>'78901234'],
            ['nombre'=>'Lucía',   'apellido'=>'Torres',   'email'=>null,                         'telefono'=>'998765432', 'dni'=>'89012345'],
        ];

        foreach ($clientes as $c) {
            Cliente::create(array_merge($c, ['tipo'=>'particular', 'ciudad'=>'Lima']));
        }

        // Datos demo: ventas, reparaciones, clientes/productos adicionales
        $this->call(DemoDataSeeder::class);
    }
}
