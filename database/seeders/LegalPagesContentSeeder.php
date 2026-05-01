<?php

namespace Database\Seeders;

use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class LegalPagesContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->privacidade();
        $this->termos();
        $this->cookies();
    }

    private function privacidade(): void
    {
        $html = <<<'HTML'
<h2>1. Responsável pelo Tratamento de Dados</h2>
<p><strong>{empresa_nome}</strong><br>
NIF {empresa_nif}<br>
{empresa_morada}<br>
Contacto de privacidade: <a href="mailto:{empresa_email}">{empresa_email}</a></p>

<h2>2. Encarregado de Proteção de Dados (DPO)</h2>
<p>Nos termos do artigo 37.º do RGPD e do artigo 11.º da Lei n.º 58/2019, de 8 de agosto, a {empresa_nome} não está obrigada à designação de Encarregado de Proteção de Dados, dado que o tratamento de dados efetuado não preenche os pressupostos que tornam essa designação obrigatória (entidade pública, monitorização sistemática em grande escala ou tratamento em larga escala de categorias especiais de dados).</p>
<p>Quaisquer questões relativas ao tratamento dos seus dados pessoais podem ser dirigidas ao responsável pelo tratamento através de <a href="mailto:{empresa_email}">{empresa_email}</a>.</p>

<h2>3. Dados Pessoais Tratados</h2>
<p>No âmbito do funcionamento da plataforma <strong>{app_nome}</strong>, são tratados os seguintes dados pessoais:</p>
<ul>
<li><strong>Identificação profissional:</strong> número de colaborador e PIN cifrado (hash irreversível sem possibilidade de reconstituição)</li>
<li><strong>Registos operacionais:</strong> equipamentos de proteção individual (EPI) atribuídos, registos de condução de veículos, guias de transporte emitidas e recebidas, incidentes e declarações de segurança</li>
<li><strong>Dados de acesso técnico:</strong> endereço IP, identificação do dispositivo e navegador (user agent) e marcas temporais (timestamps) de sessão</li>
</ul>
<p><strong>Não</strong> são tratados dados biométricos, dados relativos à saúde, origem racial ou étnica, convicções políticas ou religiosas, nem quaisquer outras categorias especiais de dados na aceção do artigo 9.º do RGPD.</p>

<h2>4. Finalidades e Base Legal (artigo 6.º do RGPD)</h2>
<table>
<thead><tr><th>Finalidade</th><th>Base Legal</th></tr></thead>
<tbody>
<tr><td>Autenticação e controlo de acessos à plataforma</td><td>Execução do contrato de trabalho — art. 6.º, n.º 1, al. b) RGPD</td></tr>
<tr><td>Registo de entrega e devolução de EPI</td><td>Cumprimento de obrigação legal — Lei n.º 102/2009 — art. 6.º, n.º 1, al. c) RGPD</td></tr>
<tr><td>Registo de condução de veículos de serviço</td><td>Cumprimento de obrigação legal — Código da Estrada — art. 6.º, n.º 1, al. c) RGPD</td></tr>
<tr><td>Emissão de guias de transporte</td><td>Cumprimento de obrigação legal — DL n.º 147/2003 — art. 6.º, n.º 1, al. c) RGPD</td></tr>
<tr><td>Registo de incidentes e declarações de segurança</td><td>Cumprimento de obrigação legal — Lei n.º 102/2009 — art. 6.º, n.º 1, al. c) RGPD</td></tr>
<tr><td>Monitorização de acessos e logs de auditoria</td><td>Interesse legítimo — segurança dos sistemas — art. 6.º, n.º 1, al. f) RGPD</td></tr>
</tbody>
</table>

<h2>5. Destinatários dos Dados</h2>
<p>Os dados pessoais não são transmitidos a terceiros para fins comerciais ou de marketing. O acesso é restrito ao pessoal autorizado da {empresa_nome} e ao seguinte subcontratante, vinculado por acordo de tratamento de dados:</p>
<ul>
<li><strong>{hosting_entidade}</strong> — fornecedor de infraestrutura de alojamento (servidores localizados em {hosting_pais}, dentro do Espaço Económico Europeu)</li>
</ul>
<p>Poderão ainda ser destinatários as autoridades públicas competentes nos casos em que o tratamento seja legalmente imposto (e.g., Autoridade para as Condições do Trabalho, autoridades fiscais).</p>

