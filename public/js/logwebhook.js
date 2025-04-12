document.addEventListener('DOMContentLoaded', function () {
    CallApiWithTokenCallback('/api/webhook/get', 'get', '', true, data => {
        if (data && data.status === 'success') {
            localStorage.setItem('tblLog', data.data);
            let tableBody = document.querySelector('#tblLog tbody');
            tableBody.innerHTML = '';
            data.data.forEach(webhook => {
                let row = document.createElement('tr');
                row.innerHTML = `
              <td>${webhook.idWebhookLog}</td>
              <td>${webhook.idTransaction}</td>
              <td><button class="btn btn-outline-info btn-sm" type="button" onclick="GetDetalle(${webhook.idWebhookLog})" data-bs-toggle="modal" data-bs-target="#modalJson">Ver</button></td>
              <td>${webhook.typeRequest}</td>
              <td>${webhook.date}</td>
            `;
                tableBody.appendChild(row);
            });
        }
    });
});
function GetDetalle(id) {
    let span = document.getElementById("idSpan");
    span.innerHTML = id;
    CallApiWithTokenCallback(`/api/webhook/getDetail/${id}`, 'get', '', true, data => {
        if (data.status === 'success') {
            LoadDetalle(data.data.logWebhook);
        }
    });
}
function LoadDetalle(jsonData) {
    let tableBody = document.querySelector('#tblDetalle tbody');
    tableBody.innerHTML = ''; // Limpiar la tabla antes de agregar nuevos datos

    for (let key in jsonData) {
        let row = document.createElement('tr');  // Una sola fila para ambos
        if (jsonData.hasOwnProperty(key)) {
            // Crear celda para el nombre de la propiedad con un span
            let th = document.createElement('th');
            th.innerHTML = `<span class="badge text-bg-light">${key}</span>`;
            row.appendChild(th);  // Agregar al mismo row

            // Crear celda para el valor con estilos dinámicos
            let td = document.createElement('td');
            let span = document.createElement('span');

            // Asignar valor
            span.textContent = jsonData[key];

            // Asignar clases dinámicas dependiendo del estado
            if (key.toLowerCase() === "status") {
                switch (jsonData[key].toLowerCase()) {
                    case "accepted":
                        span.className = "badge text-bg-success";
                        break;
                    case "pending":
                        span.className = "badge text-bg-info";
                        break;
                    case "rejected":
                        span.className = "badge text-bg-danger";
                        break;
                    default:
                        span.className = "badge text-bg-secondary";
                        break;
                }
            } else {
                // Para otros valores, usar una clase general
                span.className = "badge text-bg-secondary";
            }

            // Agregar span al td y td a la fila
            td.appendChild(span);
            row.appendChild(td);  // Agregar al mismo row
        }
        // Agregar la fila al cuerpo de la tabla
        tableBody.appendChild(row);
    }
}