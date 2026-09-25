<?php

$resultado = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $num1 = $_POST["num1"];
    $num2 = $_POST["num2"];
    $op = $_POST["operacao"];

    switch ($op) {

        case "+":
            $resultado = $num1 + $num2;
            break;

        case "-":
            $resultado = $num1 - $num2;
            break;

        case "*":
            $resultado = $num1 * $num2;
            break;

        case "/":
            if ($num2 != 0) {
                $resultado = $num1 / $num2;
            } else {
                $resultado = "Não é possível dividir por zero.";
            }
            break;
        
        default:
            $resultado = "Operação inválida.";
            break;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Calculadora</title>
</head>

<body>

    <h1>Calculadora</h1>

    <form action="" method="POST">

        <label>Primeiro número:</label>
        <input type="number" step="any" name="num1" required>

        <br><br>

        <label>Operação:</label>
        <select name="operacao" required>
            <option value="+">Adição (+)</option>
            <option value="-">Subtração (-)</option>
            <option value="*">Multiplicação (*)</option>
            <option value="/">Divisão (/)</option>
        </select>

        <br><br>

        <label>Segundo número:</label>
        <input type="number" step="any" name="num2" required>

        <br><br>

        <button type="submit">Calcular</button>

    </form>

    <?php if ($resultado !== ""): ?>
        <h2>Resultado: <?php echo $resultado; ?></h2>
    <?php endif; ?>

</body>
</html>
