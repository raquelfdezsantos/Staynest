<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;


/**
 * Controlador para la gestión de facturas.
 *
 * Permite listar, mostrar y descargar facturas tanto para clientes como para administradores.
 */
class InvoiceController extends Controller
{
    /**
     * Muestra el listado de facturas de un cliente para una propiedad específica.
     *
     * Si el usuario es admin y dueño de la propiedad, redirige al panel de administración.
     *
     * @param Property $property Propiedad asociada a las facturas.
    * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Property $property)
    {
        // Si es admin y es dueño de la propiedad, redirigir al panel admin
        if (Auth::user()->role === 'admin' && $property->user_id === Auth::id()) {
            return redirect()->route('admin.dashboard');
        }
        
        $invoices = Invoice::with(['reservation.property'])
            ->whereHas('reservation', fn($q) => $q->where('user_id', Auth::id()))
            ->latest('issued_at')
            ->paginate(10);

        return view('customer.invoices.index', compact('invoices', 'property'));
    }

    /**
     * Muestra los detalles de una factura específica y permite descargarla en PDF.
     *
     * @param string $number Número de la factura a mostrar.
     * @return \Illuminate\View\View|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response Vista o descarga PDF de la factura.
     */
    public function show(string $number)
    {
        $invoice = Invoice::with(['reservation.user', 'reservation.property'])
            ->where('number', $number)->firstOrFail();

        $this->authorize('view', $invoice->reservation);

        if (request()->boolean('download')) {
            $pdf = PDF::loadView('invoices.pdf', ['invoice' => $invoice]);
            return $pdf->download($invoice->number . '.pdf');
        }

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Muestra el listado de todas las facturas para el administrador.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function adminIndex()
    {
        $invoices = Invoice::with(['reservation.user', 'reservation.property'])
            ->latest('issued_at')
            ->paginate(20);

        return view('admin.invoices.index', compact('invoices'));
    }
}
