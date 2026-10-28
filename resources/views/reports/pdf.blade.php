<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventaris &amp; Nilai Aset</title>
    <style>
        @page {
            margin: 28px 24px 52px 24px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .header-table td {
            vertical-align: top;
        }

        .brand {
            font-size: 15px;
            font-weight: bold;
            color: #4338ca;
            margin: 0 0 2px 0;
        }

        .subtitle {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-title {
            font-size: 13px;
            font-weight: bold;
            text-align: right;
            margin: 0 0 4px 0;
        }

        .doc-meta {
            font-size: 8px;
            color: #64748b;
            text-align: right;
            line-height: 1.5;
        }

        .divider {
            border-top: 2px solid #4338ca;
            margin: 0 0 12px 0;
        }

        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #475569;
            margin: 0 0 6px 0;
        }

        .filters {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .filters td {
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            padding: 5px 8px;
            font-size: 8px;
        }

        .filters .label {
            color: #64748b;
            font-weight: bold;
            width: 10%;
            text-transform: uppercase;
        }

        .filters .value {
            width: 40%;
        }

        .metrics {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .metrics td {
            width: 33.33%;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            padding: 8px 10px;
        }

        .metric-label {
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #64748b;
            font-weight: bold;
        }

        .metric-value {
            font-size: 13px;
            font-weight: bold;
            margin-top: 3px;
        }

        .accent {
            color: #4338ca;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            background-color: #eef2ff;
            border-bottom: 1px solid #c7d2fe;
            color: #3730a3;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            padding: 6px 6px;
        }

        .data-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 5px 6px;
            vertical-align: top;
            font-size: 8px;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .text-right {
            text-align: right;
        }

        .mono {
            font-family: "DejaVu Sans Mono", monospace;
            color: #4338ca;
            font-weight: bold;
        }

        .muted {
            color: #64748b;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            font-size: 7px;
        }

        .empty {
            text-align: center;
            color: #94a3b8;
            padding: 18px 0;
        }

        .status-table {
            width: 55%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .status-table th {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 7.5px;
            text-align: left;
            padding: 4px 6px;
            text-transform: uppercase;
        }

        .status-table td {
            border: 1px solid #e2e8f0;
            padding: 4px 6px;
            font-size: 8px;
        }

        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -38px;
            border-top: 1px solid #cbd5e1;
            padding-top: 5px;
            font-size: 7px;
            color: #64748b;
        }

        .footer .right {
            float: right;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div class="brand">IT Inventory Trans</div>
                <div class="subtitle">Asset Operations</div>
            </td>
            <td style="width: 50%;">
                <div class="doc-title">Laporan Inventaris &amp; Nilai Aset</div>
                <div class="doc-meta">
                    Dicetak: {{ $generatedAt->translatedFormat('d F Y H:i') }}<br>
                    Oleh: {{ $generatedBy }}
                </div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="section-title">Filter Aktif</div>
    <table class="filters">
        @foreach (array_chunk($filterLabels, 2, true) as $filterRow)
            <tr>
                @foreach ($filterRow as $label => $value)
                    <td class="label">{{ $label }}</td>
                    <td class="value">{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>

    <table class="metrics">
        <tr>
            <td>
                <div class="metric-label">Jumlah Aset Terfilter</div>
                <div class="metric-value">{{ number_format($totalCount, 0, ',', '.') }} unit</div>
            </td>
            <td>
                <div class="metric-label">Total Nilai Perolehan</div>
                <div class="metric-value accent">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="metric-label">Rata-rata Nilai / Unit</div>
                <div class="metric-value">Rp {{ number_format($averageValue, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Breakdown Status</div>
    <table class="status-table">
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-right">Jumlah</th>
                <th class="text-right">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($statusBreakdown as $row)
                <tr>
                    <td>{{ \App\Models\Asset::STATUSES[$row->status] ?? $row->status }}</td>
                    <td class="text-right">{{ number_format($row->count, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->total_cost, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty">Belum ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Rincian Aset</div>
    <table class="data-table">
        <colgroup>
            <col style="width: 4%;">
            <col style="width: 12%;">
            <col style="width: 20%;">
            <col style="width: 11%;">
            <col style="width: 15%;">
            <col style="width: 11%;">
            <col style="width: 10%;">
            <col style="width: 17%;">
        </colgroup>
        <thead>
            <tr>
                <th>No</th>
                <th>Asset Tag</th>
                <th>Nama Perangkat &amp; Merek</th>
                <th>Kategori</th>
                <th>Lokasi &amp; PIC</th>
                <th>Status</th>
                <th>Tgl Pembelian</th>
                <th class="text-right">Biaya Perolehan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assets as $index => $asset)
                <tr>
                    <td class="muted">{{ $index + 1 }}</td>
                    <td class="mono">{{ $asset->asset_tag }}</td>
                    <td>
                        <strong>{{ $asset->name }}</strong><br>
                        <span class="muted">{{ $asset->brand }} &bull; {{ $asset->model }}</span>
                    </td>
                    <td>{{ $asset->category?->name ?? '-' }}</td>
                    <td>
                        {{ $asset->location?->name ?? '-' }}<br>
                        <span class="muted">{{ $asset->assignedUser?->name ?: 'Tanpa PIC' }}</span>
                    </td>
                    <td>
                        <span class="badge">{{ \App\Models\Asset::STATUSES[$asset->status] ?? $asset->status }}</span>
                    </td>
                    <td class="muted">{{ $asset->purchase_date ? $asset->purchase_date->translatedFormat('d M Y') : '-' }}</td>
                    <td class="text-right">
                        {{ $asset->purchase_cost ? 'Rp ' . number_format($asset->purchase_cost, 0, ',', '.') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty">Tidak ada data aset yang cocok dengan filter laporan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sumber: IT Inventory Trans &bull; Laporan dibuat otomatis dari data inventaris
        <span class="right">{{ $generatedAt->translatedFormat('d/m/Y H:i') }} WIB</span>
    </div>
</body>
</html>
