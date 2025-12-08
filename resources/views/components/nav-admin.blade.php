<header class="nav-header nav-header--solid" style="border-bottom: 1px solid var(--color-border-light);">
    <nav class="nav-container">
        @php
            // Determinar propiedad en contexto admin:
            // 1. Ruta con {property:slug}
            // 2. Sesión current_property_slug (si pertenece al admin)
            // 3. Si solo tiene una propiedad, usarla como contexto implícito
            $currentProperty = request()->route('property');
            if (is_string($currentProperty)) {
                $currentProperty = \App\Models\Property::where('slug', $currentProperty)
                    ->where('user_id', auth()->id())
                    ->whereNull('deleted_at')
                    ->first();
            }
            if (!$currentProperty && session('current_property_slug')) {
                $sessionProp = \App\Models\Property::where('slug', session('current_property_slug'))
                    ->where('user_id', auth()->id())
                    ->whereNull('deleted_at')
                    ->first();
                if ($sessionProp) { $currentProperty = $sessionProp; }
            }
            if (!$currentProperty) {
                $countOwn = \App\Models\Property::where('user_id', auth()->id())
                    ->whereNull('deleted_at')
                    ->count();
                if ($countOwn === 1) {
                    $currentProperty = \App\Models\Property::where('user_id', auth()->id())
                        ->whereNull('deleted_at')
                        ->first();
                }
            }
            // Definir destino del logo: siempre al sitio público si hay propiedades, sino al dashboard
            if ($currentProperty) {
                $logoHref = route('properties.show', $currentProperty->slug);
            } else {
                $anyProperty = \App\Models\Property::where('user_id', auth()->id())
                    ->whereNull('deleted_at')
                    ->first();
                $logoHref = $anyProperty ? route('properties.show', $anyProperty->slug) : route('home');
            }
        @endphp
        <div class="nav-logo-wrapper">
            <a href="{{ $logoHref }}" aria-label="Ir al inicio de la propiedad">
                <x-logo />
            </a>
        </div>

        {{-- Grupo: Menú + Acciones (juntos a la derecha) --}}
        <div class="nav-right-group">
            <ul class="nav-menu">
            @if(auth()->user()->role === 'admin')
                @if($currentProperty)
                    {{-- Si hay propiedad en contexto, Dashboard apunta al dashboard de esa propiedad --}}
                    <li><a href="{{ route('admin.property.dashboard', $currentProperty->slug) }}"
                            class="nav-link {{ request()->routeIs('admin.property.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.property.edit', $currentProperty->slug) }}"
                            class="nav-link {{ request()->routeIs('admin.property.edit') ? 'active' : '' }}">Propiedad</a></li>
                    <li><a href="{{ route('admin.property.photos.index', $currentProperty->slug) }}"
                            class="nav-link {{ request()->routeIs('admin.property.photos.*') ? 'active' : '' }}">Fotos</a></li>
                    <li><a href="{{ route('admin.property.calendar.index', $currentProperty->slug) }}"
                            class="nav-link {{ request()->routeIs('admin.property.calendar.*') ? 'active' : '' }}">Calendario</a></li>
                @else
                    {{-- Si NO hay propiedad en contexto, Dashboard apunta al dashboard general --}}
                    <li><a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                @endif
                
                <li><a href="{{ route('admin.properties.index') }}"
                        class="nav-link {{ request()->routeIs('admin.properties.*') || request()->routeIs('admin.reservations.*') || request()->routeIs('admin.invoices.*') ? 'active' : '' }}">Panel</a></li>
            @endif
            
            {{-- Menú de usuario --}}
            @auth
                <li class="nav-menu-user-mobile">
                    <div class="nav-user-dropdown">
                        <button class="nav-user-trigger" type="button">
                        @if(auth()->user()->avatar_path)
                            <img src="{{ Storage::disk('public')->url(auth()->user()->avatar_path) }}" 
                                 alt="{{ auth()->user()->name }}"
                                 style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1px solid var(--color-border-light);">
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="8" r="5"/>
                                <path d="M3 21c0-5 4-7 9-7s9 2 9 7"/>
                            </svg>
                        @endif
                        <span>{{ auth()->user()->name }}</span>
                        <svg class="nav-dropdown-arrow" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <ul class="nav-dropdown-menu">
                        @php
                            $publicProperty = $currentProperty ?? \App\Models\Property::where('user_id', auth()->id())
                                ->whereNull('deleted_at')
                                ->first();
                        @endphp
                        <li><a href="{{ $publicProperty ? route('properties.show', $publicProperty->slug) : route('home') }}" class="nav-dropdown-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path d="M9 22V12h6v10"/>
                            </svg>
                            Ver sitio público
                        </a></li>
                        <li><a href="{{ route('profile.edit') }}" class="nav-dropdown-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M12 1v6m0 6v6m7.07-14.07l-4.24 4.24m-5.66 5.66l-4.24 4.24m16.97-4.24l-4.24-4.24M4.93 4.93l4.24 4.24"/>
                            </svg>
                            Perfil
                        </a></li>
                        <li><hr class="nav-dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-dropdown-item nav-dropdown-item--danger">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                    </div>
                </div>
            @endauth
        </ul>

        {{-- Acciones de usuario --}}
        <div class="nav-actions">
            @auth
                <div class="nav-user-dropdown">
                    <button class="nav-user-trigger" type="button">
                        @if(auth()->user()->avatar_path)
                            <img src="{{ Storage::disk('public')->url(auth()->user()->avatar_path) }}" 
                                 alt="{{ auth()->user()->name }}"
                                 style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1px solid var(--color-border-light);">
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="8" r="5"/>
                                <path d="M3 21c0-5 4-7 9-7s9 2 9 7"/>
                            </svg>
                        @endif
                        <span>{{ auth()->user()->name }}</span>
                        <svg class="nav-dropdown-arrow" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <ul class="nav-dropdown-menu">
                        @php
                            $publicProperty = $currentProperty ?? \App\Models\Property::where('user_id', auth()->id())
                                ->whereNull('deleted_at')
                                ->first();
                        @endphp
                        <li><a href="{{ $publicProperty ? route('properties.show', $publicProperty->slug) : route('home') }}" class="nav-dropdown-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path d="M9 22V12h6v10"/>
                            </svg>
                            Ver sitio público
                        </a></li>
                        <li><a href="{{ route('profile.edit') }}" class="nav-dropdown-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M12 1v6m0 6v6m7.07-14.07l-4.24 4.24m-5.66 5.66l-4.24 4.24m16.97-4.24l-4.24-4.24M4.93 4.93l4.24 4.24"/>
                            </svg>
                            Perfil
                        </a></li>
                        <li><hr class="nav-dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-dropdown-item nav-dropdown-item--danger">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>{{-- fin nav-actions --}}
        </div>{{-- fin nav-right-group --}}

        <div class="mobile-menu-anchor">
            <button id="mobile-menu-toggle" class="mobile-menu-toggle" aria-label="Menú">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </nav>

    <button id="theme-toggle" class="theme-toggle theme-toggle--corner" aria-label="Cambiar tema" title="Cambiar tema">
        <span class="icon-sun-wrapper"><x-icon name="sun" :size="18" /></span>
        <span class="icon-moon-wrapper"><x-icon name="moon" :size="18" /></span>
    </button>
