<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Pago completado</title></head>
<body style="font-family: Arial, sans-serif; color:#222; line-height:1.5; max-width:600px; margin:0 auto; padding:20px;">
	<div style="background:#4caf50; color:white; padding:20px; border-radius:8px 8px 0 0; text-align:center;">
		<h2 style="margin:0; font-size:24px;">✓ Pago completado</h2>
		<p style="margin:8px 0 0 0; font-size:14px; opacity:0.9;">El pago adicional ha sido procesado exitosamente</p>
	</div>

	<div style="background:#fff; border:2px solid #e0e0e0; border-top:none; border-radius:0 0 8px 8px; padding:24px;">
		<p style="margin:0 0 16px 0; font-size:16px;">Hola <strong>{{ $reservation->user->name }}</strong>,</p>
		<p style="margin:0 0 20px 0; color:#666;">
			Hemos procesado correctamente el pago adicional correspondiente a la modificación de tu reserva en <strong>{{ $reservation->property->name }}</strong>.
		</p>

		<div style="background:#f5f5f5; border-radius:8px; padding:16px; margin:20px 0;">
			<h3 style="margin:0 0 12px 0; font-size:16px; color:#333;">💳 Detalle del pago</h3>
			<table style="width:100%; border-collapse:collapse;">
				<tr>
					<td style="padding:8px 0; color:#666;"><strong>Reserva:</strong></td>
					<td style="padding:8px 0; text-align:right;">{{ $reservation->code ?? '#'.$reservation->id }}</td>
				</tr>
				<tr style="border-top:1px solid #e0e0e0;">
					<td style="padding:8px 0; color:#666;"><strong>Propiedad:</strong></td>
					<td style="padding:8px 0; text-align:right;">{{ $reservation->property->name }}</td>
				</tr>
				<tr style="border-top:1px solid #e0e0e0;">
					<td style="padding:8px 0; color:#666;"><strong>Fechas:</strong></td>
					<td style="padding:8px 0; text-align:right;">{{ $reservation->check_in->format('d/m/Y') }} → {{ $reservation->check_out->format('d/m/Y') }}</td>
				</tr>
				<tr style="border-top:2px solid #4caf50;">
					<td style="padding:12px 0; color:#333; font-size:16px;"><strong>Importe abonado:</strong></td>
					<td style="padding:12px 0; text-align:right; font-size:20px; font-weight:bold; color:#4caf50;">{{ number_format($amount, 2, ',', '.') }} €</td>
				</tr>
				<tr style="border-top:1px solid #e0e0e0;">
					<td style="padding:8px 0; color:#666;"><strong>Total de la reserva:</strong></td>
					<td style="padding:8px 0; text-align:right; font-weight:bold;">{{ number_format($reservation->total_price, 2, ',', '.') }} €</td>
				</tr>
				<tr>
					<td style="padding:8px 0; color:#666;"><strong>Total pagado:</strong></td>
					<td style="padding:8px 0; text-align:right; font-weight:bold; color:#4caf50;">{{ number_format($reservation->paidAmount(), 2, ',', '.') }} €</td>
				</tr>
			</table>
		</div>

		<div style="background:#e8f5e9; border-left:4px solid #4caf50; padding:12px; margin:20px 0; border-radius:4px;">
			<p style="margin:0; color:#2e7d32; font-size:14px;">
				<strong>✓ Tu reserva está completamente pagada.</strong> No tienes pagos pendientes para esta reserva.
			</p>
		</div>

		<hr style="border:none; border-top:1px solid #e0e0e0; margin:24px 0;">
		
		<p style="color:#666; font-size:13px; margin:0; text-align:center;">
			Gracias por confiar en nosotros.<br>
			<strong>Staynest</strong> - Tu alojamiento perfecto
		</p>
	</div>
</body>
</html>
