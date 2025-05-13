<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'sistemas_clientes';
$username = 'root'; // Altere para seu usuário do MySQL
$password = ''; // Altere para sua senha do MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Funções para manipulação de clientes
function getClientes($pdo, $search = '') {
    $sql = "SELECT * FROM clientes";
    if (!empty($search)) {
        $sql .= " WHERE nome LIKE :search OR email LIKE :search";
    }
    $sql .= " ORDER BY data_cadastro DESC";
    
    $stmt = $pdo->prepare($sql);
    if (!empty($search)) {
        $stmt->bindValue(':search', "%$search%");
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addCliente($pdo, $dados) {
    $sql = "INSERT INTO clientes (nome, email, data_nascimento, data_cadastro) 
            VALUES (:nome, :email, :data_nascimento, :data_cadastro)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($dados);
}

function updateCliente($pdo, $dados) {
    $sql = "UPDATE clientes SET 
            nome = :nome, 
            email = :email, 
            data_nascimento = :data_nascimento 
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($dados);
}

function deleteCliente($pdo, $id) {
    $sql = "DELETE FROM clientes WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute(['id' => $id]);
}

function getClienteById($pdo, $id) {
    $sql = "SELECT * FROM clientes WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Processamento de formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add':
                    $dados = [
                        'nome' => $_POST['nome'],
                        'email' => $_POST['email'],
                        'data_nascimento' => !empty($_POST['data_nascimento']) ? $_POST['data_nascimento'] : null,
                        'data_cadastro' => date('Y-m-d H:i:s')
                    ];
                    if (addCliente($pdo, $dados)) {
                        $message = 'Cliente cadastrado com sucesso!';
                        $messageType = 'success';
                    }
                    break;
                    
                case 'edit':
                    $dados = [
                        'id' => $_POST['id'],
                        'nome' => $_POST['nome'],
                        'email' => $_POST['email'],
                        'data_nascimento' => !empty($_POST['data_nascimento']) ? $_POST['data_nascimento'] : null
                    ];
                    if (updateCliente($pdo, $dados)) {
                        $message = 'Cliente atualizado com sucesso!';
                        $messageType = 'success';
                    }
                    break;
                    
                case 'delete':
                    if (deleteCliente($pdo, $_POST['id'])) {
                        $message = 'Cliente excluído com sucesso!';
                        $messageType = 'success';
                    }
                    break;
                    
                case 'clear_all':
                    $pdo->exec("TRUNCATE TABLE clientes");
                    $message = 'Todos os clientes foram removidos!';
                    $messageType = 'warning';
                    break;
            }
        } catch (PDOException $e) {
            $message = 'Erro: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Obter clientes para exibição
$search = isset($_GET['search']) ? $_GET['search'] : '';
$clientes = getClientes($pdo, $search);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Clientes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <header class="mb-10 text-center">
            <h1 class="text-4xl font-bold text-indigo-700 mb-2">Sistema de Clientes</h1>
            <p class="text-gray-600">Cadastre e visualize seus clientes de forma simples e eficiente</p>
        </header>
        
        <!-- Notificação -->
        <?php if (isset($message)): ?>
            <div class="fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white font-medium flex items-center transition-all fade-in <?php 
                echo $messageType === 'success' ? 'bg-green-500' : 
                     ($messageType === 'error' ? 'bg-red-500' : 
                     ($messageType === 'warning' ? 'bg-yellow-500' : 'bg-indigo-500')); 
            ?>">
                <i class="fas <?php 
                    echo $messageType === 'success' ? 'fa-check-circle' : 
                         ($messageType === 'error' ? 'fa-exclamation-circle' : 
                         ($messageType === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle')); 
                ?> mr-2"></i>
                <?php echo $message; ?>
            </div>
            <script>
                setTimeout(() => {
                    document.querySelector('.fixed.bottom-4.right-4').classList.add('opacity-0');
                    setTimeout(() => document.querySelector('.fixed.bottom-4.right-4').remove(), 300);
                }, 3000);
            </script>
        <?php endif; ?>
        
        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Registration Form -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-indigo-100 p-3 rounded-full mr-4">
                        <i class="fas fa-user-plus text-indigo-600 text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800">Cadastro de Cliente</h2>
                </div>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="add">
                    
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                        <input type="text" id="nome" name="nome" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                               placeholder="Digite o nome completo">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                               placeholder="Digite o e-mail">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="data_nascimento" class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
                            <input type="date" id="data_nascimento" name="data_nascimento" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        </div>
                        
                        <div>
                            <label for="data_cadastro" class="block text-sm font-medium text-gray-700 mb-1">Data de Cadastro</label>
                            <input type="datetime-local" id="data_cadastro" name="data_cadastro" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                   value="<?php echo date('Y-m-d\TH:i'); ?>" disabled>
                        </div>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-all flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> Cadastrar Cliente
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Client List -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800">Clientes Cadastrados</h2>
                </div>
                
                <div class="mb-4 flex justify-between items-center">
                    <form method="GET" class="relative w-full max-w-xs">
                        <input type="text" name="search" placeholder="Buscar cliente..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                               value="<?php echo htmlspecialchars($search); ?>">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </form>
                    <a href="?" class="ml-2 p-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all">
                        <i class="fas fa-sync-alt text-gray-600"></i>
                    </a>
                </div>
                
                <div id="clientList" class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    <?php if (empty($clientes)): ?>
                        <div id="emptyState" class="text-center py-10">
                            <i class="fas fa-user-slash text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Nenhum cliente cadastrado ainda</p>
                            <p class="text-sm text-gray-400 mt-1">Cadastre seu primeiro cliente usando o formulário ao lado</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <div class="client-card bg-gray-50 p-4 rounded-lg border border-gray-200 card-hover transition-all fade-in">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium text-lg text-gray-800"><?php echo htmlspecialchars($cliente['nome']); ?></h3>
                                        <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($cliente['email']); ?></p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="openEditModal(<?php echo $cliente['id']; ?>)" 
                                                class="edit-btn p-1 text-indigo-600 hover:text-indigo-800 transition-all">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
                                            <button type="submit" onclick="return confirm('Tem certeza que deseja excluir este cliente?')"
                                                    class="p-1 text-red-600 hover:text-red-800 transition-all">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                        Nasc: <?php echo $cliente['data_nascimento'] ? date('d/m/Y', strtotime($cliente['data_nascimento'])) : 'Não informada'; ?>
                                    </span>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                                        Cadastro: <?php echo date('d/m/Y H:i', strtotime($cliente['data_cadastro'])); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4 flex justify-between items-center text-sm text-gray-500">
                    <span id="clientCount"><?php echo count($clientes); ?> cliente(s)</span>
                    <?php if (!empty($clientes)): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="clear_all">
                            <button type="submit" onclick="return confirm('Tem certeza que deseja excluir TODOS os clientes? Esta ação não pode ser desfeita.')" 
                                    class="text-red-500 hover:text-red-700 transition-all flex items-center">
                                <i class="fas fa-trash mr-1"></i> Limpar todos
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Edit -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 w-full max-w-md fade-in">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Editar Cliente</h3>
                <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editForm" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="editId" name="id">
                
                <div>
                    <label for="editNome" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                    <input type="text" id="editNome" name="nome" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                
                <div>
                    <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" id="editEmail" name="email" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                
                <div>
                    <label for="editDataNascimento" class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
                    <input type="date" id="editDataNascimento" name="data_nascimento" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                
                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funções para o modal de edição
        function openEditModal(id) {
            fetch('get_cliente.php?id=' + id)
                .then(response => response.json())
                .then(cliente => {
                    document.getElementById('editId').value = cliente.id;
                    document.getElementById('editNome').value = cliente.nome;
                    document.getElementById('editEmail').value = cliente.email;
                    document.getElementById('editDataNascimento').value = cliente.data_nascimento || '';
                    document.getElementById('editModal').classList.remove('hidden');
                })
                .catch(error => console.error('Erro:', error));
        }
        
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>