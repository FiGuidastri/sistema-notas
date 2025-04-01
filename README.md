README - Sistema de Gestão de Notas Fiscais
📋 Descrição do Projeto
O Sistema de Gestão de Notas Fiscais é uma aplicação web desenvolvida para gerenciar notas fiscais de forma eficiente. Ele permite cadastrar, listar, editar e visualizar notas fiscais em um calendário, além de exibir o status das notas com base em critérios específicos. O sistema organiza as notas por prioridade, destacando aquelas que precisam de atenção, como "Requisição Pendente", "Pedido Pendente" e "Protocolo Pendente".

🛠️ Funcionalidades
Cadastro de Notas Fiscais: Adicione novas notas fiscais com informações detalhadas.
Listagem de Notas: Exibe todas as notas fiscais em uma tabela, ordenadas por prioridade:
Requisição Pendente
Pedido Pendente
Protocolo Pendente
OK
Visualização no Calendário: Mostra as notas fiscais agrupadas por data de vencimento.
Modal de Detalhes: Exibe as notas de uma data específica em um modal ao clicar no evento do calendário.
Edição de Notas: Permite editar informações de notas fiscais existentes.
Status Visual: Cada status é destacado com cores específicas para facilitar a identificação.
🚀 Tecnologias Utilizadas
Frontend:
HTML5
CSS3
JavaScript (com FullCalendar.js)
Backend:
PHP 7+
Banco de Dados:
MySQL
📂 Estrutura do Projeto
⚙️ Configuração do Ambiente
1. Pré-requisitos
PHP 7.4 ou superior
MySQL 5.7 ou superior
Servidor local (ex.: XAMPP, WAMP, Laragon)
2. Configuração do Banco de Dados
Crie um banco de dados no MySQL:

Importe o arquivo SQL com a estrutura e os dados iniciais (se disponível):

Configure o arquivo conexao.php com as credenciais do banco de dados:

3. Configuração do Servidor
Coloque o projeto na pasta htdocs (ou equivalente) do seu servidor local.
Acesse o sistema no navegador:
🖥️ Uso do Sistema
1. Listagem de Notas
Acesse a página listar_notas.php para visualizar todas as notas fiscais.
As notas são exibidas em ordem de prioridade:
Requisição Pendente (vermelho)
Pedido Pendente (amarelo)
Protocolo Pendente (laranja)
OK (verde)
2. Visualização no Calendário
Acesse a página calendario.php para visualizar as notas agrupadas por data de vencimento.
Clique em um evento para abrir um modal com os detalhes das notas daquela data.
3. Cadastro de Notas
Acesse a página formulario.php para cadastrar novas notas fiscais.
4. Edição de Notas
Clique no ícone ✏️ na tabela de listagem para editar uma nota fiscal.
🎨 Estilo Visual
Cores dos Status:

Requisição Pendente: Vermelho (#e74c3c)
Pedido Pendente: Amarelo (#ffce33)
Protocolo Pendente: Laranja (#ff9933)
OK: Verde (#2ecc71)
Design Responsivo:

A tabela e os botões são ajustados para dispositivos móveis.
🐞 Depuração
Exibição de Erros
Certifique-se de que os erros estão habilitados no ambiente de desenvolvimento:

Logs no Console
Verifique os logs no console do navegador para depurar problemas no JavaScript.
Use console.log para verificar os dados recebidos no fetch ou eventos clicados.
📌 Melhorias Futuras
Implementar autenticação de usuários.
Adicionar paginação na listagem de notas fiscais.
Exportar relatórios em PDF ou Excel.
Adicionar filtros avançados na listagem de notas.