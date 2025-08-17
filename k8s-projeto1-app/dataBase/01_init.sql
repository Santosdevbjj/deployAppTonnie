-- Cria banco e tabela conforme o enunciado do projeto
CREATE DATABASE IF NOT EXISTS meubanco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE meubanco;

CREATE TABLE IF NOT EXISTS mensagem (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  comentario VARCHAR(1000) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Usuário de aplicação com permissão apenas no DB da app
CREATE USER IF NOT EXISTS 'appuser'@'%' IDENTIFIED BY 'mudarDepois';
GRANT SELECT, INSERT, UPDATE, DELETE ON meubanco.* TO 'appuser'@'%';
FLUSH PRIVILEGES;
