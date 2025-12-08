<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReceiptMail;
use App\Models\Reservation;
use App\Models\Invoice;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ContactController;
use App\Models\Property;
use App\Http\Controllers\QuoteController;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::middleware('web')->group(function () {

    // Redirigir home a la primera propiedad disponible
    Route::get('/', function () {
        // Buscar la propiedad por defecto
        $property = Property::where('slug', 'piso-turistico-centro')->first();

        if (!$property) {
            $property = Property::whereNull('deleted_at')->first();
        }

        if (!$property) {
            return view('welcome');
        }

        return redirect()->route('properties.show', $property);
    })->name('home');

    // Ruta individual para cada propiedad (Inicio)
    Route::get('/propiedades/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');

    // Rutas anidadas por propiedad
    Route::get('/propiedades/{property:slug}/entorno', [PropertyController::class, 'entorno'])->name('properties.entorno');
    Route::get('/propiedades/{property:slug}/contacto', [ContactController::class, 'create'])->name('properties.contact.create');
    Route::post('/propiedades/{property:slug}/contacto', [ContactController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('properties.contact.store');
    Route::get('/propiedades/{property:slug}/reservar', [PropertyController::class, 'reservar'])->name('properties.reservar');

    // Rutas de cliente anidadas por propiedad (accesibles para customers y admins de otras propiedades)
    Route::middleware(['auth'])->group(function () {
        Route::get('/propiedades/{property:slug}/mis-reservas', [ReservationController::class, 'index'])->name('properties.reservas.index');
        Route::get('/propiedades/{property:slug}/mis-facturas', [InvoiceController::class, 'index'])->name('properties.invoices.index');
    });

    // Lista de propiedades de un administrador (solo si tiene más de una)
    Route::get('/propiedades-de/{userId}', [PropertyController::class, 'byOwner'])->name('properties.byOwner');

    // Dashboard específico por propiedad (admin) - necesita slug
    Route::middleware(['auth', 'role:admin'])->get('/propiedades/{property:slug}/admin', [AdminController::class, 'propertyDashboardFiltered'])->name('admin.property.dashboard');

    // Página institucional: Descubre Staynest (muestra todas las propiedades)
    Route::get('/descubre-staynest', [PropertyController::class, 'discover'])->name('discover');

    // Soporte
    Route::get('/soporte', [App\Http\Controllers\SupportController::class, 'index'])->name('soporte.index');
    Route::post('/soporte', [App\Http\Controllers\SupportController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('soporte.store');

    // Páginas legales
    Route::get('/aviso-legal', fn() => view('legal.aviso-legal'))->name('legal.aviso');
    Route::get('/politica-privacidad', fn() => view('legal.politica-privacidad'))->name('legal.privacidad');
    Route::get('/cookies', fn() => view('legal.cookies'))->name('legal.cookies');



/*
|--------------------------------------------------------------------------
| Rutas protegidas comunes (Breeze)
|--------------------------------------------------------------------------
*/
    Route::get('/dashboard', function () {
        return redirect()->route('reservas.index');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/perfil',  [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/perfil', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });



/*
|--------------------------------------------------------------------------
| Área ADMIN (/admin)
|--------------------------------------------------------------------------
*/
    Route::middleware(['auth', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Dashboard principal con estadísticas
            Route::get('/', [AdminController::class, 'index'])->name('dashboard');

            // Gestión de PROPIEDADES (listado general y crear)
            Route::get('/properties', [AdminController::class, 'propertiesIndex'])->name('properties.index');
            Route::get('/properties/create', [AdminController::class, 'propertiesCreate'])->name('properties.create');
            Route::post('/properties', [AdminController::class, 'propertiesStore'])->name('properties.store');
            Route::patch('/properties/{property}/restore', [AdminController::class, 'propertiesRestore'])->name('properties.restore');

            // Reservas (gestión general)
            Route::post('/reservations/{id}/cancel', [AdminController::class, 'cancel'])->name('reservations.cancel');
            Route::get('/reservations/{reservation}', [AdminController::class, 'show'])->name('reservations.show');
            Route::get('/reservations/{id}/edit', [AdminController::class, 'edit'])->name('reservations.edit');
            Route::put('/reservations/{id}', [AdminController::class, 'update'])->name('reservations.update');
            Route::post('/reservations/{id}/refund', [AdminController::class, 'refund'])->name('reservations.refund');

            // Listado de facturas
            Route::get('/invoices', [InvoiceController::class, 'adminIndex'])
                ->name('invoices.index');
            Route::get('/invoices/{number}', [InvoiceController::class, 'show'])
                ->name('invoices.show');
        });



/*
|--------------------------------------------------------------------------
| Área ADMIN - Gestión por propiedad (con slug en URL)
|--------------------------------------------------------------------------
*/
    Route::middleware(['auth', 'role:admin'])
        ->prefix('propiedades/{property:slug}/admin')
        ->name('admin.property.')
        ->group(function () {
            // Dashboard filtrado por propiedad específica
            Route::get('/', [AdminController::class, 'propertyDashboardFiltered'])->name('dashboard');

            // Gestión de propiedad individual
            Route::get('/property', [AdminController::class, 'propertyEdit'])->name('edit');
            Route::put('/property', [AdminController::class, 'propertyUpdate'])->name('update');
            Route::delete('/property', [AdminController::class, 'destroyProperty'])->name('destroy');

            // Gestión del entorno de la propiedad
            Route::put('/property/environment', [AdminController::class, 'environmentUpdate'])->name('environment.update');

            // Gestión de fotos
            Route::get('/photos', [AdminController::class, 'photosIndex'])->name('photos.index');
            Route::post('/photos', [AdminController::class, 'photosStore'])->name('photos.store');
            Route::delete('/photos/{photo}', [AdminController::class, 'photosDestroy'])->name('photos.destroy');
            Route::post('/photos/reorder', [AdminController::class, 'photosReorder'])->name('photos.reorder');
            Route::post('/photos/{photo}/set-cover', [AdminController::class, 'photosSetCover'])->name('photos.set-cover');

            // Calendario y precios
            Route::get('/calendar', [AdminController::class, 'calendarIndex'])->name('calendar.index');
            Route::post('/calendar/set-price', [AdminController::class, 'setPrice'])->name('calendar.set-price');
            Route::post('/calendar/block', [AdminController::class, 'blockDates'])->name('calendar.block');
            Route::post('/calendar/unblock', [AdminController::class, 'unblockDates'])->name('calendar.unblock');
        });



/*
|--------------------------------------------------------------------------
| Área CLIENTE (reservas)
|--------------------------------------------------------------------------
*/
    Route::middleware(['auth', 'role:customer'])->group(function () {
        // Redirecciones de rutas legacy a rutas anidadas
        Route::get('/mis-reservas', function () {
            $property = Property::first();
            if ($property) {
                return redirect()->route('properties.reservas.index', $property->slug);
            }
            return redirect()->route('home');
        })->name('reservas.index');

        Route::get('/mis-facturas', function () {
            $property = Property::first();
            if ($property) {
                return redirect()->route('properties.invoices.index', $property->slug);
            }
            return redirect()->route('home');
        })->name('invoices.index');

        // Crear reserva (POST desde ficha)
        Route::post('/reservas', [ReservationController::class, 'store'])->name('reservas.store');

        // Editar, actualizar y cancelar reserva
        Route::get('/reservar/{reservation}/editar', [ReservationController::class, 'edit'])->name('reservas.edit');
        Route::put('/reservar/{reservation}', [ReservationController::class, 'update'])->name('reservas.update');
        Route::post('/reservar/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservas.cancel');

        // Pagar diferencia de una reserva ya existente
        Route::post('/reservations/{id}/pay-difference', [PaymentController::class, 'payDifference'])
            ->name('reservations.pay_difference');

        // Cancelación por parte del cliente
        Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancelSelf'])
            ->name('reservas.cancel.self');
    });

    // Flujo público para preparar reserva: guarda datos y fuerza login con retorno a /reservar
    Route::post('/reservar/prepare', [ReservationController::class, 'prepare'])->name('reservas.prepare');

/*
|--------------------------------------------------------------------------
| Pagos y facturas (comunes con auth)
|--------------------------------------------------------------------------
*/
    Route::middleware(['auth'])->group(function () {
        Route::post('/reservations/{id}/pay', [PaymentController::class, 'pay'])->name('reservations.pay');
        Route::get('/invoices/{number}', [InvoiceController::class, 'show'])->name('invoices.show');
    });

    // Ruta de prueba envío email 
    Route::get('/dev/test-payment-mail', function () {
        $reservation = Reservation::with(['user', 'property'])->latest()->firstOrFail();
        $invoice = Invoice::where('reservation_id', $reservation->id)->latest()->firstOrFail();
        \Mail::to('cliente@vut.test')->send(new PaymentReceiptMail($reservation, $invoice));
        return 'OK sent';
    });



/*
|--------------------------------------------------------------------------
| Stripe (test)
|--------------------------------------------------------------------------
*/
    Route::middleware(['auth', 'role:customer'])->group(function () {
        // Crear sesión de Stripe Checkout (POST)
        Route::post('/checkout/{reservation}', [StripeController::class, 'checkout'])
            ->name('stripe.checkout');

        // Pagar diferencia tras modificar reserva
        Route::post('/checkout/{reservation}/difference', [StripeController::class, 'checkoutDifference'])
            ->name('stripe.checkout.difference');

        // URLs de retorno desde Stripe (GET)
        Route::get('/checkout/success', [StripeController::class, 'success'])
            ->name('stripe.success');
        Route::get('/checkout/cancel', [StripeController::class, 'cancel'])
            ->name('stripe.cancel');
    });

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/
    Route::get('/api/quote', [QuoteController::class, 'show'])->name('quote.show');


/*
|--------------------------------------------------------------------------
| Auth (Breeze)
|--------------------------------------------------------------------------
*/
    require __DIR__ . '/auth.php';
});
