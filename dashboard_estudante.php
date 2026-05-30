<?php
session_start();

// Simula sessão para teste sem banco de dados
if (empty($_SESSION['logado'])) {
    // Para testar sem login, cria uma sessão fake
    $_SESSION['logado'] = true;
    $_SESSION['nome']   = 'Maria Silva';
    $_SESSION['tipo']   = 'estudante';
}

if ($_SESSION['tipo'] !== 'estudante') {
    header('Location: index.html'); exit;
}

$nome = $_SESSION['nome'];
$primeiroNome = explode(' ', $nome)[0];

// Vagas simuladas (sem banco de dados)
$vagas = [
    ['id'=>1, 'titulo'=>'Garçom para Evento',       'empresa'=>'Buffet Sabor & Arte',    'salario'=>'R$ 150,00',  'horario'=>'Sáb, 18h–23h',    'categoria'=>'Bares & Restaurantes', 'local'=>'Centro, SJdR'],
    ['id'=>2, 'titulo'=>'Designer de Posts',         'empresa'=>'Moda Mineira Boutique',  'salario'=>'R$ 300,00',  'horario'=>'Flexível',         'categoria'=>'Ilustração Digital',   'local'=>'Remoto'],
    ['id'=>3, 'titulo'=>'Fotógrafo de Casamento',    'empresa'=>'Studio Luz & Cia',       'salario'=>'R$ 500,00',  'horario'=>'Dom, dia inteiro', 'categoria'=>'Fotografia',           'local'=>'São João del-Rei'],
    ['id'=>4, 'titulo'=>'Editor de Vídeo',           'empresa'=>'Agência Click Digital',  'salario'=>'R$ 250,00',  'horario'=>'Flexível',         'categoria'=>'Edição de Vídeo',      'local'=>'Remoto'],
    ['id'=>5, 'titulo'=>'Músico para Bar',           'empresa'=>'Bar do Zé Mineiro',      'salario'=>'R$ 200,00',  'horario'=>'Sex, 20h–00h',     'categoria'=>'Música & Eventos',     'local'=>'Tiradentes'],
    ['id'=>6, 'titulo'=>'Redator de Blog',           'empresa'=>'Portal SJdR Notícias',   'salario'=>'R$ 180,00',  'horario'=>'Flexível',         'categoria'=>'Redação',              'local'=>'Remoto'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FreelynSJ — Painel do Estudante</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    /* ── Dashboard Layout ── */
    .dashboard-wrapper {
      min-height: 100vh;
      background: var(--cream);
    }

    /* ── Navbar logada ── */
    .nav-user {
      display: flex; align-items: center; gap: 12px;
    }
    .nav-user-info {
      text-align: right;
    }
    .nav-user-name {
      font-size: .88rem; font-weight: 700; color: var(--white);
      line-height: 1.2;
    }
    .nav-user-role {
      font-size: .7rem; color: rgba(255,255,255,.55);
      text-transform: uppercase; letter-spacing: .08em;
    }
    .nav-avatar {
      width: 38px; height: 38px; border-radius: 50%;
      background: var(--orange);
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: .95rem; color: var(--white);
      border: 2px solid rgba(255,255,255,.3);
    }
    .btn-logout {
      background: rgba(255,255,255,.12);
      border: 1px solid rgba(255,255,255,.2);
      color: var(--white); border-radius: 8px;
      padding: 7px 16px; font-size: .8rem; font-weight: 600;
      cursor: pointer; transition: all .2s;
      font-family: 'DM Sans', sans-serif;
      text-decoration: none; display: inline-block;
    }
    .btn-logout:hover { background: rgba(255,255,255,.22); }

    /* ── Hero do dashboard ── */
    .dash-hero {
      background: linear-gradient(135deg, var(--navy) 0%, #263e54 100%);
      padding: 48px 5% 56px;
      position: relative; overflow: hidden;
    }
    .dash-hero::before {
      content: '';
      position: absolute; top: -60px; right: -60px;
      width: 300px; height: 300px;
      background: var(--orange); opacity: .07;
      border-radius: 50%;
    }
    .dash-hero::after {
      content: '';
      position: absolute; bottom: -80px; left: 30%;
      width: 200px; height: 200px;
      background: var(--sky); opacity: .05;
      border-radius: 50%;
    }
    .dash-hero-inner {
      max-width: 1200px; margin: 0 auto;
      position: relative; z-index: 1;
    }
    .dash-greeting {
      font-size: .75rem; font-weight: 700; letter-spacing: .14em;
      text-transform: uppercase; color: var(--orange);
      margin-bottom: 8px;
    }
    .dash-hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.8rem, 3.5vw, 2.8rem);
      font-weight: 900; color: var(--white);
      margin-bottom: 10px; line-height: 1.15;
    }
    .dash-hero h1 em { color: var(--orange); font-style: normal; }
    .dash-hero p {
      color: rgba(255,255,255,.6); font-size: .95rem;
      max-width: 480px; line-height: 1.6;
    }

    /* ── Stats mini ── */
    .dash-stats {
      display: flex; gap: 20px; margin-top: 32px; flex-wrap: wrap;
    }
    .dash-stat {
      background: rgba(255,255,255,.07);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 10px; padding: 14px 22px;
      display: flex; align-items: center; gap: 12px;
    }
    .dash-stat-icon { font-size: 1.4rem; }
    .dash-stat-num {
      font-family: 'Playfair Display', serif;
      font-size: 1.4rem; font-weight: 700; color: var(--orange);
      line-height: 1;
    }
    .dash-stat-label { font-size: .72rem; color: rgba(255,255,255,.5); margin-top: 2px; }

    /* ── Conteúdo principal ── */
    .dash-content {
      max-width: 1200px; margin: 0 auto;
      padding: 40px 5%;
    }

    /* ── Filtros ── */
    .dash-filters {
      display: flex; gap: 10px; flex-wrap: wrap;
      margin-bottom: 32px; align-items: center;
    }
    .dash-filters-label {
      font-size: .78rem; font-weight: 700; color: var(--muted);
      text-transform: uppercase; letter-spacing: .08em;
      margin-right: 4px;
    }
    .filter-chip {
      background: var(--white); border: 1.5px solid var(--sand);
      border-radius: 20px; padding: 6px 16px;
      font-size: .8rem; font-weight: 600; color: var(--muted);
      cursor: pointer; transition: all .2s;
    }
    .filter-chip:hover, .filter-chip.active {
      background: var(--orange); border-color: var(--orange);
      color: var(--white);
    }

    /* ── Seção título ── */
    .dash-section-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem; font-weight: 700; color: var(--navy);
      margin-bottom: 6px;
    }
    .dash-section-sub {
      font-size: .88rem; color: var(--muted); margin-bottom: 28px;
    }

    /* ── Grid de vagas ── */
    .vagas-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 20px;
    }

    /* ── Card de vaga ── */
    .vaga-card {
      background: var(--white);
      border-radius: 14px;
      padding: 28px;
      box-shadow: 0 4px 20px rgba(28,43,58,.08);
      border: 1.5px solid transparent;
      transition: all .25s;
      display: flex; flex-direction: column; gap: 0;
    }
    .vaga-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 36px rgba(28,43,58,.14);
      border-color: var(--orange);
    }
    .vaga-categoria {
      display: inline-block;
      font-size: .68rem; font-weight: 700; letter-spacing: .1em;
      text-transform: uppercase; padding: 3px 10px; border-radius: 4px;
      background: #FDE9D9; color: var(--orange);
      margin-bottom: 12px;
    }
    .vaga-titulo {
      font-family: 'Playfair Display', serif;
      font-size: 1.15rem; font-weight: 700; color: var(--navy);
      margin-bottom: 4px; line-height: 1.3;
    }
    .vaga-empresa {
      font-size: .85rem; color: var(--muted); font-weight: 500;
      margin-bottom: 16px;
    }
    .vaga-detalhes {
      display: flex; gap: 16px; flex-wrap: wrap;
      margin-bottom: 20px;
    }
    .vaga-detalhe {
      display: flex; align-items: center; gap: 5px;
      font-size: .8rem; color: var(--text); font-weight: 500;
    }
    .vaga-detalhe-icon { font-size: .9rem; }
    .vaga-salario {
      font-family: 'Playfair Display', serif;
      font-size: 1.3rem; font-weight: 700; color: var(--orange);
      margin-bottom: 16px;
    }
    .btn-candidatar {
      width: 100%;
      background: var(--navy); color: var(--white);
      border: none; border-radius: 10px;
      padding: 12px; font-family: 'DM Sans', sans-serif;
      font-size: .9rem; font-weight: 700; cursor: pointer;
      transition: all .2s; margin-top: auto;
    }
    .btn-candidatar:hover {
      background: var(--orange);
      transform: translateY(-1px);
      box-shadow: 0 4px 16px rgba(212,116,58,.35);
    }
    .btn-candidatar.aplicado {
      background: #e8f5e9; color: #2e7d32;
      cursor: default; transform: none; box-shadow: none;
    }

    /* ── Toast de confirmação ── */
    .toast {
      position: fixed; bottom: 28px; right: 28px; z-index: 999;
      background: var(--navy); color: var(--white);
      padding: 14px 24px; border-radius: 12px;
      font-size: .9rem; font-weight: 600;
      box-shadow: 0 8px 32px rgba(28,43,58,.3);
      transform: translateY(80px); opacity: 0;
      transition: all .4s cubic-bezier(.34,1.56,.64,1);
      display: flex; align-items: center; gap: 10px;
    }
    .toast.show { transform: translateY(0); opacity: 1; }
    .toast-icon { font-size: 1.2rem; }
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
        <div class="nav-user-role">Freelancer</div>
      </div>
      <div class="nav-avatar"><?php echo strtoupper(substr($primeiroNome, 0, 1)); ?></div>
      <a href="logout.php" class="btn-logout">Sair</a>
    </div>
  </nav>

  <!-- Hero do dashboard -->
  <div class="dash-hero">
    <div class="dash-hero-inner">
      <p class="dash-greeting">Painel do Freelancer</p>
      <h1>Olá, <em><?php echo htmlspecialchars($primeiroNome); ?></em>! 👋</h1>
      <p>Encontre oportunidades em São João del-Rei e região. Candidate-se com um clique.</p>
      <div class="dash-stats">
        <div class="dash-stat">
          <div class="dash-stat-icon">💼</div>
          <div>
            <div class="dash-stat-num"><?php echo count($vagas); ?></div>
            <div class="dash-stat-label">Vagas disponíveis</div>
          </div>
        </div>
        <div class="dash-stat">
          <div class="dash-stat-icon">📍</div>
          <div>
            <div class="dash-stat-num">SJdR</div>
            <div class="dash-stat-label">Sua região</div>
          </div>
        </div>
        <div class="dash-stat">
          <div class="dash-stat-icon">⭐</div>
          <div>
            <div class="dash-stat-num">100%</div>
            <div class="dash-stat-label">Gratuito</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Conteúdo -->
  <div class="dash-content">

    <!-- Filtros -->
    <div class="dash-filters">
      <span class="dash-filters-label">Filtrar:</span>
      <div class="filter-chip active" onclick="filtrar(this, 'todas')">Todas</div>
      <div class="filter-chip" onclick="filtrar(this, 'Remoto')">Remoto</div>
      <div class="filter-chip" onclick="filtrar(this, 'Fotografia')">Fotografia</div>
      <div class="filter-chip" onclick="filtrar(this, 'Música & Eventos')">Música</div>
      <div class="filter-chip" onclick="filtrar(this, 'Bares & Restaurantes')">Bares</div>
    </div>

    <p class="dash-section-title">Vagas disponíveis</p>
    <p class="dash-section-sub">Clique em "Candidatar-se" para enviar seu interesse à empresa.</p>

    <!-- Grid de vagas gerado pelo PHP -->
    <div class="vagas-grid" id="vagas-grid">
      <?php foreach ($vagas as $vaga): ?>
      <div class="vaga-card" data-categoria="<?php echo htmlspecialchars($vaga['categoria']); ?>" data-local="<?php echo htmlspecialchars($vaga['local']); ?>">
        <span class="vaga-categoria"><?php echo htmlspecialchars($vaga['categoria']); ?></span>
        <div class="vaga-titulo"><?php echo htmlspecialchars($vaga['titulo']); ?></div>
        <div class="vaga-empresa"><?php echo htmlspecialchars($vaga['empresa']); ?></div>
        <div class="vaga-detalhes">
          <div class="vaga-detalhe"><span class="vaga-detalhe-icon">🕐</span><?php echo htmlspecialchars($vaga['horario']); ?></div>
          <div class="vaga-detalhe"><span class="vaga-detalhe-icon">📍</span><?php echo htmlspecialchars($vaga['local']); ?></div>
        </div>
        <div class="vaga-salario"><?php echo htmlspecialchars($vaga['salario']); ?></div>
        <button class="btn-candidatar" onclick="candidatar(this, '<?php echo htmlspecialchars($vaga['titulo']); ?>')">
          Candidatar-se →
        </button>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</div>

<!-- Toast de confirmação -->
<div class="toast" id="toast">
  <span class="toast-icon">✅</span>
  <span id="toast-msg">Candidatura enviada!</span>
</div>

<script>
  function candidatar(btn, titulo) {
    btn.textContent = '✓ Candidatura enviada!';
    btn.classList.add('aplicado');
    btn.disabled = true;

    const toast = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = `Candidatura para "${titulo}" enviada!`;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3500);
  }

  function filtrar(chip, valor) {
    document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');

    document.querySelectorAll('.vaga-card').forEach(card => {
      if (valor === 'todas') {
        card.style.display = '';
      } else {
        const cat = card.dataset.categoria;
        const local = card.dataset.local;
        card.style.display = (cat === valor || local.includes(valor)) ? '' : 'none';
      }
    });
  }
</script>
</body>
</html>
