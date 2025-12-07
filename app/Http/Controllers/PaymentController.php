<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Mail\PaymentReceiptMail;
use App\Mail\PaymentBalanceSettledMail;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminPaymentNotificationMail;
use App\Mail\AdminPaymentBalanceSettledMail;
use App\Mail\ReservationUpdatedMail;
use App\Mail\ReservationCancelledMail;
use App\Mail\PaymentRefundIssuedMail;
use App\Mail\PaymentBalanceDueMail;

/**
 * Controlador para la gestión de pagos de reservas.
 */
class PaymentController extends Controller
{
    /**
     * Simula el pago de una reserva pendiente y genera la factura correspondiente.
     *
     * @param int $reservationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pay(int $reservationId)
    {
        $reservation = Reservation::with(['user', 'property'])
        ->findOrFail($reservationId);

        // Autorización por policy (cliente dueño o admin)
        $this->authorize('pay', $reservation);

        // Reglas de negocio
        abort_unless($reservation->status === 'pending', 403);

        // Guardamos la factura que se crea
        $invoice = DB::transaction(function () use ($reservation) {
            $reservation->update([
                'status' => 'paid',
                'expires_at' => null,
            ]);

            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'        => $reservation->total_price,
                'method'        => 'simulated',
                'status'        => 'succeeded',
                'provider_ref'  => 'SIM-' . Str::upper(Str::random(8)),
            ]);

            $invoiceNumber = Invoice::generateUniqueNumber('INV');

            return Invoice::create([
                'reservation_id' => $reservation->id,
                'number'         => $invoiceNumber,
                'pdf_path'       => null,
                'issued_at'      => now(),
                'amount'         => $reservation->total_price,
                'details'        => [
                    'context'     => 'initial_payment',
                    'check_in'    => $reservation->check_in->format('Y-m-d'),
                    'check_out'   => $reservation->check_out->format('Y-m-d'),
                    'guests'      => (int)$reservation->guests,
                    'adults'      => (int)($reservation->adults ?? 0),
                    'children'    => (int)($reservation->children ?? 0),
                    'pets'        => (int)($reservation->pets ?? 0),
                ],
            ]);
        });

        // Cargamos por si faltaran relaciones
        $reservation->loadMissing(['user', 'property']);

        // Enviar emails (cliente y admin), no romper si falla
        try {
            Mail::to($reservation->user->email)->send(
                new PaymentReceiptMail($reservation, $invoice)
            );
        } catch (\Throwable $e) {
            Log::error('Fallo enviando PaymentReceiptMail', ['msg' => $e->getMessage()]);
        }

        try {
            Mail::to($reservation->property->user->email)->send(
                new AdminPaymentNotificationMail($reservation, $invoice)
            );
        } catch (\Throwable $e) {
            Log::error('Fallo enviando AdminPaymentNotificationMail', ['msg' => $e->getMessage()]);
        }

        return back()->with('success', 'Pago simulado realizado y factura generada.');
    }


    /**
     * Simula el abono de una diferencia pendiente en una reserva ya pagada parcialmente.
     *
     * @param int $reservationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function payDifference(int $reservationId)
    {
        $reservation = Reservation::with(['user', 'property', 'payments'])->findOrFail($reservationId);
        $this->authorize('pay', $reservation);

        $balance = $reservation->balanceDue();
        if ($balance <= 0.0) {
            return back()->with('status', 'No hay importe pendiente.');
        }

        $invoice = DB::transaction(function () use ($reservation, $balance) {
            // Calcular el total previo (lo que ya se había pagado)
            $previousTotal = $reservation->paidAmount();
            
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'        => $balance,
                'method'        => 'simulated',
                'status'        => 'succeeded',
                'provider_ref'  => 'SIM-ADD-' . Str::upper(Str::random(6)),
            ]);

            // Generar factura rectificativa por el incremento (solo por la diferencia pagada)
            $invoiceNumber = Invoice::generateUniqueNumber('RECT');

            // Recuperar detalles de la modificación desde la sesión
            $sessionKey = 'pending_balance_details_' . $reservation->id;
            $modificationDetails = session()->get($sessionKey, []);
            
            // Construir los detalles de la factura
            $invoiceDetails = [
                'context'        => 'increase_update',
                'previous_paid'  => round((float)$previousTotal, 2),
                'difference'     => round((float)$balance, 2),
                'new_total'      => round((float)$reservation->total_price, 2),
            ];
            
            // Si hay detalles de modificación guardados, incluirlos
            if (!empty($modificationDetails)) {
                $invoiceDetails = array_merge($invoiceDetails, $modificationDetails);
                // Limpiar la sesión después de usar los datos
                session()->forget($sessionKey);
            } else {
                // Fallback: usar datos actuales (menos ideal pero funcional)
                $invoiceDetails['check_in'] = $reservation->check_in->format('Y-m-d');
                $invoiceDetails['check_out'] = $reservation->check_out->format('Y-m-d');
                $invoiceDetails['guests'] = (int)$reservation->guests;
                $invoiceDetails['adults'] = (int)($reservation->adults ?? 0);
                $invoiceDetails['children'] = (int)($reservation->children ?? 0);
                $invoiceDetails['pets'] = (int)($reservation->pets ?? 0);
            }

            return Invoice::create([
                'reservation_id' => $reservation->id,
                'number'         => $invoiceNumber,
                'pdf_path'       => null,
                'issued_at'      => now(),
                'amount'         => $balance,
                'details'        => $invoiceDetails,
            ]);
        });

        // Emails (cliente y admin), no romper si falla
        try {
            Mail::to($reservation->user->email)->send(
                new PaymentBalanceSettledMail($reservation, $balance, $invoice)
            );
        } catch (\Throwable $e) {
            Log::error('Fallo enviando PaymentBalanceSettledMail', ['msg' => $e->getMessage()]);
        }
        try {
            Mail::to($reservation->property->user->email)->send(
                new AdminPaymentBalanceSettledMail($reservation, $balance, $invoice)
            );
        } catch (\Throwable $e) {
            Log::error('Fallo enviando AdminPaymentBalanceSettledMail', ['msg' => $e->getMessage()]);
        }

        return back()->with('success', 'Diferencia abonada correctamente.');
    }
}
