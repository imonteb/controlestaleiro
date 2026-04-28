---
name: ControlEstaleiroProject
description: "Context and history of the Control Estaleiro shipyard/logistics management project."
---

# Projeto Control Estaleiro (Logística & Segurança)

Este projeto é um sistema de gestão para estaleiros e logística, focado no controlo de equipas, veículos, PEPs (Projetos), EPIs e Guias de Transporte.

## Arquitetura e Tech Stack
- **Framework**: Laravel 12 + Livewire 4
- **UI Components**: Flux UI v2 (`livewire/flux`) + Livewire Blaze
- **Auth**: Laravel Fortify v1 (headless)
- **Frontend Móvel**: PWA customizado via Livewire (`PhoneDisplay.php` / `resources/views/layouts/phone.blade.php`)
- **Base de Dados**: MySQL (Laragon local)
- **Estilos**: TailwindCSS v4 + Vanilla CSS customizado no painel móvel
- **Extras**: Maatwebsite/Excel (exportações), Web Push (notificações)
- **Nota**: Filament NÃO está instalado. Não há multitenancy.

## Componentes Críticos
- `app/Livewire/PhoneDisplay.php`: Cérebro do PWA móvel. Gere Login (Numero/PIN), SOS, pedidos de EPI e Guias.
- `app/Livewire/GestaoGuias.php`: Gestão centralizada das guias no PC.
- `app/Models/GuiaTransporte.php`: Modelo principal para o transporte de bens.
- `app/Models/Colaborador.php`: Modelo de utilizador/funcionário com Numero e PIN.

## Últimos Avanços (Março 2026)
- **Sugerencias Históricas e Persistencia no Móvel**: Implementados datalists para autocompletar Matrícula, Carga e Destino. A persistência evita que os campos sejam limpos após o envio, facilitando entradas repetitivas.
- **Sincronização**: O fluxo de "Guia Móvel -> Guia PC" foi estabilizado com campos de morada, localidade e C. Postal.
- **Correção de Erros**: Resolvidos problemas de reset de formulário e login de colaborador.

## Dados para Testes Locais
- **Colaborador de Teste**: Numero `500`, PIN `1234` (ID 405).
- **Ambiente**: Laragon local `http://127.0.0.1:8000`.

## Próximos Passos Sugeridos
1. Finalizar a exportação de PDF para as guias geradas no móvel.
2. Refinar as notificações de segurança em tempo real.

---
*Este ficheiro serve como ponto de partida para retomar o projeto com Antigravity sem necessidade de re-explicar o contexto.*