<h2>6. Transferências Internacionais de Dados</h2>
<p>Os dados pessoais são tratados e armazenados exclusivamente em servidores localizados no Espaço Económico Europeu (EEE). <strong>Não são realizadas transferências de dados para países terceiros</strong> fora do EEE, não se aplicando as garantias previstas nos artigos 44.º a 49.º do RGPD.</p>

<h2>7. Prazos de Conservação</h2>
<ul>
<li><strong>Dados de sessão e acesso (IP, dispositivo):</strong> 90 dias após a cessação da relação laboral</li>
<li><strong>Registos de EPI:</strong> 5 anos a contar da data de cessação do contrato, nos termos da Lei n.º 102/2009</li>
<li><strong>Registos de condução de veículos:</strong> 3 anos</li>
<li><strong>Guias de transporte:</strong> 10 anos, nos termos do DL n.º 147/2003 e do Código Comercial</li>
<li><strong>Logs de auditoria e segurança:</strong> 1 ano</li>
</ul>
<p>Decorridos os prazos de conservação, os dados são eliminados ou anonimizados de forma irreversível.</p>

<h2>8. Decisões Automatizadas e Definição de Perfis</h2>
<p>A plataforma {app_nome} <strong>não toma quaisquer decisões automatizadas</strong> com efeitos jurídicos ou significativos sobre os titulares dos dados, nem realiza qualquer atividade de definição de perfis (profiling) na aceção do artigo 22.º do RGPD.</p>

<h2>9. Direitos dos Titulares dos Dados</h2>
<p>Nos termos do RGPD, assistem-lhe os seguintes direitos, exercíveis mediante pedido escrito para <a href="mailto:{empresa_email}">{empresa_email}</a>, com indicação do nome completo e número de colaborador:</p>
<ul>
<li><strong>Acesso (art. 15.º RGPD):</strong> obter confirmação sobre o tratamento e cópia dos dados</li>
<li><strong>Retificação (art. 16.º RGPD):</strong> corrigir dados inexatos ou incompletos</li>
<li><strong>Apagamento (art. 17.º RGPD):</strong> solicitar a eliminação dos dados, quando aplicável — este direito pode ser limitado por obrigações legais de conservação</li>
<li><strong>Limitação do tratamento (art. 18.º RGPD):</strong> solicitar a suspensão temporária do tratamento em determinadas circunstâncias previstas na lei</li>
<li><strong>Portabilidade (art. 20.º RGPD):</strong> receber os dados num formato estruturado e legível por máquina, quando tecnicamente aplicável</li>
<li><strong>Oposição (art. 21.º RGPD):</strong> opor-se ao tratamento baseado em interesse legítimo, salvo fundamento imperioso do responsável</li>
</ul>
<p>O prazo de resposta é de <strong>30 dias</strong>, prorrogável por mais 60 dias em casos de especial complexidade, com comunicação prévia ao titular.</p>

<h2>10. Direito de Reclamação junto da CNPD</h2>
<p>Sem prejuízo de qualquer outro recurso administrativo ou judicial, tem o direito de apresentar reclamação junto da autoridade de controlo competente:</p>
<p><strong>Comissão Nacional de Proteção de Dados (CNPD)</strong><br>
Website: <a href="https://www.cnpd.pt" target="_blank">www.cnpd.pt</a><br>
Morada: Av. D. Carlos I, 134, 1.º, 1200-651 Lisboa<br>
Email: <a href="mailto:geral@cnpd.pt">geral@cnpd.pt</a><br>
Telefone: (+351) 213 928 400</p>

<h2>11. Obrigatoriedade de Fornecimento dos Dados</h2>
<p>O fornecimento de dados pessoais é condição necessária para o acesso à plataforma {app_nome} e para o cumprimento das obrigações legais decorrentes da relação laboral. A recusa em fornecer dados obrigatórios impedirá o acesso ao sistema.</p>

<h2>12. Monitorização do Sistema (artigos 20.º e 21.º do Código do Trabalho)</h2>
<p>Os trabalhadores são informados de que o sistema regista acessos e atividade com a finalidade exclusiva de segurança da informação e cumprimento de obrigações legais. Os dados de monitorização <strong>não são utilizados para fins de avaliação de desempenho</strong>.</p>

