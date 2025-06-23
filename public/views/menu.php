<!-- menu.php -->
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01"
            aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
            <a class="navbar-brand" href="/">WebHook de RedPay</a>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'LogWeb') !== false ? 'text-bg-dark' : ''; ?>"
                        href="/LogWeb">Transacciones</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo strpos($_SERVER['REQUEST_URI'], 'Payment') !== false || strpos($_SERVER['REQUEST_URI'], 'Consulta') !== false ? 'text-bg-dark' : ''; ?>"
                        href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Pagos
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">                      
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['REQUEST_URI'], 'Payment/List') !== false ? 'active' : ''; ?>"
                                href="/Payment/List">Consulta</a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo strpos($_SERVER['REQUEST_URI'], 'Payment/New') !== false ? 'active' : ''; ?>"
                                href="/Payment/New">Nuevo Pago</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'Users') !== false ? 'text-bg-dark' : ''; ?>"
                        href="/Users">Usuarios</a>
                </li>
            </ul>
            <div class="d-flex" role="search">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#loginModal" id="btnModalLogin">Entrar</button>
                </form>
                <button type="button" class="btn btn-outline-danger" id="btnSalir">Desconectarse</button>
                </form>
            </div>
        </div>
</nav>
<!-- Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" data-bs-backdrop="static"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Iniciar Sesión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="loginForm" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" placeholder="Ingrese su usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" placeholder="Ingrese su contraseña"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Toast container for notifications -->
<div id="toast-container" aria-live="polite" aria-atomic="true" class="d-flex justify-content-center align-items-center w-100">
    <div class="toast-container p-3">
    </div>
</div>
<!--begin::Page loading-->
<div id="loading-page" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(170, 144, 230, 0.8); display: flex; justify-content: center; align-items: center; z-index: 1000;">
  <div class="d-flex justify-content-center align-items-center">
    <div class="spinner-grow text-primary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-secondary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-success" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-danger" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-warning" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-info" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-light" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div class="spinner-grow text-dark" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
</div>
<script src="../public/js/login.js?v=<?= time() ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>