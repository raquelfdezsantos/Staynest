<?php

return [
    // Política de cancelación para clientes (días antes del check-in => % reembolso sobre lo pagado)
    // Orden: mayor a menor para la evaluación secuencial.
    'cancellation_policy' => [
        ['min_days' => 15, 'percent' => 100],
        ['min_days' => 7,  'percent' => 50],
        ['min_days' => 0,  'percent' => 0],
    ],
];
