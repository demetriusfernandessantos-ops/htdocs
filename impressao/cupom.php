<?php
$dados = [
    'dataAtual' => $_POST['dataAtual'] ?? '',
    'valor_troco' => $_POST['valor_troco'] ?? 'R$ 0,00',
    'valor_pago' => $_POST['valor_pago'] ?? '',
    'forma_pag' => $_POST['forma_pag'] ?? '',
    'valor_venda' => $_POST['valor_venda'] ?? '',
    'nu_parcelas' => $_POST['nu_parcelas'] ?? '',
    'produtos' => json_decode($_POST['produtos'] ?? '[]', true)
];

$valor_pagamento_exibicao = $dados['valor_venda']; 

if ($dados['forma_pag'] === 'Cartão' && !empty($dados['nu_parcelas'])) {
    $valorNumerico = floatval(str_replace(['R$', '.', ','], ['', '', '.'], $dados['valor_venda']));
    $parcelas = intval($dados['nu_parcelas']);
    $valorParcela = $parcelas > 0 ? $valorNumerico / $parcelas : $valorNumerico;
    $valorParcela = number_format($valorParcela, 2, ',', '.');

    // Ex: "6x de R$ 20,00"
    $valor_pagamento_exibicao = "{$parcelas}x de R$ {$valorParcela}";
}

list($data, $hora) = explode(',', $dados['dataAtual']);
$total_itens = count($dados['produtos']);

$html = '<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Cupom Fiscal - Donna Rafinha</title>
<style>
    html, body {
        width: 183px;
        height: auto;
        font-family: monospace;
        font-weight: bold;
        font-size: 10px;
        margin: 0 auto;
        padding: 0;
        background: #fff;
    }
    body {
        display: flex;
        flex-direction: column;
        height: auto !important;
    }
    .cabecalho {
        text-align: center;
        margin-bottom: 5px;
    }
    .cabecalho img {
        display: block;
        margin: 0 auto;
    }
    .data {
        text-align: left;
        margin-bottom: 4px;
    }
    h2 {
        font-size: 18px;
        margin: 0;
    }
    .forma {
        margin: 5px 0 10px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
    }
    td {
        padding: 2px 0;
    }
    td.nome {
        max-width: 200px;
        word-wrap: break-word;
        white-space: normal;
        text-align: left;
    }
    td.valor {
        display: flex;
        justify-content: space-between;
    }
    .linha {
        border-bottom: 2px dashed #000;
        margin: 5px 0;
    }
    .total {
        margin-top: 1px;
        padding-top: 2px;
        display: flex;
        justify-content: space-between;
    }
</style>
</head>
<body>
    <div class="cabecalho">
        <img src="../imagens/logo_preta.png" width=100 height=100 alt="Logo">
        <h2>Donna Rafinha</h2>
    </div>
    <div class="linha"></div>
    <div>Data: ' . htmlspecialchars($data) . '</div>
    <div>Hora: ' . htmlspecialchars($hora) . '</div>
    <div class="linha"></div>

    <table>
        <tbody>';

foreach ($dados['produtos'] as $p) {
    $nome = substr($p['nome'], 0, 22);
    $valor = number_format($p['valor'], 2, ',', '.');
    $html .= '<tr>
                <td class="nome">' . htmlspecialchars($nome) . '</td>
              </tr>
              <tr>
                <td class="valor"><span>01 Un x</span><span>' . $valor . '</span><span>' . $valor . '</span></td>
              </tr>
              ';
}

$html .= '
        </tbody>
    </table>
    <div class="linha"></div>
    <div class="total"><span>QTD. total de itens: </span><span>' . $total_itens . '</span></div>
    <div class="total"><span>TOTAL: </span><span>' . htmlspecialchars($dados['valor_venda']) . '</span></div>
    <div class="total"><span>' . htmlspecialchars($dados['forma_pag']) . ': </span><span>' . htmlspecialchars($valor_pagamento_exibicao) . '</span></div>
    <div  class="total"><span>Troco: </span><span>' . htmlspecialchars($dados['valor_troco']) . '</span></div>
    <div class="linha"></div>
    <div class="cabecalho">Obrigado pela preferência!</div>
</body>
</html>';

file_put_contents(__DIR__ . '/cupom.html', $html);
echo 'ok';
