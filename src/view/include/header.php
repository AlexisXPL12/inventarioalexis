<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>SIGI - IES</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Sistema Integrado de Gestión Institucional" name="description" />
    <meta content="AnibalYucraC" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo BASE_URL ?>src/view/pp/assets/images/favicon.ico">

    <!-- Plugins css -->
    <script src="<?php echo BASE_URL ?>src/view/js/principal.js"></script>
    <link href="<?php echo BASE_URL ?>src/view/pp/plugins/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo BASE_URL ?>src/view/pp/plugins/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo BASE_URL ?>src/view/pp/plugins/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo BASE_URL ?>src/view/pp/plugins/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
    <!-- Sweet Alerts css -->
    <link href="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="<?php echo BASE_URL ?>src/view/pp/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo BASE_URL ?>src/view/pp/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo BASE_URL ?>src/view/pp/assets/css/theme.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo BASE_URL ?>src/view/include/styles.css" rel="stylesheet" type="text/css" />
    <script>
        const base_url = '<?php echo BASE_URL; ?>';
        const base_url_server = '<?php echo BASE_URL_SERVER; ?>';
        const session_session = '<?php echo $_SESSION['sesion_id']; ?>';
        const session_ies = '<?php echo $_SESSION['sesion_ies']; ?>';
        const token_token = '<?php echo $_SESSION['sesion_token']; ?>';
    </script>
    <?php date_default_timezone_set('America/Lima');  ?>
    <style>
        /* ============================================
           SISTEMA DE GESTIÓN - TEMA OSCURO ELEGANTE
           ============================================ */

        /* Variables CSS para el tema oscuro */
        :root {
            /* Colores principales */
            --bg-primary: #1a1a2e;
            --bg-secondary: #16213e;
            --bg-card: rgba(30, 41, 59, 0.8);
            --bg-input: #374151;
            --bg-button: linear-gradient(135deg, #374151 0%, #4b5563 100%);

            /* Gradientes animados */
            --gradient-main: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f172a 100%);
            --gradient-accent: linear-gradient(45deg, #4338ca, #3730a3, #312e81);
            --gradient-hover: linear-gradient(135deg, #4b5563 0%, #6b7280 100%);

            /* Colores de texto */
            --text-primary: #e2e8f0;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --text-accent: #60a5fa;

            /* Bordes y sombras */
            --border-color: rgba(71, 85, 105, 0.3);
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.3);
            --shadow-strong: 0 8px 32px rgba(0, 0, 0, 0.4);
            --glow-blue: 0 0 20px rgba(96, 165, 250, 0.3);

            /* Z-index layers - CLAVE PARA SOLUCIONAR EL PROBLEMA */
            --z-background: -1;
            --z-base: 1;
            --z-cards: 10;
            --z-header: 1000;
            --z-navigation: 1100;
            --z-dropdown: 1200;
            --z-popup: 10000;
        }

        /* Animaciones globales */
        @keyframes gradientShift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Estilos base del body */
        body {
            background: var(--gradient-main) !important;
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            color: var(--text-primary) !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Efectos de partículas de fondo */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 20% 80%, rgba(96, 165, 250, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(59, 130, 246, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: var(--z-background);
        }

        /* ===============================
           HEADER PRINCIPAL
           =============================== */
        #page-topbar {
            background: rgba(26, 26, 46, 0.95) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color) !important;
            box-shadow: var(--shadow-soft);
            position: fixed !important;
            /* FIJO para mantener siempre arriba */
            top: 0;
            left: 0;
            right: 0;
            z-index: var(--z-header) !important;
            width: 100%;
        }

        #page-topbar::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient-accent);
        }

        /* Logo mejorado */
        #page-topbar .navbar-brand-box .logo {
            color: var(--text-primary) !important;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
        }

        #page-topbar .navbar-brand-box .logo:hover {
            color: var(--text-accent) !important;
            text-shadow: var(--glow-blue);
            text-decoration: none;
            transform: translateX(5px);
        }

        #page-topbar .navbar-brand-box .logo i {
            font-size: 1.8rem;
            margin-right: 12px;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: pulse 2s infinite;
        }

        /* Botones del header */
        #page-topbar .btn.header-item {
            background: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-primary) !important;
            border-radius: 12px;
            transition: all 0.4s ease;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
            z-index: var(--z-header) !important;
        }

        #page-topbar .btn.header-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 0.2), transparent);
            transition: left 0.5s;
        }

        #page-topbar .btn.header-item:hover::before {
            left: 100%;
        }

        #page-topbar .btn.header-item:hover,
        #page-topbar .btn.header-item:focus {
            background: var(--bg-button) !important;
            border-color: var(--text-accent) !important;
            color: var(--text-primary) !important;
            transform: translateY(-2px);
            box-shadow: var(--glow-blue);
        }

        /* Avatar del usuario */
        .header-profile-user {
            border: 2px solid var(--border-color);
            transition: all 0.4s ease;
            filter: brightness(1.1);
        }

        #page-topbar .btn.header-item:hover .header-profile-user {
            border-color: var(--text-accent);
            transform: scale(1.1);
            box-shadow: var(--glow-blue);
        }

        /* ===============================
           BARRA DE NAVEGACIÓN
           =============================== */
        .topnav {
            background: rgba(22, 33, 62, 0.9) !important;
            backdrop-filter: blur(15px);
            border: none !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: fixed !important;
            /* FIJO para mantener consistencia */
            top: 70px;
            /* Debajo del header */
            left: 0;
            right: 0;
            z-index: var(--z-navigation) !important;
            width: 100%;
        }

        .topnav::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background: var(--gradient-accent);
        }

        /* Enlaces de navegación */
        .topnav .navbar-nav .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 600;
            padding: 15px 25px;
            border-radius: 10px;
            margin: 0 5px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .topnav .navbar-nav .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-accent);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .topnav .navbar-nav .nav-link:hover::before {
            left: 0;
        }

        .topnav .navbar-nav .nav-link:hover,
        .topnav .navbar-nav .nav-link:focus {
            color: white !important;
            transform: translateY(-3px);
            box-shadow: var(--shadow-strong);
        }

        .topnav .navbar-nav .nav-link i {
            margin-right: 8px;
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        .topnav .navbar-nav .nav-link:hover i {
            transform: scale(1.2);
        }

        /* ===============================
           DROPDOWNS MEJORADOS - SOLUCIÓN PRINCIPAL
           =============================== */
        .dropdown-menu {
            background: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 15px !important;
            box-shadow: var(--shadow-strong) !important;
            backdrop-filter: blur(20px);
            padding: 10px 0 !important;
            margin-top: 10px !important;
            z-index: var(--z-dropdown) !important;
            /* Z-INDEX MÁS ALTO */
            animation: fadeInDown 0.3s ease;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            min-width: 200px;
            transform: translateY(0) !important;
        }

        /* Dropdown del header - posicionamiento especial */
        #page-topbar .dropdown-menu {
            z-index: calc(var(--z-dropdown) + 100) !important;
            /* Aún más alto para header */
            right: 0 !important;
            left: auto !important;
        }

        /* Dropdown de navegación */
        .topnav .dropdown-menu {
            z-index: calc(var(--z-dropdown) + 50) !important;
        }

        .dropdown-item {
            padding: 12px 25px !important;
            color: var(--text-primary) !important;
            transition: all 0.3s ease !important;
            font-weight: 500;
            border-radius: 8px;
            margin: 2px 8px;
            position: relative;
            overflow: hidden;
        }

        .dropdown-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-accent);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .dropdown-item:hover::before {
            left: 0;
        }

        .dropdown-item:hover,
        .dropdown-item:focus,
        .dropdown-item:active {
            background: transparent !important;
            color: white !important;
            transform: translateX(5px);
        }

        /* Asegurar que los dropdowns se mantengan abiertos correctamente */
        .dropdown.show .dropdown-menu {
            display: block !important;
            opacity: 1;
            visibility: visible;
            transform: translateY(0) !important;
        }

        /* ===============================
           TARJETAS DEL DASHBOARD - Z-INDEX CORREGIDO
           =============================== */
        .card {
            background: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 20px !important;
            box-shadow: var(--shadow-soft) !important;
            backdrop-filter: blur(15px);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
            z-index: var(--z-cards) !important;
            /* Z-INDEX MENOR QUE LOS DROPDOWNS */
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient-accent);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-strong);
            border-color: var(--text-accent);
            z-index: var(--z-cards) !important;
            /* Mantener z-index bajo incluso en hover */
        }

        .card-title {
            color: var(--text-primary) !important;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .card h2,
        .card h3,
        .card h4 {
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .card .btn-primary {
            background: var(--bg-button) !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .card .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-hover);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .card .btn-primary:hover::before {
            left: 0;
        }

        .card .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--glow-blue);
            color: white;
        }

        /* ===============================
           CONTENIDO PRINCIPAL - AJUSTE DE POSICIÓN
           =============================== */
        .page-content {
            background: transparent;
            min-height: calc(100vh - 200px);
            padding-top: 140px !important;
            /* Espacio para header y nav fijos */
            position: relative;
            z-index: var(--z-base);
        }

        .container-fluid {
            position: relative;
            z-index: var(--z-base);
        }

        /* ===============================
           POPUP DE CARGA MEJORADO
           =============================== */
        #popup-carga {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: var(--z-popup) !important;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #popup-carga .popup-content {
            background: var(--bg-card);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: var(--shadow-strong);
            border: 1px solid var(--border-color);
            max-width: 300px;
            animation: fadeInUp 0.4s ease;
        }

        #popup-carga .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--border-color);
            border-top: 3px solid var(--text-accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
            filter: drop-shadow(var(--glow-blue));
        }

        #popup-carga p {
            margin: 0;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1rem;
        }

        /* ===============================
           FOOTER ELEGANTE
           =============================== */
        .footer {
            background: var(--bg-secondary) !important;
            border-top: 1px solid var(--border-color) !important;
            margin-top: 4rem;
            padding: 2.5rem 0;
            position: relative;
            overflow: hidden;
            z-index: var(--z-base);
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient-accent);
        }

        .footer .text-center,
        .footer .text-right {
            color: var(--text-secondary) !important;
            font-weight: 500;
        }

        /* ===============================
           MEJORAS RESPONSIVE
           =============================== */
        @media (max-width: 768px) {
            #page-topbar .navbar-brand-box .logo span {
                font-size: 0.9rem;
            }

            .topnav .navbar-nav .nav-link {
                padding: 12px 18px;
                margin: 2px 0;
            }

            .card {
                margin-bottom: 1.5rem;
            }

            .dropdown-menu {
                border-radius: 12px !important;
                margin-top: 5px !important;
                left: 0 !important;
                right: 0 !important;
                min-width: auto;
                max-width: 280px;
            }

            .page-content {
                padding-top: 120px !important;
            }

            /* Dropdown del header en móvil */
            #page-topbar .dropdown-menu {
                right: 10px !important;
                left: auto !important;
                min-width: 180px;
            }
        }

        /* Efectos adicionales para elementos interactivos */
        .btn,
        .form-control,
        .card,
        .dropdown-menu {
            will-change: transform;
        }

        /* Mejoras de accesibilidad */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Estados de focus mejorados */
        .btn:focus,
        .dropdown-item:focus,
        .nav-link:focus {
            outline: 2px solid var(--text-accent);
            outline-offset: 2px;
        }

        /* ===============================
           CORRECCIONES ADICIONALES
           =============================== */

        /* Evitar overflow en elementos padre */
        .navbar-header,
        .topnav .container-fluid {
            position: relative;
            overflow: visible;
        }

        /* Asegurar que los dropdowns no se corten */
        .dropdown {
            position: relative;
        }

        .dropdown-toggle::after {
            transition: transform 0.3s ease;
        }

        .dropdown.show .dropdown-toggle::after {
            transform: rotate(180deg);
        }

        /* Corrección para elementos que puedan interferir */
        .main-content {
            position: relative;
            z-index: var(--z-base);
        }

        #layout-wrapper {
            position: relative;
            z-index: var(--z-base);
        }

        /* Corrección específica para Bootstrap dropdowns */
        .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            z-index: var(--z-dropdown) !important;
        }

        /* ===============================
   CORRECCIÓN ESPECÍFICA PARA DROPDOWNS DEL HEADER
   =============================== */

