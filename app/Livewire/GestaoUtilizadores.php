<?php

namespace App\Livewire;

use App\Models\User;
use App\Traits\HasMobileSignature;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gestão de Utilizadores')]
class GestaoUtilizadores extends Component
{
    use HasMobileSignature;

    private static function protectedEmail(): string
    {
        return env('ADMIN_EMAIL', '');
    }

    private function isProtected(User $user): bool
    {
        // El email protegido siempre es intocable para otros.
        if ($user->email === self::protectedEmail() && \Auth::user()->email !== self::protectedEmail()) {
            return true;
        }

        // Un admin no puede tocar a un super_admin ni a otro admin (pero sí a sí mismo).
        if (\Auth::user()->isAdmin() && ! \Auth::user()->isSuperAdmin()) {
            if ($user->id !== \Auth::id() && ($user->isSuperAdmin() || $user->isAdmin())) {
                return true;
            }
        }

        return false;
    }

    // ── Listagem ──────────────────────────────────────────────
    public string $search = '';

    // ── Modal editar ─────────────────────────────────────────
    public bool $showEditModal = false;

    public ?int $editingId = null;

    public string $editName = '';

    public string $editEmail = '';

    public array $editRoles = [];

    public string $editEmployeeNumber = '';

    public string $editSignature = '';

    // ── Modal palavra-passe ──────────────────────────────────
    public bool $showPasswordModal = false;

    public ?int $passwordUserId = null;

    public string $newPassword = '';

    public string $newPasswordConfirmation = '';

    // ── Modal borrar ─────────────────────────────────────────
    public bool $showDeleteModal = false;

    public ?int $deleteId = null;

    public string $successMessage = '';

    // ── Listagem ──────────────────────────────────────────────
    public function getUsersProperty()
    {
        return User::query()
            ->when($this->search, fn ($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->orderBy('name')
            ->get();
    }

    // ── Abrir modal editar ───────────────────────────────────
    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);
        if ($this->isProtected($user)) {
            return;
        }
        $this->editingId = $id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editEmployeeNumber = $user->employee_number ?? '';
        $this->editSignature = $user->signature ?? '';

        // Estandarizar roles al abrir
        $this->editRoles = $user->role ?? [User::ROLE_OPERARIO];

        // Asegurar que solo hay roles válidos
        $availableRoles = [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_EPI, User::ROLE_LOGI, User::ROLE_OPERARIO];
        $this->editRoles = array_intersect($this->editRoles, $availableRoles);

        if (empty($this->editRoles)) {
            $this->editRoles = [User::ROLE_OPERARIO];
        }

        $this->showEditModal = true;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function saveEdit(): void
    {
        $user = User::findOrFail($this->editingId);
        if ($this->isProtected($user)) {
            return;
        }

        $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editEmail' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingId)],
            'editRoles' => ['required', 'array', 'min:1'],
            'editRoles.*' => [Rule::in([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_EPI, User::ROLE_LOGI, User::ROLE_OPERARIO])],
            'editEmployeeNumber' => ['nullable', 'string', 'max:50'],
            'editSignature' => ['nullable', 'string'],
        ], [
            'editRoles.*.in' => 'Um dos papéis selecionados é inválido.',
        ]);

        // Restricción extra: solo super_admin puede AÑADIR roles admin o super_admin que el usuario no tenía ya.
        /** @var User $currentUser */
        $currentUser = \Auth::user();
        if (! $currentUser->isSuperAdmin()) {
            $existingRoles = $user->role ?? [];
            $adminRolesAdded = array_diff(
                array_intersect($this->editRoles, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN]),
                $existingRoles
            );
            if (! empty($adminRolesAdded)) {
                $this->addError('editRoles', 'Solo un Super Administrador puede asignar roles administrativos.');

                return;
            }
        }

        $user->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
            'role' => $this->editRoles,
            'employee_number' => $this->editEmployeeNumber ?: null,
            'signature' => $this->editSignature ?: null,
        ]);

        $this->showEditModal = false;
        $this->successMessage = 'Utilizador atualizado com sucesso.';
    }

    public function startMobileSignature(): void
    {
        if (! $this->editingId) {
            return;
        }
        $user = User::findOrFail($this->editingId);

        $metadata = [
            'colaborador_nombre' => $user->name,
            'colaborador_numero' => $user->employee_number,
            'type' => 'user_signature',
        ];

        $this->generateSignatureRequest($user, $metadata);
    }

    public function onSignatureCompleted($request): void
    {
        $this->editSignature = $request->signature_data;
        $this->dispatch('signature-ready', signature: $request->signature_data);
    }

    // ── Abrir modal contraseña ───────────────────────────────
    public function openResetPassword(int $id): void
    {
        $user = User::findOrFail($id);
        if ($this->isProtected($user)) {
            return;
        }

        $this->passwordUserId = $id;
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';
        $this->showPasswordModal = true;
        $this->successMessage = '';
    }

    public function savePassword(): void
    {
        $user = User::findOrFail($this->passwordUserId);
        if ($this->isProtected($user)) {
            return;
        }

        $this->validate([
            'newPassword' => ['required', 'string', 'min:8', 'same:newPasswordConfirmation'],
            'newPasswordConfirmation' => ['required', 'string'],
        ], [
            'newPassword.required' => 'A palavra-passe é obrigatória.',
            'newPassword.min' => 'A palavra-passe deve ter pelo menos 8 caracteres.',
            'newPassword.same' => 'As palavras-passe não coincidem.',
            'newPasswordConfirmation.required' => 'A confirmação é obrigatória.',
        ]);

        User::findOrFail($this->passwordUserId)->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->showPasswordModal = false;
        $this->successMessage = 'Palavra-passe atualizada com sucesso.';
    }

    // ── Eliminar utilizador ──────────────────────────────────
    public function confirmDelete(int $id): void
    {
        /** @var \App\Models\User|null $currentUser */
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        if ($id === (int) ($currentUser->id ?? 0)) {
            return;
        } // não se pode eliminar a si próprio
        $user = User::findOrFail($id);
        if ($this->isProtected($user)) {
            return;
        }
        $this->deleteId = $id;
        $this->showDeleteModal = true;
        $this->successMessage = '';
    }

    public function deleteUser(): void
    {
        if ($this->deleteId === (int) \Illuminate\Support\Facades\Auth::id()) {
            return;
        }
        $user = User::findOrFail($this->deleteId);
        if ($this->isProtected($user)) {
            return;
        }
        $user->delete();
        $this->showDeleteModal = false;
        $this->successMessage = 'Utilizador eliminado.';
    }

    public function render()
    {
        return view('livewire.gestao-utilizadores', [
            'users' => $this->users,
        ]);
    }
}
