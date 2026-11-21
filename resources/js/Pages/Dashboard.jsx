import { useId } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Apple,
    Building2,
    Database,
    Dna,
    Droplet,
    Droplets,
    Factory,
    FlaskConical,
    FlaskRound,
    Flower2,
    Gem,
    Inbox,
    Microscope,
    Milk,
    Monitor,
    Pill,
    ShieldCheck,
    Sprout,
    TestTubes,
    Utensils,
    Wheat,
    Wind,
    createLucideIcon,
} from 'lucide-react';

// Maçã mordida (o lucide só tem a maçã inteira): mesmo desenho do ícone
// Apple, com uma mordida côncava no lado direito
const MacaMordida = createLucideIcon('maca-mordida', [
    ['path', { d: 'M12 6.528V3a1 1 0 0 1 1-1h0', key: 'cabo' }],
    [
        'path',
        {
            d: 'M18.237 21A15 15 0 0 0 21.039 16.346A3.2 3.2 0 0 1 21.248 8.091A6 6 0 0 0 12 6.528A6 6 0 0 0 2 11a15.1 15.1 0 0 0 3.763 10 3 3 0 0 0 3.648.648 5.5 5.5 0 0 1 5.178 0A3 3 0 0 0 18.237 21',
            key: 'corpo',
        },
    ],
]);

// Ícone com um número num círculo (Plantas Piloto I, II e III; Laboratórios
// de Apoio LPP I e II): figura do lucide (fábrica, balão) reduzida no canto
// superior esquerdo e, no canto inferior direito, um círculo com o número.
// A figura é recortada em volta do círculo (máscara), como nos ícones
// compostos do lucide. Componente próprio por causa da máscara, que precisa
// de id único (useId).

// Figuras de base (traços dos ícones Factory e FlaskRound do lucide) e o
// deslocamento [x, y] de cada uma depois de reduzida a 80%
const FIGURA_ESCALA = 0.8;
const FIGURAS = {
    fabrica: {
        tracos: [
            'M3 19a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.5a.5.5 0 0 0-.769-.422l-4.462 2.844A.5.5 0 0 1 15 10.5v-2a.5.5 0 0 0-.769-.422L9.77 10.922A.5.5 0 0 1 9 10.5V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z',
            'M8 16h.01M12 16h.01',
        ],
        deslocamento: [0.3, 0.3],
    },
    // Balão de fundo redondo, um pouco mais à esquerda para abrir espaço ao círculo
    balao: {
        tracos: ['M10 2v6.292a7 7 0 1 0 4 0V2', 'M5 15h14', 'M8.5 2h7'],
        deslocamento: [-1.6, 0.3],
    },
};

// Círculo do número: centro (16.8, 16.8), raio 6; folga do recorte: 2
const CIRCULO_C = 16.8;
const CIRCULO_R = 6;
const CIRCULO_FOLGA = 2;

// Algarismos desenhados para um círculo de centro (17.5, 17.5) e raio 5
// (altura de 15.2 a 19.8); são ampliados para o círculo atual (escala R/5)
const NUMEROS = {
    1: 'M16.4 16.3 17.8 15.2V19.8',
    2: 'M16 16.4a1.6 1.6 0 0 1 3.1.6c0 .7-.5 1.1-1 1.5L16 19.8h3.2',
    3: 'M16 15.9a1.5 1.5 0 0 1 2.9.5c0 .7-.6 1.1-1.3 1.1c.8 0 1.5.5 1.5 1.2a1.6 1.6 0 0 1-3.1.5',
};

function IconeNumerado({ figura, numero, className, ...props }) {
    // useId pode gerar ":r0:" ou "«r0»", que quebram o url(#...) da máscara
    const mascara = `icone-${figura}-${numero}-${useId().replace(/[^a-zA-Z0-9_-]/g, '')}`;
    const { tracos, deslocamento: [dx, dy] } = FIGURAS[figura];

    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
            className={className}
            {...props}
        >
            {/* Recorte (círculo + folga) levado ao espaço da figura reduzida e deslocada */}
            <mask id={mascara} maskUnits="userSpaceOnUse" x="-10" y="-10" width="50" height="50">
                <rect x="-10" y="-10" width="50" height="50" fill="#fff" stroke="none" />
                <circle
                    cx={(CIRCULO_C - dx) / FIGURA_ESCALA}
                    cy={(CIRCULO_C - dy) / FIGURA_ESCALA}
                    r={(CIRCULO_R + CIRCULO_FOLGA) / FIGURA_ESCALA}
                    fill="#000"
                    stroke="none"
                />
            </mask>
            {/* Traço compensado pela redução (2 / 0.8 = 2.5), igual ao dos outros ícones */}
            <g
                transform={`translate(${dx} ${dy}) scale(${FIGURA_ESCALA})`}
                strokeWidth={2 / FIGURA_ESCALA}
                mask={`url(#${mascara})`}
            >
                {tracos.map((d) => <path key={d} d={d} />)}
            </g>
            <circle cx={CIRCULO_C} cy={CIRCULO_C} r={CIRCULO_R} />
            {/* Número ampliado R/5 a partir do centro original; traço compensado para ficar 2 */}
            <g
                transform={`translate(${CIRCULO_C} ${CIRCULO_C}) scale(${CIRCULO_R / 5}) translate(-17.5 -17.5)`}
                strokeWidth={2 / (CIRCULO_R / 5)}
            >
                <path d={NUMEROS[numero]} />
            </g>
        </svg>
    );
}

