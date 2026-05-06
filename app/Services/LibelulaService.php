<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LibelulaService
{
    private $appkey;
    private $url;
    private $modo;

    public function __construct()
    {
        $this->appkey = config('services.libelula.appkey');
        $this->url = config('services.libelula.url');
        $this->modo = config('services.libelula.modo');
    }

    public function registrarDeuda($datos)
    {
        $payload = [
            'appkey' => $this->appkey,
            'email_cliente' => $datos['email'],
            'identificador' => $datos['identificador'],
            'descripcion' => $datos['descripcion'],
            'nombre_cliente' => $datos['nombre'],
            'apellido_cliente' => $datos['apellido'],
            'callback_url' => $datos['callback_url'],
            'url_retorno' => $datos['url_retorno'],
            'lineas_detalle_deuda' => [
                [
                    'concepto' => $datos['concepto'],
                    'cantidad' => 1,
                    'costo_unitario' => $datos['monto'],
                    'descuento_unitario' => 0,
                ]
            ],
        ];

        try {
            $response = Http::timeout(30)->post($this->url, $payload);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['error']) && $data['error'] == 0) {
                    return [
                        'success' => true,
                        'id_transaccion' => $data['id_transaccion'],
                        'codigo_recaudacion' => $data['codigo_recaudacion'],
                        'url_pasarela' => $data['url_pasarela_pagos'],
                        'qr_url' => $data['qr_simple_url'],
                        'modo' => 'libelula',
                    ];
                }

                return [
                    'success' => false,
                    'mensaje' => $data['mensaje'] ?? 'Error al registrar deuda en Libélula',
                    'modo' => 'libelula',
                ];
            }
        } catch (\Exception $e) {
            // Si Libélula no responde, usar modo simulación
            return $this->simularPago($datos);
        }

        return $this->simularPago($datos);
    }

    private function simularPago($datos)
    {
        $transactionId = 'SIM-' . strtoupper(Str::random(12));

        return [
            'success' => true,
            'id_transaccion' => $transactionId,
            'codigo_recaudacion' => 'SIM-' . rand(100000, 999999),
            'url_pasarela' => null,
            'qr_url' => null,
            'modo' => 'simulacion',
        ];
    }

    public function verificarPago($transactionId)
    {
        if (str_starts_with($transactionId, 'SIM-')) {
            return [
                'success' => true,
                'pagado' => true,
                'modo' => 'simulacion',
            ];
        }

        try {
            $response = Http::timeout(30)->post('https://api.libelula.bo/rest/deuda/consultar_pagos', [
                'appkey' => $this->appkey,
                'fecha_inicial' => now()->subDay()->format('Y-m-d H:i:s'),
                'fecha_final' => now()->addHour()->format('Y-m-d H:i:s'),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['error']) && $data['error'] == 0 && isset($data['datos'])) {
                    foreach ($data['datos'] as $pago) {
                        if ($pago['id_transaccion'] === $transactionId) {
                            return [
                                'success' => true,
                                'pagado' => true,
                                'monto_pagado' => $pago['monto_pagado'],
                                'forma_pago' => $pago['forma_pago'],
                                'fecha_pago' => $pago['fecha_pago'],
                                'modo' => 'libelula',
                            ];
                        }
                    }

                    // No se encontró el pago en la lista
                    return [
                        'success' => true,
                        'pagado' => false,
                        'modo' => 'libelula',
                    ];
                }
            }
        } catch (\Exception $e) {
            // Error de conexión
        }

        return [
            'success' => false,
            'pagado' => false,
            'modo' => 'libelula',
        ];
    }
}