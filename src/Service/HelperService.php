<?php namespace App\Service;

class HelperService
{
    private $router;
    private $request;
    
    public function __construct($router, $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    public function baseUrl()
    {
        return $this->request->getUri()->getBaseUrl();
    }

    public function currentUrl()
    {
        return $this->baseUrl().$this->request->getUri()->getPath();
    }

    public function pathFor($name, $params = [], $query = [])
    {
        return $this->router->pathFor($name, $params, $query);
    }

    public function completePathFor($name, $params = [], $query = [])
    {
        return $this->baseUrl().$this->pathFor($name, $params, $query);
    }

    public function generateTrace($str)
    {
        return preg_replace('/[^[:alnum:]]/ui', '', $str);
    }

    public function getCategories()
    {
        return [
            'social' => 'Integración Social',
            'educacion' => 'Educación',
            'comunicacion' => 'Comunicación',
            'ambiente' => 'Medio Ambiente',
            'cultura' => 'Cultura',
            'salud' => 'Salud',
            'deporte' => 'Deporte y recreación',
            'empleo' => 'Empleo y Capacitación',
        ];
    }

    public function getLocalidades()
    {
        return [
            'AARON CASTELLANOS', 'ACEBAL', 'AGUARÁ GRANDE', 'ALBARELLOS', 'ALCORTA', 'ALDAO', 'ALEJANDRA',
            'ALVAREZ', 'ALVEAR', 'AMBROSETTI', 'AMENÁBAR', 'ANGEL GALLARDO', 'ANGÉLICA', 'ANGELONI',
            'AREQUITO', 'ARMINDA', 'ARMSTRONG', 'AROCENA', 'ARROYO AGUIAR', 'ARROYO CEIBAL', 'ARROYO LEYES',
            'ARROYO SECO', 'ARRUFÓ', 'ARTEAGA', 'ATALIVA', 'AURELIA', 'AVELLANEDA', 'BARRANCAS',
            'BAUER Y SIGEL', 'BELLA ITALIA', 'BERAVEBÚ', 'BERNA', 'BERNARDO DE IRIGOYEN', 'BIGAND', 'BIGAND',
            'BOMBAL', 'BOUQUET', 'BUSTINZA', 'CABAL', 'CACIQUE ARIACAIQUÍN', 'CAFFERATA', 'CALCHAQUÍ',
            'CAMPO ANDINO', 'CAMPO PIAGGIO', 'CANDIOTI', 'CAÑADA DE GÓMEZ', 'CAÑADA DEL UCLE', 'CAÑADA OMBÚ',
            'CAÑADA RICA', 'CAÑADA ROSQUÍN', 'CAPITÁN BERMÚDEZ', 'CAPIVARA', 'CARCARAÑÁ', 'CARLOS PELLEGRINI',
            'CARMEN', 'CARMEN DEL SAUCE', 'CARRERAS', 'CARRIZALES', 'CASALEGNO', 'CASAS', 'CASILDA',
            'CASTELAR', 'CAYASTÁ', 'CAYASTACITO', 'CENTENO', 'CEPEDA', 'CERES', 'CHABÁS', 'CHAÑAR LADEADO',
            'CHAPUY', 'CHOVET', 'CLASON', 'COLONIA ALDAO', 'COLONIA ANA', 'COLONIA BELGRANO', 'COLONIA BICHA',
            'COLONIA BOSSI', 'COLONIA CASTELLANOS', 'COLONIA CAVOUR', 'COLONIA CELLO', 'COLONIA CHRISTOPHERSEN',
            'COLONIA CLARA', 'COLONIA DOLORES', 'COLONIA DOS ROSAS Y LA LEGUA', 'COLONIA DURAN',
            'COLONIA ESTHER', 'COLONIA FIDELA', 'COLONIA HUGENTOBLER', 'COLONIA ITURRASPE', 'COLONIA MARGARITA',
            'COLONIA MASCÍAS', 'COLONIA MAUA', 'COLONIA RAQUEL', 'COLONIA ROSA', 'COLONIA SAN JOSE',
            'COLONIA SILVA', 'COLONIA TACURALES', 'CONSTANZA', 'CORONDA', 'CORONEL ARNOLD', 'CORONEL BOGADO',
            'CORONEL DOMINGUEZ', 'CORONEL FRAGA', 'CORREA', 'CRISPI', 'CULULÚ', 'CURUPAITY', 'DESVÍO ARIJÓN',
            'DÍAZ', 'DIEGO DE ALVEAR', 'EGUSQUIZA', 'EL ARAZÁ', 'EL RABÓN', 'EL SOMBRERITO', 'EL TRÉBOL',
            'ELISA', 'ELORTONDO', 'EMILIA', 'EMPALME SAN CARLOS', 'EMPALME VILLA CONSTITUCIÓN', 'ESMERALDA',
            'ESPERANZA', 'ESTACIÓN CLUCELLAS', 'ESTACIÓN SAGUIER', 'ESTEBAN RAMS', 'EUSEBIA', 'EUSTOLIA',
            'FELICIA', 'FIGHIERA', 'FIRMAT', 'FLORENCIA', 'FORTÍN OLMOS', 'FRANCK', 'FRAY LUIS BELTRÁN',
            'FRONTERA', 'FUENTES', 'FUNES', 'GABOTO', 'GALISTEO', 'GÁLVEZ', 'GARABATO', 'GARIBALDI',
            'GATO COLORADO', 'GENERAL GELLY', 'GENERAL LAGOS', 'GESSLER', 'GOBERNADOR CRESPO', 'GODEKEN',
            'GODOY', 'GOLONDRINA', 'GRANADERO BAIGORRIA', 'GREGORIA P. DE DENIS', 'GRUTLY', 'GUADALUPE NORTE',
            'HELVECIA', 'HERSILIA', 'HIPATÍA', 'HUANQUEROS', 'HUGHES', 'HUMBERTO PRIMO', 'HUMBOLDT',
            'IBARLUCEA', 'INGENIERO CHANOURDIE', 'INTIYACO', 'IRIGOYEN (PUEBLO)', 'ITUZAINGO',
            'JACINTO L. ARAUZ', 'JESÚS MARÍA (TIMBÚES)', 'JOSEFINA', 'JUAN B. MOLINA', 'JUAN DE GARAY',
            'JUNCAL', 'LA BRAVA', 'LA CABRAL', 'LA CAMILA', 'LA CHISPA', 'LA CRIOLLA', 'LA GALLARETA',
            'LA LUCILA', 'LA PELADA', 'LA PENCA    Y CARAGUATÁ', 'LA RUBIA', 'LA SARITA', 'LA VANGUARDIA',
            'LABORDEBOY', 'LAGUNA PAIVA', 'LANDETA', 'LANTERI', 'LARRECHEA', 'LAS AVISPAS', 'LAS BANDURRIAS',
            'LAS GARZAS', 'LAS PALMERAS', 'LAS PAREJAS', 'LAS PETACAS', 'LAS ROSAS', 'LAS TOSCAS', 'LAS TUNAS',
            'LAZZARINO', 'LEHMANN', 'LLAMBÍ CAMPBELL', 'LOGROÑO', 'LOMA ALTA', 'LOPEZ', 'LOS AMORES',
            'LOS CARDOS', 'LOS LAURELES', 'LOS MOLINOS', 'LOS QUIRQUINCHOS', 'LUCIO V. LOPEZ', 'LUIS PALACIOS',
            'MACIEL', 'MAGGIOLO', 'MALABRIGO', 'MARCELINO ESCALADA', 'MARGARITA', 'MARÍA JUANA', 'MARÍA LUISA',
            'MARIA SUSANA', 'MARÍA TERESA', 'MATILDE', 'MÁXIMO PAZ', 'MELINCUÉ', 'MIGUEL TORRES', 'MOISES VILLE',
            'MONIGOTES', 'MONJE', 'MONTE OSCURIDAD', 'MONTE VERA', 'MONTEFIORE', 'MONTES DE OCA', 'MURPHY',
            'NARÉ', 'NELSON', 'NICANOR MOLINAS', 'NUEVO TORINO', 'ÑANDUCITA', 'OLIVEROS', 'PALACIOS', 'PAVÓN',
            'PAVÓN ARRIBA', 'PEDRO GÓMEZ CELLO', 'PÉREZ', 'PEYRANO', 'PIAMONTE', 'PILAR', 'PIÑERO',
            'PLAZA CLUCELLAS', 'PORTUGALETE', 'POZO BORRADO', 'PRESIDENTE ROCA', 'PROGRESO', 'PROVIDENCIA',
            'PUEBLO ANDINO', 'PUEBLO ESTHER', 'PUEBLO MARINI', 'PUEBLO MUÑOZ', 'PUERTO GENERA SAN MARTÍN',
            'PUJATO', 'PUJATO NORTE', 'RAFAELA', 'RAMAYÓN', 'RAMONA', 'RECONQUISTA', 'RECREO', 'RICARDONE',
            'RIVADAVIA', 'ROLDÁN', 'ROMANG', 'ROSARIO', 'RUEDA', 'RUFINO', 'SA PEREIRA', 'SALADERO MARIANO CABAL',
            'SALTO GRANDE', 'SAN AGUSTÍN', 'SAN ANTONIO', 'SAN ANTONIO DE OBLIGADO', 'SAN BERNARDO',
            'SAN BERNARDO', 'SAN CARLOS CENTRO', 'SAN CARLOS NORTE', 'SAN CARLOS SUD', 'SAN CRISTÓBAL',
            'SAN EDUARDO', 'SAN EUGENIO', 'SAN FABIÁN', 'SAN FRANCISCO DE SANTA FE', 'SAN GENARO',
            'SAN GREGORIO', 'SAN GUILLERMO', 'SAN JAVIER', 'SAN JERÓNIMO NORTE', 'SAN JERÓNIMO SUD',
            'SAN JERÓNIMODEL SAUCE', 'SAN JORGE', 'SAN JOSÉ DE LA ESQUINA', 'SAN JOSÉ DEL RINCÓN', 'SAN JUSTO',
            'SAN LORENZO', 'SAN MARIANO', 'SAN MARTÍN DE ESCOBAS', 'SAN MARTÍN NORTE', 'SAN VICENTE',
            'SANCTI SPIRITU', 'SANFORD', 'SANTA  ISABEL', 'SANTA CLARA DE BUENA VISTA', 'SANTA FE',
            'SANTA MARGARITA', 'SANTA MARÍA CENTRO', 'SANTA MARÍA NORTE', 'SANTA ROSA DE CALCHINES',
            'SANTA TERESA', 'SANTO DOMINGO', 'SANTO TOMÉ', 'SANTURCE', 'SARGENTO CABRAL', 'SARMIENTO',
            'SASTRE', 'SAUCE VIEJO', 'SERODINO', 'SOLDINI', 'SOLEDAD', 'SOUTOMAYOR', 'STA CLARA DE SAGUIER',
            'SUARDI', 'SUNCHALES', 'SUSANA', 'TACUARENDÍ', 'TACURAL', 'TARTAGAL', 'TEODELINA', 'THEOBALD',
            'TOBA', 'TORTUGAS', 'TOSTADO', 'TOTORAS', 'TRAILL', 'URANGA', 'VENADO TUERTO', 'VERA',
            'VERA Y PINTADO', 'VIDELA', 'VILA', 'VILLA AMELIA', 'VILLA ANA', 'VILLA CAÑÁS', 'VILLA CONSTITUCIÓN',
            'VILLA ELOISA', 'VILLA GOBERNADOR GÁLVEZ', 'VILLA GUILLERMINA', 'VILLA MINETTI', 'VILLA MUGUETA',
            'VILLA OCAMPO', 'VILLA SAN JOSE', 'VILLA SARALEGUI', 'VILLA TRINIDAD', 'VILLADA', 'VIRGINIA',
            'WHEELWRIGHT', 'ZAVALLA', 'ZENÓN PEREYRA', 'OTRA'
        ];
    }
}
