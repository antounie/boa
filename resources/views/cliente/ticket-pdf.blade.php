<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    @page {
        size: 595pt 290pt;
        margin: 0;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10px;
        background: #ffffff;
        color: #1a1a1a;
        width: 595pt;
    }

    /* ── HEADER ── */
    .hdr {
        background-color: #1A5276;
        color: #ffffff;
        padding: 9px 18px;
    }
    .hdr-table { width: 100%; border-collapse: collapse; }
    .hdr-table td { vertical-align: middle; }
    .hdr-airline { font-size: 14px; font-weight: bold; letter-spacing: 0.5px; }
    .hdr-sub     { font-size: 8px; opacity: 0.75; margin-top: 1px; }
    .hdr-bp      { text-align: right; font-size: 11px; font-weight: bold;
                   letter-spacing: 3px; color: #AED6F1; }

    /* ── ACCENT STRIPE ── */
    .stripe { background-color: #2980B9; height: 3px; }

    /* ── MAIN BODY ── */
    .main-table { width: 100%; border-collapse: collapse; }

    .col-pax {
        width: 34%;
        padding: 10px 16px;
        border-right: 1px dashed #AED6F1;
        vertical-align: top;
        background-color: #FDFEFE;
    }
    .col-route {
        width: 28%;
        text-align: center;
        padding: 10px 6px;
        border-right: 1px dashed #AED6F1;
        vertical-align: middle;
        background-color: #FDFEFE;
    }
    .col-flight {
        width: 38%;
        padding: 10px 14px;
        vertical-align: top;
        background-color: #FDFEFE;
    }

    .lbl {
        font-size: 6.5px;
        text-transform: uppercase;
        color: #95a5a6;
        letter-spacing: 0.8px;
        margin-bottom: 1px;
    }
    .val-lg   { font-size: 12px; font-weight: bold; color: #1A5276; margin-bottom: 6px; }
    .val-md   { font-size: 10px; font-weight: bold; color: #1A5276; margin-bottom: 6px; }
    .val-sm   { font-size: 9px;  font-weight: bold; color: #1A5276; margin-bottom: 5px; }

    .iata  { font-size: 30px; font-weight: bold; color: #1A5276; line-height: 1; }
    .city  { font-size: 7px; color: #7f8c8d; margin-top: 1px; }
    .plane { font-size: 20px; color: #2980B9; margin: 5px 0; }

    /* flight detail rows */
    .fd-table { width: 100%; border-collapse: collapse; }
    .fd-table td { padding-bottom: 5px; vertical-align: top; }
    .fd-table .fd-lbl { width: 42%; }
    .fd-table .fd-val { width: 58%; }

    /* ── TEAR LINE ── */
    .tear { border-top: 1.5px dashed #85C1E9; margin: 0 0; }

    /* ── QR FOOTER ── */
    .qr-section {
        background-color: #EAF4FB;
        padding: 8px 16px 6px;
    }
    .qr-table { width: 100%; border-collapse: collapse; }
    .qr-table td { vertical-align: middle; }
    .col-qr-img { width: 88px; text-align: center; }
    .col-qr-img img { width: 80px; height: 80px; display: block; }
    .col-mid    { padding: 0 14px; border-left: 1px dashed #85C1E9; border-right: 1px dashed #85C1E9; }
    .col-right  { padding-left: 14px; }

    .tkt-num {
        font-size: 14px;
        font-weight: bold;
        color: #1A5276;
        letter-spacing: 0.5px;
    }
    .scan-tip { font-size: 7px; color: #7f8c8d; margin-top: 2px; font-style: italic; }
    .status-ok {
        display: inline-block;
        background-color: #1A5276;
        color: #fff;
        font-size: 7.5px;
        font-weight: bold;
        padding: 2px 8px;
        border-radius: 8px;
        margin-top: 4px;
    }
    .footer-note {
        font-size: 6.5px;
        color: #bdc3c7;
        text-align: center;
        padding: 4px 0 2px;
        border-top: 1px solid #d6eaf8;
        margin-top: 6px;
    }
    .reprog-banner {
        background-color: #FEF9E7;
        border-top: 2px solid #F39C12;
        padding: 5px 16px;
        font-size: 8px;
        color: #7D6608;
    }
    .reprog-banner strong { color: #B7770D; }
</style>
</head>
<body>
@php
    $prog    = $ticket->venta->programacionVuelo;
    $ori     = $ticket->subTramo ? $ticket->subTramo->aeropuertoOrigen : $prog->aeropuertoOrigen;
    $dest    = $ticket->subTramo ? $ticket->subTramo->aeropuertoDestino : $prog->aeropuertoDestino;
    $asn     = $ticket->asiento;
    $cli     = $ticket->venta->cliente;
    $salida  = \Carbon\Carbon::parse($prog->fecha_salida  . ' ' . $prog->hora_salida);
    $llegada = \Carbon\Carbon::parse($prog->fecha_llegada . ' ' . $prog->hora_llegada);
@endphp

{{-- ── HEADER ── --}}
<div class="hdr">
    <table class="hdr-table">
        <tr>
            <td>
                <div class="hdr-airline">&#9992; BOLIVIANA DE AVIACIÓN</div>
                <div class="hdr-sub">BoA &mdash; Sistema de Pasajes Aéreos</div>
            </td>
            <td><div class="hdr-bp">BOARDING PASS</div></td>
        </tr>
    </table>
</div>
<div class="stripe"></div>

{{-- ── MAIN BODY ── --}}
<table class="main-table">
<tr>

    {{-- Pasajero --}}
    <td class="col-pax">
        <div class="lbl">Pasajero</div>
        @php
            $paxNombre   = $ticket->pasajero_nombre   ?? $cli->nombre;
            $paxApellido = $ticket->pasajero_apellido ?? $cli->apellido;
        @endphp
        <div class="val-lg">{{ strtoupper($paxNombre . ' ' . $paxApellido) }}</div>

        <div class="lbl">Comprador</div>
        <div class="val-md">{{ $cli->nombre }} {{ $cli->apellido }}</div>

        <div class="lbl">Clase</div>
        <div class="val-md">{{ $asn->tipoClase->nombre }}</div>

        <div class="lbl">Método de Pago</div>
        <div class="val-md">{{ ($ticket->venta->transacciones->first()->metodo_pago ?? '-') }}</div>

        <div class="lbl">Monto Pagado</div>
        <div class="val-md">Bs. {{ number_format($ticket->venta->monto_total, 2) }}</div>
    </td>

    {{-- Ruta --}}
    <td class="col-route">
        <div class="iata">{{ $ori->codigo_IATA }}</div>
        <div class="city">{{ $ori->ciudad }}</div>
        <div class="plane">&#9992;</div>
        <div class="iata">{{ $dest->codigo_IATA }}</div>
        <div class="city">{{ $dest->ciudad }}</div>
    </td>

    {{-- Detalles de vuelo --}}
    <td class="col-flight">
        <table class="fd-table">
            <tr>
                <td class="fd-lbl"><div class="lbl">N° Vuelo</div></td>
                <td class="fd-val"><div class="val-lg">{{ $prog->codigo_vuelo }}</div></td>
            </tr>
            <tr>
                <td class="fd-lbl"><div class="lbl">Fecha Salida</div></td>
                <td class="fd-val"><div class="val-md">{{ $salida->isoFormat('D MMM Y') }}</div></td>
            </tr>
            <tr>
                <td class="fd-lbl"><div class="lbl">Hora Salida</div></td>
                <td class="fd-val"><div class="val-md">{{ $salida->format('H:i') }} hrs</div></td>
            </tr>
            <tr>
                <td class="fd-lbl"><div class="lbl">Llegada Est.</div></td>
                <td class="fd-val"><div class="val-md">{{ $llegada->format('H:i') }} hrs</div></td>
            </tr>
            <tr>
                <td class="fd-lbl"><div class="lbl">Asiento</div></td>
                <td class="fd-val"><div class="val-lg">{{ $asn->numero }}</div></td>
            </tr>
            <tr>
                <td class="fd-lbl"><div class="lbl">Aeronave</div></td>
                <td class="fd-val"><div class="val-sm">{{ $prog->aeronave->modelo ?? 'N/D' }}</div></td>
            </tr>
        </table>
    </td>

</tr>
</table>

@if($prog->fecha_original)
<div class="reprog-banner">
    &#9888; <strong>VUELO REPROGRAMADO:</strong>
    Fecha original: <strong>{{ \Carbon\Carbon::parse($prog->fecha_original)->format('d/m/Y') }} {{ $prog->hora_original }} hrs</strong>
    &rarr; Nueva fecha: <strong>{{ \Carbon\Carbon::parse($prog->fecha_salida)->format('d/m/Y') }} {{ $prog->hora_salida }} hrs</strong>
    &mdash; Motivo: {{ $prog->motivo_reprogramacion }}
</div>
@endif

{{-- ── TEAR LINE ── --}}
<div class="tear"></div>

{{-- ── QR + CODES ── --}}
<div class="qr-section">
    <table class="qr-table">
        <tr>
            <td class="col-qr-img">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="80" height="80" alt="QR">
            </td>
            <td class="col-mid">
                <div class="lbl">N° Ticket</div>
                <div class="tkt-num">{{ $ticket->numero_ticket }}</div>
                <div class="scan-tip">Escanee el QR para verificar este ticket</div>
                <div class="status-ok">&#10003; {{ strtoupper($ticket->estado) }}</div>
            </td>
            <td class="col-right">
                <div class="lbl">Código de Venta</div>
                <div class="val-sm">{{ $ticket->venta->codigo_venta }}</div>

                <div class="lbl">Emitido el</div>
                <div class="val-sm">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>

                <div class="lbl">Origen &rarr; Destino</div>
                <div class="val-sm">{{ $ori->nombre }} &rarr; {{ $dest->nombre }}</div>
            </td>
        </tr>
    </table>
    <div class="footer-note">
        Boliviana de Aviación (BoA) &mdash; Este documento es válido como tarjeta de embarque digital. Por favor preséntelo en el mostrador de abordaje.
    </div>
</div>

</body>
</html>
