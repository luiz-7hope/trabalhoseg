<?php

require 'conexao.php'; // Inclui a função getConnection()
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Permite requisições de diferentes origens (CORS) - Ajustar para produção
// header('Access-Control-Allow-Origin: *'); 
// header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

try {
    $pdo = getConnection();

    switch ($method) {
        // --- READ: Busca todos os eventos ou um evento específico ---
        case 'GET':
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $stmt = $pdo->prepare("SELECT * FROM calendario_escolar.eventos WHERE id = :id");
                $stmt->execute(['id' => $id]);
                $eventos = $stmt->fetch();
                if ($eventos) {
                    echo json_encode(['success' => true, 'data' => $eventos]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Evento não encontrado']);
                }
            } else {
                // Seleciona todos os campos definidos na tabela eventos:
                $stmt = $pdo->query("SELECT id, titulo, descricao, data_evento AS data, tipo FROM calendario_escolar.eventos");
                $eventos = $stmt->fetchAll();
                
                // Mapeia o campo 'tipo' para a cor (Exemplo)
                $eventosComCor = array_map(function($ev){
                    // Se o seu banco tiver uma coluna 'cor', use-a.
                    // Senão, adicione uma lógica de mapeamento para simular a cor:
                    if(!isset($ev['cor'])) {
                        $ev['cor'] = '#28a745'; // Cor padrão (Verde)
                        if($ev['tipo'] === 'feriado') $ev['cor'] = '#c82333'; // Vermelho Escuro
                        if($ev['tipo'] === 'recesso') $ev['cor'] = '#ffc107'; // Laranja Suave
                    }
                    unset($ev['tipo']); // Remove o campo 'tipo' para compatibilidade com o JS
                    return $ev;
                }, $eventos);

                echo json_encode(['success' => true, 'data' => $eventosComCor]);
            }
            break;

        // --- CREATE: Insere um novo evento ---
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['titulo'], $data['data'], $data['descricao'], $data['tipo'])) {
                 http_response_code(400);
                 echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
                 return;
            }

            $sql = "INSERT INTO calendario_escolar.eventos (titulo, descricao, data_evento, tipo) VALUES (:titulo, :descricao, :data, :tipo)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'titulo' => $data['titulo'],
                'descricao' => $data['descricao'],
                'data' => $data['data'],
                'tipo' => $data['tipo'] 
            ]);

            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;

        // --- UPDATE: Atualiza um evento existente ---
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['id'], $data['titulo'], $data['data'], $data['descricao'], $data['tipo'])) {
                 http_response_code(400);
                 echo json_encode(['success' => false, 'message' => 'Dados incompletos para atualização']);
                 return;
            }

            $sql = "UPDATE calendario_escolar.eventos SET titulo = :titulo, descricao = :descricao, data_evento = :data, tipo = :tipo WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'id' => $data['id'],
                'titulo' => $data['titulo'],
                'descricao' => $data['descricao'],
                'data' => $data['data'],
                'tipo' => $data['tipo'] 
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Evento atualizado']);
            break;

        // --- DELETE: Exclui um evento ---
        case 'DELETE':
            // Recebe o ID via query string para exclusão
            $id = $_GET['id'] ?? null;
            if (!$id) {
                 http_response_code(400);
                 echo json_encode(['success' => false, 'message' => 'ID do evento é obrigatório para exclusão']);
                 return;
            }

            $sql = "DELETE FROM calendario_escolar.eventos WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            echo json_encode(['success' => true, 'message' => 'Evento excluído']);
            break;

        // --- OPTIONS: Necessário para pré-voo CORS ---
        case 'OPTIONS':
            http_response_code(200);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            break;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro no Banco de Dados: ' . $e->getMessage()]);
}
?>