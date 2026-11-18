<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Planche de QR Codes - Bureaux</title>
    <style>
        /* Configuration générale pour DomPDF */
        @page {
            margin: 40px 30px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #1e293b; /* slate-800 */
            background-color: #ffffff;
        }

        /* En-tête institutionnel */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f1f5f9; /* slate-100 */
        }
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a; /* slate-900 */
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            font-size: 12px;
            color: #64748b; /* slate-500 */
            margin: 0;
        }
        /* Petite barre tricolore sous le titre */
        .tricolor-bar {
            width: 60px;
            height: 3px;
            margin: 10px auto 0 auto;
            background: linear-gradient(to right, #007FFF 33%, #F7D618 33%, #F7D618 66%, #CE1021 66%);
            border-radius: 2px;
        }

        /* Conteneur des cartes (Table pour compatibilité DomPDF) */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px;
            margin-left: -15px; /* Compense le border-spacing externe */
            margin-right: -15px;
        }
        td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        /* Style de la carte QR */
        .carte {
            border: 1.5px solid #e2e8f0; /* slate-200 */
            border-radius: 12px;
            padding: 20px 15px;
            text-align: center;
            page-break-inside: avoid;
            background-color: #ffffff;
            position: relative;
        }
        /* Bande colorée en haut de chaque carte */
        .carte::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background-color: #007FFF; /* brand-blue */
            border-radius: 12px 12px 0 0;
        }

        .num {
            font-size: 24px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
            font-family: 'DejaVu Sans Mono', monospace;
        }
        .lib {
            font-size: 12px;
            color: #334155;
            font-weight: 600;
            margin-bottom: 4px;
            line-height: 1.2;
            min-height: 28px; /* Évite le décalage si le titre est sur 1 ou 2 lignes */
        }
        .etage {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }
        
        /* Image QR Code */
        .qr-container {
            margin: 0 auto 15px auto;
            width: 160px;
            height: 160px;
            padding: 5px;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            background-color: #fff;
        }
        .qr-container img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .hint {
            font-size: 9px;
            color: #64748b;
            line-height: 1.3;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #e2e8f0;
        }
        .hint strong {
            color: #0f172a;
        }
    </style>
</head>
<body>

    {{-- En-tête du document --}}
    <div class="header">
        <h1>Hôtel de Ville de Kinshasa</h1>
        <p>Planche de QR Codes officiels des bureaux</p>
        <div class="tricolor-bar"></div>
    </div>

    {{-- Grille des cartes --}}
    <table>
        <tbody>
            @foreach ($bureaux as $index => $b)
                {{-- Nouvelle ligne tous les 2 bureaux --}}
                @if ($index % 2 === 0)
                    <tr>
                @endif

                <td>
                    <div class="carte">
                        <div class="num">{{ $b->numero }}</div>
                        <div class="lib">{{ $b->libelle }}</div>
                        <div class="etage">{{ $b->etage->nom ?? '' }}</div>
                        
                        <div class="qr-container">
                            <img src="{{ $qr($b) }}" alt="QR {{ $b->numero }}">
                        </div>
                        
                        <div class="hint">
                            Scannez pour consulter<br>
                            la fiche ou <strong>signaler un incident</strong>
                        </div>
                    </div>
                </td>

                {{-- Fermeture de la ligne si c'est le 2ème élément ou le dernier --}}
                @if ($index % 2 !== 0 || $loop->last)
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

</body>
</html>