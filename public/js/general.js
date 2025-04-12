let peticionesPendientes = [];
const loginButton = document.getElementById('btnModalLogin');
const logoutButton = document.getElementById('btnSalir');

function ToMoney(value){
    if (value) {
        value = parseFloat(value.toString().replace(/[^0-9.-]+/g,""));
        if (isNaN(value)) {
            return 0;
        }
        return value
        .toLocaleString('es-MX', {
            style: 'currency',
            currency: 'MXN'
          }); 
    } else {
        return 0;
    }
}

async function verificarToken() {
    return CallApi("/api/validate-token", 'get', '', true)
        .then(data => {
            if (!data.valid) {
                throw new Error('Token inválido');
            }
            return data;
        });
}

async function CallApi(apiUrl, type, data, isToken) {
    let headers = { 'Content-Type': 'application/json' };
    if (isToken) {
        const token = sessionStorage.getItem('token');
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        } else {
            throw new Error('Token no encontrado');
        }
    }
    document.getElementById('loading-page').style.display = 'flex';

    try {
        const response = await fetch(apiUrl, {
            method: type,
            headers: headers,
            body: data ? JSON.stringify(data) : null
        });

        if (!response.ok) {
            if (response.status === 404 || response.status === 500) {
                swal.fire({
                    title: 'Error',
                    text: response.status === 404 ? "No se encontró el recurso" : "Error interno del servidor",
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
            // Manejar errores de respuesta no exitosa (por ejemplo, 404, 500)
            if (response.headers.get('Content-Type')?.includes('application/json')) {
                // Si la respuesta es JSON, intenta analizarla
                const errorData = await response.json();
                throw new Error(JSON.stringify(errorData));
            } else {
                // Si la respuesta no es JSON, usa el texto del error
                const errorText = await response.text();
                throw new Error(errorText);
            }
        }

        return await response.json(); // Devuelve la respuesta como JSON

    } catch (error) {
        // Manejar errores de la solicitud (por ejemplo, red, parseo de JSON)
        console.error('Error en la solicitud:', error);
        throw error; // Propaga el error para que lo maneje el código que llama a CallApi
    } finally {
        document.getElementById('loading-page').style.display = 'none';
    }
}


async function CallApiWithTokenCallback(apiUrl, type, data, isToken = false, callback) {
    try {
        await verificarToken();
        const result = await CallApi(apiUrl, type, data, isToken); // Llamar a CallApi directamente
        callback(result);
    } catch (error) {
        if (error.message === 'Token inválido' || error.message === 'Token no encontrado') {
            peticionesPendientes.push({ apiUrl, type, data, isToken, callback });
            window.dispatchEvent(new CustomEvent('necesitaAutenticacion'));
            return null;
        }
        throw error;
    }
}
async function RetryCalls() {
    for (const peticion of peticionesPendientes) {
        try {
            // Llamar a CallApi directamente
            const data = await CallApi(peticion.apiUrl, peticion.type, peticion.data, peticion.isToken);
            if (data) {
                peticion.callback(data); // Llamar a la función de callback con los datos.
            }
        } catch (error) {
            console.error('Error al reintentar la petición:', error);
            // Manejar el error según sea necesario
        }
    }
    peticionesPendientes = [];
}

// Ejemplo de uso para verificar el token al cargar la página:
window.addEventListener('load', async () => {
    try {
        await verificarToken();
        loginButton.style.display = 'none';
        logoutButton.style.display = 'block';
    } catch (error) {
        console.error('Error al verificar el token:', error);
        sessionStorage.removeItem('token');
        loginButton.style.display = 'block';
        logoutButton.style.display = 'none';
        document.getElementById('loading-page').style.display = 'none';
        if (error.message === 'Token inválido' || error.message === 'Token no encontrado')
            window.dispatchEvent(new CustomEvent('necesitaAutenticacion'));
    }
});

// Escuchar el evento personalizado para mostrar el modal de login
window.addEventListener('necesitaAutenticacion', () => {
    $('#loginModal').modal('show');
});

function showBootstrapToast(type, message, duration = 5000, callback = null) {
    // Crear el elemento del Toast
    const toast = document.createElement('div');
    toast.className = `toast text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    const btnClass = type == "info" || type == "secondary" ? "btn-close" : "btn-close-white";

    // Crear el contenido del Toast con el título "Alerta"
    toast.innerHTML = `
                <div class="toast-header">
                    <strong class="me-auto">Alerta</strong><br>
                    <button type="button" class="btn-close ${btnClass} me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">                
                    ${message}
                </div>
    `;

    // Agregar el Toast al contenedor
    const toastContainer = document.getElementById('toast-container');
    toastContainer.appendChild(toast);

    // Inicializar el Toast
    const bootstrapToast = new bootstrap.Toast(toast, {
        autohide: true, // Ocultar automáticamente
        delay: duration // Duración antes de ocultar
    });

    // Mostrar el Toast
    bootstrapToast.show();

    // Eliminar el Toast del DOM después de que se oculte
    toast.addEventListener('hidden.bs.toast', () => {
        if (callback) {
            callback(); // Llamar al callback si se proporciona
        }
        toast.remove();
    });
}
