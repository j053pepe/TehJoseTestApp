document.addEventListener('DOMContentLoaded', async function () {
    const loginButton = document.getElementById('btnModalLogin');
    const logoutButton = document.getElementById('btnSalir');

    logoutButton.addEventListener('click', () => {
        showBootstrapToast('secondary', 'Cerrando sesion');
        sessionStorage.removeItem('token');
        // Recargar la página después de 5 segundos
        setTimeout(() => {
            window.location.reload();
        }, 5000);
    });

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {        
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Evita que el formulario se envíe de manera tradicional
            $('#loginModal').modal('hide'); // Ocultar el modal al cargar la página
            // Obtén los datos del formulario
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // Llama a CallApi y maneja la respuesta
            CallApi('/api/account/login', 'POST', { username: username, password: password }, false)
                .then(data => {
                    if (data.status === 'success') {
                        // Mostrar alerta de éxito
                        showBootstrapToast('success', 'Login exitoso');

                        // Guardar el token en sessionStorage
                        sessionStorage.setItem('token', data.token);
                        //Ocultar botones de login y mostrar el de logout
                        loginButton.style.display = 'none';
                        logoutButton.style.display = 'block';
                        RetryCalls(); // Reintentar las peticiones pendientes
                    } else {
                        // Mostrar alerta de error
                        showBootstrapToast('danger', 'Error: ' + data.error,5000, dataresult => {
                            // Mostrar el modal de login nuevamente
                            $('#loginModal').modal('show');
                        });
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud:', error);
                    // Mostrar alerta de error
                    showBootstrapToast('danger', 'Credenciales incorrectas.',5000, dataresult => {
                        // Mostrar el modal de login nuevamente
                        $('#loginModal').modal('show');
                    });
                });
        });
    }
});