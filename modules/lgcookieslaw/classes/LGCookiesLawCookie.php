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
class LGCookiesLawCookie extends ObjectModel
{
    public $id_shop;
    public $id_lgcookieslaw_purpose;
    public $name;
    public $provider;
    public $provider_url;
    public $install_script = false;
    public $script_hook;
    public $add_script_tag = false;
    public $add_script_literal = false;
    public $script_notes;
    public $active = true;
    public $date_add;
    public $date_upd;

    public $cookie_purpose;
    public $expiry_time;
    public $script_code;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'lgcookieslaw_cookie',
        'primary' => 'id_lgcookieslaw_cookie',
        'multilang' => true,
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_lgcookieslaw_purpose' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'name' => ['type' => self::TYPE_STRING, 'size' => 64, 'required' => true],
            'provider' => ['type' => self::TYPE_STRING],
            'provider_url' => ['type' => self::TYPE_STRING, 'validate' => 'isAbsoluteUrl'],
            'install_script' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'script_hook' => ['type' => self::TYPE_STRING],
            'add_script_tag' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'add_script_literal' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'script_notes' => ['type' => self::TYPE_STRING, 'validate' => 'isMessage'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],

            'cookie_purpose' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'expiry_time' => ['type' => self::TYPE_STRING, 'lang' => true],
            'script_code' => ['type' => self::TYPE_HTML, 'lang' => true],
        ],
    ];

    public static function getInstallationDefaults()
    {
        $installation_defaults = [
            LGCookiesLawPurpose::FUNCTIONAL_PURPOSE => [
                [
                    'active' => true,
                    'name' => 'PHP_SESSID',
                    'provider' => Tools::getHttpHost(),
                    'provider_url' => '',
                    'cookie_purpose' => [
                        'es' => 'La cookie PHPSESSID es nativa de PHP y permite a los sitios web almacenar datos de ' .
                            'estado serializados. En el sitio web se utiliza para establecer una sesión de usuario y ' .
                            'para pasar los datos de estado a través de una cookie temporal, que se conoce ' .
                            'comúnmente como una cookie de sesión. Estas Cookies solo permanecerán en su ' .
                            'equipo hasta que cierre el navegador.',
                        'en' => 'The PHPSESSID cookie is native to PHP and allows websites to store serialised ' .
                            'status data. On the website it is used to establish a user session and to pass state ' .
                            'data through a temporary cookie, which is commonly known as a session cookie. These ' .
                            'Cookies will only remain on your computer until you close your browser.',
                        'fr' => 'Le cookie PHPSESSID est natif de PHP et permet aux sites web de stocker des ' .
                            'données d\'état sérialisées. Sur le site web, il est utilisé pour établir une ' .
                            'session d\'utilisateur et pour transmettre des données d\'état par le biais d\'un ' .
                            'cookie temporaire, communément appelé cookie de session. Ces cookies ne resteront ' .
                            'sur votre ordinateur que jusqu\'à ce que vous fermiez votre navigateur.',
                        'pl' => 'Plik cookie PHPSESSID jest natywny dla PHP i umożliwia stronom internetowym ' .
                            'przechowywanie zserializowanych danych o stanie. Na stronie internetowej służy do ' .
                            'ustanowienia sesji użytkownika i przekazania danych o stanie poprzez tymczasowe ' .
                            'ciasteczko, które jest powszechnie znane jako ciasteczko sesyjne. Te pliki cookie ' .
                            'pozostaną na Twoim komputerze tylko do momentu zamknięcia przeglądarki.',
                        'pt' => 'O cookie PHPSESSID é nativo de PHP e permite que os websites armazenem dados de ' .
                            'estado seriados. No sítio web é utilizado para estabelecer uma sessão do utilizador e ' .
                            'para passar dados de estado através de um cookie temporário, que é vulgarmente ' .
                            'conhecido como cookie de sessão. Estes Cookies só permanecerão no seu computador até ' .
                            'que feche o seu navegador.',
                        'nl' => 'De PHPSESSID-cookie is native voor PHP en stelt websites in staat om ' .
                            'geserialiseerde statusgegevens op te slaan. Op de website wordt het gebruikt om ' .
                            'een gebruikerssessie tot stand te brengen en om statusgegevens door te geven via ' .
                            'een tijdelijke cookie, die algemeen bekend staat als een sessiecookie. Deze cookies ' .
                            'blijven alleen op uw computer totdat u de browser sluit.',
                        'de' => 'Das PHPSESSID-Cookie ist PHP nativ und ermöglicht es Websites, serialisierte ' .
                            'Statusdaten zu speichern. Auf der Website wird es verwendet, um eine Benutzersitzung ' .
                            'aufzubauen und Statusdaten durch ein temporäres Cookie zu übergeben, das allgemein als ' .
                            'Sitzungscookie bekannt ist. Diese Cookies verbleiben nur auf Ihrem Computer, bis Sie ' .
                            'den Browser schließen.',
                        'it' => 'Il cookie PHPSESSID è nativo di PHP e permette ai siti web di memorizzare dati di ' .
                            'stato serializzati. Sul sito web viene utilizzato per stabilire una sessione ' .
                            'dell\'utente e per passare dati di stato attraverso un cookie temporaneo, comunemente ' .
                            'noto come cookie di sessione. Questi cookie rimarranno sul suo computer solo fino ' .
                            'alla chiusura del suo browser.',
                        'gb' => 'The PHPSESSID cookie is native to PHP and allows websites to store serialised ' .
                            'status data. On the website it is used to establish a user session and to pass state ' .
                            'data through a temporary cookie, which is commonly known as a session cookie. These ' .
                            'Cookies will only remain on your computer until you close your browser.',
                    ],
                    'expiry_time' => [
                        'es' => 'Sesión',
                        'en' => 'Session',
                        'fr' => 'Session',
                        'pl' => 'Sesja',
                        'pt' => 'Sessão',
                        'nl' => 'Sessie',
                        'de' => 'Sitzung',
                        'it' => 'Sessione',
                        'gb' => 'Session',
                    ],
                ],
                [
                    'active' => true,
                    'name' => 'PrestaShop-#',
                    'provider' => Tools::getHttpHost(),
                    'provider_url' => '',
                    'cookie_purpose' => [
                        'es' => 'Se trata de una cookie que usa Prestashop para guardar información y mantener ' .
                            'abierta la sesión del usuario. Permite guardar información como la divisa, el ' .
                            'idioma, identificador del cliente, entre otros datos necesarios para el ' .
                            'correcto funcionamiento de la tienda.',
                        'en' => 'This is a cookie used by Prestashop to store information and keep the user\'s ' .
                            'session open. It stores information such as currency, language, customer ID, among ' .
                            'other data necessary for the proper functioning of the shop.',
                        'fr' => 'Il s\'agit d\'un cookie utilisé par Prestashop pour stocker des informations et ' .
                            'garder la session de l\'utilisateur ouverte. Il stocke des informations telles que ' .
                            'la devise, la langue, l\'identifiant du client, entre autres données nécessaires au ' .
                            'bon fonctionnement de la boutique.',
                        'pl' => 'Jest to plik cookie, którego Prestashop używa do zapisywania informacji i ' .
                            'utrzymywania otwartej sesji użytkownika. Umożliwia zapisywanie informacji takich jak ' .
                            'waluta, język, identyfikator klienta między innymi danymi niezbędnymi do ' .
                            'prawidłowego funkcjonowania sklepu.',
                        'pt' => 'Este é um cookie utilizado pela Prestashop para armazenar informação e manter a ' .
                            'sessão do utilizador aberta. Armazena informações tais como moeda, língua, ' .
                            'identificação do cliente, entre outros dados necessários para o bom funcionamento da ' .
                            'loja.',
                        'nl' => 'Het is een cookie die Prestashop gebruikt om informatie op te slaan en de sessie ' .
                            'van de gebruiker open te houden. Hiermee kunt u informatie opslaan zoals valuta, ' .
                            'taal, klantidentificatie en andere gegevens die nodig zijn voor de goede werking ' .
                            'van de winkel.',
                        'de' => 'Es ist ein Cookie, das Prestashop verwendet, um Informationen zu speichern und die ' .
                            'itzung des Benutzers offen zu halten. Ermöglicht das Speichern von Informationen wie ' .
                            'Währung, Sprache, Kundenkennung und anderen Daten, die für das ordnungsgemäße ' .
                            'Funktionieren des Shops erforderlich sind.',
                        'it' => 'Questo è un cookie utilizzato da Prestashop per memorizzare informazioni e ' .
                            'mantenere aperta la sessione dell\'utente. Memorizza informazioni come la valuta, ' .
                            'la lingua, l\'ID del cliente, tra gli altri dati necessari per il corretto ' .
                            'funzionamento del negozio.',
                        'gb' => 'This is a cookie used by Prestashop to store information and keep the user\'s ' .
                            'session open. It stores information such as currency, language, customer ID, among ' .
                            'other data necessary for the proper functioning of the shop.',
                    ],
                    'expiry_time' => [
                        'es' => Configuration::get('PS_COOKIE_LIFETIME_FO') . ' horas',
                        'en' => Configuration::get('PS_COOKIE_LIFETIME_FO') . ' hours',
                        'fr' => Configuration::get('PS_COOKIE_LIFETIME_FO') . ' heures',
                        'pl' => Configuration::get('PS_COOKIE_LIFETIME_FO') . ' godziny',
                        'pt' => Configuration::get('PS_COOKIE_LIFETIME_FO') . ' horas',
                        'nl' => Configuration::get('PS_COOKIE_LIFETIME_FO') . ' uren',
                        'de' => Configuration::get('PS_COOKIE_LIFETIME_FO') . ' stunden',
                        'it' => Configuration::get('PS_COOKIE_LIFETIME_FO') . ' ore',
                        'gb' => Configuration::get('PS_COOKIE_LIFETIME_FO') . ' hours',
                    ],
                ],
                [
                    'active' => false,
                    'name' => 'rc::a',
                    'provider' => 'Google',
                    'provider_url' => 'https://policies.google.com/privacy',
                    'cookie_purpose' => [
                        'es' => 'Se usa para leer y filtrar solicitudes de bots.',
                        'en' => 'It is used to read and filter bot requests.',
                        'fr' => 'Il est utilisé pour lire et filtrer les requêtes des bots.',
                        'pl' => 'Służy do odczytywania i filtrowania żądań od botów.',
                        'pt' => 'Ele é usado para ler e filtrar solicitações de bots.',
                        'nl' => 'Het wordt gebruikt om botverzoeken te lezen en te filteren.',
                        'de' => 'Es wird verwendet, um Bot-Anfragen zu lesen und zu filtern.',
                        'it' => 'Viene utilizzato per leggere e filtrare le richieste dei bot.',
                        'gb' => 'It is used to read and filter bot requests.',
                    ],
                    'expiry_time' => [
                        'es' => 'Persistente',
                        'en' => 'Persistent',
                        'fr' => 'Persistant',
                        'pl' => 'Uporczywy',
                        'pt' => 'Persistente',
                        'nl' => 'Aanhoudend',
                        'de' => 'Hartnäckig',
                        'it' => 'Persistente',
                        'gb' => 'Persistent',
                    ],
                ],
                [
                    'active' => false,
                    'name' => 'rc::c',
                    'provider' => 'Google',
                    'provider_url' => 'https://policies.google.com/privacy',
                    'cookie_purpose' => [
                        'es' => 'Se usa para leer y filtrar solicitudes de bots.',
                        'en' => 'It is used to read and filter bot requests.',
                        'fr' => 'Il est utilisé pour lire et filtrer les requêtes des bots.',
                        'pl' => 'Służy do odczytywania i filtrowania żądań od botów.',
                        'pt' => 'Ele é usado para ler e filtrar solicitações de bots.',
                        'nl' => 'Het wordt gebruikt om botverzoeken te lezen en te filteren.',
                        'de' => 'Es wird verwendet, um Bot-Anfragen zu lesen und zu filtern.',
                        'it' => 'Viene utilizzato per leggere e filtrare le richieste dei bot.',
                        'gb' => 'It is used to read and filter bot requests.',
                    ],
                    'expiry_time' => [
                        'es' => 'Persistente',
                        'en' => 'Persistent',
                        'fr' => 'Persistant',
                        'pl' => 'Uporczywy',
                        'pt' => 'Persistente',
                        'nl' => 'Aanhoudend',
                        'de' => 'Hartnäckig',
                        'it' => 'Persistente',
                        'gb' => 'Persistent',
                    ],
                ],
            ],
            LGCookiesLawPurpose::MARKETING_PURPOSE => [
                [
                    'active' => false,
                    'name' => 'fr',
                    'provider' => 'Facebook',
                    'provider_url' => 'https://www.facebook.com/policies/cookies/',
                    'cookie_purpose' => [
                        'es' => 'Utilizada por Facebook para proporcionar una serie de productos publicitarios como pujas en tiempo real de terceros anunciantes.',
                        'en' => 'Used by Facebook to deliver a series of advertisement products such as real time bidding from third party advertisers.',
                        'fr' => 'Utilisé par Facebook pour fournir une série de produits publicitaires tels que les offres en temps réel d\'annonceurs tiers.',
                        'pl' => 'Używany przez Facebooka do dostarczania serii produktów reklamowych, takich jak licytowanie w czasie rzeczywistym od reklamodawców zewnętrznych.',
                        'pt' => 'Usado pelo Facebook para entregar uma série de produtos de publicidade, como lances em tempo real de anunciantes terceiros.',
                        'nl' => 'Gebruikt door Facebook om een reeks advertentieproducten te leveren, zoals realtime bieden van externe adverteerders.',
                        'de' => 'Wird von Facebook genutzt, um eine Reihe von Werbeprodukten anzuzeigen, zum Beispiel Echtzeitgebote dritter Werbetreibender.',
                        'it' => 'Utilizzato da Facebook per fornire una serie di prodotti pubblicitari come offerte in tempo reale da inserzionisti terzi.',
                        'gb' => 'Used by Facebook to deliver a series of advertisement products such as real time bidding from third party advertisers.',
                    ],
                    'expiry_time' => [
                        'es' => '3 meses',
                        'en' => '3 months',
                        'fr' => '3 mois',
                        'pl' => '3 miesiące',
                        'pt' => '3 meses',
                        'nl' => '3 maanden',
                        'de' => '3 Monate',
                        'it' => '3 mesi',
                        'gb' => '3 months',
                    ],
                ],
                [
                    'active' => false,
                    'name' => '_fbp',
                    'provider' => 'Facebook',
                    'provider_url' => 'https://www.facebook.com/policies/cookies/',
                    'cookie_purpose' => [
                        'es' => 'Utilizada por Facebook para proporcionar una serie de productos publicitarios como pujas en tiempo real de terceros anunciantes.',
                        'en' => 'Used by Facebook to deliver a series of advertisement products such as real time bidding from third party advertisers.',
                        'fr' => 'Utilisé par Facebook pour fournir une série de produits publicitaires tels que les offres en temps réel d\'annonceurs tiers.',
                        'pl' => 'Używany przez Facebooka do dostarczania serii produktów reklamowych, takich jak licytowanie w czasie rzeczywistym od reklamodawców zewnętrznych.',
                        'pt' => 'Usado pelo Facebook para entregar uma série de produtos de publicidade, como lances em tempo real de anunciantes terceiros.',
                        'nl' => 'Gebruikt door Facebook om een reeks advertentieproducten te leveren, zoals realtime bieden van externe adverteerders.',
                        'de' => 'Wird von Facebook genutzt, um eine Reihe von Werbeprodukten anzuzeigen, zum Beispiel Echtzeitgebote dritter Werbetreibender.',
                        'it' => 'Utilizzato da Facebook per fornire una serie di prodotti pubblicitari come offerte in tempo reale da inserzionisti terzi.',
                        'gb' => 'Used by Facebook to deliver a series of advertisement products such as real time bidding from third party advertisers.',
                    ],
                    'expiry_time' => [
                        'es' => '3 meses',
                        'en' => '3 months',
                        'fr' => '3 mois',
                        'pl' => '3 miesiące',
                        'pt' => '3 meses',
                        'nl' => '3 maanden',
                        'de' => '3 Monate',
                        'it' => '3 mesi',
                        'gb' => '3 months',
                    ],
                ],
                [
                    'active' => false,
                    'name' => 'tr',
                    'provider' => 'Facebook',
                    'provider_url' => 'https://www.facebook.com/policies/cookies/',
                    'cookie_purpose' => [
                        'es' => 'Utilizada por Facebook para proporcionar una serie de productos publicitarios como pujas en tiempo real de terceros anunciantes.',
                        'en' => 'Used by Facebook to deliver a series of advertisement products such as real time bidding from third party advertisers.',
                        'fr' => 'Utilisé par Facebook pour fournir une série de produits publicitaires tels que les offres en temps réel d\'annonceurs tiers.',
                        'pl' => 'Używany przez Facebooka do dostarczania serii produktów reklamowych, takich jak licytowanie w czasie rzeczywistym od reklamodawców zewnętrznych.',
                        'pt' => 'Usado pelo Facebook para entregar uma série de produtos de publicidade, como lances em tempo real de anunciantes terceiros.',
                        'nl' => 'Gebruikt door Facebook om een reeks advertentieproducten te leveren, zoals realtime bieden van externe adverteerders.',
                        'de' => 'Wird von Facebook genutzt, um eine Reihe von Werbeprodukten anzuzeigen, zum Beispiel Echtzeitgebote dritter Werbetreibender.',
                        'it' => 'Utilizzato da Facebook per fornire una serie di prodotti pubblicitari come offerte in tempo reale da inserzionisti terzi.',
                        'gb' => 'Used by Facebook to deliver a series of advertisement products such as real time bidding from third party advertisers.',
                    ],
                    'expiry_time' => [
                        'es' => 'Sesión',
                        'en' => 'Session',
                        'fr' => 'Session',
                        'pl' => 'Sesja',
                        'pt' => 'Sessão',
                        'nl' => 'Sessie',
                        'de' => 'Session',
                        'it' => 'Sessione',
                        'gb' => 'Session',
                    ],
                ],
                [
                    'active' => false,
                    'name' => 'VISITOR_INFO1_LIVE',
                    'provider' => ' youtube.com',
                    'provider_url' => 'https://policies.google.com/technologies/cookies',
                    'cookie_purpose' => [
                        'es' => 'Intenta calcular el ancho de banda del usuario en páginas con vídeos de YouTube integrados.',
                        'en' => 'Tries to estimate the users\' bandwidth on pages with integrated YouTube videos.',
                        'fr' => 'Tente d\'estimer la bande passante des utilisateurs sur des pages avec des vidéos YouTube intégrées.',
                        'pl' => 'Próbuje oszacować przepustowość użytkowników na stronach ze zintegrowanymi filmami z YouTube.',
                        'pt' => 'Tenta estimar a largura de banda dos usuários em páginas com vídeos integrados do YouTube.',
                        'nl' => 'Probeert de bandbreedte van gebruikers te schatten op pagina\'s met geïntegreerde YouTube-video\'s.',
                        'de' => 'Versucht, die Benutzerbandbreite auf Seiten mit integrierten YouTube-Videos zu schätzen.',
                        'it' => 'Prova a stimare la velocità della connessione dell\'utente su pagine con video YouTube integrati.',
                        'gb' => 'Tries to estimate the users\' bandwidth on pages with integrated YouTube videos.',
                    ],
                    'expiry_time' => [
                        'es' => '179 días',
                        'en' => '179 days',
                        'fr' => '179 jours',
                        'pl' => '179 dni',
                        'pt' => '179 dias',
                        'nl' => '179 dagen',
                        'de' => '179 Tage',
                        'it' => '179 giorni',
                        'gb' => '179 days',
                    ],
                ],
                [
                    'active' => false,
                    'name' => 'YSC',
                    'provider' => ' youtube.com',
                    'provider_url' => 'https://policies.google.com/technologies/cookies',
                    'cookie_purpose' => [
                        'es' => 'Registra una identificación única para mantener estadísticas de qué vídeos de YouTube ha visto el usuario.',
                        'en' => 'Registers a unique ID to keep statistics of what videos from YouTube the user has seen.',
                        'fr' => 'Enregistre un identifiant unique pour conserver des statistiques sur les vidéos de YouTube vues par l\'utilisateur.',
                        'pl' => 'Rejestruje unikalny identyfikator, aby prowadzić statystyki dotyczące filmów wideo z YouTube, które widział użytkownik.',
                        'pt' => 'Registra um ID único para manter estatísticas de quais vídeos do YouTube o usuário viu.',
                        'nl' => 'Registreert een unieke ID om statistieken bij te houden van welke video\'s van YouTube de gebruiker heeft gezien.',
                        'de' => 'Registriert eine eindeutige ID, um Statistiken der Videos von YouTube, die der Benutzer gesehen hat, zu behalten.',
                        'it' => 'Registra un ID univoco per statistiche legate a quali video YouTube sono stati visualizzati dall\'utente.',
                        'gb' => 'Registers a unique ID to keep statistics of what videos from YouTube the user has seen.',
                    ],
                    'expiry_time' => [
                        'es' => 'Sesión',
                        'en' => 'Session',
                        'fr' => 'Session',
                        'pl' => 'Sesja',
                        'pt' => 'Sessão',
                        'nl' => 'Sessie',
                        'de' => 'Session',
                        'it' => 'Sessione',
                        'gb' => 'Session',
                    ],
                ],
                [
                    'active' => false,
                    'name' => 'yt-remote-cast-installed',
                    'provider' => ' youtube.com',
                    'provider_url' => 'https://policies.google.com/technologies/cookies',
                    'cookie_purpose' => [
                        'es' => 'Registra las preferencias del reproductor de vídeo del usuario al ver vídeos incrustados de YouTube.',
                        'en' => 'Stores the user\'s video player preferences using embedded YouTube video',
                        'fr' => 'Stocke les préférences de lecture vidéo de l\'utilisateur pour les vidéos YouTube incorporées.',
                        'pl' => 'Przechowuje preferencje odtwarzacza wideo użytkownika za pomocą osadzonego wideo YouTube.',
                        'pt' => 'Armazena as preferências do player de vídeo do usuário usando o vídeo do YouTube incorporado.',
                        'nl' => 'Bewaart de voorkeuren van de videospeler van de gebruiker met ingesloten YouTube-video',
                        'de' => 'Registriert eine eindeutige ID, um Statistiken der Videos von YouTube, die der Benutzer gesehen hat, zu behalten.',
                        'it' => 'Memorizza le preferenze del lettore video dell\'utente usando il video YouTube incorporato',
                        'gb' => 'Stores the user\'s video player preferences using embedded YouTube video',
                    ],
                    'expiry_time' => [
                        'es' => 'Sesión',
                        'en' => 'Session',
                        'fr' => 'Session',
                        'pl' => 'Sesja',
                        'pt' => 'Sessão',
                        'nl' => 'Sessie',
                        'de' => 'Session',
                        'it' => 'Sessione',
                        'gb' => 'Session',
                    ],
                ],
                [
                    'active' => false,
                    'name' => 'yt-remote-connected-devices',
                    'provider' => ' youtube.com',
                    'provider_url' => 'https://policies.google.com/technologies/cookies',
                    'cookie_purpose' => [
                        'es' => 'Registra las preferencias del reproductor de vídeo del usuario al ver vídeos incrustados de YouTube.',
                        'en' => 'Stores the user\'s video player preferences using embedded YouTube video',
                        'fr' => 'Stocke les préférences de lecture vidéo de l\'utilisateur pour les vidéos YouTube incorporées.',
                        'pl' => 'Przechowuje preferencje odtwarzacza wideo użytkownika za pomocą osadzonego wideo YouTube.',
                        'pt' => 'Armazena as preferências do player de vídeo do usuário usando o vídeo do YouTube incorporado.',
                        'nl' => 'Bewaart de voorkeuren van de videospeler van de gebruiker met ingesloten YouTube-video',
                        'de' => 'Registriert eine eindeutige ID, um Statistiken der Videos von YouTube, die der Benutzer gesehen hat, zu behalten.',
                        'it' => 'Memorizza le preferenze del lettore video dell\'utente usando il video YouTube incorporato',
                        'gb' => 'Stores the user\'s video player preferences using embedded YouTube video',
                    ],
                    'expiry_time' => [
                        'es' => 'Persistente',
                        'en' => 'Persistent',
                        'fr' => 'Persistant',
                        'pl' => 'Trwały',
                        'pt' => 'Persistente',
                        'nl' => 'Aanhoudend',
                        'de' => 'Hartnäckig',
                        'it' => 'Persistente',
                        'gb' => 'Persistent',
                    ],
                ],
                [
                    'active' => false,
                    'name' => 'yt-remote-device-id',
                    'provider' => ' youtube.com',
                    'provider_url' => 'https://policies.google.com/technologies/cookies',
                    'cookie_purpose' => [
                        'es' => 'Registra las preferencias del reproductor de vídeo del usuario al ver vídeos incrustados de YouTube.',
                        'en' => 'Stores the user\'s video player preferences using embedded YouTube video',
                        'fr' => 'Stocke les préférences de lecture vidéo de l\'utilisateur pour les vidéos YouTube incorporées.',
                        'pl' => 'Przechowuje preferencje odtwarzacza wideo użytkownika za pomocą osadzonego wideo YouTube.',
                        'pt' => 'Armazena as preferências do player de vídeo do usuário usando o vídeo do YouTube incorporado.',
                        'nl' => 'Bewaart de voorkeuren van de videospeler van de gebruiker met ingesloten YouTube-video',
                        'de' => 'Registriert eine eindeutige ID, um Statistiken der Videos von YouTube, die der Benutzer gesehen hat, zu behalten.',
                        'it' => 'Memorizza le preferenze del lettore video dell\'utente usando il video YouTube incorporato',
                        'gb' => 'Stores the user\'s video player preferences using embedded YouTube video',
                    ],
                    'expiry_time' => [
                        'es' => 'Persistente',
                        'en' => 'Persistent',
                        'fr' => 'Persistant',
                        'pl' => 'Trwały',
                        'pt' => 'Persistente',
                        'nl' => 'Aanhoudend',
                        'de' => 'Hartnäckig',
                        'it' => 'Persistente',
                        'gb' => 'Persistent',
                    ],
                ],
                [
                    'active' => false,
                    'name' => 'yt-remote-fast-check-period',
                    'provider' => ' youtube.com',
                    'provider_url' => 'https://policies.google.com/technologies/cookies',
                    'cookie_purpose' => [
                        'es' => 'Registra las preferencias del reproductor de vídeo del usuario al ver vídeos incrustados de YouTube.',
                        'en' => 'Stores the user\'s video player preferences using embedded YouTube video',
                        'fr' => 'Stocke les préférences de lecture vidéo de l\'utilisateur pour les vidéos YouTube incorporées.',
                        'pl' => 'Przechowuje preferencje odtwarzacza wideo użytkownika za pomocą osadzonego wideo YouTube.',
                        'pt' => 'Armazena as preferências do player de vídeo do usuário usando o vídeo do YouTube incorporado.',
                        'nl' => 'Bewaart de voorkeuren van de videospeler van de gebruiker met ingesloten YouTube-video',
                        'de' => 'Registriert eine eindeutige ID, um Statistiken der Videos von YouTube, die der Benutzer gesehen hat, zu behalten.',
                        'it' => 'Memorizza le preferenze del lettore video dell\'utente usando il video YouTube incorporato',
                        'gb' => 'Stores the user\'s video player preferences using embedded YouTube video',
                    ],
                    'expiry_time' => [
                        'es' => 'Sesión',
                        'en' => 'Session',
                        'fr' => 'Session',
                        'pl' => 'Sesja',
                        'pt' => 'Sessão',
                        'nl' => 'Sessie',
                        'de' => 'Session',
                        'it' => 'Sessione',
                        'gb' => 'Session',
                    ],
                ],
                [
                    'active' => false,
                    'name' => 'yt-remote-session-app',
                    'provider' => ' youtube.com',
                    'provider_url' => 'https://policies.google.com/technologies/cookies',
                    'cookie_purpose' => [
                        'es' => 'Registra las preferencias del reproductor de vídeo del usuario al ver vídeos incrustados de YouTube.',
                        'en' => 'Stores the user\'s video player preferences using embedded YouTube video',
                        'fr' => 'Stocke les préférences de lecture vidéo de l\'utilisateur pour les vidéos YouTube incorporées.',
                        'pl' => 'Przechowuje preferencje odtwarzacza wideo użytkownika za pomocą osadzonego wideo YouTube.',
                        'pt' => 'Armazena as preferências do player de vídeo do usuário usando o vídeo do YouTube incorporado.',
                        'nl' => 'Bewaart de voorkeuren van de videospeler van de gebruiker met ingesloten YouTube-video',
                        'de' => 'Registriert eine eindeutige ID, um Statistiken der Videos von YouTube, die der Benutzer gesehen hat, zu behalten.',
                        'it' => 'Memorizza le preferenze del lettore video dell\'utente usando il video YouTube incorporato',
                        'gb' => 'Stores the user\'s video player preferences using embedded YouTube video',
                    ],
                    'expiry_time' => [
                        'es' => 'Sesión',
                        'en' => 'Session',
                        'fr' => 'Session',
                        'pl' => 'Sesja',
                        'pt' => 'Sessão',
                        'nl' => 'Sessie',
                        'de' => 'Session',
                        'it' => 'Sessione',
                        'gb' => 'Session',
                    ],
                ],
                [
                    'active' => false,
                    'name' => 'yt-remote-session-name',
                    'provider' => ' youtube.com',
                    'provider_url' => 'https://policies.google.com/technologies/cookies',
                    'cookie_purpose' => [
                        'es' => 'Registra las preferencias del reproductor de vídeo del usuario al ver vídeos incrustados de YouTube.',
                        'en' => 'Stores the user\'s video player preferences using embedded YouTube video',
                        'fr' => 'Stocke les préférences de lecture vidéo de l\'utilisateur pour les vidéos YouTube incorporées.',
                        'pl' => 'Przechowuje preferencje odtwarzacza wideo użytkownika za pomocą osadzonego wideo YouTube.',
                        'pt' => 'Armazena as preferências do player de vídeo do usuário usando o vídeo do YouTube incorporado.',
                        'nl' => 'Bewaart de voorkeuren van de videospeler van de gebruiker met ingesloten YouTube-video',
                        'de' => 'Registriert eine eindeutige ID, um Statistiken der Videos von YouTube, die der Benutzer gesehen hat, zu behalten.',
                        'it' => 'Memorizza le preferenze del lettore video dell\'utente usando il video YouTube incorporato',
                        'gb' => 'Stores the user\'s video player preferences using embedded YouTube video',
                    ],
                    'expiry_time' => [
                        'es' => 'Sesión',
                        'en' => 'Session',
                        'fr' => 'Session',
                        'pl' => 'Sesja',
                        'pt' => 'Sessão',
                        'nl' => 'Sessie',
                        'de' => 'Session',
                        'it' => 'Sessione',
                        'gb' => 'Session',
                    ],
                ],
                [
                    'active' => false,
                    'name' => 'ads/ga-audiences',
                    'provider' => 'Google',
                    'provider_url' => 'https://policies.google.com/privacy',
                    'cookie_purpose' => [
                        'es' => 'Google AdWords utiliza estas cookies para volver a atraer a los visitantes que probablemente se conviertan en clientes en función del comportamiento en línea del visitante en los sitios web.',
                        'en' => 'These cookies are used by Google AdWords to re-engage visitors that are likely to convert to customers based on the visitor’s online behaviour across websites.',
                        'fr' => 'Ces cookies sont utilisés par Google AdWords pour réengager les visiteurs susceptibles de se convertir en clients en fonction du comportement en ligne du visiteur sur les sites Web.',
                        'pl' => 'Te pliki cookie są używane przez Google AdWords do ponownego angażowania użytkowników, którzy mogą przekształcić się w klientów na podstawie zachowania użytkownika online w różnych witrynach.',
                        'pt' => 'Esses cookies são usados pelo Google AdWords para reconquistar visitantes que provavelmente se converterão em clientes com base no comportamento online do visitante nos sites.',
                        'nl' => 'Deze cookies worden door Google AdWords gebruikt om bezoekers opnieuw aan te spreken die waarschijnlijk in klanten zullen worden omgezet op basis van het online gedrag van de bezoeker op verschillende websites.',
                        'de' => 'Diese Cookies werden von Google AdWords verwendet, um Besucher wieder einzubeziehen, die aufgrund des Online-Verhaltens des Besuchers auf verschiedenen Websites wahrscheinlich zu Kunden werden.',
                        'it' => 'Questi cookie vengono utilizzati da Google AdWords per coinvolgere nuovamente i visitatori che potrebbero convertirsi in clienti in base al comportamento online del visitatore sui siti web.',
                        'gb' => 'These cookies are used by Google AdWords to re-engage visitors that are likely to convert to customers based on the visitor’s online behaviour across websites.',
                    ],
                    'expiry_time' => [
                        'es' => 'Sesión',
                        'en' => 'Session',
                        'fr' => 'Session',
                        'pl' => 'Sesja',
                        'pt' => 'Sessão',
                        'nl' => 'Sessie',
                        'de' => 'Session',
                        'it' => 'Sessione',
                        'gb' => 'Session',
                    ],
                ],
            ],
            LGCookiesLawPurpose::ANALYTICS_PURPOSE => [
                [
                    'active' => false,
                    'name' => '_ga',
                    'provider' => 'Google',
                    'provider_url' => 'https://policies.google.com/privacy',
                    'cookie_purpose' => [
                        'es' => 'Registra una identificación única que se utiliza para generar datos estadísticos acerca de cómo utiliza el visitante el sitio web.',
                        'en' => 'Registers a unique ID that is used to generate statistical data on how the visitor uses the website.',
                        'fr' => 'Enregistre un identifiant unique utilisé pour générer des données statistiques sur la façon dont le visiteur utilise le site.',
                        'pl' => 'Rejestruje unikalny identyfikator, który służy do generowania danych statystycznych dotyczących sposobu, w jaki odwiedzający korzysta ze strony internetowej.',
                        'pt' => 'Registra um ID exclusivo que é usado para gerar dados estatísticos sobre como o visitante usa o site.',
                        'nl' => 'Registreert een uniek ID die wordt gebruikt om statistische gegevens te genereren over hoe de bezoeker de website gebruikt.',
                        'de' => 'Registriert eine eindeutige ID, die verwendet wird, um statistische Daten dazu, wie der Besucher die Website nutzt, zu generieren.',
                        'it' => 'Registra un ID univoco utilizzato per generare dati statistici su come il visitatore utilizza il sito internet.',
                        'gb' => 'Registers a unique ID that is used to generate statistical data on how the visitor uses the website.',
                    ],
                    'expiry_time' => [
                        'es' => '2 años',
                        'en' => '2 years',
                        'fr' => '2 années',
                        'pl' => '2 lata',
                        'pt' => '2 anos',
                        'nl' => '2 jaar',
                        'de' => '2 Jahre',
                        'it' => '2 anni',
                        'gb' => '2 years',
                    ],
                ],
                [
                    'active' => false,
                    'name' => '_gat',
                    'provider' => 'Google',
                    'provider_url' => 'https://policies.google.com/privacy',
                    'cookie_purpose' => [
                        'es' => 'Utilizado por Google Analytics para controlar la tasa de peticiones',
                        'en' => 'Used by Google Analytics to throttle request rate',
                        'fr' => 'Utilisé par Google Analytics pour diminuer radicalement le taux de requêtes',
                        'pl' => 'Używany przez Google Analytics do ograniczania liczby żądań',
                        'pt' => 'Usado pelo Google Analytics para controlar a taxa de solicitação',
                        'nl' => 'Gebruikt door Google Analytics om verzoeksnelheid te vertragen',
                        'de' => 'Wird von Google Analytics verwendet, um die Anforderungsrate einzuschränken',
                        'it' => 'Utilizzato da Google Analytics per limitare la frequenza delle richieste',
                        'gb' => 'Used by Google Analytics to throttle request rate',
                    ],
                    'expiry_time' => [
                        'es' => '1 día',
                        'en' => '1 day',
                        'fr' => '1 jour',
                        'pl' => '1 dzień',
                        'pt' => '1 dia',
                        'nl' => '1 dag',
                        'de' => '1 Tag',
                        'it' => '1 giorno',
                        'gb' => '1 day',
                    ],
                ],
                [
                    'active' => false,
                    'name' => '_gid',
                    'provider' => 'Google',
                    'provider_url' => 'https://policies.google.com/privacy',
                    'cookie_purpose' => [
                        'es' => 'Registra una identificación única que se utiliza para generar datos estadísticos acerca de cómo utiliza el visitante el sitio web.',
                        'en' => 'Registers a unique ID that is used to generate statistical data on how the visitor uses the website.',
                        'fr' => 'Enregistre un identifiant unique utilisé pour générer des données statistiques sur la façon dont le visiteur utilise le site.',
                        'pl' => 'Rejestruje unikalny identyfikator, który służy do generowania danych statystycznych dotyczących sposobu, w jaki odwiedzający korzysta ze strony internetowej.',
                        'pt' => 'Registra um ID exclusivo que é usado para gerar dados estatísticos sobre como o visitante usa o site.',
                        'nl' => 'Registreert een uniek ID die wordt gebruikt om statistische gegevens te genereren over hoe de bezoeker de website gebruikt.',
                        'de' => 'Registriert eine eindeutige ID, die verwendet wird, um statistische Daten dazu, wie der Besucher die Website nutzt, zu generieren.',
                        'it' => 'Registra un ID univoco utilizzato per generare dati statistici su come il visitatore utilizza il sito internet.',
                        'gb' => 'Registers a unique ID that is used to generate statistical data on how the visitor uses the website.',
                    ],
                    'expiry_time' => [
                        'es' => '1 día',
                        'en' => '1 day',
                        'fr' => '1 jour',
                        'pl' => '1 dzień',
                        'pt' => '1 dia',
                        'nl' => '1 dag',
                        'de' => '1 Tag',
                        'it' => '1 giorno',
                        'gb' => '1 day',
                    ],
                ],
                [
                    'active' => false,
                    'name' => '_gd#',
                    'provider' => 'Google',
                    'provider_url' => 'https://policies.google.com/privacy',
                    'cookie_purpose' => [
                        'es' => 'Se trata de una cookie de sesión de Google Analytics que se utiliza para generar datos estadísticos sobre cómo utiliza el sitio web que se elimina cuando sale de su navegador.',
                        'en' => 'This is a Google Analytics Session cookie used to generate statistical data on how you use the website which is removed when you quit your browser.',
                        'fr' => 'Il s\'agit d\'un cookie de session Google Analytics utilisé pour générer des données statistiques sur la façon dont vous utilisez le site Web, qui est supprimé lorsque vous quittez votre navigateur.',
                        'pl' => 'To jest sesyjny plik cookie Google Analytics służący do generowania danych statystycznych o sposobie korzystania ze strony internetowej, który jest usuwany po zamknięciu przeglądarki.',
                        'pt' => 'Este é um cookie de sessão do Google Analytics usado para gerar dados estatísticos sobre como você usa o site, que são removidos quando você fecha o navegador.',
                        'nl' => 'Dit is een Google Analytics-sessiecookie die wordt gebruikt om statistische gegevens te genereren over hoe u de website gebruikt en die wordt verwijderd wanneer u uw browser afsluit.',
                        'de' => 'Dies ist ein Google Analytics-Sitzungscookie, mit dem statistische Daten zur Nutzung der Website generiert werden, die beim Beenden Ihres Browsers entfernt werden.',
                        'it' => 'Si tratta di un cookie di sessione di Google Analytics utilizzato per generare dati statistici su come utilizzi il sito web che vengono rimossi quando chiudi il browser.',
                        'gb' => 'This is a Google Analytics Session cookie used to generate statistical data on how you use the website which is removed when you quit your browser.',
                    ],
                    'expiry_time' => [
                        'es' => 'Sesión',
                        'en' => 'Session',
                        'fr' => 'Session',
                        'pl' => 'Sesja',
                        'pt' => 'Sessão',
                        'nl' => 'Sessie',
                        'de' => 'Session',
                        'it' => 'Sessione',
                        'gb' => 'Session',
                    ],
                ],
                [
                    'active' => false,
                    'name' => '_gat_gtag_UA_#',
                    'provider' => 'Google',
                    'provider_url' => 'https://policies.google.com/privacy',
                    'cookie_purpose' => [
                        'es' => 'Se utiliza para acelerar la tasa de solicitudes.',
                        'en' => 'Used to throttle request rate.',
                        'fr' => 'Utilisé pour limiter le taux de demande.',
                        'pl' => 'Służy do ograniczania szybkości żądań.',
                        'pt' => 'Usado para controlar a taxa de solicitação.',
                        'nl' => 'Wordt gebruikt om de verzoeksnelheid te vertragen.',
                        'de' => 'Wird verwendet, um die Anforderungsrate zu drosseln.',
                        'it' => 'Utilizzato per limitare la frequenza delle richieste.',
                        'gb' => 'Used to throttle request rate.',
                    ],
                    'expiry_time' => [
                        'es' => '1 minuto',
                        'en' => '1 minute',
                        'fr' => '1 minute',
                        'pl' => '1 minuta',
                        'pt' => '1 minuto',
                        'nl' => '1 minuut',
                        'de' => '1 Minute',
                        'it' => '1 minuto',
                        'gb' => '1 minute',
                    ],
                ],
            ],
        ];

        return $installation_defaults;
    }

    public static function getCookies($id_lang = null, $id_shop = null, $active = false, $except_technicals = false)
    {
        $context = Context::getContext();

        if (is_null($id_lang)) {
            $id_lang = $context->language->id;
        }

        if (is_null($id_shop)) {
            $id_shop = $context->shop->id;
        }

        $query = new DbQuery();

        $query->select('a.*, b.`cookie_purpose`, b.`expiry_time`');
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

        if ($except_technicals) {
            $query->leftJoin(
                LGCookiesLawPurpose::$definition['table'],
                'c',
                '(c.`' . LGCookiesLawPurpose::$definition['primary'] . '` = a.`' .
                LGCookiesLawPurpose::$definition['primary'] . '`)'
            );

            $query->where('c.`technical` = 0');
        }

        $query->orderBy('a.`name`');

        return Db::getInstance()->executeS($query);
    }

    public static function getCookiesByPurpose($id_lgcookieslaw_purpose, $id_lang = null, $id_shop = null, $active = false)
    {
        $context = Context::getContext();

        if (is_null($id_lang)) {
            $id_lang = $context->language->id;
        }

        if (is_null($id_shop)) {
            $id_shop = $context->shop->id;
        }

        $query = new DbQuery();

        $query->select('a.*, b.`cookie_purpose`, b.`expiry_time`, b.`script_code`');
        $query->from(self::$definition['table'], 'a');
        $query->where('a.`' . LGCookiesLawPurpose::$definition['primary'] . '` = ' . (int) $id_lgcookieslaw_purpose);
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

        $query->orderBy('a.`name`');

        return Db::getInstance()->executeS($query);
    }

    public static function getCookiesLiteByPurpose($id_lgcookieslaw_purpose, $id_lang = null, $id_shop = null, $active = false)
    {
        $context = Context::getContext();

        if (is_null($id_lang)) {
            $id_lang = $context->language->id;
        }

        if (is_null($id_shop)) {
            $id_shop = $context->shop->id;
        }

        $query = new DbQuery();

        $query->select('a.`name`, a.`provider`, a.`provider_url`, b.`cookie_purpose`, b.`expiry_time`');
        $query->from(self::$definition['table'], 'a');
        $query->where('a.`' . LGCookiesLawPurpose::$definition['primary'] . '` = ' . (int) $id_lgcookieslaw_purpose);
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

        $query->orderBy('a.`name`');

        return Db::getInstance()->executeS($query);
    }
}
