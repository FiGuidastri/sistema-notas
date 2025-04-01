<?php
require_once '../conexao.php';

// Consultar as notas agrupadas por data de vencimento
$sql = "SELECT 
            data_vencimento, 
            COUNT(*) AS total_notas 
        FROM notas_fiscais 
        WHERE data_vencimento IS NOT NULL 
        GROUP BY data_vencimento";

$result = $conn->query($sql);

$eventos = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $eventos[] = [
            'title' => $row['total_notas'] . ' Nota(s)',
            'start' => $row['data_vencimento']
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário de Notas</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
        }
        #calendar {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Calendário de Notas Fiscais</h1>
    </div>

    <div id="calendar"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            // Dados dos eventos vindos do PHP
            var eventos = <?= json_encode($eventos) ?>;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth', // Exibição mensal
                locale: 'pt-br', // Idioma em português
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: eventos // Eventos carregados do PHP
            });

            calendar.render();
        });
    </script>
</body>
</html>