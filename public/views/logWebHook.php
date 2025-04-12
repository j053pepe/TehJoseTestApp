<!DOCTYPE html>
<html lang="es-mx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de logs</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <h4>Log de transacciones<h4>
            </div>
            <div class="card-body">
                <h5 class="card-title">Movimietos más recientes</h5>
                <table class="table table-responsive" id="tblLog">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">IdTransaction</th>
                            <th scope="col">Data</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>0000</td>
                            <td></td>
                            <td>Post</td>
                            <td>1
                            <td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent border-success">
                By Jose Rodriguez
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalJson" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Detalle de data</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <h4>Id Registro# <span class="badge text-bg-secondary" id="idSpan"></span></h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                <table class="table table-bordered table-responsive-sm" id="tblDetalle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    <script src="../public/js/logwebhook.js"></script>
</body>

</html>