<?php
$date = date('Y-m-d'); // Definir la fecha actual una vez al inicio del script
echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bot de Señales Crypto IDX</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #000; /* Fondo de la página en negro */
            color: #fff; /* Texto en blanco para mejor visibilidad */
        }

        .oculto { display: none; }
        .candado { cursor: pointer; }

        /* Estilos para centrar las opciones y hacerlas más grandes y en negrita */
        select {
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            width: 100%;
        }

        option {
            font-weight: bold;
            font-size: 14px;
        }

        /* Reducir la altura de las filas y celdas */
        table {
            width: 30%;
            border-collapse: collapse;
            background-color: #333; /* Fondo de la tabla en un color oscuro */
            color: #fff; /* Texto de la tabla en blanco */
        }

        td, th {
            padding: 4px 8px;
            text-align: center;
        }

        th {
            font-size: 15px;
            font-weight: bold;
        }

        /* Evitar salto de línea en la columna "Señal" */
        .no-wrap {
            white-space: nowrap; /* Mantener el texto en una sola línea */
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #222;
            color: #fff;
            text-align: left;
            padding: 10px 0;
            font-size: 16px;
        }

        #reloj {
            font-weight: bold;
            font-size: 20px;

        }
    </style>
</head>
<body>
    <h2>Señales del día: $date</h2>
    <button id="toggleAll" onclick="toggleAll()">Mostrar todo</button>
    <button id="toggleView" onclick="toggleView()">Ver solo Hora y Señal</button>
    <table>
        <tr>
            <th>Hora</th>
            <th class="no-wrap">Señal</th>
            <th class="extra">Estado</th>
            <th class="extra">Resultado</th>
        </tr>
HTML;

// Función para generar una secuencia de señales cada 5 minutos
function generar_señales() {
    $señales = array();
    $hora_actual = strtotime('06:00'); // Hora de inicio
    $hora_limite = strtotime('23:55'); // Hora de fin

    while ($hora_actual <= $hora_limite) {
        $señal = array(
            'hora' => date('H:i', $hora_actual),
            'tipo' => rand(0, 1) ? 'Compra' : 'Venta', // Genera aleatoriamente 'Compra' o 'Venta'
            'estado' => true, // Estado inicial como visible
            'resultado_lista' => '-' // Resultado inicial
        );
        $señales[] = $señal;
        $hora_actual = strtotime('+5 minutes', $hora_actual); // Añade 5 minutos a la hora actual
    }

    return $señales;
}

// Función para mostrar las señales en formato de tabla
function mostrar_señales($señales) {
    $html = "";
    foreach ($señales as $index => $señal) {
        $emoji = ($señal['tipo'] === 'Compra') ? '🟢' : '🔴';
        $estado_clase = isset($señal['estado']) && $señal['estado'] ? '' : 'oculto';
        $candado = $señal['estado'] ? '🔓' : '🔒';

        $html .= "<tr id='fila_{$index}'>";
        $html .= "<td>{$señal['hora']}</td>";
        $html .= "<td class='no-wrap {$estado_clase}'>{$emoji}</td>";
        $html .= "<td class='extra'><span class='candado' onclick='toggleSeñal({$index})'>{$candado}</span></td>";
        $html .= "<td class='extra'>
                    <select onchange='guardarResultado({$index})' id='resultado_lista_{$index}'>
                        <option value='-' " . ($señal['resultado_lista'] === '-' ? 'selected' : '') . ">-</option>
                        <option value='SÍ' " . ($señal['resultado_lista'] === 'SÍ' ? 'selected' : '') . ">SÍ ✅</option>
                        <option value='NO' " . ($señal['resultado_lista'] === 'NO' ? 'selected' : '') . ">NO ❌</option>
                    </select>
                  </td>";
        $html .= "</tr>";
    }
    return $html;
}

// Función para guardar las señales en un archivo JSON
function guardar_señales($señales) {
    $contenido = json_encode($señales);
    file_put_contents('señales_crypto_idx_' . date('Y-m-d') . '.json', $contenido);
}

// Función para obtener las señales desde el archivo JSON
function obtener_señales() {
    $archivo = 'señales_crypto_idx_' . date('Y-m-d') . '.json';
    if (file_exists($archivo)) {
        $señales = json_decode(file_get_contents($archivo), true);
        foreach ($señales as &$señal) {
            if (!isset($señal['estado'])) {
                $señal['estado'] = true;
            }
            if (!isset($señal['resultado_lista'])) {
                $señal['resultado_lista'] = '-';
            }
        }
    } else {
        $señales = generar_señales();
        guardar_señales($señales);
    }
    return $señales;
}

$señales = obtener_señales();
$html = mostrar_señales($señales);
echo $html;

echo <<<HTML
    </table>
    <footer>
    Máx. M1 - Hora: <span id="reloj"></span>
    <br>
     Únete a nuestra comunidad VIP - es gratis.</footer>
    <script>
        function toggleSeñal(index) {
            var fila = document.getElementById('fila_' + index);
            var columna = fila.getElementsByTagName('td')[1];
            var candado = fila.getElementsByTagName('td')[2].getElementsByTagName('span')[0];

            if (columna.classList.contains('oculto')) {
                columna.classList.remove('oculto');
                candado.innerText = '🔓';
            } else {
                columna.classList.add('oculto');
                candado.innerText = '🔒';
            }
        }

        function toggleAll() {
            var boton = document.getElementById('toggleAll');
            var filas = document.querySelectorAll('tr');
            var mostrar = boton.innerText === "Mostrar todo";

            filas.forEach((fila, index) => {
                if (index > 0) {
                    var columna = fila.getElementsByTagName('td')[1];
                    var candado = fila.getElementsByTagName('td')[2].getElementsByTagName('span')[0];
                    if (mostrar) {
                        columna.classList.remove('oculto');
                        candado.innerText = '🔓';
                    } else {
                        columna.classList.add('oculto');
                        candado.innerText = '🔒';
                    }
                }
            });

            boton.innerText = mostrar ? "Ocultar todo" : "Mostrar todo";
        }

        function toggleView() {
            var boton = document.getElementById('toggleView');
            var mostrarExtra = boton.innerText === "Ver solo Hora y Señal";
            var columnasExtra = document.querySelectorAll('.extra');

            columnasExtra.forEach(columna => {
                columna.style.display = mostrarExtra ? 'none' : '';
            });

            boton.innerText = mostrarExtra ? "Ver todo" : "Ver solo Hora y Señal";
        }

        function guardarResultado(index) {
            var resultado = document.getElementById('resultado_lista_' + index).value;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "guardar_resultado.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("index=" + index + "&resultado=" + resultado);
        }

        function actualizarReloj() {
            const reloj = document.getElementById('reloj');
            const ahora = new Date();
            const horas = ahora.getHours().toString().padStart(2, '0');
            const minutos = ahora.getMinutes().toString().padStart(2, '0');
            const segundos = ahora.getSeconds().toString().padStart(2, '0');
            reloj.innerText = horas + ':' + minutos + ':' + segundos;
        }

        setInterval(actualizarReloj, 1000);
        actualizarReloj();
    </script>
</body>
</html>
HTML;
?>
