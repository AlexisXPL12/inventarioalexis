<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .background-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #0f3460, #16537e, #1e3a8a, #1f2937, #374151);
            background-size: 300% 300%;
            animation: gradientShift 8s ease infinite;
            opacity: 0.9;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            background: rgba(100, 120, 150, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .login-container {
            background: rgba(30, 30, 50, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(100, 120, 150, 0.3);
            border-radius: 20px;
            padding: 40px 30px;
            width: 400px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 10;
            animation: slideUp 0.8s ease-out;
            text-align: center;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container h1 {
            color: #e2e8f0;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            animation: textGlow 2s ease-in-out infinite alternate;
        }

        @keyframes textGlow {
            from { text-shadow: 0 0 10px rgba(100, 150, 200, 0.5); }
            to { text-shadow: 0 0 20px rgba(100, 150, 200, 0.8); }
        }

        .login-container img {
            margin-bottom: 20px;
            border-radius: 10px;
            opacity: 0.9;
            transition: transform 0.3s ease;
        }

        .login-container img:hover {
            transform: scale(1.05);
        }

        .login-container h4 {
            color: rgba(226, 232, 240, 0.8);
            font-size: 1.1rem;
            margin-bottom: 30px;
            font-weight: normal;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            color: rgba(226, 232, 240, 0.6);
            font-size: 18px;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .input-wrapper input {
            padding-left: 50px !important;
        }

        .input-wrapper:focus-within .input-icon {
            color: rgba(100, 150, 200, 0.8);
            transform: scale(1.1);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            color: rgba(226, 232, 240, 0.6);
            font-size: 18px;
            cursor: pointer;
            z-index: 2;
            transition: all 0.3s ease;
            user-select: none;
        }

        .toggle-password:hover {
            color: rgba(100, 150, 200, 0.8);
            transform: scale(1.1);
        }

        .toggle-password.active {
            color: rgba(100, 150, 200, 1);
            animation: toggleBounce 0.3s ease;
        }

        @keyframes toggleBounce {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1.1); }
        }

        .login-container input {
            width: 100%;
            padding: 15px 20px;
            background: rgba(40, 50, 70, 0.6);
            border: 2px solid rgba(100, 120, 150, 0.3);
            border-radius: 12px;
            color: #e2e8f0;
            font-size: 16px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            outline: none;
        }

        .login-container input::placeholder {
            color: rgba(226, 232, 240, 0.5);
        }

        .login-container input:focus {
            border-color: rgba(100, 150, 200, 0.7);
            background: rgba(50, 60, 80, 0.8);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .login-container button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #374151, #4b5563);
            border: none;
            border-radius: 12px;
            color: #e2e8f0;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 20px;
        }

        .login-container button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(100, 150, 200, 0.3), transparent);
            transition: left 0.5s;
        }

        .login-container button:hover::before {
            left: 100%;
        }

        .login-container button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
            background: linear-gradient(135deg, #4b5563, #6b7280);
        }

        .login-container button:active {
            transform: translateY(-1px);
        }

        .login-container a {
            display: block;
            margin-top: 20px;
            color: rgba(226, 232, 240, 0.7);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .login-container a:hover {
            color: #e2e8f0;
            text-shadow: 0 0 10px rgba(100, 150, 200, 0.5);
        }

        .loading {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 4px solid rgba(100, 120, 150, 0.3);
            border-top: 4px solid #e2e8f0;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #64748b;
            border-radius: 50%;
            opacity: 0.6;
            animation: particleFloat 4s linear infinite;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 0.6;
            }
            90% {
                opacity: 0.6;
            }
            100% {
                transform: translateY(-10px) translateX(100px);
                opacity: 0;
            }
        }
    </style>
    <!-- Sweet Alerts css -->
    <link href="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <script>
        const base_url = '<?php echo BASE_URL; ?>';
        const base_url_server = '<?php echo BASE_URL_SERVER; ?>';
    </script>
</head>

<body>
    <div class="background-animation"></div>
    
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="particles" id="particles"></div>

    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <img src="https://sispa.iestphuanta.edu.pe/img/logo.png" alt="Logo" width="100%">
        <h4>Sistema de Control de Inventario</h4>
        
        <form id="frm_login">
            <div class="form-group">
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input type="text" name="dni" id="dni" placeholder="DNI" required>
                </div>
            </div>
            
            <div class="form-group">
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" name="password" id="password" placeholder="Contraseña" required>
                    <span class="toggle-password" onclick="togglePassword()">👁️</span>
                </div>
            </div>

            <button type="submit">
                Ingresar
                <div class="loading" id="loading"></div>
            </button>
        </form>

        <a href="#">¿Olvidaste tu contraseña?</a>
    </div>

    <script>
        // Función para mostrar/ocultar contraseña
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈';
                toggleIcon.classList.add('active');
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️';
                toggleIcon.classList.remove('active');
            }
            
            // Animación de bounce
            toggleIcon.style.animation = 'none';
            setTimeout(() => {
                toggleIcon.style.animation = 'toggleBounce 0.3s ease';
            }, 10);
        }

        // Crear partículas animadas
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            
            setInterval(() => {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDuration = (Math.random() * 3 + 2) + 's';
                particle.style.animationDelay = Math.random() * 2 + 's';
                
                particlesContainer.appendChild(particle);
                
                setTimeout(() => {
                    particle.remove();
                }, 4000);
            }, 300);
        }

        // Efectos de hover para inputs
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.animation = 'none';
                this.style.animation = 'inputFocus 0.3s ease forwards';
            });
        });

        // Inicializar partículas
        createParticles();

        // Efecto de ondas al hacer clic
        document.addEventListener('click', function(e) {
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(100, 120, 150, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.left = (e.clientX - 25) + 'px';
            ripple.style.top = (e.clientY - 25) + 'px';
            ripple.style.width = '50px';
            ripple.style.height = '50px';
            ripple.style.pointerEvents = 'none';
            
            document.body.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });

        // CSS para animación de ripple
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            
            @keyframes inputFocus {
                0% { transform: scale(1); }
                50% { transform: scale(1.02); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

<!-- Script de sesión original -->
<script src="<?php echo BASE_URL; ?>src/view/js/sesion.js"></script>
<!-- Sweet Alerts Js-->
<script src="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.js"></script>

</html>