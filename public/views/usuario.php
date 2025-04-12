<!DOCTYPE html>
<html lang="es-mx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simular pago</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../public/css/home.css" rel="stylesheet">
</head>

<body class="bd-indigo-400">
    <?php include __DIR__ . '../../views/menu.php'; ?>
    <div class="container bd-indigo-400">
        <div class="row">
            </br>
        </div>
        <div class="card text-bg-ligh border-success">
            <div class="card-header border-success">
                <h4>Administración de usuarios<h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button class="btn btn-outline-primary" onClick="OpenModal(true,{})" type="button">Nuevo</button>
                    </div>
                    <div class="col-md-12">
                        <h5>Lista de usuarios</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="tblUsers">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Correo</th>
                                        <th scope="col">Creación</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="usuariosBody">
                                    <!-- Aquí se llenará la tabla con AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-success">
                By Jose Rodriguez
            </div>
        </div>
    </div>
    <!--Modal Nuevo Usuario -->
    <div class="modal fade" id="modalUser" tabindex="-1" aria-labelledby="userModalLabel" data-bs-backdrop="static"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Registrar usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm" method="POST" data-action="new">
                        <input type="hidden" id="usuarioId" value="">
                        <div class="mb-3">
                            <label for="username1" class="form-label">Nombre de usuario</label>
                            <input type="text" class="form-control" id="username1" placeholder="Ingrese un username o nickname"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="emailuser1" class="form-label">Email</label>
                            <input type="email" class="form-control" id="emailuser1" placeholder="Ingrese un correo valido"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="password1" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password1" minlength="8"
                                placeholder="minimo 8 caracteres" required>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary" id="btnRegister">Registrar</button>
                            <button type="submit" class="btn btn-outline-primary" id="btnEditar">Editar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <!-- jQuery (para usar AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../public/js/general.js"></script>
    <script src="../public/js/usuario.js"></script>
</body>

</html>