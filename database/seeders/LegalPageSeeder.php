<?php

namespace Database\Seeders;

use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class LegalPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $privacidade = <<<'HTML'
                    <div class="space-y-10 animate-in fade-in zoom-in-95 duration-500">

                        {{-- Responsável pelo Tratamento --}}
                        <section class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-800 flex items-center justify-center text-blue-400 font-black text-xs">01</div>
                                <h3 class="text-xs font-black text-white uppercase tracking-widest">Responsável pelo Tratamento</h3>
                            </div>
                            <p class="text-sm text-white/70 leading-relaxed font-medium pl-11">
                                <strong class="text-white">Construção e Manutenção Electromecânica S.A.</strong><br>
                                NIF 501 369 295 · Lagoas Park, Edifício 11, Piso 0 · 2740-270 Porto Salvo<br>
                                Email: <a href="mailto:cme@cme.pt" class="text-blue-400 underline">cme@cme.pt</a>
                            </p>
                        </section>

                        {{-- DPO --}}
                        <section class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-800 flex items-center justify-center text-blue-400 font-black text-xs">02</div>
                                <h3 class="text-xs font-black text-white uppercase tracking-widest">Encarregado de Proteção de Dados (DPO)</h3>
                            </div>
                            <p class="text-sm text-white/70 leading-relaxed font-medium pl-11">
                                John Doe · <a href="mailto:jhon@.test.com" class="text-blue-400 underline">jhon@.test.com</a>
                            </p>
                        </section>

                        {{-- Dados Tratados --}}
                        <section class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-800 flex items-center justify-center text-blue-400 font-black text-xs">03</div>
                                <h3 class="text-xs font-black text-white uppercase tracking-widest">Dados Tratados</h3>
                            </div>
                            <ul class="text-sm text-white/70 leading-relaxed font-medium pl-11 list-disc list-inside space-y-1">
                                <li>Identificação profissional (Nº Colaborador, PIN cifrado)</li>
                                <li>Registos operacionais: EPI atribuídos, condução de veículos, guias de transporte, incidentes</li>
                                <li>Dados de acesso técnico: endereço IP, localização aproximada, navegador/dispositivo e timestamps de sessão</li>
                                <li><strong class="text-white">Não</strong> são tratados dados biométricos nem categorias especiais (art. 9.º RGPD)</li>
                            </ul>
                        </section>

                        {{-- Finalidades e Base Legal --}}
                        <section class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-800 flex items-center justify-center text-blue-400 font-black text-xs">04</div>
                                <h3 class="text-xs font-black text-white uppercase tracking-widest">Finalidades e Base Legal (art. 6.º RGPD)</h3>
                            </div>
                            <div class="pl-11 overflow-x-auto">
                                <table class="text-xs text-white/70 w-full border-collapse">
                                    <thead>
                                        <tr class="border-b border-white/10">
                                            <th class="text-left py-2 pr-4 text-white font-black">Finalidade</th>
                                            <th class="text-left py-2 text-white font-black">Base Legal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        <tr><td class="py-2 pr-4">Autenticação na plataforma</td><td>Execução de contrato (6.1.b)</td></tr>
                                        <tr><td class="py-2 pr-4">Registo de EPI</td><td>Obrigação legal — Lei 102/2009 (6.1.c)</td></tr>
                                        <tr><td class="py-2 pr-4">Registo de condução de veículos</td><td>Obrigação legal — Código da Estrada (6.1.c)</td></tr>
                                        <tr><td class="py-2 pr-4">Emissão de guias de transporte</td><td>Obrigação legal — DL 147/2003 (6.1.c)</td></tr>
                                        <tr><td class="py-2 pr-4">Monitorização de sessões e acessos (IP e Dispositivo)</td><td>Interesse legítimo — segurança (6.1.f)</td></tr>
                                        <tr><td class="py-2 pr-4">Segurança da informação (logs de auditoria)</td><td>Interesse legítimo (6.1.f)</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        {{-- Prazos de Conservação --}}
                        <section class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-800 flex items-center justify-center text-blue-400 font-black text-xs">05</div>
                                <h3 class="text-xs font-black text-white uppercase tracking-widest">Prazos de Conservação</h3>
                            </div>
                            <ul class="text-sm text-white/70 leading-relaxed font-medium pl-11 list-disc list-inside space-y-1">
                                <li>Dados de acesso e sessões (IP): 90 dias após cessação da relação laboral</li>
                                <li>Registos de EPI: 5 anos (exigência legal — higiene e segurança no trabalho)</li>
                                <li>Registos de condução: 3 anos</li>
                            </ul>
                        </section>

                        {{-- Direitos --}}
                        <section class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-800 flex items-center justify-center text-blue-400 font-black text-xs">06</div>
                                <h3 class="text-xs font-black text-white uppercase tracking-widest">Os Seus Direitos</h3>
                            </div>
                            <p class="text-sm text-white/70 leading-relaxed font-medium pl-11">
                                Tem direito de acesso, retificação, apagamento*, portabilidade e oposição ao tratamento dos seus dados. Para exercer os seus direitos, contacte <a href="mailto:cme@cme.pt" class="text-blue-400 underline">cme@cme.pt</a>. Prazo de resposta: 30 dias.<br><br>
                                *O apagamento pode ser limitado por obrigações legais de conservação de registos.<br><br>
                                Tem ainda o direito de apresentar reclamação à <strong class="text-white">CNPD</strong> — Comissão Nacional de Proteção de Dados (<a href="https://www.cnpd.pt" target="_blank" class="text-blue-400 underline">www.cnpd.pt</a>).
                            </p>
                        </section>

                    </div>
