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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(false, 'Use o método POST.');

$idPergunta = (int) ($_POST['idpergunta'] ?? 0);
$resposta   = limpar($_POST['resposta'] ?? '');
$certa      = (($_POST['certa'] ?? '2') == '1') ? 1 : 2;

if ($idPergunta < 1 || $resposta === '') responder(false, 'Informe o ID da pergunta e a resposta.');

$existe = false;
foreach (array_slice(lerLinhas('perguntas.txt'), 1) as $l) {
    if ((int) explode(';', $l)[0] === $idPergunta) $existe = true;
}
if (!$existe) responder(false, 'Pergunta não encontrada.');

// se a nova resposta for a correta, as outras da mesma pergunta viram "errada"
$novas = ['id;idpergunta;resposta;certa'];
foreach (array_slice(lerLinhas('respostas.txt'), 1) as $l) {
    $p = explode(';', $l);
    if ($certa == 1 && count($p) >= 4 && (int) $p[1] === $idPergunta) $p[3] = 2;
    $novas[] = implode(';', $p);
}
$id = maiorId('respostas.txt') + 1;
$novas[] = $id . ';' . $idPergunta . ';' . $resposta . ';' . $certa;

file_put_contents('respostas.txt', implode("\n", $novas) . "\n", LOCK_EX);

responder(true, "Resposta cadastrada com o id $id.", ['id' => $id]);
