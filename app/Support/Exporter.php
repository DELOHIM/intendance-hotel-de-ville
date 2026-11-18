<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Exporter
{
    /**
     * Exporte un tableau au format CSV (ouvrable dans Excel) ou PDF.
     *
     * @param  array<int,string>  $entetes
     * @param  iterable<int,array<int,mixed>>  $lignes
     */
    public static function sortir(string $format, string $nomFichier, string $titre, array $entetes, iterable $lignes, array $filtres = []): Response
    {
        if ($format === 'pdf') {
            return Pdf::loadView('rapports.pdf', [
                'titre' => $titre,
                'entetes' => $entetes,
                'lignes' => collect($lignes)->values(),
                'filtres' => $filtres,
            ])->setPaper('a4', 'landscape')->download($nomFichier.'.pdf');
        }

        return new StreamedResponse(function () use ($entetes, $lignes) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel
            fputcsv($out, $entetes, ';');
            foreach ($lignes as $ligne) {
                fputcsv($out, array_map(fn ($v) => is_string($v) ? str_replace(["\r", "\n"], ' ', $v) : $v, $ligne), ';');
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$nomFichier.'.csv"',
        ]);
    }
}
