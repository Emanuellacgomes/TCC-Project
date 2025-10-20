<?php
// notification.php
// ⚠️ Troque este token APENAS se tiver certeza que seu token mudou.
// O token atual é: APP_USR-5880562592274061-100112-8ecca3eaff0ff2aff31c493116724eb8-2725312304
$access_token = "APP_USR-5880562592274061-100112-8ecca3eaff0ff2aff31c493116724eb8-2725312304"; 

// Inclui a conexão com o banco de dados
require_once "conexao.php";

// Apenas para debug, verifica se a conexão funciona no topo
if (!$conn) {
    file_put_contents("notificacoes.log", date("Y-m-d H:i:s") . " - ERRO CONEXÃO DB: " . mysqli_connect_error() . "\n", FILE_APPEND);
    http_response_code(500);
    die("DB_ERROR");
}

// 1. Receber os dados da notificação
$body = @file_get_contents("php://input");
$data = json_decode($body, true);

// 2. Verificar se a notificação é válida e se é do tipo "payment"
if ($data && isset($data["type"]) && $data["type"] === "payment" && isset($data["data"]["id"])) {
    
    $payment_id = $data["data"]["id"];
    
    // 3. Chamar a API do Mercado Pago para obter o status real do pagamento
    $url = "https://api.mercadopago.com/v1/payments/" . $payment_id;
    
    // *** NOVO MÉTODO: USANDO file_get_contents com stream context (MAIS ROBUSTO LOCALMENTE) ***
    $context = stream_context_create([
        'ssl' => [
            // CORREÇÃO CRÍTICA para problemas de SSL local (HTTP 0)
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
        'http' => [
            'method' => 'GET',
            'header' => 'Authorization: Bearer ' . $access_token
        ]
    ]);

    $response = @file_get_contents($url, false, $context);
    $payment_info = json_decode($response, true);
    
    // O status HTTP não é fácil de obter aqui, mas podemos testar o conteúdo
    if ($response === false || !isset($payment_info["status"])) {
        // Se falhou ou não retornou o status, é um erro.
        file_put_contents("notificacoes.log", date("Y-m-d H:i:s") . " - ERRO STREAM CONTEXT: Falha ao obter dados MP. Resposta: " . $response . "\n", FILE_APPEND);
    } else {
        
        $status = $payment_info["status"];
        $pedido_id = $payment_info["external_reference"];
        $status_final = 'pendente'; 
        
        if ($status === 'approved') {
            $status_final = 'pago';
        } elseif ($status === 'in_process') {
            $status_final = 'em_processamento';
        } elseif ($status === 'rejected' || $status === 'cancelled') {
            $status_final = 'cancelado';
        }

        // 5. Atualizar o banco de dados
        if (!empty($pedido_id)) {
            $stmt = mysqli_prepare($conn, "UPDATE pedidos SET status_pagamento = ?, id_transacao_mp = ? WHERE id_pedido = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssi", $status_final, $payment_id, $pedido_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                
                // Log de sucesso
                file_put_contents("notificacoes.log", date("Y-m-d H:i:s") . " - PEDIDO #" . $pedido_id . " ATUALIZADO PARA: " . $status_final . "\n", FILE_APPEND);
            } else {
                file_put_contents("notificacoes.log", date("Y-m-d H:i:s") . " - ERRO PREPARE DB: " . mysqli_error($conn) . "\n", FILE_APPEND);
            }
        }
    }
} else {
    file_put_contents("notificacoes.log", date("Y-m-d H:i:s") . " - NOTIFICAÇÃO IGNORADA/INVÁLIDA: " . $body . "\n", FILE_APPEND);
}

// O Mercado Pago exige a resposta HTTP 200 (OK)
http_response_code(200);
echo "OK";
?>