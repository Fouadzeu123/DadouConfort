<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #000;
        }
        .document-type {
            font-size: 24pt;
            font-weight: 900;
            text-align: right;
            color: #ccc;
            text-transform: uppercase;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 40px;
        }
        .info-grid td {
            vertical-align: top;
            width: 50%;
        }
        .label {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #e11d48; /* primary color */
            border-bottom: 1px solid #fee2e2;
            display: inline-block;
            margin-bottom: 5px;
        }
        .client-label {
            color: #6366f1; /* indigo color */
            border-bottom: 1px solid #e0e7ff;
        }
        .date-bar {
            background-color: #f9fafb;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 30px;
            font-size: 9pt;
            color: #6b7280;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.items th {
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #9ca3af;
            padding: 10px;
            border-bottom: 2px solid #f3f4f6;
        }
        table.items td {
            padding: 15px 10px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }
        .total-section {
            width: 100%;
            margin-top: 20px;
        }
        .total-box {
            float: right;
            width: 250px;
        }
        .total-row {
            margin-bottom: 8px;
            font-size: 10pt;
            color: #6b7280;
        }
        .total-row .val {
            float: right;
            color: #000;
        }
        .grand-total {
            background-color: #1e1b4b; /* indigo-950 */
            color: white;
            padding: 15px;
            border-radius: 15px;
            margin-top: 15px;
        }
        .grand-total .lbl {
            font-size: 8pt;
            opacity: 0.6;
            text-transform: uppercase;
        }
        .grand-total .val {
            font-size: 16pt;
            font-weight: bold;
            float: right;
        }
        .notes {
            margin-top: 50px;
            font-size: 10pt;
            font-style: italic;
            color: #4b5563;
        }
        .signatures {
            margin-top: 80px;
            width: 100%;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .signature-box {
            height: 100px;
            border-bottom: 1px solid #eee;
            margin: 10px 40px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            text-transform: uppercase;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td class="company-name">DadouConfort</td>
                <td class="document-type">Devis</td>
            </tr>
            <tr>
                <td style="font-size: 9pt; color: #9ca3af;">{{ $devi->numero }}</td>
                <td></td>
            </tr>
        </table>
    </div>

    <table class="info-grid">
        <tr>
            <td>
                <div class="label">De la part de</div>
                <div style="font-weight: bold; font-size: 11pt;">DadouConfort</div>
                <div style="font-size: 10pt; color: #6b7280; line-height: 1.4;">
                    Artisan Menuisier & Services<br>
                    Yaoundé, Cameroun<br>
                    <span style="color: #e11d48; font-weight: bold;">Tél: 672 320 021</span>
                </div>
            </td>
            <td style="text-align: right;">
                <div class="label client-label">Destiné à</div>
                <div style="font-weight: bold; font-size: 11pt; text-transform: uppercase;">{{ $devi->client->nom_complet }}</div>
                <div style="font-size: 10pt; color: #6b7280; line-height: 1.4;">
                    {{ $devi->client->adresse ?? 'Adresse non renseignée' }}<br>
                    <span style="color: #6366f1; font-weight: bold;">{{ $devi->client->telephone }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="date-bar">
        <span>Emis le {{ $devi->date->format('d/m/Y') }}</span>
        <span style="float: right;">Validité 30 jours</span>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">#</th>
                <th>Désignation / Ouvrage</th>
                <th style="width: 80px; text-align: center;">Quantité</th>
                <th style="width: 120px; text-align: right;">Total (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devi->lignes as $index => $ligne)
            <tr>
                <td style="text-align: center; color: #ccc;">{{ $index + 1 }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $ligne->designation }}</div>
                    @if($ligne->description)
                        <div style="font-size: 9pt; color: #6b7280; font-style: italic;">{{ $ligne->description }}</div>
                    @endif
                    <div style="font-size: 8pt; color: #9ca3af; text-transform: uppercase; margin-top: 5px;">
                        {{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} / {{ $ligne->unite }}
                    </div>
                </td>
                <td style="text-align: center; font-weight: bold;">
                    {{ $ligne->quantite }} <span style="font-size: 8pt; color: #9ca3af;">{{ $ligne->unite }}</span>
                </td>
                <td style="text-align: right; font-weight: bold;">
                    {{ number_format($ligne->quantite * $ligne->prix_unitaire, 0, ',', ' ') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-box">
            <div class="total-row">
                Sous-Total Hors Taxes <span class="val">{{ number_format($devi->sous_total, 0, ',', ' ') }}</span>
            </div>
            @if($devi->cout_main_oeuvre > 0)
            <div class="total-row">
                Main d'œuvre <span class="val">+ {{ number_format($devi->cout_main_oeuvre, 0, ',', ' ') }}</span>
            </div>
            @endif
            @if($devi->cout_transport > 0)
            <div class="total-row">
                Transport <span class="val">+ {{ number_format($devi->cout_transport, 0, ',', ' ') }}</span>
            </div>
            @endif
            @if($devi->remise > 0)
            <div class="total-row" style="color: #e11d48;">
                Remise commerciale <span class="val">- {{ number_format($devi->remise, 0, ',', ' ') }}</span>
            </div>
            @endif
            <div class="grand-total">
                <span class="lbl">TOTAL À PAYER (XAF)</span>
                <span class="val">{{ number_format($devi->total_general, 0, ',', ' ') }}</span>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    @if($devi->notes)
    <div class="notes">
        <div style="margin-bottom: 5px; font-weight: bold;">Conditions & Notes :</div>
        "{{ $devi->notes }}"
    </div>
    @endif

    <table class="signatures">
        <tr>
            <td>
                Signature Client
                <div class="signature-box"></div>
            </td>
            <td>
                Cachet DadouConfort
                <div class="signature-box"></div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Édité via Système DadouConfort - Artisan Premium
    </div>
</body>
</html>