const FabricaUm = (props) => <IconeNumerado figura="fabrica" numero={1} {...props} />;
const FabricaDois = (props) => <IconeNumerado figura="fabrica" numero={2} {...props} />;
const FabricaTres = (props) => <IconeNumerado figura="fabrica" numero={3} {...props} />;
const BalaoUm = (props) => <IconeNumerado figura="balao" numero={1} {...props} />;
const BalaoDois = (props) => <IconeNumerado figura="balao" numero={2} {...props} />;

// Ícone pelo tipo de trabalho, a partir de palavras do nome da unidade
// (sem acentos, minúsculas). A primeira regra que casar vale, por isso as
// mais específicas vêm antes (ex.: "cereais" antes de "planta piloto").
const ICONES_POR_TIPO = [
    ['recepcao de amostra', Inbox],
    ['sensorial', Utensils],
    ['minerais', Gem],
    ['fisico-quimica', FlaskConical],
    ['banco de dados', Database],
    ['bioacessibilidade', Pill],
    ['bioprocesso', FlaskRound],
    ['bioquimica', TestTubes],
    ['cromatografia gasosa', Wind],
    ['cromatografia liquida', Droplets],
    ['diagnostico molecular', Dna],
    ['informatica', Monitor],
    ['leite', Milk],
    ['micologia', Sprout],
    ['microbiologia', Microscope],
    ['oleos essenciais', Flower2],
    ['oleos graxos', Droplet],
    ['garantia da qualidade', ShieldCheck],
    ['cereais', Wheat],
    ['planta piloto de tecnologia de pos-colheita', MacaMordida],
    // "(pp i)" não casa com "(pp ii)", "(pp iii)" nem "(lpp i)"; idem "(pp ii)"
    // Laboratórios de Apoio LPP I e II; "(lpp i)" não casa com "(lpp ii".
    // O nome do LPP II está gravado sem o ")" final, por isso "(lpp ii".
    ['(lpp i)', BalaoUm],
    ['(lpp ii', BalaoDois],
    ['(pp i)', FabricaUm],
    ['(pp ii)', FabricaDois],
    ['(pp iii)', FabricaTres],
    ['pos-colheita', Apple],
    ['planta piloto', Factory],
];

function iconeDaUnidade(nome) {
    const texto = (nome ?? '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    return ICONES_POR_TIPO.find(([chave]) => texto.includes(chave))?.[1] ?? Building2;
}

function CardUnidade({ unidade }) {
    const Icone = iconeDaUnidade(unidade.nome);

    return (
        <Card size="sm" className="h-full">
            {/* Só o ícone e, na linha abaixo, o nome da unidade */}
            <CardContent className="flex flex-col items-center gap-1.5 px-2 text-center">
                <span className="flex size-8 items-center justify-center rounded-md bg-primary/10 text-primary">
                    <Icone className="size-4" aria-hidden="true" />
                </span>
                <CardTitle className="text-xs leading-snug font-medium break-words">{unidade.nome}</CardTitle>
            </CardContent>
        </Card>
    );
}

export default function Dashboard({ unidadesOperacionais = [] }) {
    return (
        <AppLayout>
            <Head title="Dashboard" />
            <div className="flex flex-col gap-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Dashboard</CardTitle>
                        <CardDescription>Painel administrativo do SCA.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p className="text-sm text-muted-foreground">
                            Use o menu lateral para navegar entre os módulos disponíveis.
                        </p>
                    </CardContent>
                </Card>

                {/* Um card para cada unidade operacional ativa */}
                <Card>
                    <CardHeader>
                        <CardTitle>Unidades Operacionais</CardTitle>
                        <CardDescription>
                            {unidadesOperacionais.length} unidade(s) operacional(is) ativa(s).
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {unidadesOperacionais.length === 0 ? (
                            <p className="text-sm text-muted-foreground">Nenhuma unidade operacional ativa.</p>
                        ) : (
                            <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8">
                                {unidadesOperacionais.map((unidade) => (
                                    <CardUnidade key={unidade.unidade_operacional_id} unidade={unidade} />
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
