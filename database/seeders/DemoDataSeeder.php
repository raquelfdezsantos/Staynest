<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\Photo;
use App\Models\PropertyEnvironment;
use App\Models\RateCalendar;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Invoice;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ReservationController;

class DemoDataSeeder extends Seeder
{
    /**
     * SEEDER PARA DEFENSA DEL TFG
     * 
     * Crea un entorno demo completo para mostrar todas las funcionalidades:
     * 
     * USUARIOS:
     * - Admin Principal (luis@staynest.com) → Gestiona Apartamento Nordeste + 1 propiedad demo
     * - Admin Secundario (ana@staynest.com) → Tiene 1 propiedad soft-deleted para recuperar
     * - 3 Clientes con reservas en diferentes estados
     * 
     * PROPIEDADES:
     * - Apartamento Nordeste (REAL) → Propiedad principal para la demo
     * - Chalet con Piscina → Para mostrar edición/eliminación
     * - Estudio Playa (SOFT-DELETED) → Para demostrar recuperación
     * 
     * RESERVAS:
     * - Estados: pending (con expiración cercana), paid, cancelled
     * - Con pagos, facturas y diferentes escenarios
     */
    public function run(): void
    {
        $this->command->info('Creando datos de demostración para la defensa del TFG...');

        // ========================================
        // 1. USUARIOS
        // ========================================
        
        // Admin Principal - Luis (gestiona Apartamento Nordeste)
        $adminLuis = User::create([
            'name' => 'Luis García',
            'email' => 'luis@staynest.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'document_type' => 'dni',
            'document_id' => '12345678A',
            'phone' => '+34 612 345 678',
        ]);

        // Admin Secundario - Ana (tiene propiedad borrada)
        $adminAna = User::create([
            'name' => 'Ana García',
            'email' => 'ana@staynest.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'document_type' => 'dni',
            'document_id' => '87654321B',
            'phone' => '+34 622 987 654',
        ]);

        // Clientes
        $cliente1 = User::create([
            'name' => 'Laura Martínez',
            'email' => 'laura@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'email_verified_at' => now(),
            'document_type' => 'dni',
            'document_id' => '45678912C',
            'phone' => '+34 633 111 222',
        ]);

        $cliente2 = User::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'carlos@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'email_verified_at' => now(),
            'document_type' => 'nie',
            'document_id' => 'X9876543D',
            'phone' => '+34 644 333 444',
        ]);

        $cliente3 = User::create([
            'name' => 'Isabel López',
            'email' => 'isabel@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'email_verified_at' => now(),
            'document_type' => 'dni',
            'document_id' => '78945612E',
            'phone' => '+34 655 555 666',
        ]);

        $this->command->info('Usuarios creados: 2 admins + 3 clientes');

        // ========================================
        // 2. PROPIEDADES
        // ========================================

        // PROPIEDAD 1: Apartamento Nordeste (REAL - Luis)
        $nordeste = Property::create([
            'user_id' => $adminLuis->id,
            'name' => 'Apartamento Nordeste',
            'slug' => 'apartamento-nordeste-gijon',
            'description' => "Apartamento Nordeste es un apartamento turístico en Gijón ideal para disfrutar de una estancia cómoda cerca del mar. Se encuentra a solo 9 minutos a pie de la Playa de Poniente y a 15 de la playa de San Lorenzo. Muy próximo al centro de Gijón, lo que lo convierte en una excelente opción para quienes buscan alojamiento en Gijón cerca de la playa.\n\nEl apartamento ofrece wifi gratis, TV de pantalla plana y un espacio moderno y bien equipado. Dispone de 2 dormitorios, 2 baños con ducha, y una cocina completa con nevera, lavavajillas y menaje. También cuenta con lavadora, toallas, ropa de cama y todo lo necesario para una estancia confortable, tanto para turismo como para trabajo remoto.\n\nLa ubicación es uno de sus mayores atractivos: a pocos minutos encontrarás puntos de interés como la Plaza Mayor de Gijón, las Termas Romanas de Campo Valdés, la Estación de Alsas, y las Estación de Tren Sanz Crespo. En los alrededores hay supermercados, restaurantes y zonas comerciales, perfectos para recorrer Gijón a pie.\n\nEl Aeropuerto de Asturias se sitúa a 39 km, lo que facilita el acceso a viajeros nacionales e internacionales.\n\nSi buscas un apartamento vacacional en Gijón bien ubicado, cómodo y con todos los servicios, Apartamento Nordeste es una opción ideal para tu estancia.",
            'address' => 'Avenida de Portugal 18',
            'city' => 'Gijón',
            'postal_code' => '33207',
            'capacity' => 4,
            'tourism_license' => 'VUT-4222-AS',
            'rental_registration' => 'ESFCTU00003302300025188100000000000000000VUT-4222-AS1',
            'services' => ['wifi', 'heating', 'tv', 'elevator', 'pool', 'hairdryer', 'kitchen', 'washer', 'first_aid_kit', 'towels', 'dishwasher', 'bed_linen'],
        ]);

        // Fotos Apartamento Nordeste (FOTOS REALES)
        Photo::create(['property_id' => $nordeste->id, 'url' => 'images/demo/1.jpg', 'is_cover' => true, 'sort_order' => 1]);
        Photo::create(['property_id' => $nordeste->id, 'url' => 'images/demo/2.jpg', 'is_cover' => false, 'sort_order' => 2]);
        Photo::create(['property_id' => $nordeste->id, 'url' => 'images/demo/3.jpg', 'is_cover' => false, 'sort_order' => 3]);
        Photo::create(['property_id' => $nordeste->id, 'url' => 'images/demo/4.jpg', 'is_cover' => false, 'sort_order' => 4]);
        Photo::create(['property_id' => $nordeste->id, 'url' => 'images/demo/5.jpg', 'is_cover' => false, 'sort_order' => 5]);
        Photo::create(['property_id' => $nordeste->id, 'url' => 'images/demo/6.jpg', 'is_cover' => false, 'sort_order' => 6]);
        Photo::create(['property_id' => $nordeste->id, 'url' => 'images/demo/7.jpg', 'is_cover' => false, 'sort_order' => 7]);
        Photo::create(['property_id' => $nordeste->id, 'url' => 'images/demo/8.jpg', 'is_cover' => false, 'sort_order' => 8]);
        Photo::create(['property_id' => $nordeste->id, 'url' => 'images/demo/9.jpg', 'is_cover' => false, 'sort_order' => 9]);
        Photo::create(['property_id' => $nordeste->id, 'url' => 'images/demo/10.jpg', 'is_cover' => false, 'sort_order' => 10]);
        Photo::create(['property_id' => $nordeste->id, 'url' => 'images/demo/11.jpg', 'is_cover' => false, 'sort_order' => 11]);
        Photo::create(['property_id' => $nordeste->id, 'url' => 'images/demo/12.jpg', 'is_cover' => false, 'sort_order' => 12]);

        $this->generateRateCalendar($nordeste->id, basePrice: 95, weekendPrice: 120);

        // Información del entorno para Apartamento Nordeste
        \App\Models\PropertyEnvironment::create([
            'property_id' => $nordeste->id,
            'title' => 'Descubre Gijón',
            'subtitle' => 'Una ciudad costera con encanto, perfecta combinación de playa, cultura y naturaleza. Disfruta del Cantábrico, la gastronomía asturiana y una ubicación privilegiada cerca del mar.',
            'summary' => "Gijón te ofrece una experiencia única donde el mar Cantábrico se encuentra con una rica oferta cultural y gastronómica. Desde el apartamento podrás acceder fácilmente a:\n\n\nPlaya de Poniente a 9 min a pie\nPlaya de San Lorenzo a 15 min a pie\nCentro histórico y Plaza Mayor\nRutas de senderismo en Asturias\nGastronomía: sidrerías y marisquerías",
            'hero_photo' => 'images/demo/hero.jpg',
            'nature_description' => 'El Cantábrico a tus pies. Disfruta de la Playa de Poniente, San Lorenzo y los acantilados del Cabo de Peñas. A pocos kilómetros, los Picos de Europa te esperan con rutas de montaña y paisajes únicos.',
            'nature_photo' => 'images/demo/naturaleza.jpg',
            'culture_description' => 'Las Termas Romanas de Campo Valdés, la Universidad Laboral, el Elogio del Horizonte de Chillida y un casco histórico lleno de historia. Gijón respira cultura en cada rincón.',
            'culture_photo' => 'images/demo/cultura.jpg',
            'activities_description' => 'Surf, paddle surf, kayak, ciclismo por la Senda Costera, visitas al Jardín Botánico Atlántico y rutas por los alrededores. Gijón es perfecto para deportes y ocio.',
            'activities_photo' => 'images/demo/actividades.jpg',
            'services_description' => 'Supermercados, farmacias, transporte público, centro médico y zonas comerciales a pocos minutos. Todo lo necesario para una estancia cómoda y sin preocupaciones.',
            'services_photo' => 'images/demo/servicios.jpg',
        ]);

        // PROPIEDAD 2: Chalet con Piscina (DEMO - Luis, para editar/eliminar)
        $chalet = Property::create([
            'user_id' => $adminLuis->id,
            'name' => 'Chalet con Vistas Rías Bajas',
            'slug' => 'chalet-vistas-rias-bajas',
            'description' => "Espectacular chalet con vistas panorámicas a las Rías Bajas gallegas. Piscina privada, jardín de 200m² y barbacoa. Ubicado en zona residencial tranquila cerca de Sanxenxo.\n\nDispone de 3 dormitorios dobles, 2 baños completos, salón amplio con chimenea y garaje para 2 coches. Perfecto para grupos y familias que buscan privacidad y la belleza del mar gallego.\n\nLa piscina está disponible de mayo a octubre. A 10 minutos en coche de las mejores playas de Pontevedra.",
            'address' => 'Urbanización Mirador del Mar, Parcela 15',
            'city' => 'Sanxenxo',
            'postal_code' => '36960',
            'capacity' => 8,
            'tourism_license' => 'VT-123456-PO',
            'rental_registration' => 'VT-123456-PO',
            'services' => ['wifi', 'pool', 'parking', 'air_conditioning', 'heating', 'tv', 'kitchen', 'washer', 'dishwasher', 'towels', 'bed_linen', 'terrace', 'pets_allowed'],
        ]);

        Photo::create(['property_id' => $chalet->id, 'url' => 'https://picsum.photos/id/1018/1600/1067', 'is_cover' => true, 'sort_order' => 1]);
        Photo::create(['property_id' => $chalet->id, 'url' => 'https://picsum.photos/id/1016/1600/1067', 'is_cover' => false, 'sort_order' => 2]);
        Photo::create(['property_id' => $chalet->id, 'url' => 'https://picsum.photos/id/1015/1600/1067', 'is_cover' => false, 'sort_order' => 3]);
        Photo::create(['property_id' => $chalet->id, 'url' => 'https://picsum.photos/id/1019/1600/1067', 'is_cover' => false, 'sort_order' => 4]);

        $this->generateRateCalendar($chalet->id, basePrice: 150, weekendPrice: 200);

        // Información del entorno para Chalet Rías Bajas
        \App\Models\PropertyEnvironment::create([
            'property_id' => $chalet->id,
            'title' => 'Descubre las Rías Bajas',
            'subtitle' => 'Un paraíso natural en la costa gallega. Playas de arena blanca, aguas cristalinas y la tranquilidad del mar en un entorno privilegiado.',
            'summary' => "Las Rías Bajas te ofrecen un paisaje único donde el Atlántico dibuja una costa de ensueño. Desde el chalet tendrás acceso a:\n\n\nPlayas de Sanxenxo a 10 min en coche\nIsla de La Toja y su puente\nCombarro, pueblo marinero tradicional\nRuta del vino Rías Baixas\nMarisquerías y gastronomía gallega",
            'hero_photo' => 'https://picsum.photos/id/1024/1600/900',
            'nature_description' => 'El Atlántico en su máxima expresión. Las Rías Bajas ofrecen playas paradisíacas como La Lanzada, A Toxa y Montalvo. Senderos costeros, miradores con vistas al océano y paisajes que enamoran.',
            'nature_photo' => 'https://picsum.photos/id/1025/1600/1067',
            'culture_description' => 'Combarro y sus hórreos sobre el mar, la Isla de La Toja con sus tradiciones, y Pontevedra con su casco histórico. Galicia respira historia y tradición marinera en cada rincón.',
            'culture_photo' => 'https://picsum.photos/id/1033/1600/1067',
            'activities_description' => 'Rutas en barco por las rías, deportes acuáticos, golf, ciclismo por la costa, visitas a bodegas de Albariño y excursiones a las Islas Cíes. Un paraíso para el ocio.',
            'activities_photo' => 'https://picsum.photos/id/1036/1600/1067',
            'services_description' => 'Sanxenxo ofrece supermercados, farmacias, centros de salud, puerto deportivo y una amplia oferta gastronómica. Todo lo necesario para una estancia perfecta.',
            'services_photo' => 'https://picsum.photos/id/1040/1600/1067',
        ]);

        // PROPIEDAD 3: Estudio Playa (SOFT-DELETED - Ana, para recuperar)
        $estudio = Property::create([
            'user_id' => $adminAna->id,
            'name' => 'Estudio Primera Línea Playa Llanes',
            'slug' => 'estudio-playa-llanes',
            'description' => "Estudio moderno con vistas al mar Cantábrico en primera línea de playa de Llanes. Totalmente equipado con cocina americana, terraza con vistas panorámicas y parking incluido.\n\nA pie de playa y del casco histórico de Llanes. Cerca de restaurantes y rutas de senderismo. Ideal para parejas.",
            'address' => 'Paseo de San Pedro, 28',
            'city' => 'Llanes',
            'postal_code' => '33500',
            'capacity' => 2,
            'tourism_license' => 'VUT-789012-AS',
            'rental_registration' => 'VUT-789012-AS',
            'services' => ['wifi', 'air_conditioning', 'tv', 'kitchen', 'parking', 'towels', 'bed_linen', 'terrace'],
            'deleted_at' => now()->subDays(10), // Borrado hace 10 días
        ]);

        Photo::create(['property_id' => $estudio->id, 'url' => 'https://picsum.photos/id/1041/1600/1067', 'is_cover' => true, 'sort_order' => 1]);
        Photo::create(['property_id' => $estudio->id, 'url' => 'https://picsum.photos/id/1042/1600/1067', 'is_cover' => false, 'sort_order' => 2]);
        Photo::create(['property_id' => $estudio->id, 'url' => 'https://picsum.photos/id/1043/1600/1067', 'is_cover' => false, 'sort_order' => 3]);

        $this->generateRateCalendar($estudio->id, basePrice: 70, weekendPrice: 95, onlyPast: true);

        // Información del entorno para Estudio Llanes
        \App\Models\PropertyEnvironment::create([
            'property_id' => $estudio->id,
            'title' => 'Descubre Llanes',
            'subtitle' => 'Villa marinera asturiana con playas espectaculares y un casco histórico medieval. El oriente de Asturias en su máxima expresión.',
            'summary' => "Llanes combina playa, montaña y tradición en un entorno único. Desde el estudio podrás disfrutar de:\n\n\nPlaya de Llanes a pie de calle\nCasco histórico medieval\nBuffones de Pría (géiseres naturales)\nPlaya de Gulpiyuri (playa interior)\nRutas por los Picos de Europa",
            'hero_photo' => 'https://picsum.photos/id/1044/1600/900',
            'nature_description' => 'Playas salvajes, acantilados impresionantes y los Picos de Europa a un paso. Llanes ofrece naturaleza en estado puro: desde el mar Cantábrico hasta las montañas más emblemáticas de España.',
            'nature_photo' => 'https://picsum.photos/id/1045/1600/1067',
            'culture_description' => 'Un pueblo medieval con historia marinera. La Basílica de Santa María, el puerto pesquero, los cubos de la memoria pintados por artistas y un casco histórico que transporta en el tiempo.',
            'culture_photo' => 'https://picsum.photos/id/1047/1600/1067',
            'activities_description' => 'Senderismo por la costa, surf, visitas a los Bufones, excursiones a los Picos de Europa, rutas en kayak y descenso del Sella. Llanes es aventura asegurada.',
            'activities_photo' => 'https://picsum.photos/id/1048/1600/1067',
            'services_description' => 'Llanes cuenta con supermercados, farmacia, centro de salud, restaurantes con sidra y marisco fresco, y todo lo necesario para una estancia cómoda junto al mar.',
            'services_photo' => 'https://picsum.photos/id/1051/1600/1067',
        ]);

        $this->command->info('Propiedades creadas: 2 activas + 1 soft-deleted');

        // ========================================
        // 3. RESERVAS Y PAGOS
        // ========================================

        // RESERVA 1: Laura → Nordeste (PENDING, expira en 2 minutos para demo)
        $reserva1 = Reservation::create([
            'user_id' => $cliente1->id,
            'property_id' => $nordeste->id,
            'code' => 'SN-' . now()->format('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 6)),
            'check_in' => now()->addDays(15)->toDateString(),
            'check_out' => now()->addDays(18)->toDateString(),
            'guests' => 2,
            'adults' => 2,
            'children' => 0,
            'pets' => 0,
            'total_price' => 285.00,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(2), // Expira en 2 minutos para mostrar en la demo
            'notes' => 'Llegada aproximada a las 16:00h',
        ]);

        $this->blockDates($nordeste->id, $reserva1->check_in, $reserva1->check_out);

        // RESERVA 2: Carlos → Nordeste (PAID, con pago completo y factura)
        $reserva2 = Reservation::create([
            'user_id' => $cliente2->id,
            'property_id' => $nordeste->id,
            'code' => 'SN-' . now()->format('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 6)),
            'check_in' => now()->addDays(30)->toDateString(),
            'check_out' => now()->addDays(35)->toDateString(),
            'guests' => 4,
            'adults' => 2,
            'children' => 2,
            'pets' => 0,
            'total_price' => 600.00,
            'status' => 'paid',
            'expires_at' => null,
            'notes' => 'Viaje familiar, necesitamos cuna para bebé',
        ]);

        Payment::create([
            'reservation_id' => $reserva2->id,
            'amount' => 600.00,
            'method' => 'simulated',
            'status' => 'completed',
            'provider_ref' => 'SIM-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        ]);

        Invoice::create([
            'reservation_id' => $reserva2->id,
            'number' => Invoice::generateUniqueNumber('FACT'),
            'pdf_path' => null,
            'issued_at' => now(),
            'amount' => 600.00,
        ]);

        $this->blockDates($nordeste->id, $reserva2->check_in, $reserva2->check_out);

        // RESERVA 3: Isabel → Chalet (CANCELLED, con reembolso)
        $reserva3 = Reservation::create([
            'user_id' => $cliente3->id,
            'property_id' => $chalet->id,
            'code' => 'SN-' . now()->format('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 6)),
            'check_in' => now()->addDays(20)->toDateString(),
            'check_out' => now()->addDays(23)->toDateString(),
            'guests' => 6,
            'adults' => 4,
            'children' => 2,
            'pets' => 1,
            'total_price' => 450.00,
            'status' => 'cancelled',
            'expires_at' => null,
            'notes' => 'Cancelada por el cliente - cambio de planes',
        ]);

        Payment::create([
            'reservation_id' => $reserva3->id,
            'amount' => 450.00,
            'method' => 'simulated',
            'status' => 'completed',
            'provider_ref' => 'SIM-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        ]);

        Payment::create([
            'reservation_id' => $reserva3->id,
            'amount' => -225.00,
            'method' => 'policy',
            'status' => 'refunded',
            'provider_ref' => 'POL-REF-' . strtoupper(substr(md5(uniqid()), 0, 6)),
        ]);

        Invoice::create([
            'reservation_id' => $reserva3->id,
            'number' => Invoice::generateUniqueNumber('FACT'),
            'pdf_path' => null,
            'issued_at' => now()->subDays(5),
            'amount' => 450.00,
        ]);

        Invoice::create([
            'reservation_id' => $reserva3->id,
            'number' => Invoice::generateUniqueNumber('RECT'),
            'pdf_path' => null,
            'issued_at' => now()->subDays(2),
            'amount' => -225.00,
        ]);

        $this->command->info('Reservas creadas: 1 pending + 1 paid + 1 cancelled');

        // ========================================
        // RESUMEN
        // ========================================
        $this->command->info('');
        $this->command->info('¡Datos de demostración creados exitosamente!');
        $this->command->info('');
        $this->command->info('CREDENCIALES DE ACCESO:');
        $this->command->info('');
        $this->command->info('Admin Principal (Luis):');
        $this->command->info('   Email: luis@staynest.com');
        $this->command->info('   Password: password');
        $this->command->info('   Propiedades: Apartamento Nordeste + Chalet Rías Bajas');
        $this->command->info('');
        $this->command->info('Admin Secundario (Ana):');
        $this->command->info('   Email: ana@staynest.com');
        $this->command->info('   Password: password');
        $this->command->info('   Propiedades: 1 estudio borrado (para recuperar)');
        $this->command->info('');
        $this->command->info('Clientes:');
        $this->command->info('   Laura: laura@example.com (reserva pending)');
        $this->command->info('   Carlos: carlos@example.com (reserva paid)');
        $this->command->info('   Isabel: isabel@example.com (reserva cancelled)');
        $this->command->info('');
        $this->command->info('   Password para todos: password');
    }

    /**
     * Genera calendario de tarifas para una propiedad
     */
    private function generateRateCalendar(int $propertyId, int $basePrice, int $weekendPrice, bool $onlyPast = false): void
    {
        $startDate = $onlyPast ? now()->subDays(60) : now();
        $endDate = $onlyPast ? now()->subDays(1) : now()->addDays(90);

        for ($date = Carbon::parse($startDate); $date->lte($endDate); $date->addDay()) {
            $isWeekend = in_array($date->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY]);
            $price = $isWeekend ? $weekendPrice : $basePrice;

            // Temporada alta (julio-agosto): +20%
            if (in_array($date->month, [7, 8])) {
                $price = (int) ($price * 1.2);
            }

            // Navidad/Fin de año: +30%
            if (($date->month === 12 && $date->day >= 20) || ($date->month === 1 && $date->day <= 6)) {
                $price = (int) ($price * 1.3);
            }

            RateCalendar::create([
                'property_id' => $propertyId,
                'date' => $date->toDateString(),
                'price' => $price,
                'is_available' => true,
                'min_stay' => $isWeekend ? 2 : 1,
            ]);
        }
    }

    /**
     * Bloquea fechas en el calendario cuando hay una reserva usando la lógica existente
     */
    private function blockDates(int $propertyId, string $checkIn, string $checkOut): void
    {
        $dates = [];
        $period = CarbonPeriod::create($checkIn, $checkOut)->excludeEndDate();
        
        foreach ($period as $date) {
            $dates[] = $date->toDateString();
        }

        // Usar la lógica existente del ReservationController
        $controller = new ReservationController();
        $controller->setAvailability($propertyId, $dates, false);
        
        // Marcar también blocked_by para mayor consistencia
        RateCalendar::where('property_id', $propertyId)
            ->whereIn('date', $dates)
            ->update(['blocked_by' => 'reservation']);
    }
}