HTML;

        $termos = <<<'HTML'
                    <div class="space-y-6 animate-in fade-in zoom-in-95 duration-500">

                        <section class="bg-white/5 p-6 rounded-2xl border border-white/10 space-y-3">
                            <h3 class="text-yellow-500 font-black text-xs uppercase tracking-widest">1. Âmbito de Aplicação</h3>
                            <p class="text-sm text-white/80 font-medium leading-relaxed">
                                A plataforma ControlEstaleiro é uma ferramenta de gestão operacional de uso <strong class="text-white">exclusivamente profissional</strong>, reservada a trabalhadores e colaboradores da <strong class="text-white">CME — Unidade C016</strong>. O acesso por pessoal não autorizado é proibido e constitui infração disciplinar e potencialmente criminal.
                            </p>
                        </section>

                        <section class="bg-white/5 p-6 rounded-2xl border border-white/10 space-y-3">
                            <h3 class="text-yellow-500 font-black text-xs uppercase tracking-widest">2. Credenciais de Acesso</h3>
                            <p class="text-sm text-white/80 font-medium leading-relaxed">
                                O PIN e as credenciais de acesso são pessoais e intransmissíveis. O trabalhador é responsável por toda a atividade registada sob as suas credenciais. Em caso de suspeita de comprometimento, deve reportar de imediato ao responsável da Unidade.
                            </p>
                        </section>

                        <section class="bg-yellow-500/10 p-6 rounded-2xl border border-yellow-500/20 space-y-3">
                            <h3 class="text-yellow-400 font-black text-xs uppercase tracking-widest">3. Valor Legal dos Registos</h3>
                            <p class="text-sm text-white/80 font-medium leading-relaxed">
                                Todo o registo efetuado na plataforma — condução de veículos, entrega de EPI, guias de transporte, relatórios de incidentes — constitui <strong class="text-white">declaração com valor probatório</strong> nos termos do art. 376.º do Código Civil Português. Registos falsos ou fraudulentos constituem infração disciplinar grave e podem implicar responsabilidade civil e/ou criminal.
                            </p>
                        </section>

                        <section class="bg-white/5 p-6 rounded-2xl border border-white/10 space-y-3">
                            <h3 class="text-yellow-500 font-black text-xs uppercase tracking-widest">4. Propriedade Intelectual</h3>
                            <p class="text-sm text-white/80 font-medium leading-relaxed">
                                Este sistema foi desenvolvido por <strong class="text-white">Israel Montesino Barreto</strong> e os seus direitos de propriedade intelectual são reservados nos termos da Lei n.º 26/2015 (CDADC). É expressamente proibida a reprodução, modificação, distribuição ou engenharia reversa sem autorização escrita do autor.
                            </p>
                        </section>

                        <section class="bg-white/5 p-6 rounded-2xl border border-white/10 space-y-3">
                            <h3 class="text-yellow-500 font-black text-xs uppercase tracking-widest">5. Disponibilidade</h3>
                            <p class="text-sm text-white/80 font-medium leading-relaxed">
                                O serviço é prestado para uso interno operacional. Podem ocorrer períodos de manutenção. A CME S.A. não assume responsabilidade por eventuais interrupções de serviço.
                            </p>
                        </section>

                        <section class="bg-white/5 p-6 rounded-2xl border border-white/10 space-y-3">
                            <h3 class="text-yellow-500 font-black text-xs uppercase tracking-widest">6. Lei Aplicável e Foro</h3>
                            <p class="text-sm text-white/80 font-medium leading-relaxed">
                                Os presentes termos regem-se pelo Direito Português. Para resolução de litígios, é competente o foro da Comarca de Oeiras, com expressa renúncia a qualquer outro.
                            </p>
                        </section>

                    </div>
