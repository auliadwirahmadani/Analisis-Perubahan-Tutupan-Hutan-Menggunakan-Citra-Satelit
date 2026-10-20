/**
 * ForestWatch Main Application Script
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Dark Mode Management
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const htmlElement = document.documentElement;

    function applyTheme(isDark) {
        if (isDark) {
            htmlElement.classList.add('dark');
            localStorage.setItem('forestwatch_theme', 'dark');
            if (themeToggleBtn) {
                themeToggleBtn.innerHTML = `
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                `;
            }
        } else {
            htmlElement.classList.remove('dark');
            localStorage.setItem('forestwatch_theme', 'light');
            if (themeToggleBtn) {
                themeToggleBtn.innerHTML = `
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                `;
            }
        }
    }

    const savedTheme = localStorage.getItem('forestwatch_theme');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(savedTheme === 'dark' || (savedTheme === null && prefersDark));

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const isCurrentlyDark = htmlElement.classList.contains('dark');
            applyTheme(!isCurrentlyDark);
            // Refresh charts if ApexCharts exists to adapt theme
            if (window.dispatchEvent) {
                window.dispatchEvent(new Event('resize'));
            }
        });
    }

    // 2. Region Global Filter Selector
    const globalRegionSelect = document.getElementById('globalRegionSelect');
    if (globalRegionSelect) {
        globalRegionSelect.addEventListener('change', (e) => {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('region', e.target.value);
            window.location.href = currentUrl.toString();
        });
    }

    // 3. Reset Demo Data Handler with SweetAlert2
    const btnResetDemo = document.getElementById('btnResetDemo');
    if (btnResetDemo) {
        btnResetDemo.addEventListener('click', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Reset ke Data Awal?',
                    text: 'Seluruh riwayat observasi dan titik peringatan akan dikembalikan ke data sampel satelit resmi.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Reset Sekarang',
                    cancelButtonText: 'Batal',
                    background: htmlElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
                    color: htmlElement.classList.contains('dark') ? '#f1f5f9' : '#0f172a',
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(window.APP_BASE_URL + '/api/reset-demo', { method: 'POST' })
                            .then(res => res.json())
                            .then(data => {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#10b981'
                                }).then(() => {
                                    window.location.reload();
                                });
                            })
                            .catch(err => {
                                Swal.fire('Error', 'Gagal mereset database: ' + err, 'error');
                            });
                    }
                });
            }
        });
    }
});
