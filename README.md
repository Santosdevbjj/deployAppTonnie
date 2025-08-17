## Criando um Deploy de uma Aplicação.
---
![TonnieJava0002](https://github.com/user-attachments/assets/e67eb8a5-c9a3-44b8-bd6e-fe4bd4c81adc)

**Bootcamp TONNIE - Java and AI in Europe.**
---

**DESCRIÇÃO:**
Neste projeto será realizado um deploy de uma aplicação completa com frontend, backend e database mysql. No desenvolvimento do projeto serão criadas as imagens dos containeres e serviços necessários no kubernetes para que a aplicação esteja pronta para produção.

---


# 🚀 Deploy Kubernetes (Minikube) — Frontend + Backend (PHP) + MySQL

Este projeto demonstra como implantar uma aplicação **full stack** em **Kubernetes (Minikube)**, com **Frontend (Nginx)**, **Backend (PHP + Apache)** e **Banco de Dados MySQL**, utilizando **Docker**, **Kubernetes Manifests** e **CI/CD com GitHub Actions**.

---

## 📂 Estrutura do Projeto

Abaixo está a lista completa de diretórios e arquivos, com suas respectivas descrições:

### 📌 Frontend (`k8s-projeto1-app/frontend/`)

- **index.html**  
  Página principal da aplicação. Contém o formulário para o usuário enviar `nome`, `email` e `comentário`.

- **css.css**  
  Estilos básicos aplicados ao formulário e layout da página (responsividade simples, padding, fontes).

- **js.js**  
  Script em JavaScript que captura o evento do formulário, envia os dados via `fetch` para o endpoint `/api/mensagem` e exibe a resposta no navegador.

- **nginx.conf**  
  Configuração personalizada do Nginx:  
  - Servir os arquivos estáticos (`index.html`, CSS e JS).  
  - Criar um proxy reverso para `/api/*` direcionando para o backend (`backend-svc` dentro do cluster).  
  Isso evita problemas de CORS e expõe apenas o frontend para o usuário externo.

- **Dockerfile**  
  Define a imagem Docker do frontend:  
  - Baseada no `nginx:alpine`.  
  - Copia os arquivos estáticos e a configuração customizada.  
  - Expõe a porta 8080.  

---

### 📌 Backend (`k8s-projeto1-app/backend/`)

- **public/index.php**  
  Endpoint de healthcheck simples. Responde `"OK - backend online"`, usado nas probes do Kubernetes.

- **api/mensagem.php**  
  API responsável por receber o `POST` do formulário do frontend.  
  - Valida os campos.  
  - Conecta ao MySQL (usando variáveis de ambiente injetadas pelo Kubernetes).  
  - Insere os dados na tabela `mensagem`.  
  - Retorna mensagem de sucesso ou erro.  

- **Dockerfile**  
  Define a imagem Docker do backend:  
  - Baseada em `php:8.2-apache`.  
  - Instala `pdo_mysql` para comunicação com MySQL.  
  - Copia os arquivos PHP para o Apache.  
  - Expõe a porta 80.  

---

### 📌 Banco de Dados (`k8s-projeto1-app/dataBase/`)

- **01_init.sql**  
  Script de inicialização do MySQL. Executado automaticamente na primeira vez que o contêiner roda com um volume vazio.  
  - Cria o banco `meubanco`.  
  - Cria a tabela `mensagem` com colunas `id`, `nome`, `email`, `comentario`, `created_at`.  
  - Cria o usuário `appuser` com permissões apenas no banco `meubanco`.

---

### 📌 Kubernetes Manifests (`k8s-projeto1-app/k8s/`)

- **namespace.yaml** *(opcional)*  
  Define um namespace separado para os recursos da aplicação, caso queira isolar do `default`.

- **mysql-secret.yaml**  
  Armazena as senhas do MySQL em formato seguro (`base64`).  
  - `MYSQL_ROOT_PASSWORD` → senha do root.  
  - `MYSQL_APP_PASSWORD` → senha do usuário `appuser`.

- **mysql-configmap.yaml**  
  Armazena variáveis de configuração não sensíveis:  
  - Nome do banco (`meubanco`).  
  - Nome do usuário (`appuser`).

- **mysql-pvc.yaml**  
  Define um `PersistentVolumeClaim` de 2Gi para armazenar os dados do MySQL de forma persistente, mesmo que o Pod seja recriado.

- **mysql-deployment.yaml**  
  Deployment e Service do MySQL:  
  - Usa a imagem oficial `mysql:8.0`.  
  - Monta volume persistente.  
  - Monta ConfigMap com o SQL de inicialização.  
  - Service do tipo `ClusterIP` para comunicação interna.  

- **backend-deployment.yaml**  
  Deployment e Service do Backend:  
  - Cria 2 réplicas do backend.  
  - Injeta as variáveis de conexão ao MySQL (ConfigMap + Secret).  
  - Define `readinessProbe` e `livenessProbe`.  
  - Service do tipo `ClusterIP` expõe a porta 8080 dentro do cluster.

- **frontend-deployment.yaml**  
  Deployment e Service do Frontend:  
  - Cria 2 réplicas do frontend.  
  - Executa o Nginx na porta 8080.  
  - Service do tipo `NodePort`, permitindo acesso externo via `minikube service frontend-svc --url`.

- **ingress.yaml** *(opcional)*  
  Define um Ingress para expor o frontend via hostname (`app.local`).  
  Requer habilitar o addon `ingress` no Minikube ou usar um controlador de ingress em produção.

---

### 📌 CI/CD Workflow (`.github/workflows/ci-cd.yml`)

- Pipeline de **GitHub Actions** que automatiza o ciclo de vida:  
  1. Faz checkout do repositório.  
  2. Faz login no Docker Hub (usando secrets configurados no GitHub).  
  3. Builda e publica imagens do `backend` e `frontend` no Docker Hub.  
  4. (Opcional) Aplica os manifests no cluster Kubernetes, caso você configure o secret `KUBECONFIG`.  

Secrets necessários no repositório:  
- `DOCKER_HUB_USER` → usuário do Docker Hub.  
- `DOCKER_HUB_TOKEN` → token de acesso do Docker Hub.  
- `KUBECONFIG` → (opcional) conteúdo do kubeconfig do cluster para deploy automático.  

---

## 🚀 Fluxo de uso

1. Build automático de imagens → via GitHub Actions.  
2. Publicação no Docker Hub → `sergiosantos/frontend-k8s:latest` e `sergiosantos/backend-k8s:latest`.  
3. Deploy no Kubernetes → manual (`kubectl apply -f k8s/`) ou automático via pipeline.  
4. Acesso →  
   - `minikube service frontend-svc --url` (NodePort)  
   - ou `http://app.local` (Ingress habilitado).  

---

## 📌 Conclusão

Com este projeto você tem um **exemplo completo** de:  
- Construção de imagens Docker.  
- Deploy de uma aplicação com Frontend + Backend + Banco no Kubernetes.  
- Persistência de dados com PVC.  
- Injeção segura de variáveis com ConfigMaps e Secrets.  
- Automação de build e deploy com GitHub Actions.


