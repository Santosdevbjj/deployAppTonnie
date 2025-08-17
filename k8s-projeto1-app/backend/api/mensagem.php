<?php
// API: POST /api/mensagem
header('Content-Type: text/plain; charset=utf-8');

// Validação simples
$input = json_decode(file_get_contents('php://input'), true);
$nome = trim($input['nome'] ?? '');
$email = trim($input['email'] ?? '');
$comentario = trim($input['comentario'] ?? '');

if ($nome === '' || $email === '' || $comentario === '') {
  http_response_code(400);
  exit("Campos nome, email e comentario são obrigatórios.");
}

try {
  // Variáveis de ambiente injetadas pelo Deployment/ConfigMap/Secret
  $dbHost = getenv('DB_HOST') ?: 'mysql-svc.default.svc.cluster.local';
  $dbName = getenv('DB_NAME') ?: 'meubanco';
  $dbUser = getenv('DB_USER') ?: 'appuser';
  $dbPass = getenv('DB_PASS') ?: '';

  $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
  $pdo = new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ]);

  $stmt = $pdo->prepare("INSERT INTO mensagem (nome, email, comentario) VALUES (:n, :e, :c)");
  $stmt->execute([':n'=>$nome, ':e'=>$email, ':c'=>$comentario]);

  echo "Mensagem registrada com sucesso!";
} catch (Throwable $e) {
  http_response_code(500);
  echo "Erro ao gravar: " . $e->getMessage();
}
