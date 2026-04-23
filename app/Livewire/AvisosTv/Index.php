<?php

namespace App\Livewire\AvisosTv;

use App\Models\AvisoTv;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Avisos TV')]
class Index extends Component
{
    use WithFileUploads;

    // ── Modal Criar / Editar ──────────────────────────────────
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $titulo = '';

    public string $conteudo = '';

    public string $cor = 'azul';

    public bool $ativo = true;

    public int $ordem = 0;

    public string $layoutImagem = 'lateral';

    // ── Imagem ────────────────────────────────────────────────
    public $novaImagem = null;   // Livewire temp upload

    public ?string $imagemAtual = null; // path salvo na DB

    // ── Modal eliminar ────────────────────────────────────────
    public bool $showDeleteModal = false;

    public ?int $deleteId = null;

    public string $successMessage = '';

    public function getAvisosProperty()
    {
        return AvisoTv::orderBy('ordem')->orderBy('id')->get();
    }

    // ── Abrir modal novo ──────────────────────────────────────
    public function novo(): void
    {
        $this->editingId = null;
        $this->titulo = '';
        $this->conteudo = '';
        $this->cor = 'azul';
        $this->ativo = true;
        $this->ordem = AvisoTv::max('ordem') + 1;
        $this->layoutImagem = 'lateral';
        $this->novaImagem = null;
        $this->imagemAtual = null;
        $this->showModal = true;
        $this->successMessage = '';
        $this->resetValidation();
    }

    // ── Abrir modal editar ────────────────────────────────────
    public function editar(int $id): void
    {
        $aviso = AvisoTv::findOrFail($id);
        $this->editingId = $id;
        $this->titulo = $aviso->titulo;
        $this->conteudo = $aviso->conteudo ?? '';
        $this->cor = $aviso->cor;
        $this->ativo = $aviso->ativo;
        $this->ordem = $aviso->ordem;
        $this->layoutImagem = $aviso->layout_imagem ?? 'lateral';
        $this->imagemAtual = $aviso->imagem;
        $this->novaImagem = null;
        $this->showModal = true;
        $this->successMessage = '';
        $this->resetValidation();
    }

    // ── Guardar ───────────────────────────────────────────────
    public function guardar(): void
    {
        $this->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'conteudo' => ['nullable', 'string', 'max:2000'],
            'cor' => ['required', 'in:azul,verde,amarelo,vermelho,roxo,branco'],
            'ativo' => ['boolean'],
            'ordem' => ['integer', 'min:0'],
            'novaImagem' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:10240'],
            'layoutImagem' => ['required', 'in:lateral,topo'],
        ]);

        $data = [
            'titulo' => trim($this->titulo),
            'conteudo' => trim($this->conteudo) ?: null,
            'cor' => $this->cor,
            'ativo' => $this->ativo,
            'ordem' => $this->ordem,
            'layout_imagem' => $this->layoutImagem,
        ];

        if ($this->novaImagem) {
            // Delete old image if editing
            if ($this->imagemAtual && Storage::disk('public')->exists($this->imagemAtual)) {
                Storage::disk('public')->delete($this->imagemAtual);
            }
            $data['imagem'] = $this->processarImagem($this->novaImagem);
        }

        AvisoTv::updateOrCreate(['id' => $this->editingId], $data);

        $this->showModal = false;
        $this->successMessage = $this->editingId ? 'Aviso atualizado.' : 'Aviso criado.';
        $this->editingId = null;
        $this->novaImagem = null;
        $this->imagemAtual = null;
    }

    // ── Remover imagem sem eliminar aviso ─────────────────────
    public function removerImagem(): void
    {
        if ($this->imagemAtual && Storage::disk('public')->exists($this->imagemAtual)) {
            Storage::disk('public')->delete($this->imagemAtual);
        }
        if ($this->editingId) {
            AvisoTv::where('id', $this->editingId)->update(['imagem' => null]);
        }
        $this->imagemAtual = null;
        $this->novaImagem = null;
        $this->successMessage = 'Imagem removida.';
    }

    // ── Toggle ativo ──────────────────────────────────────────
    public function toggleAtivo(int $id): void
    {
        $aviso = AvisoTv::findOrFail($id);
        $aviso->update(['ativo' => ! $aviso->ativo]);
        $this->successMessage = $aviso->ativo ? 'Aviso ativado.' : 'Aviso desativado.';
    }

    // ── Eliminar ──────────────────────────────────────────────
    public function confirmarEliminar(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
        $this->successMessage = '';
    }

    public function eliminar(): void
    {
        $aviso = AvisoTv::findOrFail($this->deleteId);
        if ($aviso->imagem && Storage::disk('public')->exists($aviso->imagem)) {
            Storage::disk('public')->delete($aviso->imagem);
        }
        $aviso->delete();
        $this->showDeleteModal = false;
        $this->successMessage = 'Aviso eliminado.';
        $this->deleteId = null;
    }

    // ── Processamento de imagem com GD ────────────────────────
    private function processarImagem($file): string
    {
        $tmpPath = $file->getRealPath();
        $mime = $file->getMimeType() ?? '';

        // Abrir com GD conforme tipo
        $src = match (true) {
            str_contains($mime, 'png') => imagecreatefrompng($tmpPath),
            str_contains($mime, 'gif') => imagecreatefromgif($tmpPath),
            str_contains($mime, 'webp') => imagecreatefromwebp($tmpPath),
            default => imagecreatefromjpeg($tmpPath),
        };

        $origW = imagesx($src);
        $origH = imagesy($src);

        // Calcular novas dimensões, máximo 500×500 mantendo proporção
        $maxDim = 500;
        if ($origW > $maxDim || $origH > $maxDim) {
            $ratio = min($maxDim / $origW, $maxDim / $origH);
            $newW = (int) round($origW * $ratio);
            $newH = (int) round($origH * $ratio);
        } else {
            $newW = $origW;
            $newH = $origH;
        }

        $dst = imagecreatetruecolor($newW, $newH);

        // Preservar transparência para PNG/GIF
        if (str_contains($mime, 'png') || str_contains($mime, 'gif')) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($src);

        // Garantir directório
        $dir = storage_path('app/public/avisos');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'avisos/aviso_'.uniqid('', true).'.jpg';
        $fullPath = storage_path('app/public/'.$filename);

        // Guardar como JPEG — compressão única, qualidade 88 (sem re-compressão)
        $quality = 88;
        imagejpeg($dst, $fullPath, $quality);

        // Só reduz qualidade se mesmo assim ultrapassar 1MB
        if (file_exists($fullPath) && filesize($fullPath) > 1024 * 1024) {
            $quality = 75;
            imagejpeg($dst, $fullPath, $quality);
        }

        imagedestroy($dst);

        return $filename;
    }

    public function render()
    {
        return view('livewire.avisos-tv.index', [
            'avisos' => $this->avisos,
        ]);
    }
}
