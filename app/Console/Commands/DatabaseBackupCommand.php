<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup {--restore= : Path to backup SQL file to restore}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a consistent MySQL database backup or restore an existing SQL snapshot';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $restoreFile = $this->option('restore');

        if ($restoreFile) {
            return $this->handleRestore($restoreFile);
        }

        return $this->handleBackup();
    }

    /**
     * Handle Database Backup Creation
     */
    protected function handleBackup(): int
    {
        $this->info('Memulai pencadangan basis data MySQL PasarHub...');

        $backupDir = storage_path('app/backups');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'backup_pasarhub_' . date('Ymd_His') . '.sql';
        $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $handle = fopen($filepath, 'w+');
        if (!$handle) {
            $this->error("Gagal membuat berkas cadangan pada: {$filepath}");
            return Command::FAILURE;
        }

        // Header SQL
        fwrite($handle, "-- ============================================================\n");
        fwrite($handle, "-- PASARHUB SISTEM OPERASIONAL PASAR TERPADU\n");
        fwrite($handle, "-- Automated MySQL Database Backup Snapshot\n");
        fwrite($handle, "-- Tanggal: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- ============================================================\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n");
        fwrite($handle, "SET AUTOCOMMIT=0;\n");
        fwrite($handle, "START TRANSACTION;\n\n");

        $database = config('database.connections.mysql.database');
        $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
        $keyName = "Tables_in_" . $database;

        $tableCount = 0;
        foreach ($tables as $tableObj) {
            $tableName = $tableObj->$keyName ?? array_values((array) $tableObj)[0];
            $this->line("  -> Mencadangkan tabel: {$tableName}");

            // Drop table statement
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");

            // Create table statement
            $createRes = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createSql = $createRes[0]->{'Create Table'} ?? '';
            fwrite($handle, $createSql . ";\n\n");

            // Dump data rows
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                foreach ($rows->chunk(100) as $chunk) {
                    $values = [];
                    foreach ($chunk as $row) {
                        $rowArr = (array) $row;
                        $escapedValues = array_map(function ($val) {
                            if (is_null($val)) return 'NULL';
                            return DB::getPdo()->quote((string) $val);
                        }, array_values($rowArr));
                        $values[] = '(' . implode(', ', $escapedValues) . ')';
                    }

                    $columns = array_map(fn($c) => "`{$c}`", array_keys((array) $chunk->first()));
                    $insertSql = "INSERT INTO `{$tableName}` (" . implode(', ', $columns) . ") VALUES\n" . implode(",\n", $values) . ";\n";
                    fwrite($handle, $insertSql);
                }
            }

            fwrite($handle, "\n");
            $tableCount++;
        }

        fwrite($handle, "COMMIT;\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);

        $sizeKb = round(filesize($filepath) / 1024, 2);
        $this->info("Pencadangan Sukses! {$tableCount} tabel dicadangkan ke:\n-> {$filepath} ({$sizeKb} KB)");

        return Command::SUCCESS;
    }

    /**
     * Handle Database Restore
     */
    protected function handleRestore(string $filepath): int
    {
        if (!File::exists($filepath)) {
            $this->error("Berkas cadangan tidak ditemukan: {$filepath}");
            return Command::FAILURE;
        }

        $this->warn("PERINGATAN: Memulihkan database dari {$filepath} akan menimpa data aktif saat ini!");
        
        $sql = File::get($filepath);
        if (empty($sql)) {
            $this->error("Berkas SQL kosong!");
            return Command::FAILURE;
        }

        DB::unprepared($sql);
        $this->info("Restorasi basis data berhasil diselesaikan!");

        return Command::SUCCESS;
    }
}
