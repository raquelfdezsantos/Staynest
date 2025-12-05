<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        html[data-theme="dark"] .profile-card {
            background: rgba(51, 51, 51, 0.2) !important;
        }

        html[data-theme="light"] .profile-card {
            background: #e3e3e3 !important;
        }

        /* Calendario Flatpickr - Modo Oscuro */
        html[data-theme="dark"] .flatpickr-calendar {
            background: rgb(38, 38, 38);
            border: 1px solid var(--color-border-light);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        html[data-theme="dark"] .flatpickr-months {
            background: rgb(38, 38, 38);
            color: var(--color-text-primary);
        }

        html[data-theme="dark"] .flatpickr-current-month .flatpickr-monthDropdown-months,
        html[data-theme="dark"] .flatpickr-current-month input.cur-year {
            background: rgb(38, 38, 38);
            color: var(--color-text-primary);
        }

        html[data-theme="dark"] .flatpickr-weekdays {
            background: rgb(38, 38, 38);
        }

        html[data-theme="dark"] span.flatpickr-weekday {
            color: var(--color-text-secondary);
        }

        html[data-theme="dark"] .flatpickr-day {
            color: var(--color-text-primary);
        }

        html[data-theme="dark"] .flatpickr-day:hover {
            background: rgba(77, 141, 148, 0.2);
            color: var(--color-text-primary);
        }

        html[data-theme="dark"] .flatpickr-day.selected {
            background: var(--color-accent);
            color: white;
            border-color: var(--color-accent);
        }

        html[data-theme="dark"] .flatpickr-day.today {
            border-color: var(--color-accent);
        }

        html[data-theme="dark"] .flatpickr-months .flatpickr-prev-month svg,
        html[data-theme="dark"] .flatpickr-months .flatpickr-next-month svg {
            fill: var(--color-text-primary);
        }

        html[data-theme="light"] .flatpickr-calendar {
            background: white;
            border: 1px solid #e0e0e0;
        }

        html[data-theme="light"] .flatpickr-months {
            background: white;
        }

        html[data-theme="light"] .flatpickr-weekdays {
            background: white;
        }

        html[data-theme="light"] .flatpickr-day.selected {
            background: var(--color-accent);
            color: white;
        }

        html[data-theme="light"] .flatpickr-day.today {
            border-color: var(--color-accent);
        }

        .flatpickr-days {
            width: 308px !important;
        }

        .dayContainer {
            width: 308px !important;
            min-width: 308px !important;
            max-width: 308px !important;
            justify-content: center !important;
        }

        .flatpickr-day {
            max-width: 38px !important;
            max-height: 38px !important;
            width: 38px !important;
            height: 38px !important;
            line-height: 38px !important;
            margin: 2px !important;
            border-radius: 2px !important;
            border: none !important;
        }

        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            opacity: 0.4 !important;
        }
    </style>

    <div class="max-w-5xl mx-auto px-4 py-10">
        {{-- Header centrado --}}
        <header class="mb-16 text-center">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Mi perfil</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Actualiza tu información
                personal y configuración de la cuenta.</p>
        </header>

        <div class="grid gap-8">
            {{-- Información del perfil --}}
            <section class="profile-card p-6"
                style="border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.8); backdrop-filter: blur(10px);">
                <h2
                    style="font-size: var(--text-lg); font-weight:600; color: var(--color-text-primary); margin:0 0 1rem; text-transform: uppercase; letter-spacing:0.05em;">
                    Información del perfil</h2>
                @include('profile.partials.update-profile-information-form')
            </section>

            {{-- Cambiar contraseña --}}
            <section class="profile-card p-6"
                style="border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.8); backdrop-filter: blur(10px);">
                <h2
                    style="font-size: var(--text-lg); font-weight:600; color: var(--color-text-primary); margin:0 0 1rem; text-transform: uppercase; letter-spacing:0.05em;">
                    Cambiar contraseña</h2>
                @include('profile.partials.update-password-form')
            </section>

            {{-- Eliminar cuenta --}}
            <section class="profile-card p-6"
                style="border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.8); backdrop-filter: blur(10px);">
                <h2
                    style="font-size: var(--text-lg); font-weight:600; color: var(--color-text-primary); margin:0 0 1rem; text-transform: uppercase; letter-spacing:0.05em;">
                    Eliminar cuenta</h2>
                @include('profile.partials.delete-user-form')
            </section>
        </div>

        {{-- Modal de confirmación de eliminación --}}
        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <div
                style="padding: 2rem; border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.9); backdrop-filter: blur(10px);">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    {{-- Campo oculto de username para accesibilidad --}}
                    <input type="text" name="username" autocomplete="username" value="{{ auth()->user()->email }}"
                        style="display: none;" aria-hidden="true">

                    <h2
                        style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                        ¿Estás seguro de que deseas eliminar tu cuenta?
                    </h2>

                    <p style="margin-bottom: 1rem; font-size: var(--text-base); color: var(--color-text-secondary);">
                        Una vez eliminada, todos tus datos se borrarán permanentemente.
                    </p>

                    @php
                        $activeReservations = \App\Models\Reservation::where('user_id', auth()->id())
                            ->whereIn('status', ['pending', 'paid'])
                            ->count();
                    @endphp

                    @if($activeReservations > 0)
                        <div
                            style="margin-bottom: 1.5rem; padding: 1rem; background-color: rgba(204, 89, 86, 0.1); border-left: 4px solid var(--color-error); border-radius: var(--radius-base); display: flex; gap: 0.75rem;">
                            <svg style="width: 20px; height: 20px; flex-shrink: 0; margin-top: 0.125rem;" fill="none"
                                stroke="var(--color-error)" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p
                                    style="font-size: var(--text-base); color: var(--color-error); font-weight: 600; margin-bottom: 0.5rem;">
                                    Atención: Tienes {{ $activeReservations }}
                                    {{ $activeReservations === 1 ? 'reserva activa' : 'reservas activas' }}
                                </p>
                                <p style="font-size: var(--text-sm); color: var(--color-text-secondary);">
                                    Al eliminar tu cuenta, todas tus reservas se cancelarán automáticamente.
                                    El reembolso dependerá de los días de antelación según la política de cancelación:
                                </p>
                                <ul
                                    style="margin-top: 0.5rem; margin-left: 1.5rem; font-size: var(--text-sm); color: var(--color-text-secondary);">
                                    <li>30 o más días: 100% de reembolso</li>
                                    <li>14-29 días: 75% de reembolso</li>
                                    <li>7-13 días: 50% de reembolso</li>
                                    <li>Menos de 7 días: Sin reembolso</li>
                                </ul>
                            </div>
                        </div>
                    @endif

                    <p style="margin-bottom: 1.5rem; font-size: var(--text-base); color: var(--color-text-secondary);">
                        Por favor, introduce tu contraseña para confirmar.
                    </p>

                    <div style="margin-bottom: 1.5rem;">
                        <x-input-label for="password" value="Contraseña" class="sr-only" />
                        <x-text-input id="password" name="password" type="password" class="block w-full"
                            autocomplete="current-password" placeholder="Contraseña" />
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" @click="$dispatch('close-modal', 'confirm-user-deletion')"
                            class="btn-action btn-action-secondary sn-sentence py-2 px-5"
                            style="height:36px; display:inline-flex; align-items:center; background-color: var(--color-bg-secondary); color: var(--color-text-primary); border: 1px solid var(--color-accent); border-radius: var(--radius-base); transition: all 0.3s ease; font-size: 14px;"
                            onmouseover="this.style.color = 'var(--color-accent)'; this.style.backgroundColor = 'rgba(77, 141, 148, 0.10)'; this.style.border = 'none';"
                            onmouseout="this.style.backgroundColor = 'var(--color-bg-secondary)'; this.style.color = 'var(--color-text-primary)'; this.style.border = '1px solid var(--color-accent)';">
                            Cancelar
                        </button>

                        <button type="submit" class="btn-action btn-action-danger sn-sentence py-2 px-5"
                            style="height:36px; display:inline-flex; align-items:center; background-color: var(--color-error); color: #fff; border: 1px solid var(--color-error); border-radius: var(--radius-base); transition: all 0.3s ease; font-size: 14px;"
                            onmouseover="this.style.color = 'var(--color-error)'; this.style.backgroundColor = 'rgba(204, 89, 86, 0.10)'; this.style.borderColor = 'transparent';"
                            onmouseout="this.style.backgroundColor = 'var(--color-error)'; this.style.color = '#fff'; this.style.borderColor = 'var(--color-error)';">
                            Eliminar cuenta
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Inicializar Flatpickr para fecha de nacimiento (solo si existe el campo)
                const birthDateInput = document.getElementById('birth_date');
                if (birthDateInput) {
                    // Calcular fecha máxima (18 años atrás desde hoy)
                    const today = new Date();
                    const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());

                    flatpickr('#birth_date', {
                        locale: 'es',
                        dateFormat: 'Y-m-d',
                        maxDate: maxDate,
                        allowInput: true
                    });
                }
            });
        </script>
    </div>
</x-app-layout>