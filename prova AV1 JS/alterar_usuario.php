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

$nome = limpar($_POST['nome'] ?? '');
$cpf  = limpar($_POST['cpf'] ?? '');
if ($nome === '' || $cpf === '') responder(false, 'Informe o CPF e o novo nome.');

$achou = false;
$novas = [];
foreach (lerLinhas('usuarios.txt') as $l) {
    $p = explode(';', $l);
    if (($p[1] ?? '') === $cpf) {
        $novas[] = $nome . ';' . $cpf;
        $achou = true;
    } else {
        $novas[] = $l;
    }
}
if (!$achou) responder(false, 'Usuário não encontrado.');

file_put_contents('usuarios.txt', implode("\n", $novas) . "\n", LOCK_EX);

responder(true, 'Usuário alterado com sucesso.');
