// Mostrar el popup de carga
function mostrarPopupCarga() {
  const popup = document.getElementById("popup-carga");
  if (popup) {
    popup.style.display = "flex";
  }
}
// Ocultar el popup de carga
function ocultarPopupCarga() {
  const popup = document.getElementById("popup-carga");
  if (popup) {
    popup.style.display = "none";
  }
}
//funcion en caso de session acudacada
async function alerta_sesion() {
  Swal.fire({
    type: "error",
    title: "Error de Sesión",
    text: "Sesión Caducada, Por favor inicie sesión",
    confirmButtonClass: "btn btn-confirm mt-2",
    footer: "",
    timer: 1000,
  });
  location.replace(base_url + "login");
}
// cargar elementos de menu
async function cargar_institucion_menu(id_ies = 0) {
  const formData = new FormData();
  formData.append("sesion", session_session);
  formData.append("token", token_token);
  try {
    let respuesta = await fetch(
      base_url_server + "src/control/Institucion.php?tipo=listar",
      {
        method: "POST",
        mode: "cors",
        cache: "no-cache",
        body: formData,
      }
    );
    let json = await respuesta.json();
    if (json.status) {
      let datos = json.contenido;
      let contenido = "";
      let sede = "";
      datos.forEach((item) => {
        if (id_ies == item.id) {
          sede = item.nombre;
        }
        contenido += `<button href="javascript:void(0);" class="dropdown-item notify-item" onclick="actualizar_ies_menu(${item.id});">${item.nombre}</button>`;
      });
      document.getElementById("contenido_menu_ies").innerHTML = contenido;
      document.getElementById("menu_ies").innerHTML = sede;
    }
    //console.log(respuesta);
  } catch (e) {
    console.log("Error al cargar categorias" + e);
  }
}
async function cargar_datos_menu(sede) {
  cargar_institucion_menu(sede);
}
// actualizar elementos del menu
async function actualizar_ies_menu(id) {
  const formData = new FormData();
  formData.append("id_ies", id);
  try {
    let respuesta = await fetch(
      base_url + "src/control/sesion_cliente.php?tipo=actualizar_ies_sesion",
      {
        method: "POST",
        mode: "cors",
        cache: "no-cache",
        body: formData,
      }
    );
    let json = await respuesta.json();
    if (json.status) {
      location.reload();
    }
    //console.log(respuesta);
  } catch (e) {
    console.log("Error al cargar instituciones" + e);
  }
}
function generar_paginacion(total, cantidad_mostrar) {
  let actual = document.getElementById("pagina").value;
  let paginas = Math.ceil(total / cantidad_mostrar);
  let paginacion = '<li class="page-item';
  if (actual == 1) {
    paginacion += " disabled";
  }
  paginacion +=
    ' "><button class="page-link waves-effect" onclick="numero_pagina(1);">Inicio</button></li>';
  paginacion += '<li class="page-item ';
  if (actual == 1) {
    paginacion += " disabled";
  }
  paginacion +=
    '"><button class="page-link waves-effect" onclick="numero_pagina(' +
    (actual - 1) +
    ');">Anterior</button></li>';
  if (actual > 4) {
    var iin = actual - 2;
  } else {
    var iin = 1;
  }
  for (let index = iin; index <= paginas; index++) {
    if (paginas - 7 > index) {
      var n_n = iin + 5;
    }
    if (index == n_n) {
      var nn = actual + 1;
      paginacion +=
        '<li class="page-item"><button class="page-link" onclick="numero_pagina(' +
        nn +
        ')">...</button></li>';
      index = paginas - 2;
    }
    paginacion += '<li class="page-item ';
    if (actual == index) {
      paginacion += "active";
    }
    paginacion +=
      '" ><button class="page-link" onclick="numero_pagina(' +
      index +
      ');">' +
      index +
      "</button></li>";
  }
  paginacion += '<li class="page-item ';
  if (actual >= paginas) {
    paginacion += "disabled";
  }
  paginacion +=
    '"><button class="page-link" onclick="numero_pagina(' +
    (parseInt(actual) + 1) +
    ');">Siguiente</button></li>';

  paginacion += '<li class="page-item ';
  if (actual >= paginas) {
    paginacion += "disabled";
  }
  paginacion +=
    '"><button class="page-link" onclick="numero_pagina(' +
    paginas +
    ');">Final</button></li>';
  return paginacion;
}
function generar_texto_paginacion(total, cantidad_mostrar) {
  let actual = document.getElementById("pagina").value;
  let paginas = Math.ceil(total / cantidad_mostrar);
  let iniciar = (actual - 1) * cantidad_mostrar;
  if (actual < paginas) {
    var texto =
      "<label>Mostrando del " +
      (parseInt(iniciar) + 1) +
      " al " +
      (parseInt(iniciar) + 1 + 9) +
      " de un total de " +
      total +
      " registros</label>";
  } else {
    var texto =
      "<label>Mostrando del " +
      (parseInt(iniciar) + 1) +
      " al " +
      total +
      " de un total de " +
      total +
      " registros</label>";
  }
  return texto;
}
// ---------------------------------------------  DATOS DE CARGA PARA FILTRO DE BUSQUEDA -----------------------------------------------
//cargar programas de estudio
function cargar_ambientes_filtro(
  datos,
  form = "busqueda_tabla_ambiente",
  filtro = "filtro_ambiente"
) {
  let ambiente_actual = document.getElementById(filtro).value;
  lista_ambiente = `<option value="0">TODOS</option>`;
  datos.forEach((ambiente) => {
    pe_selected = "";
    if (ambiente.id == ambiente_actual) {
      pe_selected = "selected";
    }
    lista_ambiente += `<option value="${ambiente.id}" ${pe_selected}>${ambiente.detalle}</option>`;
  });
  document.getElementById(form).innerHTML = lista_ambiente;
}
//cargar programas de estudio
function cargar_sede_filtro(sedes) {
  let sede_actual = document.getElementById("sede_actual_filtro").value;
  lista_sede = `<option value="0">TODOS</option>`;
  sedes.forEach((sede) => {
    sede_selected = "";
    if (sede.id == sede_actual) {
      sede_selected = "selected";
    }
    lista_sede += `<option value="${sede.id}" ${sede_selected}>${sede.nombre}</option>`;
  });
  document.getElementById("busqueda_tabla_sede").innerHTML = lista_sede;
}
// ------------------------------------------- FIN DE DATOS DE CARGA PARA FILTRO DE BUSQUEDA -----------------------------------------------
async function validar_datos_reset_password() {
  let id = document.getElementById("data").value;
  let token = document.getElementById("data2").value;
  const formData = new FormData();
  formData.append("id", id);
  formData.append("token", token);
  formData.append("sesion", "");
  try {
    let respuesta = await fetch(
      base_url_server +
      "src/control/Usuario.php?tipo=validar_datos_reset_password",
      {
        method: "POST",
        mode: "cors",
        cache: "no-cache",
        body: formData,
      }
    );
    let json = await respuesta.json();
    if (json.status == false) {
  let formulario = document.getElementById("passwordCard");
  formulario.innerHTML = `
    <div style="text-align: center; color: white; font-weight: bold;">
      <p style="font-size: 1.2em;">
        <i class="fas fa-exclamation-triangle"></i> El enlace ha expirado o ya fue utilizado.
      </p>
      <p>Por razones de seguridad, te recomendamos iniciar sesión en tu cuenta para generar un nuevo enlace de recuperación.</p>
      <p>Si no solicitaste el cambio, puedes ignorar este mensaje.</p>
      <a href="${base_url}login" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #ffffff22; color: #fff; border-radius: 8px; text-decoration: none; transition: background 0.3s;">
        <i class="fas fa-sign-in-alt"></i> Iniciar sesión
      </a>
      <p style="margin-top: 10px; font-size: 0.9em; color: #ccc;">Serás redirigido automáticamente en unos segundos...</p>
    </div>
  `;

  // Redirección automática al login después de 7 segundos
  setTimeout(() => {
    location.replace(base_url + "login");
  }, 7000);
}
    //console.log(respuesta);
  } catch (e) {
    console.log("Error al cargar categorias" + e);
  }
}
function validar_input_password() {
  let pass1 = document.getElementById(password).value;
  let pass2 = document.getElementById(confirmPassword).value;
}