/* Variables de Z-index actualizadas */
:root {
    --z-dropdown-header: 1600;  /* MÁS ALTO PARA HEADER */
    --z-dropdown-nav: 1200;     /* MENOR PARA NAVEGACIÓN */
}

/* Contenedor del header - CRÍTICO */
.navbar-header {
    position: relative !important;
    overflow: visible !important;
    z-index: var(--z-header) !important;
}

/* Contenedor de dropdowns del header - SOLUCIÓN PRINCIPAL */
#page-topbar .d-flex.align-items-center {
    position: relative !important;
    z-index: calc(var(--z-header) + 1) !important;
}

/* DROPDOWN DEL HEADER - Z-INDEX MÁS ALTO */
#page-topbar .dropdown {
    position: relative !important;
    z-index: var(--z-dropdown-header) !important;
}

#page-topbar .dropdown-menu {
    background: rgba(30, 41, 59, 0.98) !important;
    border: 1px solid rgba(96, 165, 250, 0.3) !important;
    border-radius: 15px !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5) !important;
    backdrop-filter: blur(25px) !important;
    padding: 15px 0 !important;
    margin-top: 12px !important;
    min-width: 220px !important;
    max-width: 300px !important;
    
    /* POSICIONAMIENTO CRÍTICO */
    position: absolute !important;
    top: calc(100% + 5px) !important;
    right: 0 !important;
    left: auto !important;
    
    /* Z-INDEX MÁS ALTO */
    z-index: var(--z-dropdown-header) !important;
    
    /* Animación mejorada */
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
}

