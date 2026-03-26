<?php
// ============================================================
// ATIVIDADE PRÁTICA: Dashboard de BI - Restaurante
// Disciplina: Programação Orientada a Software Básica
// Professor: Weverson Garcia Medeiros
// UNICEPLAC
// ============================================================

// ============================================================
// PASSO 1: FUNÇÃO DE UTILIDADE - formatarMoeda()
// ============================================================

/**
 * Formata um valor decimal no padrão monetário brasileiro.
 * Exemplo: 1250.00 => "R$ 1.250,00"
 *
 * @param float $valor O valor numérico a ser formatado
 * @return string Valor formatado no padrão BR
 */
function formatarMoeda(float $valor): string {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}


// ============================================================
// PASSO 2: DADOS DOS PRODUTOS (simulando banco de dados)
// ============================================================

$produtos = [
    ['nome' => 'Picanha Grelhada',    'vendas' => 320, 'preco' => 89.90],
    ['nome' => 'Frango à Parmegiana', 'vendas' => 210, 'preco' => 54.90],
    ['nome' => 'Salmão ao Molho',     'vendas' => 95,  'preco' => 119.90],
    ['nome' => 'Macarrão Carbonara',  'vendas' => 180, 'preco' => 42.50],
    ['nome' => 'Salada Caesar',       'vendas' => 45,  'preco' => 28.00],
    ['nome' => 'Risoto de Cogumelos', 'vendas' => 30,  'preco' => 67.00],
];


// ============================================================
// PASSO 3: CALCULAR FATURAMENTO DE CADA PRODUTO E O TOTAL
// ============================================================

// Calcula o faturamento individual de cada produto
foreach ($produtos as $chave => $produto) {
    $produtos[$chave]['faturamento'] = $produto['vendas'] * $produto['preco'];
}

// Soma o faturamento total do restaurante
$faturamentoTotal = array_sum(array_column($produtos, 'faturamento'));


// ============================================================
// PASSO 4: FUNÇÃO DE LÓGICA DE NEGÓCIO - calcularPerformance()
// ============================================================

/**
 * Calcula a fatia percentual de um produto no faturamento total.
 *
 * @param float $faturamentoProduto Faturamento individual do produto
 * @param float $faturamentoTotal   Faturamento total do restaurante
 * @return float Percentual de participação (0 a 100)
 */
function calcularPerformance(float $faturamentoProduto, float $faturamentoTotal): float {
    if ($faturamentoTotal == 0) {
        return 0;
    }
    return ($faturamentoProduto / $faturamentoTotal) * 100;
}


// ============================================================
// PASSO 5: FUNÇÃO DE ALERTA - verificarAlerta()
// ============================================================

/**
 * Verifica se um produto possui baixo desempenho de vendas.
 * Critério: performance abaixo de 10% do faturamento total.
 *
 * @param float $performance Percentual de participação do produto
 * @return bool true se o produto está em alerta, false caso contrário
 */
function verificarAlerta(float $performance): bool {
    return $performance < 10.0;
}


// ============================================================
// PASSO 6: EXIBIÇÃO DO DASHBOARD (HTML + PHP)
// ============================================================
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard BI - Restaurante</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1a1a2e;
            color: #e0e0e0;
            padding: 30px;
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        header h1 {
            font-size: 2rem;
            color: #e2b96f;
            letter-spacing: 1px;
        }

        header p {
            color: #aaa;
            margin-top: 5px;
        }

        .resumo {
            background: #16213e;
            border: 1px solid #0f3460;
            border-radius: 10px;
            padding: 20px 30px;
            margin-bottom: 30px;
            text-align: center;
        }

        .resumo h2 {
            color: #aaa;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .resumo .valor-total {
            font-size: 2.5rem;
            font-weight: bold;
            color: #e2b96f;
            margin-top: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #16213e;
            border-radius: 10px;
            overflow: hidden;
        }

        thead {
            background-color: #0f3460;
        }

        thead th {
            padding: 14px 18px;
            text-align: left;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #c9d1d9;
        }

        tbody tr {
            border-bottom: 1px solid #0f3460;
            transition: background 0.2s;
        }

        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background-color: #0d2035; }

        tbody td {
            padding: 14px 18px;
            font-size: 0.95rem;
            vertical-align: middle;
        }

        /* Barra de performance */
        .barra-container {
            background: #0f3460;
            border-radius: 20px;
            height: 12px;
            width: 180px;
            overflow: hidden;
        }

        .barra-fill {
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(to right, #e2b96f, #f0a500);
        }

        .barra-fill.alerta {
            background: linear-gradient(to right, #e74c3c, #c0392b);
        }

        .percentual { font-weight: bold; }
        .percentual.alerta { color: #e74c3c; }
        .percentual.ok     { color: #2ecc71; }

        /* Badge de alerta */
        .badge-alerta {
            display: inline-block;
            background: #e74c3c;
            color: #fff;
            font-size: 0.72rem;
            font-weight: bold;
            padding: 3px 9px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            animation: piscar 1.2s infinite;
        }

        .badge-ok {
            display: inline-block;
            background: #27ae60;
            color: #fff;
            font-size: 0.72rem;
            font-weight: bold;
            padding: 3px 9px;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        @keyframes piscar {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.4; }
        }

        footer {
            text-align: center;
            margin-top: 30px;
            color: #555;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<header>
    <h1>Painel de Business Intelligence</h1>
    <p>Performance de Vendas por Produto</p>
</header>

<!-- CARD: Faturamento Total -->
<div class="resumo">
    <h2>Faturamento Total do Restaurante</h2>
    <div class="valor-total"><?= formatarMoeda($faturamentoTotal) ?></div>
</div>

<!-- TABELA DE PRODUTOS -->
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Produto</th>
            <th>Preco Unitario</th>
            <th>Vendas (un.)</th>
            <th>Faturamento</th>
            <th>Performance</th>
            <th>Participacao</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produtos as $indice => $produto):
            $performance = calcularPerformance($produto['faturamento'], $faturamentoTotal);
            $emAlerta    = verificarAlerta($performance);
            $larguraBarra = round($performance);
        ?>
        <tr>
            <!-- Número -->
            <td><?= $indice + 1 ?></td>

            <!-- Nome do Produto -->
            <td><strong><?= htmlspecialchars($produto['nome']) ?></strong></td>

            <!-- Preco Unitario formatado -->
            <td><?= formatarMoeda($produto['preco']) ?></td>

            <!-- Quantidade vendida -->
            <td><?= number_format($produto['vendas'], 0, ',', '.') ?></td>

            <!-- Faturamento do produto -->
            <td><?= formatarMoeda($produto['faturamento']) ?></td>

            <!-- Barra visual de performance -->
            <td>
                <div class="barra-container">
                    <div class="barra-fill <?= $emAlerta ? 'alerta' : '' ?>"
                         style="width: <?= $larguraBarra ?>%;">
                    </div>
                </div>
            </td>

            <!-- Percentual numerico -->
            <td class="percentual <?= $emAlerta ? 'alerta' : 'ok' ?>">
                <?= number_format($performance, 1, ',', '.') ?>%
            </td>

            <!-- Badge de status -->
            <td>
                <?php if ($emAlerta): ?>
                    <span class="badge-alerta">BAIXO DESEMPENHO</span>
                <?php else: ?>
                    <span class="badge-ok">Normal</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<footer>
    <p>UNICEPLAC &mdash; Disciplina: Programacao Orientada a Software Basica &mdash; Prof. Weverson Garcia Medeiros</p>
</footer>

</body>
</html>
