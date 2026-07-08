<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Reparacion;
use Carbon\Carbon;
use PDO;

class BackupController extends Controller
{
    private string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    // ── Vista principal ──────────────────────────────────────────────────────
    public function index()
    {
        $archivos = glob($this->backupDir . '/*.sql') ?: [];
        $backups  = [];

        foreach ($archivos as $archivo) {
            $backups[] = [
                'nombre'  => basename($archivo),
                'tamanio' => filesize($archivo),
                'fecha'   => Carbon::createFromTimestamp(filemtime($archivo)),
            ];
        }

        usort($backups, fn($a, $b) => $b['fecha'] <=> $a['fecha']);

        $stats = [
            'ventas'      => Venta::count(),
            'clientes'    => Cliente::count(),
            'productos'   => Producto::count(),
            'reparaciones'=> Reparacion::count(),
            'backups'     => count($backups),
            'ultimo'      => count($backups) > 0 ? $backups[0]['fecha'] : null,
            'tamTotal'    => array_sum(array_column($backups, 'tamanio')),
        ];

        return view('backup.index', compact('backups', 'stats'));
    }

    // ── Crear backup ─────────────────────────────────────────────────────────
    public function crear()
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        try {
            $sql    = $this->generarSQL();
            $nombre = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
            $ruta   = $this->backupDir . '/' . $nombre;
            file_put_contents($ruta, $sql);

            return back()->with('success', "Backup <strong>{$nombre}</strong> creado correctamente (" . $this->formatBytes(filesize($ruta)) . ").");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al crear el backup: ' . $e->getMessage());
        }
    }

    // ── Descargar archivo ────────────────────────────────────────────────────
    public function descargar(string $nombre)
    {
        $ruta = $this->backupDir . '/' . basename($nombre);

        abort_unless(file_exists($ruta) && str_ends_with($nombre, '.sql'), 404, 'Archivo no encontrado.');

        return response()->download($ruta);
    }

    // ── Eliminar archivo ─────────────────────────────────────────────────────
    public function eliminar(string $nombre)
    {
        $ruta = $this->backupDir . '/' . basename($nombre);

        if (file_exists($ruta) && str_ends_with($nombre, '.sql')) {
            unlink($ruta);
            return back()->with('success', "Backup <strong>{$nombre}</strong> eliminado.");
        }

        return back()->with('error', 'Archivo no encontrado.');
    }

    // ── Restaurar desde archivo ───────────────────────────────────────────────
    public function restaurar(Request $request)
    {
        $request->validate([
            'archivo_sql' => 'required|file|max:102400',
        ], [
            'archivo_sql.required' => 'Debes seleccionar un archivo .sql',
            'archivo_sql.max'      => 'El archivo no puede superar 100 MB.',
        ]);

        set_time_limit(600);
        ini_set('memory_limit', '512M');

        try {
            // Backup automático de seguridad antes de restaurar
            $autoNombre = 'pre_restore_' . now()->format('Y-m-d_H-i-s') . '.sql';
            file_put_contents($this->backupDir . '/' . $autoNombre, $this->generarSQL());

            $contenido = file_get_contents($request->file('archivo_sql')->getRealPath());

            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Dividir en sentencias individuales
            $statements = preg_split('/;\s*[\r\n]+/', $contenido);

            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if (empty($stmt) || preg_match('/^--/', $stmt) || preg_match('/^\/\*/', $stmt)) {
                    continue;
                }
                DB::unprepared($stmt);
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return back()->with('success', 'Base de datos restaurada correctamente. Se guardó un backup automático previo (<strong>' . $autoNombre . '</strong>).');
        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return back()->with('error', 'Error al restaurar: ' . $e->getMessage());
        }
    }

    // ── Resetear sistema ─────────────────────────────────────────────────────
    public function resetear(Request $request)
    {
        $request->validate([
            'tipo_reset'   => 'required|in:ventas,datos,total',
            'confirmacion' => 'required|in:RESETEAR',
        ], [
            'confirmacion.in' => 'Debes escribir exactamente "RESETEAR" para confirmar.',
        ]);

        // Backup automático antes de resetear
        try {
            $autoNombre = 'pre_reset_' . now()->format('Y-m-d_H-i-s') . '.sql';
            file_put_contents($this->backupDir . '/' . $autoNombre, $this->generarSQL());
        } catch (\Throwable) { /* Continúa aunque falle el backup */ }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            switch ($request->tipo_reset) {
                case 'ventas':
                    DB::table('detalle_ventas')->truncate();
                    DB::table('ventas')->truncate();
                    DB::table('reparaciones')->truncate();
                    $msg = 'Ventas y reparaciones eliminadas. Clientes, productos y usuarios conservados.';
                    break;

                case 'datos':
                    DB::table('detalle_ventas')->truncate();
                    DB::table('ventas')->truncate();
                    DB::table('reparaciones')->truncate();
                    DB::table('clientes')->truncate();
                    DB::table('productos')->truncate();
                    $msg = 'Datos comerciales eliminados. Usuarios, categorías y marcas conservados.';
                    break;

                case 'total':
                    DB::table('detalle_ventas')->truncate();
                    DB::table('ventas')->truncate();
                    DB::table('reparaciones')->truncate();
                    DB::table('clientes')->truncate();
                    DB::table('productos')->truncate();
                    DB::table('users')->where('rol', '!=', 'admin')->delete();
                    $msg = 'Sistema reseteado a estado de fábrica. Solo el administrador fue conservado.';
                    break;
            }
        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return back()->with('error', 'Error durante el reset: ' . $e->getMessage());
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return back()->with('success', $msg . ' Se generó un backup automático previo.');
    }

    // ── Generador SQL puro PHP ───────────────────────────────────────────────
    private function generarSQL(): string
    {
        $pdo    = DB::connection()->getPdo();
        $dbName = config('database.connections.mysql.database');
        $ahora  = now()->format('d/m/Y H:i:s');
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();

        $sql  = "-- ==============================================================\n";
        $sql .= "--  CRM Tienda Celulares — Backup Completo\n";
        $sql .= "--  Generado  : {$ahora}\n";
        $sql .= "--  Base datos: {$dbName}\n";
        $sql .= "--  MySQL     : {$version}\n";
        $sql .= "-- ==============================================================\n\n";
        $sql .= "SET NAMES utf8mb4;\n";
        $sql .= "SET CHARACTER SET utf8mb4;\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        $tablas = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tablas as $tabla) {
            $sql .= "-- --------------------------------------------------------------\n";
            $sql .= "-- Tabla: `{$tabla}`\n";
            $sql .= "-- --------------------------------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$tabla}`;\n";

            $create = $pdo->query("SHOW CREATE TABLE `{$tabla}`")->fetch(PDO::FETCH_ASSOC);
            $sql .= $create['Create Table'] . ";\n\n";

            $rows = $pdo->query("SELECT * FROM `{$tabla}`")->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($rows)) {
                foreach (array_chunk($rows, 500) as $chunk) {
                    $sql .= "INSERT INTO `{$tabla}` VALUES\n";
                    $lines = [];
                    foreach ($chunk as $row) {
                        $vals = array_map(
                            fn($v) => $v === null ? 'NULL' : $pdo->quote($v),
                            array_values($row)
                        );
                        $lines[] = '(' . implode(', ', $vals) . ')';
                    }
                    $sql .= implode(",\n", $lines) . ";\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        return $sql;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1_048_576) return round($bytes / 1_048_576, 2) . ' MB';
        if ($bytes >= 1_024)     return round($bytes / 1_024,     2) . ' KB';
        return $bytes . ' B';
    }
}
