<?php
/**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class LGCookiesLawPurpose extends ObjectModel
{
    public $id_shop;
    public $technical = false;
    public $locked_modules;
    public $consent_mode = false;
    public $consent_type;
    public $active = true;
    public $date_add;
    public $date_upd;

    public $name;
    public $description;

    const FUNCTIONAL_PURPOSE = 1;
    const MARKETING_PURPOSE = 2;
    const ANALYTICS_PURPOSE = 3;
    const PERFORMANCE_PURPOSE = 4;
    const OTHER_PURPOSE = 5;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'lgcookieslaw_purpose',
        'primary' => 'id_lgcookieslaw_purpose',
        'multilang' => true,
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'technical' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'locked_modules' => ['type' => self::TYPE_STRING],
            'consent_mode' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'consent_type' => ['type' => self::TYPE_STRING, 'size' => 32],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
            'name' => [
                'type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 64,
                'required' => true,
            ],
            'description' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true],
        ],
    ];

    public static function getInstallationDefaults()
    {
        $installation_defaults = [
            self::FUNCTIONAL_PURPOSE => [
                'name' => [
                    'es' => 'Cookies funcionales',
                    'en' => 'Functional cookies',
                    'fr' => 'Cookies fonctionnels',
                    'pl' => 'Funkcjonalne pliki cookie',
                    'pt' => 'Cookies funcionais',
                    'nl' => 'Functionele cookies',
                    'de' => 'Funktionale Cookies',
                    'it' => 'Cookie funzionali',
                    'gb' => 'Functional cookies',
                ],
                'description' => [
                    'es' => 'Las cookies funcionales son estrictamente necesarias para proporcionar ' .
                        'los servicios de la tienda, así como para su correcto funcionamiento, por ello ' .
                        'no es posible rechazar su uso. Permiten al usuario la navegación a través de ' .
                        'nuestra web y la utilización de las diferentes opciones o servicios que ' .
                        'existen en ella.',
                    'en' => 'Functional cookies are strictly necessary to provide the services of the ' .
                        'shop, as well as for its proper functioning, so it is not possible to refuse ' .
                        'their use. They allow the user to browse through our website and use the ' .
                        'different options or services that exist on it.',
                    'fr' => 'Les cookies fonctionnels sont strictement nécessaires pour fournir les services de la ' .
                        'boutique, ainsi que pour son bon fonctionnement, il n\'est donc pas possible de refuser ' .
                        'leur utilisation. Ils permettent à l\'utilisateur de naviguer sur notre site web et ' .
                        'd\'utiliser les différentes options ou services qui y sont proposés.',
                    'pl' => 'Funkcjonalne pliki cookie są bezwzględnie niezbędne do świadczenia usług sklepu, ' .
                        'a także do jego prawidłowego działania, dlatego nie ma możliwości odmowy ich użycia. ' .
                        'Umożliwiają one użytkownikowi poruszanie się po naszej stronie internetowej i ' .
                        'korzystanie z różnych opcji lub usług, które na niej istnieją.',
                    'pt' => 'Os cookies funcionais são estritamente necessários para fornecer os serviços da ' .
                        'loja, bem como para o seu bom funcionamento, pelo que não é possível recusar a sua ' .
                        'utilização. Permitem ao utilizador navegar no nosso website e utilizar as diferentes ' .
                        'opções ou serviços que nele existem.',
                    'nl' => 'Functionele cookies zijn strikt noodzakelijk om de diensten van de winkel te ' .
                        'leveren, evenals voor de juiste werking ervan, daarom is het niet mogelijk om het ' .
                        'gebruik ervan te weigeren. Ze stellen de gebruiker in staat om door onze website te ' .
                        'navigeren en gebruik te maken van de verschillende opties of diensten die erop bestaan.',
                    'de' => 'Funktionale Cookies sind für die Bereitstellung der Dienste des Shops sowie für ' .
                        'den ordnungsgemäßen Betrieb unbedingt erforderlich, daher ist es nicht möglich, ihre ' .
                        'Verwendung abzulehnen. Sie ermöglichen es dem Benutzer, durch unsere Website zu ' .
                        'navigieren und die verschiedenen Optionen oder Dienste zu nutzen, die auf dieser ' .
                        'vorhanden sind.',
                    'it' => 'I cookie funzionali sono strettamente necessari per fornire i servizi del negozio, ' .
                        'così come per il suo corretto funzionamento, quindi non è possibile rifiutare il loro ' .
                        'utilizzo. Permettono all\'utente di navigare nel nostro sito web e di utilizzare le ' .
                        'diverse opzioni o servizi che esistono in esso.',
                    'gb' => 'Functional cookies are strictly necessary to provide the services of the shop, as ' .
                        'well as for its proper functioning, so it is not possible to refuse their use. They ' .
                        'allow the user to browse through our website and use the different options or services ' .
                        'that exist on it.',
                ],
                'technical' => true,
                'consent_mode' => true,
                'consent_type' => 'functionality_storage',
                'active' => true,
                'locked_modules' => [],
            ],
            self::MARKETING_PURPOSE => [
                'name' => [
                    'es' => 'Cookies publicitarias',
                    'en' => 'Advertising Cookies',
                    'fr' => 'Cookies publicitaires',
                    'pl' => 'Reklamowe pliki cookie',
                    'pt' => 'Cookies publicitários',
                    'nl' => 'Advertentiecookies',
                    'de' => 'Werbe-Cookies',
                    'it' => 'Cookie pubblicitari',
                    'gb' => 'Advertising Cookies',
                ],
                'description' => [
                    'es' => 'Son aquellas que recaban información sobre los anuncios mostrados a los usuarios del ' .
                        'sitio web. Pueden ser de anónimas, si solo recopilan información sobre los espacios ' .
                        'publicitarios mostrados sin identificar al usuario o, personalizadas, si recopilan ' .
                        'información personal del usuario de la tienda por parte de un tercero, para la ' .
                        'personalización de dichos espacios publicitarios.',
                    'en' => 'These are cookies that collect information about the advertisements shown to users of ' .
                        'the website. They can be anonymous, if they only collect information about the ' .
                        'advertising spaces shown without identifying the user, or personalised, if they collect ' .
                        'personal information about the user of the shop by a third party, for the personalisation ' .
                        'of these advertising spaces.',
                    'fr' => 'Il s\'agit de cookies qui collectent des informations sur les publicités montrées aux ' .
                        'utilisateurs du site web. Elles peuvent être anonymes, si elles ne collectent que des ' .
                        'informations sur les espaces publicitaires affichés sans identifier l\'utilisateur, ou ' .
                        'personnalisées, si elles collectent des informations personnelles sur l\'utilisateur de la ' .
                        'boutique par un tiers, pour la personnalisation de ces espaces publicitaires.',
                    'pl' => 'To te, które zbierają informacje o reklamach wyświetlanych użytkownikom serwisu. ' .
                        'Mogą być anonimowe, jeśli zbierają tylko informacje o wyświetlanych powierzchniach ' .
                        'reklamowych bez identyfikacji użytkownika lub spersonalizowane, jeśli zbierają dane ' .
                        'osobowe użytkownika sklepu przez osobę trzecią, w celu personalizacji wspomnianych ' .
                        'powierzchni reklamowych.',
                    'pt' => 'Estes são cookies que recolhem informações sobre os anúncios mostrados aos utilizadores ' .
                        'do sítio web. Podem ser anónimas, se apenas recolherem informações sobre os espaços ' .
                        'publicitários mostrados sem identificar o utilizador, ou personalizadas, se recolherem ' .
                        'informações pessoais sobre o utilizador da loja por terceiros, para a personalização desses ' .
                        'espaços publicitários.',
                    'nl' => 'Dit zijn degenen die informatie verzamelen over de advertenties die aan gebruikers van ' .
                        'de website worden getoond. Ze kunnen anoniem zijn, als ze alleen informatie verzamelen ' .
                        'over de weergegeven advertentieruimten zonder de gebruiker te identificeren of, ' .
                        'gepersonaliseerd, als ze persoonlijke informatie van de gebruiker van de winkel ' .
                        'verzamelen door een derde partij, voor de personalisatie van genoemde advertentieruimten.',
                    'de' => 'Sie sind diejenigen, die Informationen über die Anzeigen sammeln, die den ' .
                        'Benutzern der Website angezeigt werden. Sie können anonym sein, wenn sie nur ' .
                        'Informationen über die angezeigten Werbeflächen sammeln, ohne den Benutzer zu ' .
                        'identifizieren, oder personalisiert, wenn sie personenbezogene Daten des Benutzers des ' .
                        'Shops durch einen Dritten sammeln, um diese Werbeflächen zu personalisieren.',
                    'it' => 'Si tratta di cookie che raccolgono informazioni sulle pubblicità mostrate agli ' .
                        'utenti del sito web. Possono essere anonimi, se raccolgono solo informazioni sugli spazi ' .
                        'pubblicitari mostrati senza identificare l\'utente, o personalizzati, se raccolgono ' .
                        'informazioni personali sull\'utente del negozio da una terza parte, per la ' .
                        'personalizzazione di questi spazi pubblicitari.',
                    'gb' => 'These are cookies that collect information about the advertisements shown to users of ' .
                        'the website. They can be anonymous, if they only collect information about the advertising ' .
                        'spaces shown without identifying the user, or personalised, if they collect personal ' .
                        'information about the user of the shop by a third party, for the personalisation of these ' .
                        'advertising spaces.',
                ],
                'technical' => false,
                'consent_mode' => true,
                'consent_type' => 'ad_storage',
                'active' => true,
                'locked_modules' => [],
            ],
            self::ANALYTICS_PURPOSE => [
                'name' => [
                    'es' => 'Cookies de analíticas',
                    'en' => 'Analytics cookies',
                    'fr' => 'Cookies d\'analyse',
                    'pl' => 'Analityczne pliki cookie',
                    'pt' => 'Cookies analíticos',
                    'nl' => 'Analytics-cookies',
                    'de' => 'Analyse-Cookies',
                    'it' => 'Cookie di analisi',
                    'gb' => 'Analytics cookies',
                ],
                'description' => [
                    'es' => 'Recopilan información sobre la experiencia de navegación del usuario en la tienda, ' .
                        'normalmente de forma anónima, aunque en ocasiones también permiten identificar de manera ' .
                        'única e inequívoca al usuario con el fin de obtener informes sobre los intereses de los ' .
                        'usuarios en los productos o servicios que ofrece la tienda.',
                    'en' => 'Collect information about the user\'s browsing experience in the shop, usually ' .
                        'anonymously, although sometimes they also allow the user to be uniquely and unequivocally ' .
                        'identified in order to obtain reports on the user\'s interests in the products or services ' .
                        'offered by the shop.',
                    'fr' => 'Collecter des informations sur la navigation de l\'utilisateur dans la boutique, ' .
                        'généralement de manière anonyme, bien que parfois elles permettent également d\'identifier ' .
                        'l\'utilisateur de manière unique et sans équivoque afin d\'obtenir des rapports sur les ' .
                        'intérêts de l\'utilisateur pour les produits ou services proposés par la boutique.',
                    'pl' => 'Zbierają informacje o tym, jak użytkownik przegląda sklep, zwykle anonimowo, ' .
                        'choć czasami pozwalają również na jednoznaczną identyfikację użytkownika w celu ' .
                        'uzyskania raportów o zainteresowaniach użytkowników produktami lub usługami, które ' .
                        'oferuje sklep.',
                    'pt' => 'Recolher informação sobre a experiência de navegação do utilizador na loja, geralmente ' .
                        'anónima, embora por vezes também permitam que o utilizador seja identificado de forma única ' .
                        'e inequívoca a fim de obter relatórios sobre os interesses do utilizador nos produtos ou ' .
                        'serviços oferecidos pela loja.',
                    'nl' => 'Ze verzamelen informatie over de browse-ervaring van de gebruiker in de winkel, ' .
                        'meestal anoniem, hoewel ze soms ook de gebruiker op unieke en ondubbelzinnige wijze ' .
                        'kunnen identificeren om rapporten te verkrijgen over de interesses van gebruikers in de ' .
                        'aangeboden producten of diensten. de winkel.',
                    'de' => 'Sie sammeln Informationen über das Surferlebnis des Benutzers im Geschäft, ' .
                        'normalerweise anonym, obwohl sie manchmal auch eine eindeutige und eindeutige ' .
                        'Identifizierung des Benutzers ermöglichen, um Berichte über die Interessen der ' .
                        'Benutzer an den angebotenen Produkten oder Dienstleistungen zu erhalten. der Laden.',
                    'it' => 'Raccogliere informazioni sull\'esperienza di navigazione dell\'utente nel negozio, di ' .
                        'solito in modo anonimo, anche se a volte permettono di identificare l\'utente in ' .
                        'modo univoco e inequivocabile per ottenere rapporti sugli interessi dell\'utente nei ' .
                        'prodotti o servizi offerti dal negozio.',
                    'gb' => 'Collect information about the user\'s browsing experience in the shop, usually ' .
                        'anonymously, although sometimes they also allow the user to be uniquely and unequivocally ' .
                        'identified in order to obtain reports on the user\'s interests in the products or services ' .
                        'offered by the shop.',
                ],
                'technical' => false,
                'consent_mode' => true,
                'consent_type' => 'analytics_storage',
                'active' => true,
                'locked_modules' => [],
            ],
            self::PERFORMANCE_PURPOSE => [
                'name' => [
                    'es' => 'Cookies de rendimiento',
                    'en' => 'Performance cookies',
                    'fr' => 'Cookies de performance',
                    'pl' => 'Wydajnościowe pliki cookie',
                    'pt' => 'Cookies de desempenho',
                    'nl' => 'Prestatiecookies',
                    'de' => 'Leistungs-Cookies',
                    'it' => 'Cookie di performance',
                    'gb' => 'Performance cookies',
                ],
                'description' => [
                    'es' => 'Se usan para mejorar la experiencia de navegación y optimizar el funcionamiento de la ' .
                        'tienda.',
                    'en' => 'These are used to improve the browsing experience and optimize the operation of the shop.',
                    'fr' => 'Ils sont utilisés pour améliorer l\'expérience de navigation et optimiser le ' .
                        'fonctionnement de la boutique.',
                    'pl' => 'Służą one do usprawnienia przeglądania i optymalizacji działania sklepu.',
                    'pt' => 'Estes são utilizados para melhorar a experiência de navegação e optimizar o ' .
                        'funcionamento da loja.',
                    'nl' => 'Ze worden gebruikt om de browse-ervaring te verbeteren en de werking van de winkel te ' .
                        'optimaliseren.',
                    'de' => 'Sie werden verwendet, um das Surferlebnis zu verbessern und den Betrieb des Shops zu ' .
                        'optimieren.',
                    'it' => 'Questi sono utilizzati per migliorare l\'esperienza di navigazione e ottimizzare il ' .
                        'funzionamento del negozio.',
                    'gb' => 'These are used to improve the browsing experience and optimize the operation of the shop.',
                ],
                'technical' => false,
                'consent_mode' => false,
                'consent_type' => null,
                'active' => true,
                'locked_modules' => [],
            ],
            self::OTHER_PURPOSE => [
                'name' => [
                    'es' => 'Otras cookies',
                    'en' => 'Other cookies',
                    'fr' => 'Autres cookies',
                    'pl' => 'Inne pliki cookie',
                    'pt' => 'Outros cookies',
                    'nl' => 'Andere cookies',
                    'de' => 'Andere cookies',
                    'it' => 'Altri cookie',
                    'gb' => 'Other cookies',
                ],
                'description' => [
                    'es' => 'Son cookies sin un propósito claro o aquellas que todavía estamos en proceso de ' .
                        'clasificar.',
                    'en' => 'These are cookies without a clear purpose or those that we are still in the process of ' .
                        'classifying.',
                    'fr' => 'Il s\'agit de cookies sans finalité claire ou de ceux que nous sommes encore en ' .
                        'train de classifier.',
                    'pl' => 'Są to pliki cookie bez wyraźnego celu lub te, które wciąż klasyfikujemy.',
                    'pt' => 'Estes são cookies sem um objectivo claro ou aqueles que ainda estamos em processo de ' .
                        'classificação.',
                    'nl' => 'Het zijn cookies zonder duidelijk doel of cookies die we nog aan het classificeren zijn.',
                    'de' => 'Es handelt sich um Cookies ohne eindeutigen Zweck oder solche, die wir noch im ' .
                        'Klassifizierungsprozess sind.',
                    'it' => 'Si tratta di cookie senza uno scopo chiaro o di quelli che stiamo ancora classificando.',
                    'gb' => 'These are cookies without a clear purpose or those that we are still in the process of ' .
                        'classifying.',
                ],
                'technical' => false,
                'consent_mode' => false,
                'consent_type' => null,
                'active' => true,
                'locked_modules' => [],
            ],
        ];

        return $installation_defaults;
    }

    public static function getPurposes($id_lang = null, $id_shop = null, $active = false)
    {
        $context = Context::getContext();

        if (is_null($id_lang)) {
            $id_lang = $context->language->id;
        }

        if (is_null($id_shop)) {
            $id_shop = $context->shop->id;
        }

        $query = new DbQuery();

        $query->select('a.*, b.`name`, b.`description`');
        $query->from(self::$definition['table'], 'a');
        $query->where('a.`id_shop` = ' . (int) $id_shop);

        $query->leftJoin(
            self::$definition['table'] . '_lang',
            'b',
            '(b.`' . self::$definition['primary'] . '` = a.`' . self::$definition['primary'] .
            '` AND b.`id_lang` = ' . (int) $id_lang . ')'
        );

        if ($active) {
            $query->where('a.`active` = ' . (int) $active);
        }

        return Db::getInstance()->executeS($query);
    }

    public static function getPurposesLite($id_lang = null, $id_shop = null, $active = false, $all_values = true)
    {
        $context = Context::getContext();

        if (is_null($id_lang)) {
            $id_lang = $context->language->id;
        }

        if (is_null($id_shop)) {
            $id_shop = $context->shop->id;
        }

        $query = new DbQuery();

        if ($all_values) {
            $query->select('a.`id_lgcookieslaw_purpose` as id, a.`technical` as t, b.`name`, b.`description`');
        } else {
            $query->select('a.`id_lgcookieslaw_purpose` as id, a.`technical` as t');
        }
        $query->from(self::$definition['table'], 'a');
        $query->where('a.`id_shop` = ' . (int) $id_shop);

        $query->leftJoin(
            self::$definition['table'] . '_lang',
            'b',
            '(b.`' . self::$definition['primary'] . '` = a.`' . self::$definition['primary'] .
            '` AND b.`id_lang` = ' . (int) $id_lang . ')'
        );

        if ($active) {
            $query->where('a.`active` = ' . (int) $active);
        }

        return Db::getInstance()->executeS($query);
    }

    public static function getLockedModules($enabled_purposes = null, $id_shop = null, $active = true)
    {
        $context = Context::getContext();

        if (is_null($id_shop)) {
            $id_shop = $context->shop->id;
        }

        $query = new DbQuery();

        $query->select('a.`' . self::$definition['primary'] . '`, a.`locked_modules`');
        $query->from(self::$definition['table'], 'a');
        $query->where('a.`id_shop` = ' . (int) $id_shop);

        if (!is_null($enabled_purposes)) {
            $query->where('a.`' . self::$definition['primary'] . '` NOT IN (' . pSQL($enabled_purposes) . ')');
        }

        if ($active) {
            $query->where('a.`active` = ' . (int) $active);
        }

        return Db::getInstance()->executeS($query);
    }

    public function deleteAssociatedCookies()
    {
        $lgcookieslaw_cookies = LGCookiesLawCookie::getCookiesByPurpose((int) $this->id, null, (int) $this->id_shop);

        foreach ($lgcookieslaw_cookies as $lgcookieslaw_cookie) {
            $lgcookieslaw_cookie_object = new LGCookiesLawCookie(
                (int) $lgcookieslaw_cookie[LGCookiesLawCookie::$definition['primary']],
                null,
                (int) $this->id_shop
            );

            $lgcookieslaw_cookie_object->delete();

            unset($lgcookieslaw_cookie_object);
        }
    }
}
