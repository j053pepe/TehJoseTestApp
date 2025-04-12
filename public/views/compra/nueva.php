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
                <h4>Crear un pago<h4>
            </div>
            <div class="card-body">
                <form id="frmPagar">
                    <h5 class="card-title">Pago</h5>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label for="ReferenceNumber" class="form-label">Referencia de pago</label>
                            <input type="text" class="form-control" name="ReferenceNumber" id="ReferenceNumber"
                                required>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label for="Amount" class="form-label">Monto</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" name="Amount" id="Amount"
                                    required>
                                <span class="input-group-text">MXN</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <p><br /></p>
                        <hr>
                    </div>
                    <h5 class="card-title">Datos de tarjeta</h5>
                    <!-- Nombre y Apellidos -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="FirstName" class="form-label">Nombre(s)</label>
                            <input type="text" class="form-control" id="FirstName" name="FirstName"
                                placeholder="Ingrese su nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="LastName" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" id="LastName" name="LastName"
                                placeholder="Ingrese sus apellidos" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="Email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="Email" id="Email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="PhoneNumber" class="form-label">Teléfono / Celular</label>
                            <input type="phone" class="form-control" name="PhoneNumber" id="PhoneNumber" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Número de tarjeta -->
                        <div class="col-md-4 mb-3">
                            <label for="cardNumber" class="form-label">Número de tarjeta</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="cardNumber" name="cardNumber"
                                    placeholder="1234 5678 9012 3456" required>
                                <span class="input-group-text">
                                    <i class="fab fa-cc-visa"></i>
                                    <i class="fab fa-cc-mastercard"></i>
                                    <i class="fab fa-cc-amex"></i>
                                </span>
                            </div>
                        </div>
                        <!-- Fecha de expiración y CVV -->
                        <div class="col-md-2 mb-3">
                            <label for="cardExpirationMonth" class="form-label">Mes</label>
                            <input type="text" class="form-control" id="cardExpirationMonth" name="cardExpirationMonth"
                                placeholder="MM" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="cardExpirationYear" class="form-label">Año</label>
                            <input type="text" class="form-control" id="cardExpirationYear" name="cardExpirationYear"
                                placeholder="AAAA" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <input type="hidden" id="CardType" name="CardType">
                            <label class="badge rounded-pill text-bg-info" id="CardTypeLabel">--</label>
                        </div>
                    </div>
                    <div class="row">
                        <p><br /></p>
                        <hr>
                        <h5 class="card-title">Dirección</h5>
                    </div>
                    <!-- Dirección -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="Country" class="form-label">País</label>
                            <select name="Country" id="Country" class="form-control">
                                <!-- Opciones de país -->
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="State" class="form-label">Estado</label>
                            <input type="text" class="form-control" name="State" id="State" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="City" class="form-label">Ciudad</label>
                            <input type="text" class="form-control" name="City" id="City" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="PostalCode" class="form-label">Código postal</label>
                            <input type="number" steps="1" class="form-control" name="PostalCode" id="PostalCode"
                                required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="Street" class="form-label">Calle</label>
                            <input type="text" class="form-control" name="Street" id="Street" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="StreetNumber" class="form-label">Número exterior</label>
                            <input type="text" class="form-control" name="StreetNumber" id="StreetNumber" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="StreetNumber2" class="form-label">Número interior</label>
                            <input type="text" class="form-control" name="StreetNumber2" id="StreetNumber2">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="Street2Col" class="form-label">Colonia</label>
                            <input type="text" class="form-control" name="Street2Col" id="Street2Col">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="Street2Del" class="form-label">Delegación/Municipio</label>
                            <input type="text" class="form-control" name="Street2Del" id="Street2Del">
                        </div>
                    </div>
                    <!-- Botón de enviar -->
                    <div class="d-grid">
                        <p><br /></p>
                        <button type="submit" class="btn btn-primary">Pagar</button>
                    </div>
                </form>
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
    <script src="../public/js/compra/nueva.js"></script>
</body>

</html>