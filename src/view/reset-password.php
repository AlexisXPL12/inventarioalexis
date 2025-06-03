<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear Contraseña</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      font-family: 'Arial', sans-serif;
      background: linear-gradient(135deg, #6e45e2, #88d3ce);
      color: #fff;
    }

    .container {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      text-align: center;
      width: 300px;
    }

    .container h2 {
      font-size: 1.5rem;
      margin-bottom: 20px;
    }

    .input-container {
      position: relative;
      margin: 15px 0;
    }

    .input-container input {
      width: 100%;
      padding: 10px 40px 10px 10px;
      border-radius: 5px;
      border: none;
      outline: none;
      background: rgba(255, 255, 255, 0.8);
      color: #333;
      font-size: 1rem;
    }

    .input-container i.toggle-password {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #555;
    }

    button {
      width: 100%;
      padding: 10px;
      margin-top: 20px;
      background: #6e45e2;
      border: none;
      border-radius: 5px;
      color: #fff;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #88d3ce;
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
    <input type="hidden" id="data" value="<?php echo $_GET['data']?>">
    <input type="hidden" id="data2" value="<?= @$_GET['data2'] ?>">
    <h2><i class="fas fa-lock"></i> Crear Contraseña</h2>
    <div class="input-container">
      <input type="password" id="password" placeholder="Nueva Contraseña">
      <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
    </div>
    <div class="input-container">
      <input type="password" id="confirm-password" placeholder="Confirmar Contraseña">
      <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm-password', this)"></i>
    </div>
    <button onclick="guardar()">Guardar</button>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
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

    function guardar() {
      const pass1 = document.getElementById('password').value;
      const pass2 = document.getElementById('confirm-password').value;

      if (pass1 !== pass2) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Las contraseñas no coinciden.' });
        return;
      }

      if (pass1.length < 6) {
        Swal.fire({ icon: 'warning', title: 'Advertencia', text: 'La contraseña debe tener al menos 6 caracteres.' });
        return;
      }

      Swal.fire({ icon: 'success', title: 'Éxito', text: 'Contraseña guardada correctamente.' });
    }
  </script>
</body>
</html>

