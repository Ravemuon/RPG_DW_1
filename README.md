# 🎲 Sistema de RPG - Gerenciador de Campanhas e Personagens

[![Status do Projeto](https://img.shields.io/badge/status-em%20desenvolvimento-yellow)]()
[![IFSC](https://img.shields.io/badge/IFSC-Chapec%C3%B3-blue)]()
[![Laravel](https://img.shields.io/badge/Laravel-11.x-red)]()
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple)]()
[![License](https://img.shields.io/badge/license-MIT-green)]()

---

## 📋 Sobre o Projeto

Este projeto consiste em um **Sistema de Gerenciamento de RPG** desenvolvido como parte da disciplina de **Desenvolvimento de Aplicações Web** no **IFSC - Câmpus Chapecó**. O sistema permite que mestres e jogadores gerenciem campanhas, personagens, itens, magias e sessões de jogo, com geração de relatórios e gráficos para análise de desempenho e progressão.

O desenvolvimento segue o padrão **MVC (Model-View-Controller)** utilizando o framework **Laravel**, aplicando conceitos de **Programação Orientada a Objetos**, **Eloquent ORM** e **relacionamentos entre tabelas (1:1, 1:N, N:N)**.

---

## 👤 Credenciais de Acesso

### Administrador
Acesso total ao sistema, incluindo páginas administrativas e gerenciamento de dados.

| Campo | Valor |
| :--- | :--- |
| **E-mail** | `admin@teste.com` |
| **Senha** | `admin123` |

### Usuário Comum
Acesso às funcionalidades padrão do sistema (visualização de personagens, campanhas, etc.).

| Campo | Valor |
| :--- | :--- |
| **E-mail** | `jogador@teste.com` |
| **Senha** | `password` |

---

## 🚀 Funcionalidades Principais

### 👥 Autenticação e Usuários
- Registro e login de usuários.
- Perfis de **Administrador** e **Jogador**.
- Controle de acesso baseado em permissões.

### 🧙 Personagens
- CRUD completo de personagens (Nome, Raça, Classe, Nível, Atributos).
- Relacionamento N:N entre personagens e itens/magias.

### 🗺️ Campanhas e Sessões
- Criação de campanhas com descrição, cenário e objetivos.
- Sessões de jogo com registro de data, participantes e resumo.

### 📦 Itens e Magias
- Cadastro de itens (nome, tipo, raridade, bônus).
- Cadastro de magias (nome, escola, nível, descrição).
- Vinculação de itens e magias aos personagens.

### 📊 Dashboards e Relatórios
- Gráficos com evolução de níveis dos personagens (Chart.js).
- Relatórios em PDF com ficha completa do personagem (DomPDF).
- Métricas gerais: total de personagens, campanhas ativas, sessões realizadas.

---

## 🛠️ Tecnologias Utilizadas

### Back-end
- **PHP 8.2+**
- **Laravel 11.x**
- **Eloquent ORM** (relacionamentos 1:1, 1:N, N:N)
- **MySQL** (banco de dados relacional)

### Front-end
- **Blade** (template engine do Laravel)
- **Bootstrap 5** (estilização e responsividade)
- **Chart.js** (gráficos interativos)
- **DomPDF / Laravel-Snappy** (geração de relatórios)

### Ferramentas
- **Composer** (gerenciador de dependências PHP)
- **NPM** (gerenciador de pacotes front-end)
- **Git & GitHub** (controle de versão)
- **Visual Studio Code** (editor de código)

---

## 📥 Como Executar o Projeto Localmente

### Pré-requisitos

Antes de começar, certifique-se de ter instalado:

| Ferramenta | Download | Finalidade |
| :--- | :--- | :--- |
| **Laravel Herd** | [herd.laravel.com](https://herd.laravel.com/windows) | Ambiente PHP + Nginx para Laravel |
| **Laragon** | [laragon.org](https://laragon.org/download) | Ambiente completo (Apache, MySQL, PHP) |
| **Visual Studio Code** | [code.visualstudio.com](https://code.visualstudio.com/) | Editor de código |

---

### Passo a Passo

#### 1️⃣ Clone o repositório
```bash
git clone https://github.com/Ravemuon/rpg-sistema-laravel.git
cd rpg-sistema-laravel
