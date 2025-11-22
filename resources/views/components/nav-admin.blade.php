<header class="nav-header nav-header--solid" style="border-bottom: 1px solid var(--color-border-light);">
    <nav class="nav-container">
        <x-logo />

        @php
            // Obtener el parámetro de propiedad desde la ruta actual
            $currentProperty = request()->route('property');
            // Si no hay propiedad en la ruta, obtener la primera propiedad del usuario
            if (!$currentProperty) {
                $currentProperty = \App\Models\Property::where('user_id', auth()->id())->first();
            }
        @endphp

        <ul class="nav-menu">
            <li><a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
            
            @if($currentProperty)
                <li><a href="{{ route('admin.property.edit', $currentProperty->slug) }}"
                        class="nav-link {{ request()->routeIs('admin.property.edit') ? 'active' : '' }}">Propiedad</a></li>
                <li><a href="{{ route('admin.property.photos.index', $currentProperty->slug) }}"
                        class="nav-link {{ request()->routeIs('admin.property.photos.*') ? 'active' : '' }}">Fotos</a></li>
                <li><a href="{{ route('admin.property.calendar.index', $currentProperty->slug) }}"
                        class="nav-link {{ request()->routeIs('admin.property.calendar.*') ? 'active' : '' }}">Calendario</a></li>
            @endif
            
            <li><a href="{{ route('admin.properties.index') }}"
                    class="nav-link {{ request()->routeIs('admin.properties.*') || request()->routeIs('admin.reservations.*') || request()->routeIs('admin.invoices.*') ? 'active' : '' }}">Panel</a></li>
            
            {{-- Menú de usuario --}}
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
                            $adminProperty = \App\Models\Property::where('user_id', auth()->id())->first();
                        @endphp
                        @if($adminProperty)
                            <li><a href="{{ route('properties.show', $adminProperty->slug) }}" class="nav-dropdown-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path d="M9 22V12h6v10"/>
                                </svg>
                                Ver sitio público
                            </a></li>
                        @else
                            <li><a href="{{ route('home') }}" class="nav-dropdown-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path d="M9 22V12h6v10"/>
                                </svg>
                                Ver sitio público
                            </a></li>
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
        </ul>

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
    })();
</script>
