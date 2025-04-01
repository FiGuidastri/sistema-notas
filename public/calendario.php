<?php
require_once '../conexao.php';

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
            'start' => $row['data_vencimento'],
            'data_vencimento' => $row['data_vencimento'] // Adiciona a data para uso no AJAX
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
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
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
        .fc-event {
            cursor: pointer;
        }

        /* Estilo do modal */
        .modal {
            display: none; /* Oculta o modal inicialmente */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5); /* Fundo escuro com transparência */
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        .modal-content h2 {
            margin-top: 0;
        }
        .modal-content table {
            width: 100%;
            border-collapse: collapse;
        }
        .modal-content table th, .modal-content table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .modal-content table th {
            background-color: #f4f4f4;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Calendário de Notas Fiscais</h1>
        <a href="listar_notas.php" style="background-color: #0977d8; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 10px; display: inline-block;">🔙 Voltar</a>
    </div>

    <div id="calendar"></div>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Notas do Dia</h2>
            <table>
                <thead>
                    <tr>
                        <th>Número da Nota</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="modal-notas-list">
                    <!-- As notas serão carregadas aqui -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var modal = document.getElementById('modal');
            var modalNotasList = document.getElementById('modal-notas-list');
            var closeModal = document.querySelector('.close');

            // Dados dos eventos vindos do PHP
            var eventos = <?= json_encode($eventos) ?>;

            console.log(eventos); // Verificar os eventos no console

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth', // Exibição mensal
                locale: 'pt-br', // Idioma em português
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: eventos, // Eventos carregados do PHP
                eventClick: function(info) {
                    console.log('Evento clicado:', info.event.extendedProps.data_vencimento);
                    info.jsEvent.preventDefault(); // Evita o comportamento padrão

                    // Faz uma requisição AJAX para buscar as notas da data clicada
                    fetch('buscar_notas.php?data_vencimento=' + info.event.extendedProps.data_vencimento)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erro na requisição: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Dados recebidos:', data); // Verificar os dados no console

                            // Limpa a lista de notas no modal
                            modalNotasList.innerHTML = '';

                            // Adiciona as notas na tabela do modal
                            if (data.length > 0) {
                                data.forEach(nota => {
                                    var row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td>${nota.numero_nota}</td>
                                        <td>${nota.status_nota}</td>
                                    `;
                                    modalNotasList.appendChild(row);
                                });
                            } else {
                                // Exibe mensagem se não houver notas
                                modalNotasList.innerHTML = '<tr><td colspan="2">Nenhuma nota encontrada para esta data.</td></tr>';
                            }

                            // Exibe o modal
                            modal.style.display = 'block';
                        })
                        .catch(error => {
                            console.error('Erro ao buscar notas:', error);
                            alert('Erro ao buscar notas. Verifique o console para mais detalhes.');
                        });
                }
            });

            calendar.render();

            // Fechar o modal ao clicar no botão de fechar
            closeModal.onclick = function() {
                modal.style.display = 'none';
            };

            // Fechar o modal ao clicar fora dele
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            };
        });
    </script>
</body>
</html>