HTML;

        $cookies = <<<'HTML'
                    <div class="space-y-8 animate-in fade-in zoom-in-95 duration-500">

                        <p class="text-sm text-white/70 leading-relaxed font-medium">
                            Cookies são pequenos ficheiros de texto armazenados no seu dispositivo que permitem o funcionamento técnico da plataforma. Esta aplicação utiliza <strong class="text-white">apenas cookies estritamente necessários</strong> para o funcionamento do serviço, isentos de consentimento prévio ao abrigo da Diretiva ePrivacy.
                        </p>

                        {{-- Tabela de Cookies --}}
                        <section class="space-y-3">
                            <h3 class="text-xs font-black text-white uppercase tracking-widest">Cookies Utilizados</h3>
                            <div class="overflow-x-auto rounded-2xl border border-white/10">
                                <table class="text-xs text-white/70 w-full">
                                    <thead class="bg-white/5">
                                        <tr>
                                            <th class="text-left px-4 py-3 text-white font-black">Cookie</th>
                                            <th class="text-left px-4 py-3 text-white font-black">Tipo</th>
                                            <th class="text-left px-4 py-3 text-white font-black">Duração</th>
                                            <th class="text-left px-4 py-3 text-white font-black">Finalidade</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        <tr class="hover:bg-white/3">
                                            <td class="px-4 py-3 font-mono text-blue-300">laravel_session</td>
                                            <td class="px-4 py-3">Sessão técnica</td>
                                            <td class="px-4 py-3">Sessão (apagado ao fechar o browser)</td>
                                            <td class="px-4 py-3">Manutenção da sessão autenticada. Essencial para o funcionamento.</td>
                                        </tr>
                                        <tr class="hover:bg-white/3">
                                            <td class="px-4 py-3 font-mono text-blue-300">XSRF-TOKEN</td>
                                            <td class="px-4 py-3">Segurança</td>
                                            <td class="px-4 py-3">Sessão</td>
                                            <td class="px-4 py-3">Proteção contra ataques CSRF. Obrigatório por segurança.</td>
                                        </tr>
                                        <tr class="hover:bg-white/3">
                                            <td class="px-4 py-3 font-mono text-blue-300">remember_web_*</td>
                                            <td class="px-4 py-3">Funcional</td>
                                            <td class="px-4 py-3">400 dias</td>
                                            <td class="px-4 py-3">Apenas ativo se selecionar "Lembrar-me" no login. Mantém a sessão entre visitas.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <section class="bg-green-500/10 p-6 rounded-2xl border border-green-500/20 space-y-2">
                            <h3 class="text-green-400 font-black text-xs uppercase tracking-widest">Declaração de Ausência</h3>
                            <p class="text-sm text-white/80 font-medium leading-relaxed">
                                Esta plataforma <strong class="text-white">não utiliza</strong> cookies de publicidade, rastreamento, analytics de terceiros, redes sociais ou qualquer outra finalidade não essencial. Não são instalados cookies de serviços externos.
                            </p>
                        </section>

                        <section class="space-y-3">
                            <h3 class="text-xs font-black text-white uppercase tracking-widest">Como Gerir Cookies</h3>
                            <p class="text-sm text-white/70 leading-relaxed font-medium">
                                Pode configurar o seu browser para recuar ou eliminar cookies, mas tal irá impedir o funcionamento desta plataforma, dado que os cookies utilizados são tecnicamente indispensáveis. Consulte as instruções do seu browser: <a href="https://support.google.com/chrome/answer/95647" class="text-blue-400 underline" target="_blank">Chrome</a> · <a href="https://support.mozilla.org/pt-PT/kb/cookies-informacao-armazenada" class="text-blue-400 underline" target="_blank">Firefox</a> · <a href="https://support.apple.com/pt-pt/guide/safari/sfri11471/mac" class="text-blue-400 underline" target="_blank">Safari</a>
                            </p>
                        </section>

                    </div>
HTML;

        LegalPage::create([
            'slug' => 'privacidade',
            'titulo' => 'Declaração de Privacidade',
            'conteudo' => $privacidade,
            'versao' => 'v1.0.0',
            'ultima_revisao' => '2026-03-22',
            'publicada' => true,
        ]);

        LegalPage::create([
            'slug' => 'termos',
            'titulo' => 'Condições de Utilização',
            'conteudo' => $termos,
            'versao' => 'v1.0.0',
            'ultima_revisao' => '2026-03-22',
            'publicada' => true,
        ]);

        LegalPage::create([
            'slug' => 'cookies',
            'titulo' => 'Política de Cookies',
            'conteudo' => $cookies,
            'versao' => 'v1.0.0',
            'ultima_revisao' => '2026-03-22',
            'publicada' => true,
        ]);
    }
}
