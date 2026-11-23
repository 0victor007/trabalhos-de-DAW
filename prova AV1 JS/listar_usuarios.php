<?php
header('Content-Type: application/json; charset=utf-8');

function lerLinhas($arquivo) {
    if (!file_exists($arquivo)) return [];
    return array_values(array_filter(array_map('trim', file($arquivo)), 'strlen'));
}

$usuarios = [];
foreach (lerLinhas('usuarios.txt') as $l) {
    $p = explode(';', $l);
    $usuarios[] = ['nome' => $p[0], 'cpf' => $p[1] ?? ''];
}

echo json_encode(['sucesso' => true, 'mensagem' => 'OK', 'usuarios' => $usuarios], JSON_UNESCAPED_UNICODE);
