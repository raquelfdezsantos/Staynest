@props(['transparent' => false, 'property' => null])

<header class="nav-header {{ $transparent ? 'nav-header--transparent' : 'nav-header--solid' }}"
    data-sn-transparent="{{ $transparent ? '1' : '0' }}">

    {{-- Logo grande en hero (solo visible en modo transparente) --}}
    <div class="nav-logo-hero">
        <a href="{{ $property ? route('properties.show', $property) : route('home') }}">
            <x-logo />
        </a>
    </div>

    <nav class="nav-container">
        <div class="nav-logo-wrapper">
            <a href="{{ $property ? route('properties.show', $property) : route('home') }}">
                <x-logo />
            </a>
        </div>

        {{-- Grupo: Menú + Acciones (juntos a la derecha) --}}
        <div class="nav-right-group">
            <ul class="nav-menu">
                @if($property)
                    <li><a href="{{ route('properties.show', $property) }}"
                            class="nav-link {{ request()->routeIs('properties.show') ? 'active' : '' }}">Inicio</a></li>
                    <li><a href="{{ route('properties.entorno', $property) }}"
                            class="nav-link {{ request()->routeIs('properties.entorno') ? 'active' : '' }}">Entorno</a></li>
                    <li><a href="{{ route('properties.contact.create', $property) }}"
                            class="nav-link {{ request()->routeIs('properties.contact.*') ? 'active' : '' }}">Contacto</a></li>
                    <li><a href="{{ route('properties.reservar', $property) }}"
                            class="nav-link {{ request()->routeIs('properties.reservar') ? 'active' : '' }}">Reservar</a></li>
                    @if($property->user && $property->user->properties()->whereNull('deleted_at')->count() > 1)
                        <li><a href="{{ route('properties.byOwner', $property->user_id) }}"
                                class="nav-link {{ request()->routeIs('properties.byOwner') ? 'active' : '' }}">Propiedades</a></li>
                    @endif
                @else
                    {{-- Menú para páginas institucionales (sin propiedad específica) --}}
                    <li><a href="{{ route('home') }}"
                            class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Inicio</a></li>
                @endif
            </ul>

            {{-- Acciones de usuario --}}
            <div class="nav-actions">
            {{-- Menú de usuario logueado --}}
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
                        @if(auth()->user()->role === 'admin')
                            <li><a href="{{ route('admin.properties.index') }}" class="nav-dropdown-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                                </svg>
                                Panel Admin
                            </a></li>
                        @else
                            @php
                                // Determinar la propiedad actual para las rutas de cliente
                                $currentProperty = $property ?? \App\Models\Property::whereNull('deleted_at')->first();
                            @endphp
                            @if($currentProperty)
                                <li><a href="{{ route('properties.reservas.index', $currentProperty->slug) }}" class="nav-dropdown-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                                        <path d="M8 2v4M16 2v4M3 10h18"/>
                                    </svg>
                                    Mis Reservas
                                </a></li>
                                <li><a href="{{ route('properties.invoices.index', $currentProperty->slug) }}" class="nav-dropdown-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/>
                                        <path d="M14 2v6h6M8 13h8M8 17h5"/>
                                    </svg>
                                    Mis Facturas
                                </a></li>
                            @endif
                        @endif
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
                </li>
            @endauth
            
            @guest
                <a href="{{ route('login') }}{{ $property ? '?property=' . $property->slug : '' }}"
                    class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
            @endguest
            </div>{{-- fin nav-actions --}}
        </div>{{-- fin nav-right-group --}}
            
        <button id="mobile-menu-toggle" class="mobile-menu-toggle" aria-label="Menú">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </nav>

    <button id="theme-toggle" class="theme-toggle theme-toggle--corner" aria-label="Cambiar tema" title="Cambiar tema">
        <span class="icon-sun-wrapper"><x-icon name="sun" :size="18" /></span>
        <span class="icon-moon-wrapper"><x-icon name="moon" :size="18" /></span>
    </button>
</header>

<div class="nav-spacer"></div>

