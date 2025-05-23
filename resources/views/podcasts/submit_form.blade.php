<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Nuevo Podcast</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], input[type="url"] { width: calc(100% - 22px); padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        .message { padding: 10px; margin-top: 20px; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Enviar Nuevo Podcast</h1>

        <form id="podcastForm">
            <div>
                <label for="title">Título del Podcast:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="url">URL del Podcast:</label>
                <input type="url" id="url" name="url" required>
            </div>
            <button type="submit">Enviar Podcast</button>
        </form>

        <div id="responseMessage" class="message" style="display:none;"></div>
    </div>

    <script>
        document.getElementById('podcastForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Evitar el envío tradicional del formulario

            const title = document.getElementById('title').value;
            const url = document.getElementById('url').value;
            const responseMessageDiv = document.getElementById('responseMessage');

            // Limpiar mensajes anteriores
            responseMessageDiv.style.display = 'none';
            responseMessageDiv.className = 'message'; // Reset class

            fetch('/api/podcasts', { // La URL de tu API
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    // Para rutas API stateless, X-CSRF-TOKEN no suele ser necesario
                    // si se configura correctamente (routes/api.php no usa CSRF por defecto)
                },
                body: JSON.stringify({
                    title: title,
                    url: url
                })
            })
            .then(response => {
                // Primero verificamos si la respuesta fue exitosa en la red
                if (!response.ok) {
                    // Si el servidor responde con un error (4xx, 5xx)
                    // Intentamos leer el cuerpo del error como JSON
                    return response.json().then(err => {
                        throw { status: response.status, data: err };
                    });
                }
                return response.json(); // Si es ok (2xx), parseamos el JSON
            })
            .then(data => {
                console.log('Success:', data);
                responseMessageDiv.textContent = data.message || '¡Podcast enviado exitosamente!';
                responseMessageDiv.classList.add('success');
                responseMessageDiv.style.display = 'block';
                document.getElementById('podcastForm').reset(); // Limpiar el formulario
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMessage = 'Ocurrió un error al enviar el podcast.';
                if (error.status) {
                    // Si tenemos un objeto de error estructurado
                    errorMessage = `Error ${error.status}: `;
                    if (error.data && error.data.message) {
                        errorMessage += error.data.message;
                    }
                    if (error.data && error.data.errors) {
                        // Manejar errores de validación de Laravel
                        let validationErrors = [];
                        for (const key in error.data.errors) {
                            validationErrors.push(error.data.errors[key].join(', '));
                        }
                        errorMessage += ' Detalles: ' + validationErrors.join('; ');
                    }
                } else {
                    // Error de red u otro error de JavaScript
                    errorMessage = error.message || errorMessage;
                }
                responseMessageDiv.textContent = errorMessage;
                responseMessageDiv.classList.add('error');
                responseMessageDiv.style.display = 'block';
            });
        });
    </script>
</body>
</html>