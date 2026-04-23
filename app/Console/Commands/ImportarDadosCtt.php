<?php

namespace App\Console\Commands;

use App\Models\CtCodigoPostal;
use App\Models\CtConcelho;
use App\Models\CtDistrito;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportarDadosCtt extends Command
{
    protected $signature = 'ctt:importar
                            {--path= : Pasta com os ficheiros CTT (distritos.txt, concelhos.txt, todos_cp.txt)}
                            {--apenas= : Importar apenas: distritos, concelhos, ou codigos}';

    protected $description = 'Importa os dados dos CTT (distritos, concelhos e códigos postais)';

    public function handle(): int
    {
        $path = rtrim($this->option('path') ?? storage_path('ctt'), '/\\');
        $apenas = $this->option('apenas');

        if (! is_dir($path)) {
            $this->error("Pasta não encontrada: {$path}");

            return self::FAILURE;
        }

        if (! $apenas || $apenas === 'distritos') {
            $this->importarDistritos($path);
        }

        if (! $apenas || $apenas === 'concelhos') {
            $this->importarConcelhos($path);
        }

        if (! $apenas || $apenas === 'codigos') {
            $this->importarCodigosPostais($path);
        }

        $this->info('Importação concluída.');

        return self::SUCCESS;
    }

    private function importarDistritos(string $path): void
    {
        $file = $path.'/distritos.txt';

        if (! file_exists($file)) {
            $this->warn("Ficheiro não encontrado: {$file}");

            return;
        }

        $this->info('A importar distritos...');
        $count = 0;

        foreach ($this->lerLinhas($file) as $linha) {
            $campos = explode(';', $linha);

            if (count($campos) < 2) {
                continue;
            }

            CtDistrito::updateOrCreate(
                ['dd' => trim($campos[0])],
                ['desig' => trim($campos[1])]
            );
            $count++;
        }

        $this->info("  {$count} distritos importados.");
    }

    private function importarConcelhos(string $path): void
    {
        $file = $path.'/concelhos.txt';

        if (! file_exists($file)) {
            $this->warn("Ficheiro não encontrado: {$file}");

            return;
        }

        $this->info('A importar concelhos...');
        $count = 0;

        foreach ($this->lerLinhas($file) as $linha) {
            $campos = explode(';', $linha);

            if (count($campos) < 3) {
                continue;
            }

            CtConcelho::updateOrCreate(
                ['dd' => trim($campos[0]), 'cc' => trim($campos[1])],
                ['desig' => trim($campos[2])]
            );
            $count++;
        }

        $this->info("  {$count} concelhos importados.");
    }

    private function importarCodigosPostais(string $path): void
    {
        $file = $path.'/todos_cp.txt';

        if (! file_exists($file)) {
            $this->warn("Ficheiro não encontrado: {$file}");

            return;
        }

        $this->info('A importar códigos postais (pode demorar alguns minutos)...');

        $total = $this->contarLinhas($file);
        $this->info("  Total de linhas: {$total}");

        $bar = $this->output->createProgressBar($total);
        $bar->setRedrawFrequency(500);

        $chunk = [];
        $count = 0;
        $chunkSize = 500;

        DB::statement('SET foreign_key_checks=0');
        CtCodigoPostal::truncate();
        DB::statement('SET foreign_key_checks=1');

        foreach ($this->lerLinhas($file) as $linha) {
            $campos = explode(';', $linha);

            if (count($campos) < 17) {
                $bar->advance();

                continue;
            }

            $chunk[] = [
                'dd' => trim($campos[0]),
                'cc' => trim($campos[1]),
                'llll' => trim($campos[2]),
                'localidade' => trim($campos[3]),
                'art_cod' => $this->nullable($campos[4]),
                'art_tipo' => $this->nullable($campos[5]),
                'pri_prep' => $this->nullable($campos[6]),
                'art_titulo' => $this->nullable($campos[7]),
                'seg_prep' => $this->nullable($campos[8]),
                'art_desig' => $this->nullable($campos[9]),
                'art_local' => $this->nullable($campos[10]),
                'troco' => $this->nullable($campos[11]),
                'porta' => $this->nullable($campos[12]),
                'cliente' => $this->nullable($campos[13]),
                'cp4' => trim($campos[14]),
                'cp3' => trim($campos[15]),
                'cpalf' => trim($campos[16]),
                'nome_arteria' => $this->construirNomeArteria($campos),
            ];

            $count++;
            $bar->advance();

            if (count($chunk) >= $chunkSize) {
                DB::table('ct_codigos_postais')->insert($chunk);
                $chunk = [];
            }
        }

        if (! empty($chunk)) {
            DB::table('ct_codigos_postais')->insert($chunk);
        }

        $bar->finish();
        $this->newLine();
        $this->info("  {$count} códigos postais importados.");
    }

    private function construirNomeArteria(array $campos): ?string
    {
        // ART_TIPO(5) + PRI_PREP(6) + ART_TITULO(7) + SEG_PREP(8) + ART_DESIG(9)
        $partes = array_filter([
            trim($campos[5] ?? ''),
            trim($campos[6] ?? ''),
            trim($campos[7] ?? ''),
            trim($campos[8] ?? ''),
            trim($campos[9] ?? ''),
        ], fn ($p) => $p !== '');

        $nome = implode(' ', $partes);

        return $nome !== '' ? $nome : null;
    }

    private function nullable(string $valor): ?string
    {
        $valor = trim($valor);

        return $valor !== '' ? $valor : null;
    }

    private function lerLinhas(string $file): \Generator
    {
        $handle = fopen($file, 'r');

        while (($linha = fgets($handle)) !== false) {
            $linha = rtrim($linha, "\r\n");

            if ($linha === '') {
                continue;
            }

            // Converte de ISO-8859-1 (Latin-1) para UTF-8
            yield mb_convert_encoding($linha, 'UTF-8', 'ISO-8859-1');
        }

        fclose($handle);
    }

    private function contarLinhas(string $file): int
    {
        $count = 0;
        $handle = fopen($file, 'r');

        while (fgets($handle) !== false) {
            $count++;
        }

        fclose($handle);

        return $count;
    }
}