// Función para actualizar 
async function actualizar_contraseña() {
  const id = document.getElementById('data').value;
  const token = document.getElementById('data2').value;
  const password = document.getElementById('password').value; // Corregido

  const formData = new FormData();
  formData.append('id', id);
  formData.append('token', token);
  formData.append('password', password);
  formData.append('sesion', '');

  try {
    let respuesta = await fetch(base_url_server + 'src/control/Usuario.php?tipo=actualizar_password_reset', {
      method: 'POST',
      mode: 'cors',
      cache: 'no-cache',
      body: formData
    });

    let json = await respuesta.json();

    if (json.status) {
      await Swal.fire({
        icon: 'success',
        title: '¡Contraseña actualizada!',
        text: 'Tu contraseña ha sido actualizada correctamente. Serás redirigido al login.',
        confirmButtonClass: 'btn btn-confirm mt-2',
        timer: 3000,
        timerProgressBar: true
      });

      // Redirigir al login después de 3 segundos
      setTimeout(() => {
        location.replace(base_url + "login");
      }, 500);

    } else {
      throw new Error(json.mensaje || 'Error al actualizar la contraseña');
    }

  } catch (error) {
    console.log("Error al actualizar contraseña: " + error);
    throw error;
  }  
}


// ENVIAR INFORMACION DEL PASSWORD Y EL ID AL CONTROLADOR USUARIO
// RECIBIR INFORMACION EN EL CONTROLADOR Y EMCRIPTAR LA NUEVA CONTRASEÑA
// QUE ESTA SE GUARDE EN LA BASE DE DATOS Y ACTUALIZAR CAMPOS EN RESET_PASSWORD Y TOKEN_PASSWORD
// NOTIFICACION A USUARIO SOBRE EL ESTADO DEL PROCESO
//TiendaProductos