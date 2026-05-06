# Segurança — ControlEstaleiro

## Auditoria de Segurança — Maio 2026

### Sprint 1 — Crítico (resolvido)

**1.1 Métodos Livewire do /phone protegidos**
- `GuiasTab::enviar()` e `repetirGuia()` — guard `if (!$this->colaboradorId) return`
- `EpiTab` e `SosTab` — já estavam protegidos (confirmado)
- Ficheiros: `app/Livewire/Phone/GuiasTab.php`

**1.2 Métodos eliminar sem autorização**
- Adicionado `abort(403)` se `!auth()->user()->isAdmin()` em 8 componentes:
  - MaterialCategorias, Localizacoes, TiposTrabalho, Saude
  - Extintores, Epis/Rececoes, Epis/Entregas, LocaisFrequentes

---

### Sprint 2 — Alto (resolvido)

**2.1 Email hardcoded removido do código**
- `GestaoUtilizadores.php` — email movido para `.env` como `ADMIN_EMAIL`

**2.2 $fillable adicionado a modelos sem proteção**
- `AtribuicaoColaborador` — campos: asignacion_id, colaborador_id, rol_en_equipo, equipo_tipo, es_jefe
- `AtribuicaoVeiculo` — campos: asignacion_id, vehiculo_id, equipo_tipo

**2.3 PIN removido do $fillable de Colaborador**
- PIN definido apenas via métodos dedicados, não via mass assignment

**2.4 Validação PIN atual antes de mudar PIN**
- Já estava implementado com `Hash::check()` — confirmado

**2.5 APP_DEBUG e APP_ENV em produção**
- Corrigido para `APP_ENV=production` e `APP_DEBUG=false`

---

### Sprint 3 — Médio (resolvido)

**3.1 Rotas sem middleware de autorização**
- `/tipos-trabalho` e `/localizacoes` movidas para grupo `middleware(['admin'])`
- Qualquer utilizador autenticado já não consegue aceder — apenas admins

**3.2 PIN de 4 dígitos — migração para 6 dígitos**
- Nova migration: campo `force_pin_change boolean DEFAULT true`
- Primeiro acesso obriga a definir novo PIN de 6 dígitos
- Validação atualizada: `digits:4` → `digits:6`
- Input do login: `maxlength="4"` → `maxlength="6"`

**3.3 Trait WithFileUploads duplicado**
- Removido de `Extintores/Index.php`

---

### Pendente — Próxima sprint

**3.4 Validação MIME real nos uploads**
- Usar `finfo` para validar tipo real do ficheiro (não só extensão)
- Afecta: Epis, Extintores, AvisosTv

---

### Boas práticas confirmadas ✅
- CSRF em todos os formulários
- Passwords e PINs com hash bcrypt
- Todas as queries via Eloquent (sem SQL injection)
- 40/40 modelos com `$fillable` definido (após esta auditoria)
- XSS protegido — todo output com `{{ }}`
- `.env` no `.gitignore`
- Transações DB em operações críticas

---

*Auditoria realizada em Maio 2026*
