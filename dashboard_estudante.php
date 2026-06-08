<?php
session_start();

// ── Origem 1: vindo do login (POST direto)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loginUsuario'])) {
    $_SESSION['logado']  = true;
    $_SESSION['tipo']    = 'estudante';
    $_SESSION['nome']    = $_POST['loginUsuario'];
    $_SESSION['usuario'] = $_POST['loginUsuario'];
    $_SESSION['cadastro_novo'] = false;
}

// ── Origem 2: vindo do cadastro (sessão já preenchida por processa.php)
// (nada a fazer — a sessão já está pronta)

// ── Origem 3: sem sessão (acesso direto / demo)
if (empty($_SESSION['logado'])) {
    $_SESSION['logado']        = true;
    $_SESSION['nome']          = 'Maria Silva';
    $_SESSION['tipo']          = 'estudante';
    $_SESSION['cadastro_novo'] = false;
}

$nomeCompleto  = $_SESSION['nome']    ?? 'Usuário';
$usuario       = $_SESSION['usuario'] ?? '';
$email         = $_SESSION['email']   ?? '';
$cpf           = $_SESSION['cpf']     ?? '';
$cadastroNovo  = $_SESSION['cadastro_novo'] ?? false;

// Limpa a flag para não reexibir na próxima recarga
$_SESSION['cadastro_novo'] = false;

// Divide o nome com segurança
$partesNome   = explode(' ', trim($nomeCompleto));
$primeiroNome = !empty($partesNome[0]) ? $partesNome[0] : 'Usuário';

