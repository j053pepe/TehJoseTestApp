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
    <?php include __DIR__ . '../../../views/menu.php'; ?>
    <div class="container bd-indigo-400">
        <div class="row">
            </br>
        </div>
        <div class="card text-bg-ligh border-success">
            <div class="card-header border-success">
                <h4>Consulta de pagos<h4>
            </div>
            <div class="card-body">
                <h5 class="card-title">Historial</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="tbPayment">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Referencia</th>
                                <th scope="col">Monto</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Fecha</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <!-- Aquí se llenará la tabla con los datos de la API -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-transparent border-success">
                By Jose Rodriguez
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <!-- jQuery (para usar AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../public/js/general.js"></script>
    <script src="../public/js/compra/consulta.js"></script>
</body>

</html>