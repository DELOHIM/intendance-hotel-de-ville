<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $titre }} — Hôtel de Ville de Kinshasa</title>
    <style>
        /* --- Configuration de la page --- */
        @page {
            margin: 100px 30px 60px 30px;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }

        /* --- En-tête fixe (répété sur chaque page) --- */
        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 70px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
            background: transparent !important;
        }
        .logo {
            width: 55px;
            height: auto;
        }
        .header-text {
            padding-left: 10px;
        }
        .header-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-subtitle {
            font-size: 9px;
            color: #64748b;
            margin: 2px 0 0 0;
        }
        .header-meta {
            text-align: right;
            font-size: 8px;
            color: #64748b;
        }
        .tricolor-bar {
            width: 100%;
            height: 3px;
            background: linear-gradient(to right, #007FFF 33%, #F7D618 33%, #F7D618 66%, #CE1021 66%);
            margin-top: 8px;
        }

        /* --- Titre du document --- */
        .document-title {
            text-align: center;
            margin: 20px 0 5px 0;
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .document-subtitle {
            text-align: center;
            color: #64748b;
            font-size: 9px;
            margin: 0 0 20px 0;
        }

        /* --- Filtres appliqués --- */
        .filters-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-size: 8px;
            color: #475569;
        }
        .filters-box strong {
            color: #0f172a;
        }

        /* --- Tableau --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead {
            display: table-header-group; /* Répète l'en-tête sur chaque page */
        }
        th {
            background-color: #0f172a;
            color: #ffffff;
            text-align: left;
            padding: 8px 6px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #0f172a;
        }
        td {
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 8.5px;
        }
        tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        tr {
            page-break-inside: avoid;
        }

        /* --- Pied de page fixe --- */
        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-size: 8px;
            color: #94a3b8;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            border: none;
            padding: 0;
            background: transparent !important;
        }
        .footer-left { text-align: left; }
        .footer-right { text-align: right; }

        /* Numérotation des pages (DomPDF compatible) */
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>

    {{-- En-tête fixe (répété sur chaque page) --}}
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 60px;">
                    {{-- Logo : essayez d'abord le chemin public --}}
                    @if(file_exists(public_path('images/logo-kinshasa.png')))
                        <img src="{{ public_path('images/logo-kinshasa.png') }}" class="logo" alt="Logo">
                    @endif
                </td>
                <td class="header-text">
                    <p class="header-title">Hôtel de Ville de Kinshasa</p>
                    <p class="header-subtitle">République Démocratique du Congo — Solution d'Intendance</p>
                </td>
                <td class="header-meta">
                    <strong>Document officiel</strong><br>
                    Généré le {{ now()->format('d/m/Y') }}<br>
                    à {{ now()->format('H:i') }}
                </td>
            </tr>
        </table>
        <div class="tricolor-bar"></div>
    </header>

    {{-- Pied de page fixe (répété sur chaque page) --}}
    <footer>
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    Solution d'intendance — Document généré automatiquement.
                </td>
                <td class="footer-right">
                    Page <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </footer>

    {{-- Contenu principal --}}
    <main>
        <h1 class="document-title">{{ $titre }}</h1>
        <p class="document-subtitle">
            Total : {{ $lignes->count() }} ligne(s) affichée(s)
        </p>

        {{-- Filtres appliqués --}}
        @if (collect($filtres)->filter()->isNotEmpty())
            <div class="filters-box">
                <strong>Critères de sélection appliqués :</strong>
                {{ collect($filtres)->filter()->map(fn ($v, $k) => "$k = $v")->join(' · ') }}
            </div>
        @endif

        {{-- Tableau de données --}}
        <table>
            <thead>
                <tr>
                    @foreach ($entetes as $e)
                        <th>{{ $e }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($lignes as $ligne)
                    <tr>
                        @foreach ($ligne as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>

</body>
</html>