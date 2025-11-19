<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Reserva modificada</title></head>
<body style="font-family: Arial, sans-serif; color:#222; line-height:1.5; max-width:600px; margin:0 auto; padding:20px;">
	<h2 style="margin:0 0 20px; color:#2c5aa0;">✓ Reserva modificada exitosamente</h2>
	<p>Hola <strong>{{ $reservation->user->name }}</strong>,</p>
	<p>Tu reserva <strong>#{{ $reservation->code ?? $reservation->id }}</strong> en <strong>{{ $reservation->property->name }}</strong> ha sido actualizada.</p>
	
	<div style="background:#f5f5f5; border-radius:8px; padding:16px; margin:24px 0;">
		<h3 style="margin:0 0 12px; font-size:16px; color:#333;">📋 Datos actualizados de tu reserva:</h3>
		<table style="width:100%; border-collapse:collapse;">
			<tr>
				<td style="padding:6px 0; color:#666;"><strong>Fechas:</strong></td>
				<td style="padding:6px 0; text-align:right;">{{ $reservation->check_in->format('d/m/Y') }} → {{ $reservation->check_out->format('d/m/Y') }}</td>
			</tr>
			<tr>
				<td style="padding:6px 0; color:#666;"><strong>Noches:</strong></td>
				<td style="padding:6px 0; text-align:right;">{{ $reservation->check_in->diffInDays($reservation->check_out) }} {{ $reservation->check_in->diffInDays($reservation->check_out) === 1 ? 'noche' : 'noches' }}</td>
			</tr>
			<tr>
				<td style="padding:6px 0; color:#666;"><strong>Huéspedes:</strong></td>
				<td style="padding:6px 0; text-align:right;">
					@php
						$parts = [];
						$ad = (int) ($reservation->adults ?? 0);
						$ch = (int) ($reservation->children ?? 0);
						$pt = (int) ($reservation->pets ?? 0);
						if ($ad > 0) { $parts[] = $ad.' '.($ad === 1 ? 'adulto' : 'adultos'); }
						if ($ch > 0) { $parts[] = $ch.' '.($ch === 1 ? 'niño' : 'niños'); }
						if ($pt > 0) { $parts[] = $pt.' '.($pt === 1 ? 'mascota' : 'mascotas'); }
					@endphp
					@if(count($parts))
						{{ implode(', ', $parts) }}
					@else
						{{ $reservation->guests }} {{ $reservation->guests === 1 ? 'persona' : 'personas' }}
					@endif
				</td>
			</tr>
			@if(!empty($reservation->notes))
			<tr>
				<td colspan="2" style="padding:12px 0 6px 0; color:#666;"><strong>Notas:</strong></td>
			</tr>
			<tr>
				<td colspan="2" style="padding:0; color:#555; font-size:14px; white-space: pre-wrap;">{{ $reservation->notes }}</td>
			</tr>
			@endif
		</table>
	</div>

	<div style="background:#fff; border:2px solid #e0e0e0; border-radius:8px; padding:16px; margin:24px 0;">
		<h3 style="margin:0 0 12px; font-size:16px; color:#333;">💰 Resumen económico:</h3>
		
		@if($previousTotal > 0 && abs($difference) > 0.01)
			<table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
				<tr>
					<td style="padding:6px 0; color:#666;">Total anterior:</td>
					<td style="padding:6px 0; text-align:right; text-decoration:line-through; color:#999;">{{ number_format($previousTotal, 2, ',', '.') }} €</td>
				</tr>
				<tr style="border-top:1px solid #e0e0e0;">
					<td style="padding:10px 0; color:#333; font-size:16px;"><strong>Nuevo total:</strong></td>
					<td style="padding:10px 0; text-align:right; font-size:20px; font-weight:bold; color:#2c5aa0;">{{ number_format($reservation->total_price, 2, ',', '.') }} €</td>
				</tr>
			</table>

			@if($difference > 0)
				<div style="background:#d32f2f; color:white; border-radius:6px; padding:12px; margin-top:12px;">
					<strong style="font-size:15px;">✓ Cargo adicional aplicado</strong>
					<p style="margin:8px 0 0 0; font-size:14px;">
						Se ha cobrado <strong>{{ number_format($difference, 2, ',', '.') }} €</strong> adicionales mediante el método de pago registrado para cubrir el incremento del costo de tu reserva.
					</p>
				</div>
			@elseif($difference < 0)
				<div style="background:#388e3c; color:white; border-radius:6px; padding:12px; margin-top:12px;">
					<strong style="font-size:15px;">✓ Reembolso procesado</strong>
					<p style="margin:8px 0 0 0; font-size:14px;">
						Se ha devuelto <strong>{{ number_format(abs($difference), 2, ',', '.') }} €</strong> a tu método de pago original por la reducción del costo de la reserva.
					</p>
				</div>
			@endif
		@else
			<table style="width:100%; border-collapse:collapse;">
				<tr>
					<td style="padding:10px 0; color:#333; font-size:16px;"><strong>Total:</strong></td>
					<td style="padding:10px 0; text-align:right; font-size:20px; font-weight:bold; color:#2c5aa0;">{{ number_format($reservation->total_price, 2, ',', '.') }} €</td>
				</tr>
			</table>
			<p style="margin:12px 0 0 0; color:#666; font-size:14px;">
				El total de tu reserva permanece sin cambios.
			</p>
		@endif
	</div>

	<div style="background:#e8f5e9; border-left:4px solid #4caf50; padding:12px; margin:24px 0; border-radius:4px;">
		<p style="margin:0; color:#2e7d32; font-size:14px;">
			<strong>Todo está en orden.</strong> Tu reserva ha sido actualizada correctamente. Recibirás un correo de confirmación con todos los detalles.
		</p>
	</div>

	<hr style="border:none; border-top:1px solid #e0e0e0; margin:24px 0;">
	
	<p style="color:#666; font-size:13px; margin:0;">
		Si tienes alguna pregunta o necesitas realizar más cambios, no dudes en contactarnos.<br>
		<strong>Staynest</strong> - Tu alojamiento perfecto
	</p>
</body>
</html>