<script>
    (function () {
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
                
                // Si estamos en modo hero, quitar el padding-top
                if (document.body.classList.contains('sn-hero-mode')) {
                    document.body.style.paddingTop = '0';
                }
            } catch (e) { }
        }

        window.addEventListener('load', function () {
            setNavSpacerHeight();
            setTimeout(setNavSpacerHeight, 150);
            setTimeout(setNavSpacerHeight, 400);
        });
        window.addEventListener('resize', function () {
            window.requestAnimationFrame(setNavSpacerHeight);
        });
        window.addEventListener('orientationchange', setNavSpacerHeight);

        var headerNode = document.querySelector('.nav-header');
        if (headerNode && 'MutationObserver' in window) {
            var mo = new MutationObserver(function () { setNavSpacerHeight(); });
            mo.observe(headerNode, { attributes: true, childList: true, subtree: true });
        }

        const btn = document.getElementById('mobile-menu-toggle');
        const menu = document.querySelector('.nav-menu');
        if (btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('is-open');
                btn.classList.toggle('is-active');
            });
        }

        // Dropdown de usuario
        const userTrigger = document.querySelector('.nav-user-trigger');
        const userDropdown = document.querySelector('.nav-user-dropdown');
        if (userTrigger && userDropdown) {
            userTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('is-open');
            });
            
            // Cerrar al hacer click fuera
            document.addEventListener('click', (e) => {
                if (!userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('is-open');
                }
            });
        }

        // Header sólido al hacer scroll (solo Home y páginas de propiedades comienzan transparentes)
        function updateHeaderMode() {
            var header = document.querySelector('.nav-header');
            if (!header) return;

            var threshold = 80;
            const IS_TRANSPARENT_PAGE = @json(request()->routeIs('home') || request()->routeIs('properties.show'));

            // Páginas normales: siempre sólido, pase lo que pase
            if (!IS_TRANSPARENT_PAGE) {
                header.classList.add('nav-header--solid');
                header.classList.remove('nav-header--transparent');
                document.body.classList.remove('sn-hero-mode');
                return;
            }

            // Home y propiedades: transparente arriba, sólido al hacer scroll
            if (window.scrollY > threshold) {
                header.classList.add('nav-header--solid');
                header.classList.remove('nav-header--transparent');
                document.body.classList.remove('sn-hero-mode');
                document.body.style.paddingTop = '';
            } else {
                header.classList.add('nav-header--transparent');
                header.classList.remove('nav-header--solid');
                document.body.classList.add('sn-hero-mode');
                document.body.style.paddingTop = '0';
            }
        }

        window.addEventListener('load', function () {
            updateHeaderMode();
            setTimeout(updateHeaderMode, 150);
            setTimeout(updateHeaderMode, 500);
            
            // Usar IntersectionObserver para detectar cuando el usuario hace scroll
            const IS_TRANSPARENT_PAGE = @json(request()->routeIs('home') || request()->routeIs('properties.show'));
            
            if (IS_TRANSPARENT_PAGE) {
                const hero = document.querySelector('.sn-hero');
                if (hero) {
                    const observer = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            const header = document.querySelector('.nav-header');
                            if (!header) return;
                            
                            // Si el hero está completamente visible, header transparente
                            // En cuanto se hace scroll, header sólido
                            if (entry.intersectionRatio >= 0.95) {
                                header.classList.add('nav-header--transparent');
                                header.classList.remove('nav-header--solid');
                                document.body.classList.add('sn-hero-mode');
                                document.body.style.paddingTop = '0';
                            } else {
                                header.classList.add('nav-header--solid');
                                header.classList.remove('nav-header--transparent');
                                document.body.classList.remove('sn-hero-mode');
                                document.body.style.paddingTop = '';
                            }
                        });
                    }, {
                        threshold: [0, 0.5, 0.95, 1]
                    });
                    
                    observer.observe(hero);
                }
            }
        });
        
        window.addEventListener('resize', function () {
            updateHeaderMode();
            setTimeout(updateHeaderMode, 100);
        });
    })();
</script>