<?php
// RECEBE OS DADOS ENVIADOS PELO FORMULÁRIO POST
$nome = $_POST["nomeCadastro"] ?? '';
$cpf = $_POST["cpf"] ?? '';
$usuario = $_POST["usuario"] ?? '';
$email = $_POST["email"] ?? '';
$senha = $_POST["senha"] ?? '';

// VALIDAÇÃO: Verifica se algum campo está vazio
if (empty($nome) || empty($cpf) || empty($usuario) || empty($email) || empty ($senha)) {
    $erro = "Erro! por favor, preencha todos os campos!";
}
// VALIDAÇÃO: Senha curta
elseif (strlen($senha) < 8) {
    $erro = "A senha precisa ter pelo menos 8 caracteres.";
}

// SE ESTIVER TUDO CORRETO
else {
    $erro = null;
    $primeiroNome = explode(' ', trim($nome))[0] ?? '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>FreelynSJ — Cadastro</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php if ($erro): ?>
    <!-- Se houve erro, mostra a mensagem e um link pra voltar -->
    <div style="text-align:center; padding:60px 20px;">
      <h2>Ops, algo deu errado!</h2>
      <p><?php echo htmlspecialchars($erro); ?></p>
      <a href="index.html">← Voltar e tentar novamente</a>
    </div>

<?php else: ?>
    <!-- Se deu tudo certo, exibe a mensagem de boas-vindas -->
    <div style="text-align:center; padding:60px 20px;">
      <div style="font-size:3rem;">🎉</div>
      <h2>Cadastro realizado com sucesso!</h2>
      <p>Bem-vindo(a), <strong><?php echo htmlspecialchars($primeiroNome); ?></strong>!</p>
      <a href="index.html">Explorar vagas →</a>
    </div>

<?php endif; ?>

</body>
</html>