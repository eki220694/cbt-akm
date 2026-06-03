<!-- Core MathJax Engine -->
<script>
    window.MathJax = {
        tex: {
            inlineMath: [['\\(', '\\)']],
            displayMath: [['$$', '$$']],
            processEscapes: true
        }
    };
</script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@wiris/mathtype-tinymce6@8.2.0/plugin.min.js"></script>

<!-- CSS SYSTEM OVERRIDES: Memperbaiki Tampilan Pecah Mode Gelap TinyMCE -->
<style>
    /* ==========================================================================
       1. KONDISI DEFAULT / LIGHT MODE (TAMPILAN CERAH)
       ========================================================================== */
    .tox-tinymce {
        border-color: #cbd5e1 !important;
        border-radius: 0.5rem !important;
    }
    .tox .tox-editor-header {
        background-color: #f8fafc !important;
        border-bottom: 1px solid #cbd5e1 !important;
    }
    .tox .tox-toolbar, .tox .tox-toolbar__primary, .tox .tox-toolbar__group {
        background-color: #f8fafc !important;
    }

    /* ==========================================================================
       2. KONDISI AMBIENT / DARK MODE (TERKUNCI SEMPURNA)
       ========================================================================== */
    /* Cangkang Luar Utama Editor */
    .dark .tox-tinymce {
        border-color: #3f3f46 !important;
        background-color: #18181b !important;
    }

    /* Area Baris Atas Menu & Toolbar */
    .dark .tox .tox-editor-header {
        background-color: #27272a !important;
        border-bottom: 1px solid #3f3f46 !important;
    }
    .dark .tox .tox-toolbar,
    .dark .tox .tox-toolbar__primary,
    .dark .tox .tox-toolbar__group {
        background-color: #27272a !important;
    }

    /* Memperbaiki Tombol-Tombol Icon Agar Tidak Putih Rontok */
    .dark .tox .tox-tbtn {
        background-color: transparent !important;
        color: #e4e4e7 !important;
    }
    .dark .tox .tox-tbtn:hover {
        background-color: #3f3f46 !important;
        color: #ffffff !important;
    }
    .dark .tox .tox-tbtn--active, .dark .tox .tox-tbtn--enabled {
        background-color: #3f3f46 !important;
    }
    .dark .tox .tox-tbtn svg {
        fill: #e4e4e7 !important;
    }

    /* Memperbaiki Dropdown (Huruf Sistem, 12pt, Paragraf) yang Menjadi Blok Putih */
    .dark .tox .tox-listboxfield,
    .dark .tox .tox-tbtn--select,
    .dark .tox .tox-toolbar .tox-tbtn--select {
        background-color: #18181b !important;
        color: #e4e4e7 !important;
        border: 1px solid #3f3f46 !important;
    }
    .dark .tox .tox-tbtn--select:hover {
        background-color: #3f3f46 !important;
    }
</style>

<!-- JAVASCRIPT THEME SYNC: Menembus Pembatas Iframe Area Ketik Soal -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const applyTinyMceTheme = () => {
            if (!window.tinymce) return;

            const isDark = document.documentElement.classList.contains('dark');
            window.tinymce.editors.forEach(editor => {
                const targetBody = editor.getBody();
                if (targetBody) {
                    if (isDark) {
                        targetBody.style.backgroundColor = '#18181b';
                        targetBody.style.color = '#f4f4f5';
                    } else {
                        targetBody.style.backgroundColor = '#ffffff';
                        targetBody.style.color = '#1e293b';
                    }
                }
            });
        };

        // Pasang hook saat editor di-inisialisasi awal
        window.addEventListener('tinymce:init', (e) => {
            if (e.detail && e.detail.config) {
                e.detail.config.external_plugins = e.detail.config.external_plugins || {};
                e.detail.config.external_plugins['tiny_mce_wiris'] = 'https://cdn.jsdelivr.net/npm/@wiris/mathtype-tinymce6@8.2.0/plugin.min.js';
            }
            setTimeout(applyTinyMceTheme, 150);
        });

        // Pemantau Tombol Ganti Tema Filament Secara Real-Time
        const themeObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    applyTinyMceTheme();
                }
            });
        });
        themeObserver.observe(document.documentElement, { attributes: true });
    });

    document.addEventListener('livewire:navigated', () => {
        if (window.MathJax) {
            window.MathJax.typesetPromise();
        }
    });
</script>
