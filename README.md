![TonnieJava0002](https://github.com/user-attachments/assets/3ae12dba-8fc5-44dc-8acb-ca033b27d4ca)

# ☸️ Deploy de Aplicação Fullstack com Kubernetes

> **Bootcamp TONNIE — Java and AI in Europe**

![CI/CD](https://github.com/Santosdevbjj/deployAppTonnie/actions/workflows/ci-cd.yml/badge.svg)

---

## 1. 🧩 Problema de Negócio

Subir uma aplicação em produção vai muito além de escrever código funcional. A maioria dos desenvolvedores sabe criar um sistema — poucos sabem **colocar esse sistema no ar de forma confiável, automatizada e resiliente**.

As dores mais comuns nesse cenário são:

- Aplicações que "funcionam na minha máquina" mas falham em produção por divergência de ambiente;
- Deploys manuais e frágeis, onde um erro humano pode derrubar o sistema;
- Ausência de recuperação automática quando um serviço cai;
- Imagens Docker desatualizadas em produção por falta de pipeline de CI/CD.

> **O que este projeto resolve:** Como estruturar, containerizar e orquestrar uma aplicação Fullstack completa (Frontend + Backend + Banco de Dados) com Kubernetes, garantindo que o deploy seja automatizado, versionado e seguro desde o primeiro commit.

---

## 2. 📌 Contexto

O projeto foi desenvolvido como parte do Bootcamp TONNIE — Java and AI in Europe, com foco em **práticas reais de DevOps e deploy em Kubernetes**.

A aplicação em si é uma plataforma simples de envio e listagem de comentários — propositalmente simples para que a atenção esteja na **infraestrutura, não na lógica de negócio**. O desafio real é:

- Fazer o Frontend (Nginx + HTML/CSS/JS) conversar com o Backend (PHP);
- Fazer o Backend se conectar ao MySQL de forma segura, via Secrets do Kubernetes;
- Garantir que os dados do banco **persistam** mesmo que o pod seja reiniciado (PVC);
- Automatizar todo o ciclo de build, publicação e validação via GitHub Actions.

A arquitetura entregue é um modelo replicável para qualquer aplicação Fullstack que precise ser colocada em produção com Kubernetes.

---

## 3. 📐 Premissas da Solução

Para garantir que o projeto refletisse um cenário de produção real, as seguintes premissas foram adotadas:

- Cada camada da aplicação (Frontend, Backend, Banco) roda em seu próprio **container isolado**, gerenciado pelo Kubernetes como um Deployment independente;
- Credenciais do banco de dados **nunca aparecem em texto puro** nos manifestos — são gerenciadas exclusivamente via `Secret` do Kubernetes;
- O banco de dados usa **PersistentVolumeClaim (PVC)** para garantir que os dados sobrevivam a reinicializações de pods;
- O pipeline de CI/CD roda em **todo push e pull request para a branch `main`**, validando os manifestos antes de qualquer deploy;
- O ambiente local utiliza **Minikube**, que replica o comportamento de um cluster Kubernetes de produção sem custo de infraestrutura.

---

## 4. ⚙️ Estratégia da Solução

A construção seguiu uma abordagem progressiva — da aplicação ao pipeline:

**Passo 1 — Containerização das camadas**
Criação de `Dockerfiles` individuais para Frontend (Nginx servindo arquivos estáticos) e Backend (PHP com Apache). O MySQL usa a imagem oficial sem customização.

**Passo 2 — Manifests Kubernetes**
Criação de todos os objetos necessários na pasta `k8s/`:
- `namespace.yaml` → isolamento do projeto no cluster;
- `mysql-secret.yaml` → credenciais encriptadas do banco;
- `mysql-configmap.yaml` → variáveis de configuração reutilizáveis;
- `mysql-pvc.yaml` → volume persistente para os dados do MySQL;
- `mysql-deployment.yaml` → Deployment + Service do banco;
- `backend-deployment.yaml` → Deployment com `readinessProbe` e `livenessProbe` + Service ClusterIP;
- `frontend-deployment.yaml` → Deployment + Service NodePort para acesso externo;
- `ingress.yaml` → roteamento de tráfego por hostname.

**Passo 3 — Pipeline CI/CD com GitHub Actions**
Configuração do workflow `.github/workflows/ci-cd.yml` com quatro jobs encadeados: validação de build, publicação das imagens no Docker Hub com tag `latest` e hash do commit, e validação dos manifestos Kubernetes com `yamllint`, `kubeval` e `kubectl --dry-run`.

**Passo 4 — Validação end-to-end no Minikube**
Execução completa do fluxo: `minikube start` → `kubectl apply -f k8s/` → acesso ao frontend via `minikube service` → envio e listagem de comentários validados contra o banco MySQL.

---

## 5. 💡 Insights Técnicos

Os maiores aprendizados vieram dos problemas que não estavam no tutorial:

- **`imagePullPolicy: Always` é obrigatório em desenvolvimento.** Sem ele, o Kubernetes usa a imagem em cache local — que pode ser uma versão antiga. Isso gerou horas de debug antes de entender que o container rodava código desatualizado.

- **`readinessProbe` e `livenessProbe` não são opcionais em produção.** Sem eles, o Kubernetes envia tráfego para o backend antes que ele esteja pronto para responder, gerando erros intermitentes impossíveis de reproduzir localmente.

- **A ordem de aplicação dos manifestos importa.** O `Secret` e o `ConfigMap` precisam existir antes do `Deployment` do MySQL, que por sua vez precisa estar rodando antes do `Deployment` do backend. Aplicar tudo com `kubectl apply -f k8s/` em ordem alfabética gerou erros de dependência — a solução foi nomear os arquivos com prefixo numérico para controlar a ordem.

- **Secrets do Kubernetes não são criptografia — são apenas Base64.** Para produção real, o próximo passo é usar um gerenciador de segredos externo (como HashiCorp Vault ou AWS Secrets Manager). Entender essa limitação durante o projeto mudou a forma de pensar segurança em Kubernetes.

- **O Ingress no Minikube exige o addon habilitado explicitamente.** `minikube addons enable ingress` não é mencionado na maioria dos tutoriais — mas sem ele, o Ingress Controller não existe e nenhuma regra de roteamento funciona.

---

## 6. 📊 Resultados

| Entregável | Status |
|---|---|
| Frontend containerizado com Nginx | ✅ |
| Backend PHP containerizado | ✅ |
| MySQL com volume persistente (PVC) | ✅ |
| Credenciais gerenciadas via Secret | ✅ |
| Comunicação entre serviços via ClusterIP | ✅ |
| Acesso externo via NodePort e Ingress | ✅ |
| Pipeline CI/CD com GitHub Actions | ✅ |
| Publicação automática de imagens no Docker Hub | ✅ |
| Validação de manifestos com `kubeval` e `dry-run` | ✅ |

**Estrutura do repositório:**

<img width="952" height="1651" alt="Screenshot_20250818-014114" src="https://github.com/user-attachments/assets/06be37a5-405f-4863-b467-ddea0f26a388" />

**Schema do banco de dados:**

```sql
CREATE TABLE mensagem (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  nome      VARCHAR(100),
  email     VARCHAR(100),
  comentario VARCHAR(255)
);
```

---

## 7. 🚀 Próximos Passos

O projeto tem base sólida para evoluir em direção a um ambiente de produção real:

- [ ] **Helm Charts** — substituir os manifestos YAML manuais por um Helm Chart parametrizável, facilitando deploy em múltiplos ambientes (dev, staging, prod);
- [ ] **Gerenciamento de segredos externo** — integrar HashiCorp Vault ou AWS Secrets Manager para substituir os Secrets do Kubernetes em produção;
- [ ] **HorizontalPodAutoscaler (HPA)** — configurar escalonamento automático do backend com base em uso de CPU;
- [ ] **Deploy em cluster cloud** — migrar do Minikube para um cluster real (GKE, EKS ou AKS) para validar o comportamento em produção;
- [ ] **Monitoramento com Prometheus + Grafana** — adicionar observabilidade para métricas de latência, disponibilidade e uso de recursos;
- [ ] **Rollback automatizado** — configurar o pipeline para fazer rollback automático se os health checks falharem após o deploy.

---

## 🛠️ Tecnologias Utilizadas

| Tecnologia | Função no projeto |
|---|---|
| Docker | Containerização do Frontend e Backend |
| Kubernetes (Minikube) | Orquestração local dos containers |
| Nginx | Servidor web para o Frontend |
| PHP | Backend da aplicação |
| MySQL | Banco de dados relacional com persistência via PVC |
| GitHub Actions | Pipeline CI/CD automatizado |
| Docker Hub | Registro de imagens Docker |
| `yamllint` + `kubeval` | Validação de manifestos Kubernetes |

---

## 🔧 Como Executar Localmente

**Pré-requisitos:**
- Docker instalado e rodando
- Minikube instalado
- `kubectl` configurado

**1. Inicie o Minikube e habilite o Ingress**
```bash
minikube start
minikube addons enable ingress
```

**2. Aplique os manifestos**
```bash
kubectl apply -f k8s/
```

**3. Verifique se os pods estão rodando**
```bash
kubectl get pods -n default
```

**4. Acesse o Frontend**
```bash
minikube service frontend-svc
```

---

## 🤖 CI/CD — Fluxo do Pipeline

O pipeline roda automaticamente em cada push ou pull request para `main`:

```
push → main
   │
   ├── test-build         → build local sem push (valida os Dockerfiles)
   ├── build-backend      → publica imagem no Docker Hub (latest + hash do commit)
   ├── build-frontend     → publica imagem no Docker Hub (latest + hash do commit)
   └── validate-k8s       → yamllint + kubeval + kubectl dry-run
```

---

## 📚 Aprendizados

Este foi o projeto que mais mudou minha visão sobre o que significa "colocar uma aplicação em produção".

Antes deste projeto, eu achava que deploy era basicamente copiar arquivos para um servidor. Depois dele, entendi que **deploy é um processo de engenharia** — com controle de versão de imagens, gerenciamento de segredos, health checks, persistência de dados e automação de validação.

O erro que mais me custou tempo foi não entender a diferença entre `ClusterIP` e `NodePort` no início. O backend precisa ser `ClusterIP` (acessível apenas dentro do cluster), o frontend precisa de `NodePort` (acessível externamente via Minikube). Acertar essa arquitetura de rede foi o momento em que o projeto começou a fazer sentido de verdade.

Se fosse recomeçar, começaria pelos manifestos Kubernetes antes de escrever qualquer linha de código da aplicação — definir a infraestrutura primeiro força a pensar na arquitetura de forma mais clara.

---

## 👤 Autor

**Sérgio Santos**



[![Portfólio](https://img.shields.io/badge/Portfólio-Sérgio_Santos-111827?style=for-the-badge&logo=githubpages&logoColor=00eaff)](https://portfoliosantossergio.vercel.app)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-Sérgio_Santos-0A66C2?style=for-the-badge&logo=linkedin&logoColor=white)](https://linkedin.com/in/santossergioluiz)

---

