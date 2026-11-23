<?php
header('Content-Type: application/json; charset=utf-8');

function responder($sucesso, $mensagem, $extra = []) {
    echo json_encode(array_merge(['sucesso' => $sucesso, 'mensagem' => $mensagem], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}
function lerLinhas($arquivo) {
    if (!file_exists($arquivo)) return [];
    return array_values(array_filter(array_map('trim', file($arquivo)), 'strlen'));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(false, 'Use o método POST.');

$id = (int) ($_POST['id'] ?? 0);

$pergunta = null;
foreach (array_slice(lerLinhas('perguntas.txt'), 1) as $l) {
    $p = explode(';', $l, 2);
    if ((int) $p[0] === $id) {
        $pergunta = $p[1] ?? '';
        break;
    }
}
if ($pergunta === null) responder(false, 'Pergunta não encontrada.');

$respostas = [];
foreach (array_slice(lerLinhas('respostas.txt'), 1) as $l) {
    $p = explode(';', $l);
    if (count($p) >= 4 && (int) $p[1] === $id) {
        $respostas[] = ['id' => (int) $p[0], 'resposta' => $p[2], 'certa' => $p[3] == '1'];
    }
}

responder(true, 'Pergunta encontrada.', ['id' => $id, 'pergunta' => $pergunta, 'respostas' => $respostas]);
