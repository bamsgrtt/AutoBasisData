<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DatabaseConfigController extends Controller
{
    public function showConfigForm(): View
    {
        return view('database_config');
    }

    public function testConnection(Request $request): JsonResponse
    {
        $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'required|string',
            'password' => 'nullable|string',
        ]);

        try {
            config([
                'database.connections.test_mysql' => [
                    'driver' => 'mysql',
                    'host' => $request->host,
                    'port' => $request->port,
                    'username' => $request->username,
                    'password' => $request->password ?? '',
                    'database' => '',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ],
            ]);

            $pdo = DB::connection('test_mysql')->getPdo();
            $databases = $pdo->query('SHOW DATABASES')->fetchAll(\PDO::FETCH_COLUMN);

            return response()->json(['status' => 'success', 'databases' => $databases]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function storeSelectedDatabase(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'selected_database' => 'required|string',
        ]);

        session(['selected_database' => $request->selected_database]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'saved', 'database' => $request->selected_database]);
        }

        return redirect()->back()->with('success', 'Database selection saved successfully.');
    }
}
