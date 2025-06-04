
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Actualizar Contraseña</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%,rgb(13, 2, 25) 100%);
      color: #fff;
    }

    .container {
      background: rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(20px);
      padding: 40px 35px;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
      border: 1px solid rgba(255, 255, 255, 0.18);
      text-align: center;
      width: 100%;
      max-width: 400px;
      animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .container h2 {
      font-size: 1.8rem;
      margin-bottom: 30px;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .form-group {
      position: relative;
      margin: 20px 0;
      text-align: left;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.9);
    }

    .input-container {
      position: relative;
    }

    .input-container input {
      width: 100%;
      padding: 12px 45px 12px 15px;
      border-radius: 10px;
      border: 2px solid rgba(255, 255, 255, 0.2);
      outline: none;
      background: rgba(255, 255, 255, 0.9);
      color: #333;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .input-container input:focus {
      border-color: #4CAF50;
      box-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
      transform: translateY(-2px);
    }

    .input-container input.error {
      border-color: #f44336;
      box-shadow: 0 0 10px rgba(244, 67, 54, 0.3);
    }

    .input-container input.success {
      border-color: #4CAF50;
      box-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #666;
      font-size: 1.1rem;
      transition: color 0.3s ease;
    }

    .toggle-password:hover {
      color: #4CAF50;
    }

    .password-strength {
      margin-top: 8px;
      font-size: 0.8rem;
    }

    .strength-bar {
      height: 4px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 2px;
      margin: 5px 0;
      overflow: hidden;
    }

    .strength-fill {
      height: 100%;
      width: 0%;
      transition: all 0.3s ease;
      border-radius: 2px;
    }

    .strength-weak { background: #f44336; }
    .strength-medium { background: #ff9800; }
    .strength-strong { background: #4CAF50; }

    .error-message {
      color: #ffcdd2;
      font-size: 0.8rem;
      margin-top: 5px;
      display: none;
    }

    .success-message {
      color: #c8e6c9;
      font-size: 0.8rem;
      margin-top: 5px;
      display: none;
    }

    .requirements {
      text-align: left;
      margin-top: 15px;
      padding: 15px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 10px;
      font-size: 0.85rem;
    }

    .requirements h4 {
      margin: 0 0 10px 0;
      color: rgba(255, 255, 255, 0.9);
    }

    .requirement {
      display: flex;
      align-items: center;
      margin: 5px 0;
      color: rgba(255, 255, 255, 0.7);
    }

    .requirement i {
      margin-right: 8px;
      width: 12px;
    }

    .requirement.valid {
      color: #4CAF50;
    }

    .requirement.invalid {
      color: #f44336;
    }

    .btn {
      width: 100%;
      padding: 14px;
      margin-top: 25px;
      background: linear-gradient(45deg, #4CAF50, #45a049);
      border: none;
      border-radius: 10px;
      color: #fff;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
    }

    .btn:active {
      transform: translateY(0);
    }

    .btn:disabled {
      background: rgba(255, 255, 255, 0.3);
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .loading {
      display: none;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    .spinner {
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top: 2px solid #fff;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    @media (max-width: 480px) {
      .container {
        padding: 30px 25px;
        margin: 10px;
      }
      
      .container h2 {
        font-size: 1.5rem;
      }
    }
  </style>
  <script>
    const base_url = '<?php echo BASE_URL; ?>';
    const base_url_server = '<?php echo BASE_URL_SERVER; ?>';
  </script>
  <script src=""></script>
</head>
<body>
  <div class="container">
    <input type="hidden" id="data" value="">
    <input type="hidden" id="data2" value="">
    
    <h2>
      <i class="fas fa-key"></i>
      Actualizar Contraseña
    </h2>

    <form id="passwordForm" novalidate>
      <div class="form-group">
    <input type="hidden" id="data" value="<?php echo $_GET['data']?>">
    <input type="hidden" id="data2" value="<?= @$_GET['data2'] ?>">
      <label for="password">Nueva Contraseña</label>
    <div class="input-container">
          <input type="password" id="password" placeholder="Ingresa tu nueva contraseña" required>
          <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)" title="Mostrar/Ocultar contraseña"></i>
        </div>
        <div class="password-strength">
          <div class="strength-bar">
            <div class="strength-fill" id="strengthFill"></div>
          </div>
          <span id="strengthText">Fortaleza: Muy débil</span>
        </div>
        <div class="error-message" id="passwordError"></div>
      </div>

      <div class="form-group">
        <label for="confirmPassword">Confirmar Contraseña</label>
        <div class="input-container">
          <input type="password" id="confirmPassword" placeholder="Confirma tu nueva contraseña" required>
          <i class="fas fa-eye toggle-password" onclick="togglePassword('confirmPassword', this)" title="Mostrar/Ocultar contraseña"></i>
        </div>
        <div class="error-message" id="confirmError"></div>
        <div class="success-message" id="confirmSuccess">Las contraseñas coinciden</div>
      </div>

      <div class="requirements">
        <h4>Requisitos de la contraseña:</h4>
        <div class="requirement" id="req-length">
          <i class="fas fa-times"></i>
          Al menos 8 caracteres
        </div>
        <div class="requirement" id="req-uppercase">
          <i class="fas fa-times"></i>
          Una letra mayúscula
        </div>
        <div class="requirement" id="req-lowercase">
          <i class="fas fa-times"></i>
          Una letra minúscula
        </div>
        <div class="requirement" id="req-number">
          <i class="fas fa-times"></i>
          Un número
        </div>
        <div class="requirement" id="req-special">
          <i class="fas fa-times"></i>
          Un carácter especial (!@#$%^&*)
        </div>
      </div>

      <button type="submit" class="btn" id="submitBtn">
        <span id="btnText">Actualizar Contraseña</span>
        <div class="loading" id="loading">
          <div class="spinner"></div>
        </div>
      </button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Variables globales
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirmPassword');
    const form = document.getElementById('passwordForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Configuración de requisitos
    const requirements = {
      length: { regex: /.{8,}/, element: 'req-length' },
      uppercase: { regex: /[A-Z]/, element: 'req-uppercase' },
      lowercase: { regex: /[a-z]/, element: 'req-lowercase' },
      number: { regex: /\d/, element: 'req-number' },
      special: { regex: /[!@#$%^&*(),.?":{}|<>]/, element: 'req-special' }
    };

    // Función para alternar visibilidad de contraseña
    function togglePassword(id, icon) {
      const input = document.getElementById(id);
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }

    // Función para evaluar fortaleza de contraseña
    function evaluatePasswordStrength(password) {
      let score = 0;
      let feedback = [];

      // Evaluar cada requisito
      Object.entries(requirements).forEach(([key, req]) => {
        const element = document.getElementById(req.element);
        const isValid = req.regex.test(password);
        
        if (isValid) {
          score++;
          element.classList.add('valid');
          element.classList.remove('invalid');
          element.querySelector('i').className = 'fas fa-check';
        } else {
          element.classList.add('invalid');
          element.classList.remove('valid');
          element.querySelector('i').className = 'fas fa-times';
        }
      });

      // Determinar nivel de fortaleza
      const strengthFill = document.getElementById('strengthFill');
      const strengthText = document.getElementById('strengthText');
      
      if (score === 0) {
        strengthFill.style.width = '0%';
        strengthText.textContent = 'Fortaleza: Muy débil';
        strengthFill.className = 'strength-fill';
      } else if (score <= 2) {
        strengthFill.style.width = '25%';
        strengthText.textContent = 'Fortaleza: Débil';
        strengthFill.className = 'strength-fill strength-weak';
      } else if (score <= 4) {
        strengthFill.style.width = '60%';
        strengthText.textContent = 'Fortaleza: Media';
        strengthFill.className = 'strength-fill strength-medium';
      } else {
        strengthFill.style.width = '100%';
        strengthText.textContent = 'Fortaleza: Fuerte';
        strengthFill.className = 'strength-fill strength-strong';
      }

      return score;
    }

    // Función para validar contraseñas
    function validatePasswords() {
      const password = passwordInput.value;
      const confirmPassword = confirmInput.value;
      const passwordError = document.getElementById('passwordError');
      const confirmError = document.getElementById('confirmError');
      const confirmSuccess = document.getElementById('confirmSuccess');
      
      let isValid = true;

      // Validar fortaleza de contraseña
      const strengthScore = evaluatePasswordStrength(password);
      
      if (password && strengthScore < 5) {
        passwordInput.classList.add('error');
        passwordInput.classList.remove('success');
        passwordError.textContent = 'La contraseña debe cumplir todos los requisitos';
        passwordError.style.display = 'block';
        isValid = false;
      } else if (password) {
        passwordInput.classList.add('success');
        passwordInput.classList.remove('error');
        passwordError.style.display = 'none';
      }

      // Validar confirmación
      if (confirmPassword && password !== confirmPassword) {
        confirmInput.classList.add('error');
        confirmInput.classList.remove('success');
        confirmError.textContent = 'Las contraseñas no coinciden';
        confirmError.style.display = 'block';
        confirmSuccess.style.display = 'none';
        isValid = false;
      } else if (confirmPassword && password === confirmPassword && strengthScore === 5) {
        confirmInput.classList.add('success');
        confirmInput.classList.remove('error');
        confirmError.style.display = 'none';
        confirmSuccess.style.display = 'block';
      } else if (confirmPassword) {
        confirmInput.classList.remove('success', 'error');
        confirmError.style.display = 'none';
        confirmSuccess.style.display = 'none';
      }

      // Habilitar/deshabilitar botón
      const allValid = password && confirmPassword && strengthScore === 5 && password === confirmPassword;
      submitBtn.disabled = !allValid;
      
      return allValid;
    }

    // Event listeners
    passwordInput.addEventListener('input', validatePasswords);
    confirmInput.addEventListener('input', validatePasswords);

    // Manejar envío del formulario
    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      if (!validatePasswords()) {
        Swal.fire({
          icon: 'error',
          title: 'Error de validación',
          text: 'Por favor, corrige los errores antes de continuar.',
          confirmButtonColor: '#4CAF50'
        });
        return;
      }

      // Mostrar loading
      const btnText = document.getElementById('btnText');
      const loading = document.getElementById('loading');
      
      submitBtn.disabled = true;
      btnText.style.display = 'none';
      loading.style.display = 'block';

      try {
        // Simular petición al servidor
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // Aquí harías la petición real al servidor
        /*
        const response = await fetch('/api/update-password', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            password: passwordInput.value,
            data: document.getElementById('data').value,
            data2: document.getElementById('data2').value
          })
        });
        
        if (!response.ok) {
          throw new Error('Error al actualizar la contraseña');
        }
        */

        await Swal.fire({
          icon: 'success',
          title: '¡Contraseña actualizada!',
          text: 'Tu contraseña ha sido actualizada correctamente.',
          confirmButtonColor: '#4CAF50',
          timer: 3000,
          timerProgressBar: true
        });

        // Limpiar formulario
        form.reset();
        validatePasswords();
        
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Hubo un problema al actualizar la contraseña. Inténtalo de nuevo.',
          confirmButtonColor: '#4CAF50'
        });
      } finally {
        // Ocultar loading
        submitBtn.disabled = false;
        btnText.style.display = 'block';
        loading.style.display = 'none';
      }
    });

    // Inicializar validación
    validatePasswords();
  </script>
</body>
</html>