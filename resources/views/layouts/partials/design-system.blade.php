{{-- =======================================================================
     PUBLIC SURFACE DESIGN SYSTEM (Framer-Inspired Minimalist UI/UX)
     Typography Ink: #0b0b0c (Crisp Black) & #6a6f7c (Muted Slate)
     Brand / Action Color: #013981 (Polman Deep Royal Blue, Hover: #012b63)
     Canvas / Border: #ffffff, #fafafa, #f4f4f5, #e4e4e7
======================================================================= --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Geist', 'Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
                    mono: ['Geist', 'Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
                },
                colors: {
                    /* Text Ink Tokens (Always Black/Neutral) */
                    ink: {
                        DEFAULT: '#0b0b0c',
                        soft:    '#18181b',
                        muted:   '#6a6f7c',
                        subtle:  '#a1a1aa',
                    },
                    /* Brand / Button / Accent Tokens (#013981) */
                    brand: {
                        DEFAULT: '#013981',
                        hover:   '#012b63',
                        light:   '#eef4fc',
                        border:  '#d8e6f8',
                    },
                    canvas: {
                        DEFAULT: '#ffffff',
                        subtle:  '#fafafa',
                        muted:   '#f4f4f5',
                        border:  '#e4e4e7',
                    },
                    /* Semantic compatibility tokens */
                    "primary":                  "#013981",
                    "primary-container":        "#eef4fc",
                    "on-primary":               "#ffffff",
                    "secondary":                "#012b63",
                    "surface":                  "#fafafa",
                    "surface-container-lowest": "#ffffff",
                    "surface-container-low":    "#fafafa",
                    "surface-container":        "#f4f4f5",
                    "surface-container-high":   "#e4e4e7",
                    "surface-container-highest":"#d4d4d8",
                    "on-surface":               "#0b0b0c",
                    "on-surface-variant":       "#52525b",
                    "outline":                  "#6a6f7c",
                    "outline-variant":          "#e4e4e7",
                    aqua: {
                        50:  '#f5f8fd',
                        100: '#eef4fc',
                        200: '#d8e6f8',
                        300: '#b3cef1',
                        400: '#7aa8e5',
                        500: '#3f7fd4',
                        600: '#1c5db6',
                        700: '#0a489c',
                        800: '#013981',
                        900: '#012b63',
                    },
                    blue: {
                        50:  '#f5f8fd',
                        100: '#eef4fc',
                        200: '#d8e6f8',
                        300: '#b3cef1',
                        400: '#7aa8e5',
                        500: '#3f7fd4',
                        600: '#013981',
                        700: '#012b63',
                        800: '#013981',
                        900: '#0b0b0c',
                        950: '#0b0b0c',
                    },
                },
                boxShadow: {
                    'surface':    '0 1px 2px rgba(11, 11, 12, 0.04)',
                    'surface-md': '0 1px 2px rgba(11, 11, 12, 0.04), 0 8px 24px -8px rgba(11, 11, 12, 0.07)',
                    'surface-lg': '0 1px 2px rgba(11, 11, 12, 0.05), 0 18px 40px -16px rgba(11, 11, 12, 0.10)',
                },
            }
        }
    }
</script>

