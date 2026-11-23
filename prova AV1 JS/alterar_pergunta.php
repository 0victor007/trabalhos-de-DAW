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

$id        = (int) ($_POST['id'] ?? 0);
$pergunta  = limpar($_POST['pergunta'] ?? '');
$respostas = [
    limpar($_POST['r1'] ?? ''),
    limpar($_POST['r2'] ?? ''),
    limpar($_POST['r3'] ?? ''),
    limpar($_POST['r4'] ?? '')
];
$correta = (int) ($_POST['correta'] ?? 0);

if ($id < 1 || $pergunta === '' || in_array('', $respostas, true) || $correta < 1 || $correta > 4) {
    responder(false, 'Preencha o ID, a pergunta, as 4 respostas e marque a correta.');
}

// 1) altera o texto da pergunta
$achou = false;
$novas = [];
foreach (lerLinhas('perguntas.txt') as $i => $l) {
    if ($i > 0 && (int) explode(';', $l, 2)[0] === $id) {
        $novas[] = $id . ';' . $pergunta;
        $achou = true;
    } else {
        $novas[] = $l;
    }
}
if (!$achou) responder(false, 'Pergunta não encontrada.');
file_put_contents('perguntas.txt', implode("\n", $novas) . "\n", LOCK_EX);

// 2) troca as respostas dessa pergunta pelas novas
$novasResp = ['id;idpergunta;resposta;certa'];
$maior = 0;
foreach (array_slice(lerLinhas('respostas.txt'), 1) as $l) {
    $p = explode(';', $l);
    if (count($p) >= 4 && (int) $p[1] === $id) continue;
    $novasResp[] = $l;
    $maior = max($maior, (int) $p[0]);
}
foreach ($respostas as $i => $resp) {
    $certa = ($i + 1 == $correta) ? 1 : 2;
    $novasResp[] = ($maior + $i + 1) . ';' . $id . ';' . $resp . ';' . $certa;
}
file_put_contents('respostas.txt', implode("\n", $novasResp) . "\n", LOCK_EX);

responder(true, "Pergunta $id alterada com sucesso.");
