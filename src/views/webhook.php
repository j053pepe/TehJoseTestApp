<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jose - RedPay</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">
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
        <div>
            <?php if (!empty($methodForView)): ?>
            <p><strong>Método:</strong> <?= htmlspecialchars($methodForView) ?>
            </p>
            <?php endif; ?>
        </div>
        <?php
function renderDataTable($title, $data) {
    if (empty($data) || !is_array($data)) return;
    echo "<h5>$title</h5>";
    echo '<table class="table table-bordered">';
    echo '<thead class="table-dark"><tr><th>Nombre</th><th>Valor</th></tr></thead><tbody>';
    foreach ($data as $key => $value) {
        $rowClass = 'badge text-bg-secondary';
        if (strtolower($key) === 'status') {
            switch (strtolower($value)) {
                case 'accepted': $rowClass = 'badge text-bg-success'; break;
                case 'pending': $rowClass = 'badge text-bg-info'; break;
                case 'rejected': $rowClass = 'badge text-bg-danger'; break;
            }
        }
        echo '<tr>';
        echo '<td><span class="badge text-bg-light">' . htmlspecialchars($key) . '</span></td>';
        echo '<td><span class="' . $rowClass . '">' . htmlspecialchars($value) . '</span></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}
?>
        <?php if (!empty($apiResultForView)): ?>
        <h5>JSON bruto de API:</h5>
        <pre>
            <?= htmlspecialchars(json_encode($apiResultForView, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?>
        </pre>
        <?php renderDataTable("Tabla de API Result", $apiResultForView); ?>
        <?php endif; ?>        
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    </script>
</body>
</html>