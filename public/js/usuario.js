document.addEventListener('DOMContentLoaded', function () {
    $('#btnEditar').hide();
    getUsers();
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Evita que el formulario se envíe de manera tradicional            
            const username = document.getElementById('username1').value; // Obtiene el valor del campo de nombre de usuario
            const password = document.getElementById('password1').value; // Obtiene el valor del campo de contraseña
            const emailuser = document.getElementById('emailuser1').value; // Obtiene el valor del campo de correo electrónico
            const jsondata = { username: username, password: password, email:emailuser }; // Crea un objeto JSON con los datos del formulario
            const action = userForm.dataset.action; // Obtiene la acción del formulario
            if (action === 'new') {
                RegisterUser(jsondata); // Llama a la función para registrar un nuevo usuario
            } else if (action === 'edit') {
                UpdateUser(jsondata); // Llama a la función para editar un usuario existente
            }
        });
    }
});
function OpenModal(isnew, usuario){
    if(isnew){
        $('#btnEditar').hide();
        $('#btnRegister').show();
        document.getElementById('username1').value = "";
        document.getElementById('emailuser1').value = "";
        document.getElementById('password1').value = "";
        document.getElementById('password1').setAttribute('required', 'required');
        document.getElementById('userModalLabel').value = "Registrar usuario";
        document.getElementById('userForm').dataset.action = 'new'; // Cambia la acción del formulario
    }else{
        document.getElementById('username1').value = usuario.username;
        document.getElementById('emailuser1').value = usuario.email;
        document.getElementById('password1').value = "";
        document.getElementById('password1').removeAttribute('required');
        document.getElementById('usuarioId').value = usuario.usuarioId;
        document.getElementById('userModalLabel').value = "Editar usuario";
        document.getElementById('userForm').dataset.action = 'edit'; // Cambia la acción del formulario
        $('#btnEditar').show();
        $('#btnRegister').hide();
    }
    $('#modalUser').modal('show');
}
function RegisterUser(jsondata) {
            $('#modalUser').modal('hide');
            // Llama a CallApi y maneja la respuesta
            CallApiWithTokenCallback('/api/account/register', 'POST', jsondata, true, data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Usuario',
                            text: 'El usuario se registro existosamente.',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                          });
                          getUsers();
                    } else {
                        Swal.fire({
                            title: 'Usuario',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                          });
                          $('#modalUser').modal('show');
                    }
                });
}
function UpdateUser(jsondata) {
    const usuarioId = document.getElementById('usuarioId').value;
    $('#modalUser').modal('hide');
    CallApiWithTokenCallback(`/api/user/${usuarioId}`, 'put', jsondata , true, data => {
        if (data.status === 'success') {
            Swal.fire({
                title: 'Usuario',
                text: 'El usuario se actualizo existosamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
              });
              getUsers();
        } else {
            Swal.fire({
                title: 'Usuario',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'Aceptar'
              });
              $('#modalUser').modal('show');
        }
    });
}
function getUsers(){
    CallApiWithTokenCallback('/api/user', 'get', '', true, resultdata => {
        if (resultdata && resultdata.status === 'success') {
            localStorage.setItem('tblUsers', resultdata.data);
            let tableBody = document.querySelector('#tblUsers tbody');
            tableBody.innerHTML = '';
            resultdata.data.forEach(usuario => {
                let row = document.createElement('tr');
                let status = usuario.active?`<span class="badge rounded-pill text-bg-success">Activo</span>`:
                `<span class="badge rounded-pill text-bg-secondary">Inactivo</span>`;
                let buttonAction="";
                if(!usuario.isReadOnly){
                    buttonAction = usuario.active?`<button type="button" class="btn btn-sm btn-outline-danger" onClick="UpdateStatus('${usuario.usuarioId}')">Desactivar</button>`:
                `<button type="button" class="btn btn-sm btn-outline-success" onClick="UpdateStatus('${usuario.usuarioId}')">Activar</button>`;
                buttonAction+=`<button type="button" class="btn btn-sm btn-outline-primary" name="btnEditar" data-id="${usuario.usuarioId}">Editar</button>`;}
                else {
                    buttonAction = `<span class="badge rounded-pill text-bg-secondary">No editable</span>`;
                }
                row.innerHTML = `
            <td>${usuario.usuarioId}</td>
            <td>${usuario.username}</td>
            <td>${usuario.email}</td>
            <td>${usuario.createDate}</td>
            <td>${status}</td>
            <td>${buttonAction}</td>
            `;
                tableBody.appendChild(row);
            });
            // Agregar evento de clic a los botones de editar
            const editButtons = document.querySelectorAll('button[name="btnEditar"]');
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const usuarioId = this.getAttribute('data-id');
                    const usuario = resultdata.data.find(user => user.usuarioId === usuarioId);
                    if (usuario) {
                        OpenModal(false, usuario);
                    }
                });
            });
        }
    });
}


function UpdateStatus(usuarioId) {
    Swal.fire({
        title: 'Usuario',
        text: '¿Estas seguro de cambiar el status del usuario?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, Cambiar!',
        cancelButtonText: 'No, Cancelar!'
      }).then((result) => {
        if (result.isConfirmed) {
            CallApiWithTokenCallback(`/api/user/status/${usuarioId}`, 'put','', true, resultdata => {
                if (resultdata && resultdata.status === 'success') {
                    Swal.fire({
                        title: 'Usuario',
                        text: 'El status del usuario se actualizo existosamente.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                      });
                    getUsers();
                }else{
                    Swal.fire({
                        title: 'Usuario',
                        text: resultdata.message,
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                      });
                }
            });
        }
      });
}