</header>

<div class="nav-spacer"></div>

<script>
    (function () {
        // Ajusta altura del spacer del header fijo
        function setNavSpacerHeight() {
            try {
                var header = document.querySelector('.nav-header');
                var spacer = document.querySelector('.nav-spacer');
                if (!header || !spacer) return;

                var headerRect = header.getBoundingClientRect();
                var maxHeight = headerRect.height;

                var absEls = header.querySelectorAll('.theme-toggle--corner, .mobile-menu-toggle');
                absEls.forEach(function (el) {
                    var r = el.getBoundingClientRect();
                    var bottomWithinHeader = r.bottom - headerRect.top;
                    if (bottomWithinHeader > maxHeight) maxHeight = bottomWithinHeader;
                });

                var h = Math.ceil(maxHeight) + 6;
                document.body.classList.add('sn-has-fixed-header');
                document.documentElement.style.setProperty('--sn-header-h', h + 'px');
                spacer.style.height = '0px';
            } catch (e) { }
        }

        // Configura event listeners para ajustar header
        window.addEventListener('load', function () {
            setNavSpacerHeight();
            setTimeout(setNavSpacerHeight, 150);
            setTimeout(setNavSpacerHeight, 400);
        });
        window.addEventListener('resize', function () {
            window.requestAnimationFrame(setNavSpacerHeight);
        });
        window.addEventListener('orientationchange', setNavSpacerHeight);

        // Observa cambios en el header para reajustar altura
        var headerNode = document.querySelector('.nav-header');
        if (headerNode && 'MutationObserver' in window) {
            var mo = new MutationObserver(function () { setNavSpacerHeight(); });
            mo.observe(headerNode, { attributes: true, childList: true, subtree: true });
        }

        // Toggle del menú móvil
        const btn = document.getElementById('mobile-menu-toggle');
        const menu = document.querySelector('.nav-menu');
        if (btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('is-open');
                btn.classList.toggle('is-active');
            });
        }

        // Funcionalidad del dropdown de usuario
        const userTriggers = document.querySelectorAll('.nav-user-trigger');
        
        userTriggers.forEach((trigger) => {
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                // Buscar el dropdown más cercano (padre)
                const dropdown = trigger.closest('.nav-user-dropdown');
                if (dropdown) {
                    dropdown.classList.toggle('is-open');
                }
            });
        });
        
        // Cierra dropdowns al hacer click fuera
        document.addEventListener('click', (e) => {
            const allDropdowns = document.querySelectorAll('.nav-user-dropdown');
            allDropdowns.forEach((dropdown) => {
                if (dropdown && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('is-open');
                }
            });
        });
    })();
</script>
