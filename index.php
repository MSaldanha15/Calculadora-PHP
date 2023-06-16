<?php
$jsonPrecos = file_get_contents('precos.json');
$jsonPlanos = file_get_contents('planos.json');

$precos = json_decode($jsonPrecos, true);
$planos = json_decode($jsonPlanos, true);

$exibirCampos = false;
if (isset($_POST['quantidade'])) {
    $quantidade = $_POST['quantidade'];

    if ($quantidade > 0) {
        $exibirCampos = true;
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Calculadora de Preços de Plano de Saúde</title>
    <style>
        *{
            background: #191e28;
            color: white;
        }
        .calculadora{
            max-width: 600px;
            margin: 0 auto;
        }
        p{
            padding-bottom: 20px;
        }
        form{
            font-size: 18px;
        }
        input{
            background-color: #2b333d;
            width:100%;
            padding:0;
            border:0;
        }
        input::placeholder {
            color: white;
        }
        .botao {
            background: #4488ee;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class='calculadora'>
        <h1>Calculadora de Preços de Plano de Saúde</h1>
        <p>Feito por Matheus Saldanha Coelho</p>

        <form method="POST">
            <label for="quantidade">Quantidade de beneficiários:</label>
            <input type="number" name="quantidade" placeholder="Digite aqui a quantidade">

            <?php if ($exibirCampos) : ?>
                <p><strong>Favor inserir novamente a quantidade de beneficiarios</strong></p>
                <label for="plano">Selecione um plano:</label>
                <select name="plano">
                    <?php foreach ($planos as $plano) : ?>
                        <option value="<?php echo $plano['codigo']; ?>">
                            <?php echo $plano['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                </br>
                <?php for ($i = 1; $i <= $quantidade; $i++) : ?>
                    </br>
                    <div>
                        <label for="beneficiario<?php echo $i; ?>">Beneficiário <?php echo $i; ?>:</label>
                        <input type="text"<?php echo $i; ?> placeholder="Digite aqui seu nome" name="beneficiarios[]"><br>
                    </div>
                    </br>
                    <div>
                        <label for="idade<?php echo $i; ?>">Idade: </label>
                        <input type="text"<?php echo $i; ?> placeholder="Digite aqui sua idade" name="idade[]"><br>
                    </div>

                <?php endfor; ?>

                <?php
                $planoSelecionado = null;
                $planoEncontrado = false;
                $planoMaxVidas = null;
                $maxMinimoVidas = 0;
                $valorTotal = 0;

                if (isset($_POST['plano'])) {
                    $planoSelecionado = $_POST['plano'];

                    foreach ($planos as $plano) {
                        if ($plano['codigo'] == $planoSelecionado) {
                            $planoEncontrado = true;
                            $nomePlanoSelecionado = $plano['nome'];
                            $registroPlanoSelecionado = $plano['registro'];
                            break;
                        }
                    }
                }

                if ($planoEncontrado) {
                    foreach ($precos as $preco) {
                        if ($preco['codigo'] == $planoSelecionado && $quantidade >= $preco['minimo_vidas']) {
                            if ($preco['minimo_vidas'] > $maxMinimoVidas) {
                                $maxMinimoVidas = $preco['minimo_vidas'];
                                $planoMaxVidas = $preco;
                            }
                        }
                    }

                    if ($planoMaxVidas != null) {
                        $faixa1 = number_format($planoMaxVidas['faixa1'], 2);
                        $faixa2 = number_format($planoMaxVidas['faixa2'], 2);
                        $faixa3 = number_format($planoMaxVidas['faixa3'], 2);

                        echo "<br>Plano selecionado: $nomePlanoSelecionado (Registro: $registroPlanoSelecionado)<br>";
                        echo "<br> Pessoas de 0 a 17 anos vão para a Faixa 1: R$$faixa1<br>";
                        echo "<br> Pessoas de 18 a 40 anos vão para a Faixa 2: R$$faixa2<br>";
                        echo "<br> Pessoas com mais de 40 anos vão para a Faixa 3: R$$faixa3<br>";
                        echo "<br>";

                        $idades = $_POST['idade'];
                        $faixa = '';
                        foreach ($idades as $idade) {
                            $idade = (int)$idade;
                            if ($idade >= 0 && $idade <= 17) {
                                $faixa = 'faixa1';
                            } elseif ($idade >= 18 && $idade <= 40) {
                                $faixa = 'faixa2';
                            } elseif ($idade > 40) {
                                $faixa = 'faixa3';
                            }
                            echo "Beneficiário com idade $idade está na $faixa: R$" . number_format($planoMaxVidas[$faixa], 2) . "<br>";
                            $valorTotal += $planoMaxVidas[$faixa];
                        }
                        echo "<br>Valor Total: R$" . number_format($valorTotal, 2) . "<br>";
                    }
                }
                ?>
            <?php endif; ?>
            </br>
            <input class="botao" type="submit" value="Enviar">
        </form>
    </div>
</body>

</html>
