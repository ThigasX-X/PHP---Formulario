<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario.html');
    exit;
}

function limpar($valor) {
    return htmlspecialchars(strip_tags(trim($valor)));
}

$nome      = limpar(isset($_POST['nome'])      ? $_POST['nome']      : '');
$sobrenome = limpar(isset($_POST['sobrenome']) ? $_POST['sobrenome'] : '');
$email     = limpar(isset($_POST['email'])     ? $_POST['email']     : '');
$telefone  = limpar(isset($_POST['telefone'])  ? $_POST['telefone']  : '');
$assunto   = limpar(isset($_POST['assunto'])   ? $_POST['assunto']   : '');
$mensagem  = limpar(isset($_POST['mensagem'])  ? $_POST['mensagem']  : '');

$assuntosValidos = ['suporte', 'comercial', 'financeiro', 'outro'];

$erros = [];

if (empty($nome)) {
    $erros[] = 'O campo Nome é obrigatório.';
}

if (empty($sobrenome)) {
    $erros[] = 'O campo Sobrenome é obrigatório.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Informe um e-mail válido.';
}

if (!empty($telefone) && !preg_match('/^[\d\s()\-+]{7,20}$/', $telefone)) {
    $erros[] = 'O telefone informado é inválido.';
}

if (!in_array($assunto, $assuntosValidos)) {
    $erros[] = 'Selecione um assunto válido.';
}

if (empty($mensagem)) {
    $erros[] = 'O campo Mensagem é obrigatório.';
}

if (!empty($erros)) {
    $queryString = http_build_query([
        'status' => 'erro',
        'erros'  => implode('|', $erros),
    ]);
    header("Location: formulario.html?$queryString");
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Dados Recebidos</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f0f2f5; display: flex; justify-content: center; padding: 40px; }
    .card { background: #fff; padding: 32px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,.1); width: 100%; max-width: 480px; }
    h1 { color: #155724; margin-bottom: 24px; font-size: 1.4rem; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 10px 12px; border-bottom: 1px solid #eee; }
    th { background: #f8f9fa; color: #555; width: 35%; }
    td { color: #333; word-break: break-word; }
    a { display: inline-block; margin-top: 24px; padding: 10px 20px; background: #4a90e2; color: #fff; text-decoration: none; border-radius: 4px; }
    a:hover { background: #357abd; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Dados enviados com sucesso!</h1>
    <table>
      <tr><th>Nome</th><td><?= $nome ?> <?= $sobrenome ?></td></tr>
      <tr><th>E-mail</th><td><?= $email ?></td></tr>
      <tr><th>Telefone</th><td><?= $telefone ?: '—' ?></td></tr>
      <tr><th>Assunto</th><td><?= ucfirst($assunto) ?></td></tr>
      <tr><th>Mensagem</th><td><?= nl2br($mensagem) ?></td></tr>
    </table>
    <a href="formulario.html">Voltar ao formulário</a>
  </div>
</body>
</html>