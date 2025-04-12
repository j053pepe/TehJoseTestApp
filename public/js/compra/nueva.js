document.getElementById('frmPagar').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita que el formulario se envíe de forma tradicional

    // Captura todos los datos del formulario
    const formData = new FormData(this);

    // Si necesitas convertirlo a un objeto JSON
    const dataJson = {};
    formData.forEach((value, key) => {
      dataJson[key] = value;
    });

    CallApiWithTokenCallback("/api/pagar", 'post', dataJson, true, data => {
        console.log('Respuesta del servidor:', data);
        if (data.status === 'success') {
            Swal.fire({
              title: 'Pago',
              text: 'El pago se ha realizado correctamente.',
              icon: 'success',
              confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location='/Payment/List';
            });
        }else {
          Swal.fire({
            title: 'Pago',
            text: data.message,
            icon: 'error',
            confirmButtonText: 'Aceptar'
          });
        }
    });
  });

document.addEventListener('DOMContentLoaded', function () {
    CallApiWithTokenCallback("/api/pais", 'get', '', true,result => {
        if (result.status === 'success') {
            const select = document.getElementById('Country');

            // Limpiar todas las opciones (por si acaso)
            select.innerHTML = '';

            // Agregar el primer elemento estático
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.text = 'Seleccione un país';
            select.add(defaultOption);

            // Agregar opciones dinámicas
            result.data.forEach(pais => {
                const option = document.createElement('option');
                option.value = pais.Code; // Valor del option
                option.text = pais.Name;   // Texto visible del option
                if(pais.Code === 'MX') {
                    option.selected = true; // Seleccionar España por defecto
                }
                select.add(option);
            });
        }
    });

document.getElementById('cardNumber').addEventListener('input', function() {
        let cardNumber = this.value.replace(/\D/g, '');
        let cardTypeInput = document.getElementById('CardType');
        let cardTypeLabel = document.getElementById('CardTypeLabel'); // Obtener el label
    
        if (cardNumber.startsWith('4')) {
            cardTypeInput.value = '001';
            cardTypeLabel.textContent = 'Visa'; // Agregar texto al label
        } else if (cardNumber.startsWith('51') || cardNumber.startsWith('52') || cardNumber.startsWith('53') || cardNumber.startsWith('54') || cardNumber.startsWith('55')) {
            cardTypeInput.value = '002';
            cardTypeLabel.textContent = 'Mastercard'; // Agregar texto al label
        } else if (cardNumber.startsWith('34') || cardNumber.startsWith('37')) {
            cardTypeInput.value = '003';
            cardTypeLabel.textContent = 'Amex'; // Agregar texto al label
        } else {
            cardTypeInput.value = '';
            cardTypeLabel.textContent = 'Tipo de tarjeta desconocido'; // Agregar texto al label
        }
    });
});

document.getElementById("ReferenceNumber").addEventListener("change", function() {
    const ReferenceNumber = this.value;
    CallApiWithTokenCallback("/api/pagar/reference/" + ReferenceNumber, 'get', '', true, dataResult => {
        if(dataResult.status === 'error') {
            Swal.fire({
                title: 'Compra',
                text: dataResult.message,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            })
            .then(() => {
                this.value = ''; // Limpiar el campo si hay un error
                this.focus(); // Volver a enfocar el campo
            });
        }
    });
});