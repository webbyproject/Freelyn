<?php
session_start();

// Simula sessão para teste sem banco de dados
if (empty($_SESSION['logado'])) {
    $_SESSION['logado'] = true;
    $_SESSION['nome']   = 'Padaria do João';
    $_SESSION['tipo']   = 'empresa';
}

if ($_SESSION['tipo'] !== 'empresa') {
    header('Location: index.html'); exit;
}

$nomeEmpresa = $_SESSION['nome'];
$primeiroNome = explode(' ', $nomeEmpresa)[0];

// Vaga recém postada (simulado via POST)
$vagaPostada = null;
$erroVaga = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['titulo']    ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $salario   = trim($_POST['salario']   ?? '');
    $horario   = trim($_POST['horario']   ?? '');
    $categoria = trim($_POST['categoria'] ?? '');

    if (empty($titulo) || empty($descricao) || empty($salario) || empty($horario) || empty($categoria)) {
        $erroVaga = "Preencha todos os campos para publicar a vaga.";
    } else {
        $vagaPostada = [
            'titulo'    => $titulo,
            'descricao' => $descricao,
            'salario'   => $salario,
            'horario'   => $horario,
            'categoria' => $categoria,
        ];
    }
}

// Vagas simuladas já publicadas
$vagasPublicadas = [
    ['titulo'=>'Garçom para Evento', 'categoria'=>'Bares & Restaurantes', 'horario'=>'Sáb, 18h–23h', 'salario'=>'R$ 150,00', 'candidatos'=>3],
    ['titulo'=>'Auxiliar de Cozinha', 'categoria'=>'Bares & Restaurantes', 'horario'=>'Dom, 10h–16h', 'salario'=>'R$ 120,00', 'candidatos'=>5],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FreelynSJ — Painel da Empresa</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    .dashboard-wrapper { min-height: 100vh; background: var(--cream); }

    /* ── Navbar logada ── */
    .nav-user { display: flex; align-items: center; gap: 12px; }
    .nav-user-info { text-align: right; }
    .nav-user-name { font-size: .88rem; font-weight: 700; color: var(--white); line-height: 1.2; }
    .nav-user-role { font-size: .7rem; color: rgba(255,255,255,.55); text-transform: uppercase; letter-spacing: .08em; }
    .nav-avatar {
      width: 38px; height: 38px; border-radius: 50%;
      background: var(--sky);
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: .95rem; color: var(--navy);
      border: 2px solid rgba(255,255,255,.3);
    }
    .btn-logout {
      background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
      color: var(--white); border-radius: 8px; padding: 7px 16px;
      font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .2s;
      font-family: 'DM Sans', sans-serif; text-decoration: none; display: inline-block;
    }
    .btn-logout:hover { background: rgba(255,255,255,.22); }

    /* ── Hero ── */
    .dash-hero {
      background: linear-gradient(135deg, #1C2B3A 0%, #263e54 100%);
      padding: 48px 5% 56px; position: relative; overflow: hidden;
    }
    .dash-hero::before {
      content: ''; position: absolute; top: -60px; right: -60px;
      width: 300px; height: 300px; background: var(--sky); opacity: .06; border-radius: 50%;
    }
    .dash-hero-inner { max-width: 1200px; margin: 0 auto; position: relative; z-index: 1; }
    .dash-greeting { font-size: .75rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--sky); margin-bottom: 8px; }
    .dash-hero h1 { font-family: 'Playfair Display', serif; font-size: clamp(1.8rem, 3.5vw, 2.8rem); font-weight: 900; color: var(--white); margin-bottom: 10px; line-height: 1.15; }
    .dash-hero h1 em { color: var(--sky); font-style: normal; }
    .dash-hero p { color: rgba(255,255,255,.6); font-size: .95rem; max-width: 480px; line-height: 1.6; }
    .dash-stats { display: flex; gap: 20px; margin-top: 32px; flex-wrap: wrap; }
    .dash-stat { background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1); border-radius: 10px; padding: 14px 22px; display: flex; align-items: center; gap: 12px; }
    .dash-stat-icon { font-size: 1.4rem; }
    .dash-stat-num { font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 700; color: var(--sky); line-height: 1; }
    .dash-stat-label { font-size: .72rem; color: rgba(255,255,255,.5); margin-top: 2px; }

    /* ── Layout de duas colunas ── */
    .dash-content { max-width: 1200px; margin: 0 auto; padding: 40px 5%; display: grid; grid-template-columns: 1fr 1.2fr; gap: 32px; align-items: start; }
    @media (max-width: 900px) { .dash-content { grid-template-columns: 1fr; } }

    /* ── Formulário de nova vaga ── */
    .form-card {
      background: var(--white); border-radius: 16px;
      padding: 36px 32px; box-shadow: 0 4px 24px rgba(28,43,58,.1);
      position: sticky; top: 90px;
    }
    .form-card-title { font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 700; color: var(--navy); margin-bottom: 4px; }
    .form-card-sub { font-size: .85rem; color: var(--muted); margin-bottom: 28px; }
    .form-card .form-group { margin-bottom: 16px; }
    .form-card label { display: block; font-size: .75rem; font-weight: 700; color: var(--navy); margin-bottom: 6px; letter-spacing: .04em; text-transform: uppercase; }
    .form-card input,
    .form-card textarea,
    .form-card select {
      width: 100%; padding: 11px 14px;
      border: 1.5px solid #E2E8F0; border-radius: 10px;
      font-family: 'DM Sans', sans-serif; font-size: .9rem; color: var(--text);
      background: var(--cream); transition: border-color .2s, box-shadow .2s; outline: none;
    }
    .form-card input:focus,
    .form-card textarea:focus,
    .form-card select:focus {
      border-color: var(--navy); box-shadow: 0 0 0 3px rgba(28,43,58,.1); background: var(--white);
    }
    .form-card textarea { resize: vertical; min-height: 90px; }
    .form-card select { cursor: pointer; }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .btn-publicar {
      width: 100%; margin-top: 8px;
      background: var(--navy); color: var(--white);
      border: none; border-radius: 10px; padding: 14px;
      font-family: 'DM Sans', sans-serif; font-size: 1rem; font-weight: 700;
      cursor: pointer; transition: all .2s;
    }
    .btn-publicar:hover { background: var(--orange); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(28,43,58,.3); }

    /* ── Alerta de erro ── */
    .alerta-erro {
      background: #fff3f3; border: 1.5px solid #ffb3b3; border-radius: 10px;
      padding: 12px 16px; color: #c0392b; font-size: .85rem; font-weight: 600;
      margin-bottom: 16px;
    }

    /* ── Sucesso de vaga postada ── */
    .alerta-sucesso {
      background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 10px;
      padding: 16px; color: #166534; font-size: .9rem; font-weight: 600;
      margin-bottom: 16px; display: flex; align-items: center; gap: 10px;
    }
    .alerta-sucesso-icon { font-size: 1.4rem; }

    /* ── Painel direito: vagas publicadas ── */
    .vagas-publicadas { display: flex; flex-direction: column; gap: 0; }
    .dash-section-title { font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 700; color: var(--navy); margin-bottom: 4px; }
    .dash-section-sub { font-size: .85rem; color: var(--muted); margin-bottom: 24px; }

    .vaga-pub-card {
      background: var(--white); border-radius: 14px; padding: 24px 26px;
      box-shadow: 0 4px 16px rgba(28,43,58,.07); margin-bottom: 16px;
      border-left: 4px solid var(--navy); transition: all .2s;
    }
    .vaga-pub-card:hover { transform: translateX(4px); border-left-color: var(--orange); }
    .vaga-pub-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
    .vaga-pub-titulo { font-family: 'Playfair Display', serif; font-size: 1.1rem; font-weight: 700; color: var(--navy); }
    .vaga-pub-categoria { font-size: .68rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; padding: 3px 10px; border-radius: 4px; background: #D9E4F0; color: var(--navy); }
    .vaga-pub-detalhes { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 14px; }
    .vaga-pub-detalhe { font-size: .8rem; color: var(--muted); font-weight: 500; display: flex; align-items: center; gap: 4px; }
    .vaga-pub-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid var(--sand); }
    .vaga-pub-candidatos { font-size: .85rem; font-weight: 700; color: var(--navy); }
    .vaga-pub-candidatos span { color: var(--orange); font-family: 'Playfair Display', serif; font-size: 1.1rem; }
    .btn-ver-candidatos {
      background: var(--orange); color: var(--white);
      border: none; border-radius: 8px; padding: 8px 18px;
      font-family: 'DM Sans', sans-serif; font-size: .8rem; font-weight: 700;
      cursor: pointer; transition: all .2s;
    }
    .btn-ver-candidatos:hover { background: var(--terracota); }

    /* Vaga nova (recém postada) */
    .vaga-pub-card.nova { border-left-color: var(--orange); background: #fffaf7; }
    .badge-nova { display: inline-block; background: var(--orange); color: var(--white); font-size: .65rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; padding: 2px 8px; border-radius: 4px; margin-left: 8px; vertical-align: middle; }

    .empty-state {
      text-align: center; padding: 40px 20px;
      background: var(--white); border-radius: 14px;
      box-shadow: 0 4px 16px rgba(28,43,58,.07);
    }
    .empty-state-icon { font-size: 2.5rem; margin-bottom: 12px; }
    .empty-state p { color: var(--muted); font-size: .9rem; }
  </style>
</head>
<body>
<div class="dashboard-wrapper">

  <!-- Navbar logada -->
  <nav>
    <a class="nav-logo" href="index.html"><img src="assets/logosj.png" alt="FreelynSJ"/></a>
    <div class="nav-user">
      <div class="nav-user-info">
        <div class="nav-user-name"><?php echo htmlspecialchars($primeiroNome); ?></div>
        <div class="nav-user-role">Empresa</div>
      </div>
      <div class="nav-avatar">🏢</div>
      <a href="logout.php" class="btn-logout">Sair</a>
    </div>
  </nav>

  <!-- Hero -->
  <div class="dash-hero">
    <div class="dash-hero-inner">
      <p class="dash-greeting">Painel da Empresa</p>
      <h1>Olá, <em><?php echo htmlspecialchars($primeiroNome); ?></em>! 🏢</h1>
      <p>Publique vagas e encontre o freelancer certo para o seu negócio em São João del-Rei.</p>
      <div class="dash-stats">
        <div class="dash-stat">
          <div class="dash-stat-icon">📋</div>
          <div>
            <div class="dash-stat-num"><?php echo count($vagasPublicadas) + ($vagaPostada ? 1 : 0); ?></div>
            <div class="dash-stat-label">Vagas publicadas</div>
          </div>
        </div>
        <div class="dash-stat">
          <div class="dash-stat-icon">👥</div>
          <div>
            <div class="dash-stat-num"><?php echo array_sum(array_column($vagasPublicadas, 'candidatos')); ?></div>
            <div class="dash-stat-label">Candidatos recebidos</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Conteúdo: 2 colunas -->
  <div class="dash-content">

    <!-- Coluna esquerda: formulário -->
    <div>
      <div class="form-card">
        <div class="form-card-title">Publicar nova vaga</div>
        <div class="form-card-sub">Preencha os detalhes e encontre seu freelancer.</div>

        <?php if ($erroVaga): ?>
          <div class="alerta-erro">⚠️ <?php echo htmlspecialchars($erroVaga); ?></div>
        <?php endif; ?>

        <?php if ($vagaPostada): ?>
          <div class="alerta-sucesso">
            <span class="alerta-sucesso-icon">🎉</span>
            Vaga "<?php echo htmlspecialchars($vagaPostada['titulo']); ?>" publicada com sucesso!
          </div>
        <?php endif; ?>

        <form method="POST" action="dashboard_empresa.php">
          <div class="form-group">
            <label for="titulo">Título da vaga</label>
            <input type="text" id="titulo" name="titulo" placeholder="Ex.: Garçom para Evento" required/>
          </div>

          <div class="form-group">
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria" required>
              <option value="">Selecione...</option>
              <option>Programação</option>
              <option>Ilustração Digital</option>
              <option>Marketing</option>
              <option>Edição de Vídeo</option>
              <option>Bares & Restaurantes</option>
              <option>Fotografia</option>
              <option>Redação</option>
              <option>Música & Eventos</option>
            </select>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label for="salario">Remuneração</label>
              <input type="text" id="salario" name="salario" placeholder="Ex.: R$ 150,00" required/>
            </div>
            <div class="form-group">
              <label for="horario">Horário</label>
              <input type="text" id="horario" name="horario" placeholder="Ex.: Sáb, 18h–23h" required/>
            </div>
          </div>

          <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" placeholder="Descreva a função, requisitos e local..." required></textarea>
          </div>

          <button type="submit" class="btn-publicar">Publicar vaga →</button>
        </form>
      </div>
    </div>

    <!-- Coluna direita: vagas publicadas -->
    <div class="vagas-publicadas">
      <p class="dash-section-title">Suas vagas publicadas</p>
      <p class="dash-section-sub">Acompanhe os candidatos interessados.</p>

      <?php if ($vagaPostada): ?>
      <div class="vaga-pub-card nova">
        <div class="vaga-pub-header">
          <div>
            <span class="vaga-pub-titulo"><?php echo htmlspecialchars($vagaPostada['titulo']); ?></span>
            <span class="badge-nova">Nova</span>
          </div>
          <span class="vaga-pub-categoria"><?php echo htmlspecialchars($vagaPostada['categoria']); ?></span>
        </div>
        <div class="vaga-pub-detalhes">
          <div class="vaga-pub-detalhe">🕐 <?php echo htmlspecialchars($vagaPostada['horario']); ?></div>
          <div class="vaga-pub-detalhe">💰 <?php echo htmlspecialchars($vagaPostada['salario']); ?></div>
        </div>
        <div class="vaga-pub-footer">
          <div class="vaga-pub-candidatos">Candidatos: <span>0</span></div>
          <button class="btn-ver-candidatos">Ver candidatos</button>
        </div>
      </div>
      <?php endif; ?>

      <?php foreach ($vagasPublicadas as $vp): ?>
      <div class="vaga-pub-card">
        <div class="vaga-pub-header">
          <span class="vaga-pub-titulo"><?php echo htmlspecialchars($vp['titulo']); ?></span>
          <span class="vaga-pub-categoria"><?php echo htmlspecialchars($vp['categoria']); ?></span>
        </div>
        <div class="vaga-pub-detalhes">
          <div class="vaga-pub-detalhe">🕐 <?php echo htmlspecialchars($vp['horario']); ?></div>
          <div class="vaga-pub-detalhe">💰 <?php echo htmlspecialchars($vp['salario']); ?></div>
        </div>
        <div class="vaga-pub-footer">
          <div class="vaga-pub-candidatos">Candidatos: <span><?php echo $vp['candidatos']; ?></span></div>
          <button class="btn-ver-candidatos">Ver candidatos</button>
        </div>
      </div>
      <?php endforeach; ?>

      <?php if (!$vagaPostada && empty($vagasPublicadas)): ?>
      <div class="empty-state">
        <div class="empty-state-icon">📋</div>
        <p>Nenhuma vaga publicada ainda.<br>Use o formulário ao lado para começar!</p>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>
</body>
</html>