<style>
    :root {
        --ps-bg: #ffffff;
        --ps-bg-subtle: #fafafa;
        --ps-surface: #f4f4f5;
        --ps-border: #e4e4e7;
        --ps-border-strong: #d4d4d8;
        --ps-ink: #0b0b0c;
        --ps-brand: #013981;
        --ps-brand-hover: #012b63;
        --ps-muted: #6a6f7c;
        --ps-success: #15803d;
        --ps-warning: #b45309;
        --ps-danger: #b91c1c;
    }

    html, body, .font-mono {
        font-family: 'Geist', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
    }

    html {
        color: #0b0b0c;
        background-color: var(--ps-bg-subtle);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
        scroll-behavior: smooth;
    }

    ::selection {
        background: #013981;
        color: #ffffff;
    }

    [x-cloak] { display: none !important; }

    /* Minimal Scrollbars */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Material Symbols Refinement */
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 350, 'GRAD' 0, 'opsz' 20;
        user-select: none;
    }

    /* =====================================================================
       GLOBAL TYPOGRAPHY & COMPONENT HARMONIZATION
       Text stays crisp black (#0b0b0c); Buttons & Accents use #013981
    ===================================================================== */

    /* 1. Editorial Typography */
    h1, h2, h3, h4, .font-black, .font-extrabold {
        font-style: normal !important;
        letter-spacing: -0.025em;
    }
    h1, h2 {
        font-weight: 600 !important;
        letter-spacing: -0.03em !important;
    }
    h3, h4 {
        font-weight: 600 !important;
    }
    main .italic {
        font-style: normal !important;
    }
    main .font-black,
    main .font-extrabold {
        font-weight: 600 !important;
    }

    /* 2. Restrained Border Radii (Framer 10px–12px) */
    main .rounded-\[1\.5rem\],
    main .rounded-\[2rem\],
    main .rounded-\[2\.5rem\],
    main .rounded-\[2\.6rem\],
    main .rounded-\[3rem\],
    main .rounded-3xl {
        border-radius: 0.75rem !important; /* 12px */
    }
    main .rounded-2xl {
        border-radius: 0.625rem !important; /* 10px */
    }

    /* 3. Understated Shadows & Crisp 1px Hairline Borders */
    main .shadow-xl,
    main .shadow-2xl,
    main .shadow-lg,
    main .shadow-md,
    main .shadow-sm,
    main [class*="shadow-["] {
        box-shadow: 0 1px 2px rgba(11, 11, 12, 0.03), 0 4px 12px -4px rgba(11, 11, 12, 0.04) !important;
    }
    main .border-4,
    main .border-2 {
        border-width: 1px !important;
    }
    main .border-b-4,
    main .border-b-2 {
        border-bottom-width: 1px !important;
    }
    main .border-t-4,
    main .border-t-2 {
        border-top-width: 1px !important;
    }
    main .border-r-2 {
        border-right-width: 1px !important;
    }
    main .border-l-2 {
        border-left-width: 1px !important;
    }
    main .hover\:translate-y-1:hover,
    main .hover\:translate-x-1:hover,
    main .hover\:translate-y-px:hover,
    main .hover\:translate-x-px:hover,
    main .hover\:-translate-y-1:hover,
    main .hover\:-translate-y-0\.5:hover {
        transform: none !important;
    }

    /* 4. Text Color Stays Black (#0b0b0c), Buttons/Accents Use #013981 */
    .text-\[\#004d4d\],
    .text-\[\#003d3d\],
    .text-\[\#00695c\],
    .text-\[\#00897b\],
    .text-blue-900 {
        color: #0b0b0c !important;
    }
    .text-\[\#2dce89\] {
        color: #013981 !important;
    }
    .bg-\[\#004d4d\],
    .bg-\[\#003d3d\],
    .bg-\[\#00695c\],
    .bg-\[\#013981\],
    .hover\:bg-\[\#004d4d\]:hover,
    .hover\:bg-\[\#003d3d\]:hover,
    .hover\:bg-\[\#012b63\]:hover,
    main .bg-gradient-to-br {
        background-image: none !important;
        background-color: #013981 !important;
        color: #ffffff !important;
    }
    .bg-\[\#004d4d\] .text-\[\#7fffd4\],
    .bg-\[\#013981\] .text-\[\#7fffd4\],
    a.bg-\[\#004d4d\].text-\[\#7fffd4\],
    button.bg-\[\#004d4d\].text-\[\#7fffd4\] {
        color: #ffffff !important;
    }
    .bg-\[\#7fffd4\] {
        background-color: #013981 !important;
        color: #ffffff !important;
    }
    .bg-\[\#2dce89\] {
        background-color: #013981 !important;
        color: #ffffff !important;
    }
    .bg-\[\#f4f7f6\],
    .bg-\[\#f0faf8\] {
        background-color: #fafafa !important;
    }
    .bg-teal-50,
    .bg-\[\#e8f5f3\],
    .bg-\[\#cfe9e5\] {
        background-color: #eef4fc !important;
        color: #013981 !important;
    }
    .border-\[\#2dce89\],
    .border-\[\#013981\],
    .hover\:border-\[\#2dce89\]:hover,
    .hover\:border-\[\#013981\]:hover {
        border-color: #013981 !important;
    }
    main .bg-\[\#FFB347\] {
        background-color: #ffffff !important;
        border-color: #e4e4e7 !important;
    }

    /* Override inline background:#004d4d or #2dce89 on stat cards & buttons */
    main [style*="background:#004d4d"],
    main [style*="background: #004d4d"] {
        background: #013981 !important;
        color: #ffffff !important;
        border: 1px solid #012b63;
    }
    main [style*="background:#2dce89"],
    main [style*="background: #2dce89"] {
        background: #ffffff !important;
        color: #0b0b0c !important;
        border: 1px solid #e4e4e7 !important;
    }
    main [style*="background:#2dce89"] p,
    main [style*="background:#2dce89"] h2,
    main [style*="background:#2dce89"] span {
        color: #0b0b0c !important;
    }

    /* 5. Forms, Inputs, Selects & Textareas */
    main input[type="text"],
    main input[type="email"],
    main input[type="password"],
    main input[type="number"],
    main input[type="date"],
    main input[type="time"],
    main select,
    main textarea {
        border-radius: 0.5rem !important; /* 8px */
        border-width: 1px !important;
        border-color: #e4e4e7 !important;
        color: #0b0b0c;
        font-size: 0.8125rem;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
    }
    main input[type="text"]:focus,
    main input[type="email"]:focus,
    main input[type="password"]:focus,
    main input[type="number"]:focus,
    main input[type="date"]:focus,
    main input[type="time"]:focus,
    main select:focus,
    main textarea:focus {
        border-color: #013981 !important;
        box-shadow: 0 0 0 3px rgba(1, 57, 129, 0.10) !important;
        outline: none !important;
    }

    /* 6. Tables — Uniform Clean Framer Data Grid Look across Internal & External */
    main table thead,
    main table thead tr,
    main table thead tr.bg-\[\#004d4d\],
    main table thead tr.bg-slate-800 {
        background-color: #fafafa !important;
        color: #6a6f7c !important;
        border-bottom: 1px solid #e4e4e7 !important;
    }
    main table thead th {
        font-family: 'Geist', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        font-style: normal !important;
        letter-spacing: 0.04em !important;
        color: #6a6f7c !important;
        background-color: #fafafa !important;
        text-transform: uppercase;
    }
    main table tbody tr {
        border-bottom: 1px solid #f4f4f5;
        transition: background-color 0.12s ease;
    }
    main table tbody tr:hover {
        background-color: #fafafa !important;
    }

    /* Reusable Public Surface Helper Classes */
    .ps-card {
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px rgba(11, 11, 12, 0.03);
    }
    .ps-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-family: 'Geist', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.1875rem 0.625rem;
        border-radius: 9999px;
        border: 1px solid #d8e6f8;
        background: #eef4fc;
        color: #013981;
    }
    .ps-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: #013981;
        color: #ffffff;
        font-weight: 500;
        font-size: 0.8125rem;
        padding: 0.5625rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #013981;
        transition: background-color 0.15s ease;
    }
    .ps-btn-primary:hover {
        background: #012b63;
    }
    .ps-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: #ffffff;
        color: #0b0b0c;
        font-weight: 500;
        font-size: 0.8125rem;
        padding: 0.5625rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #e4e4e7;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }
    .ps-btn-secondary:hover {
        background: #f4f4f5;
        border-color: #d4d4d8;
    }
</style>
