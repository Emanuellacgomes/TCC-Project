<?php
// Arquivo: retorno_processar.php
// Recebe o redirecionamento do Mercado Pago e atualiza o status do pedido no banco.

require_once "conexao.php";

// Parâmetros enviados pelo Mercado Pago na URL:
$status_mp = isset($_GET['collection_status']) ? $_GET['collection_status'] : null;
$pedido_id = isset($_GET['external_reference']) ? $_GET['external_reference'] : null;
$id_transacao_mp = isset($_GET['collection_id']) ? $_GET['collection_id'] : null;

$status_final_db = 'desconhecido';
$redirecionamento_url = 'loja.php'; // Padrão se algo der errado

if (!empty($pedido_id) && !empty($status_mp)) {
    
    // 1. Determina o status no banco e a URL de redirecionamento
    switch ($status_mp) {
        case 'approved':
            $status_final_db = 'aprovado';
            $redirecionamento_url = 'retorno_sucesso.php'; // Sua página de aprovação
            break;
        case 'pending':
        case 'in_process':
            $status_final_db = 'pendente';
            $redirecionamento_url = 'retorno_pendente.php'; // Sua página de pendente
            break;
        case 'rejected':
        case 'cancelled':
            $status_final_db = 'cancelado';
            $redirecionamento_url = 'retorno_erro.php'; // Sua página de erro
            break;
        default:
             $redirecionamento_url = 'retorno_erro.php'; 
             break;
    }

    // 2. Atualiza o banco de dados (se o status não for desconhecido)
    if ($status_final_db !== 'desconhecido') {
        
        $stmt = mysqli_prepare($conn, "UPDATE pedidos SET status_pagamento = ?, id_transacao_mp = ? WHERE id_pedido = ?");
        
        if ($stmt) {
            $pedido_id_int = intval($pedido_id);
            
            // Atualiza o status e a transação
            mysqli_stmt_bind_param($stmt, "ssi", $status_final_db, $id_transacao_mp, $pedido_id_int);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

// 3. Redireciona o cliente para a tela final
header("Location: {$redirecionamento_url}");
exit();
?>