/* Mostrar dropdown cuando esté activo */
#page-topbar .dropdown.show .dropdown-menu {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) !important;
    display: block !important;
}

/* DROPDOWN DE NAVEGACIÓN - Z-INDEX CORREGIDO */
.topnav .dropdown-menu {
    z-index: var(--z-dropdown-nav) !important; /* MENOR QUE HEADER */
    
    /* Animación mejorada */
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
}

.topnav .dropdown.show .dropdown-menu {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) !important;
    display: block !important;
}

/* ===============================
   CORRECCIÓN FINAL - FORZAR Z-INDEX
   =============================== */

/* Asegurar que NADA interfiera con los dropdowns del header */
#page-topbar .dropdown-menu,
#page-topbar .dropdown-menu.show {
    z-index: 1600 !important; /* Valor muy alto */
}

/* Contenido que NO debe interferir */
.main-content,
.container-fluid,
.row,
.col-*,
.card,
.card:hover {
    z-index: auto !important;
}

/* Navegación debe estar por debajo */
.topnav,
.topnav .dropdown-menu {
    z-index: 1100 !important;
}
        
    </style>
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <div class="main-content">

            <header id="page-topbar">
                <div class="navbar-header">
                    <!-- LOGO -->
                    <div class="navbar-brand-box d-flex align-items-left">
                        <a href="<?php echo BASE_URL ?>" class="logo">
                            <i class="mdi mdi-album"></i>
                            <span>
                                SISTEMA DE GESTION DE INVENTARIO
                            </span>
                        </a>

                        <button type="button" class="btn btn-sm mr-2 font-size-16 d-lg-none header-item waves-effect waves-light" data-toggle="collapse" data-target="#topnav-menu-content">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item waves-effect waves-light"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-none d-sm-inline-block ml-1" id="menu_ies">Huanta</span>
                                <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" id="contenido_menu_ies">
                            </div>
                        </div>
                        <div class="dropdown d-inline-block ml-2">
                            <button type="button" class="btn header-item waves-effect waves-light"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="rounded-circle header-profile-user" src="https://cdn-icons-png.flaticon.com/512/1077/1077063.png">
                                <span class="d-none d-sm-inline-block ml-1"><?php /* echo $_SESSION['sesion_sigi_usuario_nom']; */ ?></span>
                                <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item d-flex align-items-center justify-content-between" href="javascript:void(0)">
                                    Mi perfil
                                </a>
                                <button class="dropdown-item d-flex align-items-center justify-content-between" onclick="sent_email_password();">
                                    <span>Cambiar mi Contraseña</span>
                                </button>
                                <button class="dropdown-item d-flex align-items-center justify-content-between" onclick="cerrar_sesion();">
                                    <span>Cerrar Sesión</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <div class="topnav">
                <div class="container-fluid">
                    <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                        <div class="collapse navbar-collapse" id="topnav-menu-content">
                            <ul class="navbar-nav">

                                <!-- ---------------------------------------------- INICIO MENU SIGI ------------------------------------------------------------ -->
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo BASE_URL ?>">
                                        <i class="mdi mdi-home"></i>Inicio
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-diamond-stone"></i>Gestión <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="topnav-components">
                                        <a href="<?php echo BASE_URL ?>usuarios" class="dropdown-item">Usuarios</a>
                                        <a href="<?php echo BASE_URL ?>instituciones" class="dropdown-item">Instituciones</a>
                                        <a href="<?php echo BASE_URL ?>ambientes" class="dropdown-item">Ambientes</a>
                                        <a href="<?php echo BASE_URL ?>bienes" class="dropdown-item">Bienes</a>
                                        <a href="<?php echo BASE_URL ?>movimientos" class="dropdown-item">Movimientos</a>
                                        <a href="<?php echo BASE_URL ?>reportes" class="dropdown-item">Reportes</a>
                                    </div>
                                </li>

                                <!-- ---------------------------------------------- FIN MENU SIGI ------------------------------------------------------------ -->
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>


            <div class="page-content">
                <div class="container-fluid">
                    <!-- start page title -->

                    <!-- Popup de carga -->
                    <div id="popup-carga" style="display: none;">
                        <div class="popup-overlay">
                            <div class="popup-content">
                                <div class="spinner"></div>
                                <p>Cargando, por favor espere...</p>
                            </div>
                        </div>
                    </div>
                    <script>
                        cargar_datos_menu(<?php echo $_SESSION['sesion_ies']; ?>);
                    </script>