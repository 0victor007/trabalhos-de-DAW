<?php
header('Content-Type: application/json; charset=utf-8');

$resposta = ['sucesso' => false, 'resultado' => null, 'mensagem' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $resposta['mensagem'] = 'Método não permitido.';
    echo json_encode($resposta);
    exit;
}

$num1 = $_POST['num1'] ?? '';
$num2 = $_POST['num2'] ?? '';
$op   = $_POST['operacao'] ?? '';

if (!is_numeric($num1) || !is_numeric($num2)) {
    http_response_code(400);
    $resposta['mensagem'] = 'Informe números válidos.';
    echo json_encode($resposta);
    exit;
}

$num1 = (float) $num1;
$num2 = (float) $num2;

switch ($op) {
    case '+':
        $resposta['resultado'] = $num1 + $num2;
        $resposta['sucesso'] = true;
        break;

    case '-':
        $resposta['resultado'] = $num1 - $num2;
        $resposta['sucesso'] = true;
        break;

    case '*':
        $resposta['resultado'] = $num1 * $num2;
        $resposta['sucesso'] = true;
        break;

    case '/':
        if ($num2 != 0) {
            $resposta['resultado'] = $num1 / $num2;
            $resposta['sucesso'] = true;
        } else {
            $resposta['mensagem'] = 'Não é possível dividir por zero.';
        }
        break;

    default:
        $resposta['mensagem'] = 'Operação inválida.';
        break;
}

echo json_encode($resposta);
