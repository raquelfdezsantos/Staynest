@extends('layouts.app')

@section('title','Política de Cookies')
@section('content')
<div class="container" style="max-width: 1100px; padding: var(--spacing-xl) 0 var(--spacing-2xl);">
    <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); margin-bottom: var(--spacing-lg); color: var(--color-text-primary);">Política de Cookies</h1>
    <div class="sn-legal" style="color: var(--color-text-secondary); line-height: 1.8; font-size: var(--text-sm);">
                    <h2>1. ¿Qué son las cookies?</h2>
                    <p>
                        Las cookies son pequeños archivos de texto que se almacenan en su dispositivo cuando visita un sitio web. Las cookies permiten que el sitio web recuerde sus acciones y preferencias durante un período de tiempo.
                    </p>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>2. Cookies utilizadas en este sitio web</h2>
                    <p>
                        Este sitio web utiliza únicamente <strong>cookies técnicas estrictamente necesarias</strong> para su funcionamiento:
                    </p>

                    <h3>Cookies de sesión (Laravel)</h3>
                    
                    {{-- Tabla para desktop --}}
                    <table class="cookies-table" style="width:100%; border-collapse: collapse; margin-top: var(--spacing-md);">
                        <thead>
                            <tr>
                                <th style="text-align:left; padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-primary);">Cookie</th>
                                <th style="text-align:left; padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-primary);">Finalidad</th>
                                <th style="text-align:left; padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-primary);">Duración</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-secondary);"><code>{{ config('session.cookie') }}</code></td>
                                <td style="padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-secondary);">Identificador de sesión del usuario. Necesaria para mantener su sesión iniciada.</td>
                                <td style="padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-secondary);">Sesión (se elimina al cerrar el navegador)</td>
                            </tr>
                            <tr>
                                <td style="padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-secondary);"><code>XSRF-TOKEN</code></td>
                                <td style="padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-secondary);">Protección contra ataques CSRF (Cross-Site Request Forgery). Seguridad del sitio.</td>
                                <td style="padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-secondary);">2 horas</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Cards para móvil/tablet --}}
                    <div class="cookies-cards" style="display: none; gap: 1rem; margin-top: var(--spacing-md);">
                        <div style="background: var(--color-bg-secondary); border: 1px solid var(--color-border-light); border-radius: var(--radius-base); padding: 1rem;">
                            <div style="margin-bottom: 0.75rem;">
                                <strong style="color: var(--color-text-primary);">Cookie:</strong>
                                <code style="display: block; margin-top: 0.25rem;">{{ config('session.cookie') }}</code>
                            </div>
                            <div style="margin-bottom: 0.75rem;">
                                <strong style="color: var(--color-text-primary);">Finalidad:</strong>
                                <p style="margin-top: 0.25rem;">Identificador de sesión del usuario. Necesaria para mantener su sesión iniciada.</p>
                            </div>
                            <div>
                                <strong style="color: var(--color-text-primary);">Duración:</strong>
                                <p style="margin-top: 0.25rem;">Sesión (se elimina al cerrar el navegador)</p>
                            </div>
                        </div>
                        <div style="background: var(--color-bg-secondary); border: 1px solid var(--color-border-light); border-radius: var(--radius-base); padding: 1rem;">
                            <div style="margin-bottom: 0.75rem;">
                                <strong style="color: var(--color-text-primary);">Cookie:</strong>
                                <code style="display: block; margin-top: 0.25rem;">XSRF-TOKEN</code>
                            </div>
                            <div style="margin-bottom: 0.75rem;">
                                <strong style="color: var(--color-text-primary);">Finalidad:</strong>
                                <p style="margin-top: 0.25rem;">Protección contra ataques CSRF (Cross-Site Request Forgery). Seguridad del sitio.</p>
                            </div>
                            <div>
                                <strong style="color: var(--color-text-primary);">Duración:</strong>
                                <p style="margin-top: 0.25rem;">2 horas</p>
                            </div>
                        </div>
                    </div>

                    <h3>Cookie de consentimiento</h3>
                    
                    {{-- Tabla para desktop --}}
                    <table class="cookies-table" style="width:100%; border-collapse: collapse; margin-top: var(--spacing-md);">
                        <thead>
                            <tr>
                                <th style="text-align:left; padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-primary);">Cookie</th>
                                <th style="text-align:left; padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-primary);">Finalidad</th>
                                <th style="text-align:left; padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-primary);">Duración</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-secondary);"><code>cookie_consent</code></td>
                                <td style="padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-secondary);">Almacena su preferencia sobre el uso de cookies para no mostrar el banner repetidamente.</td>
                                <td style="padding:.6rem .8rem; border:1px solid var(--color-border-light); color: var(--color-text-secondary);">1 año</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Cards para móvil/tablet --}}
                    <div class="cookies-cards" style="display: none; gap: 1rem; margin-top: var(--spacing-md);">
                        <div style="background: var(--color-bg-secondary); border: 1px solid var(--color-border-light); border-radius: var(--radius-base); padding: 1rem;">
                            <div style="margin-bottom: 0.75rem;">
                                <strong style="color: var(--color-text-primary);">Cookie:</strong>
                                <code style="display: block; margin-top: 0.25rem;">cookie_consent</code>
                            </div>
                            <div style="margin-bottom: 0.75rem;">
                                <strong style="color: var(--color-text-primary);">Finalidad:</strong>
                                <p style="margin-top: 0.25rem;">Almacena su preferencia sobre el uso de cookies para no mostrar el banner repetidamente.</p>
                            </div>
                            <div>
                                <strong style="color: var(--color-text-primary);">Duración:</strong>
                                <p style="margin-top: 0.25rem;">1 año</p>
                            </div>
                        </div>
                    </div>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>3. Finalidad de las cookies</h2>
                    <p>
                        Las cookies técnicas son necesarias para:
                    </p>
                    <ul>
                        <li>Mantener su sesión de usuario activa mientras navega por el sitio.</li>
                        <li>Proteger el sitio web contra ataques de seguridad.</li>
                        <li>Recordar su consentimiento sobre el uso de cookies.</li>
                    </ul>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>4. Cookies de terceros</h2>
                    <p>
                        <strong>Este sitio web NO utiliza cookies de terceros</strong> para publicidad, analítica u otros fines. No se comparten datos con terceros a través de cookies.
                    </p>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>5. Cómo gestionar las cookies</h2>
                    <p>
                        Puede configurar su navegador para rechazar cookies, pero esto puede afectar al funcionamiento correcto del sitio web. Al ser cookies técnicas necesarias, su eliminación impedirá el uso normal de las funcionalidades del sitio.
                    </p>
                    <p>
                        Para gestionar las cookies en los navegadores más comunes:
                    </p>
                    <ul>
                        <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" class="text-indigo-600">Google Chrome</a></li>
                        <li><a href="https://support.mozilla.org/es/kb/habilitar-y-deshabilitar-cookies-sitios-web-rastrear-preferencias" target="_blank" class="text-indigo-600">Mozilla Firefox</a></li>
                        <li><a href="https://support.apple.com/es-es/HT201265" target="_blank" class="text-indigo-600">Safari</a></li>
                        <li><a href="https://support.microsoft.com/es-es/microsoft-edge/eliminar-cookies-en-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" class="text-indigo-600">Microsoft Edge</a></li>
                    </ul>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>6. Consentimiento</h2>
                    <p>
                        Al navegar y utilizar este sitio web, acepta el uso de las cookies técnicas necesarias descritas en esta política.
                    </p>

                    <hr style="border:0; border-top:1px solid var(--color-border-light); margin: var(--spacing-lg) 0;" />
                    <h2>7. Más información</h2>
                    <p>
                        Para más información sobre el tratamiento de datos personales, consulte nuestra <a href="{{ route('legal.privacidad') }}" class="text-indigo-600 hover:text-indigo-800">Política de Privacidad</a>.
                    </p>

        <p style="margin-top: var(--spacing-xl); font-size: var(--text-xs); color: var(--color-text-muted);">Última actualización: {{ date('d/m/Y') }}</p>
    </div>
</div>
@endsection

<style>
    @media (max-width: 768px) {
        .cookies-table {
            display: none !important;
        }
        .cookies-cards {
            display: flex !important;
            flex-direction: column;
        }
    }
</style>
