<<<<<<< HEAD
<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'alunos') {
    header('Location: index.html');
    exit;
}

$nome = $_SESSION['nome'];
$ra = $_SESSION['ra'];
$curso = $_SESSION['curso'];
$status = "Ativo"; // pode vir do banco depois

// Exemplo de avisos (você pode puxar do banco)
$avisos = [
  ["titulo" => "INFORMATIVO – REGULARIZAÇÃO DOCUMENTAL", "data" => "06/11/2025", "texto" => "Atualize seus documentos pendentes no portal do aluno."],
  ["titulo" => "ABERTURA DE INSCRIÇÕES – SEMANA ACADÊMICA", "data" => "12/11/2025", "texto" => "Participe das palestras e oficinas abertas a todos os alunos."]
];
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Painel do Aluno - GES</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* ====== LAYOUT BASE ====== */
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background-color: #f4f6f9;
    }
    .layout {
      display: flex;
      height: 100vh;
    }

    /* ====== MENU LATERAL ====== */
    .sidebar {
      width: 250px;
      background-color: #004c97;
      color: white;
      display: flex;
      flex-direction: column;
      padding: 20px;
    }
    .sidebar .logo {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 30px;
      line-height: 1.2;
    }
    .sidebar nav a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 10px;
      border-radius: 6px;
      margin: 4px 0;
      font-size: 15px;
    }
    .sidebar nav a:hover,
    .sidebar nav a.active {
      background: rgba(255,255,255,0.2);
    }
    .logout {
      margin-top: auto;
      color: #ffcccb;
    }

    /* ====== CONTEÚDO PRINCIPAL ====== */
    .content {
      flex: 1;
      background: #f4f6f9;
      padding: 25px;
      overflow-y: auto;
    }

    /* ====== TOPO (INFO DO ALUNO) ====== */
    .topbar {
      background: white;
      border-radius: 12px;
      padding: 20px 25px;
      margin-bottom: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .aluno-info {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .aluno-foto {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      background-color: #ccc;
      background-image: url('https://cdn-icons-png.flaticon.com/512/149/149071.png');
      background-size: cover;
      background-position: center;
    }
    .aluno-dados strong {
      font-size: 18px;
      color: #004c97;
    }
    .aluno-dados small {
      color: #555;
    }
    .status {
      background: #d4edda;
      color: #155724;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 600;
    }

    /* ====== AVISOS ====== */
    .dashboard h2 {
      color: #004c97;
      margin-bottom: 15px;
    }
    .cards {
      display: grid;
      gap: 15px;
    }
    .card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .card-header {
      display: flex;
      justify-content: space-between;
      font-weight: bold;
      color: #004c97;
      margin-bottom: 8px;
    }
    .card p {
      margin: 0;
      color: #333;
      line-height: 1.5;
    }
  </style>
</head>
<body>
  <div class="layout">
    <!-- MENU LATERAL -->
    <aside class="sidebar">
      <div class="logo">GES<br><span style="font-size:14px;">Painel do Aluno</span></div>
      <nav>
        <a href="#" class="active">📢 Avisos</a>
        <a href="#">📅 Calendário</a>
        <a href="#">🧾 Notas</a>
        <a href="#">📚 Disciplinas</a>
        <a href="#">💬 Mensagens</a>
        <a href="#">⚙️ Configurações</a>
        <a href="logout.php" class="logout">Sair</a>
      </nav>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="content">
      <header class="topbar">
        <div class="aluno-info">
          <div class="aluno-foto"></div>
          <div class="aluno-dados">
            <strong><?php echo $nome; ?></strong><br>
            <small><?php echo $curso; ?> | RA: <?php echo $ra; ?></small>
          </div>
        </div>
        <div class="status"><?php echo $status; ?></div>
      </header>

      <section class="dashboard">
        <h2>Avisos Recentes</h2>
        <div class="cards">
          <?php foreach ($avisos as $a): ?>
            <div class="card">
              <div class="card-header">
                <span><?php echo $a["titulo"]; ?></span>
                <span><?php echo $a["data"]; ?></span>
              </div>
              <p><?php echo $a["texto"]; ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
=======
<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'alunos') {
    header('Location: index.html');
    exit;
}

$nome = $_SESSION['nome'];
$ra = $_SESSION['ra'];
$curso = $_SESSION['curso'];
$status = "Ativo"; // pode vir do banco depois

// Exemplo de avisos (você pode puxar do banco)
$avisos = [
  ["titulo" => "INFORMATIVO – REGULARIZAÇÃO DOCUMENTAL", "data" => "06/11/2025", "texto" => "Atualize seus documentos pendentes no portal do aluno."],
  ["titulo" => "ABERTURA DE INSCRIÇÕES – SEMANA ACADÊMICA", "data" => "12/11/2025", "texto" => "Participe das palestras e oficinas abertas a todos os alunos."]
];
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Painel do Aluno - GES</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* ====== LAYOUT BASE ====== */
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background-color: #f4f6f9;
    }
    .layout {
      display: flex;
      height: 100vh;
    }

    /* ====== MENU LATERAL ====== */
    .sidebar {
      width: 250px;
      background-color: #004c97;
      color: white;
      display: flex;
      flex-direction: column;
      padding: 20px;
    }
    .sidebar .logo {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 30px;
      line-height: 1.2;
    }
    .sidebar nav a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 10px;
      border-radius: 6px;
      margin: 4px 0;
      font-size: 15px;
    }
    .sidebar nav a:hover,
    .sidebar nav a.active {
      background: rgba(255,255,255,0.2);
    }
    .logout {
      margin-top: auto;
      color: #ffcccb;
    }

    /* ====== CONTEÚDO PRINCIPAL ====== */
    .content {
      flex: 1;
      background: #f4f6f9;
      padding: 25px;
      overflow-y: auto;
    }

    /* ====== TOPO (INFO DO ALUNO) ====== */
    .topbar {
      background: white;
      border-radius: 12px;
      padding: 20px 25px;
      margin-bottom: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .aluno-info {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .aluno-foto {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      background-color: #ccc;
      background-image: url('https://cdn-icons-png.flaticon.com/512/149/149071.png');
      background-size: cover;
      background-position: center;
    }
    .aluno-dados strong {
      font-size: 18px;
      color: #004c97;
    }
    .aluno-dados small {
      color: #555;
    }
    .status {
      background: #d4edda;
      color: #155724;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 600;
    }

    /* ====== AVISOS ====== */
    .dashboard h2 {
      color: #004c97;
      margin-bottom: 15px;
    }
    .cards {
      display: grid;
      gap: 15px;
    }
    .card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .card-header {
      display: flex;
      justify-content: space-between;
      font-weight: bold;
      color: #004c97;
      margin-bottom: 8px;
    }
    .card p {
      margin: 0;
      color: #333;
      line-height: 1.5;
    }
  </style>
</head>
<body>
  <div class="layout">
    <!-- MENU LATERAL -->
    <aside class="sidebar">
      <div class="logo">GES<br><span style="font-size:14px;">Painel do Aluno</span></div>
      <nav>
        <a href="#" class="active">📢 Avisos</a>
        <a href="#">📅 Calendário</a>
        <a href="#">🧾 Notas</a>
        <a href="#">📚 Disciplinas</a>
        <a href="#">💬 Mensagens</a>
        <a href="#">⚙️ Configurações</a>
        <a href="logout.php" class="logout">Sair</a>
      </nav>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="content">
      <header class="topbar">
        <div class="aluno-info">
          <div class="aluno-foto"></div>
          <div class="aluno-dados">
            <strong><?php echo $nome; ?></strong><br>
            <small><?php echo $curso; ?> | RA: <?php echo $ra; ?></small>
          </div>
        </div>
        <div class="status"><?php echo $status; ?></div>
      </header>

      <section class="dashboard">
        <h2>Avisos Recentes</h2>
        <div class="cards">
          <?php foreach ($avisos as $a): ?>
            <div class="card">
              <div class="card-header">
                <span><?php echo $a["titulo"]; ?></span>
                <span><?php echo $a["data"]; ?></span>
              </div>
              <p><?php echo $a["texto"]; ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
>>>>>>> ed760a3b26fef735defcdad147d9d9fc397fc755
