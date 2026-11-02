<?php

namespace App\Services\Execution;

use Illuminate\Support\Facades\DB;

class ExecutionService
{
    /**
     * @param  array{sql_command: string, database: string}  $params
     * @return array<int, mixed>
     */
    public function executeSQL(string $query, string $database): array
    {
        try {
            config(['database.connections.user_mysql' => [
                'driver' => 'mysql',
                'host' => config('database.connections.mysql.host'),
                'port' => config('database.connections.mysql.port'),
                'username' => config('database.connections.mysql.username'),
                'password' => config('database.connections.mysql.password'),
                'database' => $database,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]]);

            DB::purge('user_mysql');

            return DB::connection('user_mysql')->select($query);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
