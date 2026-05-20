<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Vuelos - BoA</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1A5276; padding-bottom: 10px; }
        .header h1 { color: #1A5276; margin: 0; font-size: 20px; }
        .header p { margin: 5px 0; color: #666; }
        .filtros { background: #f0f0f0; padding: 8px; margin-bottom: 15px; border-radius: 4px; }
        .totales { margin-bottom: 15px; }
        .totales table { width: 100%; }
        .totales td { text-align: center; padding: 8px; font-weight: bold; color: white; }
        .bg-primary { background: #1A5276; }
        .bg-warning { background: #F39C12; }
        .bg-success { background: #27AE60; }
        .bg-dark { background: #333; }
        table.datos { width: 100%; border-collapse: collapse; }
        table.datos th { background: #1A5276; color: white; padding: 6px; text-align: left; font-size: 10px; }
        table.datos td { border: 1px solid #ddd; padding: 5px; font-size: 10px; }
        table.datos tr:nth-child(even) { background: #f9f9f9; }
        .footer { text-align: center; margin-top: 20px; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .badge { padding: 2px 6px; border-radius: 3px; color: white; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✈ Boliviana de Aviación (BoA)</h1>
        <p>Reporte de Vuelos Programados</p>
        <p>Generado: {{ date('d/m/Y H:i') }}</p>
    </div>

    @if(($filtros['fecha_inicio'] ?? null) || ($filtros['fecha_fin'] ?? null) || ($filtros['estado'] ?? null))
    <div class="filtros">
        <strong>Filtros aplicados:</strong>
        @if($filtros['fecha_inicio'] ?? null) Desde: {{ $filtros['fecha_inicio'] }} @endif
        @if($filtros['fecha_fin'] ?? null) Hasta: {{ $filtros['fecha_fin'] }} @endif
        @if($filtros['estado'] ?? null) Estado: {{ $filtros['estado'] }} @endif
    </div>
    @endif

    <div class="totales">
        <table>
            <tr>
                <td class="bg-primary">Programados: {{ $totales['programados'] }}</td>
                <td class="bg-warning">Completos: {{ $totales['completos'] }}</td>
                <td class="bg-success">Salidos: {{ $totales['salidos'] }}</td>
                <td class="bg-dark">Total: {{ $totales['total'] }}</td>
            </tr>
        </table>
    </div>

    <table class="datos">
        <thead>
            <tr>
                <th>Vuelo</th>
                <th>Ruta</th>
                <th>Aeronave</th>
                <th>Fecha Salida</th>
                <th>Hora</th>
                <th>Precio</th>
                <th>Vendidos</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($programaciones as $prog)
            <tr>
                <td><strong>{{ $prog->codigo_vuelo }}</strong></td>
                <td>{{ $prog->aeropuertoOrigen->codigo_IATA }} → {{ $prog->aeropuertoDestino->codigo_IATA }}</td>
                <td>{{ $prog->aeronave->matricula }}</td>
                <td>{{ $prog->fecha_salida }}</td>
                <td>{{ $prog->hora_salida }}</td>
                <td>${{ number_format($prog->precio_base, 2) }}</td>
                <td>{{ $prog->asientos_vendidos }}/{{ $prog->aeronave->capacidad_total }}</td>
                <td>{{ $prog->estado }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Boliviana de Aviación (BoA) - Sistema de Información Web | Reporte generado automáticamente</p>
    </div>
</body>
</html>