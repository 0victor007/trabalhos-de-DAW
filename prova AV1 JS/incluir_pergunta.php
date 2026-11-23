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

$pergunta  = limpar($_POST['pergunta'] ?? '');
$respostas = [
    limpar($_POST['r1'] ?? ''),
    limpar($_POST['r2'] ?? ''),
    limpar($_POST['r3'] ?? ''),
    limpar($_POST['r4'] ?? '')
];
$correta = (int) ($_POST['correta'] ?? 0);

if ($pergunta === '' || in_array('', $respostas, true) || $correta < 1 || $correta > 4) {
    responder(false, 'Preencha a pergunta, as 4 respostas e marque a correta.');
}

if (!file_exists('perguntas.txt')) file_put_contents('perguntas.txt', "id;pergunta\n");
if (!file_exists('respostas.txt')) file_put_contents('respostas.txt', "id;idpergunta;resposta;certa\n");

$idPergunta = maiorId('perguntas.txt') + 1;
$idResposta = maiorId('respostas.txt') + 1;

anexar('perguntas.txt', $idPergunta . ';' . $pergunta . "\n");

$texto = '';
foreach ($respostas as $i => $resp) {
    $certa = ($i + 1 == $correta) ? 1 : 2;
    $texto .= ($idResposta + $i) . ';' . $idPergunta . ';' . $resp . ';' . $certa . "\n";
}
anexar('respostas.txt', $texto);

responder(true, "Pergunta cadastrada com o id $idPergunta.", ['id' => $idPergunta]);
