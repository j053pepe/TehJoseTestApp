document.addEventListener('DOMContentLoaded', function () {
    CallApiWithTokenCallback('/api/compra', 'get', '', true, dataResult => {
        if(dataResult.status === 'success') {
            const tblBody = document.getElementById('table-body');
            tblBody.innerHTML = ''; // Limpiar el contenido actual
            dataResult.data.forEach(element => {
                const labelStatus = element.Estatus === 'Pagado' ? `<span class="badge rounded-pill text-bg-success">${element.Estatus}</span>` 
                : `<span class="badge rounded-pill text-bg-danger">${element.Estatus}</span>`;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${element.IdUsuario}</td>
                    <td>${element.Username}</td>
                    <td>${element.Referencia}</td>
                    <td>${ToMoney(element.Monto)}</td>
                    <td>${labelStatus}</td>
                    <td>${element.CreateDate}</td>
                `;
                tblBody.appendChild(row);
            });
        }else {
            Swal.fire({
                title: 'Compra',
                text: dataResult.message,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    });
});