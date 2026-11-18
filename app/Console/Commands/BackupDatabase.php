<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Sauvegarde quotidienne de la base de données (MySQL/PostgreSQL/SQLite)';

    public function handle(): int
    {
        $dir = base_path(config('intendance.backup.path'));
        File::ensureDirectoryExists($dir);

        $conn = config('database.default');
        $cfg = config("database.connections.$conn");
        $base = $dir.'/backup_'.now()->format('Y-m-d_His');

        switch ($cfg['driver']) {
            case 'mysql':
                $fichier = $base.'.sql';
                $process = new Process([
                    'mysqldump', '--single-transaction', '--routines',
                    '-h', $cfg['host'], '-P', (string) $cfg['port'], '-u', $cfg['username'],
                    $cfg['database'],
                ], null, ['MYSQL_PWD' => (string) $cfg['password']]);
                break;
            case 'pgsql':
                $fichier = $base.'.sql';
                $process = new Process([
                    'pg_dump', '-h', $cfg['host'], '-p', (string) $cfg['port'], '-U', $cfg['username'], $cfg['database'],
                ], null, ['PGPASSWORD' => (string) $cfg['password']]);
                break;
            case 'sqlite':
                $fichier = $base.'.sqlite';
                File::copy($cfg['database'], $fichier);
                $this->info("Sauvegarde créée : $fichier");
                $this->purger($dir);

                return self::SUCCESS;
            default:
                $this->error('Pilote non supporté.');

                return self::FAILURE;
        }

        $process->setTimeout(600);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->error('Échec de la sauvegarde : '.$process->getErrorOutput());

            return self::FAILURE;
        }

        File::put($fichier, $process->getOutput());
        $this->info("Sauvegarde créée : $fichier");
        $this->purger($dir);

        return self::SUCCESS;
    }

    private function purger(string $dir): void
    {
        $limite = now()->subDays(config('intendance.backup.keep_days'))->getTimestamp();

        foreach (File::files($dir) as $f) {
            if ($f->getMTime() < $limite) {
                File::delete($f->getPathname());
            }
        }
    }
}