<h2>13. Alterações à Política de Privacidade</h2>
<p>A {empresa_nome} reserva-se o direito de alterar a presente política, com publicação da versão atualizada nesta plataforma. Alterações substanciais serão comunicadas aos utilizadores com antecedência razoável.</p>
HTML;

        LegalPage::where('slug', 'privacidade')->update([
            'titulo' => 'Declaração de Privacidade',
            'versao' => 'v2.0.0',
            'ultima_revisao' => now()->toDateString(),
            'publicada' => true,
            'conteudo' => $html,
        ]);
    }

    private function termos(): void
    {
        $html = <<<'HTML'
<h2>1. Âmbito e Finalidade</h2>
<p>A plataforma <strong>{app_nome}</strong> é um sistema de gestão operacional de utilização <strong>exclusivamente profissional</strong>, desenvolvido para uso interno da <strong>{empresa_nome}</strong>, Unidade <strong>{empresa_unidade}</strong>. O acesso é reservado a trabalhadores e colaboradores devidamente credenciados.</p>
<p>O acesso por pessoal não autorizado é expressamente proibido, constituindo infração disciplinar grave e podendo consubstanciar ilícito criminal nos termos da Lei n.º 109/2009, de 15 de setembro (Lei do Cibercrime) e do artigo 195.º do Código Penal (violação de segredo).</p>

<h2>2. Credenciais de Acesso</h2>
<p>O número de colaborador e o PIN são pessoais e intransmissíveis. O utilizador é integralmente responsável por toda a atividade registada sob as suas credenciais. Em caso de suspeita de comprometimento, deve comunicar imediatamente ao responsável da Unidade {empresa_unidade}.</p>
<p>A {empresa_nome} reserva-se o direito de revogar acessos sem aviso prévio em caso de utilização indevida ou cessação da relação laboral.</p>

<h2>3. Valor Legal dos Registos</h2>
<p>Todo o registo efetuado na plataforma — incluindo entrega e devolução de EPI, registos de condução de veículos, guias de transporte, declarações de segurança e relatórios de incidentes — constitui <strong>declaração com valor probatório</strong> nos termos do artigo 376.º do Código Civil Português.</p>
<p>A introdução de dados falsos, incompletos ou fraudulentos constitui infração disciplinar grave, nos termos do Código do Trabalho, e pode implicar responsabilidade civil e criminal, designadamente nos termos dos artigos 217.º (burla) e 256.º (falsificação de documentos) do Código Penal.</p>

<h2>4. Monitorização e Vigilância do Sistema</h2>
<p>Nos termos dos artigos 20.º e 21.º do Código do Trabalho e em cumprimento do dever de informação previsto no artigo 13.º do RGPD, os utilizadores são informados de que:</p>
<ul>
<li>O sistema regista todas as operações efetuadas, incluindo data, hora, dispositivo e endereço IP de acesso</li>
<li>Os registos de acesso são conservados exclusivamente para fins de segurança da informação e cumprimento de obrigações legais</li>
<li>Os dados de utilização <strong>não são utilizados</strong> para avaliação de desempenho dos trabalhadores</li>
</ul>

<h2>5. Propriedade Intelectual</h2>
<p>A plataforma {app_nome} e todos os seus elementos constitutivos — código-fonte, estrutura, design e conteúdos — são protegidos nos termos do Código do Direito de Autor e dos Direitos Conexos (CDADC), aprovado pelo Decreto-Lei n.º 63/85, de 14 de março, com as alterações subsequentes.</p>
<p>É expressamente proibida a reprodução, modificação, distribuição, engenharia reversa ou qualquer forma de exploração não autorizada. O acesso ao sistema não confere quaisquer direitos de propriedade intelectual sobre a plataforma.</p>

<h2>6. Disponibilidade do Serviço</h2>
<p>A {empresa_nome} empenhará esforços razoáveis para assegurar a disponibilidade da plataforma, mas não garante um funcionamento ininterrupto. Poderão ocorrer períodos de indisponibilidade programada para manutenção. A {empresa_nome} não assume responsabilidade por perdas decorrentes de interrupções de serviço não imputáveis a dolo ou negligência grave.</p>

<h2>7. Lei Aplicável e Foro Competente</h2>
<p>Os presentes Termos e Condições regem-se pelo Direito Português. Para resolução de quaisquer litígios emergentes da sua interpretação ou aplicação, é competente o foro da <strong>Comarca de Lisboa Oeste</strong> (que compreende Oeiras), com expressa renúncia a qualquer outro.</p>

<h2>8. Alterações aos Termos</h2>
<p>A {empresa_nome} reserva-se o direito de alterar os presentes Termos e Condições. A versão atualizada será publicada nesta plataforma, sendo os utilizadores notificados com antecedência razoável.</p>
HTML;

        LegalPage::where('slug', 'termos')->update([
            'titulo' => 'Condições de Utilização',
            'versao' => 'v2.0.0',
            'ultima_revisao' => now()->toDateString(),
            'publicada' => true,
            'conteudo' => $html,
        ]);
    }

    private function cookies(): void
    {
        $html = <<<'HTML'
<p>Os cookies são pequenos ficheiros de texto armazenados no seu dispositivo aquando da visita a um sítio web. A presente política descreve os cookies utilizados pela plataforma <strong>{app_nome}</strong>, em conformidade com a Diretiva 2002/58/CE (Diretiva ePrivacy), transposta pelo artigo 13.º-A da Lei n.º 41/2004, de 18 de agosto, e com o RGPD.</p>

<h2>1. Cookies Utilizados</h2>
<p>A plataforma {app_nome} utiliza <strong>exclusivamente cookies técnicos e funcionais estritamente necessários</strong> ao funcionamento do serviço. Estes cookies não requerem consentimento prévio do utilizador, ao abrigo do artigo 13.º-A, n.º 1 da Lei n.º 41/2004.</p>
<table>
<thead><tr><th>Cookie</th><th>Tipo</th><th>Duração</th><th>Finalidade</th></tr></thead>
<tbody>
<tr><td><code>laravel_session</code></td><td>Sessão técnica — estritamente necessário</td><td>Sessão (eliminado ao fechar o navegador)</td><td>Mantém o estado da sessão autenticada. Indispensável para o funcionamento da plataforma.</td></tr>
<tr><td><code>XSRF-TOKEN</code></td><td>Segurança — estritamente necessário</td><td>Sessão</td><td>Proteção contra ataques CSRF (Cross-Site Request Forgery). Exigido por razões de segurança.</td></tr>
<tr><td><code>remember_web_*</code></td><td>Funcional — ativado por opção do utilizador</td><td>400 dias</td><td>Ativado apenas se o utilizador selecionar "Lembrar-me" no início de sessão. Mantém a sessão ativa entre visitas.</td></tr>
</tbody>
</table>

<h2>2. Ausência de Cookies de Rastreamento</h2>
<p>A plataforma {app_nome} <strong>não utiliza</strong> cookies de:</p>
<ul>
<li>Publicidade ou retargeting</li>
<li>Análise de comportamento (analytics) de terceiros</li>
<li>Redes sociais</li>
<li>Rastreamento entre sítios web (cross-site tracking)</li>
<li>Quaisquer serviços externos não essenciais</li>
</ul>
<p>Não são instalados cookies de terceiros nesta plataforma.</p>

<h2>3. Base Legal</h2>
<p>Os cookies estritamente necessários são utilizados com base no <strong>interesse legítimo</strong> do responsável pelo tratamento para assegurar a segurança e o funcionamento técnico da plataforma — art. 6.º, n.º 1, al. f) do RGPD — e são isentos de consentimento prévio ao abrigo da Diretiva ePrivacy.</p>
<p>O cookie de sessão persistente (<code>remember_web_*</code>) é ativado exclusivamente por ação voluntária do utilizador, correspondendo ao exercício de uma opção funcional.</p>

