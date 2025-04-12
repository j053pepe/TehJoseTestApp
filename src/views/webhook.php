<!DOCTYPE html>
<html lang="es-mx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jose - RedPay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-between">
            <div class="col-4">
                <h2>Datos Recibidos</h2>
            </div>
            <div class="col-4 offset-md-4">
                <!-- <a class="btn btn-primary" type="button" href="/">Ir a inicio</a> -->
            </div>
        </div>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                <?php foreach ($data as $key => $value): ?>
                <?php
                            $rowClass = 'badge text-bg-secondary';
                            if (strtolower($key) === 'status') {
                                switch (strtolower($value)) {
                                    case 'accepted': $rowClass = 'badge text-bg-success'; break;
                                    case 'pending': $rowClass = 'badge text-bg-info'; break;
                                    case 'rejected': $rowClass = 'badge text-bg-danger'; break;
                                    default: 
                                    $rowClass = 'badge text-bg-secondary'; 
                                    break;
                                }
                            }
                        ?>
                <tr>
                    <td> <span class="badge text-bg-light"> <?= htmlspecialchars($key) ?><span></td>
                    <td> <span class="<?= $rowClass ?>"> <?= htmlspecialchars($value) ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="2">No se recibieron datos</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>