// Vagas simuladas (sem banco de dados)
$vagas = [
    ['id'=>1, 'titulo'=>'Garçom para Evento',       'empresa'=>'Buffet Sabor & Arte',    'salario'=>'R$ 150,00',  'horario'=>'Sáb, 18h–23h',    'categoria'=>'Bares & Restaurantes', 'local'=>'Centro, SJDR'],
    ['id'=>5, 'titulo'=>'Músico para Bar',           'empresa'=>'Rustic PUB',      'salario'=>'R$ 200,00',  'horario'=>'Sex, 19h–00h',     'categoria'=>'Música & Eventos',     'local'=>'Centro, SJDR'],
    ['id'=>3, 'titulo'=>'Fotógrafo de Casamento',    'empresa'=>'Studio Luz & Cia',       'salario'=>'R$ 500,00',  'horario'=>'Dom, dia inteiro', 'categoria'=>'Fotografia',           'local'=>'Tiradentes'],
    ['id'=>4, 'titulo'=>'Editor de Vídeo',           'empresa'=>'Agência Click Digital',  'salario'=>'R$ 250,00',  'horario'=>'Flexível',         'categoria'=>'Edição de Vídeo',      'local'=>'Remoto'],
    ['id'=>2, 'titulo'=>'Designer de Posts',         'empresa'=>'Moda Mineira Boutique',  'salario'=>'R$ 300,00',  'horario'=>'Flexível',         'categoria'=>'Ilustração Digital',   'local'=>'Remoto'],
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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

    /* ── Estrutura em Duas Colunas (Com espaçamento superior ajustado) ── */
    .dash-main-layout {
      max-width: 1200px;
      margin: 0 auto;
      padding: 50px 5%;
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 40px;
    }

    @media (max-width: 900px) {
      .dash-main-layout {
        grid-template-columns: 1fr;
        gap: 30px;
        padding-top: 30px;
      }
    }

    /* ── Barra Lateral (Sidebar) ── */
    .dash-sidebar {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    

    /* Saudação Integrada */
    .dash-sidebar-welcome {
      margin-bottom: 4px;
    }
    .dash-sidebar-welcome .greeting-tag {
      font-size: .7rem; font-weight: 700; letter-spacing: .1em;
      text-transform: uppercase; color: var(--orange);
      display: block; margin-bottom: 2px;
    }
    .dash-sidebar-welcome h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem; font-weight: 900; color: var(--navy);
      margin: 0; line-height: 1.2;
    }
    .dash-sidebar-welcome h1 em { color: var(--orange); font-style: normal; }

    /* Card de Estatística Estilizado */
    .dash-stat-card {
      background: var(--white);
      border-radius: 14px;
      padding: 20px;
      box-shadow: 0 4px 20px rgba(28,43,58,.03);
      border: 1px solid var(--sand);
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .dash-stat-icon-wrapper {
      width: 42px; height: 42px;
      background: var(--orange);
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
    }
    .dash-stat-num {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem; font-weight: 900; color: var(--navy);
      line-height: 1.1;
    }
    .dash-stat-label { 
      font-size: .78rem; 
      color: var(--muted); 
      font-weight: 500;
    }

    /* Caixa do Filtro Vertical */
    .filter-box {
      background: var(--white);
      border-radius: 14px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(28,43,58,.03);
      border: 1px solid var(--sand);
    }
    .filter-box-title {
      font-size: .75rem; font-weight: 700; color: var(--navy);
      text-transform: uppercase; letter-spacing: .08em;
      margin-bottom: 14px; display: block;
    }
    .filter-chips-vertical {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .filter-chip {
      background: var(--cream); 
      border: 1px solid transparent;
      border-radius: 8px; 
      padding: 10px 14px;
      font-size: .85rem; font-weight: 600; color: var(--text);
      cursor: pointer; transition: all .2s;
      text-align: left;
    }
    .filter-chip:hover, .filter-chip.active {
      background: var(--orange);
      color: var(--white);
    }

    /* ── Conteúdo da Listagem ── */
    .dash-section-header {
      margin-bottom: 28px;
      border-bottom: 1px solid var(--sand);
      padding-bottom: 16px;
    }
    .dash-section-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem; font-weight: 700; color: var(--navy);
      margin-bottom: 6px;
    }
    .dash-section-sub {
      font-size: .9rem; color: var(--muted);
    }

    /* ── Grid de vagas ── */
    .vagas-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
      gap: 20px;
    }

    /* ── Card de vaga ── */
    .vaga-card {
      background: var(--white);
      border-radius: 14px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(28,43,58,.04);
      border: 1.5px solid transparent;
      transition: all .25s;
      display: flex; flex-direction: column;
    }
    .vaga-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 36px rgba(28,43,58,.08);
      border-color: var(--orange);
    }
    .vaga-categoria {
      align-self: flex-start;
      font-size: .65rem; font-weight: 700; letter-spacing: .08em;
      text-transform: uppercase; padding: 4px 10px; border-radius: 6px;
      background: #FDE9D9; color: var(--orange);
      margin-bottom: 14px;
    }
    .vaga-titulo {
      font-family: 'Playfair Display', serif;
      font-size: 1.2rem; font-weight: 700; color: var(--navy);
      margin-bottom: 6px; line-height: 1.3;
    }
    .vaga-empresa {
      font-size: .85rem; color: var(--muted); font-weight: 500;
      margin-bottom: 16px;
    }
    .vaga-detalhes {
      display: flex; flex-direction: column; gap: 6px;
      margin-bottom: 20px; border-top: 1px solid var(--cream); padding-top: 12px;
    }
    .vaga-detalhe {
      display: flex; align-items: center; gap: 8px;
      font-size: .8rem; color: var(--text); font-weight: 500;
    }
    .vaga-detalhe-icon { font-size: .9rem; opacity: 0.8; }
    
    .vaga-footer {
      margin-top: auto;
      display: flex; align-items: center; justify-content: space-between;
      gap: 12px; padding-top: 12px;
    }
    .vaga-salario {
      font-family: 'Playfair Display', serif;
      font-size: 1.25rem; font-weight: 700; color: var(--orange);
    }
    .btn-candidatar {
      background: var(--navy); color: var(--white);
      border: none; border-radius: 8px;
      padding: 10px 16px; font-family: 'DM Sans', sans-serif;
      font-size: .85rem; font-weight: 700; cursor: pointer;
      transition: all .2s;
    }
    .btn-candidatar:hover {
      background: var(--orange);
      box-shadow: 0 4px 12px rgba(212,116,58,.2);
    }
    .btn-candidatar.aplicado {
      background: #e8f5e9; color: #2e7d32;
      cursor: default; transform: none; box-shadow: none;
    }

    /* ── Banner de boas-vindas ── */
    .banner-cadastro {
      background: linear-gradient(90deg, #e8f5e9 0%, #f0fdf4 100%);
      border-bottom: 2px solid #86efac;
      padding: 14px 5%;
      display: flex; align-items: center; justify-content: space-between;
      gap: 12px;
    }
    .banner-inner {
      display: flex; align-items: center; gap: 14px;
    }
    .banner-emoji { font-size: 1.6rem; }
    .banner-inner strong {
      display: block; font-size: .95rem; font-weight: 700; color: #166534;
    }
    .banner-inner span {
      font-size: .82rem; color: #4b7a5a;
    }
    .banner-close {
      background: none; border: none; font-size: 1rem;
      color: #4b7a5a; cursor: pointer; opacity: .7; flex-shrink: 0;
    }
    .banner-close:hover { opacity: 1; }

    /* ── Card de Perfil na Sidebar ── */
    .profile-card-dash {
      background: var(--white); border-radius: 14px; padding: 20px;
      box-shadow: 0 4px 20px rgba(28,43,58,.03); border: 1px solid var(--sand);
    }
    .profile-card-dash-title {
      font-size: .7rem; font-weight: 700; letter-spacing: .1em;
      text-transform: uppercase; color: var(--muted);
      display: block; margin-bottom: 12px;
    }
    .profile-card-dash-row {
      display: flex; align-items: center; gap: 10px;
      font-size: .82rem; color: var(--text); padding: 5px 0;
      border-bottom: 1px solid var(--cream);
    }
    .profile-card-dash-row:last-child { border-bottom: none; }
    .profile-card-dash-row strong {
      font-size: .7rem; text-transform: uppercase;
      letter-spacing: .06em; color: var(--muted); min-width: 52px;
    }

    /* ── Toast de confirmação ── */
    .toast {
      position: fixed; bottom: 28px; right: 28px; z-index: 999;
      background: var(--sky); color: var(--white);
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

  <!-- Banner de boas-vindas após cadastro -->
  <?php if ($cadastroNovo): ?>
  <div class="banner-cadastro" id="banner-boas-vindas">
    <div class="banner-inner">
      <span class="banner-emoji">🎉</span>
      <div>
        <strong>Cadastro realizado com sucesso, <?php echo htmlspecialchars($primeiroNome); ?>!</strong>
        <span>Bem-vindo(a) à comunidade FreelynSJ. Explore as vagas abaixo.</span>
      </div>
    </div>
    <button class="banner-close" onclick="document.getElementById('banner-boas-vindas').style.display='none'" aria-label="Fechar">✕</button>
  </div>
  <?php endif; ?>

  <!-- Estrutura Principal Dividida (Sem a Hero antiga) -->
  <div class="dash-main-layout">
    
    <!-- Esquerda: Saudação, Informações e Filtros -->
    <aside class="dash-sidebar">
      
      <!-- Bloco de Boas-vindas Mesclado -->
      <div class="dash-sidebar-welcome">
        <span class="greeting-tag">Painel de Vagas</span>
        <h1>Olá, <em><?php echo htmlspecialchars($primeiroNome); ?></em></h1>
      </div>
      
      <!-- Card Vagas Disponíveis -->
      <div class="dash-stat-card">
        <div class="dash-stat-icon-wrapper">
          <img src="assets/briefcase.png" width="20" alt="Vagas"/>
        </div>
        <div>
          <div class="dash-stat-num" id="count-vagas"><?php echo count($vagas); ?></div>
          <div class="dash-stat-label">Vagas abertas</div>
        </div>
      </div>
      

      <!-- Card de Perfil (dados do cadastro/login) -->
      <?php if (!empty($email) || !empty($usuario) || !empty($cpf)): ?>
      <div class="profile-card-dash">
        <span class="profile-card-dash-title">Meu Perfil</span>
        <?php if (!empty($usuario)): ?>
        <div class="profile-card-dash-row">
          <strong>Usuário</strong> <?php echo htmlspecialchars($usuario); ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($email)): ?>
        <div class="profile-card-dash-row">
          <strong>E-mail</strong> <?php echo htmlspecialchars($email); ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($cpf)): ?>
        <div class="profile-card-dash-row">
          <strong>CPF</strong> <?php echo htmlspecialchars($cpf); ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Caixa de Filtros Verticais -->
      <div class="filter-box">
        <span class="filter-box-title">Filtrar por Categoria</span>
        <div class="filter-chips-vertical">
          <button class="filter-chip active" onclick="filtrar(this, 'todas')">
            <i class="fas fa-layer-group"></i> Todas as vagas
          </button>
          <button class="filter-chip" onclick="filtrar(this, 'Remoto')">
            <i class="fas fa-house-laptop"></i> Trabalho Remoto
          </button>
          <button class="filter-chip" onclick="filtrar(this, 'Fotografia')">
            <i class="fas fa-camera"></i> Fotografia
          </button>
          <button class="filter-chip" onclick="filtrar(this, 'Música & Eventos')">
            <i class="fas fa-music"></i> Música & Shows
          </button>
          <button class="filter-chip" onclick="filtrar(this, 'Bares & Restaurantes')">
            <i class="fas fa-utensils"></i> Gastronomia
          </button>
        </div>
      </div>

    </aside>

    <!-- Direita: Título da Seção e Grid de Vagas -->
    <main>
      <div class="dash-section-header">
        <h2 class="dash-section-title">Oportunidades em Destaque</h2>
        <p class="dash-section-sub">Encontre freelancers e bicos em São João del-Rei e região. Clique para demonstrar interesse.</p>
      </div>

      <!-- Grid de vagas -->
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
          
          <div class="vaga-footer">
            <div class="vaga-salario"><?php echo htmlspecialchars($vaga['salario']); ?></div>
            <button class="btn-candidatar" onclick="candidatar(this, '<?php echo htmlspecialchars($vaga['titulo']); ?>')">
              Candidatar-se
            </button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </main>

  </div>
</div>

<!-- Toast de confirmação -->
<div class="toast" id="toast">
  <span class="toast-icon">✅</span>
  <span id="toast-msg">Candidatura enviada!</span>
</div>

<script>
  function candidatar(btn, titulo) {
    btn.textContent = '✓ Aplicado';
    btn.classList.add('aplicado');
    btn.disabled = true;

    const toast = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = `Inscrição para "${titulo}" enviada!`;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3500);
  }

  function filtrar(chip, valor) {
    document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');

    let countVisiveis = 0;

    document.querySelectorAll('.vaga-card').forEach(card => {
      if (valor === 'todas') {
        card.style.display = '';
        countVisiveis++;
      } else {
        const cat = card.dataset.categoria;
        const local = card.dataset.local;
        if (cat === valor || local.includes(valor)) {
          card.style.display = '';
          countVisiveis++;
        } else {
          card.style.display = 'none';
        }
      }
    });

    // Atualiza o contador lateral dinamicamente conforme o filtro!
    document.getElementById('count-vagas').textContent = countVisiveis;
  }
</script>
</body>
</html>