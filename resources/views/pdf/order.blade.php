<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Guia de Trabalho — {{ $order->reference }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 11px;
    color: #1a202c;
    background: #ffffff;
    padding: 0;
  }

  /* ── Header ───────────────────────────────────────────── */
  .header {
    background: #1e40af;
    color: white;
    padding: 18px 28px;
    display: table;
    width: 100%;
  }
  .header-left  { display: table-cell; vertical-align: middle; }
  .header-right { display: table-cell; vertical-align: middle; text-align: right; }

  .lab-name {
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 1px;
  }
  .lab-sub {
    font-size: 10px;
    opacity: 0.8;
    margin-top: 2px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }
  .doc-title {
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }
  .doc-ref {
    font-size: 18px;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    margin-top: 2px;
  }

  /* ── Status Badge ─────────────────────────────────────── */
  .status-bar {
    padding: 6px 28px;
    background: #eff6ff;
    border-bottom: 2px solid #bfdbfe;
    display: table;
    width: 100%;
  }
  .status-badge {
    display: inline-block;
    padding: 3px 12px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .status-awaiting_pickup   { background: #fef9c3; color: #92400e; }
  .status-in_transit        { background: #dbeafe; color: #1e40af; }
  .status-at_lab            { background: #e0e7ff; color: #3730a3; }
  .status-in_production     { background: #f3e8ff; color: #6b21a8; }
  .status-quality_check     { background: #ffedd5; color: #9a3412; }
  .status-awaiting_delivery { background: #ccfbf1; color: #115e59; }
  .status-delivered         { background: #dcfce7; color: #14532d; }
  .status-cancelled         { background: #fee2e2; color: #7f1d1d; }

  /* ── Body Layout ──────────────────────────────────────── */
  .body { padding: 18px 28px; }

  /* ── Info Grid ────────────────────────────────────────── */
  .grid-2 { display: table; width: 100%; margin-bottom: 16px; }
  .grid-2 .col { display: table-cell; vertical-align: top; width: 50%; padding-right: 12px; }
  .grid-2 .col:last-child { padding-right: 0; padding-left: 12px; }

  .card {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
  }
  .card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 7px 12px;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #64748b;
  }
  .card-body { padding: 10px 12px; }

  .dl { width: 100%; }
  .dl tr td { padding: 3px 0; font-size: 10.5px; vertical-align: top; }
  .dl tr td:first-child { color: #64748b; width: 42%; }
  .dl tr td:last-child  { font-weight: 600; color: #1e293b; }

  /* ── Work Items Table ─────────────────────────────────── */
  .items-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
  .items-table thead tr { background: #1e40af; color: white; }
  .items-table thead th {
    padding: 7px 10px;
    text-align: left;
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .items-table thead th:last-child { text-align: right; }
  .items-table tbody tr { border-bottom: 1px solid #e2e8f0; }
  .items-table tbody tr:nth-child(even) { background: #f8fafc; }
  .items-table tbody td {
    padding: 7px 10px;
    font-size: 10.5px;
    vertical-align: middle;
  }
  .items-table tbody td:last-child { text-align: right; font-weight: 600; }
  .items-table tfoot tr { background: #f1f5f9; font-weight: 700; }
  .items-table tfoot td {
    padding: 8px 10px;
    font-size: 11px;
    border-top: 2px solid #cbd5e1;
  }
  .items-table tfoot td:last-child { text-align: right; font-size: 12px; color: #1e40af; }

  .tooth-badge {
    display: inline-block;
    background: #dbeafe;
    color: #1e40af;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    line-height: 22px;
    text-align: center;
    font-weight: 700;
    font-size: 10px;
  }

  .tag {
    display: inline-block;
    padding: 1px 7px;
    border-radius: 999px;
    font-size: 9px;
    font-weight: 600;
  }
  .tag-shade    { background: #fef9c3; color: #78350f; }
  .tag-finish   { background: #f3e8ff; color: #6b21a8; }

  /* ── Notes ────────────────────────────────────────────── */
  .notes-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 16px;
  }
  .notes-box .notes-label {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #92400e;
    margin-bottom: 5px;
  }
  .notes-box p { font-size: 10.5px; color: #1e293b; line-height: 1.5; }

  /* ── Barcode Section ──────────────────────────────────── */
  .barcode-section {
    text-align: center;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 16px;
    background: #f8fafc;
  }
  .barcode-section .barcode-img { margin-bottom: 4px; }
  .barcode-section .barcode-text {
    font-family: 'Courier New', monospace;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 3px;
    color: #1e293b;
  }
  .barcode-section .barcode-ref {
    font-size: 9px;
    color: #94a3b8;
    margin-top: 2px;
  }

  /* ── Signatures ───────────────────────────────────────── */
  .sig-row { display: table; width: 100%; margin-top: 20px; }
  .sig-col  { display: table-cell; width: 33%; vertical-align: bottom; padding: 0 8px; }
  .sig-col:first-child { padding-left: 0; }
  .sig-col:last-child  { padding-right: 0; }
  .sig-line  { border-top: 1px solid #94a3b8; padding-top: 4px; margin-top: 32px; }
  .sig-label { font-size: 9px; color: #64748b; text-align: center; text-transform: uppercase; letter-spacing: 0.5px; }

  /* ── Footer ───────────────────────────────────────────── */
  .footer {
    position: fixed;
    bottom: 0;
    left: 0; right: 0;
    padding: 8px 28px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: table;
    width: 100%;
    font-size: 9px;
    color: #94a3b8;
  }
  .footer .fl { display: table-cell; }
  .footer .fr { display: table-cell; text-align: right; }

  /* ── Divider ──────────────────────────────────────────── */
  .divider { border: none; border-top: 1px dashed #cbd5e1; margin: 14px 0; }

  .section-title {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #64748b;
    margin-bottom: 8px;
  }
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
  <div class="header-left">
    <div class="lab-name">NAMELIS LAB</div>
    <div class="lab-sub">Laboratório de Prótese Dentária · Póvoa de Varzim, Portugal</div>
  </div>
  <div class="header-right">
    <div class="doc-title">Guia de Trabalho</div>
    <div class="doc-ref">{{ $order->reference }}</div>
  </div>
</div>

<!-- STATUS BAR -->
<div class="status-bar">
  <span>Estado:&nbsp;</span>
  <span class="status-badge status-{{ $order->status }}">
    {{ $statusLabels[$order->status] ?? $order->status }}
  </span>
  &nbsp;&nbsp;
  <span style="color:#64748b">Emitido em {{ now()->format('d/m/Y \à\s H:i') }}</span>
  @if($order->requested_delivery_date)
    &nbsp;&nbsp;·&nbsp;&nbsp;
    <span style="color:#1e40af; font-weight:600;">
      Prazo pretendido: {{ \Carbon\Carbon::parse($order->requested_delivery_date)->format('d/m/Y') }}
    </span>
  @endif
</div>

<div class="body">

  <!-- INFO GRID -->
  <div class="grid-2">
    <!-- Left: Clinic / Doctor -->
    <div class="col">
      <div class="card">
        <div class="card-header">Clínica / Médico Responsável</div>
        <div class="card-body">
          <table class="dl">
            <tr><td>Clínica</td><td>{{ $order->clinic?->name ?? '—' }}</td></tr>
            <tr><td>Médico</td><td>{{ $order->clinic?->doctor_name ?? '—' }}</td></tr>
            @if($order->clinic?->nif)
            <tr><td>NIF</td><td>{{ $order->clinic->nif }}</td></tr>
            @endif
            <tr><td>Morada</td><td>{{ $order->clinic?->address ?? '—' }}</td></tr>
            <tr><td>Telefone</td><td>{{ $order->clinic?->phone ?? '—' }}</td></tr>
          </table>
        </div>
      </div>
    </div>

    <!-- Right: Patient / Order info -->
    <div class="col">
      <div class="card">
        <div class="card-header">Dados do Pedido</div>
        <div class="card-body">
          <table class="dl">
            <tr><td>Ref. Paciente</td><td>{{ $order->patient_ref }}</td></tr>
            <tr><td>Tipo impressão</td><td>{{ $impressionLabels[$order->impression_type] ?? $order->impression_type }}</td></tr>
            <tr><td>Data do pedido</td><td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</td></tr>
            @if($order->requested_delivery_date)
            <tr><td>Entrega pretendida</td><td>{{ \Carbon\Carbon::parse($order->requested_delivery_date)->format('d/m/Y') }}</td></tr>
            @endif
            @if($order->assignedTech)
            <tr><td>Técnico</td><td>{{ $order->assignedTech->name }}</td></tr>
            @endif
            @if($order->estimated_price)
            <tr><td>Valor estimado</td><td style="font-size:12px;color:#1e40af;">€{{ number_format($order->estimated_price, 2, ',', '.') }}</td></tr>
            @endif
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- WORK ITEMS TABLE -->
  <div class="section-title">Trabalhos Solicitados</div>
  <table class="items-table">
    <thead>
      <tr>
        <th style="width:50px">Dente</th>
        <th>Categoria</th>
        <th>Tipo / Material</th>
        <th style="width:70px">Cor VITA</th>
        <th style="width:80px">Acabamento</th>
        <th style="width:70px">Valor</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->items as $item)
      @php
        $cat = $item->workCategory;
        $catParent = $cat?->parent;
        $catGrand  = $catParent?->parent;
      @endphp
      <tr>
        <td><span class="tooth-badge">{{ $item->tooth_fdi }}</span></td>
        <td>{{ $catGrand?->name ?? $catParent?->name ?? $cat?->name ?? '—' }}</td>
        <td>{{ $catParent?->name ? ($cat?->name ?? '—') : '—' }}</td>
        <td>
          @if($item->vita_shade)
            <span class="tag tag-shade">{{ $item->vita_shade }}</span>
          @else —
          @endif
        </td>
        <td>
          @if($item->finishing_stage)
            <span class="tag tag-finish">{{ ucfirst($item->finishing_stage) }}</span>
          @else —
          @endif
        </td>
        <td>
          @if($item->unit_price)
            €{{ number_format($item->unit_price, 2, ',', '.') }}
          @else —
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
    @php
      $total = $order->items->sum('unit_price');
    @endphp
    @if($total > 0)
    <tfoot>
      <tr>
        <td colspan="5" style="text-align:right; color:#64748b;">Total estimado</td>
        <td>€{{ number_format($total, 2, ',', '.') }}</td>
      </tr>
    </tfoot>
    @endif
  </table>

  @if($order->clinical_notes)
  <!-- CLINICAL NOTES -->
  <div class="notes-box">
    <div class="notes-label">Notas Clínicas</div>
    <p>{{ $order->clinical_notes }}</p>
  </div>
  @endif

  <!-- BARCODE -->
  @if($order->barcode)
  <div class="barcode-section">
    <div class="barcode-img">
      {!! $barcodeHtml !!}
    </div>
    <div class="barcode-text">{{ $order->barcode }}</div>
    <div class="barcode-ref">Código de rastreio interno — scan na receção</div>
  </div>
  @endif

  <hr class="divider">

  <!-- SIGNATURE FIELDS -->
  <div class="sig-row">
    <div class="sig-col">
      <div class="sig-line">
        <div class="sig-label">Rececionado por</div>
      </div>
    </div>
    <div class="sig-col">
      <div class="sig-line">
        <div class="sig-label">Técnico Responsável</div>
      </div>
    </div>
    <div class="sig-col">
      <div class="sig-line">
        <div class="sig-label">Controlo de Qualidade</div>
      </div>
    </div>
  </div>

</div><!-- /body -->

<!-- FOOTER -->
<div class="footer">
  <div class="fl">Namelis Lab · Póvoa de Varzim · contato@namelis.pt</div>
  <div class="fr">{{ $order->reference }} · Emitido {{ now()->format('d/m/Y') }}</div>
</div>

</body>
</html>
