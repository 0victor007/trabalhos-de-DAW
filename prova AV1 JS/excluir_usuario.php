<?php
header('Content-Type: application/json; charset=utf-8');

function responder($sucesso, $mensagem, $extra = []) {
    echo json_encode(array_merge(['sucesso' => $sucesso, 'mensagem' => $mensagem], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}
function limpar($texto) {
    return trim(str_replace([';', "\r", "\n"], ' ', $texto));
}
function lerLinhas($arquivo) {
    if (!file_exists($arquivo)) return [];
    return array_values(array_filter(array_map('trim', file($arquivo)), 'strlen'));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(false, 'Use o método POST.');

$cpf = limpar($_POST['cpf'] ?? '');
if ($cpf === '') responder(false, 'Informe o CPF.');

$achou = false;
$novas = [];
foreach (lerLinhas('usuarios.txt') as $l) {
    $p = explode(';', $l);
    if (($p[1] ?? '') === $cpf) {
        $achou = true;
    } else {
        $novas[] = $l;
    }
}
if (!$achou) responder(false, 'Usuário não encontrado.');

file_put_contents('usuarios.txt', $novas ? implode("\n", $novas) . "\n" : '', LOCK_EX);

responder(true, 'Usuário excluído com sucesso.');
