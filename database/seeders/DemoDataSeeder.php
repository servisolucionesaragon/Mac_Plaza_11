<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\MetodoPago;
use App\Models\Reparacion;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Limpiar tablas transaccionales (orden: FK hijos primero) ─────
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('detalle_ventas')->truncate();
        DB::table('ventas')->truncate();
        DB::table('reparaciones')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $admin    = User::where('rol', 'admin')->first();
        $vendedor = User::where('rol', 'vendedor')->first();
        $tecnico  = User::where('rol', 'tecnico')->first();

        // ── Clientes adicionales (firstOrCreate para evitar duplicados) ──
        $nuevosClientes = [
            ['nombre'=>'Roberto', 'apellido'=>'Flores',  'email'=>'roberto.flores@gmail.com',    'telefono'=>'945678901', 'dni'=>'12345671', 'tipo'=>'particular', 'ciudad'=>'Lima'],
            ['nombre'=>'Elena',   'apellido'=>'Vásquez', 'email'=>'elena.vasquez@techcorp.pe',    'telefono'=>'934567890', 'dni'=>'23456782', 'tipo'=>'empresa',   'ciudad'=>'Lima', 'empresa'=>'TechCorp SAC',   'ruc'=>'20512345671'],
            ['nombre'=>'Miguel',  'apellido'=>'Quispe',  'email'=>'miguel.quispe@outlook.com',    'telefono'=>'923456789', 'dni'=>'34567893', 'tipo'=>'particular', 'ciudad'=>'Lima'],
            ['nombre'=>'Sofía',   'apellido'=>'Mendoza', 'email'=>'sofia.mendoza@gmail.com',      'telefono'=>'912345670', 'dni'=>'45678904', 'tipo'=>'particular', 'ciudad'=>'Lima'],
            ['nombre'=>'Diego',   'apellido'=>'Herrera', 'email'=>'diego.herrera@movilstore.pe',  'telefono'=>'901234567', 'dni'=>'56789015', 'tipo'=>'empresa',   'ciudad'=>'Lima', 'empresa'=>'MovilStore EIRL','ruc'=>'20612345672'],
        ];
        foreach ($nuevosClientes as $c) {
            Cliente::firstOrCreate(['dni' => $c['dni']], $c);
        }

        // ── Productos adicionales (firstOrCreate para evitar duplicados) ─
        $nuevosProductos = [
            ['codigo'=>'HUA-NOV11-128','nombre'=>'Huawei Nova 11','categoria_id'=>1,'marca_id'=>5,'modelo'=>'Nova 11','almacenamiento'=>'128GB','ram'=>'8GB', 'precio_compra'=>580,'precio_venta'=>849,'stock'=>18,'stock_minimo'=>3,'condicion'=>'nuevo'],
            ['codigo'=>'OPP-A58-128',  'nombre'=>'OPPO A58',     'categoria_id'=>1,'marca_id'=>6,'modelo'=>'A58',    'almacenamiento'=>'128GB','ram'=>'6GB', 'precio_compra'=>420,'precio_venta'=>629,'stock'=>20,'stock_minimo'=>3,'condicion'=>'nuevo'],
        ];
        foreach ($nuevosProductos as $p) {
            Producto::firstOrCreate(['codigo' => $p['codigo']], $p);
        }

        // ── Cargar colecciones actualizadas ──────────────────────────────
        $productos = Producto::all()->keyBy('codigo');
        $clientes  = Cliente::all()->values(); // índices 0 … N-1

        // ── VENTAS — contador manual para evitar race-condition ───────────
        $ventaNum = 1;   // siempre empieza en 1 porque truncamos arriba

        // Mapa nombre (lowercase) → id del catálogo de métodos de pago.
        // Si algún método usado por el demo (ej. yape/plin) ya no existe en el
        // catálogo activo, se crea automáticamente como inactivo (mismo criterio
        // que el backfill de la migración 2026_07_08_000006).
        $metodosPagoMap = MetodoPago::all()->keyBy(fn($m) => strtolower($m->nombre));
        $resolverMetodoPagoId = function (string $nombre) use (&$metodosPagoMap) {
            $key = strtolower($nombre);
            if (!isset($metodosPagoMap[$key])) {
                $metodosPagoMap[$key] = MetodoPago::create(['nombre' => ucfirst($nombre), 'activo' => false]);
            }
            return $metodosPagoMap[$key]->id;
        };

        $ventas = [
            /* Noviembre 2025 */
            ['fecha'=>'2025-11-05 10:20:00','c'=>0,'u'=>$vendedor->id,'m'=>'efectivo',
             'items'=>[['cod'=>'SAM-A54-128','q'=>1,'p'=>899.00],['cod'=>'CASE-IPH15','q'=>2,'p'=>25.00]]],

            ['fecha'=>'2025-11-18 15:45:00','c'=>1,'u'=>$admin->id,'m'=>'tarjeta',
             'items'=>[['cod'=>'APP-IPH15-128','q'=>1,'p'=>3499.00]]],

            /* Diciembre 2025 */
            ['fecha'=>'2025-12-03 11:00:00','c'=>2,'u'=>$vendedor->id,'m'=>'yape',
             'items'=>[['cod'=>'XIA-13T-256','q'=>1,'p'=>999.00],['cod'=>'AUD-SAM-TW','q'=>1,'p'=>199.00]]],

            ['fecha'=>'2025-12-20 16:30:00','c'=>3,'u'=>$admin->id,'m'=>'efectivo',
             'items'=>[['cod'=>'MOT-G84-256','q'=>2,'p'=>699.00]]],

            /* Enero 2026 */
            ['fecha'=>'2026-01-08 09:15:00','c'=>4,'u'=>$vendedor->id,'m'=>'transferencia',
             'items'=>[['cod'=>'SAM-S24-256','q'=>1,'p'=>1299.00],['cod'=>'CAR-USB-C-65','q'=>2,'p'=>35.00]]],

            ['fecha'=>'2026-01-22 14:00:00','c'=>0,'u'=>$admin->id,'m'=>'tarjeta',
             'items'=>[['cod'=>'HUA-NOV11-128','q'=>1,'p'=>849.00],['cod'=>'AUD-SAM-TW','q'=>1,'p'=>199.00]]],

            /* Febrero 2026 */
            ['fecha'=>'2026-02-04 10:30:00','c'=>1,'u'=>$vendedor->id,'m'=>'yape',
             'items'=>[['cod'=>'OPP-A58-128','q'=>1,'p'=>629.00],['cod'=>'CASE-IPH15','q'=>3,'p'=>25.00]]],

            ['fecha'=>'2026-02-14 13:00:00','c'=>2,'u'=>$admin->id,'m'=>'efectivo',
             'items'=>[['cod'=>'APP-IPH15-128','q'=>1,'p'=>3499.00],['cod'=>'CAR-USB-C-65','q'=>1,'p'=>35.00]]],

            ['fecha'=>'2026-02-28 17:10:00','c'=>3,'u'=>$vendedor->id,'m'=>'plin',
             'items'=>[['cod'=>'SAM-A54-128','q'=>1,'p'=>899.00]]],

            /* Marzo 2026 */
            ['fecha'=>'2026-03-05 09:45:00','c'=>4,'u'=>$admin->id,'m'=>'tarjeta',
             'items'=>[['cod'=>'SAM-S24-256','q'=>1,'p'=>1299.00]]],

            ['fecha'=>'2026-03-14 11:30:00','c'=>0,'u'=>$vendedor->id,'m'=>'efectivo',
             'items'=>[['cod'=>'XIA-13T-256','q'=>2,'p'=>999.00]]],

            ['fecha'=>'2026-03-25 16:00:00','c'=>1,'u'=>$admin->id,'m'=>'yape',
             'items'=>[['cod'=>'MOT-G84-256','q'=>1,'p'=>699.00],['cod'=>'AUD-SAM-TW','q'=>2,'p'=>199.00]]],

            /* Abril 2026 */
            ['fecha'=>'2026-04-02 10:00:00','c'=>2,'u'=>$vendedor->id,'m'=>'transferencia',
             'items'=>[['cod'=>'HUA-NOV11-128','q'=>2,'p'=>849.00]]],

            ['fecha'=>'2026-04-15 14:30:00','c'=>3,'u'=>$admin->id,'m'=>'tarjeta',
             'items'=>[['cod'=>'APP-IPH15-128','q'=>1,'p'=>3499.00],['cod'=>'CASE-IPH15','q'=>1,'p'=>25.00]]],

            ['fecha'=>'2026-04-28 09:00:00','c'=>4,'u'=>$vendedor->id,'m'=>'efectivo',
             'items'=>[['cod'=>'OPP-A58-128','q'=>1,'p'=>629.00],['cod'=>'CAR-USB-C-65','q'=>3,'p'=>35.00]]],

            /* Mayo 2026 — hoy */
            ['fecha'=>'2026-05-01 09:30:00','c'=>0,'u'=>$admin->id,'m'=>'yape',
             'items'=>[['cod'=>'SAM-A54-128','q'=>1,'p'=>899.00],['cod'=>'AUD-SAM-TW','q'=>1,'p'=>199.00]]],
        ];

        foreach ($ventas as $vd) {
            $cliente  = $clientes[$vd['c']];
            $subtotal = collect($vd['items'])->sum(fn($i) => $i['q'] * $i['p']);
            $impuesto = round($subtotal * 0.18, 2);
            $total    = round($subtotal + $impuesto, 2);
            $numero   = 'VTA-' . str_pad($ventaNum++, 6, '0', STR_PAD_LEFT);

            $venta = Venta::create([
                'numero_venta' => $numero,
                'cliente_id'   => $cliente->id,
                'user_id'      => $vd['u'],
                'fecha_venta'  => Carbon::parse($vd['fecha']),
                'subtotal'     => $subtotal,
                'descuento'    => 0,
                'impuesto'     => $impuesto,
                'total'        => $total,
                'metodo_pago_id' => $resolverMetodoPagoId($vd['m']),
                'estado'       => 'completada',
            ]);

            foreach ($vd['items'] as $item) {
                $prod = $productos[$item['cod']];
                DetalleVenta::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $prod->id,
                    'cantidad'        => $item['q'],
                    'precio_unitario' => $item['p'],
                    'descuento'       => 0,
                    'subtotal'        => $item['q'] * $item['p'],
                ]);
                // Reducir stock (nunca por debajo de 0)
                $prod->update(['stock' => max(0, $prod->stock - $item['q'])]);
            }
        }

        // ── REPARACIONES — contador manual ────────────────────────────────
        $repNum = 1;

        $reparaciones = [
            /* Entregadas */
            ['fecha_r'=>'2026-02-10 09:00:00','fecha_e'=>'2026-02-13','fecha_d'=>'2026-02-13 17:30:00',
             'c'=>0,'estado'=>'entregado','prio'=>'media',
             'disp'=>'Samsung Galaxy A32','marca'=>'Samsung','modelo'=>'A32',
             'falla'=>'Pantalla rota por caída','diag'=>'LCD fragmentado, táctil sin respuesta',
             'sol'=>'Reemplazo módulo LCD + táctil','pres'=>180.00,'costo'=>160.00,'gar'=>true,'dias_g'=>30],

            ['fecha_r'=>'2026-02-18 10:30:00','fecha_e'=>'2026-02-21','fecha_d'=>'2026-02-20 15:00:00',
             'c'=>1,'estado'=>'entregado','prio'=>'alta',
             'disp'=>'iPhone 13','marca'=>'Apple','modelo'=>'13',
             'falla'=>'Batería no carga, apagados repentinos','diag'=>'Batería degradada al 64%',
             'sol'=>'Cambio batería original Apple','pres'=>220.00,'costo'=>200.00,'gar'=>true,'dias_g'=>90],

            ['fecha_r'=>'2026-03-01 11:00:00','fecha_e'=>'2026-03-06','fecha_d'=>'2026-03-05 16:45:00',
             'c'=>2,'estado'=>'entregado','prio'=>'baja',
             'disp'=>'Xiaomi Redmi Note 11','marca'=>'Xiaomi','modelo'=>'Redmi Note 11',
             'falla'=>'Se apaga solo cada 30 minutos','diag'=>'Software corrupto + batería débil',
             'sol'=>'Flash firmware MIUI + reemplazo batería','pres'=>150.00,'costo'=>130.00,'gar'=>false,'dias_g'=>0],

            ['fecha_r'=>'2026-03-15 09:30:00','fecha_e'=>'2026-03-19','fecha_d'=>'2026-03-18 14:00:00',
             'c'=>3,'estado'=>'entregado','prio'=>'urgente',
             'disp'=>'Samsung Galaxy S21','marca'=>'Samsung','modelo'=>'S21',
             'falla'=>'Cámara trasera no enfoca','diag'=>'Módulo de cámara principal dañado',
             'sol'=>'Reemplazo módulo cámara 108MP Samsung','pres'=>350.00,'costo'=>320.00,'gar'=>true,'dias_g'=>60],

            /* Listas para entregar */
            ['fecha_r'=>'2026-04-03 10:00:00','fecha_e'=>'2026-04-07','fecha_d'=>null,
             'c'=>4,'estado'=>'listo','prio'=>'media',
             'disp'=>'Motorola Moto G52','marca'=>'Motorola','modelo'=>'Moto G52',
             'falla'=>'Micrófono no funciona en llamadas','diag'=>'Micrófono MEMS dañado por humedad',
             'sol'=>'Reemplazo micrófono + limpieza placa','pres'=>120.00,'costo'=>100.00,'gar'=>true,'dias_g'=>30],

            ['fecha_r'=>'2026-04-22 14:30:00','fecha_e'=>'2026-04-27','fecha_d'=>null,
             'c'=>0,'estado'=>'listo','prio'=>'media',
             'disp'=>'Xiaomi Redmi 10C','marca'=>'Xiaomi','modelo'=>'Redmi 10C',
             'falla'=>'Conector de carga suelto','diag'=>'Puerto USB-C desgastado, pines doblados',
             'sol'=>'Reemplazo módulo USB-C','pres'=>80.00,'costo'=>70.00,'gar'=>true,'dias_g'=>30],

            /* En proceso */
            ['fecha_r'=>'2026-04-12 09:00:00','fecha_e'=>'2026-04-18','fecha_d'=>null,
             'c'=>1,'estado'=>'en_reparacion','prio'=>'alta',
             'disp'=>'iPhone 12 Pro','marca'=>'Apple','modelo'=>'12 Pro',
             'falla'=>'Face ID no funciona','diag'=>'Sensor TrueDepth dañado',
             'sol'=>null,'pres'=>450.00,'costo'=>null,'gar'=>false,'dias_g'=>0],

            ['fecha_r'=>'2026-04-20 11:15:00','fecha_e'=>'2026-05-05','fecha_d'=>null,
             'c'=>2,'estado'=>'esperando_repuesto','prio'=>'media',
             'disp'=>'Huawei P30 Lite','marca'=>'Huawei','modelo'=>'P30 Lite',
             'falla'=>'Pantalla parpadea y se pone verde','diag'=>'Flex de pantalla defectuoso, repuesto importado',
             'sol'=>null,'pres'=>200.00,'costo'=>null,'gar'=>false,'dias_g'=>0],

            /* Ingresadas hoy */
            ['fecha_r'=>'2026-05-01 09:00:00','fecha_e'=>'2026-05-06','fecha_d'=>null,
             'c'=>3,'estado'=>'recibido','prio'=>'urgente',
             'disp'=>'Samsung Galaxy A54','marca'=>'Samsung','modelo'=>'A54',
             'falla'=>'Caída al agua, no enciende','diag'=>null,
             'sol'=>null,'pres'=>null,'costo'=>null,'gar'=>false,'dias_g'=>0],

            ['fecha_r'=>'2026-05-01 10:30:00','fecha_e'=>'2026-05-08','fecha_d'=>null,
             'c'=>4,'estado'=>'en_diagnostico','prio'=>'media',
             'disp'=>'Xiaomi 12','marca'=>'Xiaomi','modelo'=>'12',
             'falla'=>'Parte superior del táctil no responde','diag'=>null,
             'sol'=>null,'pres'=>null,'costo'=>null,'gar'=>false,'dias_g'=>0],
        ];

        foreach ($reparaciones as $rd) {
            $cliente = $clientes[$rd['c']];
            $numero  = 'REP-' . str_pad($repNum++, 6, '0', STR_PAD_LEFT);

            Reparacion::create([
                'numero_orden'    => $numero,
                'cliente_id'      => $cliente->id,
                'tecnico_id'      => $tecnico->id,
                'dispositivo'     => $rd['disp'],
                'marca'           => $rd['marca'],
                'modelo'          => $rd['modelo'],
                'falla_reportada' => $rd['falla'],
                'diagnostico'     => $rd['diag'],
                'solucion'        => $rd['sol'],
                'presupuesto'     => $rd['pres'],
                'costo_final'     => $rd['costo'],
                'estado'          => $rd['estado'],
                'prioridad'       => $rd['prio'],
                'fecha_recepcion' => Carbon::parse($rd['fecha_r']),
                'fecha_estimada'  => Carbon::parse($rd['fecha_e']),
                'fecha_entrega'   => $rd['fecha_d'] ? Carbon::parse($rd['fecha_d']) : null,
                'garantia'        => $rd['gar'],
                'dias_garantia'   => $rd['dias_g'],
            ]);
        }

        $this->command->info('');
        $this->command->info('✅ DemoDataSeeder completado:');
        $this->command->info('   → ' . Cliente::count()    . ' clientes en total');
        $this->command->info('   → ' . Producto::count()   . ' productos en total');
        $this->command->info('   → ' . Venta::count()      . ' ventas registradas');
        $this->command->info('   → ' . Reparacion::count() . ' órdenes de reparación');
    }
}
