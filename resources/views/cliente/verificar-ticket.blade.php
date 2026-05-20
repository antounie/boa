<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Ticket — BoA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', Arial, sans-serif; }
        .verify-card {
            max-width: 520px;
            margin: 40px auto;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(26,82,118,0.15);
        }
        .verify-header {
            background: linear-gradient(135deg, #1A5276, #2980B9);
            color: white;
            padding: 24px 28px 20px;
        }
        .verify-header .airline { font-size: 1.1rem; font-weight: 700; letter-spacing: 0.5px; }
        .verify-header .subtitle { font-size: 0.8rem; opacity: 0.8; }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 600;
        }
        .status-emitido { background: #d4edda; color: #155724; }
        .status-otro    { background: #f8d7da; color: #721c24; }
        .route-box {
            background: linear-gradient(135deg, #EBF5FB, #D6EAF8);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 16px 0;
        }
        .iata-code { font-size: 2.4rem; font-weight: 800; color: #1A5276; line-height: 1; }
        .city-label { font-size: 0.75rem; color: #7f8c8d; }
        .arrow-icon { font-size: 1.5rem; color: #2980B9; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .info-item label { font-size: 0.7rem; text-transform: uppercase; color: #95a5a6; letter-spacing: 0.5px; display: block; }
        .info-item .val { font-size: 0.95rem; font-weight: 600; color: #1A5276; }
        .ticket-num { font-size: 1.3rem; font-weight: 800; color: #1A5276; letter-spacing: 1px; }
        .footer-note { font-size: 0.72rem; color: #aab; text-align: center; margin-top: 8px; }
        .valid-banner {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }
        .valid-banner i { font-size: 1.5rem; color: #155724; }
        .valid-banner .text { font-size: 0.88rem; color: #155724; font-weight: 600; }
    </style>
</head>
<body>
@php
    $prog  = $ticket->venta->programacionVuelo;
    $ori   = $ticket->subTramo ? $ticket->subTramo->aeropuertoOrigen : $prog->aeropuertoOrigen;
    $dest  = $ticket->subTramo ? $ticket->subTramo->aeropuertoDestino : $prog->aeropuertoDestino;
    $asn   = $ticket->asiento;
    $cli   = $ticket->venta->cliente;
    $salida  = \Carbon\Carbon::parse($prog->fecha_salida . ' ' . $prog->hora_salida);
    $llegada = \Carbon\Carbon::parse($prog->fecha_llegada . ' ' . $prog->hora_llegada);
    $emitido = $ticket->estado === 'Emitido';
@endphp

<div class="verify-card bg-white">

    {{-- Header --}}
    <div class="verify-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="airline"><i class="bi bi-airplane-fill me-2"></i>BOLIVIANA DE AVIACIÓN</div>
                <div class="subtitle">Verificación de Ticket Digital</div>
            </div>
            <div class="text-end">
                <div style="font-size:0.7rem;opacity:0.7">Escaneado</div>
                <div style="font-size:0.85rem;font-weight:600">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="p-4">

        {{-- Status --}}
        <div class="valid-banner">
            <i class="bi bi-{{ $emitido ? 'patch-check-fill' : 'x-circle-fill' }}"
               style="color:{{ $emitido ? '#155724' : '#721c24' }}"></i>
            <div>
                <div class="text">{{ $emitido ? 'Ticket Válido y Emitido' : 'Ticket no disponible' }}</div>
                <div style="font-size:0.75rem;color:#6c757d">Estado: {{ $ticket->estado }}</div>
            </div>
        </div>

        {{-- Ticket number --}}
        <div class="mb-3">
            <div style="font-size:0.7rem;text-transform:uppercase;color:#95a5a6;letter-spacing:0.5px">N° Ticket</div>
            <div class="ticket-num">{{ $ticket->numero_ticket }}</div>
        </div>

        {{-- Route --}}
        <div class="route-box">
            <div class="d-flex justify-content-around align-items-center">
                <div>
                    <div class="iata-code">{{ $ori->codigo_IATA }}</div>
                    <div class="city-label">{{ $ori->ciudad }}</div>
                    <div style="font-size:0.7rem;color:#95a5a6">{{ $ori->nombre }}</div>
                </div>
                <div class="arrow-icon">
                    <i class="bi bi-airplane-fill"></i>
                </div>
                <div>
                    <div class="iata-code">{{ $dest->codigo_IATA }}</div>
                    <div class="city-label">{{ $dest->ciudad }}</div>
                    <div style="font-size:0.7rem;color:#95a5a6">{{ $dest->nombre }}</div>
                </div>
            </div>
        </div>

        {{-- Info grid --}}
        @php
            $paxNombre   = $ticket->pasajero_nombre   ?? $cli->nombre;
            $paxApellido = $ticket->pasajero_apellido ?? $cli->apellido;
        @endphp
        <div class="info-grid mb-3">
            <div class="info-item">
                <label>Pasajero</label>
                <div class="val">{{ $paxNombre }} {{ $paxApellido }}</div>
            </div>
            <div class="info-item">
                <label>Comprador</label>
                <div class="val" style="font-size:0.82rem">{{ $cli->nombre }} {{ $cli->apellido }}</div>
            </div>
            <div class="info-item">
                <label>N° Vuelo</label>
                <div class="val">{{ $prog->codigo_vuelo }}</div>
            </div>
            <div class="info-item">
                <label>Asiento / Clase</label>
                <div class="val">{{ $asn->numero }} — {{ $asn->tipoClase->nombre }}</div>
            </div>
            <div class="info-item">
                <label>Fecha de Salida</label>
                <div class="val">{{ $salida->isoFormat('D MMM Y') }}</div>
            </div>
            <div class="info-item">
                <label>Hora Salida</label>
                <div class="val">{{ $salida->format('H:i') }} hrs</div>
            </div>
            <div class="info-item">
                <label>Llegada estimada</label>
                <div class="val">{{ $llegada->format('H:i') }} hrs</div>
            </div>
            <div class="info-item">
                <label>Código de Venta</label>
                <div class="val" style="font-size:0.82rem">{{ $ticket->venta->codigo_venta }}</div>
            </div>
            <div class="info-item">
                <label>Monto Pagado</label>
                <div class="val">Bs. {{ number_format($ticket->venta->monto_total, 2) }}</div>
            </div>
            <div class="info-item">
                <label>Fecha de Emisión</label>
                <div class="val" style="font-size:0.82rem">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div class="footer-note">
            <i class="bi bi-shield-check me-1"></i>
            Documento verificado — Boliviana de Aviación (BoA) Sistema de Información Web
        </div>
    </div>
</div>

</body>
</html>
