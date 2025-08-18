## Criando um Deploy de uma Aplicação.
---
**Bootcamp TONNIE - Java and AI in Europe.**

![TonnieJava0002](https://github.com/user-attachments/assets/3ae12dba-8fc5-44dc-8acb-ca033b27d4ca)

---

**DESCRIÇÃO:**
Neste projeto será realizado um deploy de uma aplicação completa com frontend, backend e database mysql. No desenvolvimento do projeto serão criadas as imagens dos containeres e serviços necessários no kubernetes para que a aplicação esteja pronta para produção.

---


# 🚀 Projeto Kubernetes - Deploy de Aplicação Completa

![CI/CD](https://github.com/Santosdevbjj/deployAppTonnie/actions/workflows/ci-cd.yml/badge.svg)

Este projeto implementa uma aplicação **Fullstack** (Frontend + Backend + Banco MySQL) usando **Docker e Kubernetes (Minikube)**, com pipeline de **CI/CD no GitHub Actions**.

A proposta é entregar uma aplicação pronta para produção, versionada e automatizada, com boas práticas de DevOps e documentação didática.

---

## 📌 Arquitetura do Projeto

A arquitetura é composta por três camadas principais:

1. **Frontend** → Interface do usuário em HTML, CSS e JS servida via Nginx.  
2. **Backend** → Aplicação PHP que processa requisições e se conecta ao banco.  
3. **Banco de Dados (MySQL)** → Armazena os comentários enviados pelo frontend.  

O Kubernetes gerencia cada camada com **Deployments, Services, ConfigMaps, Secrets, PVCs e Ingress**.

---

## 📂 Estrutura de Pastas

k8s-projeto1-app/ ├── frontend/                  # Código e Dockerfile do Frontend ├── backend/                   # Código e Dockerfile do Backend ├── api/                       # API PHP de mensagens ├── dataBase/                  # Scripts SQL de inicialização ├── k8s/                       # Manifests Kubernetes └── .github/workflows/         # Pipeline CI/CD

---

## 🖥️ Frontend

### 📄 `frontend/index.html`
- Página inicial da aplicação.  
- Contém formulário para envio de **nome, email e comentário**.  
- Consome a API do backend para exibir mensagens armazenadas no MySQL.

### 📄 `frontend/css.css`
- Folha de estilos da aplicação.  
- Define layout, cores e responsividade da interface.

### 📄 `frontend/js.js`
- Script em JavaScript que conecta o frontend à API do backend via AJAX/Fetch.  
- Responsável por enviar dados do formulário e listar os comentários.

### 📄 `frontend/nginx.conf`
- Configuração personalizada do **Nginx**.  
- Define o **root**, tratamento de erros e roteamento para `index.html`.

### 📄 `frontend/Dockerfile`
- Constrói a imagem Docker do frontend.  
- Copia arquivos estáticos (HTML, CSS, JS) para dentro do Nginx.  
- Expõe a porta `8080`.

---

## ⚙️ Backend

### 📄 `backend/public/index.php`
- Arquivo inicial do backend.  
- Recebe as requisições vindas do frontend.  
- Interage com o banco MySQL para buscar ou inserir dados.

### 📄 `api/mensagem.php`
- Implementa a **API de mensagens**.  
- Funções principais:
  - `GET` → Lista mensagens do banco.  
  - `POST` → Insere nova mensagem enviada pelo frontend.  

---

## 🗄️ Banco de Dados

### 📄 `dataBase/01_init.sql`
- Script de inicialização do banco.  
- Cria o banco `meubanco` e a tabela `mensagem`.  

Tabela:
```sql
CREATE TABLE mensagem (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100),
  email VARCHAR(100),
  comentario VARCHAR(255)
);


---

☸️ Kubernetes Manifests

Todos os manifests estão em k8s/.

📄 namespace.yaml

Cria um namespace isolado para o projeto.


📄 mysql-secret.yaml

Contém credenciais sensíveis (usuário e senha do banco).

Usado no backend e no MySQL Deployment.


📄 mysql-configmap.yaml

Define variáveis de configuração do MySQL, como nome do banco (meubanco).


📄 mysql-pvc.yaml

Cria um PersistentVolumeClaim para garantir persistência de dados do banco.


📄 mysql-deployment.yaml

Define o Deployment e Service do MySQL.

Configura ambiente, credenciais e volume persistente.


📄 backend-deployment.yaml

Cria o Deployment do backend.

imagePullPolicy: Always → sempre puxa a versão mais recente da imagem.

Configura variáveis de ambiente para conexão com MySQL.

Define readinessProbe e livenessProbe.

Cria o Service backend-svc (ClusterIP).


📄 frontend-deployment.yaml

Cria o Deployment do frontend.

imagePullPolicy: Always para evitar cache.

Service frontend-svc exposto via NodePort (acessível no Minikube).


📄 ingress.yaml

Cria um Ingress que roteia requisições para frontend-svc e backend-svc.

Facilita o acesso usando hostnames configurados no Minikube.



---

🤖 CI/CD - GitHub Actions

Arquivo: .github/workflows/ci-cd.yml

Fluxo do pipeline:

1. test-build

Faz build local do backend e frontend (docker build) sem push.

Garante que os Dockerfiles estão corretos.



2. build-backend

Faz login no Docker Hub.

Publica a imagem do backend (latest + hash do commit).



3. build-frontend

Faz login no Docker Hub.

Publica a imagem do frontend (latest + hash do commit).



4. validate-k8s

Roda yamllint para validar formatação dos YAMLs.

Roda kubeval para validar schemas Kubernetes.

Roda kubectl apply --dry-run=client para validar aplicação no cluster.





---

▶️ Como Executar Localmente (Minikube)

1. Inicie o Minikube

minikube start


2. Crie os recursos

kubectl apply -f k8s/


3. Verifique os pods

kubectl get pods -n default


4. Acesse o frontend

minikube service frontend-svc




---

📦 CI/CD e Deploy

O pipeline roda automaticamente em cada push ou pull request para main.

Publica imagens no Docker Hub (DOCKER_HUB_USER/backend-k8s e frontend-k8s).

Valida os manifests antes do deploy.



---

✨ Tecnologias Utilizadas

Docker → Containerização.

Kubernetes (Minikube) → Orquestração.

Nginx → Servidor web para frontend.

PHP → Backend simples.

MySQL → Banco de dados relacional.

GitHub Actions → CI/CD automatizado.



---

👨‍💻 Autor

Projeto desenvolvido por Sérgio Santos

💼 LinkedIn

🐙 GitHub


---

👉 Projeto desenvolvido durante o Bootcamp TONNIE - Java and AI in Europe.


