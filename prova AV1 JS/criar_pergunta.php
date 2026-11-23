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
function maiorId($arquivo) {
    $maior = 0;
    foreach (array_slice(lerLinhas($arquivo), 1) as $l) {
        $maior = max($maior, (int) explode(';', $l)[0]);
    }
    return $maior;
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

$pergunta = limpar($_POST['pergunta'] ?? '');
if ($pergunta === '') responder(false, 'Informe a pergunta.');

if (!file_exists('perguntas.txt')) file_put_contents('perguntas.txt', "id;pergunta\n");

$id = maiorId('perguntas.txt') + 1;
anexar('perguntas.txt', $id . ';' . $pergunta . "\n");

responder(true, "Pergunta cadastrada com o id $id.", ['id' => $id]);
