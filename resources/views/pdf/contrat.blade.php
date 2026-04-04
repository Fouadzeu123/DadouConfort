<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #18181b; /* zinc-900 */
            color: white;
            padding: 40px;
            margin-bottom: 40px;
        }
        .contract-title {
            font-size: 20pt;
            font-weight: 900;
            text-transform: uppercase;
            margin: 0;
        }
        .contract-number {
            font-size: 14pt;
            font-weight: bold;
            color: #e11d48; /* primary */
            margin-top: 5px;
        }
        .document-label {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.5;
            margin-bottom: 10px;
        }
        .content {
            padding: 0 50px;
            text-align: justify;
            white-space: pre-wrap;
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
            color: #9ca3af;
        }
        .signature-box {
            height: 100px;
            border-bottom: 1px dotted #ccc;
            margin: 10px 40px;
        }
        .financial-summary {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 15px;
            margin-top: 40px;
            font-size: 10pt;
        }
        .financial-row {
            margin-bottom: 5px;
        }
        .val {
            font-weight: bold;
            float: right;
        }
        .footer {
            margin-top: 100px;
            text-align: center;
            font-size: 7pt;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="document-label">Document Contractuel</div>
        <h1 class="contract-title">{{ $title }}</h1>
        <div class="contract-number">{{ $contrat->numero }}</div>
    </div>

    <div class="content">
        {!! nl2br(e($generatedContent)) !!}
    </div>

    <table class="signatures">
        <tr>
            <td>
                Signature DadouConfort
                <div class="signature-box"></div>
            </td>
            <td>
                Signature {{ $partyLabel }}
                <div class="signature-box"></div>
            </td>
        </tr>
    </table>

    <div class="content" style="margin-top: 50px;">
        <div class="financial-summary">
            <div class="financial-row">
                Montant Convenu : <span class="val">{{ number_format($contrat->montant_convenu, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="financial-row">
                Acompte à la signature : <span class="val">{{ number_format($contrat->acompte, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="financial-row">
                Solde restant : <span class="val">{{ number_format($contrat->montant_convenu - $contrat->acompte, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>
    </div>

    <div class="footer">
        DadouConfort ERP - Pilotage d'Attribution et Contrôle Digital<br>
        Document généré le {{ now()->format('d/m/Y H:i') }} - Authenticité vérifiable par QR Code interne
    </div>
</body>
</html>
