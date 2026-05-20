<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas - BoA</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1A5276; padding-bottom: 10px; }
        .header h1 { color: #1A5276; margin: 0; font-size: 20px; }
        .header p { margin: 5px 0; color: #666; }
        .filtros { background: #f0f0f0; padding: 8px; margin-bottom: 15px; border-radius: 4px; }
        .totales table { width: 100%; margin-bottom: 15px; }
        .totales td { text-align: center; padding: 8px; font-weight: bold; color: white; }
        .bg-success { background: #27AE60; }
        .bg-danger { background: #E74C3C; }
        .bg-dark { background: #333; }
        table.datos { width: 100%; border-collapse: collapse; }
        table.datos th { background: #1A5276; color: white; padding: 6px; text-align: left; font-size: 10px; }
        table.datos td { border: 1px solid #ddd; padding: 5px; font-size: 10px; }
        table.datos tr:nth-child(even) { background: #f9f9f9; }
        .footer { text-align: center; margin-top: 20px; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✈ Boliviana de Aviación (BoA)</h1>
        <p>Reporte de Ventas</p>
        <p>Generado: {{ date('d/m/Y H:i') }}</p>
    </div>

    @if(($filtros['fecha_inicio'] ?? null) || ($filtros['fecha_fin'] ?? null) || ($filtros['estado'] ?? null))
    <div class="filtros">
        <strong>Filtros:</strong>
        @if($filtros['fecha_inicio'] ?? null) Desde: {{ $filtros['fecha_inicio'] }} @endif
        @if($filtros['fecha_fin'] ?? null) Hasta: {{ $filtros['fecha_fin'] }} @endif
        @if($filtros['estado'] ?? null) Estado: {{ $filtros['estado'] }} @endif
    </div>
    @endif

    <div class="totales">
        <table>
            <tr>
                <td class="bg-success">Confirmadas: {{ $totales['confirmadas'] }}</td>
                <td class="bg-danger">Canceladas: {{ $totales['canceladas'] }}</td>
                <td class="bg-success">Monto: ${{ number_format($totales['monto_confirmadas'], 2) }}</td>
                <td class="bg-dark">Total: {{ $totales['total'] }}</td>
            </tr>
        </table>
    </div>

    <table class="datos">
        <thead>
            <tr>
                <th>Código</th>
                <th>Cliente</th>
                <th>Vuelo</th>
                <th>Ruta</th>
                <th>Asiento</th>
                <th>Pago</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
            <tr>
                <td>{{ $venta->codigo_venta }}</td>
                <td>{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</td>
                <td>{{ $venta->programacionVuelo->codigo_vuelo }}</td>
                <td>{{ $venta->programacionVuelo->aeropuertoOrigen->codigo_IATA }} → {{ $venta->programacionVuelo->aeropuertoDestino->codigo_IATA }}</td>
                <td>{{ $venta->tickets->first()->asiento->numero }} ({{ $venta->tickets->first()->asiento->tipoClase->nombre }})</td>
                <td>{{ ($venta->transacciones->first()->metodo_pago ?? '-') }}</td>
                <td>${{ number_format($venta->monto_total, 2) }}</td>
                <td>{{ $venta->estado }}</td>
                <td>{{ $venta->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Boliviana de Aviación (BoA) - Sistema de Información Web | Reporte generado automáticamente</p>
    </div>
</body>
</html>