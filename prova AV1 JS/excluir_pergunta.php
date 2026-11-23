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

// remove a pergunta
$achou = false;
$novas = [];
foreach (lerLinhas('perguntas.txt') as $i => $l) {
    if ($i > 0 && (int) explode(';', $l, 2)[0] === $id) {
        $achou = true;
    } else {
        $novas[] = $l;
    }
}
if (!$achou) responder(false, 'Pergunta não encontrada.');
file_put_contents('perguntas.txt', implode("\n", $novas) . "\n", LOCK_EX);

// remove as respostas ligadas a ela (coluna idpergunta)
$novasResp = ['id;idpergunta;resposta;certa'];
foreach (array_slice(lerLinhas('respostas.txt'), 1) as $l) {
    $p = explode(';', $l);
    if (count($p) >= 4 && (int) $p[1] === $id) continue;
    $novasResp[] = $l;
}
file_put_contents('respostas.txt', implode("\n", $novasResp) . "\n", LOCK_EX);

responder(true, "Pergunta $id e suas respostas foram excluídas.");
