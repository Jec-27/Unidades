<?php
if (isset($_GET['api'])) {
    header("Content-Type: application/json; charset=UTF-8");

    $host = "caboose.proxy.rlwy.net";
    $port = 43751;
    $user = "root";
    $pass = "gzGmMybpEUnAsvoNuOeUWzefhUiDDjlN";
    $db   = "railway";

    // Conexión
    $conn = @new mysqli($host, $user, $pass, $db, $port);

    if ($conn->connect_error) {
        echo json_encode(["error" => "❌ Error de conexión", "detalle" => $conn->connect_error]);
        exit;
    }

    // Probar si la tabla existe
    $check = $conn->query("SHOW TABLES LIKE 'unidades'");
    if ($check->num_rows === 0) {
        echo json_encode(["error" => "❌ La tabla 'unidades' no existe en la BD"]);
        exit;
    }

    // Consulta
    $sql = "SELECT id_uni, nom_uni FROM unidades";
    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode(["error" => "❌ Error en la consulta", "detalle" => $conn->error]);
        exit;
    }

    $unidades = [];
    while ($row = $result->fetch_assoc()) {
        $unidades[] = [
            "id" => $row["id_uni"],
            "nombre" => $row["nom_uni"] ?? "(Sin nombre)"
        ];
    }

    echo json_encode($unidades, JSON_UNESCAPED_UNICODE);
    $conn->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Selector de Unidades</title>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url("img/fondo.jpg") no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        .contenedor {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            width: 350px;
        }
        h2 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            text-align: center;
        }
        select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>Seleccionar Unidad</h2>
        <select id="unit" name="unit">
            <option value="">Cargando...</option>
        </select>
    </div>

    <script>
    async function cargarUnidades() {
        try {
            const response = await fetch("index.php?api=1");
            const data = await response.json();
            const select = document.getElementById("unit");

            // limpiar
            select.innerHTML = '<option value="">Seleccionar unidad</option>';

            if (data.error) {
                console.error("⚠️ Error en API:", data);
                select.innerHTML = `<option value="">${data.error}</option>`;
                return;
            }

            if (data.length === 0) {
                select.innerHTML = '<option value="">No hay unidades</option>';
                return;
            }

            data.forEach(u => {
                const option = document.createElement("option");
                option.value = u.id;
                option.textContent = u.nombre;
                select.appendChild(option);
            });
        } catch (err) {
            console.error("⚠️ Error cargando unidades:", err);
            document.getElementById("unit").innerHTML = '<option value="">Error al cargar</option>';
        }
    }

    cargarUnidades();
    </script>
</body>
</html>
