<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Llamar a la API</title>
</head>
<body>
    <h1>Llamar a la API</h1>

    <button onclick="callApi()">Llamar a /api</button>
    <button onclick="callApiMessage('John')">Llamar a /api/message/John</button>
    <button onclick="callApiSaludo('Ana')">Enviar a /api/message/</button>
    <button onclick="callApiWebhook()">Llamar a /api/webhook</button>

    <h2>Respuesta de la API:</h2>
    <pre id="response"></pre>

    <script>
        // Función para llamar al endpoint /api
        async function callApi() {
            const response = await fetch('/api');
            const text = await response.text();
            document.getElementById('response').textContent = text;
        }

        // Función para llamar al endpoint /api/message/{name}
        async function callApiMessage(name) {
            const response = await fetch(`/api/message/${name}`);
            const text = await response.text();
            document.getElementById('response').textContent = text;
        }

        // Función para llamar al endpoint /api/message/saludo/{name}
        async function callApiSaludo(name) {
            const data = {
            "name":name,
            "edad":18
            };
            const response = await fetch(`/api/message`,{
        method: "post",
        body: data ? JSON.stringify(data) : null // Solo incluir el body si hay datos
    });
            const text = await response.text();
            document.getElementById('response').textContent = text;
        }

        async function callApiWebhook(){
            const response = await fetch('/api/webhook');
            const text = await response.text();
            document.getElementById('response').textContent = text;
        }
    </script>
</body>
</html>