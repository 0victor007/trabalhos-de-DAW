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
function anexar($arquivo, $texto) {
    $prefixo = '';
    if (file_exists($arquivo) && filesize($arquivo) > 0) {
        $f = fopen($arquivo, 'r');
        fseek($f, -1, SEEK_END);
        if (fgetc($f) !== "\n") $prefixo = "\n";
        fclose($f);
    }
    file_put_contents($arquivo, $prefixo . $texto, FILE_APPEND | LOCK_EX);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(false, 'Use o método POST.');

$nome = limpar($_POST['nome'] ?? '');
$cpf  = limpar($_POST['cpf'] ?? '');
if ($nome === '' || $cpf === '') responder(false, 'Informe o nome e o CPF.');

foreach (lerLinhas('usuarios.txt') as $l) {
    $p = explode(';', $l);
    if (($p[1] ?? '') === $cpf) responder(false, 'Já existe um usuário com esse CPF.');
}

anexar('usuarios.txt', $nome . ';' . $cpf . "\n");

responder(true, 'Usuário criado com sucesso.');
