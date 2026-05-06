<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ingresos - BoA</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1A5276; padding-bottom: 10px; }
        .header h1 { color: #1A5276; margin: 0; font-size: 20px; }
        .header p { margin: 5px 0; color: #666; }
        .filtros { background: #f0f0f0; padding: 8px; margin-bottom: 15px; border-radius: 4px; }
        .totales table { width: 100%; margin-bottom: 15px; }
        .totales td { text-align: center; padding: 8px; font-weight: bold; color: white; }
        .bg-success { background: #27AE60; }
        .bg-primary { background: #1A5276; }
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
        <p>Reporte de Ingresos Financieros</p>
        <p>Generado: {{ date('d/m/Y H:i') }}</p>
    </div>

    @if($filtros['fecha_inicio'] || $filtros['fecha_fin'])
    <div class="filtros">
        <strong>Filtros:</strong>
        @if($filtros['fecha_inicio']) Desde: {{ $filtros['fecha_inicio'] }} @endif
        @if($filtros['fecha_fin']) Hasta: {{ $filtros['fecha_fin'] }} @endif
    </div>
    @endif

    <div class="totales">
        <table>
            <tr>
                <td class="bg-success">Monto Total: ${{ number_format($totales['monto_total'], 2) }}</td>
                <td class="bg-primary">Total Pasajes: {{ $totales['pasajes_total'] }}</td>
                <td class="bg-dark">Registros: {{ $totales['cantidad'] }}</td>
            </tr>
        </table>
    </div>

    <table class="datos">
        <thead>
            <tr>
                <th>#</th>
                <th>Vuelo</th>
                <th>Ruta</th>
                <th>Pasajes</th>
                <th>Monto Total</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ingresos as $ingreso)
            <tr>
                <td>{{ $ingreso->id }}</td>
                <td>{{ $ingreso->programacionVuelo->vuelo->codigo_vuelo }}</td>
                <td>{{ $ingreso->programacionVuelo->ruta->aeropuertoOrigen->codigo_IATA }} → {{ $ingreso->programacionVuelo->ruta->aeropuertoDestino->codigo_IATA }}</td>
                <td>{{ $ingreso->cantidad_pasajes }}</td>
                <td>${{ number_format($ingreso->monto_total, 2) }}</td>
                <td>{{ $ingreso->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Boliviana de Aviación (BoA) - Sistema de Información Web | Reporte generado automáticamente</p>
    </div>
</body>
</html>