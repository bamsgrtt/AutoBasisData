<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SQLExecutorController extends Controller
{
    public function showForm(): View
    {
        return view('execute_sql');
    }

    public function executeSQL(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sql_command' => 'required|string',
        ]);

        $selectedDb = session('selected_database');

        if (! $selectedDb) {
            return response()->json(['error' => 'No database selected. Please configure a database first.']);
        }

        try {
            config(['database.connections.user_mysql' => [
                'driver' => 'mysql',
                'host' => config('database.connections.mysql.host'),
                'port' => config('database.connections.mysql.port'),
                'username' => config('database.connections.mysql.username'),
                'password' => config('database.connections.mysql.password'),
                'database' => $selectedDb,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]]);

            DB::purge('user_mysql');
            $results = DB::connection('user_mysql')->select($validated['sql_command']);

            return response()->json(['result' => $results]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