<h2>4. Gestão de Cookies</h2>
<p>Dado que os cookies utilizados são estritamente necessários ao funcionamento da plataforma, a sua desativação impedirá o acesso ao serviço. A desativação pode ser efetuada nas definições do seu navegador:</p>
<ul>
<li><a href="https://support.google.com/chrome/answer/95647" target="_blank">Google Chrome</a></li>
<li><a href="https://support.mozilla.org/pt-PT/kb/cookies-informacao-armazenada" target="_blank">Mozilla Firefox</a></li>
<li><a href="https://support.apple.com/pt-pt/guide/safari/sfri11471/mac" target="_blank">Apple Safari</a></li>
<li><a href="https://support.microsoft.com/pt-pt/microsoft-edge/eliminar-cookies-no-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank">Microsoft Edge</a></li>
</ul>

<h2>5. Contacto e Reclamações</h2>
<p>Para questões relacionadas com a presente política de cookies, contacte <a href="mailto:{empresa_email}">{empresa_email}</a>. Tem ainda o direito de apresentar reclamação junto da <a href="https://www.cnpd.pt" target="_blank">CNPD — Comissão Nacional de Proteção de Dados</a> (www.cnpd.pt).</p>
HTML;

        LegalPage::where('slug', 'cookies')->update([
            'titulo' => 'Política de Cookies',
            'versao' => 'v2.0.0',
            'ultima_revisao' => now()->toDateString(),
            'publicada' => true,
            'conteudo' => $html,
        ]);
    }
}
