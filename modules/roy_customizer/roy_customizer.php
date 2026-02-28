<?php

/**
 * 
 * Modez Customizer 1.4.0
 * @author    roythemes.com
 * @package   Modez Theme
 * @copyright 2013-2023 RoyThemes
 * 
 */

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

if (!defined('_PS_VERSION_'))
    exit;

class Roy_Customizer extends Module
{

    private $systemFonts = array("Arial", "Georgia", "Tahoma", "Times New Roman", "Verdana");

    private $gfonts = "Jost;Josefin Sans;Nunito;Inter;Lexend;Roboto;Fira Sans;Montserrat;Lato;Poppins;Syne;Rubik;Quicksand;Manrope;Space Grotesk;DM Sans;Libre Franklin;Lora;Cardo;PT Serif;Cormorant;Caudex;Vollkorn;ABeeZee;Abel;Abril Fatface;Aclonica;Acme;Actor;Adamina;Advent Pro;Aguafina Script;Akronim;Aladin;Aldrich;Alef;Alegreya;Alegreya SC;Alegreya Sans;Alegreya Sans SC;Alex Brush;Alfa Slab One;Alice;Alike;Alike Angular;Allan;Allerta;Allerta Stencil;Allura;Almendra;Almendra Display;Almendra SC;Amarante;Amaranth;Amatic SC;Amethysta;Amiri;Amita;Anaheim;Andada;Andika;Angkor;Annie Use Your Telescope;Anonymous Pro;Antic;Antic Didone;Antic Slab;Anton;Arapey;Arbutus;Arbutus Slab;Architects Daughter;Archivo Black;Archivo Narrow;Arimo;Arizonia;Armata;Artifika;Arvo;Arya;Asap;Asar;Asset;Astloch;Asul;Atomic Age;Aubrey;Audiowide;Autour One;Average;Average Sans;Averia Gruesa Libre;Averia Libre;Averia Sans Libre;Averia Serif Libre;Bad Script;Balthazar;Bangers;Basic;Barlow;Battambang;Baumans;Bayon;Belgrano;Belleza;BenchNine;Bentham;Berkshire Swash;Bevan;Bigelow Rules;Bigshot One;Bilbo;Bilbo Swash Caps;Biryani;Bitter;Black Ops One;Bokor;Bonbon;Boogaloo;Bowlby One;Bowlby One SC;Brawler;Bree Serif;Bubblegum Sans;Bubbler One;Buda;Buenard;Butcherman;Butterfly Kids;Cabin;Cabin Condensed;Cabin Sketch;Caesar Dressing;Cagliostro;Calligraffitti;Cambay;Cambo;Candal;Cantarell;Cantata One;Cantora One;Capriola;Carme;Carrois Gothic;Carrois Gothic SC;Carter One;Catamaran;Caveat;Caveat Brush;Cedarville Cursive;Ceviche One;Changa One;Chango;Chau Philomene One;Chela One;Chelsea Market;Chenla;Cherry Cream Soda;Cherry Swash;Chewy;Chicle;Chivo;Chonburi;Cinzel;Cinzel Decorative;Circe;Clicker Script;Coda;Coda Caption;Codystar;Combo;Comfortaa;Coming Soon;Concert One;Condiment;Content;Contrail One;Convergence;Cookie;Copse;Corben;Courgette;Cousine;Coustard;Covered By Your Grace;Crafty Girls;Creepster;Crete Round;Crimson Text;Croissant One;Crushed;Cuprum;Cutive;Cutive Mono;Damion;Dancing Script;Dangrek;Dawning of a New Day;Days One;Dekko;Delius;Delius Swash Caps;Delius Unicase;Della Respira;Denk One;Devonshire;Dhurjati;Didact Gothic;Diplomata;Diplomata SC;Domine;Donegal One;Doppio One;Dorsa;Dosis;Dr Sugiyama;Droid Sans;Droid Sans Mono;Droid Serif;Duru Sans;Dynalight;EB Garamond;Eagle Lake;Eater;Economica;Eczar;Ek Mukta;Electrolize;Elsie;Elsie Swash Caps;Emblema One;Emilys Candy;Engagement;Englebert;Enriqueta;Erica One;Esteban;Euphoria Script;Ewert;Exo;Exo 2;Expletus Sans;Fanwood Text;Fascinate;Fascinate Inline;Faster One;Fasthand;Fauna One;Federant;Federo;Felipa;Fenix;Finger Paint;Fira Mono;Fjalla One;Fjord One;Flamenco;Flavors;Fondamento;Fontdiner Swanky;Forum;Francois One;Freckle Face;Fredericka the Great;Fredoka One;Freehand;Fresca;Frijole;Fruktur;Fugaz One;GFS Didot;GFS Neohellenic;Gabriela;Gafata;Galdeano;Galindo;Gentium Basic;Gentium Book Basic;Geo;Geostar;Geostar Fill;Germania One;Gidugu;Gilda Display;Give You Glory;Glass Antiqua;Glegoo;Gloria Hallelujah;Goblin One;Gochi Hand;Gorditas;Goudy Bookletter 1911;Graduate;Grand Hotel;Gravitas One;Great Vibes;Griffy;Gruppo;Gudea;Gurajada;Habibi;Halant;Hammersmith One;Hanalei;Hanalei Fill;Handlee;Hanuman;Happy Monkey;Headland One;Henny Penny;Herr Von Muellerhoff;Hind;Hind Siliguri;Hind Vadodara;Holtwood One SC;Homemade Apple;Homenaje;IM Fell DW Pica;IM Fell DW Pica SC;IM Fell Double Pica;IM Fell Double Pica SC;IM Fell English;IM Fell English SC;IM Fell French Canon;IM Fell French Canon SC;IM Fell Great Primer;IM Fell Great Primer SC;Iceberg;Iceland;Imprima;Inconsolata;Inder;Indie Flower;Inika;Inknut Antiqua;Irish Grover;Istok Web;Italiana;Italianno;Itim;Jacques Francois;Jacques Francois Shadow;Jaldi;Jim Nightshade;Jockey One;Jolly Lodger;Josefin Slab;Joti One;Judson;Julee;Julius Sans One;Junge;Jura;Just Another Hand;Just Me Again Down Here;Kadwa;Kalam;Kameron;Kantumruy;Karla;Karma;Kaushan Script;Kavoon;Kdam Thmor;Keania One;Kelly Slab;Kenia;Khand;Khmer;Khula;Kite One;Knewave;Kotta One;Koulen;Kranky;Kreon;Kristi;Krona One;Kurale;La Belle Aurore;Laila;Lakki Reddy;Lancelot;Lateef;League Script;Leckerli One;Ledger;Lekton;Lemon;Libre Baskerville;Life Savers;Lilita One;Lily Script One;Limelight;Linden Hill;Lobster;Lobster Two;Londrina Outline;Londrina Shadow;Londrina Sketch;Londrina Solid;Love Ya Like A Sister;Loved by the King;Lovers Quarrel;Luckiest Guy;Lusitana;Lustria;Macondo;Macondo Swash Caps;Magra;Maiden Orange;Mako;Mallanna;Mandali;Marcellus;Marcellus SC;Marck Script;Margarine;Marko One;Marmelad;Martel;Martel Sans;Marvel;Mate;Mate SC;Maven Pro;McLaren;Meddon;MedievalSharp;Medula One;Megrim;Meie Script;Merienda;Merienda One;Merriweather;Merriweather Sans;Metal;Metal Mania;Metamorphous;Metrophobic;Michroma;Milonga;Miltonian;Miltonian Tattoo;Miniver;Miss Fajardose;Modak;Modern Antiqua;Molengo;Molle;Monda;Monofett;Monoton;Monsieur La Doulaise;Montaga;Montez;Montserrat Alternates;Montserrat Subrayada;Moul;Moulpali;Mountains of Christmas;Mouse Memoirs;Mr Bedfort;Mr Dafoe;Mr De Haviland;Mrs Saint Delafield;Mrs Sheppards;Muli;Mystery Quest;NTR;Neucha;Neuton;New Rocker;News Cycle;Niconne;Nixie One;Nobile;Nokora;Norican;Nosifer;Nothing You Could Do;Noticia Text;Noto Sans;Noto Serif;Nova Cut;Nova Flat;Nova Mono;Nova Oval;Nova Round;Nova Script;Nova Slim;Nova Square;Numans;Odor Mean Chey;Offside;Old Standard TT;Oldenburg;Oleo Script;Oleo Script Swash Caps;Open Sans;Open Sans Condensed;Oranienbaum;Orbitron;Oregano;Orienta;Original Surfer;Oswald;Over the Rainbow;Overlock;Overlock SC;Ovo;Oxygen;Oxygen Mono;PT Mono;PT Sans;PT Sans Caption;PT Sans Narrow;PT Serif Caption;Pacifico;Palanquin;Palanquin Dark;Paprika;Parisienne;Passero One;Passion One;Pathway Gothic One;Patrick Hand;Patrick Hand SC;Patua One;Paytone One;Peddana;Peralta;Permanent Marker;Petit Formal Script;Petrona;Philosopher;Piedra;Pinyon Script;Pirata One;Plaster;Play;Playball;Playfair Display;Playfair Display SC;Podkova;Poiret One;Poller One;Poly;Pompiere;Pontano Sans;Port Lligat Sans;Port Lligat Slab;Pragati Narrow;Prata;Preahvihear;Press Start 2P;Princess Sofia;Prociono;Prosto One;Puritan;Purple Purse;Quando;Quantico;Quattrocento;Quattrocento Sans;Questrial;Quintessential;Qwigley;Racing Sans One;Radley;Rajdhani;Raleway;Raleway Dots;Ramabhadra;Ramaraja;Rambla;Rammetto One;Ranchers;Rancho;Ranga;Rationale;Ravi Prakash;Redressed;Reenie Beanie;Revalia;Rhodium Libre;Ribeye;Ribeye Marrow;Righteous;Risque;Roboto Condensed;Roboto Mono;Roboto Slab;Rochester;Rock Salt;Rokkitt;Romanesco;Ropa Sans;Rosario;Rosarivo;Rouge Script;Rozha One;Rubik Mono One;Rubik One;Ruda;Rufina;Ruge Boogie;Ruluko;Rum Raisin;Ruslan Display;Russo One;Ruthie;Rye;Sacramento;Sahitya;Sail;Salsa;Sanchez;Sancreek;Sansita One;Sarala;Sarina;Sarpanch;Satisfy;Scada;Scheherazade;Schoolbell;Seaweed Script;Sevillana;Seymour One;Shadows Into Light;Shadows Into Light Two;Shanti;Share;Share Tech;Share Tech Mono;Shojumaru;Short Stack;Siemreap;Sigmar One;Signika;Signika Negative;Simonetta;Sintony;Sirin Stencil;Six Caps;Skranji;Slabo 13px;Slabo 27px;Slackey;Smokum;Smythe;Sniglet;Snippet;Snowburst One;Sofadi One;Sofia;Sonsie One;Sorts Mill Goudy;Source Code Pro;Source Sans Pro;Source Serif Pro;Special Elite;Spicy Rice;Spinnaker;Spirax;Squada One;Sree Krushnadevaraya;Stalemate;Stalinist One;Stardos Stencil;Stint Ultra Condensed;Stint Ultra Expanded;Stoke;Strait;Sue Ellen Francisco;Sumana;Sunshiney;Supermercado One;Sura;Suranna;Suravaram;Suwannaphum;Swanky and Moo Moo;Syncopate;Tangerine;Taprom;Tauri;Teko;Telex;Tenali Ramakrishna;Tenor Sans;Text Me One;The Girl Next Door;Tienne;Tillana;Timmana;Tinos;Titan One;Titillium Web;Trade Winds;Trocchi;Trochut;Trykker;Tulpen One;Ubuntu;Ubuntu Condensed;Ubuntu Mono;Ultra;Uncial Antiqua;Underdog;Unica One;UnifrakturCook;UnifrakturMaguntia;Unkempt;Unlock;Unna;VT323;Vampiro One;Varela;Varela Round;Vast Shadow;Vesper Libre;Vibur;Vidaloka;Viga;Voces;Volkhov;Voltaire;Waiting for the Sunrise;Wallpoet;Walter Turncoat;Warnes;Wellfleet;Wendy One;Wire One;Work Sans;Yanone Kaffeesatz;Yantramanav;Yellowtail;Yeseva One;Yesteryear;Zeyada";


    public $defaults;

    public function __construct()
    {
        $this->name = 'roy_customizer';
        $this->tab = 'front_office_features';
        $this->version = '1.4';
        $this->author = 'RoyThemes';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->trans(
            'Roy Modez Customizer',
            array(),
            'Modules.Roy_Customizer.Admin'
        );
        $this->description = $this->trans(
            'Customize the design of your shop',
            array(),
            'Modules.Roy_Customizer.Admin'
        );

        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);




        $this->defaults = array(


            // Layout and colors
            "g_lay" => "1",

            "g_tp" => "150",
            "g_bp" => "150",

            "body_box_sw" => "1",
            "main_background_color" => "#e5e5e5",
            "nc_body_gs" => "#389290",
            "nc_body_ge" => "#8480df",
            "nc_body_gg" => "15",
            "nc_body_im_bg_ext" => "",
            "nc_body_im_bg_repeat" => 0,
            "nc_body_im_bg_position" => 0,
            "nc_body_im_bg_fixed" => 0,
            "gradient_scheme" => "1",
            "display_gradient" => 1,
            "body_bg_pattern" => 0,

            "nc_main_bg" => "1",
            "nc_main_bc" => "#f2f2f2",
            "nc_main_gs" => "#f8f8f8",
            "nc_main_ge" => "#d6d6d6",
            "nc_main_gg" => "15",
            "nc_main_im_bg_ext" => "",
            "nc_main_im_bg_repeat" => 0,
            "nc_main_im_bg_position" => 0,
            "nc_main_im_bg_fixed" => 0,


            // Header options
            "header_lay" => "1",
            "nc_logo_normal" => "png",
            "nc_header_shadow" => "1",

            "nc_header_bg" => "4",
            "nc_header_bc" => "#f2f2f2",
            "nc_header_gs" => "#f8f8f8",
            "nc_header_ge" => "#d6d6d6",
            "nc_header_gg" => "15",
            "nc_header_im_bg_ext" => "",
            "nc_header_im_bg_repeat" => 0,
            "nc_header_im_bg_position" => 0,
            "nc_header_im_bg_fixed" => 0,

            "nc_header_st_bg" => "#ffffff",
            "nc_header_st_bgh" => "#fafafa",
            "nc_header_st_link" => "#1c1c1c",
            "nc_header_st_linkh" => "#00c293",

            "header_nbg" => "#ffffff",
            "header_nb" => "#f2f2f2",
            "header_nt" => "#bebebe",
            "header_nl" => "#424242",
            "header_nlh" => "#00c293",
            "header_ns" => "#ffffff",

            "nc_m_align" => "1",
            "nc_m_layout" => "1",
            "nc_m_under" => "1",
            "nc_m_under_color" => "#00c293",
            "nc_m_override" => "2",
            "m_bg" => "#ffffff",
            "m_link_bg_hover" => "#fafafa",
            "m_link" => "#1c1c1c",
            "m_link_hover" => "#00c293",

            "m_popup_llink" => "#1c1c1c",
            "m_popup_llink_hover" => "#00bda0",
            "m_popup_lbg" => "#ffffff",
            "m_popup_lchevron" => "#cccccc",
            "m_popup_lborder" => "#ffffff",

            "nc_m_br" => "5px",

            "search_lay" => "1",
            "nc_i_search" => "search1",

            "search_bg" => "#ffffff",
            "search_line" => "#ffffff",
            "search_input" => "#aaaaaa",
            "search_t" => "#1c1c1c",
            "search_icon" => "#1c1c1c",
            "search_bg_hover" => "#ffffff",
            "search_lineh" => "#ffffff",
            "search_inputh" => "#aaaaaa",
            "search_t_hover" => "#1c1c1c",
            "search_iconh" => "#1c1c1c",

            "cart_lay" => "1",
            "cart_icon" => "cart2",

            "cart_bg" => "#ffffff",
            "cart_b" => "#ffffff",
            "cart_i" => "#1c1c1c",
            "cart_t" => "#1c1c1c",
            "cart_q" => "#1c1c1c",

            "cart_bg_hover" => "#ffffff",
            "cart_b_hover" => "#ffffff",
            "cart_i_hover" => "#1c1c1c",
            "cart_t_hover" => "#1c1c1c",
            "cart_q_hover" => "#1c1c1c",


            // Body design
            "g_bg_content" => "#ffffff",
            "g_border" => "#f2f2f2",
            "g_body_text" => "#777777",
            "g_body_comment" => "#bbbbbb",
            "g_body_link" => "#000000",
            "g_body_link_hover" => "#00c293",
            "g_label" => "#1c1c1c",
            "g_header" => "#1c1c1c",
            "g_header_under" => "#f2f2f2",
            "g_header_decor" => "#5fceb3",
            "g_cc" => "#f2f2f2",
            "g_ch" => "#00c293",
            "g_hb" => "#ffffff",
            "g_hc" => "#1c1c1c",
            "g_bg_even" => "#f2f2f2",
            "g_color_even" => "#000000",
            "g_acc_icon" => "#1c1c1c",
            "g_acc_title" => "#1c1c1c",
            "g_fancy_nbg" => "#ffffff",
            "g_fancy_nc" => "#1c1c1c",

            "b_normal_bg" => "#5fceb3",
            "b_normal_border" => "#5fceb3",
            "b_normal_color" => "#ffffff",
            "b_normal_bg_hover" => "#1c1c1c",
            "b_normal_border_hover" => "#1c1c1c",
            "b_normal_color_hover" => "#ffffff",
            "b_ex_bg" => "#f05377",
            "b_ex_border" => "#f05377",
            "b_ex_color" => "#ffffff",
            "nc_b_radius" => "4",
            "nc_b_sh" => "1",

            "i_bg" => "#ffffff",
            "i_color" => "#323232",
            "i_b_color" => "#f2f2f2",
            "i_bg_focus" => "#ffffff",
            "i_color_focus" => "#1c1c1c",
            "i_b_focus" => "#5c5c5c",
            "i_b_radius" => "4",
            "i_ph" => "#aaaaaa",
            "rc_bg_active" => "#00c293",

            "nc_loader" => 1,
            "nc_loader_lay" => "1",
            "nc_loader_bg" => "#ffffff",
            "nc_loader_color" => "#5fceb3",
            "nc_loader_color2" => "#5fceb3",
            "nc_loader_logo" => "2",
            "nc_logo_loader" => "png",


            // Homepage content
            "ban_spa_behead" => "1",
            "ban_ts_behead" => "0",
            "ban_bs_behead" => "0",
            "ban_spa_top" => "1",
            "ban_ts_top" => "0",
            "ban_bs_top" => "0",
            "ban_ts_left" => "0",
            "ban_bs_left" => "0",
            "ban_ts_right" => "0",
            "ban_bs_right" => "0",
            "ban_spa_pro" => "1",
            "ban_ts_pro" => "30",
            "ban_bs_pro" => "0",
            "ban_spa_befoot" => "1",
            "ban_ts_befoot" => "30",
            "ban_bs_befoot" => "0",
            "ban_spa_foot" => "1",
            "ban_ts_foot" => "30",
            "ban_bs_foot" => "0",
            "ban_spa_sidecart" => "1",
            "ban_ts_sidecart" => "0",
            "ban_bs_sidecart" => "0",
            "ban_spa_sidesearch" => "1",
            "ban_ts_sidesearch" => "0",
            "ban_bs_sidesearch" => "0",
            "ban_spa_sidemail" => "1",
            "ban_ts_sidemail" => "0",
            "ban_bs_sidemail" => "0",
            "ban_spa_sidemobilemenu" => "1",
            "ban_ts_sidemobilemenu" => "0",
            "ban_bs_sidemobilemenu" => "0",
            "ban_spa_product" => "1",
            "ban_ts_product" => "10",
            "ban_bs_product" => "0",

            "nc_carousel_featured" => "1",
            "nc_auto_featured" => "true",
            "nc_items_featured" => "3",
            "nc_carousel_best" => "1",
            "nc_auto_best" => "true",
            "nc_items_best" => "3",
            "nc_carousel_new" => "1",
            "nc_auto_new" => "true",
            "nc_items_new" => "3",
            "nc_carousel_sale" => "1",
            "nc_auto_sale" => "true",
            "nc_items_sale" => "3",
            "nc_carousel_custom1" => "1",
            "nc_auto_custom1" => "true",
            "nc_items_custom1" => "3",
            "nc_carousel_custom2" => "1",
            "nc_auto_custom2" => "true",
            "nc_items_custom2" => "3",
            "nc_carousel_custom3" => "1",
            "nc_auto_custom3" => "true",
            "nc_items_custom3" => "3",
            "nc_carousel_custom4" => "1",
            "nc_auto_custom4" => "true",
            "nc_items_custom4" => "3",
            "nc_carousel_custom5" => "1",
            "nc_auto_custom5" => "true",
            "nc_items_custom5" => "3",

            "brand_per_row" => "6",
            "brand_name" => "#000000",
            "brand_name_hover" => "#00c293",


            // Page content
            "b_layout" => "1",
            "b_link" => "#888888",
            "b_link_hover" => "#323232",
            "b_separator" => "#dddddd",

            "page_bq_q" => "#777777",
            "contact_icon" => "#1c1c1c",
            "warning_message_color" => "#e7b918",
            "success_message_color" => "#00c293",
            "danger_message_color" => "#f05377",


            // Sidebar and filter
            "sidebar_title" => "1",
            "sidebar_title_bg" => "#5fceb3",
            "sidebar_title_b" => "0",
            "sidebar_title_br" => "4",
            "sidebar_title_b1" => "0",
            "sidebar_title_b2" => "0",
            "sidebar_title_b3" => "2",
            "sidebar_title_b4" => "0",
            "sidebar_title_border" => "#2fa085",
            "sidebar_title_link" => "#ffffff",
            "sidebar_title_link_hover" => "#ffffff",
            "sidebar_block_content_bg" => "#ffffff",
            "sidebar_block_content_border" => "#ffffff",
            "sidebar_content_b" => "0",
            "sidebar_content_b1" => "1",
            "sidebar_content_b2" => "1",
            "sidebar_content_b3" => "1",
            "sidebar_content_b4" => "1",
            "sidebar_content_br" => "4",
            "sidebar_block_text_color" => "#424242",
            "sidebar_block_link" => "#1c1c1c",
            "sidebar_block_link_hover" => "#00c293",
            "sidebar_item_separator" => "#f2f2f2",

            "pl_filter_t" => "#1c1c1c",

            "sidebar_c" => "#d6d6d6",
            "sidebar_hc" => "#323232",
            "sidebar_button_bg" => "#ffffff",
            "sidebar_button_border" => "#ededed",
            "sidebar_button_color" => "#323232",
            "sidebar_button_hbg" => "#323232",
            "sidebar_button_hborder" => "#323232",
            "sidebar_button_hcolor" => "#ffffff",
            "sidebar_product_price" => "#444444",
            "sidebar_product_oprice" => "#bbbbbb",


            // Product list
            "nc_product_switch" => "3",
            "nc_subcat" => 0,
            "nc_cat" => 0,
            "pl_nav_grid" => "#1c1c1c",
            "pl_number_color" => "#1c1c1c",
            "pl_number_color_hover" => "#00c293",

            "nc_pc_layout" => "3",
            "pl_item_bg" => "#ffffff",
            "pl_item_border" => "#f2f2f2",
            "nc_pl_item_borderh" => "#f2f2f2",
            "pl_product_name" => "#1c1c1c",
            "pl_product_price" => "#1c1c1c",
            "pl_product_oldprice" => "#bbbbbb",
            "pl_list_description" => "#777777",
            "nc_pl_shadow" => "1",
            "nc_show_q" => "1",
            "nc_show_s" => "1",
            "pl_hover_but" => "#1c1c1c",
            "pl_hover_but_bg" => "#ffffff",
            "pl_product_new_bg" => "#ffffff",
            "pl_product_new_border" => "#ffffff",
            "pl_product_new_color" => "#5fceb3",
            "pl_product_sale_bg" => "#1c1c1c",
            "pl_product_sale_border" => "#1c1c1c",
            "pl_product_sale_color" => "#ffffff",
            "nc_second_img" => "1",
            "nc_colors" => "0",

            "pp_reviews_staron" => "#1c1c1c",
            "pp_reviews_staroff" => "#1c1c1c",

            "nc_count_days" => 0,
            "nc_count_bg" => "#ffffff",
            "nc_count_color" => "#888888",
            "nc_count_time" => "#1c1c1c",
            "nc_count_watch" => "#000000",
            "nc_count_watch_bg" => "#fbd4d6",

            "nc_i_qv" => "search1",
            "nc_i_discover" => "discover1",
            "nc_ai" => "1",


            //  Product page
            "pp_imgb" => "0",
            "pp_img_border" => "#f2f2f2",
            "pp_icon_border" => "#f2f2f2",
            "pp_icon_border_hover" => "#323232",
            "nc_pp_qq3" => "3",

            "pp_z" => "search1",
            "pp_zi" => "#bbbbbb",
            "pp_zihbg" => "#ffffff",

            "nc_sticky_add" => "0",
            "nc_mobadots" => "1",
            "nc_mobadotsc" => "#525252",

            "nc_att_radio" => "1",
            "nc_oldprice" => "1",
            "pp_att_label" => "#1c1c1c",
            "pp_att_color_active" => "#1c1c1c",

            "pp_price_color" => "#1c1c1c",
            "pp_price_coloro" => "#bbbbbb",
            "nc_pp_add_bg" => "#5fceb3",
            "nc_pp_add_border" => "#5fceb3",
            "nc_pp_add_color" => "#ffffff",

            "nc_count_pr_title" => "#1c1c1c",
            "nc_count_pr_bg" => "#ffffff",
            "nc_count_pr_sep" => "#f2f2f2",
            "nc_count_pr_numbers" => "#1c1c1c",
            "nc_count_pr_color" => "#888888",

            "pp_info_label" => "#c1c1c1",
            "pp_info_value" => "#1c1c1c",
            "pp_display_q" => 1,
            "pp_display_refer" => 1,
            "pp_display_cond" => 0,
            "pp_display_brand" => 1,


            // Cart and order
            "o_add" => "1",
            "o_option" => "#f2f2f2",
            "o_option_active" => "#00bda0",
            "o_info_text" => "#777777",

            "lc_bg" => "#00bda0",
            "lc_c" => "#ffffff",


            // Blog
            "bl_lay" => "1",
            "bl_cont" => "2",
            "bl_row" => "3",
            "bl_head" => "#000000",
            "bl_head_hover" => "#00c293",
            "bl_h_title" => "#000000",
            "bl_h_title_h" => "#00c293",
            "bl_h_meta" => "#aaaaaa",
            "bl_h_bg" => "#ffffff",
            "bl_h_border" => "#ffffff",
            "bl_c_row" => "2",
            "bl_desc" => "#777777",
            "bl_rm_color" => "#000000",
            "bl_rm_hover" => "#00c293",

            // Footer
            "footer_lay" => "1",
            "nc_logo_footer" => "png",
            "footer_bg" => "#fafafa",
            "footer_titles" => "#cccccc",
            "footer_text" => "#9d9d9d",
            "footer_link" => "#555555",
            "footer_link_h" => "#000000",
            "footer_news_bg" => "#ffffff",
            "footer_news_border" => "#ffffff",
            "footer_news_placeh" => "#a0a0a0",
            "footer_news_color" => "#525252",
            "footer_news_button" => "#ff4653",


            // Side and Mobile
            "levi_position" => "right",

            "nc_levi_bg" => "#ffffff",
            "nc_levi_border" => "#ffffff",
            "nc_levi_i" => "#1c1c1c",
            "nc_levi_i_hover" => "#1c1c1c",
            "nc_levi_cart" => "#1c1c1c",
            "nc_levi_cart_a" => "#00c293",
            "nc_levi_close" => "#f2f2f2",
            "nc_levi_close_i" => "#1c1c1c",
            "nc_side_bg" => "#ffffff",
            "nc_side_title" => "#1c1c1c",
            "nc_side_text" => "#aaaaaa",
            "nc_side_light" => "#bbbbbb",
            "nc_side_sep" => "#f2f2f2",

            "nc_logo_mobile" => "png",
            "nc_mob_header" => "#ffffff",
            "nc_mob_menu" => "#1c1c1c",
            "nc_mob_hp" => "1",
            "nc_mob_cat" => "1",

            "nc_hemo" => "3",


            // Typography
            "f_headings" => "Cuprum",
            "f_buttons" => "Cuprum",
            "f_text" => "Poppins",
            "f_price" => "Cuprum",
            "f_pn" => "Poppins",
            "latin_ext" => 0,
            "cyrillic" => 0,
            "font_size_pp" => 36,
            "font_size_body" => 16,
            "font_size_head" => 24,
            "font_size_buttons" => 20,
            "font_size_price" => 24,
            "font_size_prod" => 24,
            "font_size_pn" => 16,
            "nc_up_hp" => "2",
            "nc_up_nc" => "1",
            "nc_up_np" => "1",
            "nc_up_f" => "2",
            "nc_up_bp" => "1",
            "nc_up_mi" => "2",
            "nc_up_menu" => "2",
            "nc_up_head" => "2",
            "nc_up_but" => "2",
            "nc_fw_menu" => "600",
            "nc_fw_heading" => "600",
            "nc_fw_but" => "600",
            "nc_fw_pn" => "500",
            "nc_fw_ct" => "500",
            "nc_fw_price" => "600",
            "nc_ital_pn" => "1",
            "nc_italic_pp" => "1",
            "nc_ls" => "0",
            "nc_ls_h" => "0",
            "nc_ls_m" => "0",
            "nc_ls_p" => "0",
            "nc_ls_t" => "0",
            "nc_ls_b" => "0",


            // Custom CSS
            "nc_css" => "",

        );
    }

    public function install()
    {

        if (parent::install() 
        and $this->registerHook('displayHeader') 
        and $this->registerHook('displayBackOfficeHeader')) {

            Configuration::updateValue('RC_G_LAY', $this->defaults["g_lay"]);
            Configuration::updateValue('RC_G_TP', $this->defaults["g_tp"]);
            Configuration::updateValue('RC_G_BP', $this->defaults["g_bp"]);
            Configuration::updateValue('RC_BODY_BOX_SW', $this->defaults["body_box_sw"]);
            Configuration::updateValue('RC_MAIN_BACKGROUND_COLOR', $this->defaults["main_background_color"]);
            Configuration::updateValue('NC_BODY_GS', $this->defaults["nc_body_gs"]);
            Configuration::updateValue('NC_BODY_GE', $this->defaults["nc_body_ge"]);
            Configuration::updateValue('NC_BODY_GG', $this->defaults["nc_body_gg"]);
            Configuration::updateValue('NC_BODY_IM_BG_EXT', $this->defaults["nc_body_im_bg_ext"]);
            Configuration::updateValue('NC_BODY_IM_BG_REPEAT', $this->defaults["nc_body_im_bg_repeat"]);
            Configuration::updateValue('NC_BODY_IM_BG_POSITION', $this->defaults["nc_body_im_bg_position"]);
            Configuration::updateValue('NC_BODY_IM_BG_FIXED', $this->defaults["nc_body_im_bg_fixed"]);
            Configuration::updateValue('RC_GRADIENT_SCHEME', $this->defaults["gradient_scheme"]);
            Configuration::updateValue('RC_DISPLAY_GRADIENT', $this->defaults["display_gradient"]);
            Configuration::updateValue('RC_BODY_BG_PATTERN', $this->defaults["body_bg_pattern"]);
            Configuration::updateValue('NC_MAIN_BGS', $this->defaults["nc_main_bg"]);
            Configuration::updateValue('NC_MAIN_BC', $this->defaults["nc_main_bc"]);
            Configuration::updateValue('NC_MAIN_GS', $this->defaults["nc_main_gs"]);
            Configuration::updateValue('NC_MAIN_GE', $this->defaults["nc_main_ge"]);
            Configuration::updateValue('NC_MAIN_GG', $this->defaults["nc_main_gg"]);
            Configuration::updateValue('NC_MAIN_IM_BG_EXT', $this->defaults["nc_main_im_bg_ext"]);
            Configuration::updateValue('NC_MAIN_IM_BG_REPEAT', $this->defaults["nc_main_im_bg_repeat"]);
            Configuration::updateValue('NC_MAIN_IM_BG_POSITION', $this->defaults["nc_main_im_bg_position"]);
            Configuration::updateValue('NC_MAIN_IM_BG_FIXED', $this->defaults["nc_main_im_bg_fixed"]);

            // header
            Configuration::updateValue('RC_HEADER_LAY', $this->defaults["header_lay"]);
            Configuration::updateValue('NC_LOGO_NORMAL', $this->defaults["nc_logo_normal"]);
            Configuration::updateValue('NC_HEADER_SHADOWS', $this->defaults["nc_header_shadow"]);
            Configuration::updateValue('NC_HEADER_BGS', $this->defaults["nc_header_bg"]);
            Configuration::updateValue('NC_HEADER_BC', $this->defaults["nc_header_bc"]);
            Configuration::updateValue('NC_HEADER_GS', $this->defaults["nc_header_gs"]);
            Configuration::updateValue('NC_HEADER_GE', $this->defaults["nc_header_ge"]);
            Configuration::updateValue('NC_HEADER_GG', $this->defaults["nc_header_gg"]);
            Configuration::updateValue('NC_HEADER_IM_BG_EXT', $this->defaults["nc_header_im_bg_ext"]);
            Configuration::updateValue('NC_HEADER_IM_BG_REPEAT', $this->defaults["nc_header_im_bg_repeat"]);
            Configuration::updateValue('NC_HEADER_IM_BG_POSITION', $this->defaults["nc_header_im_bg_position"]);
            Configuration::updateValue('NC_HEADER_IM_BG_FIXED', $this->defaults["nc_header_im_bg_fixed"]);
            Configuration::updateValue('NC_HEADER_ST_BGCOLOR', $this->defaults["nc_header_st_bg"]);
            Configuration::updateValue('NC_HEADER_ST_BGCOLORHOVER', $this->defaults["nc_header_st_bgh"]);
            Configuration::updateValue('NC_HEADER_ST_LINKCOLOR', $this->defaults["nc_header_st_link"]);
            Configuration::updateValue('NC_HEADER_ST_LINKCOLORHOVER', $this->defaults["nc_header_st_linkh"]);
            Configuration::updateValue('RC_HEADER_NBG', $this->defaults["header_nbg"]);
            Configuration::updateValue('RC_HEADER_NB', $this->defaults["header_nb"]);
            Configuration::updateValue('RC_HEADER_NT', $this->defaults["header_nt"]);
            Configuration::updateValue('RC_HEADER_NL', $this->defaults["header_nl"]);
            Configuration::updateValue('RC_HEADER_NLH', $this->defaults["header_nlh"]);
            Configuration::updateValue('RC_HEADER_NS', $this->defaults["header_ns"]);
            Configuration::updateValue('NC_M_ALIGN_S', $this->defaults["nc_m_align"]);
            Configuration::updateValue('NC_M_LAYOUT_S', $this->defaults["nc_m_layout"]);
            Configuration::updateValue('NC_M_UNDER_S', $this->defaults["nc_m_under"]);
            Configuration::updateValue('NC_M_UNDER_COLOR', $this->defaults["nc_m_under_color"]);
            Configuration::updateValue('NC_M_OVERRIDE_S', $this->defaults["nc_m_override"]);
            Configuration::updateValue('RC_M_BG', $this->defaults["m_bg"]);
            Configuration::updateValue('RC_M_LINK_BG_HOVER', $this->defaults["m_link_bg_hover"]);
            Configuration::updateValue('RC_M_LINK', $this->defaults["m_link"]);
            Configuration::updateValue('RC_M_LINK_HOVER', $this->defaults["m_link_hover"]);
            Configuration::updateValue('RC_M_POPUP_LLINK', $this->defaults["m_popup_llink"]);
            Configuration::updateValue('RC_M_POPUP_LLINK_HOVER', $this->defaults["m_popup_llink_hover"]);
            Configuration::updateValue('RC_M_POPUP_LBG', $this->defaults["m_popup_lbg"]);
            Configuration::updateValue('RC_M_POPUP_LCHEVRON', $this->defaults["m_popup_lchevron"]);
            Configuration::updateValue('RC_M_POPUP_LBORDER', $this->defaults["m_popup_lborder"]);
            Configuration::updateValue('NC_M_BR_S', $this->defaults["nc_m_br"]);
            Configuration::updateValue('RC_SEARCH_LAY', $this->defaults["search_lay"]);
            Configuration::updateValue('NC_I_SEARCHS', $this->defaults["nc_i_search"]);
            Configuration::updateValue('RC_SEARCH_BG', $this->defaults["search_bg"]);
            Configuration::updateValue('RC_SEARCH_LINE', $this->defaults["search_line"]);
            Configuration::updateValue('RC_SEARCH_INPUT', $this->defaults["search_input"]);
            Configuration::updateValue('RC_SEARCH_T', $this->defaults["search_t"]);
            Configuration::updateValue('RC_SEARCH_ICON', $this->defaults["search_icon"]);
            Configuration::updateValue('RC_SEARCH_BG_HOVER', $this->defaults["search_bg_hover"]);
            Configuration::updateValue('RC_SEARCH_LINEH', $this->defaults["search_lineh"]);
            Configuration::updateValue('RC_SEARCH_INPUTH', $this->defaults["search_inputh"]);
            Configuration::updateValue('RC_SEARCH_T_HOVER', $this->defaults["search_t_hover"]);
            Configuration::updateValue('RC_SEARCH_ICONH', $this->defaults["search_iconh"]);
            Configuration::updateValue('RC_CART_LAY', $this->defaults["cart_lay"]);
            Configuration::updateValue('RC_CART_ICON', $this->defaults["cart_icon"]);
            Configuration::updateValue('RC_CART_BG', $this->defaults["cart_bg"]);
            Configuration::updateValue('RC_CART_B', $this->defaults["cart_b"]);
            Configuration::updateValue('RC_CART_I', $this->defaults["cart_i"]);
            Configuration::updateValue('RC_CART_T', $this->defaults["cart_t"]);
            Configuration::updateValue('RC_CART_Q', $this->defaults["cart_q"]);
            Configuration::updateValue('RC_CART_BG_HOVER', $this->defaults["cart_bg_hover"]);
            Configuration::updateValue('RC_CART_B_HOVER', $this->defaults["cart_b_hover"]);
            Configuration::updateValue('RC_CART_I_HOVER', $this->defaults["cart_i_hover"]);
            Configuration::updateValue('RC_CART_T_HOVER', $this->defaults["cart_t_hover"]);
            Configuration::updateValue('RC_CART_Q_HOVER', $this->defaults["cart_q_hover"]);

            // body design
            Configuration::updateValue('RC_G_BG_CONTENT', $this->defaults["g_bg_content"]);
            Configuration::updateValue('RC_G_BORDER', $this->defaults["g_border"]);
            Configuration::updateValue('RC_G_BODY_TEXT', $this->defaults["g_body_text"]);
            Configuration::updateValue('RC_G_BODY_COMMENT', $this->defaults["g_body_comment"]);
            Configuration::updateValue('RC_G_BODY_LINK', $this->defaults["g_body_link"]);
            Configuration::updateValue('RC_G_BODY_LINK_HOVER', $this->defaults["g_body_link_hover"]);
            Configuration::updateValue('RC_LABEL', $this->defaults["g_label"]);
            Configuration::updateValue('RC_G_HEADER', $this->defaults["g_header"]);
            Configuration::updateValue('RC_HEADER_UNDER', $this->defaults["g_header_under"]);
            Configuration::updateValue('RC_HEADER_DECOR', $this->defaults["g_header_decor"]);
            Configuration::updateValue('RC_G_CC', $this->defaults["g_cc"]);
            Configuration::updateValue('RC_G_CH', $this->defaults["g_ch"]);
            Configuration::updateValue('RC_G_HB', $this->defaults["g_hb"]);
            Configuration::updateValue('RC_G_HC', $this->defaults["g_hc"]);
            Configuration::updateValue('RC_G_BG_EVEN', $this->defaults["g_bg_even"]);
            Configuration::updateValue('RC_G_COLOR_EVEN', $this->defaults["g_color_even"]);
            Configuration::updateValue('RC_G_ACC_ICON', $this->defaults["g_acc_icon"]);
            Configuration::updateValue('RC_G_ACC_TITLE', $this->defaults["g_acc_title"]);
            Configuration::updateValue('RC_FANCY_NBG', $this->defaults["g_fancy_nbg"]);
            Configuration::updateValue('RC_FANCY_NC', $this->defaults["g_fancy_nc"]);

            Configuration::updateValue('RC_B_NORMAL_BG', $this->defaults["b_normal_bg"]);
            Configuration::updateValue('RC_B_NORMAL_BORDER', $this->defaults["b_normal_border"]);
            Configuration::updateValue('RC_B_NORMAL_BORDER_HOVER', $this->defaults["b_normal_border_hover"]);
            Configuration::updateValue('RC_B_NORMAL_BG_HOVER', $this->defaults["b_normal_bg_hover"]);
            Configuration::updateValue('RC_B_NORMAL_COLOR', $this->defaults["b_normal_color"]);
            Configuration::updateValue('RC_B_NORMAL_COLOR_HOVER', $this->defaults["b_normal_color_hover"]);
            Configuration::updateValue('RC_B_EX_BG', $this->defaults["b_ex_bg"]);
            Configuration::updateValue('RC_B_EX_BORDER', $this->defaults["b_ex_border"]);
            Configuration::updateValue('RC_B_EX_COLOR', $this->defaults["b_ex_color"]);
            Configuration::updateValue('NC_B_RADIUS', $this->defaults["nc_b_radius"]);
            Configuration::updateValue('NC_B_SHS', $this->defaults["nc_b_sh"]);

            Configuration::updateValue('RC_I_BG', $this->defaults["i_bg"]);
            Configuration::updateValue('RC_I_B_COLOR', $this->defaults["i_b_color"]);
            Configuration::updateValue('RC_I_COLOR', $this->defaults["i_color"]);
            Configuration::updateValue('RC_I_BG_FOCUS', $this->defaults["i_bg_focus"]);
            Configuration::updateValue('RC_I_COLOR_FOCUS', $this->defaults["i_color_focus"]);
            Configuration::updateValue('RC_I_B_FOCUS', $this->defaults["i_b_focus"]);
            Configuration::updateValue('RC_I_B_RADIUS', $this->defaults["i_b_radius"]);
            Configuration::updateValue('RC_I_PH', $this->defaults["i_ph"]);
            Configuration::updateValue('RC_RC_BG_ACTIVE', $this->defaults["rc_bg_active"]);

            Configuration::updateValue('NC_LOADERS', $this->defaults["nc_loader"]);
            Configuration::updateValue('NC_LOADER_LAYS', $this->defaults["nc_loader_lay"]);
            Configuration::updateValue('NC_LOADER_BG', $this->defaults["nc_loader_bg"]);
            Configuration::updateValue('NC_LOADER_COLOR', $this->defaults["nc_loader_color"]);
            Configuration::updateValue('NC_LOADER_COLOR2', $this->defaults["nc_loader_color2"]);
            Configuration::updateValue('NC_LOADER_LOGOS', $this->defaults["nc_loader_logo"]);
            Configuration::updateValue('NC_LOGO_LOADER', $this->defaults["nc_logo_loader"]);

            // Homepage content
            Configuration::updateValue('RC_BAN_SPA_BEHEAD', $this->defaults["ban_spa_behead"]);
            Configuration::updateValue('RC_BAN_TS_BEHEAD', $this->defaults["ban_ts_behead"]);
            Configuration::updateValue('RC_BAN_BS_BEHEAD', $this->defaults["ban_bs_behead"]);
            Configuration::updateValue('RC_BAN_SPA_TOP', $this->defaults["ban_spa_top"]);
            Configuration::updateValue('RC_BAN_TS_TOP', $this->defaults["ban_ts_top"]);
            Configuration::updateValue('RC_BAN_BS_TOP', $this->defaults["ban_bs_top"]);
            Configuration::updateValue('RC_BAN_TS_LEFT', $this->defaults["ban_ts_left"]);
            Configuration::updateValue('RC_BAN_BS_LEFT', $this->defaults["ban_bs_left"]);
            Configuration::updateValue('RC_BAN_TS_RIGHT', $this->defaults["ban_ts_right"]);
            Configuration::updateValue('RC_BAN_BS_RIGHT', $this->defaults["ban_bs_right"]);
            Configuration::updateValue('RC_BAN_SPA_PRO', $this->defaults["ban_spa_pro"]);
            Configuration::updateValue('RC_BAN_TS_PRO', $this->defaults["ban_ts_pro"]);
            Configuration::updateValue('RC_BAN_BS_PRO', $this->defaults["ban_bs_pro"]);
            Configuration::updateValue('RC_BAN_SPA_BEFOOT', $this->defaults["ban_spa_befoot"]);
            Configuration::updateValue('RC_BAN_TS_BEFOOT', $this->defaults["ban_ts_befoot"]);
            Configuration::updateValue('RC_BAN_BS_BEFOOT', $this->defaults["ban_bs_befoot"]);
            Configuration::updateValue('RC_BAN_SPA_FOOT', $this->defaults["ban_spa_foot"]);
            Configuration::updateValue('RC_BAN_TS_FOOT', $this->defaults["ban_ts_foot"]);
            Configuration::updateValue('RC_BAN_BS_FOOT', $this->defaults["ban_bs_foot"]);
            Configuration::updateValue('RC_BAN_SPA_SIDECART', $this->defaults["ban_spa_sidecart"]);
            Configuration::updateValue('RC_BAN_TS_SIDECART', $this->defaults["ban_ts_sidecart"]);
            Configuration::updateValue('RC_BAN_BS_SIDECART', $this->defaults["ban_bs_sidecart"]);
            Configuration::updateValue('RC_BAN_SPA_SIDESEARCH', $this->defaults["ban_spa_sidesearch"]);
            Configuration::updateValue('RC_BAN_TS_SIDESEARCH', $this->defaults["ban_ts_sidesearch"]);
            Configuration::updateValue('RC_BAN_BS_SIDESEARCH', $this->defaults["ban_bs_sidesearch"]);
            Configuration::updateValue('RC_BAN_SPA_SIDEMAIL', $this->defaults["ban_spa_sidemail"]);
            Configuration::updateValue('RC_BAN_TS_SIDEMAIL', $this->defaults["ban_ts_sidemail"]);
            Configuration::updateValue('RC_BAN_BS_SIDEMAIL', $this->defaults["ban_bs_sidemail"]);
            Configuration::updateValue('RC_BAN_SPA_SIDEMOBILEMENU', $this->defaults["ban_spa_sidemobilemenu"]);
            Configuration::updateValue('RC_BAN_TS_SIDEMOBILEMENU', $this->defaults["ban_ts_sidemobilemenu"]);
            Configuration::updateValue('RC_BAN_BS_SIDEMOBILEMENU', $this->defaults["ban_bs_sidemobilemenu"]);
            Configuration::updateValue('RC_BAN_SPA_PRODUCT', $this->defaults["ban_spa_product"]);
            Configuration::updateValue('RC_BAN_TS_PRODUCT', $this->defaults["ban_ts_product"]);
            Configuration::updateValue('RC_BAN_BS_PRODUCT', $this->defaults["ban_bs_product"]);

            Configuration::updateValue('NC_CAROUSEL_FEATUREDS', $this->defaults["nc_carousel_featured"]);
            Configuration::updateValue('NC_AUTO_FEATURED', $this->defaults["nc_auto_featured"]);
            Configuration::updateValue('NC_ITEMS_FEATUREDS', $this->defaults["nc_items_featured"]);
            Configuration::updateValue('NC_CAROUSEL_BEST', $this->defaults["nc_carousel_best"]);
            Configuration::updateValue('NC_AUTO_BEST', $this->defaults["nc_auto_best"]);
            Configuration::updateValue('NC_ITEMS_BESTS', $this->defaults["nc_items_best"]);
            Configuration::updateValue('NC_CAROUSEL_NEW', $this->defaults["nc_carousel_new"]);
            Configuration::updateValue('NC_AUTO_NEW', $this->defaults["nc_auto_new"]);
            Configuration::updateValue('NC_ITEMS_NEWS', $this->defaults["nc_items_new"]);
            Configuration::updateValue('NC_CAROUSEL_SALE', $this->defaults["nc_carousel_sale"]);
            Configuration::updateValue('NC_AUTO_SALE', $this->defaults["nc_auto_sale"]);
            Configuration::updateValue('NC_ITEMS_SALES', $this->defaults["nc_items_sale"]);
            Configuration::updateValue('NC_CAROUSEL_CUSTOM1', $this->defaults["nc_carousel_custom1"]);
            Configuration::updateValue('NC_AUTO_CUSTOM1', $this->defaults["nc_auto_custom1"]);
            Configuration::updateValue('NC_ITEMS_CUSTOM1S', $this->defaults["nc_items_custom1"]);
            Configuration::updateValue('NC_CAROUSEL_CUSTOM2', $this->defaults["nc_carousel_custom2"]);
            Configuration::updateValue('NC_AUTO_CUSTOM2', $this->defaults["nc_auto_custom2"]);
            Configuration::updateValue('NC_ITEMS_CUSTOM2S', $this->defaults["nc_items_custom2"]);
            Configuration::updateValue('NC_CAROUSEL_CUSTOM3', $this->defaults["nc_carousel_custom3"]);
            Configuration::updateValue('NC_AUTO_CUSTOM3', $this->defaults["nc_auto_custom3"]);
            Configuration::updateValue('NC_ITEMS_CUSTOM3S', $this->defaults["nc_items_custom3"]);
            Configuration::updateValue('NC_CAROUSEL_CUSTOM4', $this->defaults["nc_carousel_custom4"]);
            Configuration::updateValue('NC_AUTO_CUSTOM4', $this->defaults["nc_auto_custom4"]);
            Configuration::updateValue('NC_ITEMS_CUSTOM4', $this->defaults["nc_items_custom4"]);
            Configuration::updateValue('NC_CAROUSEL_CUSTOM5', $this->defaults["nc_carousel_custom5"]);
            Configuration::updateValue('NC_AUTO_CUSTOM5', $this->defaults["nc_auto_custom5"]);
            Configuration::updateValue('NC_ITEMS_CUSTOM5', $this->defaults["nc_items_custom5"]);

            Configuration::updateValue('RC_BRAND_PER_ROW', $this->defaults["brand_per_row"]);
            Configuration::updateValue('RC_BRAND_NAME', $this->defaults["brand_name"]);
            Configuration::updateValue('RC_BRAND_NAME_HOVER', $this->defaults["brand_name_hover"]);

            // page content
            Configuration::updateValue('RC_B_LAYOUT', $this->defaults["b_layout"]);
            Configuration::updateValue('RC_B_LINK', $this->defaults["b_link"]);
            Configuration::updateValue('RC_B_LINK_HOVER', $this->defaults["b_link_hover"]);
            Configuration::updateValue('RC_B_SEPARATOR', $this->defaults["b_separator"]);
            Configuration::updateValue('RC_PAGE_BQ_Q', $this->defaults["page_bq_q"]);
            Configuration::updateValue('RC_CONTACT_ICON', $this->defaults["contact_icon"]);
            Configuration::updateValue('RC_WARNING_MESSAGE_COLOR', $this->defaults["warning_message_color"]);
            Configuration::updateValue('RC_SUCCESS_MESSAGE_COLOR', $this->defaults["success_message_color"]);
            Configuration::updateValue('RC_DANGER_MESSAGE_COLOR', $this->defaults["danger_message_color"]);

            // Sidebar and filter
            Configuration::updateValue('RC_SIDEBAR_TITLE', $this->defaults["sidebar_title"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_BG', $this->defaults["sidebar_title_bg"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_B', $this->defaults["sidebar_title_b"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_BR', $this->defaults["sidebar_title_br"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_B1', $this->defaults["sidebar_title_b1"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_B2', $this->defaults["sidebar_title_b2"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_B3', $this->defaults["sidebar_title_b3"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_B4', $this->defaults["sidebar_title_b4"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_BORDER', $this->defaults["sidebar_title_border"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_LINK', $this->defaults["sidebar_title_link"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_LINK_HOVER', $this->defaults["sidebar_title_link_hover"]);
            Configuration::updateValue('RC_SIDEBAR_BLOCK_CONTENT_BG', $this->defaults["sidebar_block_content_bg"]);
            Configuration::updateValue('RC_SIDEBAR_BLOCK_CONTENT_BORDER', $this->defaults["sidebar_block_content_border"]);
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B', $this->defaults["sidebar_content_b"]);
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B1', $this->defaults["sidebar_content_b1"]);
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B2', $this->defaults["sidebar_content_b2"]);
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B3', $this->defaults["sidebar_content_b3"]);
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B4', $this->defaults["sidebar_content_b4"]);
            Configuration::updateValue('RC_SIDEBAR_CONTENT_BR', $this->defaults["sidebar_content_br"]);
            Configuration::updateValue('RC_SIDEBAR_BLOCK_TEXT_COLOR', $this->defaults["sidebar_block_text_color"]);
            Configuration::updateValue('RC_SIDEBAR_BLOCK_LINK', $this->defaults["sidebar_block_link"]);
            Configuration::updateValue('RC_SIDEBAR_BLOCK_LINK_HOVER', $this->defaults["sidebar_block_link_hover"]);
            Configuration::updateValue('RC_SIDEBAR_ITEM_SEPARATOR', $this->defaults["sidebar_item_separator"]);
            Configuration::updateValue('RC_PL_FILTER_T', $this->defaults["pl_filter_t"]);

            Configuration::updateValue('RC_SIDEBAR_C', $this->defaults["sidebar_c"]);
            Configuration::updateValue('RC_SIDEBAR_HC', $this->defaults["sidebar_hc"]);
            Configuration::updateValue('RC_SIDEBAR_BUTTON_BG', $this->defaults["sidebar_button_bg"]);
            Configuration::updateValue('RC_SIDEBAR_BUTTON_BORDER', $this->defaults["sidebar_button_border"]);
            Configuration::updateValue('RC_SIDEBAR_BUTTON_COLOR', $this->defaults["sidebar_button_color"]);
            Configuration::updateValue('RC_SIDEBAR_BUTTON_HBG', $this->defaults["sidebar_button_hbg"]);
            Configuration::updateValue('RC_SIDEBAR_BUTTON_HBORDER', $this->defaults["sidebar_button_hborder"]);
            Configuration::updateValue('RC_SIDEBAR_BUTTON_HCOLOR', $this->defaults["sidebar_button_hcolor"]);
            Configuration::updateValue('RC_SIDEBAR_PRODUCT_PRICE', $this->defaults["sidebar_product_price"]);
            Configuration::updateValue('RC_SIDEBAR_PRODUCT_OPRICE', $this->defaults["sidebar_product_oprice"]);

            // Product list

            Configuration::updateValue('NC_PRODUCT_SWITCH', $this->defaults["nc_product_switch"]);
            Configuration::updateValue('NC_SUBCAT_S', $this->defaults["nc_subcat"]);
            Configuration::updateValue('NC_CAT_S', $this->defaults["nc_cat"]);
            Configuration::updateValue('RC_PL_NAV_GRID', $this->defaults["pl_nav_grid"]);
            Configuration::updateValue('RC_PL_NUMBER_COLOR', $this->defaults["pl_number_color"]);
            Configuration::updateValue('RC_PL_NUMBER_COLOR_HOVER', $this->defaults["pl_number_color_hover"]);

            Configuration::updateValue('NC_PC_LAYOUTS', $this->defaults["nc_pc_layout"]);
            Configuration::updateValue('RC_PL_ITEM_BG', $this->defaults["pl_item_bg"]);
            Configuration::updateValue('RC_PL_ITEM_BORDER', $this->defaults["pl_item_border"]);
            Configuration::updateValue('NC_PL_ITEM_BORDERH', $this->defaults["nc_pl_item_borderh"]);
            Configuration::updateValue('RC_PL_PRODUCT_NAME', $this->defaults["pl_product_name"]);
            Configuration::updateValue('RC_PL_PRODUCT_PRICE', $this->defaults["pl_product_price"]);
            Configuration::updateValue('RC_PL_PRODUCT_OLDPRICE', $this->defaults["pl_product_oldprice"]);
            Configuration::updateValue('RC_PL_LIST_DESCRIPTION', $this->defaults["pl_list_description"]);
            Configuration::updateValue('NC_PL_SHADOWS', $this->defaults["nc_pl_shadow"]);
            Configuration::updateValue('NC_SHOW_QW', $this->defaults["nc_show_q"]);
            Configuration::updateValue('NC_SHOW_SW', $this->defaults["nc_show_s"]);
            Configuration::updateValue('RC_PL_HOVER_BUT', $this->defaults["pl_hover_but"]);
            Configuration::updateValue('RC_PL_HOVER_BUT_BG', $this->defaults["pl_hover_but_bg"]);
            Configuration::updateValue('RC_PL_PRODUCT_NEW_BG', $this->defaults["pl_product_new_bg"]);
            Configuration::updateValue('RC_PL_PRODUCT_NEW_BORDER', $this->defaults["pl_product_new_border"]);
            Configuration::updateValue('RC_PL_PRODUCT_NEW_COLOR', $this->defaults["pl_product_new_color"]);
            Configuration::updateValue('RC_PL_PRODUCT_SALE_BG', $this->defaults["pl_product_sale_bg"]);
            Configuration::updateValue('RC_PL_PRODUCT_SALE_BORDER', $this->defaults["pl_product_sale_border"]);
            Configuration::updateValue('RC_PL_PRODUCT_SALE_COLOR', $this->defaults["pl_product_sale_color"]);
            Configuration::updateValue('NC_SECOND_IMG_S', $this->defaults["nc_second_img"]);
            Configuration::updateValue('NC_COLORS_S', $this->defaults["nc_colors"]);

            Configuration::updateValue('RC_PP_REVIEWS_STARON', $this->defaults["pp_reviews_staron"]);
            Configuration::updateValue('RC_PP_REVIEWS_STAROFF', $this->defaults["pp_reviews_staroff"]);

            Configuration::updateValue('NC_COUNT_DAYS', $this->defaults["nc_count_days"]);
            Configuration::updateValue('NC_COUNT_BG', $this->defaults["nc_count_bg"]);
            Configuration::updateValue('NC_COUNT_COLOR', $this->defaults["nc_count_color"]);
            Configuration::updateValue('NC_COUNT_TIME', $this->defaults["nc_count_time"]);
            Configuration::updateValue('NC_COUNT_WATCH', $this->defaults["nc_count_watch"]);
            Configuration::updateValue('NC_COUNT_WATCH_BG', $this->defaults["nc_count_watch_bg"]);

            Configuration::updateValue('NC_I_QVS', $this->defaults["nc_i_qv"]);
            Configuration::updateValue('NC_I_DISCOVERS', $this->defaults["nc_i_discover"]);
            Configuration::updateValue('NC_AIS', $this->defaults["nc_ai"]);

            //  Product page
            Configuration::updateValue('RC_PP_IMGB', $this->defaults["pp_imgb"]);
            Configuration::updateValue('RC_PP_IMG_BORDER', $this->defaults["pp_img_border"]);
            Configuration::updateValue('RC_PP_ICON_BORDER', $this->defaults["pp_icon_border"]);
            Configuration::updateValue('RC_PP_ICON_BORDER_HOVER', $this->defaults["pp_icon_border_hover"]);
            Configuration::updateValue('NC_PP_QQ3S', $this->defaults["nc_pp_qq3"]);

            Configuration::updateValue('RC_PP_Z', $this->defaults["pp_z"]);
            Configuration::updateValue('RC_PP_ZI', $this->defaults["pp_zi"]);
            Configuration::updateValue('RC_PP_ZIHBG', $this->defaults["pp_zihbg"]);
            Configuration::updateValue('NC_STICKY_ADDS', $this->defaults["nc_sticky_add"]);
            Configuration::updateValue('NC_MOBADOTSS', $this->defaults["nc_mobadots"]);
            Configuration::updateValue('NC_MOBADOTSCS', $this->defaults["nc_mobadotsc"]);
            Configuration::updateValue('NC_ATT_RADIOS', $this->defaults["nc_att_radio"]);
            Configuration::updateValue('NC_OLDPRICE', $this->defaults["nc_oldprice"]);
            Configuration::updateValue('RC_PP_ATT_LABEL', $this->defaults["pp_att_label"]);
            Configuration::updateValue('RC_PP_ATT_COLOR_ACTIVE', $this->defaults["pp_att_color_active"]);

            Configuration::updateValue('RC_PP_PRICE_COLOR', $this->defaults["pp_price_color"]);
            Configuration::updateValue('RC_PP_PRICE_COLORO', $this->defaults["pp_price_coloro"]);
            Configuration::updateValue('NC_PP_ADD_BG', $this->defaults["nc_pp_add_bg"]);
            Configuration::updateValue('NC_PP_ADD_BORDER', $this->defaults["nc_pp_add_border"]);
            Configuration::updateValue('NC_PP_ADD_COLOR', $this->defaults["nc_pp_add_color"]);

            Configuration::updateValue('NC_COUNT_PR_TITLE', $this->defaults["nc_count_pr_title"]);
            Configuration::updateValue('NC_COUNT_PR_BG', $this->defaults["nc_count_pr_bg"]);
            Configuration::updateValue('NC_COUNT_PR_SEP', $this->defaults["nc_count_pr_sep"]);
            Configuration::updateValue('NC_COUNT_PR_NUMBERS', $this->defaults["nc_count_pr_numbers"]);
            Configuration::updateValue('NC_COUNT_PR_COLOR', $this->defaults["nc_count_pr_color"]);

            Configuration::updateValue('RC_PP_INFO_LABEL', $this->defaults["pp_info_label"]);
            Configuration::updateValue('RC_PP_INFO_VALUE', $this->defaults["pp_info_value"]);
            Configuration::updateValue('RC_PP_DISPLAY_Q', $this->defaults["pp_display_q"]);
            Configuration::updateValue('RC_PP_DISPLAY_REFER', $this->defaults["pp_display_refer"]);
            Configuration::updateValue('RC_PP_DISPLAY_COND', $this->defaults["pp_display_cond"]);
            Configuration::updateValue('RC_PP_DISPLAY_BRAND', $this->defaults["pp_display_brand"]);

            // Cart and order
            Configuration::updateValue('RC_O_ADDS', $this->defaults["o_add"]);
            Configuration::updateValue('RC_O_OPTION', $this->defaults["o_option"]);
            Configuration::updateValue('RC_O_OPTION_ACTIVE', $this->defaults["o_option_active"]);
            Configuration::updateValue('RC_O_INFO_TEXT', $this->defaults["o_info_text"]);

            Configuration::updateValue('RC_LC_BG', $this->defaults["lc_bg"]);
            Configuration::updateValue('RC_LC_C', $this->defaults["lc_c"]);

            // blog
            Configuration::updateValue('RC_BL_LAY', $this->defaults["bl_lay"]);
            Configuration::updateValue('RC_BL_CONT', $this->defaults["bl_cont"]);
            Configuration::updateValue('RC_BL_ROW', $this->defaults["bl_row"]);
            Configuration::updateValue('RC_BL_HEAD', $this->defaults["bl_head"]);
            Configuration::updateValue('RC_BL_HEAD_HOVER', $this->defaults["bl_head_hover"]);
            Configuration::updateValue('RC_BL_H_TITLE', $this->defaults["bl_h_title"]);
            Configuration::updateValue('RC_BL_H_TITLE_H', $this->defaults["bl_h_title_h"]);
            Configuration::updateValue('RC_BL_H_META', $this->defaults["bl_h_meta"]);
            Configuration::updateValue('RC_BL_H_BG', $this->defaults["bl_h_bg"]);
            Configuration::updateValue('RC_BL_H_BORDER', $this->defaults["bl_h_border"]);
            Configuration::updateValue('RC_BL_C_ROW', $this->defaults["bl_c_row"]);
            Configuration::updateValue('RC_BL_DESC', $this->defaults["bl_desc"]);
            Configuration::updateValue('RC_BL_RM_COLOR', $this->defaults["bl_rm_color"]);
            Configuration::updateValue('RC_BL_RM_HOVER', $this->defaults["bl_rm_hover"]);

            // footer
            Configuration::updateValue('RC_FOOTER_LAY', $this->defaults["footer_lay"]);
            Configuration::updateValue('NC_LOGO_FOOTER', $this->defaults["nc_logo_footer"]);
            Configuration::updateValue('RC_FOOTER_BG', $this->defaults["footer_bg"]);
            Configuration::updateValue('RC_FOOTER_TITLES', $this->defaults["footer_titles"]);
            Configuration::updateValue('RC_FOOTER_TEXT', $this->defaults["footer_text"]);
            Configuration::updateValue('RC_FOOTER_LINK', $this->defaults["footer_link"]);
            Configuration::updateValue('RC_FOOTER_LINK_H', $this->defaults["footer_link_h"]);
            Configuration::updateValue('RC_FOOTER_NEWS_BG', $this->defaults["footer_news_bg"]);
            Configuration::updateValue('RC_FOOTER_NEWS_BORDER', $this->defaults["footer_news_border"]);
            Configuration::updateValue('RC_FOOTER_NEWS_PLACEH', $this->defaults["footer_news_placeh"]);
            Configuration::updateValue('RC_FOOTER_NEWS_COLOR', $this->defaults["footer_news_color"]);
            Configuration::updateValue('RC_FOOTER_NEWS_BUTTON', $this->defaults["footer_news_button"]);

            // Side and Mobile
            Configuration::updateValue('RC_LEVI_POSITION', $this->defaults["levi_position"]);
            Configuration::updateValue('NC_LEVI_BG', $this->defaults["nc_levi_bg"]);
            Configuration::updateValue('NC_LEVI_BORDER', $this->defaults["nc_levi_border"]);
            Configuration::updateValue('NC_LEVI_I', $this->defaults["nc_levi_i"]);
            Configuration::updateValue('NC_LEVI_I_HOVER', $this->defaults["nc_levi_i_hover"]);
            Configuration::updateValue('NC_LEVI_CART', $this->defaults["nc_levi_cart"]);
            Configuration::updateValue('NC_LEVI_CART_A', $this->defaults["nc_levi_cart_a"]);
            Configuration::updateValue('NC_LEVI_CLOSE', $this->defaults["nc_levi_close"]);
            Configuration::updateValue('NC_LEVI_CLOSE_I', $this->defaults["nc_levi_close_i"]);
            Configuration::updateValue('NC_SIDE_BG', $this->defaults["nc_side_bg"]);
            Configuration::updateValue('NC_SIDE_TITLE', $this->defaults["nc_side_title"]);
            Configuration::updateValue('NC_SIDE_TEXT', $this->defaults["nc_side_text"]);
            Configuration::updateValue('NC_SIDE_LIGHT', $this->defaults["nc_side_light"]);
            Configuration::updateValue('NC_SIDE_SEP', $this->defaults["nc_side_sep"]);

            Configuration::updateValue('NC_LOGO_MOBILE', $this->defaults["nc_logo_mobile"]);
            Configuration::updateValue('NC_MOB_HEADER', $this->defaults["nc_mob_header"]);
            Configuration::updateValue('NC_MOB_MENU', $this->defaults["nc_mob_menu"]);
            Configuration::updateValue('NC_MOB_HP', $this->defaults["nc_mob_hp"]);
            Configuration::updateValue('NC_MOB_CAT', $this->defaults["nc_mob_cat"]);
            Configuration::updateValue('NC_HEMOS', $this->defaults["nc_hemo"]);

            // typography
            Configuration::updateValue('RC_F_HEADINGS', $this->defaults["f_headings"]);
            Configuration::updateValue('RC_F_BUTTONS', $this->defaults["f_buttons"]);
            Configuration::updateValue('RC_F_TEXT', $this->defaults["f_text"]);
            Configuration::updateValue('RC_F_PRICE', $this->defaults["f_price"]);
            Configuration::updateValue('RC_F_PN', $this->defaults["f_pn"]);
            Configuration::updateValue('RC_LATIN_EXT', $this->defaults["latin_ext"]);
            Configuration::updateValue('RC_CYRILLIC', $this->defaults["cyrillic"]);
            Configuration::updateValue('RC_FONT_SIZE_PP', $this->defaults["font_size_pp"]);
            Configuration::updateValue('RC_FONT_SIZE_BODY', $this->defaults["font_size_body"]);
            Configuration::updateValue('RC_FONT_SIZE_HEAD', $this->defaults["font_size_head"]);
            Configuration::updateValue('RC_FONT_SIZE_BUTTONS', $this->defaults["font_size_buttons"]);
            Configuration::updateValue('RC_FONT_SIZE_PRICE', $this->defaults["font_size_price"]);
            Configuration::updateValue('RC_FONT_SIZE_PROD', $this->defaults["font_size_prod"]);
            Configuration::updateValue('RC_FONT_SIZE_PN', $this->defaults["font_size_pn"]);
            Configuration::updateValue('NC_UP_HP', $this->defaults["nc_up_hp"]);
            Configuration::updateValue('NC_UP_NC', $this->defaults["nc_up_nc"]);
            Configuration::updateValue('NC_UP_NP', $this->defaults["nc_up_np"]);
            Configuration::updateValue('NC_UP_F', $this->defaults["nc_up_f"]);
            Configuration::updateValue('NC_UP_BP', $this->defaults["nc_up_bp"]);
            Configuration::updateValue('NC_UP_MI', $this->defaults["nc_up_mi"]);
            Configuration::updateValue('NC_UP_MENU', $this->defaults["nc_up_menu"]);
            Configuration::updateValue('NC_UP_HEAD', $this->defaults["nc_up_head"]);
            Configuration::updateValue('NC_UP_BUT', $this->defaults["nc_up_but"]);
            Configuration::updateValue('NC_FW_MENU', $this->defaults["nc_fw_menu"]);
            Configuration::updateValue('NC_FW_HEADING', $this->defaults["nc_fw_heading"]);
            Configuration::updateValue('NC_FW_BUT', $this->defaults["nc_fw_but"]);
            Configuration::updateValue('NC_FW_PN', $this->defaults["nc_fw_pn"]);
            Configuration::updateValue('NC_FW_CT', $this->defaults["nc_fw_ct"]);
            Configuration::updateValue('NC_FW_PRICE', $this->defaults["nc_fw_price"]);
            Configuration::updateValue('NC_ITAL_PN', $this->defaults["nc_ital_pn"]);
            Configuration::updateValue('NC_ITALIC_PP', $this->defaults["nc_italic_pp"]);
            Configuration::updateValue('NC_LS', $this->defaults["nc_ls"]);
            Configuration::updateValue('NC_LS_H', $this->defaults["nc_ls_h"]);
            Configuration::updateValue('NC_LS_M', $this->defaults["nc_ls_m"]);
            Configuration::updateValue('NC_LS_P', $this->defaults["nc_ls_p"]);
            Configuration::updateValue('NC_LS_T', $this->defaults["nc_ls_t"]);
            Configuration::updateValue('NC_LS_B', $this->defaults["nc_ls_b"]);

            // Custom CSS
            Configuration::updateValue('NC_CSS', $this->defaults["nc_css"]);

            $this->installTab();

            return true;
        } else {
            return false;
        }
    }

    private function installTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AdminRoyCustomizer');
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'AdminRoyCustomizer';
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('Modez Customizer', array(), 'Modules.Roy_Customizer.Admin', $lang['locale']);
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('IMPROVE');
        $tab->module = $this->name;

        return $tab->save();
    }

    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AdminRoyCustomizer');
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return $tab->delete();
    }

    public function uninstall()
    {
        if (
            !parent::uninstall() ||

            !Configuration::deleteByName('RC_G_LAY') ||
            !Configuration::deleteByName('RC_G_TP') ||
            !Configuration::deleteByName('RC_G_BP') ||
            !Configuration::deleteByName('RC_BODY_BOX_SW') ||
            !Configuration::deleteByName('RC_MAIN_BACKGROUND_COLOR') ||
            !Configuration::deleteByName('NC_BODY_GS') ||
            !Configuration::deleteByName('NC_BODY_GE') ||
            !Configuration::deleteByName('NC_BODY_GG') ||
            !Configuration::deleteByName('NC_BODY_IM_BG_EXT') ||
            !Configuration::deleteByName('NC_BODY_IM_BG_REPEAT') ||
            !Configuration::deleteByName('NC_BODY_IM_BG_POSITION') ||
            !Configuration::deleteByName('NC_BODY_IM_BG_FIXED') ||
            !Configuration::deleteByName('RC_GRADIENT_SCHEME') ||
            !Configuration::deleteByName('RC_DISPLAY_GRADIENT') ||
            !Configuration::deleteByName('RC_BODY_BG_PATTERN') ||
            !Configuration::deleteByName('NC_MAIN_BGS') ||
            !Configuration::deleteByName('NC_MAIN_BC') ||
            !Configuration::deleteByName('NC_MAIN_GS') ||
            !Configuration::deleteByName('NC_MAIN_GE') ||
            !Configuration::deleteByName('NC_MAIN_GG') ||
            !Configuration::deleteByName('NC_MAIN_IM_BG_EXT') ||
            !Configuration::deleteByName('NC_MAIN_IM_BG_REPEAT') ||
            !Configuration::deleteByName('NC_MAIN_IM_BG_POSITION') ||
            !Configuration::deleteByName('NC_MAIN_IM_BG_FIXED') ||

            // header
            !Configuration::deleteByName('RC_HEADER_LAY') ||
            !Configuration::deleteByName('NC_LOGO_NORMAL') ||
            !Configuration::deleteByName('NC_HEADER_SHADOWS') ||
            !Configuration::deleteByName('NC_HEADER_BGS') ||
            !Configuration::deleteByName('NC_HEADER_BC') ||
            !Configuration::deleteByName('NC_HEADER_GS') ||
            !Configuration::deleteByName('NC_HEADER_GE') ||
            !Configuration::deleteByName('NC_HEADER_GG') ||
            !Configuration::deleteByName('NC_HEADER_IM_BG_EXT') ||
            !Configuration::deleteByName('NC_HEADER_IM_BG_REPEAT') ||
            !Configuration::deleteByName('NC_HEADER_IM_BG_POSITION') ||
            !Configuration::deleteByName('NC_HEADER_IM_BG_FIXED') ||
            !Configuration::deleteByName('NC_HEADER_ST_BGCOLOR') ||
            !Configuration::deleteByName('NC_HEADER_ST_BGCOLORHOVER') ||
            !Configuration::deleteByName('NC_HEADER_ST_LINKCOLOR') ||
            !Configuration::deleteByName('NC_HEADER_ST_LINKCOLORHOVER') ||
            !Configuration::deleteByName('RC_HEADER_NBG') ||
            !Configuration::deleteByName('RC_HEADER_NB') ||
            !Configuration::deleteByName('RC_HEADER_NT') ||
            !Configuration::deleteByName('RC_HEADER_NL') ||
            !Configuration::deleteByName('RC_HEADER_NLH') ||
            !Configuration::deleteByName('RC_HEADER_NS') ||
            !Configuration::deleteByName('NC_M_ALIGN_S') ||
            !Configuration::deleteByName('NC_M_LAYOUT_S') ||
            !Configuration::deleteByName('NC_M_UNDER_S') ||
            !Configuration::deleteByName('NC_M_OVERRIDE_S') ||
            !Configuration::deleteByName('NC_M_UNDER_COLOR') ||
            !Configuration::deleteByName('NC_M_UNDER_OVERRIDES') ||
            !Configuration::deleteByName('RC_M_BG') ||
            !Configuration::deleteByName('RC_M_LINK_BG_HOVER') ||
            !Configuration::deleteByName('RC_M_LINK') ||
            !Configuration::deleteByName('RC_M_LINK_HOVER') ||
            !Configuration::deleteByName('RC_M_POPUP_LLINK') ||
            !Configuration::deleteByName('RC_M_POPUP_LLINK_HOVER') ||
            !Configuration::deleteByName('RC_M_POPUP_LBG') ||
            !Configuration::deleteByName('RC_M_POPUP_LCHEVRON') ||
            !Configuration::deleteByName('RC_M_POPUP_LBORDER') ||
            !Configuration::deleteByName('NC_M_BR_S') ||
            !Configuration::deleteByName('RC_SEARCH_LAY') ||
            !Configuration::deleteByName('NC_I_SEARCHS') ||
            !Configuration::deleteByName('RC_SEARCH_BG') ||
            !Configuration::deleteByName('RC_SEARCH_LINE') ||
            !Configuration::deleteByName('RC_SEARCH_INPUT') ||
            !Configuration::deleteByName('RC_SEARCH_T') ||
            !Configuration::deleteByName('RC_SEARCH_ICON') ||
            !Configuration::deleteByName('RC_SEARCH_BG_HOVER') ||
            !Configuration::deleteByName('RC_SEARCH_LINEH') ||
            !Configuration::deleteByName('RC_SEARCH_INPUTH') ||
            !Configuration::deleteByName('RC_SEARCH_T_HOVER') ||
            !Configuration::deleteByName('RC_SEARCH_ICONH') ||
            !Configuration::deleteByName('RC_CART_LAY') ||
            !Configuration::deleteByName('RC_CART_ICON') ||
            !Configuration::deleteByName('RC_CART_BG') ||
            !Configuration::deleteByName('RC_CART_B') ||
            !Configuration::deleteByName('RC_CART_I') ||
            !Configuration::deleteByName('RC_CART_T') ||
            !Configuration::deleteByName('RC_CART_Q') ||
            !Configuration::deleteByName('RC_CART_BG_HOVER') ||
            !Configuration::deleteByName('RC_CART_B_HOVER') ||
            !Configuration::deleteByName('RC_CART_I_HOVER') ||
            !Configuration::deleteByName('RC_CART_T_HOVER') ||
            !Configuration::deleteByName('RC_CART_Q_HOVER') ||

            // body design
            !Configuration::deleteByName('RC_G_BG_CONTENT') ||
            !Configuration::deleteByName('RC_G_BORDER') ||
            !Configuration::deleteByName('RC_G_BODY_TEXT') ||
            !Configuration::deleteByName('RC_G_BODY_COMMENT') ||
            !Configuration::deleteByName('RC_G_BODY_LINK') ||
            !Configuration::deleteByName('RC_G_BODY_LINK_HOVER') ||
            !Configuration::deleteByName('RC_LABEL') ||
            !Configuration::deleteByName('RC_G_HEADER') ||
            !Configuration::deleteByName('RC_HEADER_UNDER') ||
            !Configuration::deleteByName('RC_HEADER_DECOR') ||
            !Configuration::deleteByName('RC_G_CC') ||
            !Configuration::deleteByName('RC_G_CH') ||
            !Configuration::deleteByName('RC_G_HB') ||
            !Configuration::deleteByName('RC_G_HC') ||
            !Configuration::deleteByName('RC_G_BG_EVEN') ||
            !Configuration::deleteByName('RC_G_COLOR_EVEN') ||
            !Configuration::deleteByName('RC_G_ACC_ICON') ||
            !Configuration::deleteByName('RC_G_ACC_TITLE') ||
            !Configuration::deleteByName('RC_FANCY_NBG') ||
            !Configuration::deleteByName('RC_FANCY_NC') ||

            !Configuration::deleteByName('RC_B_NORMAL_BG') ||
            !Configuration::deleteByName('RC_B_NORMAL_BORDER') ||
            !Configuration::deleteByName('RC_B_NORMAL_BORDER_HOVER') ||
            !Configuration::deleteByName('RC_B_NORMAL_BG_HOVER') ||
            !Configuration::deleteByName('RC_B_NORMAL_COLOR') ||
            !Configuration::deleteByName('RC_B_NORMAL_COLOR_HOVER') ||
            !Configuration::deleteByName('RC_B_EX_BG') ||
            !Configuration::deleteByName('RC_B_EX_BORDER') ||
            !Configuration::deleteByName('RC_B_EX_COLOR') ||
            !Configuration::deleteByName('NC_B_RADIUS') ||
            !Configuration::deleteByName('NC_B_SHS') ||

            !Configuration::deleteByName('RC_I_BG') ||
            !Configuration::deleteByName('RC_I_B_COLOR') ||
            !Configuration::deleteByName('RC_I_COLOR') ||
            !Configuration::deleteByName('RC_I_BG_FOCUS') ||
            !Configuration::deleteByName('RC_I_COLOR_FOCUS') ||
            !Configuration::deleteByName('RC_I_B_FOCUS') ||
            !Configuration::deleteByName('RC_I_B_RADIUS') ||
            !Configuration::deleteByName('RC_I_PH') ||
            !Configuration::deleteByName('RC_RC_BG_ACTIVE') ||

            !Configuration::deleteByName('NC_LOADERS') ||
            !Configuration::deleteByName('NC_LOADER_LAYS') ||
            !Configuration::deleteByName('NC_LOADER_BG') ||
            !Configuration::deleteByName('NC_LOADER_COLOR') ||
            !Configuration::deleteByName('NC_LOADER_COLOR2') ||
            !Configuration::deleteByName('NC_LOADER_LOGOS') ||
            !Configuration::deleteByName('NC_LOGO_LOADER') ||

            // Homepage content
            !Configuration::deleteByName('RC_BAN_SPA_BEHEAD') ||
            !Configuration::deleteByName('RC_BAN_TS_BEHEAD') ||
            !Configuration::deleteByName('RC_BAN_BS_BEHEAD') ||
            !Configuration::deleteByName('RC_BAN_SPA_TOP') ||
            !Configuration::deleteByName('RC_BAN_TS_TOP') ||
            !Configuration::deleteByName('RC_BAN_BS_TOP') ||
            !Configuration::deleteByName('RC_BAN_TS_LEFT') ||
            !Configuration::deleteByName('RC_BAN_BS_LEFT') ||
            !Configuration::deleteByName('RC_BAN_TS_RIGHT') ||
            !Configuration::deleteByName('RC_BAN_BS_RIGHT') ||
            !Configuration::deleteByName('RC_BAN_SPA_PRO') ||
            !Configuration::deleteByName('RC_BAN_TS_PRO') ||
            !Configuration::deleteByName('RC_BAN_BS_PRO') ||
            !Configuration::deleteByName('RC_BAN_SPA_BEFOOT') ||
            !Configuration::deleteByName('RC_BAN_TS_BEFOOT') ||
            !Configuration::deleteByName('RC_BAN_BS_BEFOOT') ||
            !Configuration::deleteByName('RC_BAN_SPA_FOOT') ||
            !Configuration::deleteByName('RC_BAN_TS_FOOT') ||
            !Configuration::deleteByName('RC_BAN_BS_FOOT') ||
            !Configuration::deleteByName('RC_BAN_SPA_SIDECART') ||
            !Configuration::deleteByName('RC_BAN_TS_SIDECART') ||
            !Configuration::deleteByName('RC_BAN_BS_SIDECART') ||
            !Configuration::deleteByName('RC_BAN_SPA_SIDESEARCH') ||
            !Configuration::deleteByName('RC_BAN_TS_SIDESEARCH') ||
            !Configuration::deleteByName('RC_BAN_BS_SIDESEARCH') ||
            !Configuration::deleteByName('RC_BAN_SPA_SIDEMAIL') ||
            !Configuration::deleteByName('RC_BAN_TS_SIDEMAIL') ||
            !Configuration::deleteByName('RC_BAN_BS_SIDEMAIL') ||
            !Configuration::deleteByName('RC_BAN_SPA_SIDEMOBILEMENU') ||
            !Configuration::deleteByName('RC_BAN_TS_SIDEMOBILEMENU') ||
            !Configuration::deleteByName('RC_BAN_BS_SIDEMOBILEMENU') ||
            !Configuration::deleteByName('RC_BAN_SPA_PRODUCT') ||
            !Configuration::deleteByName('RC_BAN_TS_PRODUCT') ||
            !Configuration::deleteByName('RC_BAN_BS_PRODUCT') ||

            !Configuration::deleteByName('NC_CAROUSEL_FEATUREDS') ||
            !Configuration::deleteByName('NC_AUTO_FEATURED') ||
            !Configuration::deleteByName('NC_ITEMS_FEATUREDS') ||
            !Configuration::deleteByName('NC_CAROUSEL_BEST') ||
            !Configuration::deleteByName('NC_AUTO_BEST') ||
            !Configuration::deleteByName('NC_ITEMS_BESTS') ||
            !Configuration::deleteByName('NC_CAROUSEL_NEW') ||
            !Configuration::deleteByName('NC_AUTO_NEW') ||
            !Configuration::deleteByName('NC_ITEMS_NEWS') ||
            !Configuration::deleteByName('NC_CAROUSEL_SALE') ||
            !Configuration::deleteByName('NC_AUTO_SALE') ||
            !Configuration::deleteByName('NC_ITEMS_SALES') ||
            !Configuration::deleteByName('NC_CAROUSEL_CUSTOM1') ||
            !Configuration::deleteByName('NC_AUTO_CUSTOM1') ||
            !Configuration::deleteByName('NC_ITEMS_CUSTOM1S') ||
            !Configuration::deleteByName('NC_CAROUSEL_CUSTOM2') ||
            !Configuration::deleteByName('NC_AUTO_CUSTOM2') ||
            !Configuration::deleteByName('NC_ITEMS_CUSTOM2S') ||
            !Configuration::deleteByName('NC_CAROUSEL_CUSTOM3') ||
            !Configuration::deleteByName('NC_AUTO_CUSTOM3') ||
            !Configuration::deleteByName('NC_ITEMS_CUSTOM3S') ||
            !Configuration::deleteByName('NC_CAROUSEL_CUSTOM4') ||
            !Configuration::deleteByName('NC_AUTO_CUSTOM4') ||
            !Configuration::deleteByName('NC_ITEMS_CUSTOM4') ||
            !Configuration::deleteByName('NC_CAROUSEL_CUSTOM5') ||
            !Configuration::deleteByName('NC_AUTO_CUSTOM5') ||
            !Configuration::deleteByName('NC_ITEMS_CUSTOM5') ||

            !Configuration::deleteByName('RC_BRAND_PER_ROW') ||
            !Configuration::deleteByName('RC_BRAND_NAME') ||
            !Configuration::deleteByName('RC_BRAND_NAME_HOVER') ||

            // page content
            !Configuration::deleteByName('RC_B_LAYOUT') ||
            !Configuration::deleteByName('RC_B_LINK') ||
            !Configuration::deleteByName('RC_B_LINK_HOVER') ||
            !Configuration::deleteByName('RC_B_SEPARATOR') ||
            !Configuration::deleteByName('RC_PAGE_BQ_Q') ||
            !Configuration::deleteByName('RC_CONTACT_ICON') ||
            !Configuration::deleteByName('RC_WARNING_MESSAGE_COLOR') ||
            !Configuration::deleteByName('RC_SUCCESS_MESSAGE_COLOR') ||
            !Configuration::deleteByName('RC_DANGER_MESSAGE_COLOR') ||

            // Sidebar and filter
            !Configuration::deleteByName('RC_SIDEBAR_TITLE') ||
            !Configuration::deleteByName('RC_SIDEBAR_TITLE_BG') ||
            !Configuration::deleteByName('RC_SIDEBAR_TITLE_B') ||
            !Configuration::deleteByName('RC_SIDEBAR_TITLE_BR') ||
            !Configuration::deleteByName('RC_SIDEBAR_TITLE_B1') ||
            !Configuration::deleteByName('RC_SIDEBAR_TITLE_B2') ||
            !Configuration::deleteByName('RC_SIDEBAR_TITLE_B3') ||
            !Configuration::deleteByName('RC_SIDEBAR_TITLE_B4') ||
            !Configuration::deleteByName('RC_SIDEBAR_TITLE_BORDER') ||
            !Configuration::deleteByName('RC_SIDEBAR_TITLE_LINK') ||
            !Configuration::deleteByName('RC_SIDEBAR_TITLE_LINK_HOVER') ||
            !Configuration::deleteByName('RC_SIDEBAR_BLOCK_CONTENT_BG') ||
            !Configuration::deleteByName('RC_SIDEBAR_BLOCK_CONTENT_BORDER') ||
            !Configuration::deleteByName('RC_SIDEBAR_CONTENT_B') ||
            !Configuration::deleteByName('RC_SIDEBAR_CONTENT_B1') ||
            !Configuration::deleteByName('RC_SIDEBAR_CONTENT_B2') ||
            !Configuration::deleteByName('RC_SIDEBAR_CONTENT_B3') ||
            !Configuration::deleteByName('RC_SIDEBAR_CONTENT_B4') ||
            !Configuration::deleteByName('RC_SIDEBAR_CONTENT_BR') ||
            !Configuration::deleteByName('RC_SIDEBAR_BLOCK_TEXT_COLOR') ||
            !Configuration::deleteByName('RC_SIDEBAR_BLOCK_LINK') ||
            !Configuration::deleteByName('RC_SIDEBAR_BLOCK_LINK_HOVER') ||
            !Configuration::deleteByName('RC_SIDEBAR_ITEM_SEPARATOR') ||
            !Configuration::deleteByName('RC_PL_FILTER_T') ||

            !Configuration::deleteByName('RC_SIDEBAR_C') ||
            !Configuration::deleteByName('RC_SIDEBAR_HC') ||
            !Configuration::deleteByName('RC_SIDEBAR_BUTTON_BG') ||
            !Configuration::deleteByName('RC_SIDEBAR_BUTTON_BORDER') ||
            !Configuration::deleteByName('RC_SIDEBAR_BUTTON_COLOR') ||
            !Configuration::deleteByName('RC_SIDEBAR_BUTTON_HBG') ||
            !Configuration::deleteByName('RC_SIDEBAR_BUTTON_HBORDER') ||
            !Configuration::deleteByName('RC_SIDEBAR_BUTTON_HCOLOR') ||
            !Configuration::deleteByName('RC_SIDEBAR_PRODUCT_PRICE') ||
            !Configuration::deleteByName('RC_SIDEBAR_PRODUCT_OPRICE') ||

            // Product list

            !Configuration::deleteByName('NC_PRODUCT_SWITCH') ||
            !Configuration::deleteByName('NC_SUBCAT_S') ||
            !Configuration::deleteByName('NC_CAT_S') ||
            !Configuration::deleteByName('RC_PL_NAV_GRID') ||
            !Configuration::deleteByName('RC_PL_NUMBER_COLOR') ||
            !Configuration::deleteByName('RC_PL_NUMBER_COLOR_HOVER') ||

            !Configuration::deleteByName('NC_PC_LAYOUTS') ||
            !Configuration::deleteByName('RC_PL_ITEM_BG') ||
            !Configuration::deleteByName('RC_PL_ITEM_BORDER') ||
            !Configuration::deleteByName('NC_PL_ITEM_BORDERH') ||
            !Configuration::deleteByName('RC_PL_PRODUCT_NAME') ||
            !Configuration::deleteByName('RC_PL_PRODUCT_PRICE') ||
            !Configuration::deleteByName('RC_PL_PRODUCT_OLDPRICE') ||
            !Configuration::deleteByName('RC_PL_LIST_DESCRIPTION') ||
            !Configuration::deleteByName('NC_PL_SHADOWS') ||
            !Configuration::deleteByName('NC_SHOW_QW') ||
            !Configuration::deleteByName('NC_SHOW_SW') ||
            !Configuration::deleteByName('RC_PL_HOVER_BUT') ||
            !Configuration::deleteByName('RC_PL_HOVER_BUT_BG') ||
            !Configuration::deleteByName('RC_PL_PRODUCT_NEW_BG') ||
            !Configuration::deleteByName('RC_PL_PRODUCT_NEW_BORDER') ||
            !Configuration::deleteByName('RC_PL_PRODUCT_NEW_COLOR') ||
            !Configuration::deleteByName('RC_PL_PRODUCT_SALE_BG') ||
            !Configuration::deleteByName('RC_PL_PRODUCT_SALE_BORDER') ||
            !Configuration::deleteByName('RC_PL_PRODUCT_SALE_COLOR') ||
            !Configuration::deleteByName('NC_SECOND_IMG_S') ||
            !Configuration::deleteByName('NC_COLORS_S') ||

            !Configuration::deleteByName('RC_PP_REVIEWS_STARON') ||
            !Configuration::deleteByName('RC_PP_REVIEWS_STAROFF') ||

            !Configuration::deleteByName('NC_COUNT_DAYS') ||
            !Configuration::deleteByName('NC_COUNT_BG') ||
            !Configuration::deleteByName('NC_COUNT_COLOR') ||
            !Configuration::deleteByName('NC_COUNT_TIME') ||
            !Configuration::deleteByName('NC_COUNT_WATCH') ||
            !Configuration::deleteByName('NC_COUNT_WATCH_BG') ||

            !Configuration::deleteByName('NC_I_QVS') ||
            !Configuration::deleteByName('NC_I_DISCOVERS') ||
            !Configuration::deleteByName('NC_AIS') ||

            //  Product page
            !Configuration::deleteByName('RC_PP_IMGB') ||
            !Configuration::deleteByName('RC_PP_IMG_BORDER') ||
            !Configuration::deleteByName('RC_PP_ICON_BORDER') ||
            !Configuration::deleteByName('RC_PP_ICON_BORDER_HOVER') ||
            !Configuration::deleteByName('NC_PP_QQ3S') ||

            !Configuration::deleteByName('RC_PP_Z') ||
            !Configuration::deleteByName('RC_PP_ZI') ||
            !Configuration::deleteByName('RC_PP_ZIHBG') ||
            !Configuration::deleteByName('NC_MOBADOTSS') ||
            !Configuration::deleteByName('NC_MOBADOTSCS') ||
            !Configuration::deleteByName('NC_ATT_RADIOS') ||
            !Configuration::deleteByName('NC_OLDPRICE') ||
            !Configuration::deleteByName('RC_PP_ATT_LABEL') ||
            !Configuration::deleteByName('RC_PP_ATT_COLOR_ACTIVE') ||

            !Configuration::deleteByName('RC_PP_PRICE_COLOR') ||
            !Configuration::deleteByName('RC_PP_PRICE_COLORO') ||
            !Configuration::deleteByName('NC_PP_ADD_BG') ||
            !Configuration::deleteByName('NC_PP_ADD_BORDER') ||
            !Configuration::deleteByName('NC_PP_ADD_COLOR') ||

            !Configuration::deleteByName('NC_COUNT_PR_TITLE') ||
            !Configuration::deleteByName('NC_COUNT_PR_BG') ||
            !Configuration::deleteByName('NC_COUNT_PR_SEP') ||
            !Configuration::deleteByName('NC_COUNT_PR_NUMBERS') ||
            !Configuration::deleteByName('NC_COUNT_PR_COLOR') ||

            !Configuration::deleteByName('RC_PP_INFO_LABEL') ||
            !Configuration::deleteByName('RC_PP_INFO_VALUE') ||
            !Configuration::deleteByName('RC_PP_DISPLAY_Q') ||
            !Configuration::deleteByName('RC_PP_DISPLAY_REFER') ||
            !Configuration::deleteByName('RC_PP_DISPLAY_COND') ||
            !Configuration::deleteByName('RC_PP_DISPLAY_BRAND') ||

            // Cart and order
            !Configuration::deleteByName('RC_O_ADDS') ||
            !Configuration::deleteByName('RC_O_OPTION') ||
            !Configuration::deleteByName('RC_O_OPTION_ACTIVE') ||
            !Configuration::deleteByName('RC_O_INFO_TEXT') ||

            !Configuration::deleteByName('RC_LC_BG') ||
            !Configuration::deleteByName('RC_LC_C') ||

            // blog
            !Configuration::deleteByName('RC_BL_LAY') ||
            !Configuration::deleteByName('RC_BL_CONT') ||
            !Configuration::deleteByName('RC_BL_ROW') ||
            !Configuration::deleteByName('RC_BL_HEAD') ||
            !Configuration::deleteByName('RC_BL_HEAD_HOVER') ||
            !Configuration::deleteByName('RC_BL_H_TITLE') ||
            !Configuration::deleteByName('RC_BL_H_TITLE_H') ||
            !Configuration::deleteByName('RC_BL_H_META') ||
            !Configuration::deleteByName('RC_BL_H_BG') ||
            !Configuration::deleteByName('RC_BL_H_BORDER') ||
            !Configuration::deleteByName('RC_BL_C_ROW') ||
            !Configuration::deleteByName('RC_BL_DESC') ||
            !Configuration::deleteByName('RC_BL_RM_COLOR') ||
            !Configuration::deleteByName('RC_BL_RM_HOVER') ||

            // footer
            !Configuration::deleteByName('RC_FOOTER_LAY') ||
            !Configuration::deleteByName('NC_LOGO_FOOTER') ||
            !Configuration::deleteByName('RC_FOOTER_BG') ||
            !Configuration::deleteByName('RC_FOOTER_TITLES') ||
            !Configuration::deleteByName('RC_FOOTER_TEXT') ||
            !Configuration::deleteByName('RC_FOOTER_LINK') ||
            !Configuration::deleteByName('RC_FOOTER_LINK_H') ||
            !Configuration::deleteByName('RC_FOOTER_NEWS_BG') ||
            !Configuration::deleteByName('RC_FOOTER_NEWS_BORDER') ||
            !Configuration::deleteByName('RC_FOOTER_NEWS_PLACEH') ||
            !Configuration::deleteByName('RC_FOOTER_NEWS_COLOR') ||
            !Configuration::deleteByName('RC_FOOTER_NEWS_BUTTON') ||

            // Side and Mobile
            !Configuration::deleteByName('RC_LEVI_POSITION') ||
            !Configuration::deleteByName('NC_LEVI_BG') ||
            !Configuration::deleteByName('NC_LEVI_BORDER') ||
            !Configuration::deleteByName('NC_LEVI_I') ||
            !Configuration::deleteByName('NC_LEVI_I_HOVER') ||
            !Configuration::deleteByName('NC_LEVI_CART') ||
            !Configuration::deleteByName('NC_LEVI_CART_A') ||
            !Configuration::deleteByName('NC_LEVI_CLOSE') ||
            !Configuration::deleteByName('NC_LEVI_CLOSE_I') ||
            !Configuration::deleteByName('NC_SIDE_BG') ||
            !Configuration::deleteByName('NC_SIDE_TITLE') ||
            !Configuration::deleteByName('NC_SIDE_TEXT') ||
            !Configuration::deleteByName('NC_SIDE_LIGHT') ||
            !Configuration::deleteByName('NC_SIDE_SEP') ||

            !Configuration::deleteByName('NC_LOGO_MOBILE') ||
            !Configuration::deleteByName('NC_MOB_HEADER') ||
            !Configuration::deleteByName('NC_MOB_MENU') ||
            !Configuration::deleteByName('NC_MOB_HP') ||
            !Configuration::deleteByName('NC_MOB_CAT') ||
            !Configuration::deleteByName('NC_HEMOS') ||

            // typography
            !Configuration::deleteByName('RC_F_HEADINGS') ||
            !Configuration::deleteByName('RC_F_BUTTONS') ||
            !Configuration::deleteByName('RC_F_TEXT') ||
            !Configuration::deleteByName('RC_F_PRICE') ||
            !Configuration::deleteByName('RC_F_PN') ||
            !Configuration::deleteByName('RC_LATIN_EXT') ||
            !Configuration::deleteByName('RC_CYRILLIC') ||
            !Configuration::deleteByName('RC_FONT_SIZE_PP') ||
            !Configuration::deleteByName('RC_FONT_SIZE_BODY') ||
            !Configuration::deleteByName('RC_FONT_SIZE_HEAD') ||
            !Configuration::deleteByName('RC_FONT_SIZE_BUTTONS') ||
            !Configuration::deleteByName('RC_FONT_SIZE_PRICE') ||
            !Configuration::deleteByName('RC_FONT_SIZE_PROD') ||
            !Configuration::deleteByName('RC_FONT_SIZE_PN') ||
            !Configuration::deleteByName('NC_UP_HP') ||
            !Configuration::deleteByName('NC_UP_NC') ||
            !Configuration::deleteByName('NC_UP_NP') ||
            !Configuration::deleteByName('NC_UP_F') ||
            !Configuration::deleteByName('NC_UP_BP') ||
            !Configuration::deleteByName('NC_UP_MI') ||
            !Configuration::deleteByName('NC_UP_MENU') ||
            !Configuration::deleteByName('NC_UP_HEAD') ||
            !Configuration::deleteByName('NC_UP_BUT') ||
            !Configuration::deleteByName('NC_FW_MENU') ||
            !Configuration::deleteByName('NC_FW_HEADING') ||
            !Configuration::deleteByName('NC_FW_BUT') ||
            !Configuration::deleteByName('NC_FW_PN') ||
            !Configuration::deleteByName('NC_FW_CT') ||
            !Configuration::deleteByName('NC_FW_PRICE') ||
            !Configuration::deleteByName('NC_ITAL_PN') ||
            !Configuration::deleteByName('NC_ITALIC_PP') ||
            !Configuration::deleteByName('NC_LS') ||
            !Configuration::deleteByName('NC_LS_H') ||
            !Configuration::deleteByName('NC_LS_M') ||
            !Configuration::deleteByName('NC_LS_P') ||
            !Configuration::deleteByName('NC_LS_T') ||
            !Configuration::deleteByName('NC_LS_B') ||

            // Custom CSS
            !Configuration::deleteByName('NC_CSS')
        )

            return false;

        $this->uninstallTab();
        return true;
    }

    public function postProcess()
    {
    }

    public function updateOriginalValues()
    {
        foreach ($this->defaults as $key => $v) {
            $find_nc = "nc_";
            if (substr($key, 0, 3) === $find_nc) {
                $target = strtoupper($key);
                if (!Configuration::get($target)) {
                    Configuration::updateValue((string)$target, $this->defaults["$key"]);
                }
            }
        }
    }

    public function getContent()
    {

        // $this -> context -> controller -> addJS(_PS_JS_DIR_ . 'jquery/plugins/jquery.colorpicker.js');
        $this->context->controller->addJS(($this->_path) . 'js/jquery.colorpicker.js');
        $this->context->controller->addJS(($this->_path) . 'js/jquery-ui.min.js');        $this->context->controller->addJS(($this->_path) . 'js/navigation.js');
        $this->context->controller->addJS(($this->_path) . 'js/script.js');
        $this->context->controller->addCSS(($this->_path) . 'css/admin.css');
        $this->context->controller->addCSS(($this->_path) . 'css/feather.css');
        $this->context->controller->addCSS(($this->_path) . 'css/ionicons.css');
        $this->context->controller->addJS(($this->_path) . 'codemirror-4.11/lib/codemirror.js');
        $this->context->controller->addJS(($this->_path) . 'codemirror-4.11/mode/css/css.js');
        $this->context->controller->addCSS(($this->_path) . 'codemirror-4.11/lib/codemirror.css');
        $this->context->controller->addCSS(($this->_path) . 'codemirror-4.11/themes/mbo.css');
        $this->context->controller->addCSS(($this->_path) . 'codemirror-4.11/themes/zenburn.css');
        $this->context->controller->addCSS(($this->_path) . 'codemirror-4.11/themes/jquery-ui.min.css');
        $this->postProcess();

        $output = '<h2 class="roytc_title"><span class="theme_name"></span><div class="mod_name"><span class="mod_custom">' . $this->displayName . '</span><span class="mod_license">WARNING! Normal work of theme, this module, free updates and support are included only in legal version of theme, purchased from <a href="https://themeforest.net/item/ayon-multipurpose-responsive-prestashop-theme/18628985?ref=RoyVelvet" target="_blank">ThemeForest HERE</a></span></div><span class="mod_version"></span></h2>';

        $errors = '';

        if (Tools::isSubmit('export_changes')) {
            $keys = array(
                'RC_G_LAY',
                'RC_G_TP',
                'RC_G_BP',
                'RC_BODY_BOX_SW',
                'RC_MAIN_BACKGROUND_COLOR',
                'NC_BODY_GS',
                'NC_BODY_GE',
                'NC_BODY_GG',
                'NC_BODY_IM_BG_EXT',
                'NC_BODY_IM_BG_REPEAT',
                'NC_BODY_IM_BG_POSITION',
                'NC_BODY_IM_BG_FIXED',
                'RC_GRADIENT_SCHEME',
                'RC_DISPLAY_GRADIENT',
                'RC_BODY_BG_PATTERN',
                'NC_MAIN_BGS',
                'NC_MAIN_BC',
                'NC_MAIN_GS',
                'NC_MAIN_GE',
                'NC_MAIN_GG',
                'NC_MAIN_IM_BG_EXT',
                'NC_MAIN_IM_BG_REPEAT',
                'NC_MAIN_IM_BG_POSITION',
                'NC_MAIN_IM_BG_FIXED',
                'RC_HEADER_LAY',
                'NC_LOGO_NORMAL',
                'NC_HEADER_SHADOWS',
                'NC_HEADER_BGS',
                'NC_HEADER_BC',
                'NC_HEADER_GS',
                'NC_HEADER_GE',
                'NC_HEADER_GG',
                'NC_HEADER_IM_BG_EXT',
                'NC_HEADER_IM_BG_REPEAT',
                'NC_HEADER_IM_BG_POSITION',
                'NC_HEADER_IM_BG_FIXED',
                'NC_HEADER_ST_BGCOLOR',
                'NC_HEADER_ST_BGCOLORHOVER',
                'NC_HEADER_ST_LINKCOLOR',
                'NC_HEADER_ST_LINKCOLORHOVER',
                'RC_HEADER_NBG',
                'RC_HEADER_NB',
                'RC_HEADER_NT',
                'RC_HEADER_NL',
                'RC_HEADER_NLH',
                'RC_HEADER_NS',
                'NC_M_ALIGN_S',
                'NC_M_LAYOUT_S',
                'NC_M_UNDER_S',
                'NC_M_UNDER_COLOR',
                'NC_M_UNDER_OVERRIDES',
                'RC_M_BG',
                'RC_M_LINK_BG_HOVER',
                'RC_M_LINK',
                'RC_M_LINK_HOVER',
                'RC_M_POPUP_LLINK',
                'RC_M_POPUP_LLINK_HOVER',
                'RC_M_POPUP_LBG',
                'RC_M_POPUP_LCHEVRON',
                'RC_M_POPUP_LBORDER',
                'NC_M_BR_S',
                'RC_SEARCH_LAY',
                'NC_I_SEARCHS',
                'RC_SEARCH_BG',
                'RC_SEARCH_LINE',
                'RC_SEARCH_INPUT',
                'RC_SEARCH_T',
                'RC_SEARCH_ICON',
                'RC_SEARCH_BG_HOVER',
                'RC_SEARCH_LINEH',
                'RC_SEARCH_INPUTH',
                'RC_SEARCH_T_HOVER',
                'RC_SEARCH_ICONH',
                'RC_CART_LAY',
                'RC_CART_ICON',
                'RC_CART_BG',
                'RC_CART_B',
                'RC_CART_I',
                'RC_CART_T',
                'RC_CART_Q',
                'RC_CART_BG_HOVER',
                'RC_CART_B_HOVER',
                'RC_CART_I_HOVER',
                'RC_CART_T_HOVER',
                'RC_CART_Q_HOVER',
                'RC_G_BG_CONTENT',
                'RC_G_BORDER',
                'RC_G_BODY_TEXT',
                'RC_G_BODY_COMMENT',
                'RC_G_BODY_LINK',
                'RC_G_BODY_LINK_HOVER',
                'RC_LABEL',
                'RC_G_HEADER',
                'RC_HEADER_UNDER',
                'RC_HEADER_DECOR',
                'RC_G_CC',
                'RC_G_CH',
                'RC_G_HB',
                'RC_G_HC',
                'RC_G_BG_EVEN',
                'RC_G_COLOR_EVEN',
                'RC_G_ACC_ICON',
                'RC_G_ACC_TITLE',
                'RC_FANCY_NBG',
                'RC_FANCY_NC',
                'RC_B_NORMAL_BG',
                'RC_B_NORMAL_BORDER',
                'RC_B_NORMAL_BORDER_HOVER',
                'RC_B_NORMAL_BG_HOVER',
                'RC_B_NORMAL_COLOR',
                'RC_B_NORMAL_COLOR_HOVER',
                'RC_B_EX_BG',
                'RC_B_EX_BORDER',
                'RC_B_EX_COLOR',
                'NC_B_RADIUS',
                'NC_B_SHS',
                'RC_I_BG',
                'RC_I_COLOR',
                'RC_I_B_COLOR',
                'RC_I_BG_FOCUS',
                'RC_I_COLOR_FOCUS',
                'RC_I_B_FOCUS',
                'RC_I_B_RADIUS',
                'RC_I_PH',
                'RC_RC_BG_ACTIVE',
                'NC_LOADERS',
                'NC_LOADER_LAYS',
                'NC_LOADER_BG',
                'NC_LOADER_COLOR',
                'NC_LOADER_COLOR2',
                'NC_LOADER_LOGOS',
                'NC_LOGO_LOADER',
                'RC_BAN_SPA_BEHEAD',
                'RC_BAN_TS_BEHEAD',
                'RC_BAN_BS_BEHEAD',
                'RC_BAN_SPA_TOP',
                'RC_BAN_TS_TOP',
                'RC_BAN_BS_TOP',
                'RC_BAN_TS_LEFT',
                'RC_BAN_BS_LEFT',
                'RC_BAN_TS_RIGHT',
                'RC_BAN_BS_RIGHT',
                'RC_BAN_SPA_PRO',
                'RC_BAN_TS_PRO',
                'RC_BAN_BS_PRO',
                'RC_BAN_SPA_BEFOOT',
                'RC_BAN_TS_BEFOOT',
                'RC_BAN_BS_BEFOOT',
                'RC_BAN_SPA_FOOT',
                'RC_BAN_TS_FOOT',
                'RC_BAN_BS_FOOT',
                'RC_BAN_SPA_SIDECART',
                'RC_BAN_TS_SIDECART',
                'RC_BAN_BS_SIDECART',
                'RC_BAN_SPA_SIDESEARCH',
                'RC_BAN_TS_SIDESEARCH',
                'RC_BAN_BS_SIDESEARCH',
                'RC_BAN_SPA_SIDEMAIL',
                'RC_BAN_TS_SIDEMAIL',
                'RC_BAN_BS_SIDEMAIL',
                'RC_BAN_SPA_SIDEMOBILEMENU',
                'RC_BAN_TS_SIDEMOBILEMENU',
                'RC_BAN_BS_SIDEMOBILEMENU',
                'RC_BAN_SPA_PRODUCT',
                'RC_BAN_TS_PRODUCT',
                'RC_BAN_BS_PRODUCT',
                'NC_CAROUSEL_FEATUREDS',
                'NC_AUTO_FEATURED',
                'NC_ITEMS_FEATUREDS',
                'NC_CAROUSEL_BEST',
                'NC_AUTO_BEST',
                'NC_ITEMS_BESTS',
                'NC_CAROUSEL_NEW',
                'NC_AUTO_NEW',
                'NC_ITEMS_NEWS',
                'NC_CAROUSEL_SALE',
                'NC_AUTO_SALE',
                'NC_ITEMS_SALES',
                'NC_CAROUSEL_CUSTOM1',
                'NC_AUTO_CUSTOM1',
                'NC_ITEMS_CUSTOM1S',
                'NC_CAROUSEL_CUSTOM2',
                'NC_AUTO_CUSTOM2',
                'NC_ITEMS_CUSTOM2S',
                'NC_CAROUSEL_CUSTOM3',
                'NC_AUTO_CUSTOM3',
                'NC_ITEMS_CUSTOM3S',
                'NC_CAROUSEL_CUSTOM4',
                'NC_AUTO_CUSTOM4',
                'NC_ITEMS_CUSTOM4',
                'NC_CAROUSEL_CUSTOM5',
                'NC_AUTO_CUSTOM5',
                'NC_ITEMS_CUSTOM5',
                'RC_BRAND_PER_ROW',
                'RC_BRAND_NAME',
                'RC_BRAND_NAME_HOVER',
                'RC_B_LAYOUT',
                'RC_B_LINK',
                'RC_B_LINK_HOVER',
                'RC_B_SEPARATOR',
                'RC_PAGE_BQ_Q',
                'RC_CONTACT_ICON',
                'RC_WARNING_MESSAGE_COLOR',
                'RC_SUCCESS_MESSAGE_COLOR',
                'RC_DANGER_MESSAGE_COLOR',
                'RC_SIDEBAR_TITLE',
                'RC_SIDEBAR_TITLE_BG',
                'RC_SIDEBAR_TITLE_B',
                'RC_SIDEBAR_TITLE_BR',
                'RC_SIDEBAR_TITLE_B1',
                'RC_SIDEBAR_TITLE_B2',
                'RC_SIDEBAR_TITLE_B3',
                'RC_SIDEBAR_TITLE_B4',
                'RC_SIDEBAR_TITLE_BORDER',
                'RC_SIDEBAR_TITLE_LINK',
                'RC_SIDEBAR_TITLE_LINK_HOVER',
                'RC_SIDEBAR_BLOCK_CONTENT_BG',
                'RC_SIDEBAR_BLOCK_CONTENT_BORDER',
                'RC_SIDEBAR_CONTENT_B',
                'RC_SIDEBAR_CONTENT_B1',
                'RC_SIDEBAR_CONTENT_B2',
                'RC_SIDEBAR_CONTENT_B3',
                'RC_SIDEBAR_CONTENT_B4',
                'RC_SIDEBAR_CONTENT_BR',
                'RC_SIDEBAR_BLOCK_TEXT_COLOR',
                'RC_SIDEBAR_BLOCK_LINK',
                'RC_SIDEBAR_BLOCK_LINK_HOVER',
                'RC_SIDEBAR_ITEM_SEPARATOR',
                'RC_PL_FILTER_T',
                'RC_SIDEBAR_C',
                'RC_SIDEBAR_HC',
                'RC_SIDEBAR_BUTTON_BG',
                'RC_SIDEBAR_BUTTON_BORDER',
                'RC_SIDEBAR_BUTTON_COLOR',
                'RC_SIDEBAR_BUTTON_HBG',
                'RC_SIDEBAR_BUTTON_HBORDER',
                'RC_SIDEBAR_BUTTON_HCOLOR',
                'RC_SIDEBAR_PRODUCT_PRICE',
                'RC_SIDEBAR_PRODUCT_OPRICE',
                'NC_PRODUCT_SWITCH',
                'NC_SUBCAT_S',
                'NC_CAT_S',
                'RC_PL_NAV_GRID',
                'RC_PL_NUMBER_COLOR',
                'RC_PL_NUMBER_COLOR_HOVER',
                'NC_PC_LAYOUTS',
                'RC_PL_ITEM_BG',
                'RC_PL_ITEM_BORDER',
                'NC_PL_ITEM_BORDERH',
                'RC_PL_PRODUCT_NAME',
                'RC_PL_PRODUCT_PRICE',
                'RC_PL_PRODUCT_OLDPRICE',
                'RC_PL_LIST_DESCRIPTION',
                'NC_PL_SHADOWS',
                'NC_SHOW_QW',
                'NC_SHOW_SW',
                'RC_PL_HOVER_BUT',
                'RC_PL_HOVER_BUT_BG',
                'RC_PL_PRODUCT_NEW_BG',
                'RC_PL_PRODUCT_NEW_BORDER',
                'RC_PL_PRODUCT_NEW_COLOR',
                'RC_PL_PRODUCT_SALE_BG',
                'RC_PL_PRODUCT_SALE_BORDER',
                'RC_PL_PRODUCT_SALE_COLOR',
                'NC_SECOND_IMG_S',
                'NC_COLORS_S',
                'RC_PP_REVIEWS_STARON',
                'RC_PP_REVIEWS_STAROFF',
                'NC_COUNT_DAYS',
                'NC_COUNT_BG',
                'NC_COUNT_COLOR',
                'NC_COUNT_TIME',
                'NC_COUNT_WATCH',
                'NC_COUNT_WATCH_BG',
                'NC_I_QVS',
                'NC_I_DISCOVERS',
                'NC_AIS',
                'RC_PP_IMGB',
                'RC_PP_IMG_BORDER',
                'RC_PP_ICON_BORDER',
                'RC_PP_ICON_BORDER_HOVER',
                'NC_PP_QQ3S',
                'RC_PP_Z',
                'RC_PP_ZI',
                'RC_PP_ZIHBG',
                'NC_MOBADOTSS',
                'NC_MOBADOTSCS',
                'NC_ATT_RADIOS',
                'NC_OLDPRICE',
                'RC_PP_ATT_LABEL',
                'RC_PP_ATT_COLOR_ACTIVE',
                'RC_PP_PRICE_COLOR',
                'RC_PP_PRICE_COLORO',
                'NC_PP_ADD_BG',
                'NC_PP_ADD_BORDER',
                'NC_PP_ADD_COLOR',
                'NC_COUNT_PR_TITLE',
                'NC_COUNT_PR_BG',
                'NC_COUNT_PR_SEP',
                'NC_COUNT_PR_NUMBERS',
                'NC_COUNT_PR_COLOR',
                'RC_PP_INFO_LABEL',
                'RC_PP_INFO_VALUE',
                'RC_PP_DISPLAY_Q',
                'RC_PP_DISPLAY_REFER',
                'RC_PP_DISPLAY_COND',
                'RC_PP_DISPLAY_BRAND',
                'RC_O_ADDS',
                'RC_O_OPTION',
                'RC_O_OPTION_ACTIVE',
                'RC_O_INFO_TEXT',
                'RC_LC_BG',
                'RC_LC_C',
                'RC_BL_LAY',
                'RC_BL_CONT',
                'RC_BL_ROW',
                'RC_BL_HEAD',
                'RC_BL_HEAD_HOVER',
                'RC_BL_H_TITLE',
                'RC_BL_H_TITLE_H',
                'RC_BL_H_META',
                'RC_BL_H_BG',
                'RC_BL_H_BORDER',
                'RC_BL_C_ROW',
                'RC_BL_DESC',
                'RC_BL_RM_COLOR',
                'RC_BL_RM_HOVER',
                'RC_FOOTER_LAY',
                'NC_LOGO_FOOTER',
                'RC_FOOTER_BG',
                'RC_FOOTER_TITLES',
                'RC_FOOTER_TEXT',
                'RC_FOOTER_LINK',
                'RC_FOOTER_LINK_H',
                'RC_FOOTER_NEWS_BG',
                'RC_FOOTER_NEWS_BORDER',
                'RC_FOOTER_NEWS_PLACEH',
                'RC_FOOTER_NEWS_COLOR',
                'RC_FOOTER_NEWS_BUTTON',
                'RC_LEVI_POSITION',
                'NC_LEVI_BG',
                'NC_LEVI_BORDER',
                'NC_LEVI_I',
                'NC_LEVI_I_HOVER',
                'NC_LEVI_CART',
                'NC_LEVI_CART_A',
                'NC_LEVI_CLOSE',
                'NC_LEVI_CLOSE_I',
                'NC_SIDE_BG',
                'NC_SIDE_TITLE',
                'NC_SIDE_TEXT',
                'NC_SIDE_LIGHT',
                'NC_SIDE_SEP',
                'NC_LOGO_MOBILE',
                'NC_MOB_HEADER',
                'NC_MOB_MENU',
                'NC_MOB_HP',
                'NC_MOB_CAT',
                'NC_HEMOS',
                'RC_F_HEADINGS',
                'RC_F_BUTTONS',
                'RC_F_TEXT',
                'RC_F_PRICE',
                'RC_F_PN',
                'RC_LATIN_EXT',
                'RC_CYRILLIC',
                'RC_FONT_SIZE_PP',
                'RC_FONT_SIZE_BODY',
                'RC_FONT_SIZE_HEAD',
                'RC_FONT_SIZE_BUTTONS',
                'RC_FONT_SIZE_PRICE',
                'RC_FONT_SIZE_PROD',
                'RC_FONT_SIZE_PN',
                'NC_UP_HP',
                'NC_UP_NC',
                'NC_UP_NP',
                'NC_UP_F',
                'NC_UP_BP',
                'NC_UP_MI',
                'NC_UP_MENU',
                'NC_UP_HEAD',
                'NC_UP_BUT',
                'NC_FW_MENU',
                'NC_FW_HEADING',
                'NC_FW_BUT',
                'NC_FW_PN',
                'NC_FW_CT',
                'NC_FW_PRICE',
                'NC_ITAL_PN',
                'NC_ITALIC_PP',
                'NC_LS',
                'NC_LS_H',
                'NC_LS_M',
                'NC_LS_P',
                'NC_LS_T',
                'NC_LS_B',
                'NC_CSS'
            );
            $export = array();
            foreach ($keys as $value) {
                $export[$value] = Configuration::get($value);
            }
            $json_export = json_encode($export);
            $output .= '<a id="ayon_export" download="ayon_export.json" type="text/json"></a>
                       <script>
                       $(document).ready(function(){
                              var data = ' . $json_export . ';
                              var a = document.getElementById("ayon_export");
                              a.href="data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(data));
                              a.click();
                       });
                       </script>';
        }

        if (Tools::isSubmit('ayon_import_submit')) {
            $data = file_get_contents($_FILES['ayon_import_file']['tmp_name']);
            $arr = json_decode($data);
            foreach ($arr as $key => $value) {
                Configuration::updateValue($key, $value);
            }
        }

        if (Tools::isSubmit('nc_body_im_upload')) {
            if (isset($_FILES['nc_body_im_field']) && isset($_FILES['nc_body_im_field']['tmp_name']) && !empty($_FILES['nc_body_im_field']['tmp_name'])) {
                if ($error = ImageManager::validateUpload($_FILES['nc_body_im_field'], Tools::convertBytes(ini_get('upload_max_filesize'))))

                    $errors .= $error;

                else {

                    Configuration::updateValue('NC_BODY_IM_BG_EXT', substr($_FILES['nc_body_im_field']['name'], strrpos($_FILES['nc_body_im_field']['name'], '.') + 1));

                    if (Shop::getContext() == Shop::CONTEXT_SHOP)
                        $adv_imgname = 'nc_body_im_background' . '-' . (int)$this->context->shop->getContextShopID();

                    if (!move_uploaded_file($_FILES['nc_body_im_field']['tmp_name'], _PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_BODY_IM_BG_EXT')))
                        $errors .= $this->l('Error move uploaded file');
                    $output = '<div class="conf confirm">' . $this->l('Image uploaded') . '</div>' . $output;
                }
            }
        }
        if (Tools::isSubmit('nc_body_im_delete')) {

            if (Shop::getContext() == Shop::CONTEXT_SHOP)
                $adv_imgname = 'nc_body_im_background' . '-' . (int)$this->context->shop->getContextShopID();

            if (file_exists(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_BODY_IM_BG_EXT')))
                unlink(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_BODY_IM_BG_EXT'));
            Configuration::updateValue('NC_BODY_IM_BG_EXT', "");

            $output = '<div class="conf confirm">' . $this->l('Image removed') . '</div>' . $output;
        }

        if (Tools::isSubmit('nc_main_im_upload')) {
            if (isset($_FILES['nc_main_im_field']) && isset($_FILES['nc_main_im_field']['tmp_name']) && !empty($_FILES['nc_main_im_field']['tmp_name'])) {
                if ($error = ImageManager::validateUpload($_FILES['nc_main_im_field'], Tools::convertBytes(ini_get('upload_max_filesize'))))

                    $errors .= $error;

                else {

                    Configuration::updateValue('NC_MAIN_IM_BG_EXT', substr($_FILES['nc_main_im_field']['name'], strrpos($_FILES['nc_main_im_field']['name'], '.') + 1));

                    if (Shop::getContext() == Shop::CONTEXT_SHOP)
                        $adv_imgname = 'nc_main_im_background' . '-' . (int)$this->context->shop->getContextShopID();

                    if (!move_uploaded_file($_FILES['nc_main_im_field']['tmp_name'], _PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_MAIN_IM_BG_EXT')))
                        $errors .= $this->l('Error move uploaded file');
                    $output = '<div class="conf confirm">' . $this->l('Image uploaded') . '</div>' . $output;
                }
            }
        }
        if (Tools::isSubmit('nc_main_im_delete')) {

            if (Shop::getContext() == Shop::CONTEXT_SHOP)
                $adv_imgname = 'nc_main_im_background' . '-' . (int)$this->context->shop->getContextShopID();

            if (file_exists(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_MAIN_IM_BG_EXT')))
                unlink(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_MAIN_IM_BG_EXT'));
            Configuration::updateValue('NC_MAIN_IM_BG_EXT', "");

            $output = '<div class="conf confirm">' . $this->l('Image removed') . '</div>' . $output;
        }


        if (Tools::isSubmit('nc_header_im_upload')) {
            if (isset($_FILES['nc_header_im_field']) && isset($_FILES['nc_header_im_field']['tmp_name']) && !empty($_FILES['nc_header_im_field']['tmp_name'])) {
                if ($error = ImageManager::validateUpload($_FILES['nc_header_im_field'], Tools::convertBytes(ini_get('upload_max_filesize'))))

                    $errors .= $error;

                else {

                    Configuration::updateValue('NC_HEADER_IM_BG_EXT', substr($_FILES['nc_header_im_field']['name'], strrpos($_FILES['nc_header_im_field']['name'], '.') + 1));

                    if (Shop::getContext() == Shop::CONTEXT_SHOP)
                        $adv_imgname = 'nc_header_im_background' . '-' . (int)$this->context->shop->getContextShopID();

                    if (!move_uploaded_file($_FILES['nc_header_im_field']['tmp_name'], _PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_HEADER_IM_BG_EXT')))
                        $errors .= $this->l('Error move uploaded file');
                    $output = '<div class="conf confirm">' . $this->l('Image uploaded') . '</div>' . $output;
                }
            }
        }
        if (Tools::isSubmit('nc_header_im_delete')) {

            if (Shop::getContext() == Shop::CONTEXT_SHOP)
                $adv_imgname = 'nc_header_im_background' . '-' . (int)$this->context->shop->getContextShopID();

            if (file_exists(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_HEADER_IM_BG_EXT')))
                unlink(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_HEADER_IM_BG_EXT'));
            Configuration::updateValue('NC_HEADER_IM_BG_EXT', "");

            $output = '<div class="conf confirm">' . $this->l('Image removed') . '</div>' . $output;
        }


        if (Tools::isSubmit('logo_loader_button2')) {
            if (isset($_FILES['logo_loader_field2']) && isset($_FILES['logo_loader_field2']['tmp_name']) && !empty($_FILES['logo_loader_field2']['tmp_name'])) {
                if ($error = ImageManager::validateUpload($_FILES['logo_loader_field2'], Tools::convertBytes(ini_get('upload_max_filesize'))))

                    $errors .= $error;

                else {

                    Configuration::updateValue('NC_LOGO_LOADER', substr($_FILES['logo_loader_field2']['name'], strrpos($_FILES['logo_loader_field2']['name'], '.') + 1));

                    if (Shop::getContext() == Shop::CONTEXT_SHOP)
                        $adv_imgname = 'logo-loader' . '-' . (int)$this->context->shop->getContextShopID();

                    if (!move_uploaded_file($_FILES['logo_loader_field2']['tmp_name'], _PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_LOADER')))
                        $errors .= $this->l('Error move uploaded file');
                    $output = '<div class="conf confirm">' . $this->l('Image uploaded') . '</div>' . $output;
                }
            }
        }
        if (Tools::isSubmit('logo_loader_delete2')) {

            if (Shop::getContext() == Shop::CONTEXT_SHOP)
                $adv_imgname = 'logo-loader' . '-' . (int)$this->context->shop->getContextShopID();


            if (file_exists(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_LOADER')))
                unlink(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_LOADER'));
            Configuration::updateValue('NC_LOGO_LOADER', "");

            $output = '<div class="conf confirm">' . $this->l('Image removed') . '</div>' . $output;
        }


        if (Tools::isSubmit('logo_normal_button2')) {
            if (isset($_FILES['logo_normal_field2']) && isset($_FILES['logo_normal_field2']['tmp_name']) && !empty($_FILES['logo_normal_field2']['tmp_name'])) {
                if ($error = ImageManager::validateUpload($_FILES['logo_normal_field2'], Tools::convertBytes(ini_get('upload_max_filesize'))))
                    $errors .= $error;
                else {
                    Configuration::updateValue('NC_LOGO_NORMAL', substr($_FILES['logo_normal_field2']['name'], strrpos($_FILES['logo_normal_field2']['name'], '.') + 1));

                    if (Shop::getContext() == Shop::CONTEXT_SHOP)
                        $adv_imgname = 'logo-normal' . '-' . (int)$this->context->shop->getContextShopID();

                    if (!move_uploaded_file($_FILES['logo_normal_field2']['tmp_name'], _PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_NORMAL')))
                        $errors .= $this->l('Error move uploaded file');
                    $output = '<div class="conf confirm">' . $this->l('Image uploaded') . '</div>' . $output;
                }
            }
        }
        if (Tools::isSubmit('logo_normal_delete2')) {

            if (Shop::getContext() == Shop::CONTEXT_SHOP)
                $adv_imgname = 'logo-normal' . '-' . (int)$this->context->shop->getContextShopID();

            if (file_exists(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_NORMAL')))
                unlink(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_NORMAL'));
            Configuration::updateValue('NC_LOGO_NORMAL', "");

            $output = '<div class="conf confirm">' . $this->l('Image removed') . '</div>' . $output;
        }

        if (Tools::isSubmit('logo_mobile_button2')) {
            if (isset($_FILES['logo_mobile_field2']) && isset($_FILES['logo_mobile_field2']['tmp_name']) && !empty($_FILES['logo_mobile_field2']['tmp_name'])) {
                if ($error = ImageManager::validateUpload($_FILES['logo_mobile_field2'], Tools::convertBytes(ini_get('upload_max_filesize'))))
                    $errors .= $error;
                else {
                    Configuration::updateValue('NC_LOGO_MOBILE', substr($_FILES['logo_mobile_field2']['name'], strrpos($_FILES['logo_mobile_field2']['name'], '.') + 1));

                    if (Shop::getContext() == Shop::CONTEXT_SHOP)
                        $adv_imgname = 'logo-mobile' . '-' . (int)$this->context->shop->getContextShopID();

                    if (!move_uploaded_file($_FILES['logo_mobile_field2']['tmp_name'], _PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_MOBILE')))
                        $errors .= $this->l('Error move uploaded file');
                    $output = '<div class="conf confirm">' . $this->l('Image uploaded') . '</div>' . $output;
                }
            }
        }
        if (Tools::isSubmit('logo_mobile_delete2')) {

            if (Shop::getContext() == Shop::CONTEXT_SHOP)
                $adv_imgname = 'logo-mobile' . '-' . (int)$this->context->shop->getContextShopID();

            if (file_exists(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_MOBILE')))
                unlink(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_MOBILE'));
            Configuration::updateValue('NC_LOGO_MOBILE', "");

            $output = '<div class="conf confirm">' . $this->l('Image removed') . '</div>' . $output;
        }


        if (Tools::isSubmit('logo_footer_button2')) {
            if (isset($_FILES['logo_footer_field2']) && isset($_FILES['logo_footer_field2']['tmp_name']) && !empty($_FILES['logo_footer_field2']['tmp_name'])) {
                if ($error = ImageManager::validateUpload($_FILES['logo_footer_field2'], Tools::convertBytes(ini_get('upload_max_filesize'))))
                    $errors .= $error;
                else {
                    Configuration::updateValue('NC_LOGO_FOOTER', substr($_FILES['logo_footer_field2']['name'], strrpos($_FILES['logo_footer_field2']['name'], '.') + 1));

                    if (Shop::getContext() == Shop::CONTEXT_SHOP)
                        $adv_imgname = 'logo-footer' . '-' . (int)$this->context->shop->getContextShopID();

                    if (!move_uploaded_file($_FILES['logo_footer_field2']['tmp_name'], _PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_FOOTER')))
                        $errors .= $this->l('Error move uploaded file');
                    $output = '<div class="conf confirm">' . $this->l('Image uploaded') . '</div>' . $output;
                }
            }
        }
        if (Tools::isSubmit('logo_footer_delete2')) {

            if (Shop::getContext() == Shop::CONTEXT_SHOP)
                $adv_imgname = 'logo-footer' . '-' . (int)$this->context->shop->getContextShopID();

            if (file_exists(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_FOOTER')))
                unlink(_PS_MODULE_DIR_ . $this->name . '/upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_FOOTER'));
            Configuration::updateValue('NC_LOGO_FOOTER', "");

            $output = '<div class="conf confirm">' . $this->l('Image removed') . '</div>' . $output;
        }

        if (Tools::isSubmit('save_changes')) {

            Configuration::updateValue('RC_G_LAY', (string)(Tools::getValue("g_lay")));
            Configuration::updateValue('RC_G_TP', (string)(Tools::getValue("g_tp")));
            Configuration::updateValue('RC_G_BP', (string)(Tools::getValue("g_bp")));
            Configuration::updateValue('RC_BODY_BOX_SW', (string)(Tools::getValue("body_box_sw")));
            Configuration::updateValue('RC_MAIN_BACKGROUND_COLOR', (string)(Tools::getValue("main_background_color")));
            Configuration::updateValue('NC_BODY_GS', (string)(Tools::getValue("nc_body_gs")));
            Configuration::updateValue('NC_BODY_GE', (string)(Tools::getValue("nc_body_ge")));
            Configuration::updateValue('NC_BODY_GG', (string)(Tools::getValue("nc_body_gg")));
            Configuration::updateValue('NC_BODY_IM_BG_REPEAT', (int)(Tools::getValue("nc_body_im_bg_repeat")));
            Configuration::updateValue('NC_BODY_IM_BG_POSITION', (int)(Tools::getValue("nc_body_im_bg_position")));
            Configuration::updateValue('NC_BODY_IM_BG_FIXED', (int)(Tools::getValue("nc_body_im_bg_fixed")));
            Configuration::updateValue('RC_GRADIENT_SCHEME', (string)(Tools::getValue("gradient_scheme")));
            Configuration::updateValue('RC_DISPLAY_GRADIENT', (string)(Tools::getValue("display_gradient")));
            Configuration::updateValue('RC_BODY_BG_PATTERN', (string)(Tools::getValue("body_bg_pattern")));
            Configuration::updateValue('NC_MAIN_BGS', (string)(Tools::getValue("nc_main_bg")));
            Configuration::updateValue('NC_MAIN_BC', (string)(Tools::getValue("nc_main_bc")));
            Configuration::updateValue('NC_MAIN_GS', (string)(Tools::getValue("nc_main_gs")));
            Configuration::updateValue('NC_MAIN_GE', (string)(Tools::getValue("nc_main_ge")));
            Configuration::updateValue('NC_MAIN_GG', (string)(Tools::getValue("nc_main_gg")));
            Configuration::updateValue('NC_MAIN_IM_BG_REPEAT', (int)(Tools::getValue("nc_main_im_bg_repeat")));
            Configuration::updateValue('NC_MAIN_IM_BG_POSITION', (int)(Tools::getValue("nc_main_im_bg_position")));
            Configuration::updateValue('NC_MAIN_IM_BG_FIXED', (int)(Tools::getValue("nc_main_im_bg_fixed")));

            // header
            Configuration::updateValue('RC_HEADER_LAY', (string)(Tools::getValue("header_lay")));
            Configuration::updateValue('NC_LOGO_NORMAL', (string)(Tools::getValue("nc_logo_normal")));
            Configuration::updateValue('NC_HEADER_SHADOWS', (string)(Tools::getValue("nc_header_shadow")));
            Configuration::updateValue('NC_HEADER_BGS', (string)(Tools::getValue("nc_header_bg")));
            Configuration::updateValue('NC_HEADER_BC', (string)(Tools::getValue("nc_header_bc")));
            Configuration::updateValue('NC_HEADER_GS', (string)(Tools::getValue("nc_header_gs")));
            Configuration::updateValue('NC_HEADER_GE', (string)(Tools::getValue("nc_header_ge")));
            Configuration::updateValue('NC_HEADER_GG', (string)(Tools::getValue("nc_header_gg")));
            Configuration::updateValue('NC_HEADER_IM_BG_REPEAT', (int)(Tools::getValue("nc_header_im_bg_repeat")));
            Configuration::updateValue('NC_HEADER_IM_BG_POSITION', (int)(Tools::getValue("nc_header_im_bg_position")));
            Configuration::updateValue('NC_HEADER_IM_BG_FIXED', (int)(Tools::getValue("nc_header_im_bg_fixed")));
            Configuration::updateValue('NC_HEADER_ST_BGCOLOR', (string)(Tools::getValue("nc_header_st_bg")));
            Configuration::updateValue('NC_HEADER_ST_BGCOLORHOVER', (string)(Tools::getValue("nc_header_st_bgh")));
            Configuration::updateValue('NC_HEADER_ST_LINKCOLOR', (string)(Tools::getValue("nc_header_st_link")));
            Configuration::updateValue('NC_HEADER_ST_LINKCOLORHOVER', (string)(Tools::getValue("nc_header_st_linkh")));
            Configuration::updateValue('RC_HEADER_NBG', (string)(Tools::getValue("header_nbg")));
            Configuration::updateValue('RC_HEADER_NB', (string)(Tools::getValue("header_nb")));
            Configuration::updateValue('RC_HEADER_NT', (string)(Tools::getValue("header_nt")));
            Configuration::updateValue('RC_HEADER_NL', (string)(Tools::getValue("header_nl")));
            Configuration::updateValue('RC_HEADER_NLH', (string)(Tools::getValue("header_nlh")));
            Configuration::updateValue('RC_HEADER_NS', (string)(Tools::getValue("header_ns")));
            Configuration::updateValue('NC_M_ALIGN_S', (string)(Tools::getValue("nc_m_align")));
            Configuration::updateValue('NC_M_LAYOUT_S', (string)(Tools::getValue("nc_m_layout")));
            Configuration::updateValue('NC_M_UNDER_S', (string)(Tools::getValue("nc_m_under")));
            Configuration::updateValue('NC_M_UNDER_COLOR', (string)(Tools::getValue("nc_m_under_color")));
            Configuration::updateValue('NC_M_OVERRIDE_S', (string)(Tools::getValue("nc_m_override")));
            Configuration::updateValue('RC_M_BG', (string)(Tools::getValue("m_bg")));
            Configuration::updateValue('RC_M_LINK_BG_HOVER', (string)(Tools::getValue("m_link_bg_hover")));
            Configuration::updateValue('RC_M_LINK', (string)(Tools::getValue("m_link")));
            Configuration::updateValue('RC_M_LINK_HOVER', (string)(Tools::getValue("m_link_hover")));
            Configuration::updateValue('RC_M_POPUP_LLINK', (string)(Tools::getValue("m_popup_llink")));
            Configuration::updateValue('RC_M_POPUP_LLINK_HOVER', (string)(Tools::getValue("m_popup_llink_hover")));
            Configuration::updateValue('RC_M_POPUP_LBG', (string)(Tools::getValue("m_popup_lbg")));
            Configuration::updateValue('RC_M_POPUP_LCHEVRON', (string)(Tools::getValue("m_popup_lchevron")));
            Configuration::updateValue('RC_M_POPUP_LBORDER', (string)(Tools::getValue("m_popup_lborder")));
            Configuration::updateValue('NC_M_BR_S', (string)(Tools::getValue("nc_m_br")));
            Configuration::updateValue('RC_SEARCH_LAY', (string)(Tools::getValue("search_lay")));
            Configuration::updateValue('NC_I_SEARCHS', (string)(Tools::getValue("nc_i_search")));
            Configuration::updateValue('RC_SEARCH_BG', (string)(Tools::getValue("search_bg")));
            Configuration::updateValue('RC_SEARCH_LINE', (string)(Tools::getValue("search_line")));
            Configuration::updateValue('RC_SEARCH_INPUT', (string)(Tools::getValue("search_input")));
            Configuration::updateValue('RC_SEARCH_T', (string)(Tools::getValue("search_t")));
            Configuration::updateValue('RC_SEARCH_ICON', (string)(Tools::getValue("search_icon")));
            Configuration::updateValue('RC_SEARCH_BG_HOVER', (string)(Tools::getValue("search_bg_hover")));
            Configuration::updateValue('RC_SEARCH_LINEH', (string)(Tools::getValue("search_lineh")));
            Configuration::updateValue('RC_SEARCH_INPUTH', (string)(Tools::getValue("search_inputh")));
            Configuration::updateValue('RC_SEARCH_T_HOVER', (string)(Tools::getValue("search_t_hover")));
            Configuration::updateValue('RC_SEARCH_ICONH', (string)(Tools::getValue("search_iconh")));
            Configuration::updateValue('RC_CART_LAY', (string)(Tools::getValue("cart_lay")));
            Configuration::updateValue('RC_CART_ICON', (string)(Tools::getValue("cart_icon")));
            Configuration::updateValue('RC_CART_BG', (string)(Tools::getValue("cart_bg")));
            Configuration::updateValue('RC_CART_B', (string)(Tools::getValue("cart_b")));
            Configuration::updateValue('RC_CART_I', (string)(Tools::getValue("cart_i")));
            Configuration::updateValue('RC_CART_T', (string)(Tools::getValue("cart_t")));
            Configuration::updateValue('RC_CART_Q', (string)(Tools::getValue("cart_q")));
            Configuration::updateValue('RC_CART_BG_HOVER', (string)(Tools::getValue("cart_bg_hover")));
            Configuration::updateValue('RC_CART_B_HOVER', (string)(Tools::getValue("cart_b_hover")));
            Configuration::updateValue('RC_CART_I_HOVER', (string)(Tools::getValue("cart_i_hover")));
            Configuration::updateValue('RC_CART_T_HOVER', (string)(Tools::getValue("cart_t_hover")));
            Configuration::updateValue('RC_CART_Q_HOVER', (string)(Tools::getValue("cart_q_hover")));

            // body design
            Configuration::updateValue('RC_G_BG_CONTENT', (string)(Tools::getValue("g_bg_content")));
            Configuration::updateValue('RC_G_BORDER', (string)(Tools::getValue("g_border")));
            Configuration::updateValue('RC_G_BODY_TEXT', (string)(Tools::getValue("g_body_text")));
            Configuration::updateValue('RC_G_BODY_COMMENT', (string)(Tools::getValue("g_body_comment")));
            Configuration::updateValue('RC_G_BODY_LINK', (string)(Tools::getValue("g_body_link")));
            Configuration::updateValue('RC_G_BODY_LINK_HOVER', (string)(Tools::getValue("g_body_link_hover")));
            Configuration::updateValue('RC_LABEL', (string)(Tools::getValue("g_label")));
            Configuration::updateValue('RC_G_HEADER', (string)(Tools::getValue("g_header")));
            Configuration::updateValue('RC_HEADER_UNDER', (string)(Tools::getValue("g_header_under")));
            Configuration::updateValue('RC_HEADER_DECOR', (string)(Tools::getValue("g_header_decor")));
            Configuration::updateValue('RC_G_CC', (string)(Tools::getValue("g_cc")));
            Configuration::updateValue('RC_G_CH', (string)(Tools::getValue("g_ch")));
            Configuration::updateValue('RC_G_HB', (string)(Tools::getValue("g_hb")));
            Configuration::updateValue('RC_G_HC', (string)(Tools::getValue("g_hc")));
            Configuration::updateValue('RC_G_BG_EVEN', (string)(Tools::getValue("g_bg_even")));
            Configuration::updateValue('RC_G_COLOR_EVEN', (string)(Tools::getValue("g_color_even")));
            Configuration::updateValue('RC_G_ACC_ICON', (string)(Tools::getValue("g_acc_icon")));
            Configuration::updateValue('RC_G_ACC_TITLE', (string)(Tools::getValue("g_acc_title")));
            Configuration::updateValue('RC_FANCY_NBG', (string)(Tools::getValue("g_fancy_nbg")));
            Configuration::updateValue('RC_FANCY_NC', (string)(Tools::getValue("g_fancy_nc")));

            Configuration::updateValue('RC_B_NORMAL_BG', (string)(Tools::getValue("b_normal_bg")));
            Configuration::updateValue('RC_B_NORMAL_BORDER', (string)(Tools::getValue("b_normal_border")));
            Configuration::updateValue('RC_B_NORMAL_BORDER_HOVER', (string)(Tools::getValue("b_normal_border_hover")));
            Configuration::updateValue('RC_B_NORMAL_BG_HOVER', (string)(Tools::getValue("b_normal_bg_hover")));
            Configuration::updateValue('RC_B_NORMAL_COLOR', (string)(Tools::getValue("b_normal_color")));
            Configuration::updateValue('RC_B_NORMAL_COLOR_HOVER', (string)(Tools::getValue("b_normal_color_hover")));
            Configuration::updateValue('RC_B_EX_BG', (string)(Tools::getValue("b_ex_bg")));
            Configuration::updateValue('RC_B_EX_BORDER', (string)(Tools::getValue("b_ex_border")));
            Configuration::updateValue('RC_B_EX_COLOR', (string)(Tools::getValue("b_ex_color")));
            Configuration::updateValue('NC_B_RADIUS', (string)(Tools::getValue("nc_b_radius")));
            Configuration::updateValue('NC_B_SHS', (string)(Tools::getValue("nc_b_sh")));

            Configuration::updateValue('RC_I_BG', (string)(Tools::getValue("i_bg")));
            Configuration::updateValue('RC_I_B_COLOR', (string)(Tools::getValue("i_b_color")));
            Configuration::updateValue('RC_I_COLOR', (string)(Tools::getValue("i_color")));
            Configuration::updateValue('RC_I_BG_FOCUS', (string)(Tools::getValue("i_bg_focus")));
            Configuration::updateValue('RC_I_COLOR_FOCUS', (string)(Tools::getValue("i_color_focus")));
            Configuration::updateValue('RC_I_B_FOCUS', (string)(Tools::getValue("i_b_focus")));
            Configuration::updateValue('RC_I_B_RADIUS', (string)(Tools::getValue("i_b_radius")));
            Configuration::updateValue('RC_I_PH', (string)(Tools::getValue("i_ph")));
            Configuration::updateValue('RC_RC_BG_ACTIVE', (string)(Tools::getValue("rc_bg_active")));

            Configuration::updateValue('NC_LOADERS', (string)(Tools::getValue("nc_loader")));
            Configuration::updateValue('NC_LOADER_LAYS', (string)(Tools::getValue("nc_loader_lay")));
            Configuration::updateValue('NC_LOADER_BG', (string)(Tools::getValue("nc_loader_bg")));
            Configuration::updateValue('NC_LOADER_COLOR', (string)(Tools::getValue("nc_loader_color")));
            Configuration::updateValue('NC_LOADER_COLOR2', (string)(Tools::getValue("nc_loader_color2")));
            Configuration::updateValue('NC_LOADER_LOGOS', (string)(Tools::getValue("nc_loader_logo")));
            Configuration::updateValue('NC_LOGO_LOADER', (string)(Tools::getValue("nc_logo_loader")));

            // Homepage content
            Configuration::updateValue('RC_BAN_SPA_BEHEAD', (string)(Tools::getValue("ban_spa_behead")));
            Configuration::updateValue('RC_BAN_TS_BEHEAD', (string)(Tools::getValue("ban_ts_behead")));
            Configuration::updateValue('RC_BAN_BS_BEHEAD', (string)(Tools::getValue("ban_bs_behead")));
            Configuration::updateValue('RC_BAN_SPA_TOP', (string)(Tools::getValue("ban_spa_top")));
            Configuration::updateValue('RC_BAN_TS_TOP', (string)(Tools::getValue("ban_ts_top")));
            Configuration::updateValue('RC_BAN_BS_TOP', (string)(Tools::getValue("ban_bs_top")));
            Configuration::updateValue('RC_BAN_TS_LEFT', (string)(Tools::getValue("ban_ts_left")));
            Configuration::updateValue('RC_BAN_BS_LEFT', (string)(Tools::getValue("ban_bs_left")));
            Configuration::updateValue('RC_BAN_TS_RIGHT', (string)(Tools::getValue("ban_ts_right")));
            Configuration::updateValue('RC_BAN_BS_RIGHT', (string)(Tools::getValue("ban_bs_right")));
            Configuration::updateValue('RC_BAN_SPA_PRO', (string)(Tools::getValue("ban_spa_pro")));
            Configuration::updateValue('RC_BAN_TS_PRO', (string)(Tools::getValue("ban_ts_pro")));
            Configuration::updateValue('RC_BAN_BS_PRO', (string)(Tools::getValue("ban_bs_pro")));
            Configuration::updateValue('RC_BAN_SPA_BEFOOT', (string)(Tools::getValue("ban_spa_befoot")));
            Configuration::updateValue('RC_BAN_TS_BEFOOT', (string)(Tools::getValue("ban_ts_befoot")));
            Configuration::updateValue('RC_BAN_BS_BEFOOT', (string)(Tools::getValue("ban_bs_befoot")));
            Configuration::updateValue('RC_BAN_SPA_FOOT', (string)(Tools::getValue("ban_spa_foot")));
            Configuration::updateValue('RC_BAN_TS_FOOT', (string)(Tools::getValue("ban_ts_foot")));
            Configuration::updateValue('RC_BAN_BS_FOOT', (string)(Tools::getValue("ban_bs_foot")));
            Configuration::updateValue('RC_BAN_SPA_SIDECART', (string)(Tools::getValue("ban_spa_sidecart")));
            Configuration::updateValue('RC_BAN_TS_SIDECART', (string)(Tools::getValue("ban_ts_sidecart")));
            Configuration::updateValue('RC_BAN_BS_SIDECART', (string)(Tools::getValue("ban_bs_sidecart")));
            Configuration::updateValue('RC_BAN_SPA_SIDESEARCH', (string)(Tools::getValue("ban_spa_sidesearch")));
            Configuration::updateValue('RC_BAN_TS_SIDESEARCH', (string)(Tools::getValue("ban_ts_sidesearch")));
            Configuration::updateValue('RC_BAN_BS_SIDESEARCH', (string)(Tools::getValue("ban_bs_sidesearch")));
            Configuration::updateValue('RC_BAN_SPA_SIDEMAIL', (string)(Tools::getValue("ban_spa_sidemail")));
            Configuration::updateValue('RC_BAN_TS_SIDEMAIL', (string)(Tools::getValue("ban_ts_sidemail")));
            Configuration::updateValue('RC_BAN_BS_SIDEMAIL', (string)(Tools::getValue("ban_bs_sidemail")));
            Configuration::updateValue('RC_BAN_SPA_SIDEMOBILEMENU', (string)(Tools::getValue("ban_spa_sidemobilemenu")));
            Configuration::updateValue('RC_BAN_TS_SIDEMOBILEMENU', (string)(Tools::getValue("ban_ts_sidemobilemenu")));
            Configuration::updateValue('RC_BAN_BS_SIDEMOBILEMENU', (string)(Tools::getValue("ban_bs_sidemobilemenu")));
            Configuration::updateValue('RC_BAN_SPA_PRODUCT', (string)(Tools::getValue("ban_spa_product")));
            Configuration::updateValue('RC_BAN_TS_PRODUCT', (string)(Tools::getValue("ban_ts_product")));
            Configuration::updateValue('RC_BAN_BS_PRODUCT', (string)(Tools::getValue("ban_bs_product")));

            Configuration::updateValue('NC_CAROUSEL_FEATUREDS', (string)(Tools::getValue("nc_carousel_featured")));
            Configuration::updateValue('NC_AUTO_FEATURED', (string)(Tools::getValue("nc_auto_featured")));
            Configuration::updateValue('NC_ITEMS_FEATUREDS', (string)(Tools::getValue("nc_items_featured")));
            Configuration::updateValue('NC_CAROUSEL_BEST', (string)(Tools::getValue("nc_carousel_best")));
            Configuration::updateValue('NC_AUTO_BEST', (string)(Tools::getValue("nc_auto_best")));
            Configuration::updateValue('NC_ITEMS_BESTS', (string)(Tools::getValue("nc_items_best")));
            Configuration::updateValue('NC_CAROUSEL_NEW', (string)(Tools::getValue("nc_carousel_new")));
            Configuration::updateValue('NC_AUTO_NEW', (string)(Tools::getValue("nc_auto_new")));
            Configuration::updateValue('NC_ITEMS_NEWS', (string)(Tools::getValue("nc_items_new")));
            Configuration::updateValue('NC_CAROUSEL_SALE', (string)(Tools::getValue("nc_carousel_sale")));
            Configuration::updateValue('NC_AUTO_SALE', (string)(Tools::getValue("nc_auto_sale")));
            Configuration::updateValue('NC_ITEMS_SALES', (string)(Tools::getValue("nc_items_sale")));
            Configuration::updateValue('NC_CAROUSEL_CUSTOM1', (string)(Tools::getValue("nc_carousel_custom1")));
            Configuration::updateValue('NC_AUTO_CUSTOM1', (string)(Tools::getValue("nc_auto_custom1")));
            Configuration::updateValue('NC_ITEMS_CUSTOM1S', (string)(Tools::getValue("nc_items_custom1")));
            Configuration::updateValue('NC_CAROUSEL_CUSTOM2', (string)(Tools::getValue("nc_carousel_custom2")));
            Configuration::updateValue('NC_AUTO_CUSTOM2', (string)(Tools::getValue("nc_auto_custom2")));
            Configuration::updateValue('NC_ITEMS_CUSTOM2S', (string)(Tools::getValue("nc_items_custom2")));
            Configuration::updateValue('NC_CAROUSEL_CUSTOM3', (string)(Tools::getValue("nc_carousel_custom3")));
            Configuration::updateValue('NC_AUTO_CUSTOM3', (string)(Tools::getValue("nc_auto_custom3")));
            Configuration::updateValue('NC_ITEMS_CUSTOM3S', (string)(Tools::getValue("nc_items_custom3")));
            Configuration::updateValue('NC_CAROUSEL_CUSTOM4', (string)(Tools::getValue("nc_carousel_custom4")));
            Configuration::updateValue('NC_AUTO_CUSTOM4', (string)(Tools::getValue("nc_auto_custom4")));
            Configuration::updateValue('NC_ITEMS_CUSTOM4', (string)(Tools::getValue("nc_items_custom4")));
            Configuration::updateValue('NC_CAROUSEL_CUSTOM5', (string)(Tools::getValue("nc_carousel_custom5")));
            Configuration::updateValue('NC_AUTO_CUSTOM5', (string)(Tools::getValue("nc_auto_custom5")));
            Configuration::updateValue('NC_ITEMS_CUSTOM5', (string)(Tools::getValue("nc_items_custom5")));

            Configuration::updateValue('RC_BRAND_PER_ROW', (string)(Tools::getValue("brand_per_row")));
            Configuration::updateValue('RC_BRAND_NAME', (string)(Tools::getValue("brand_name")));
            Configuration::updateValue('RC_BRAND_NAME_HOVER', (string)(Tools::getValue("brand_name_hover")));

            // page content
            Configuration::updateValue('RC_B_LAYOUT', (string)(Tools::getValue("b_layout")));
            Configuration::updateValue('RC_B_LINK', (string)(Tools::getValue("b_link")));
            Configuration::updateValue('RC_B_LINK_HOVER', (string)(Tools::getValue("b_link_hover")));
            Configuration::updateValue('RC_B_SEPARATOR', (string)(Tools::getValue("b_separator")));
            Configuration::updateValue('RC_PAGE_BQ_Q', (string)(Tools::getValue("page_bq_q")));
            Configuration::updateValue('RC_CONTACT_ICON', (string)(Tools::getValue("contact_icon")));
            Configuration::updateValue('RC_WARNING_MESSAGE_COLOR', (string)(Tools::getValue("warning_message_color")));
            Configuration::updateValue('RC_SUCCESS_MESSAGE_COLOR', (string)(Tools::getValue("success_message_color")));
            Configuration::updateValue('RC_DANGER_MESSAGE_COLOR', (string)(Tools::getValue("danger_message_color")));

            // Sidebar and filter
            Configuration::updateValue('RC_SIDEBAR_TITLE', (string)(Tools::getValue("sidebar_title")));
            Configuration::updateValue('RC_SIDEBAR_TITLE_BG', (string)(Tools::getValue("sidebar_title_bg")));
            Configuration::updateValue('RC_SIDEBAR_TITLE_B', (string)(Tools::getValue("sidebar_title_b")));
            Configuration::updateValue('RC_SIDEBAR_TITLE_BR', (string)(Tools::getValue("sidebar_title_br")));
            Configuration::updateValue('RC_SIDEBAR_TITLE_B1', (string)(Tools::getValue("sidebar_title_b1")));
            Configuration::updateValue('RC_SIDEBAR_TITLE_B2', (string)(Tools::getValue("sidebar_title_b2")));
            Configuration::updateValue('RC_SIDEBAR_TITLE_B3', (string)(Tools::getValue("sidebar_title_b3")));
            Configuration::updateValue('RC_SIDEBAR_TITLE_B4', (string)(Tools::getValue("sidebar_title_b4")));
            Configuration::updateValue('RC_SIDEBAR_TITLE_BORDER', (string)(Tools::getValue("sidebar_title_border")));
            Configuration::updateValue('RC_SIDEBAR_TITLE_LINK', (string)(Tools::getValue("sidebar_title_link")));
            Configuration::updateValue('RC_SIDEBAR_TITLE_LINK_HOVER', (string)(Tools::getValue("sidebar_title_link_hover")));
            Configuration::updateValue('RC_SIDEBAR_BLOCK_CONTENT_BG', (string)(Tools::getValue("sidebar_block_content_bg")));
            Configuration::updateValue('RC_SIDEBAR_BLOCK_CONTENT_BORDER', (string)(Tools::getValue("sidebar_block_content_border")));
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B', (string)(Tools::getValue("sidebar_content_b")));
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B1', (string)(Tools::getValue("sidebar_content_b1")));
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B2', (string)(Tools::getValue("sidebar_content_b2")));
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B3', (string)(Tools::getValue("sidebar_content_b3")));
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B4', (string)(Tools::getValue("sidebar_content_b4")));
            Configuration::updateValue('RC_SIDEBAR_CONTENT_BR', (string)(Tools::getValue("sidebar_content_br")));
            Configuration::updateValue('RC_SIDEBAR_BLOCK_TEXT_COLOR', (string)(Tools::getValue("sidebar_block_text_color")));
            Configuration::updateValue('RC_SIDEBAR_BLOCK_LINK', (string)(Tools::getValue("sidebar_block_link")));
            Configuration::updateValue('RC_SIDEBAR_BLOCK_LINK_HOVER', (string)(Tools::getValue("sidebar_block_link_hover")));
            Configuration::updateValue('RC_SIDEBAR_ITEM_SEPARATOR', (string)(Tools::getValue("sidebar_item_separator")));
            Configuration::updateValue('RC_PL_FILTER_T', (string)(Tools::getValue("pl_filter_t")));

            Configuration::updateValue('RC_SIDEBAR_C', (string)(Tools::getValue("sidebar_c")));
            Configuration::updateValue('RC_SIDEBAR_HC', (string)(Tools::getValue("sidebar_hc")));
            Configuration::updateValue('RC_SIDEBAR_BUTTON_BG', (string)(Tools::getValue("sidebar_button_bg")));
            Configuration::updateValue('RC_SIDEBAR_BUTTON_BORDER', (string)(Tools::getValue("sidebar_button_border")));
            Configuration::updateValue('RC_SIDEBAR_BUTTON_COLOR', (string)(Tools::getValue("sidebar_button_color")));
            Configuration::updateValue('RC_SIDEBAR_BUTTON_HBG', (string)(Tools::getValue("sidebar_button_hbg")));
            Configuration::updateValue('RC_SIDEBAR_BUTTON_HBORDER', (string)(Tools::getValue("sidebar_button_hborder")));
            Configuration::updateValue('RC_SIDEBAR_BUTTON_HCOLOR', (string)(Tools::getValue("sidebar_button_hcolor")));
            Configuration::updateValue('RC_SIDEBAR_PRODUCT_PRICE', (string)(Tools::getValue("sidebar_product_price")));
            Configuration::updateValue('RC_SIDEBAR_PRODUCT_OPRICE', (string)(Tools::getValue("sidebar_product_oprice")));

            // Product list

            Configuration::updateValue('NC_PRODUCT_SWITCH', (string)(Tools::getValue("nc_product_switch")));
            Configuration::updateValue('NC_SUBCAT_S', (string)(Tools::getValue("nc_subcat")));
            Configuration::updateValue('NC_CAT_S', (string)(Tools::getValue("nc_cat")));
            Configuration::updateValue('RC_PL_NAV_GRID', (string)(Tools::getValue("pl_nav_grid")));
            Configuration::updateValue('RC_PL_NUMBER_COLOR', (string)(Tools::getValue("pl_number_color")));
            Configuration::updateValue('RC_PL_NUMBER_COLOR_HOVER', (string)(Tools::getValue("pl_number_color_hover")));

            Configuration::updateValue('NC_PC_LAYOUTS', (string)(Tools::getValue("nc_pc_layout")));
            Configuration::updateValue('RC_PL_ITEM_BG', (string)(Tools::getValue("pl_item_bg")));
            Configuration::updateValue('RC_PL_ITEM_BORDER', (string)(Tools::getValue("pl_item_border")));
            Configuration::updateValue('NC_PL_ITEM_BORDERH', (string)(Tools::getValue("nc_pl_item_borderh")));
            Configuration::updateValue('RC_PL_PRODUCT_NAME', (string)(Tools::getValue("pl_product_name")));
            Configuration::updateValue('RC_PL_PRODUCT_PRICE', (string)(Tools::getValue("pl_product_price")));
            Configuration::updateValue('RC_PL_PRODUCT_OLDPRICE', (string)(Tools::getValue("pl_product_oldprice")));
            Configuration::updateValue('RC_PL_LIST_DESCRIPTION', (string)(Tools::getValue("pl_list_description")));
            Configuration::updateValue('NC_PL_SHADOWS', (int)(Tools::getValue("nc_pl_shadow")));
            Configuration::updateValue('NC_SHOW_QW', (int)(Tools::getValue("nc_show_q")));
            Configuration::updateValue('NC_SHOW_SW', (int)(Tools::getValue("nc_show_s")));
            Configuration::updateValue('RC_PL_HOVER_BUT', (string)(Tools::getValue("pl_hover_but")));
            Configuration::updateValue('RC_PL_HOVER_BUT_BG', (string)(Tools::getValue("pl_hover_but_bg")));
            Configuration::updateValue('RC_PL_PRODUCT_NEW_BG', (string)(Tools::getValue("pl_product_new_bg")));
            Configuration::updateValue('RC_PL_PRODUCT_NEW_BORDER', (string)(Tools::getValue("pl_product_new_border")));
            Configuration::updateValue('RC_PL_PRODUCT_NEW_COLOR', (string)(Tools::getValue("pl_product_new_color")));
            Configuration::updateValue('RC_PL_PRODUCT_SALE_BG', (string)(Tools::getValue("pl_product_sale_bg")));
            Configuration::updateValue('RC_PL_PRODUCT_SALE_BORDER', (string)(Tools::getValue("pl_product_sale_border")));
            Configuration::updateValue('RC_PL_PRODUCT_SALE_COLOR', (string)(Tools::getValue("pl_product_sale_color")));
            Configuration::updateValue('NC_SECOND_IMG_S', (string)(Tools::getValue("nc_second_img")));
            Configuration::updateValue('NC_COLORS_S', (string)(Tools::getValue("nc_colors")));

            Configuration::updateValue('RC_PP_REVIEWS_STARON', (string)(Tools::getValue("pp_reviews_staron")));
            Configuration::updateValue('RC_PP_REVIEWS_STAROFF', (string)(Tools::getValue("pp_reviews_staroff")));

            Configuration::updateValue('NC_COUNT_DAYS', (string)(Tools::getValue("nc_count_days")));
            Configuration::updateValue('NC_COUNT_BG', (string)(Tools::getValue("nc_count_bg")));
            Configuration::updateValue('NC_COUNT_COLOR', (string)(Tools::getValue("nc_count_color")));
            Configuration::updateValue('NC_COUNT_TIME', (string)(Tools::getValue("nc_count_time")));
            Configuration::updateValue('NC_COUNT_WATCH', (string)(Tools::getValue("nc_count_watch")));
            Configuration::updateValue('NC_COUNT_WATCH_BG', (string)(Tools::getValue("nc_count_watch_bg")));

            Configuration::updateValue('NC_I_QVS', (string)(Tools::getValue("nc_i_qv")));
            Configuration::updateValue('NC_I_DISCOVERS', (string)(Tools::getValue("nc_i_discover")));
            Configuration::updateValue('NC_AIS', (string)(Tools::getValue("nc_ai")));

            //  Product page
            Configuration::updateValue('RC_PP_IMGB', (string)(Tools::getValue("pp_imgb")));
            Configuration::updateValue('RC_PP_IMG_BORDER', (string)(Tools::getValue("pp_img_border")));
            Configuration::updateValue('RC_PP_ICON_BORDER', (string)(Tools::getValue("pp_icon_border")));
            Configuration::updateValue('RC_PP_ICON_BORDER_HOVER', (string)(Tools::getValue("pp_icon_border_hover")));
            Configuration::updateValue('NC_PP_QQ3S', (string)(Tools::getValue("nc_pp_qq3")));

            Configuration::updateValue('RC_PP_Z', (string)(Tools::getValue("pp_z")));
            Configuration::updateValue('RC_PP_ZI', (string)(Tools::getValue("pp_zi")));
            Configuration::updateValue('RC_PP_ZIHBG', (string)(Tools::getValue("pp_zihbg")));
            Configuration::updateValue('NC_STICKY_ADDS', (string)(Tools::getValue("nc_sticky_add")));
            Configuration::updateValue('NC_MOBADOTSS', (string)(Tools::getValue("nc_mobadots")));
            Configuration::updateValue('NC_MOBADOTSCS', (string)(Tools::getValue("nc_mobadotsc")));
            Configuration::updateValue('NC_ATT_RADIOS', (string)(Tools::getValue("nc_att_radio")));
            Configuration::updateValue('NC_OLDPRICE', (string)(Tools::getValue("nc_oldprice")));
            Configuration::updateValue('RC_PP_ATT_LABEL', (string)(Tools::getValue("pp_att_label")));
            Configuration::updateValue('RC_PP_ATT_COLOR_ACTIVE', (string)(Tools::getValue("pp_att_color_active")));

            Configuration::updateValue('RC_PP_PRICE_COLOR', (string)(Tools::getValue("pp_price_color")));
            Configuration::updateValue('RC_PP_PRICE_COLORO', (string)(Tools::getValue("pp_price_coloro")));
            Configuration::updateValue('NC_PP_ADD_BG', (string)(Tools::getValue("nc_pp_add_bg")));
            Configuration::updateValue('NC_PP_ADD_BORDER', (string)(Tools::getValue("nc_pp_add_border")));
            Configuration::updateValue('NC_PP_ADD_COLOR', (string)(Tools::getValue("nc_pp_add_color")));

            Configuration::updateValue('NC_COUNT_PR_TITLE', (string)(Tools::getValue("nc_count_pr_title")));
            Configuration::updateValue('NC_COUNT_PR_BG', (string)(Tools::getValue("nc_count_pr_bg")));
            Configuration::updateValue('NC_COUNT_PR_SEP', (string)(Tools::getValue("nc_count_pr_sep")));
            Configuration::updateValue('NC_COUNT_PR_NUMBERS', (string)(Tools::getValue("nc_count_pr_numbers")));
            Configuration::updateValue('NC_COUNT_PR_COLOR', (string)(Tools::getValue("nc_count_pr_color")));

            Configuration::updateValue('RC_PP_INFO_LABEL', (string)(Tools::getValue("pp_info_label")));
            Configuration::updateValue('RC_PP_INFO_VALUE', (string)(Tools::getValue("pp_info_value")));
            Configuration::updateValue('RC_PP_DISPLAY_Q', (string)(Tools::getValue("pp_display_q")));
            Configuration::updateValue('RC_PP_DISPLAY_REFER', (string)(Tools::getValue("pp_display_refer")));
            Configuration::updateValue('RC_PP_DISPLAY_COND', (string)(Tools::getValue("pp_display_cond")));
            Configuration::updateValue('RC_PP_DISPLAY_BRAND', (string)(Tools::getValue("pp_display_brand")));

            // Cart and order
            Configuration::updateValue('RC_O_ADDS', (string)(Tools::getValue("o_add")));
            Configuration::updateValue('RC_O_OPTION', (string)(Tools::getValue("o_option")));
            Configuration::updateValue('RC_O_OPTION_ACTIVE', (string)(Tools::getValue("o_option_active")));
            Configuration::updateValue('RC_O_INFO_TEXT', (string)(Tools::getValue("o_info_text")));

            Configuration::updateValue('RC_LC_BG', (string)(Tools::getValue("lc_bg")));
            Configuration::updateValue('RC_LC_C', (string)(Tools::getValue("lc_c")));

            // blog
            Configuration::updateValue('RC_BL_LAY', (string)(Tools::getValue("bl_lay")));
            Configuration::updateValue('RC_BL_CONT', (string)(Tools::getValue("bl_cont")));
            Configuration::updateValue('RC_BL_ROW', (string)(Tools::getValue("bl_row")));
            Configuration::updateValue('RC_BL_HEAD', (string)(Tools::getValue("bl_head")));
            Configuration::updateValue('RC_BL_HEAD_HOVER', (string)(Tools::getValue("bl_head_hover")));
            Configuration::updateValue('RC_BL_H_TITLE', (string)(Tools::getValue("bl_h_title")));
            Configuration::updateValue('RC_BL_H_TITLE_H', (string)(Tools::getValue("bl_h_title_h")));
            Configuration::updateValue('RC_BL_H_META', (string)(Tools::getValue("bl_h_meta")));
            Configuration::updateValue('RC_BL_H_BG', (string)(Tools::getValue("bl_h_bg")));
            Configuration::updateValue('RC_BL_H_BORDER', (string)(Tools::getValue("bl_h_border")));
            Configuration::updateValue('RC_BL_C_ROW', (string)(Tools::getValue("bl_c_row")));
            Configuration::updateValue('RC_BL_DESC', (string)(Tools::getValue("bl_desc")));
            Configuration::updateValue('RC_BL_RM_COLOR', (string)(Tools::getValue("bl_rm_color")));
            Configuration::updateValue('RC_BL_RM_HOVER', (string)(Tools::getValue("bl_rm_hover")));

            // footer
            Configuration::updateValue('RC_FOOTER_LAY', (string)(Tools::getValue("footer_lay")));
            Configuration::updateValue('NC_LOGO_FOOTER', (string)(Tools::getValue("nc_logo_footer")));
            Configuration::updateValue('RC_FOOTER_BG', (string)(Tools::getValue("footer_bg")));
            Configuration::updateValue('RC_FOOTER_TITLES', (string)(Tools::getValue("footer_titles")));
            Configuration::updateValue('RC_FOOTER_TEXT', (string)(Tools::getValue("footer_text")));
            Configuration::updateValue('RC_FOOTER_LINK', (string)(Tools::getValue("footer_link")));
            Configuration::updateValue('RC_FOOTER_LINK_H', (string)(Tools::getValue("footer_link_h")));
            Configuration::updateValue('RC_FOOTER_NEWS_BG', (string)(Tools::getValue("footer_news_bg")));
            Configuration::updateValue('RC_FOOTER_NEWS_BORDER', (string)(Tools::getValue("footer_news_border")));
            Configuration::updateValue('RC_FOOTER_NEWS_PLACEH', (string)(Tools::getValue("footer_news_placeh")));
            Configuration::updateValue('RC_FOOTER_NEWS_COLOR', (string)(Tools::getValue("footer_news_color")));
            Configuration::updateValue('RC_FOOTER_NEWS_BUTTON', (string)(Tools::getValue("footer_news_button")));

            // Side and Mobile
            Configuration::updateValue('RC_LEVI_POSITION', (string)(Tools::getValue("levi_position")));
            Configuration::updateValue('NC_LEVI_BG', (string)(Tools::getValue("nc_levi_bg")));
            Configuration::updateValue('NC_LEVI_BORDER', (string)(Tools::getValue("nc_levi_border")));
            Configuration::updateValue('NC_LEVI_I', (string)(Tools::getValue("nc_levi_i")));
            Configuration::updateValue('NC_LEVI_I_HOVER', (string)(Tools::getValue("nc_levi_i_hover")));
            Configuration::updateValue('NC_LEVI_CART', (string)(Tools::getValue("nc_levi_cart")));
            Configuration::updateValue('NC_LEVI_CART_A', (string)(Tools::getValue("nc_levi_cart_a")));
            Configuration::updateValue('NC_LEVI_CLOSE', (string)(Tools::getValue("nc_levi_close")));
            Configuration::updateValue('NC_LEVI_CLOSE_I', (string)(Tools::getValue("nc_levi_close_i")));
            Configuration::updateValue('NC_SIDE_BG', (string)(Tools::getValue("nc_side_bg")));
            Configuration::updateValue('NC_SIDE_TITLE', (string)(Tools::getValue("nc_side_title")));
            Configuration::updateValue('NC_SIDE_TEXT', (string)(Tools::getValue("nc_side_text")));
            Configuration::updateValue('NC_SIDE_LIGHT', (string)(Tools::getValue("nc_side_light")));
            Configuration::updateValue('NC_SIDE_SEP', (string)(Tools::getValue("nc_side_sep")));

            Configuration::updateValue('NC_LOGO_MOBILE', (string)(Tools::getValue("nc_logo_mobile")));
            Configuration::updateValue('NC_MOB_HEADER', (string)(Tools::getValue("nc_mob_header")));
            Configuration::updateValue('NC_MOB_MENU', (string)(Tools::getValue("nc_mob_menu")));
            Configuration::updateValue('NC_MOB_HP', (string)(Tools::getValue("nc_mob_hp")));
            Configuration::updateValue('NC_MOB_CAT', (string)(Tools::getValue("nc_mob_cat")));
            Configuration::updateValue('NC_HEMOS', (string)(Tools::getValue("nc_hemo")));

            // typography
            Configuration::updateValue('RC_F_HEADINGS', (string)(Tools::getValue("f_headings")));
            Configuration::updateValue('RC_F_BUTTONS', (string)(Tools::getValue("f_buttons")));
            Configuration::updateValue('RC_F_TEXT', (string)(Tools::getValue("f_text")));
            Configuration::updateValue('RC_F_PRICE', (string)(Tools::getValue("f_price")));
            Configuration::updateValue('RC_F_PN', (string)(Tools::getValue("f_pn")));
            Configuration::updateValue('RC_LATIN_EXT', (int)(Tools::getValue("latin_ext")));
            Configuration::updateValue('RC_CYRILLIC', (int)(Tools::getValue("cyrillic")));
            Configuration::updateValue('RC_FONT_SIZE_PP', (string)(Tools::getValue("font_size_pp")));
            Configuration::updateValue('RC_FONT_SIZE_BODY', (string)(Tools::getValue("font_size_body")));
            Configuration::updateValue('RC_FONT_SIZE_HEAD', (string)(Tools::getValue("font_size_head")));
            Configuration::updateValue('RC_FONT_SIZE_BUTTONS', (string)(Tools::getValue("font_size_buttons")));
            Configuration::updateValue('RC_FONT_SIZE_PRICE', (string)(Tools::getValue("font_size_price")));
            Configuration::updateValue('RC_FONT_SIZE_PROD', (string)(Tools::getValue("font_size_prod")));
            Configuration::updateValue('RC_FONT_SIZE_PN', (string)(Tools::getValue("font_size_pn")));
            Configuration::updateValue('NC_UP_HP', (string)(Tools::getValue("nc_up_hp")));
            Configuration::updateValue('NC_UP_NC', (string)(Tools::getValue("nc_up_nc")));
            Configuration::updateValue('NC_UP_NP', (string)(Tools::getValue("nc_up_np")));
            Configuration::updateValue('NC_UP_F', (string)(Tools::getValue("nc_up_f")));
            Configuration::updateValue('NC_UP_BP', (string)(Tools::getValue("nc_up_bp")));
            Configuration::updateValue('NC_UP_MI', (string)(Tools::getValue("nc_up_mi")));
            Configuration::updateValue('NC_UP_MENU', (string)(Tools::getValue("nc_up_menu")));
            Configuration::updateValue('NC_UP_HEAD', (string)(Tools::getValue("nc_up_head")));
            Configuration::updateValue('NC_UP_BUT', (string)(Tools::getValue("nc_up_but")));
            Configuration::updateValue('NC_FW_MENU', (string)(Tools::getValue("nc_fw_menu")));
            Configuration::updateValue('NC_FW_HEADING', (string)(Tools::getValue("nc_fw_heading")));
            Configuration::updateValue('NC_FW_BUT', (string)(Tools::getValue("nc_fw_but")));
            Configuration::updateValue('NC_FW_PN', (string)(Tools::getValue("nc_fw_pn")));
            Configuration::updateValue('NC_FW_CT', (string)(Tools::getValue("nc_fw_ct")));
            Configuration::updateValue('NC_FW_PRICE', (string)(Tools::getValue("nc_fw_price")));
            Configuration::updateValue('NC_ITAL_PN', (string)(Tools::getValue("nc_ital_pn")));
            Configuration::updateValue('NC_ITALIC_PP', (string)(Tools::getValue("nc_italic_pp")));
            Configuration::updateValue('NC_LS', (string)(Tools::getValue("nc_ls")));
            Configuration::updateValue('NC_LS_H', (string)(Tools::getValue("nc_ls_h")));
            Configuration::updateValue('NC_LS_M', (string)(Tools::getValue("nc_ls_m")));
            Configuration::updateValue('NC_LS_P', (string)(Tools::getValue("nc_ls_p")));
            Configuration::updateValue('NC_LS_T', (string)(Tools::getValue("nc_ls_t")));
            Configuration::updateValue('NC_LS_B', (string)(Tools::getValue("nc_ls_b")));

            // Custom CSS
            Configuration::updateValue('NC_CSS', (string)(Tools::getValue("nc_css")));


            $this->generateCss();
        }

        $this->updateOriginalValues();

        if (Tools::isSubmit('reset_changes')) {

            Configuration::updateValue('RC_G_LAY', $this->defaults["g_lay"]);
            Configuration::updateValue('RC_G_TP', $this->defaults["g_tp"]);
            Configuration::updateValue('RC_G_BP', $this->defaults["g_bp"]);
            Configuration::updateValue('RC_BODY_BOX_SW', $this->defaults["body_box_sw"]);
            Configuration::updateValue('RC_MAIN_BACKGROUND_COLOR', $this->defaults["main_background_color"]);
            Configuration::updateValue('NC_BODY_GS', $this->defaults["nc_body_gs"]);
            Configuration::updateValue('NC_BODY_GE', $this->defaults["nc_body_ge"]);
            Configuration::updateValue('NC_BODY_GG', $this->defaults["nc_body_gg"]);
            Configuration::updateValue('NC_BODY_IM_BG_EXT', $this->defaults["nc_body_im_bg_ext"]);
            Configuration::updateValue('NC_BODY_IM_BG_REPEAT', $this->defaults["nc_body_im_bg_repeat"]);
            Configuration::updateValue('NC_BODY_IM_BG_POSITION', $this->defaults["nc_body_im_bg_position"]);
            Configuration::updateValue('NC_BODY_IM_BG_FIXED', $this->defaults["nc_body_im_bg_fixed"]);
            Configuration::updateValue('RC_GRADIENT_SCHEME', $this->defaults["gradient_scheme"]);
            Configuration::updateValue('RC_DISPLAY_GRADIENT', $this->defaults["display_gradient"]);
            Configuration::updateValue('RC_BODY_BG_PATTERN', $this->defaults["body_bg_pattern"]);
            Configuration::updateValue('NC_MAIN_BGS', $this->defaults["nc_main_bg"]);
            Configuration::updateValue('NC_MAIN_BC', $this->defaults["nc_main_bc"]);
            Configuration::updateValue('NC_MAIN_GS', $this->defaults["nc_main_gs"]);
            Configuration::updateValue('NC_MAIN_GE', $this->defaults["nc_main_ge"]);
            Configuration::updateValue('NC_MAIN_GG', $this->defaults["nc_main_gg"]);
            Configuration::updateValue('NC_MAIN_IM_BG_REPEAT', $this->defaults["nc_main_im_bg_repeat"]);
            Configuration::updateValue('NC_MAIN_IM_BG_POSITION', $this->defaults["nc_main_im_bg_position"]);
            Configuration::updateValue('NC_MAIN_IM_BG_FIXED', $this->defaults["nc_main_im_bg_fixed"]);

            // header
            Configuration::updateValue('RC_HEADER_LAY', $this->defaults["header_lay"]);
            Configuration::updateValue('NC_LOGO_NORMAL', $this->defaults["nc_logo_normal"]);
            Configuration::updateValue('NC_HEADER_SHADOWS', $this->defaults["nc_header_shadow"]);
            Configuration::updateValue('NC_HEADER_BGS', $this->defaults["nc_header_bg"]);
            Configuration::updateValue('NC_HEADER_BC', $this->defaults["nc_header_bc"]);
            Configuration::updateValue('NC_HEADER_GS', $this->defaults["nc_header_gs"]);
            Configuration::updateValue('NC_HEADER_GE', $this->defaults["nc_header_ge"]);
            Configuration::updateValue('NC_HEADER_GG', $this->defaults["nc_header_gg"]);
            Configuration::updateValue('NC_HEADER_IM_BG_EXT', $this->defaults["nc_header_im_bg_ext"]);
            Configuration::updateValue('NC_HEADER_IM_BG_REPEAT', $this->defaults["nc_header_im_bg_repeat"]);
            Configuration::updateValue('NC_HEADER_IM_BG_POSITION', $this->defaults["nc_header_im_bg_position"]);
            Configuration::updateValue('NC_HEADER_IM_BG_FIXED', $this->defaults["nc_header_im_bg_fixed"]);
            Configuration::updateValue('NC_HEADER_ST_BGCOLOR', $this->defaults["nc_header_st_bg"]);
            Configuration::updateValue('NC_HEADER_ST_BGCOLORHOVER', $this->defaults["nc_header_st_bgh"]);
            Configuration::updateValue('NC_HEADER_ST_LINKCOLOR', $this->defaults["nc_header_st_link"]);
            Configuration::updateValue('NC_HEADER_ST_LINKCOLORHOVER', $this->defaults["nc_header_st_linkh"]);
            Configuration::updateValue('RC_HEADER_NBG', $this->defaults["header_nbg"]);
            Configuration::updateValue('RC_HEADER_NB', $this->defaults["header_nb"]);
            Configuration::updateValue('RC_HEADER_NT', $this->defaults["header_nt"]);
            Configuration::updateValue('RC_HEADER_NL', $this->defaults["header_nl"]);
            Configuration::updateValue('RC_HEADER_NLH', $this->defaults["header_nlh"]);
            Configuration::updateValue('RC_HEADER_NS', $this->defaults["header_ns"]);
            Configuration::updateValue('NC_M_ALIGN_S', $this->defaults["nc_m_align"]);
            Configuration::updateValue('NC_M_LAYOUT_S', $this->defaults["nc_m_layout"]);
            Configuration::updateValue('NC_M_UNDER_S', $this->defaults["nc_m_under"]);
            Configuration::updateValue('NC_M_UNDER_COLOR', $this->defaults["nc_m_under_color"]);
            Configuration::updateValue('NC_M_OVERRIDE_S', $this->defaults["nc_m_override"]);
            Configuration::updateValue('RC_M_BG', $this->defaults["m_bg"]);
            Configuration::updateValue('RC_M_LINK_BG_HOVER', $this->defaults["m_link_bg_hover"]);
            Configuration::updateValue('RC_M_LINK', $this->defaults["m_link"]);
            Configuration::updateValue('RC_M_LINK_HOVER', $this->defaults["m_link_hover"]);
            Configuration::updateValue('RC_M_POPUP_LLINK', $this->defaults["m_popup_llink"]);
            Configuration::updateValue('RC_M_POPUP_LLINK_HOVER', $this->defaults["m_popup_llink_hover"]);
            Configuration::updateValue('RC_M_POPUP_LBG', $this->defaults["m_popup_lbg"]);
            Configuration::updateValue('RC_M_POPUP_LCHEVRON', $this->defaults["m_popup_lchevron"]);
            Configuration::updateValue('RC_M_POPUP_LBORDER', $this->defaults["m_popup_lborder"]);
            Configuration::updateValue('NC_M_BR_S', $this->defaults["nc_m_br"]);
            Configuration::updateValue('RC_SEARCH_LAY', $this->defaults["search_lay"]);
            Configuration::updateValue('NC_I_SEARCHS', $this->defaults["nc_i_search"]);
            Configuration::updateValue('RC_SEARCH_BG', $this->defaults["search_bg"]);
            Configuration::updateValue('RC_SEARCH_LINE', $this->defaults["search_line"]);
            Configuration::updateValue('RC_SEARCH_INPUT', $this->defaults["search_input"]);
            Configuration::updateValue('RC_SEARCH_T', $this->defaults["search_t"]);
            Configuration::updateValue('RC_SEARCH_ICON', $this->defaults["search_icon"]);
            Configuration::updateValue('RC_SEARCH_BG_HOVER', $this->defaults["search_bg_hover"]);
            Configuration::updateValue('RC_SEARCH_LINEH', $this->defaults["search_lineh"]);
            Configuration::updateValue('RC_SEARCH_INPUTH', $this->defaults["search_inputh"]);
            Configuration::updateValue('RC_SEARCH_T_HOVER', $this->defaults["search_t_hover"]);
            Configuration::updateValue('RC_SEARCH_ICONH', $this->defaults["search_iconh"]);
            Configuration::updateValue('RC_CART_LAY', $this->defaults["cart_lay"]);
            Configuration::updateValue('RC_CART_ICON', $this->defaults["cart_icon"]);
            Configuration::updateValue('RC_CART_BG', $this->defaults["cart_bg"]);
            Configuration::updateValue('RC_CART_B', $this->defaults["cart_b"]);
            Configuration::updateValue('RC_CART_I', $this->defaults["cart_i"]);
            Configuration::updateValue('RC_CART_T', $this->defaults["cart_t"]);
            Configuration::updateValue('RC_CART_Q', $this->defaults["cart_q"]);
            Configuration::updateValue('RC_CART_BG_HOVER', $this->defaults["cart_bg_hover"]);
            Configuration::updateValue('RC_CART_B_HOVER', $this->defaults["cart_b_hover"]);
            Configuration::updateValue('RC_CART_I_HOVER', $this->defaults["cart_i_hover"]);
            Configuration::updateValue('RC_CART_T_HOVER', $this->defaults["cart_t_hover"]);
            Configuration::updateValue('RC_CART_Q_HOVER', $this->defaults["cart_q_hover"]);

            // body design
            Configuration::updateValue('RC_G_BG_CONTENT', $this->defaults["g_bg_content"]);
            Configuration::updateValue('RC_G_BORDER', $this->defaults["g_border"]);
            Configuration::updateValue('RC_G_BODY_TEXT', $this->defaults["g_body_text"]);
            Configuration::updateValue('RC_G_BODY_COMMENT', $this->defaults["g_body_comment"]);
            Configuration::updateValue('RC_G_BODY_LINK', $this->defaults["g_body_link"]);
            Configuration::updateValue('RC_G_BODY_LINK_HOVER', $this->defaults["g_body_link_hover"]);
            Configuration::updateValue('RC_LABEL', $this->defaults["g_label"]);
            Configuration::updateValue('RC_G_HEADER', $this->defaults["g_header"]);
            Configuration::updateValue('RC_HEADER_UNDER', $this->defaults["g_header_under"]);
            Configuration::updateValue('RC_HEADER_DECOR', $this->defaults["g_header_decor"]);
            Configuration::updateValue('RC_G_CC', $this->defaults["g_cc"]);
            Configuration::updateValue('RC_G_CH', $this->defaults["g_ch"]);
            Configuration::updateValue('RC_G_HB', $this->defaults["g_hb"]);
            Configuration::updateValue('RC_G_HC', $this->defaults["g_hc"]);
            Configuration::updateValue('RC_G_BG_EVEN', $this->defaults["g_bg_even"]);
            Configuration::updateValue('RC_G_COLOR_EVEN', $this->defaults["g_color_even"]);
            Configuration::updateValue('RC_G_ACC_ICON', $this->defaults["g_acc_icon"]);
            Configuration::updateValue('RC_G_ACC_TITLE', $this->defaults["g_acc_title"]);
            Configuration::updateValue('RC_FANCY_NBG', $this->defaults["g_fancy_nbg"]);
            Configuration::updateValue('RC_FANCY_NC', $this->defaults["g_fancy_nc"]);

            Configuration::updateValue('RC_B_NORMAL_BG', $this->defaults["b_normal_bg"]);
            Configuration::updateValue('RC_B_NORMAL_BORDER', $this->defaults["b_normal_border"]);
            Configuration::updateValue('RC_B_NORMAL_BORDER_HOVER', $this->defaults["b_normal_border_hover"]);
            Configuration::updateValue('RC_B_NORMAL_BG_HOVER', $this->defaults["b_normal_bg_hover"]);
            Configuration::updateValue('RC_B_NORMAL_COLOR', $this->defaults["b_normal_color"]);
            Configuration::updateValue('RC_B_NORMAL_COLOR_HOVER', $this->defaults["b_normal_color_hover"]);
            Configuration::updateValue('RC_B_EX_BG', $this->defaults["b_ex_bg"]);
            Configuration::updateValue('RC_B_EX_BORDER', $this->defaults["b_ex_border"]);
            Configuration::updateValue('RC_B_EX_COLOR', $this->defaults["b_ex_color"]);
            Configuration::updateValue('NC_B_RADIUS', $this->defaults["nc_b_radius"]);
            Configuration::updateValue('NC_B_SHS', $this->defaults["nc_b_sh"]);

            Configuration::updateValue('RC_I_BG', $this->defaults["i_bg"]);
            Configuration::updateValue('RC_I_B_COLOR', $this->defaults["i_b_color"]);
            Configuration::updateValue('RC_I_COLOR', $this->defaults["i_color"]);
            Configuration::updateValue('RC_I_BG_FOCUS', $this->defaults["i_bg_focus"]);
            Configuration::updateValue('RC_I_COLOR_FOCUS', $this->defaults["i_color_focus"]);
            Configuration::updateValue('RC_I_B_FOCUS', $this->defaults["i_b_focus"]);
            Configuration::updateValue('RC_I_B_RADIUS', $this->defaults["i_b_radius"]);
            Configuration::updateValue('RC_I_PH', $this->defaults["i_ph"]);
            Configuration::updateValue('RC_RC_BG_ACTIVE', $this->defaults["rc_bg_active"]);

            Configuration::updateValue('NC_LOADERS', $this->defaults["nc_loader"]);
            Configuration::updateValue('NC_LOADER_LAYS', $this->defaults["nc_loader_lay"]);
            Configuration::updateValue('NC_LOADER_BG', $this->defaults["nc_loader_bg"]);
            Configuration::updateValue('NC_LOADER_COLOR', $this->defaults["nc_loader_color"]);
            Configuration::updateValue('NC_LOADER_COLOR2', $this->defaults["nc_loader_color2"]);
            Configuration::updateValue('NC_LOADER_LOGOS', $this->defaults["nc_loader_logo"]);
            Configuration::updateValue('NC_LOGO_LOADER', $this->defaults["nc_logo_loader"]);

            // Homepage content
            Configuration::updateValue('RC_BAN_SPA_BEHEAD', $this->defaults["ban_spa_behead"]);
            Configuration::updateValue('RC_BAN_TS_BEHEAD', $this->defaults["ban_ts_behead"]);
            Configuration::updateValue('RC_BAN_BS_BEHEAD', $this->defaults["ban_bs_behead"]);
            Configuration::updateValue('RC_BAN_SPA_TOP', $this->defaults["ban_spa_top"]);
            Configuration::updateValue('RC_BAN_TS_TOP', $this->defaults["ban_ts_top"]);
            Configuration::updateValue('RC_BAN_BS_TOP', $this->defaults["ban_bs_top"]);
            Configuration::updateValue('RC_BAN_TS_LEFT', $this->defaults["ban_ts_left"]);
            Configuration::updateValue('RC_BAN_BS_LEFT', $this->defaults["ban_bs_left"]);
            Configuration::updateValue('RC_BAN_TS_RIGHT', $this->defaults["ban_ts_right"]);
            Configuration::updateValue('RC_BAN_BS_RIGHT', $this->defaults["ban_bs_right"]);
            Configuration::updateValue('RC_BAN_SPA_PRO', $this->defaults["ban_spa_pro"]);
            Configuration::updateValue('RC_BAN_TS_PRO', $this->defaults["ban_ts_pro"]);
            Configuration::updateValue('RC_BAN_BS_PRO', $this->defaults["ban_bs_pro"]);
            Configuration::updateValue('RC_BAN_SPA_BEFOOT', $this->defaults["ban_spa_befoot"]);
            Configuration::updateValue('RC_BAN_TS_BEFOOT', $this->defaults["ban_ts_befoot"]);
            Configuration::updateValue('RC_BAN_BS_BEFOOT', $this->defaults["ban_bs_befoot"]);
            Configuration::updateValue('RC_BAN_SPA_FOOT', $this->defaults["ban_spa_foot"]);
            Configuration::updateValue('RC_BAN_TS_FOOT', $this->defaults["ban_ts_foot"]);
            Configuration::updateValue('RC_BAN_BS_FOOT', $this->defaults["ban_bs_foot"]);
            Configuration::updateValue('RC_BAN_SPA_SIDECART', $this->defaults["ban_spa_sidecart"]);
            Configuration::updateValue('RC_BAN_TS_SIDECART', $this->defaults["ban_ts_sidecart"]);
            Configuration::updateValue('RC_BAN_BS_SIDECART', $this->defaults["ban_bs_sidecart"]);
            Configuration::updateValue('RC_BAN_SPA_SIDESEARCH', $this->defaults["ban_spa_sidesearch"]);
            Configuration::updateValue('RC_BAN_TS_SIDESEARCH', $this->defaults["ban_ts_sidesearch"]);
            Configuration::updateValue('RC_BAN_BS_SIDESEARCH', $this->defaults["ban_bs_sidesearch"]);
            Configuration::updateValue('RC_BAN_SPA_SIDEMAIL', $this->defaults["ban_spa_sidemail"]);
            Configuration::updateValue('RC_BAN_TS_SIDEMAIL', $this->defaults["ban_ts_sidemail"]);
            Configuration::updateValue('RC_BAN_BS_SIDEMAIL', $this->defaults["ban_bs_sidemail"]);
            Configuration::updateValue('RC_BAN_SPA_SIDEMOBILEMENU', $this->defaults["ban_spa_sidemobilemenu"]);
            Configuration::updateValue('RC_BAN_TS_SIDEMOBILEMENU', $this->defaults["ban_ts_sidemobilemenu"]);
            Configuration::updateValue('RC_BAN_BS_SIDEMOBILEMENU', $this->defaults["ban_bs_sidemobilemenu"]);
            Configuration::updateValue('RC_BAN_SPA_PRODUCT', $this->defaults["ban_spa_product"]);
            Configuration::updateValue('RC_BAN_TS_PRODUCT', $this->defaults["ban_ts_product"]);
            Configuration::updateValue('RC_BAN_BS_PRODUCT', $this->defaults["ban_bs_product"]);

            Configuration::updateValue('NC_CAROUSEL_FEATUREDS', $this->defaults["nc_carousel_featured"]);
            Configuration::updateValue('NC_AUTO_FEATURED', $this->defaults["nc_auto_featured"]);
            Configuration::updateValue('NC_ITEMS_FEATUREDS', $this->defaults["nc_items_featured"]);
            Configuration::updateValue('NC_CAROUSEL_BEST', $this->defaults["nc_carousel_best"]);
            Configuration::updateValue('NC_AUTO_BEST', $this->defaults["nc_auto_best"]);
            Configuration::updateValue('NC_ITEMS_BESTS', $this->defaults["nc_items_best"]);
            Configuration::updateValue('NC_CAROUSEL_NEW', $this->defaults["nc_carousel_new"]);
            Configuration::updateValue('NC_AUTO_NEW', $this->defaults["nc_auto_new"]);
            Configuration::updateValue('NC_ITEMS_NEWS', $this->defaults["nc_items_new"]);
            Configuration::updateValue('NC_CAROUSEL_SALE', $this->defaults["nc_carousel_sale"]);
            Configuration::updateValue('NC_AUTO_SALE', $this->defaults["nc_auto_sale"]);
            Configuration::updateValue('NC_ITEMS_SALES', $this->defaults["nc_items_sale"]);
            Configuration::updateValue('NC_CAROUSEL_CUSTOM1', $this->defaults["nc_carousel_custom1"]);
            Configuration::updateValue('NC_AUTO_CUSTOM1', $this->defaults["nc_auto_custom1"]);
            Configuration::updateValue('NC_ITEMS_CUSTOM1S', $this->defaults["nc_items_custom1"]);
            Configuration::updateValue('NC_CAROUSEL_CUSTOM2', $this->defaults["nc_carousel_custom2"]);
            Configuration::updateValue('NC_AUTO_CUSTOM2', $this->defaults["nc_auto_custom2"]);
            Configuration::updateValue('NC_ITEMS_CUSTOM2S', $this->defaults["nc_items_custom2"]);
            Configuration::updateValue('NC_CAROUSEL_CUSTOM3', $this->defaults["nc_carousel_custom3"]);
            Configuration::updateValue('NC_AUTO_CUSTOM3', $this->defaults["nc_auto_custom3"]);
            Configuration::updateValue('NC_ITEMS_CUSTOM3S', $this->defaults["nc_items_custom3"]);
            Configuration::updateValue('NC_CAROUSEL_CUSTOM4', $this->defaults["nc_carousel_custom4"]);
            Configuration::updateValue('NC_AUTO_CUSTOM4', $this->defaults["nc_auto_custom4"]);
            Configuration::updateValue('NC_ITEMS_CUSTOM4', $this->defaults["nc_items_custom4"]);
            Configuration::updateValue('NC_CAROUSEL_CUSTOM5', $this->defaults["nc_carousel_custom5"]);
            Configuration::updateValue('NC_AUTO_CUSTOM5', $this->defaults["nc_auto_custom5"]);
            Configuration::updateValue('NC_ITEMS_CUSTOM5', $this->defaults["nc_items_custom5"]);

            Configuration::updateValue('RC_BRAND_PER_ROW', $this->defaults["brand_per_row"]);
            Configuration::updateValue('RC_BRAND_NAME', $this->defaults["brand_name"]);
            Configuration::updateValue('RC_BRAND_NAME_HOVER', $this->defaults["brand_name_hover"]);

            // page content
            Configuration::updateValue('RC_B_LAYOUT', $this->defaults["b_layout"]);
            Configuration::updateValue('RC_B_LINK', $this->defaults["b_link"]);
            Configuration::updateValue('RC_B_LINK_HOVER', $this->defaults["b_link_hover"]);
            Configuration::updateValue('RC_B_SEPARATOR', $this->defaults["b_separator"]);
            Configuration::updateValue('RC_PAGE_BQ_Q', $this->defaults["page_bq_q"]);
            Configuration::updateValue('RC_CONTACT_ICON', $this->defaults["contact_icon"]);
            Configuration::updateValue('RC_WARNING_MESSAGE_COLOR', $this->defaults["warning_message_color"]);
            Configuration::updateValue('RC_SUCCESS_MESSAGE_COLOR', $this->defaults["success_message_color"]);
            Configuration::updateValue('RC_DANGER_MESSAGE_COLOR', $this->defaults["danger_message_color"]);

            // Sidebar and filter
            Configuration::updateValue('RC_SIDEBAR_TITLE', $this->defaults["sidebar_title"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_BG', $this->defaults["sidebar_title_bg"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_B', $this->defaults["sidebar_title_b"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_BR', $this->defaults["sidebar_title_br"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_B1', $this->defaults["sidebar_title_b1"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_B2', $this->defaults["sidebar_title_b2"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_B3', $this->defaults["sidebar_title_b3"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_B4', $this->defaults["sidebar_title_b4"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_BORDER', $this->defaults["sidebar_title_border"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_LINK', $this->defaults["sidebar_title_link"]);
            Configuration::updateValue('RC_SIDEBAR_TITLE_LINK_HOVER', $this->defaults["sidebar_title_link_hover"]);
            Configuration::updateValue('RC_SIDEBAR_BLOCK_CONTENT_BG', $this->defaults["sidebar_block_content_bg"]);
            Configuration::updateValue('RC_SIDEBAR_BLOCK_CONTENT_BORDER', $this->defaults["sidebar_block_content_border"]);
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B', $this->defaults["sidebar_content_b"]);
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B1', $this->defaults["sidebar_content_b1"]);
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B2', $this->defaults["sidebar_content_b2"]);
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B3', $this->defaults["sidebar_content_b3"]);
            Configuration::updateValue('RC_SIDEBAR_CONTENT_B4', $this->defaults["sidebar_content_b4"]);
            Configuration::updateValue('RC_SIDEBAR_CONTENT_BR', $this->defaults["sidebar_content_br"]);
            Configuration::updateValue('RC_SIDEBAR_BLOCK_TEXT_COLOR', $this->defaults["sidebar_block_text_color"]);
            Configuration::updateValue('RC_SIDEBAR_BLOCK_LINK', $this->defaults["sidebar_block_link"]);
            Configuration::updateValue('RC_SIDEBAR_BLOCK_LINK_HOVER', $this->defaults["sidebar_block_link_hover"]);
            Configuration::updateValue('RC_SIDEBAR_ITEM_SEPARATOR', $this->defaults["sidebar_item_separator"]);
            Configuration::updateValue('RC_PL_FILTER_T', $this->defaults["pl_filter_t"]);

            Configuration::updateValue('RC_SIDEBAR_C', $this->defaults["sidebar_c"]);
            Configuration::updateValue('RC_SIDEBAR_HC', $this->defaults["sidebar_hc"]);
            Configuration::updateValue('RC_SIDEBAR_BUTTON_BG', $this->defaults["sidebar_button_bg"]);
            Configuration::updateValue('RC_SIDEBAR_BUTTON_BORDER', $this->defaults["sidebar_button_border"]);
            Configuration::updateValue('RC_SIDEBAR_BUTTON_COLOR', $this->defaults["sidebar_button_color"]);
            Configuration::updateValue('RC_SIDEBAR_BUTTON_HBG', $this->defaults["sidebar_button_hbg"]);
            Configuration::updateValue('RC_SIDEBAR_BUTTON_HBORDER', $this->defaults["sidebar_button_hborder"]);
            Configuration::updateValue('RC_SIDEBAR_BUTTON_HCOLOR', $this->defaults["sidebar_button_hcolor"]);
            Configuration::updateValue('RC_SIDEBAR_PRODUCT_PRICE', $this->defaults["sidebar_product_price"]);
            Configuration::updateValue('RC_SIDEBAR_PRODUCT_OPRICE', $this->defaults["sidebar_product_oprice"]);

            // Product list

            Configuration::updateValue('NC_PRODUCT_SWITCH', $this->defaults["nc_product_switch"]);
            Configuration::updateValue('NC_SUBCAT_S', $this->defaults["nc_subcat"]);
            Configuration::updateValue('NC_CAT_S', $this->defaults["nc_cat"]);
            Configuration::updateValue('RC_PL_NAV_GRID', $this->defaults["pl_nav_grid"]);
            Configuration::updateValue('RC_PL_NUMBER_COLOR', $this->defaults["pl_number_color"]);
            Configuration::updateValue('RC_PL_NUMBER_COLOR_HOVER', $this->defaults["pl_number_color_hover"]);

            Configuration::updateValue('NC_PC_LAYOUTS', $this->defaults["nc_pc_layout"]);
            Configuration::updateValue('RC_PL_ITEM_BG', $this->defaults["pl_item_bg"]);
            Configuration::updateValue('RC_PL_ITEM_BORDER', $this->defaults["pl_item_border"]);
            Configuration::updateValue('NC_PL_ITEM_BORDERH', $this->defaults["nc_pl_item_borderh"]);
            Configuration::updateValue('RC_PL_PRODUCT_NAME', $this->defaults["pl_product_name"]);
            Configuration::updateValue('RC_PL_PRODUCT_PRICE', $this->defaults["pl_product_price"]);
            Configuration::updateValue('RC_PL_PRODUCT_OLDPRICE', $this->defaults["pl_product_oldprice"]);
            Configuration::updateValue('RC_PL_LIST_DESCRIPTION', $this->defaults["pl_list_description"]);
            Configuration::updateValue('NC_PL_SHADOWS', $this->defaults["nc_pl_shadow"]);
            Configuration::updateValue('NC_SHOW_QW', $this->defaults["nc_show_q"]);
            Configuration::updateValue('NC_SHOW_SW', $this->defaults["nc_show_s"]);
            Configuration::updateValue('RC_PL_HOVER_BUT', $this->defaults["pl_hover_but"]);
            Configuration::updateValue('RC_PL_HOVER_BUT_BG', $this->defaults["pl_hover_but_bg"]);
            Configuration::updateValue('RC_PL_PRODUCT_NEW_BG', $this->defaults["pl_product_new_bg"]);
            Configuration::updateValue('RC_PL_PRODUCT_NEW_BORDER', $this->defaults["pl_product_new_border"]);
            Configuration::updateValue('RC_PL_PRODUCT_NEW_COLOR', $this->defaults["pl_product_new_color"]);
            Configuration::updateValue('RC_PL_PRODUCT_SALE_BG', $this->defaults["pl_product_sale_bg"]);
            Configuration::updateValue('RC_PL_PRODUCT_SALE_BORDER', $this->defaults["pl_product_sale_border"]);
            Configuration::updateValue('RC_PL_PRODUCT_SALE_COLOR', $this->defaults["pl_product_sale_color"]);
            Configuration::updateValue('NC_SECOND_IMG_S', $this->defaults["nc_second_img"]);
            Configuration::updateValue('NC_COLORS_S', $this->defaults["nc_colors"]);

            Configuration::updateValue('RC_PP_REVIEWS_STARON', $this->defaults["pp_reviews_staron"]);
            Configuration::updateValue('RC_PP_REVIEWS_STAROFF', $this->defaults["pp_reviews_staroff"]);

            Configuration::updateValue('NC_COUNT_DAYS', $this->defaults["nc_count_days"]);
            Configuration::updateValue('NC_COUNT_BG', $this->defaults["nc_count_bg"]);
            Configuration::updateValue('NC_COUNT_COLOR', $this->defaults["nc_count_color"]);
            Configuration::updateValue('NC_COUNT_TIME', $this->defaults["nc_count_time"]);
            Configuration::updateValue('NC_COUNT_WATCH', $this->defaults["nc_count_watch"]);
            Configuration::updateValue('NC_COUNT_WATCH_BG', $this->defaults["nc_count_watch_bg"]);

            Configuration::updateValue('NC_I_QVS', $this->defaults["nc_i_qv"]);
            Configuration::updateValue('NC_I_DISCOVERS', $this->defaults["nc_i_discover"]);
            Configuration::updateValue('NC_AIS', $this->defaults["nc_ai"]);

            //  Product page
            Configuration::updateValue('RC_PP_IMGB', $this->defaults["pp_imgb"]);
            Configuration::updateValue('RC_PP_IMG_BORDER', $this->defaults["pp_img_border"]);
            Configuration::updateValue('RC_PP_ICON_BORDER', $this->defaults["pp_icon_border"]);
            Configuration::updateValue('RC_PP_ICON_BORDER_HOVER', $this->defaults["pp_icon_border_hover"]);
            Configuration::updateValue('NC_PP_QQ3S', $this->defaults["nc_pp_qq3"]);

            Configuration::updateValue('RC_PP_Z', $this->defaults["pp_z"]);
            Configuration::updateValue('RC_PP_ZI', $this->defaults["pp_zi"]);
            Configuration::updateValue('RC_PP_ZIHBG', $this->defaults["pp_zihbg"]);
            Configuration::updateValue('NC_STICKY_ADDS', $this->defaults["nc_sticky_add"]);
            Configuration::updateValue('NC_MOBADOTSS', $this->defaults["nc_mobadots"]);
            Configuration::updateValue('NC_MOBADOTSCS', $this->defaults["nc_mobadotsc"]);
            Configuration::updateValue('NC_ATT_RADIOS', $this->defaults["nc_att_radio"]);
            Configuration::updateValue('NC_OLDPRICE', $this->defaults["nc_oldprice"]);
            Configuration::updateValue('RC_PP_ATT_LABEL', $this->defaults["pp_att_label"]);
            Configuration::updateValue('RC_PP_ATT_COLOR_ACTIVE', $this->defaults["pp_att_color_active"]);

            Configuration::updateValue('RC_PP_PRICE_COLOR', $this->defaults["pp_price_color"]);
            Configuration::updateValue('RC_PP_PRICE_COLORO', $this->defaults["pp_price_coloro"]);
            Configuration::updateValue('NC_PP_ADD_BG', $this->defaults["nc_pp_add_bg"]);
            Configuration::updateValue('NC_PP_ADD_BORDER', $this->defaults["nc_pp_add_border"]);
            Configuration::updateValue('NC_PP_ADD_COLOR', $this->defaults["nc_pp_add_color"]);

            Configuration::updateValue('NC_COUNT_PR_TITLE', $this->defaults["nc_count_pr_title"]);
            Configuration::updateValue('NC_COUNT_PR_BG', $this->defaults["nc_count_pr_bg"]);
            Configuration::updateValue('NC_COUNT_PR_SEP', $this->defaults["nc_count_pr_sep"]);
            Configuration::updateValue('NC_COUNT_PR_NUMBERS', $this->defaults["nc_count_pr_numbers"]);
            Configuration::updateValue('NC_COUNT_PR_COLOR', $this->defaults["nc_count_pr_color"]);

            Configuration::updateValue('RC_PP_INFO_LABEL', $this->defaults["pp_info_label"]);
            Configuration::updateValue('RC_PP_INFO_VALUE', $this->defaults["pp_info_value"]);
            Configuration::updateValue('RC_PP_DISPLAY_Q', $this->defaults["pp_display_q"]);
            Configuration::updateValue('RC_PP_DISPLAY_REFER', $this->defaults["pp_display_refer"]);
            Configuration::updateValue('RC_PP_DISPLAY_COND', $this->defaults["pp_display_cond"]);
            Configuration::updateValue('RC_PP_DISPLAY_BRAND', $this->defaults["pp_display_brand"]);

            // Cart and order
            Configuration::updateValue('RC_O_ADDS', $this->defaults["o_add"]);
            Configuration::updateValue('RC_O_OPTION', $this->defaults["o_option"]);
            Configuration::updateValue('RC_O_OPTION_ACTIVE', $this->defaults["o_option_active"]);
            Configuration::updateValue('RC_O_INFO_TEXT', $this->defaults["o_info_text"]);

            Configuration::updateValue('RC_LC_BG', $this->defaults["lc_bg"]);
            Configuration::updateValue('RC_LC_C', $this->defaults["lc_c"]);

            // blog
            Configuration::updateValue('RC_BL_LAY', $this->defaults["bl_lay"]);
            Configuration::updateValue('RC_BL_CONT', $this->defaults["bl_cont"]);
            Configuration::updateValue('RC_BL_ROW', $this->defaults["bl_row"]);
            Configuration::updateValue('RC_BL_HEAD', $this->defaults["bl_head"]);
            Configuration::updateValue('RC_BL_HEAD_HOVER', $this->defaults["bl_head_hover"]);
            Configuration::updateValue('RC_BL_H_TITLE', $this->defaults["bl_h_title"]);
            Configuration::updateValue('RC_BL_H_TITLE_H', $this->defaults["bl_h_title_h"]);
            Configuration::updateValue('RC_BL_H_META', $this->defaults["bl_h_meta"]);
            Configuration::updateValue('RC_BL_H_BG', $this->defaults["bl_h_bg"]);
            Configuration::updateValue('RC_BL_H_BORDER', $this->defaults["bl_h_border"]);
            Configuration::updateValue('RC_BL_C_ROW', $this->defaults["bl_c_row"]);
            Configuration::updateValue('RC_BL_DESC', $this->defaults["bl_desc"]);
            Configuration::updateValue('RC_BL_RM_COLOR', $this->defaults["bl_rm_color"]);
            Configuration::updateValue('RC_BL_RM_HOVER', $this->defaults["bl_rm_hover"]);

            // footer
            Configuration::updateValue('RC_FOOTER_LAY', $this->defaults["footer_lay"]);
            Configuration::updateValue('NC_LOGO_FOOTER', $this->defaults["nc_logo_footer"]);
            Configuration::updateValue('RC_FOOTER_BG', $this->defaults["footer_bg"]);
            Configuration::updateValue('RC_FOOTER_TITLES', $this->defaults["footer_titles"]);
            Configuration::updateValue('RC_FOOTER_TEXT', $this->defaults["footer_text"]);
            Configuration::updateValue('RC_FOOTER_LINK', $this->defaults["footer_link"]);
            Configuration::updateValue('RC_FOOTER_LINK_H', $this->defaults["footer_link_h"]);
            Configuration::updateValue('RC_FOOTER_NEWS_BG', $this->defaults["footer_news_bg"]);
            Configuration::updateValue('RC_FOOTER_NEWS_BORDER', $this->defaults["footer_news_border"]);
            Configuration::updateValue('RC_FOOTER_NEWS_PLACEH', $this->defaults["footer_news_placeh"]);
            Configuration::updateValue('RC_FOOTER_NEWS_COLOR', $this->defaults["footer_news_color"]);
            Configuration::updateValue('RC_FOOTER_NEWS_BUTTON', $this->defaults["footer_news_button"]);

            // Side and Mobile
            Configuration::updateValue('RC_LEVI_POSITION', $this->defaults["levi_position"]);
            Configuration::updateValue('NC_LEVI_BG', $this->defaults["nc_levi_bg"]);
            Configuration::updateValue('NC_LEVI_BORDER', $this->defaults["nc_levi_border"]);
            Configuration::updateValue('NC_LEVI_I', $this->defaults["nc_levi_i"]);
            Configuration::updateValue('NC_LEVI_I_HOVER', $this->defaults["nc_levi_i_hover"]);
            Configuration::updateValue('NC_LEVI_CART', $this->defaults["nc_levi_cart"]);
            Configuration::updateValue('NC_LEVI_CART_A', $this->defaults["nc_levi_cart_a"]);
            Configuration::updateValue('NC_LEVI_CLOSE', $this->defaults["nc_levi_close"]);
            Configuration::updateValue('NC_LEVI_CLOSE_I', $this->defaults["nc_levi_close_i"]);
            Configuration::updateValue('NC_SIDE_BG', $this->defaults["nc_side_bg"]);
            Configuration::updateValue('NC_SIDE_TITLE', $this->defaults["nc_side_title"]);
            Configuration::updateValue('NC_SIDE_TEXT', $this->defaults["nc_side_text"]);
            Configuration::updateValue('NC_SIDE_LIGHT', $this->defaults["nc_side_light"]);
            Configuration::updateValue('NC_SIDE_SEP', $this->defaults["nc_side_sep"]);

            Configuration::updateValue('NC_LOGO_MOBILE', $this->defaults["nc_logo_mobile"]);
            Configuration::updateValue('NC_MOB_HEADER', $this->defaults["nc_mob_header"]);
            Configuration::updateValue('NC_MOB_MENU', $this->defaults["nc_mob_menu"]);
            Configuration::updateValue('NC_MOB_HP', $this->defaults["nc_mob_hp"]);
            Configuration::updateValue('NC_MOB_CAT', $this->defaults["nc_mob_cat"]);
            Configuration::updateValue('NC_HEMOS', $this->defaults["nc_hemo"]);

            // typography
            Configuration::updateValue('RC_F_HEADINGS', $this->defaults["f_headings"]);
            Configuration::updateValue('RC_F_BUTTONS', $this->defaults["f_buttons"]);
            Configuration::updateValue('RC_F_TEXT', $this->defaults["f_text"]);
            Configuration::updateValue('RC_F_PRICE', $this->defaults["f_price"]);
            Configuration::updateValue('RC_F_PN', $this->defaults["f_pn"]);
            Configuration::updateValue('RC_LATIN_EXT', $this->defaults["latin_ext"]);
            Configuration::updateValue('RC_CYRILLIC', $this->defaults["cyrillic"]);
            Configuration::updateValue('RC_FONT_SIZE_PP', $this->defaults["font_size_pp"]);
            Configuration::updateValue('RC_FONT_SIZE_BODY', $this->defaults["font_size_body"]);
            Configuration::updateValue('RC_FONT_SIZE_HEAD', $this->defaults["font_size_head"]);
            Configuration::updateValue('RC_FONT_SIZE_BUTTONS', $this->defaults["font_size_buttons"]);
            Configuration::updateValue('RC_FONT_SIZE_PRICE', $this->defaults["font_size_price"]);
            Configuration::updateValue('RC_FONT_SIZE_PROD', $this->defaults["font_size_prod"]);
            Configuration::updateValue('RC_FONT_SIZE_PN', $this->defaults["font_size_pn"]);
            Configuration::updateValue('NC_UP_HP', $this->defaults["nc_up_hp"]);
            Configuration::updateValue('NC_UP_NC', $this->defaults["nc_up_nc"]);
            Configuration::updateValue('NC_UP_NP', $this->defaults["nc_up_np"]);
            Configuration::updateValue('NC_UP_F', $this->defaults["nc_up_f"]);
            Configuration::updateValue('NC_UP_BP', $this->defaults["nc_up_bp"]);
            Configuration::updateValue('NC_UP_MI', $this->defaults["nc_up_mi"]);
            Configuration::updateValue('NC_UP_MENU', $this->defaults["nc_up_menu"]);
            Configuration::updateValue('NC_UP_HEAD', $this->defaults["nc_up_head"]);
            Configuration::updateValue('NC_UP_BUT', $this->defaults["nc_up_but"]);
            Configuration::updateValue('NC_FW_MENU', $this->defaults["nc_fw_menu"]);
            Configuration::updateValue('NC_FW_HEADING', $this->defaults["nc_fw_heading"]);
            Configuration::updateValue('NC_FW_BUT', $this->defaults["nc_fw_but"]);
            Configuration::updateValue('NC_FW_PN', $this->defaults["nc_fw_pn"]);
            Configuration::updateValue('NC_FW_CT', $this->defaults["nc_fw_ct"]);
            Configuration::updateValue('NC_FW_PRICE', $this->defaults["nc_fw_price"]);
            Configuration::updateValue('NC_ITAL_PN', $this->defaults["nc_ital_pn"]);
            Configuration::updateValue('NC_ITALIC_PP', $this->defaults["nc_italic_pp"]);
            Configuration::updateValue('NC_LS', $this->defaults["nc_ls"]);
            Configuration::updateValue('NC_LS_H', $this->defaults["nc_ls_h"]);
            Configuration::updateValue('NC_LS_M', $this->defaults["nc_ls_m"]);
            Configuration::updateValue('NC_LS_P', $this->defaults["nc_ls_p"]);
            Configuration::updateValue('NC_LS_T', $this->defaults["nc_ls_t"]);
            Configuration::updateValue('NC_LS_B', $this->defaults["nc_ls_b"]);

            // Custom CSS
            Configuration::updateValue('NC_CSS', $this->defaults["nc_css"]);

            $this->generateCss();
        }

        if ($errors)
            $output .= $this->displayError($errors);

        return $output . $this->displayForm();
    }


    public function displayForm()
    {
        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);
        $divLangName = 'text¤title';
        $html = "";
        $html .= '
		<script type="text/javascript">
			id_language = Number(' . $defaultLanguage . ');
		</script>

		<form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" method="post" enctype="multipart/form-data" class="roytc_form">
			<fieldset class="roytc_filedset" style="margin-bottom: 20px;">
                <div class="customizer_wrapper">
                    <div class="roytc_navigation">
                        <ul id="#sidenav" class="ulnav tabs">
                            <li class="rtc_menu1 tab" id="tab_menu_1">
                                <a data-toggle="tab" title="Layout and Color scheme" href="#tab-general" class="tworows upper"><i class="icon-layout"></i>Layout and<br/> Color scheme</a>
                            </li>
                            <li class="rtc_menu2 tab" id="tab_menu_2">
                                <a data-toggle="tab" title="Header Options" href="#tab-header"><i class="icon-credit-card"></i>Header options</a>
                            </li>
                            <li class="rtc_menu1 tab" id="tab_menu_20">
                                <a data-toggle="tab" title="Body and Sections" href="#tab-global" class="upper"><i class="icon-aperture rotate"></i>Body Design</a>
                            </li>
                            <li class="rtc_menu3 tab" id="tab_menu_3">
                                <a data-toggle="tab" title="Homepage Content" href="#tab-homepage"><i class="icon-monitor"></i>Homepage Content</a>
                            </li>
                            <li class="rtc_menu4 tab" id="tab_menu_4">
                                <a data-toggle="tab" title="Page and sidebar" href="#tab-page"><i class="icon-file-text upper"></i>Page Content</a>
                            </li>
                            <li class="rtc_menu4 tab" id="tab_menu_19">
                                <a data-toggle="tab" title="Sidebar blocks and Filter" href="#tab-sidebar" class=""><i class="icon-sidebar"></i>Sidebar blocks</a>
                            </li>
                            <li class="rtc_menu5 tab" id="tab_menu_5">
                                <a data-toggle="tab" title="Products and Categories" href="#tab-productlist" class="tworows"><i class="icon-grid"></i>Products<br/> and Categories</a>
                            </li>
                            <li class="rtc_menu6 tab" id="tab_menu_6">
                                <a data-toggle="tab" title="Product page" href="#tab-productpage"><i class="icon-box"></i>Product page</a>
                            </li>
                            <li class="rtc_menu7 tab" id="tab_menu_7">
                                <a data-toggle="tab" title="Cart and order" href="#tab-cart"><i class="icon-shopping-cart upper"></i>Cart and order </a>
                            </li>
                            <li class="rtc_menu9 tab" id="tab_menu_9">
                                <a data-toggle="tab" title="Blog" href="#tab-blog"><i class="icon-image upper"></i>Blog</a>
                            </li>
                            <li class="rtc_menu10 tab" id="tab_menu_10">
                                <a data-toggle="tab" title="Footer" href="#tab-footer"><i class="icon-book"></i>Footer</a>
                            </li>
                            <li class="rtc_menu14 tab" id="tab_menu_14">
                                <a data-toggle="tab" title="Side" href="#tab-side" class="tworows"><i class="icon-smartphone"></i>Side Navigation<br /> and Mobile layout</a>
                            </li>
                            <li class="rtc_menu11 tab" id="tab_menu_11">
                                <a data-toggle="tab" title="Typography" href="#tab-fonts"><i class="icon-bold"></i>Typography</a>
                            </li>
                            <li class="rtc_menu12 tab" id="tab_menu_12">
                                <a data-toggle="tab" title="Custom CSS" href="#tab-css"><i class="icon-slack"></i>Custom CSS</a>
                            </li>
                            <li class="rtc_menu13 tab" id="tab_menu_13">
                                <a data-toggle="tab" title="Import / Export config" href="#tab-ie" class=""><i class="icon-package upper"></i>Import / Export config</a>
                            </li>
                        </ul>
                    </div>

                <div class="roytc_content">
                    <div class="tab-pane" id ="tab-general">
                    <h2 class="rtc_title1">' . $this->l('Layout and Color scheme') . '</h2>
                        <h3 class="first">Select DEMO</h3>
                        <div class="roytc_row ds_wrap" style="width:100%; margin-bottom:0;">
                              <div class="margin-form" style="margin-bottom:0; padding-left:56px; display:inline-block">
                                    <input type="radio" class="regular-radio select_demo" name="select_demo" id="select_demo1" value="1" ' . ((Configuration::get('NC_SELECT_DEMO') == "1") ? 'checked="checked" ' : '') . '/>
                                    <label class="ds_demo demo1" for="select_demo1"> <span></span></label>
                                    <input type="radio" class="regular-radio select_demo" name="select_demo" id="select_demo2" value="2" ' . ((Configuration::get('NC_SELECT_DEMO') == "2") ? 'checked="checked" ' : '') . '/>
                                    <label class="ds_demo demo2" for="select_demo2"> <span></span></label>
                                    <input type="radio" class="regular-radio select_demo" name="select_demo" id="select_demo3" value="3" ' . ((Configuration::get('NC_SELECT_DEMO') == "3") ? 'checked="checked" ' : '') . '/>
                                    <label class="ds_demo demo3" for="select_demo3"> <span></span></label>
                                    <input type="radio" class="regular-radio select_demo" name="select_demo" id="select_demo4" value="4" ' . ((Configuration::get('NC_SELECT_DEMO') == "4") ? 'checked="checked" ' : '') . '/>
                                    <label class="ds_demo demo4" for="select_demo4"> <span></span></label>
                                    <input type="radio" class="regular-radio select_demo" name="select_demo" id="select_demo5" value="5" ' . ((Configuration::get('NC_SELECT_DEMO') == "5") ? 'checked="checked" ' : '') . '/>
                                    <label class="ds_demo demo5" for="select_demo5"> <span></span></label>
                              </div>

                              <div class="margin-form" style="padding-left:56px; margin-top:0;">
                                    <div class="demo_apply"><span></span>Apply Demo settings</div>

                                    <div style="padding-top:10px; display:inline-block; width:100%">
                                    <p class="clear helpcontent" style="font-weight:bold; text-transform: uppercase; margin-top:16px;">
                                    ' . $this->l('It will change only settings like layouts of elements, spacings, widths, icons, etc, not change colors.') . '<br /><br />
                                    ' . $this->l('This switcher does not import banners and sliders of demo. You should do it according to guide.') . '<br /><br />
                                    ' . $this->l('It will change settings of customizer module and erase your current settings!') . '
                                    </p>
                                    </div>
                              </div>
                        </div>

                        <h3>Select COLOR SCHEME</h3>
                        <div class="roytc_row ds_wrap" style="width:100%; margin-bottom:0;">
                              <div class="margin-form" style="padding-left:56px; display:inline-block">
                                    <input type="radio" class="regular-radio select_scheme" name="select_scheme" id="select_scheme1" value="1" ' . ((Configuration::get('NC_SELECT_SCHEME') == "1") ? 'checked="checked" ' : '') . '/>
                                    <label class="ds_demo scheme1" for="select_scheme1"> <span></span></label>
                                    <input type="radio" class="regular-radio select_scheme" name="select_scheme" id="select_scheme2" value="2" ' . ((Configuration::get('NC_SELECT_SCHEME') == "2") ? 'checked="checked" ' : '') . '/>
                                    <label class="ds_demo scheme2" for="select_scheme2"> <span></span></label>
                                    <input type="radio" class="regular-radio select_scheme" name="select_scheme" id="select_scheme3" value="3" ' . ((Configuration::get('NC_SELECT_SCHEME') == "3") ? 'checked="checked" ' : '') . '/>
                                    <label class="ds_demo scheme3" for="select_scheme3"> <span></span></label>
                                    <input type="radio" class="regular-radio select_scheme" name="select_scheme" id="select_scheme4" value="4" ' . ((Configuration::get('NC_SELECT_SCHEME') == "4") ? 'checked="checked" ' : '') . '/>
                                    <label class="ds_demo scheme4" for="select_scheme4"> <span></span></label>
                                    <input type="radio" class="regular-radio select_scheme" name="select_scheme" id="select_scheme5" value="5" ' . ((Configuration::get('NC_SELECT_SCHEME') == "5") ? 'checked="checked" ' : '') . '/>
                                    <label class="ds_demo scheme5" for="select_scheme5"> <span></span></label>
                              </div>

                              <div class="margin-form" style="padding-left:56px; margin-top:0;">
                                    <div class="colors_apply"><span></span>Apply Color scheme</div>
                                    <div style="padding-top:10px; display:inline-block">
                                          <p class="clear helpcontent" style="font-weight:bold; text-transform: uppercase;">' . $this->l('It will change colors of your theme to premade color scheme, according to each demo and more. You can mix it of course.') . '<br /><br />
                                    ' . $this->l('If you want to change design of specific element or type of elements (buttons border for example) – you can set color scheme that you want, then go to tabs and find your element.') . '</p>
                                    </div>
                              </div>
                        </div>


                  <div class="hr"></div>
                     <h3 class="first">Layout settings and design</h3>
                     <div class="roytc_row ds_wrap" style="margin-bottom:100px;">
                        <label>Body layout</label>
                        <div class="margin-form" style="margin-top:0; padding-top:10px;">
                            <input type="radio" class="glay regular-radio" name="g_lay" value="1" id="g_lay1" ' . ((Configuration::get('RC_G_LAY') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds g_lay1" for="g_lay1"> <span>1 . Container</span></label>
                            <input type="radio" class="glay regular-radio" name="g_lay" value="2" id="g_lay2" ' . ((Configuration::get('RC_G_LAY') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds g_lay2" for="g_lay2"> <span>2 . Wide</span></label>
                            <input type="radio" class="glay regular-radio" name="g_lay" value="3" id="g_lay3" ' . ((Configuration::get('RC_G_LAY') == "3") ? 'checked="checked" ' : '') . ' />
                            <label class="ds g_lay3" for="g_lay3"> <span>3 . List</span></label>
                            <input type="radio" class="glay regular-radio" name="g_lay" value="4" id="g_lay4" ' . ((Configuration::get('RC_G_LAY') == "4") ? 'checked="checked" ' : '') . ' />
                            <label class="ds g_lay4" for="g_lay4"> <span>4 . Boxed</span></label>
                     </div></div>

                  <div class="if_boxed">
                  <div class="hr"></div>
                    <h3 class="first" style="margin-top:0;">Boxed layout settings</h3>
                    <div class="roytc_row" style="margin-top:0;">
                        <div class="roytc_row">
                              <label>Top margin</label>
                              <div class="margin-form">
                                    <input type="text" name="g_tp" id="g_tp" value="' . Configuration::get('RC_G_TP') . '" />px
                                    <p class="clear helpcontent">' . $this->l('Default: 150px') . '</p>
                              </div>
                        </div>
                        <div class="roytc_row">
                              <label>Bottom margin</label>
                              <div class="margin-form">
                                    <input type="text" name="g_bp" id="g_bp" value="' . Configuration::get('RC_G_BP') . '" />px
                                    <p class="clear helpcontent">' . $this->l('Default: 150px') . '</p>
                              </div>
                        </div>

                        <h3>' . $this->l('Outter main background') . '</h3>
                        <div class="roytc_row ds_wrap" style="margin-bottom:80px; margin-top:60px">
                              <label>What to use?</label>
                              <div class="margin-form" style="margin-top:0; padding-top:10px;">
                                  <input type="radio" class="regular-radio body_box_sw" name="body_box_sw" value="1" id="body_box_sw1" ' . ((Configuration::get('RC_BODY_BOX_SW') == "1") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds bg1 body_box_sw1" for="body_box_sw1"> <span>1 . Background</span></label>
                                  <input type="radio" class="regular-radio body_box_sw" name="body_box_sw" value="2" id="body_box_sw2" ' . ((Configuration::get('RC_BODY_BOX_SW') == "2") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds bg2 body_box_sw2" for="body_box_sw2"> <span>2 . Gradient</span></label>
                                  <input type="radio" class="regular-radio body_box_sw" name="body_box_sw" value="3" id="body_box_sw3" ' . ((Configuration::get('RC_BODY_BOX_SW') == "3") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds bg3 body_box_sw3" for="body_box_sw3"> <span>3 . Image</span></label>
                                  <input type="radio" class="regular-radio body_box_sw" name="body_box_sw" value="5" id="body_box_sw4" ' . ((Configuration::get('RC_BODY_BOX_SW') == "5") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds bg5 body_box_sw4" for="body_box_sw4"> <span>4 . Transparent</span></label>
                        </div></div>
                        <div class="if_body_box_bg">
                        <div class="roytc_row" style="margin-top:0;">
                              <label>Background Color</label>
                              <div class="margin-form" style="margin-top:0;">
                              <input type="color" name="main_background_color"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_MAIN_BACKGROUND_COLOR') . '" />
                        </div></div>
                        </div>
                        <div class="if_body_box_gr">
                              <div class="roytc_row" style="margin-top:0;">
                                   <label>Gradient start color</label>
                                   <div class="margin-form">
                                   <input type="color" name="nc_body_gs" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_BODY_GS') . '" />
                              </div></div>
                              <div class="roytc_row">
                                   <label>Gradient end color</label>
                                   <div class="margin-form">
                                   <input type="color" name="nc_body_ge" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_BODY_GE') . '" />
                              </div></div>
                              <div class="roytc_row">
                                   <label>Gradient angle</label>
                                   <div class="margin-form">
                                   <input type="text" name="nc_body_gg" value="' . Configuration::get('NC_BODY_GG') . '" /> degrees
                                   <p class="clear grad_direction"></p>
                              </div></div>
                        </div>
                        <div class="if_body_box_im">
                              ';

        $html .= $this->backgroundOptions($panel = "nc_body_im", $panelupper = "BODY_IM");

        $html .= '
                        </div>

                  </div></div>


                 <h3>' . $this->l('Main background') . '</h3>
                        <div class="roytc_row ds_wrap" style="margin-bottom:80px; margin-top:60px">
                              <label>What to use?</label>
                              <div class="margin-form" style="margin-top:0; padding-top:10px;">
                                  <input type="radio" class="regular-radio nc_main_bg" name="nc_main_bg" value="1" id="nc_main_bg1" ' . ((Configuration::get('NC_MAIN_BGS') == "1") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds bg1" for="nc_main_bg1"> <span>1 . Background</span></label>
                                  <input type="radio" class="regular-radio nc_main_bg" name="nc_main_bg" value="2" id="nc_main_bg2" ' . ((Configuration::get('NC_MAIN_BGS') == "2") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds bg2" for="nc_main_bg2"> <span>2 . Gradient</span></label>
                                  <input type="radio" class="regular-radio nc_main_bg" name="nc_main_bg" value="3" id="nc_main_bg3" ' . ((Configuration::get('NC_MAIN_BGS') == "3") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds bg3" for="nc_main_bg3"> <span>3 . Image</span></label>
                                  <input type="radio" class="regular-radio nc_main_bg" name="nc_main_bg" value="4" id="nc_main_bg4" ' . ((Configuration::get('NC_MAIN_BGS') == "4") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds bg5" for="nc_main_bg4"> <span>4 . Transparent</span></label>
                        </div></div>
                  <div class="if_nc_main_bc">
                        <div class="roytc_row" style="margin-top:0;">
                             <label>Section Background</label>
                             <div class="margin-form" style="margin-top:0;">
                             <input type="color" name="nc_main_bc" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_MAIN_BC') . '" />
                        </div></div>
                  </div>
                  <div class="if_nc_main_gr">
                        <div class="roytc_row" style="margin-top:0;">
                             <label>Gradient start color</label>
                             <div class="margin-form">
                             <input type="color" name="nc_main_gs" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_MAIN_GS') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Gradient end color</label>
                             <div class="margin-form">
                             <input type="color" name="nc_main_ge" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_MAIN_GE') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Gradient angle</label>
                             <div class="margin-form">
                             <input type="text" name="nc_main_gg" value="' . Configuration::get('NC_MAIN_GG') . '" /> degrees
                             <p class="clear grad_direction"></p>
                        </div></div>
                  </div>
                  <div class="if_nc_main_im">
                        ';

        $html .= $this->backgroundOptions($panel = "nc_main_im", $panelupper = "MAIN_IM");

        $html .= '
                  </div>


                  <div class="hr" style="float:none;"></div>

                  <p class="clear helpcontent" style="margin-top:14px; margin-left:60px; margin-bottom:40px;">' . $this->l('Don’t forget to export your config after finished design customization.') . '</p>
            </div>

            <div class="tab-pane" id ="tab-global">
                    <h2 class="rtc_title20">' . $this->l('Body and sections') . '</h2>
                  <div class="nav_inside_container">
                        <ul class="nav_inside tabs">
                              <li class="inside_tab tab-global1 active">
                                    <a data-inside="tab" href="#tab-global1">Body design</a>
                              </li>
                              <li class="inside_tab tab-global5">
                                    <a data-inside="tab" href="#tab-global5">Buttons</a>
                              </li>
                              <li class="inside_tab tab-global6">
                                    <a data-inside="tab" href="#tab-global6">Inputs</a>
                              </li>
                              <li class="inside_tab tab-global4">
                                    <a data-inside="tab" href="#tab-global4">Loader</a>
                              </li>
                        </ul>
                  </div>

                <div class="tab-content hide" id="tab-global1">
            <h3 class="first">Body design</h3>
            <div class="half_container low_paddings" style="display:inline-block; width:100%">
            <div class="half">
                <div class="roytc_row">
                    <label>Content background</label>
                        <div class="margin-form">
                              <input type="color" name="g_bg_content"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_BG_CONTENT') . '" />
                        </div></div>
                <div class="roytc_row">
                    <label>Body text color</label>
                        <div class="margin-form">
                              <input type="color" name="g_body_text"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_BODY_TEXT') . '" />
                        </div></div>
                <div class="roytc_row">
                    <label>Content borders / separators</label>
                        <div class="margin-form">
                              <input type="color" name="g_border"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_BORDER') . '" />
                        </div></div>
                <div class="roytc_row">
                    <label>Body label</label>
                        <div class="margin-form">
                              <input type="color" name="g_label"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_LABEL') . '" />
                        </div></div>
            </div><div class="half half_right">
                <div class="roytc_row">
                    <label>Body link color</label>
                        <div class="margin-form">
                              <input type="color" name="g_body_link"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_BODY_LINK') . '" />
                        </div></div>
                <div class="roytc_row">
                    <label>Body hover link color</label>
                        <div class="margin-form">
                              <input type="color" name="g_body_link_hover"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_BODY_LINK_HOVER') . '" />
                        </div></div>
                <div class="roytc_row">
                    <label>Body notes / comments</label>
                        <div class="margin-form">
                              <input type="color" name="g_body_comment"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_BODY_COMMENT') . '" />
                        </div></div>
            </div></div>

            <h4>Headers and titles</h4>
            <div class="half_container low_paddings" style="display:inline-block; width:100%">
            <div class="half">
                <div class="roytc_row">
                    <label>Headers underline</label>
                        <div class="margin-form">
                              <input type="color" name="g_header_under"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_HEADER_UNDER') . '" />
                        </div></div>
                <div class="roytc_row">
                    <label>Headers color</label>
                        <div class="margin-form">
                              <input type="color" name="g_header"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_HEADER') . '" />
                        </div></div>
            </div><div class="half half_right">
                <div class="roytc_row">
                    <label>Headers underline decor</label>
                        <div class="margin-form">
                              <input type="color" name="g_header_decor"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_HEADER_DECOR') . '" />
                        </div></div>
            </div></div>

            <h4>Controls</h4>
                  <div class="roytc_row">
                     <label>Color</label>
                        <div class="margin-form">
                              <input type="color" name="g_cc"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_CC') . '" />
                  </div></div>
                  <div class="roytc_row">
                     <label>Color hover</label>
                        <div class="margin-form">
                              <input type="color" name="g_ch"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_CH') . '" />
                  </div></div>

            <h4>Tooltip</h4>
                  <div class="roytc_row">
                     <label>Background</label>
                        <div class="margin-form">
                              <input type="color" name="g_hb"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_HB') . '" />
                  </div></div>
                  <div class="roytc_row">
                     <label>Color</label>
                        <div class="margin-form">
                              <input type="color" name="g_hc"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_HC') . '" />
                  </div></div>

            <h4>Tables</h4>

                <div class="roytc_row">
                    <label>Even row background</label>
                        <div class="margin-form">
                              <input type="color" name="g_bg_even"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_BG_EVEN') . '" />
                </div></div>
                <div class="roytc_row">
                    <label>Even row color</label>
                        <div class="margin-form">
                              <input type="color" name="g_color_even"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_COLOR_EVEN') . '" />
                </div></div>

            <h4>Account</h4>
            <div class="half_container low_paddings" style="display:inline-block; width:100%">
            <div class="half">
                <div class="roytc_row">
                    <label>Account icon</label>
                        <div class="margin-form">
                              <input type="color" name="g_acc_icon"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_ACC_ICON') . '" />
                        </div></div>
                <div class="roytc_row">
                    <label>Account title</label>
                        <div class="margin-form">
                              <input type="color" name="g_acc_title"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_G_ACC_TITLE') . '" />
                        </div></div>
            </div><div class="half half_right">
            </div></div>

            <h4>Fancybox</h4>
            <div class="half_container low_paddings" style="display:inline-block; width:100%">
            <div class="half">
                  <div class="roytc_row">
                     <label>Name background</label>
                        <div class="margin-form">
                              <input type="color" name="g_fancy_nbg"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_FANCY_NBG') . '" />
                  </div></div>
                  <div class="roytc_row">
                     <label>Name color</label>
                        <div class="margin-form">
                              <input type="color" name="g_fancy_nc"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_FANCY_NC') . '" />
                  </div></div>
            </div><div class="half half_right">
            </div></div>
          </div>

          <div class="tab-content hide" id="tab-global4">
                  <h3 class="first">Loader options</h3>
                  <div class="roytc_row">
                        <label>Display Loader?</label>
                        <div class="margin-form">
                            <input type="radio" class="regular-radio" name="nc_loader" id="nc_loader_1" value="1" ' . ((Configuration::get('NC_LOADERS') == 1) ? 'checked="checked" ' : '') . '/>
                            <label class="t" for="nc_loader_1"> Yes</label>
                            <input type="radio" class="regular-radio" name="nc_loader" id="nc_loader_0" value="0" ' . ((Configuration::get('NC_LOADERS') == 0) ? 'checked="checked" ' : '') . '/>
                            <label class="t" for="nc_loader_0"> No</label>
                  </div></div>

                  <div class="roytc_row ds_wrap">
                        <label>Loader Layout</label>
                        <div class="margin-form" style="margin-top:0; padding-top:10px;">
                            <input type="radio" class="regular-radio" name="nc_loader_logo" value="1" id="nc_loader_logo1" ' . ((Configuration::get('NC_LOADER_LOGOS') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds nc_loader1" for="nc_loader_logo1"> <span>1 . Loader</span></label>
                            <input type="radio" class="regular-radio" name="nc_loader_logo" value="2" id="nc_loader_logo2" ' . ((Configuration::get('NC_LOADER_LOGOS') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds nc_loader2" for="nc_loader_logo2"> <span>2 . Logo + loader</span></label>
                            <input type="radio" class="regular-radio" name="nc_loader_logo" value="3" id="nc_loader_logo3" ' . ((Configuration::get('NC_LOADER_LOGOS') == "3") ? 'checked="checked" ' : '') . ' />
                            <label class="ds nc_loader3" for="nc_loader_logo3"> <span>3 . Logo only</span></label>
                            <input type="radio" class="regular-radio" name="nc_loader_logo" value="4" id="nc_loader_logo4" ' . ((Configuration::get('NC_LOADER_LOGOS') == "4") ? 'checked="checked" ' : '') . ' />
                            <label class="ds nc_loader4" for="nc_loader_logo4"> <span>4 . Logo inside loader</span></label>
                  </div></div>


                  <div class="roytc_row" style="margin-top:0;">
                        <label>Loader logo upload</label>
                            <div class="margin-form" style="margin-top:0;">
                                <input id="logo_loader_field2" type="file" name="logo_loader_field2">
                                <input id="logo_loader_button2" type="submit" class="button" name="logo_loader_button2" value="' . $this->l('Upload') . '">
                                <p class="clear helpcontent">' . $this->l('Max width - 480px, max height - 240px. Preffered format - transparent .png') . '</p>
                            </div>';
        $loader_logo_ext = Configuration::get('NC_LOGO_LOADER');
        if ($loader_logo_ext != "") {
            if (Shop::getContext() == Shop::CONTEXT_SHOP)
                $adv_imgname = 'logo-loader' . '-' . (int)$this->context->shop->getContextShopID();

            $html .= '<label>Loader logo</label>
                                                <div class="margin-form">
                                                <img class="imgback" src="' . $this->_path . 'upload/' . $adv_imgname . '.' . $loader_logo_ext . '" /><br /><br />
                                                <input id="logo_loader_delete2" type="submit" class="button" value="' . $this->l('Delete image') . '" name="logo_loader_delete2">
                                                </div>';
        }

        $html .= '
                  </div>

                  <div class="hr"></div>
                  <div class="roytc_row">
                        <label>Loader background</label>
                        <div class="margin-form">
                              <input type="color" name="nc_loader_bg"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_LOADER_BG') . '" />
                  </div></div>
                  <div class="roytc_row">
                        <label>Loader color</label>
                        <div class="margin-form">
                              <input type="color" name="nc_loader_color"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_LOADER_COLOR') . '" />
                  </div></div>
          </div>
          <div class="tab-content hide" id="tab-global5">
                     <h3 class="first">' . $this->l('Buttons') . '</h3>

                            <div class="roytc_row">
                                  <label>Buttons border radius</label>
                                  <div class="margin-form">
                                        <input type="text" name="nc_b_radius"  value="' . Configuration::get('NC_B_RADIUS') . '" />px
                                        <p class="clear helpcontent">Recommendation: 0-10
                                        </p>
                                  </div>
                            </div>
                            <div class="roytc_row">
                              <label>Buttons shadow</label>
                            <div class="margin-form">
                                <input type="radio" class="regular-radio" name="nc_b_sh" id="nc_b_sh1" value="1" ' . ((Configuration::get('NC_B_SHS') == 1) ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="nc_b_sh1"> Yes</label>
                                <input type="radio" class="regular-radio" name="nc_b_sh" id="nc_b_sh0" value="0" ' . ((Configuration::get('NC_B_SHS') == 0) ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="nc_b_sh0"> No</label>
                            </div></div>

                <div class="hr"></div>
                <div class="half_container" style="display:inline-block; width:100%">
                <div class="half">
          		 <div class="roytc_row">
                        <label>Normal button background</label>
                        <div class="margin-form">
                        <input type="color" name="b_normal_bg"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_B_NORMAL_BG') . '" /></div>
                     </div>
    				 <div class="roytc_row">
                        <label>Normal button border</label>
                        <div class="margin-form">
                        <input type="color" name="b_normal_border"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_B_NORMAL_BORDER') . '" /></div>
                     </div>
    				 <div class="roytc_row">
                        <label>Normal button color</label>
                        <div class="margin-form">
                        <input type="color" name="b_normal_color"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_B_NORMAL_COLOR') . '" /></div>
                     </div>
                </div>
                <div class="half half_right">
                      <div class="roytc_row">
                        <label>Normal button hover background</label>
                        <div class="margin-form">
                        <input type="color" name="b_normal_bg_hover"  class="colorpicker cs_sc" data-hex="true" value="' . Configuration::get('RC_B_NORMAL_BG_HOVER') . '" /></div>
                      </div>
                      <div class="roytc_row">
                        <label>Normal button hover border</label>
                        <div class="margin-form">
                        <input type="color" name="b_normal_border_hover"  class="colorpicker cs_sc" data-hex="true" value="' . Configuration::get('RC_B_NORMAL_BORDER_HOVER') . '" /></div>
                      </div>
                      <div class="roytc_row">
                        <label>Normal button hover color</label>
                        <div class="margin-form">
                        <input type="color" name="b_normal_color_hover"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_B_NORMAL_COLOR_HOVER') . '" /></div>
                      </div>
                </div>
                <div class="hr"></div>
                <div class="half">
                      <div class="roytc_row">
                        <label>Exclusive button background</label>
                        <div class="margin-form">
                        <input type="color" name="b_ex_bg"  class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_B_EX_BG') . '" /></div>
                      </div>
                             <div class="roytc_row">
                        <label>Exclusive button border</label>
                        <div class="margin-form">
                        <input type="color" name="b_ex_border"  class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_B_EX_BORDER') . '" /></div>
                      </div>
                             <div class="roytc_row">
                        <label>Exclusive button color</label>
                        <div class="margin-form">
                        <input type="color" name="b_ex_color"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_B_EX_COLOR') . '" /></div>
                      </div>
                </div>
                <div class="half half_right">

                </div>

                </div>

          </div>
          <div class="tab-content hide" id="tab-global6">
               <h3 class="first">' . $this->l('Inputs') . '</h3>

                      <div class="roytc_row">
                            <label>Inputs border radius</label>
                            <div class="margin-form">
                                  <input type="text" name="i_b_radius"  value="' . Configuration::get('RC_I_B_RADIUS') . '" />px
                                  <p class="clear helpcontent">Recommendation: 0-10
                                  </p>
                            </div>
                      </div>

                <div class="hr"></div>
                <div class="half_container" style="display:inline-block; width:100%">
                <div class="half">
                       <div class="roytc_row">
                        <label>Inputs background</label>
                        <div class="margin-form">
                        <input type="color" name="i_bg"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_I_BG') . '" /></div>
                     </div>
                             <div class="roytc_row">
                        <label>Inputs border</label>
                        <div class="margin-form">
                        <input type="color" name="i_b_color"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_I_B_COLOR') . '" /></div>
                     </div>
                             <div class="roytc_row">
                        <label>Inputs color</label>
                        <div class="margin-form">
                        <input type="color" name="i_color"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_I_COLOR') . '" /></div>
                     </div>
                </div>
                <div class="half half_right">
                             <div class="roytc_row">
                        <label>Inputs focused background</label>
                        <div class="margin-form">
                        <input type="color" name="i_bg_focus"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_I_BG_FOCUS') . '" /></div>
                     </div>
                             <div class="roytc_row">
                        <label>Inputs focused border</label>
                        <div class="margin-form">
                        <input type="color" name="i_b_focus"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_I_B_FOCUS') . '" /></div>
                     </div>
                             <div class="roytc_row">
                        <label>Inputs focused color</label>
                        <div class="margin-form">
                        <input type="color" name="i_color_focus"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_I_COLOR_FOCUS') . '" /></div>
                     </div>
                </div>
                </div>
                <div class="hr"></div>
                <div class="half_container" style="display:inline-block; width:100%">
                <div class="half">
                    <div class="roytc_row">
                      <label style="line-height:18px">Placeholder color</label>
                      <div class="margin-form">
                      <input type="color" name="i_ph"  class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_I_PH') . '" /></div>
                    </div>
                </div>
                <div class="half half_right">
                  <div class="roytc_row">
                    <label style="line-height:18px">Checkboxes, radio checked background</label>
                    <div class="margin-form">
                    <input type="color" name="rc_bg_active"  class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_RC_BG_ACTIVE') . '" /></div>
                  </div>
                </div>
                </div>
          </div>


            </div>

            <div class="tab-pane" id ="tab-header">
                  <h2 class="rtc_title2">' . $this->l('Header options') . '</h2>
                  <div class="nav_inside_container">
                        <ul class="nav_inside tabs">
                              <li class="inside_tab tab-header1 active">
                                    <a data-inside="tab" href="#tab-header1">Layout and design</a>
                              </li>
                              <li class="inside_tab tab-header2">
                                    <a data-inside="tab" href="#tab-header2">Menu</a>
                              </li>
                              <li class="inside_tab tab-header3">
                                    <a data-inside="tab" href="#tab-header3">Search</a>
                              </li>
                              <li class="inside_tab tab-header4">
                                    <a data-inside="tab" href="#tab-header4">Cart</a>
                              </li>
                        </ul>
                  </div>

                  <div class="tab-content hide" id="tab-header1">
                        <h3 class="first">' . $this->l('Header Layout') . '</h3>
                        <div class="roytc_row ds_wrap" style="display:inline-block; margin-top:10px;">
                              <label>Select your Header</label>
                              <div class="margin-form" style="margin-top:0; padding-top:10px;">
                                  <input type="radio" class="regular-radio header_lay" name="header_lay" value="1" id="header_lay1" ' . ((Configuration::get('RC_HEADER_LAY') == "1") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds header_lay1" for="header_lay1"> <span>1 . Classic</span></label>
                                  <input type="radio" class="regular-radio header_lay" name="header_lay" value="2" id="header_lay2" ' . ((Configuration::get('RC_HEADER_LAY') == "2") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds header_lay2" for="header_lay2"> <span>1 . Centered</span></label>
                        </div></div>


                  <h3>' . $this->l('Logo') . '</h3>
                  <div class="roytc_row" style="margin-top:0;">
                        <label>Logo upload</label>
                        <div class="margin-form" style="margin-top:0;">
                              <input id="logo_normal_field2" type="file" name="logo_normal_field2">
                              <input id="logo_normal_button2" type="submit" class="button" name="logo_normal_button2" value="' . $this->l('Upload') . '">
                              <p class="clear helpcontent">' . $this->l('Max height - 90px. If you use vertical header - logo can be higher. Preffered format - transparent .png') . '</p>
                        </div>';
        $logo_normal_ext = Configuration::get('NC_LOGO_NORMAL');
        if ($logo_normal_ext != "") {
            if (Shop::getContext() == Shop::CONTEXT_SHOP)
                $adv_imgname = 'logo-normal' . '-' . (int)$this->context->shop->getContextShopID();

            $html .= '<label>Uploaded logo</label>
                                                <div class="margin-form">
                                                <img class="imgback" src="' . $this->_path . 'upload/' . $adv_imgname . '.' . $logo_normal_ext . '" /><br /><br />
                                                <input id="logo_normal_delete2" type="submit" class="button" value="' . $this->l('Delete image') . '" name="logo_normal_delete2">
                                                </div>';
        }
        $html .= '
                  </div>


                         <h3>' . $this->l('Header background') . '</h3>
                                <div class="roytc_row ds_wrap" style="margin-bottom:80px; margin-top:60px">
                                      <label>What to use?</label>
                                      <div class="margin-form" style="margin-top:0; padding-top:10px;">
                                          <input type="radio" class="regular-radio nc_header_bg" name="nc_header_bg" value="1" id="nc_header_bg1" ' . ((Configuration::get('NC_HEADER_BGS') == "1") ? 'checked="checked" ' : '') . ' />
                                          <label class="ds bg1" for="nc_header_bg1"> <span>1 . Background</span></label>
                                          <input type="radio" class="regular-radio nc_header_bg" name="nc_header_bg" value="2" id="nc_header_bg2" ' . ((Configuration::get('NC_HEADER_BGS') == "2") ? 'checked="checked" ' : '') . ' />
                                          <label class="ds bg2" for="nc_header_bg2"> <span>2 . Gradient</span></label>
                                          <input type="radio" class="regular-radio nc_header_bg" name="nc_header_bg" value="3" id="nc_header_bg3" ' . ((Configuration::get('NC_HEADER_BGS') == "3") ? 'checked="checked" ' : '') . ' />
                                          <label class="ds bg3" for="nc_header_bg3"> <span>3 . Image</span></label>
                                          <input type="radio" class="regular-radio nc_header_bg" name="nc_header_bg" value="4" id="nc_header_bg4" ' . ((Configuration::get('NC_HEADER_BGS') == "4") ? 'checked="checked" ' : '') . ' />
                                          <label class="ds bg5" for="nc_header_bg4"> <span>4 . Transparent</span></label>
                                </div></div>
                          <div class="if_nc_header_bc">
                                <div class="roytc_row" style="margin-top:0;">
                                     <label>Section Background</label>
                                     <div class="margin-form" style="margin-top:0;">
                                     <input type="color" name="nc_header_bc" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_HEADER_BC') . '" />
                                </div></div>
                          </div>
                          <div class="if_nc_header_gr">
                                <div class="roytc_row" style="margin-top:0;">
                                     <label>Gradient start color</label>
                                     <div class="margin-form">
                                     <input type="color" name="nc_header_gs" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_HEADER_GS') . '" />
                                </div></div>
                                <div class="roytc_row">
                                     <label>Gradient end color</label>
                                     <div class="margin-form">
                                     <input type="color" name="nc_header_ge" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_HEADER_GE') . '" />
                                </div></div>
                                <div class="roytc_row">
                                     <label>Gradient angle</label>
                                     <div class="margin-form">
                                     <input type="text" name="nc_header_gg" value="' . Configuration::get('NC_HEADER_GG') . '" /> degrees
                                     <p class="clear grad_direction"></p>
                                </div></div>
                          </div>
                          <div class="if_nc_header_im">
                                ';

        $html .= $this->backgroundOptions($panel = "nc_header_im", $panelupper = "HEADER_IM");

        $html .= '
                          </div>

                                      <h3>' . $this->l('Sticky Header') . '</h3>
                            <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                            <div class="half" style="width:620px; padding-bottom:0;">
                                      <div class="roytc_row">
                                           <label>Background</label>
                                           <div class="margin-form">
                                           <input type="color" name="nc_header_st_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_HEADER_ST_BGCOLOR') . '" />
                                      </div></div>
                                      <div class="roytc_row">
                                           <label>Hover link background</label>
                                           <div class="margin-form">
                                           <input type="color" name="nc_header_st_bgh" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_HEADER_ST_BGCOLORHOVER') . '" />
                                      </div></div>
                            </div><div class="half" style="width:620px; padding-bottom:0;">
                                      <div class="roytc_row">
                                           <label>Link</label>
                                           <div class="margin-form">
                                           <input type="color" name="nc_header_st_link" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_HEADER_ST_LINKCOLOR') . '" />
                                      </div></div>
                                      <div class="roytc_row">
                                           <label>Link hover</label>
                                           <div class="margin-form">
                                           <input type="color" name="nc_header_st_linkh" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_HEADER_ST_LINKCOLORHOVER') . '" />
                                      </div></div>
                            </div></div>


                                      <h3>' . $this->l('Top Panel') . '</h3>
                            <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                            <div class="half" style="width:620px; padding-bottom:0;">
                                      <div class="roytc_row">
                                           <label>Background</label>
                                           <div class="margin-form">
                                           <input type="color" name="header_nbg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_HEADER_NBG') . '" />
                                      </div></div>
                                      <div class="roytc_row">
                                           <label>Border</label>
                                           <div class="margin-form">
                                           <input type="color" name="header_nb" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_HEADER_NB') . '" />
                                      </div></div>
                                      <div class="roytc_row">
                                           <label>Popup background</label>
                                           <div class="margin-form">
                                           <input type="color" name="header_ns" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_HEADER_NS') . '" />
                                      </div></div>
                            </div><div class="half" style="width:620px; padding-bottom:0;">
                                      <div class="roytc_row">
                                           <label>Text</label>
                                           <div class="margin-form">
                                           <input type="color" name="header_nt" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_HEADER_NT') . '" />
                                      </div></div>
                                      <div class="roytc_row">
                                           <label>Link</label>
                                           <div class="margin-form">
                                           <input type="color" name="header_nl" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_HEADER_NL') . '" />
                                      </div></div>
                                      <div class="roytc_row">
                                           <label>Link hover</label>
                                           <div class="margin-form">
                                           <input type="color" name="header_nlh" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_HEADER_NLH') . '" />
                                      </div></div>
                            </div></div>


                                <h3>' . $this->l('Shadow of header blocks') . '</h3>
                                <div class="roytc_row">
                                <label>Add shadow?</label>
                                     <div class="margin-form">
                                    <input type="radio" class="regular-radio nc_header_shadow" name="nc_header_shadow" id="nc_header_shadow1" value="1" ' . ((Configuration::get('NC_HEADER_SHADOWS') == 1) ? 'checked="checked" ' : '') . '/>
                                    <label class="t" for="nc_header_shadow1"> Yes</label>
                                    <input type="radio" class="regular-radio nc_header_shadow" name="nc_header_shadow" id="nc_header_shadow0" value="0" ' . ((Configuration::get('NC_HEADER_SHADOWS') == 0) ? 'checked="checked" ' : '') . '/>
                                    <label class="t" for="nc_header_shadow0"> No</label>
                                </div></div>


            </div>

                  <div class="tab-content hide" id="tab-header2">

                  <h3 class="first">' . $this->l('Menu header appearance') . '</h3>

                  <div class="roytc_row ds_wrap">
                        <label>Menu elements align</label>
                        <div class="margin-form" style="margin-top:0; padding-top:10px;">
                            <input type="radio" class="regular-radio nc_m_align" name="nc_m_align" value="1" id="nc_m_align1" ' . ((Configuration::get('NC_M_ALIGN_S') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds align1" for="nc_m_align1"> <span></span></label>
                            <input type="radio" class="regular-radio nc_m_align" name="nc_m_align" value="2" id="nc_m_align2" ' . ((Configuration::get('NC_M_ALIGN_S') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds align2" for="nc_m_align2"> <span></span></label>
                            <input type="radio" class="regular-radio nc_m_align" name="nc_m_align" value="3" id="nc_m_align3" ' . ((Configuration::get('NC_M_ALIGN_S') == "3") ? 'checked="checked" ' : '') . ' />
                            <label class="ds align3" for="nc_m_align3"> <span></span></label>
                  </div></div>
                  <div class="roytc_row ds_wrap">
                        <label>Menu layout</label>
                        <div class="margin-form" style="margin-top:0; padding-top:10px;">
                            <input type="radio" class="regular-radio nc_m_layout" name="nc_m_layout" value="1" id="nc_m_layout1" ' . ((Configuration::get('NC_M_LAYOUT_S') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds m_layout1" for="nc_m_layout1"> <span>1 . On the border</span></label>
                            <input type="radio" class="regular-radio nc_m_layout" name="nc_m_layout" value="2" id="nc_m_layout2" ' . ((Configuration::get('NC_M_LAYOUT_S') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds m_layout2" for="nc_m_layout2"> <span>2 . Inside header</span></label>
                  </div></div>


              <div class="half_container low_paddings" style="display:inline-block; width:100%; margin-top:20px;">
              <div class="half" style="width:620px; padding-bottom:0;">
                <div class="roytc_row">
                    <label>Underline on hover?</label>
                    <div class="margin-form">
                    <input type="radio" class="regular-radio nc_m_under" name="nc_m_under" id="nc_m_under1" value="1" ' . ((Configuration::get('NC_M_UNDER_S') == "1") ? 'checked="checked" ' : '') . ' style="display:none!important;" />
                    <label class="t" for="nc_m_under1"> Yes</label>
                    <input type="radio" class="regular-radio nc_m_under" name="nc_m_under" id="nc_m_under0" value="0" ' . ((Configuration::get('NC_M_UNDER_S') == "0") ? 'checked="checked" ' : '') . ' style="display:none!important;" />
                    <label class="t" for="nc_m_under0"> No</label>
                </div></div>
              </div><div class="half" style="width:620px; padding-bottom:0;">
                  <div class="roytc_row">
                        <label>Underline on hover color</label>
                        <div class="margin-form">
                        <input type="color" name="nc_m_under_color" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_M_UNDER_COLOR') . '" />
                  </div></div>
              </div></div>

              <h3>' . $this->l('Menu colors') . '</h3>
                <div class="roytc_row">
                    <label>Override menu module colors?</label>
                    <div class="margin-form">
                    <input type="radio" class="regular-radio nc_m_override" name="nc_m_override" id="nc_m_override2" value="2" ' . ((Configuration::get('NC_M_OVERRIDE_S') == "2") ? 'checked="checked" ' : '') . ' style="display:none!important;" />
                    <label class="t" for="nc_m_override2"> Yes</label>
                    <input type="radio" class="regular-radio nc_m_override" name="nc_m_override" id="nc_m_override1" value="1" ' . ((Configuration::get('NC_M_OVERRIDE_S') == "1") ? 'checked="checked" ' : '') . ' style="display:none!important;" />
                    <label class="t" for="nc_m_override1"> No</label>
                </div></div>
              <div class="half_container low_paddings" style="display:inline-block; width:100%; margin-top:20px;">
              <div class="half" style="width:620px; padding-bottom:0;">
                  <div class="roytc_row">
                        <label>Menu line background</label>
                        <div class="margin-form">
                        <input type="color" name="m_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_M_BG') . '" />
                  </div></div>
                  <div class="roytc_row">
                        <label>Link background hover</label>
                        <div class="margin-form">
                        <input type="color" name="m_link_bg_hover" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_M_LINK_BG_HOVER') . '" />
                  </div></div>

              </div><div class="half" style="width:620px; padding-bottom:0;">

                  <div class="roytc_row">
                       <label>Top menu link</label>
                       <div class="margin-form">
                       <input type="color" name="m_link" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_M_LINK') . '" />
                  </div></div>
                  <div class="roytc_row">
                        <label>Top menu link hover</label>
                        <div class="margin-form">
                        <input type="color" name="m_link_hover" class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_M_LINK_HOVER') . '" />
                  </div></div>

              </div><div class="half" style="width:620px; padding-bottom:0;">
              </div></div>


                  <h3>' . $this->l('Submenu popup') . '</h3>
                     <div class="half_container low_paddings" style="display:inline-block; width:100%">
                        <div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                             <label>Background</label>
                             <div class="margin-form">
                             <input type="color" name="m_popup_lbg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_M_POPUP_LBG') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Separators</label>
                             <div class="margin-form">
                             <input type="color" name="m_popup_lborder" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_M_POPUP_LBORDER') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Text</label>
                             <div class="margin-form">
                             <input type="color" name="m_popup_lchevron" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_M_POPUP_LCHEVRON') . '" />
                        </div></div>
                    </div><div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                             <label>Link color</label>
                             <div class="margin-form">
                             <input type="color" name="m_popup_llink" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_M_POPUP_LLINK') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Link color hover</label>
                             <div class="margin-form">
                             <input type="color" name="m_popup_llink_hover" class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_M_POPUP_LLINK_HOVER') . '" />
                        </div></div>
                    </div></div>
                    <div class="hr"></div>
                    <div class="roytc_row" style="margin-top:80px;">
                         <label>Border radius</label>
                         <div class="margin-form" style="margin-top:0;">
                         <input type="text" name="nc_m_br" value="' . Configuration::get('NC_M_BR_S') . '" /> px
                    </div></div>

                  </div>

                  <div class="tab-content hide" id="tab-header3">
                        <h3 class="first">' . $this->l('Header appearance') . '</h3>
                        <div class="roytc_row ds_wrap">
                              <label>Layout</label>
                              <div class="margin-form" style="margin-top:0; padding-top:10px;">
                                  <input type="radio" class="regular-radio search_lay" name="search_lay" value="1" id="search_lay1" ' . ((Configuration::get('RC_SEARCH_LAY') == "1") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds search_lay1" for="search_lay1"> <span>1 . Classic</span></label>
                                  <input type="radio" class="regular-radio search_lay" name="search_lay" value="2" id="search_lay2" ' . ((Configuration::get('RC_SEARCH_LAY') == "2") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds search_lay2" for="search_lay2"> <span>2 . Minimal</span></label>
                                  <input type="radio" class="regular-radio search_lay" name="search_lay" value="3" id="search_lay3" ' . ((Configuration::get('RC_SEARCH_LAY') == "3") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds search_lay3" for="search_lay3"> <span>3 . Line Transparent</span></label>
                                  <input type="radio" class="regular-radio search_lay" name="search_lay" value="4" id="search_lay4" ' . ((Configuration::get('RC_SEARCH_LAY') == "4") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds search_lay4" for="search_lay4"> <span>4 . Minimal Transparent</span></label>
                        </div></div>

                  <div class="roytc_row ds_wrap">
                        <label>Search icon</label>
                        <div class="margin-form" style="margin-top:0; padding-top:10px;">
                            <input type="radio" class="regular-radio" name="nc_i_search" value="search1" id="nc_i_search1" ' . ((Configuration::get('NC_I_SEARCHS') == "search1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds_icon nc_i_qv1" for="nc_i_search1"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_i_search" value="search2" id="nc_i_search2" ' . ((Configuration::get('NC_I_SEARCHS') == "search2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds_icon nc_i_qv2" for="nc_i_search2"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_i_search" value="search3" id="nc_i_search3" ' . ((Configuration::get('NC_I_SEARCHS') == "search3") ? 'checked="checked" ' : '') . ' />
                            <label class="ds_icon nc_i_qv3" for="nc_i_search3"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_i_search" value="search4" id="nc_i_search4" ' . ((Configuration::get('NC_I_SEARCHS') == "search4") ? 'checked="checked" ' : '') . ' />
                            <label class="ds_icon nc_i_qv4" for="nc_i_search4"> <span></span></label>
                  </div></div>

                  <h3>' . $this->l('Search colors') . '</h3>

                <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:620px; padding-bottom:0;">
                  <div class="roytc_row">
                        <label>Background</label>
                        <div class="margin-form">
                        <input type="color" name="search_bg"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SEARCH_BG') . '" />
                  </div></div>
                  <div class="roytc_row">
                        <label>Border</label>
                        <div class="margin-form">
                        <input type="color" name="search_line"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SEARCH_LINE') . '" />
                  </div></div>
                  <div class="roytc_row">
                        <label>Placeholder</label>
                        <div class="margin-form">
                        <input type="color" name="search_input"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SEARCH_INPUT') . '" />
                  </div></div>
                    <div class="roytc_row">
                        <label>Search text</label>
                        <div class="margin-form">
                         <input type="color" name="search_t"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SEARCH_T') . '" />
                    </div></div>
                    <div class="roytc_row">
                          <label>Icon</label>
                          <div class="margin-form">
                          <input type="color" name="search_icon"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SEARCH_ICON') . '" />
                    </div></div>
                </div><div class="half" style="width:620px; padding-bottom:0;">
                  <div class="roytc_row">
                      <div class="roytc_row">
                            <label>Background focused</label>
                            <div class="margin-form">
                            <input type="color" name="search_bg_hover"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SEARCH_BG_HOVER') . '" />
                      </div></div>
                      <div class="roytc_row">
                            <label>Border focused</label>
                            <div class="margin-form">
                            <input type="color" name="search_lineh"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SEARCH_LINEH') . '" />
                      </div></div>
                            <label>Placeholder focused</label>
                            <div class="margin-form">
                            <input type="color" name="search_inputh"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SEARCH_INPUTH') . '" />
                      </div></div>
                        <div class="roytc_row">
                            <label>Text focused</label>
                            <div class="margin-form">
                             <input type="color" name="search_t_hover"  class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_SEARCH_T_HOVER') . '" />
                        </div></div>
                      <div class="roytc_row">
                            <label>Icon focused</label>
                            <div class="margin-form">
                            <input type="color" name="search_iconh"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SEARCH_ICONH') . '" />
                      </div></div>
                </div></div>

                  </div>

                  <div class="tab-content hide" id="tab-header4">
                        <h3 class="first">' . $this->l('Header appearance') . '</h3>
                        <div class="roytc_row ds_wrap">
                              <label>Layout</label>
                              <div class="margin-form" style="margin-top:0; padding-top:10px;">
                                  <input type="radio" class="regular-radio cart_lay" name="cart_lay" value="1" id="cart_lay1" ' . ((Configuration::get('RC_CART_LAY') == "1") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds cart_lay1" for="cart_lay1"> <span>1 . Classic</span></label>
                                  <input type="radio" class="regular-radio cart_lay" name="cart_lay" value="2" id="cart_lay2" ' . ((Configuration::get('RC_CART_LAY') == "2") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds cart_lay2" for="cart_lay2"> <span>2 . Minimal</span></label>
                                  <input type="radio" class="regular-radio cart_lay" name="cart_lay" value="3" id="cart_lay3" ' . ((Configuration::get('RC_CART_LAY') == "3") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds cart_lay3" for="cart_lay3"> <span>3 . Transparent Classic</span></label>
                                  <input type="radio" class="regular-radio cart_lay" name="cart_lay" value="4" id="cart_lay4" ' . ((Configuration::get('RC_CART_LAY') == "4") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds cart_lay4" for="cart_lay4"> <span>4 . Transparent icon</span></label>
                        </div></div>

                        <div class="roytc_row" style="margin-bottom: 20px; margin-top: 20px; display: inline-block;">
                              <label>Cart icon</label>
                              <div class="margin-form">
                                  <input type="radio" class="regular-radio" name="cart_icon" value="cart1" id="cart_icon1" ' . ((Configuration::get('RC_CART_ICON') == "cart1") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon cart_icon1" for="cart_icon1"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="cart_icon" value="cart2" id="cart_icon2" ' . ((Configuration::get('RC_CART_ICON') == "cart2") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon cart_icon2" for="cart_icon2"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="cart_icon" value="cart3" id="cart_icon3" ' . ((Configuration::get('RC_CART_ICON') == "cart3") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon cart_icon3" for="cart_icon3"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="cart_icon" value="cart4" id="cart_icon4" ' . ((Configuration::get('RC_CART_ICON') == "cart4") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon cart_icon4" for="cart_icon4"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="cart_icon" value="cart5" id="cart_icon5" ' . ((Configuration::get('RC_CART_ICON') == "cart5") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon cart_icon5" for="cart_icon5"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="cart_icon" value="cart6" id="cart_icon6" ' . ((Configuration::get('RC_CART_ICON') == "cart6") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon cart_icon6" for="cart_icon6"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="cart_icon" value="cart7" id="cart_icon7" ' . ((Configuration::get('RC_CART_ICON') == "cart7") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon cart_icon7" for="cart_icon7"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="cart_icon" value="cart8" id="cart_icon8" ' . ((Configuration::get('RC_CART_ICON') == "cart8") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon cart_icon8" for="cart_icon8"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="cart_icon" value="cart9" id="cart_icon9" ' . ((Configuration::get('RC_CART_ICON') == "cart9") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon cart_icon9" for="cart_icon9"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="cart_icon" value="cart10" id="cart_icon10" ' . ((Configuration::get('RC_CART_ICON') == "cart10") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon cart_icon10" for="cart_icon10"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="cart_icon" value="cart11" id="cart_icon11" ' . ((Configuration::get('RC_CART_ICON') == "cart11") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon cart_icon11" for="cart_icon11"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="cart_icon" value="cart12" id="cart_icon12" ' . ((Configuration::get('RC_CART_ICON') == "cart12") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon cart_icon12" for="cart_icon12"> <span></span></label>
                        </div></div>

                  <h3>' . $this->l('Cart colors') . '</h3>

              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
              <div class="half" style="width:620px; padding-bottom:0;">
                    <div class="roytc_row">
                    <label>Background</label>
                    <div class="margin-form">
                     <input type="color" name="cart_bg"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_CART_BG') . '" />
                    </div></div>
                    <div class="roytc_row">
                    <label>Border</label>
                    <div class="margin-form">
                     <input type="color" name="cart_b"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_CART_B') . '" />
                    </div></div>
                    <div class="roytc_row">
                    <label>Icon color</label>
                    <div class="margin-form">
                     <input type="color" name="cart_i"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_CART_I') . '" />
                    </div></div>
                    <div class="roytc_row">
                    <label>Text color</label>
                    <div class="margin-form">
                     <input type="color" name="cart_t"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_CART_T') . '" />
                    </div></div>
                    <div class="roytc_row">
                    <label>Quantity</label>
                    <div class="margin-form">
                     <input type="color" name="cart_q"  class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_CART_Q') . '" />
                    </div></div>
              </div><div class="half" style="width:620px; padding-bottom:0;">
                     <div class="roytc_row">
                     <label>Background hover</label>
                     <div class="margin-form">
                      <input type="color" name="cart_bg_hover"  class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_CART_BG_HOVER') . '" />
                     </div></div>
                    <div class="roytc_row">
                    <label>Border hover</label>
                    <div class="margin-form">
                     <input type="color" name="cart_b_hover"  class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_CART_B_HOVER') . '" />
                    </div></div>
                    <div class="roytc_row">
                    <label>Icon hover</label>
                    <div class="margin-form">
                     <input type="color" name="cart_i_hover"  class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_CART_I_HOVER') . '" />
                    </div></div>
                    <div class="roytc_row">
                    <label>Text hover</label>
                    <div class="margin-form">
                     <input type="color" name="cart_t_hover"  class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_CART_T_HOVER') . '" />
                    </div></div>
                    <div class="roytc_row">
                    <label>Quantity hover</label>
                    <div class="margin-form">
                     <input type="color" name="cart_q_hover"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_CART_Q_HOVER') . '" />
                    </div></div>
              </div></div>

                  </div>

            </div>

            <div class="tab-pane" id ="tab-side">
                  <h2 class="rtc_title2">' . $this->l('Side Navigation and Mobile layout') . '</h2>
                  <div class="nav_inside_container">
                        <ul class="nav_inside tabs">
                              <li class="inside_tab tab-side1 active">
                                    <a data-inside="tab" href="#tab-side1">Levi Box</a>
                              </li>
                              <li class="inside_tab tab-side2">
                                    <a data-inside="tab" href="#tab-side2">Mobile layout</a>
                              </li>
                        </ul>
                  </div>

                  <div class="tab-content hide" id="tab-side1">
                        <h3 class="first">' . $this->l('Position and appearance') . '</h3>

                  <div class="roytc_row ds_wrap">
                        <label>Position</label>
                        <div class="margin-form" style="margin-top:0; padding-top:10px;">
                            <input type="radio" class="regular-radio" name="levi_position" value="left" id="levi_position1" ' . ((Configuration::get('RC_LEVI_POSITION') == "left") ? 'checked="checked" ' : '') . ' />
                            <label class="ds levi1" for="levi_position1"> <span>1 . Left</span></label>
                            <input type="radio" class="regular-radio" name="levi_position" value="right" id="levi_position2" ' . ((Configuration::get('RC_LEVI_POSITION') == "right") ? 'checked="checked" ' : '') . ' />
                            <label class="ds levi2" for="levi_position2"> <span>2 . Right</span></label>
                  </div></div>

                        <h3>' . $this->l('Levibox colors') . '</h3>
                <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                              <label>Levibox Background</label>
                              <div class="margin-form">
                              <input type="color" name="nc_levi_bg"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_LEVI_BG') . '" />
                        </div></div>
                        <div class="roytc_row">
                              <label>Levibox Border</label>
                              <div class="margin-form">
                              <input type="color" name="nc_levi_border"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_LEVI_BORDER') . '" />
                        </div></div>
                </div><div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                              <label>Levibox Icons</label>
                              <div class="margin-form">
                              <input type="color" name="nc_levi_i"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_LEVI_I') . '" />
                        </div></div>
                        <div class="roytc_row">
                              <label>Levibox Icons hover</label>
                              <div class="margin-form">
                              <input type="color" name="nc_levi_i_hover"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_LEVI_I_HOVER') . '" />
                        </div></div>
                </div></div>
                <div class="hr">   </div>
                <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                              <label>Cart icon</label>
                              <div class="margin-form">
                              <input type="color" name="nc_levi_cart"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_LEVI_CART') . '" />
                        </div></div>
                </div><div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                              <label>Cart product inside</label>
                              <div class="margin-form">
                              <input type="color" name="nc_levi_cart_a"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_LEVI_CART_A') . '" />
                        </div></div>
                </div></div>


                <h3>' . $this->l('Side content colors') . '</h3>
                <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                              <label>Background</label>
                              <div class="margin-form">
                              <input type="color" name="nc_side_bg"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_SIDE_BG') . '" />
                        </div></div>
                        <div class="roytc_row">
                              <label>Titles / links</label>
                              <div class="margin-form">
                              <input type="color" name="nc_side_title"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_SIDE_TITLE') . '" />
                        </div></div>
                        <div class="roytc_row">
                              <label>Text</label>
                              <div class="margin-form">
                              <input type="color" name="nc_side_text"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_SIDE_TEXT') . '" />
                        </div></div>
                </div><div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                              <label>Comment / light text</label>
                              <div class="margin-form">
                              <input type="color" name="nc_side_light"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_SIDE_LIGHT') . '" />
                        </div></div>
                        <div class="roytc_row">
                              <label>Separators</label>
                              <div class="margin-form">
                              <input type="color" name="nc_side_sep"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_SIDE_SEP') . '" />
                        </div></div>
                </div></div>
                <div class="hr">   </div>
                <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                              <label>Close background</label>
                              <div class="margin-form">
                              <input type="color" name="nc_levi_close"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_LEVI_CLOSE') . '" />
                        </div></div>
                </div><div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                              <label>Close icon</label>
                              <div class="margin-form">
                              <input type="color" name="nc_levi_close_i"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_LEVI_CLOSE_I') . '" />
                        </div></div>
                </div></div>


              </div>

                  <div class="tab-content hide" id="tab-side2">

                      <h3 class="first">' . $this->l('Mobile Header layout') . '</h3>
                      <div class="roytc_row ds_wrap">
                            <label>Layout</label>
                            <div class="margin-form" style="margin-top:0; padding-top:10px;">
                                <input type="radio" class="regular-radio" name="nc_hemo" value="1" id="nc_hemo1" ' . ((Configuration::get('NC_HEMOS') == "1") ? 'checked="checked" ' : '') . ' />
                                <label class="ds hemo1" for="nc_hemo1"> <span>1 . Top and bottom sticky</span></label>
                                <input type="radio" class="regular-radio" name="nc_hemo" value="2" id="nc_hemo2" ' . ((Configuration::get('NC_HEMOS') == "2") ? 'checked="checked" ' : '') . ' />
                                <label class="ds hemo2" for="nc_hemo2"> <span>2 . Only bottom sticky</span></label>
                                <input type="radio" class="regular-radio" name="nc_hemo" value="3" id="nc_hemo3" ' . ((Configuration::get('NC_HEMOS') == "3") ? 'checked="checked" ' : '') . ' />
                                <label class="ds hemo3" for="nc_hemo3"> <span>3 . Menu Top sticky</span></label>
                      </div></div>

                        <h3 class="">' . $this->l('Mobile Logo') . '</h3>
                        <div class="roytc_row" style="margin-top:0;">
                              <label>Logo upload</label>
                              <div class="margin-form" style="margin-top:0;">
                                    <input id="logo_mobile_field2" type="file" name="logo_mobile_field2">
                                    <input id="logo_mobile_button2" type="submit" class="button" name="logo_mobile_button2" value="' . $this->l('Upload') . '">
                                    <p class="clear helpcontent">' . $this->l('Max height - 90px. If you use vertical header - logo can be higher. Preffered format - transparent .png') . '</p>
                              </div>';
        $logo_mobile_ext = Configuration::get('NC_LOGO_MOBILE');
        if ($logo_mobile_ext != "") {
            if (Shop::getContext() == Shop::CONTEXT_SHOP)
                $adv_imgname = 'logo-mobile' . '-' . (int)$this->context->shop->getContextShopID();

            $html .= '<label>Uploaded logo</label>
                                                      <div class="margin-form">
                                                      <img class="imgback" src="' . $this->_path . 'upload/' . $adv_imgname . '.' . $logo_mobile_ext . '" /><br /><br />
                                                      <input id="logo_mobile_delete2" type="submit" class="button" value="' . $this->l('Delete image') . '" name="logo_mobile_delete2">
                                                      </div>';
        }
        $html .= '
                        </div>
                        <h3>' . $this->l('Mobile header colors') . '</h3>
                        <div class="roytc_row">
                              <label>Header background</label>
                              <div class="margin-form">
                              <input type="color" name="nc_mob_header"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_MOB_HEADER') . '" />
                        </div></div>
                        <div class="roytc_row">
                              <label>Header Menu icon</label>
                              <div class="margin-form">
                              <input type="color" name="nc_mob_menu"  class="colorpicker" data-hex="true" value="' . Configuration::get('NC_MOB_MENU') . '" />
                        </div></div>

                        <h3>' . $this->l('Home product sliders') . '</h3>
                                <div class="roytc_row ds_wrap">
                                    <label>Products per row?</label>
                                    <div class="margin-form">
                                        <input type="radio" class="regular-radio" name="nc_mob_hp" id="nc_mob_hp1" value="1" ' . ((Configuration::get('NC_MOB_HP') == 1) ? 'checked="checked" ' : '') . '/>
                                        <label class="ds items_onrow1" for="nc_mob_hp1"><span>1</span></label>
                                        <input type="radio" class="regular-radio" name="nc_mob_hp" id="nc_mob_hp2" value="2" ' . ((Configuration::get('NC_MOB_HP') == 2) ? 'checked="checked" ' : '') . '/>
                                        <label class="ds items_onrow2" for="nc_mob_hp2"><span>2</span></label>
                                    </div></div>
                        <h3>' . $this->l('Category product list') . '</h3>
                                <div class="roytc_row ds_wrap">
                                    <label>Products per row?</label>
                                    <div class="margin-form">
                                        <input type="radio" class="regular-radio" name="nc_mob_cat" id="nc_mob_cat1" value="1" ' . ((Configuration::get('NC_MOB_CAT') == 1) ? 'checked="checked" ' : '') . '/>
                                        <label class="ds items_onrow1" for="nc_mob_cat1"><span>1</span></label>
                                        <input type="radio" class="regular-radio" name="nc_mob_cat" id="nc_mob_cat2" value="2" ' . ((Configuration::get('NC_MOB_CAT') == 2) ? 'checked="checked" ' : '') . '/>
                                        <label class="ds items_onrow2" for="nc_mob_cat2"><span>2</span></label>
                                    </div></div>
                  </div>

            </div>


            <div class="tab-pane" id ="tab-fonts">
                    <h2 class="rtc_title11">' . $this->l('Typography') . '</h2>

                  <div class="nav_inside_container">
                        <ul class="nav_inside tabs">
                              <li class="inside_tab tab-fonts1 active">
                                    <a data-inside="tab" href="#tab-fonts1">Font family</a>
                              </li>
                              <li class="inside_tab tab-fonts2">
                                    <a data-inside="tab" href="#tab-fonts2">Font size</a>
                              </li>
                              <li class="inside_tab tab-fonts3">
                                    <a data-inside="tab" href="#tab-fonts3">Text transform</a>
                              </li>
                              <li class="inside_tab tab-fonts4">
                                    <a data-inside="tab" href="#tab-fonts4">Font weight</a>
                              </li>
                              <li class="inside_tab tab-fonts5">
                                    <a data-inside="tab" href="#tab-fonts5">Font style</a>
                              </li>
                        </ul>
                  </div>

            <div class="tab-content hide" id="tab-fonts1">
                 <h3 class="first">' . $this->l('Font family') . '</h3>
                    <div class="roytc_row">
		        		<label>Headings and menu font</label>
                                <div class="margin-form">
                              ';
        $html .= $this->fontOptions($panel = "f_headings", $panelupper = "RC_F_HEADINGS");
        $html .= '
                                <p id="headingexample" class="fontshow" style="text-transform:uppercase;">' . $this->l('Headings font now looks like this ... ( Latin ext: ą, ć, ž, Š ... Cyrillic: я, ж, Щ, Ю )') . '</p>
                                <p class="clear helpcontent" style="margin-top:0.5em">' . $this->l('Choose font for headings, menu links and buttons. Default: Montserrat') . '</p>
                                </div>

                                <label>Buttons font</label>
                                <div class="margin-form">
                                ';
        $html .= $this->fontOptions($panel = "f_buttons", $panelupper = "RC_F_BUTTONS");
        $html .= '
                                <p id="buttonsexample" class="fontshow">' . $this->l('Button font now looks like this ... ( Latin ext: ą, ć, ž, Š ... Cyrillic: я, ж, Щ, Ю )') . '</p>
                                <p class="clear helpcontent" style="margin-top:0.5em">' . $this->l('Choose font for buttons. Default: Montserrat') . '</p>
                                </div>

                                <label>Text font</label>
                                <div class="margin-form">
                                ';
        $html .= $this->fontOptions($panel = "f_text", $panelupper = "RC_F_TEXT");
        $html .= '
                                <p id="textexample" class="fontshow">' . $this->l('Text font now looks like this ... ( Latin ext: ą, ć, ž, Š ... Cyrillic: я, ж, Щ, Ю )') . '</p>
                                <p class="clear helpcontent" style="margin-top:0.5em">' . $this->l('Choose font for body text. Default: Montserrat') . '</p>
                                </div>

                                <label>Price font</label>
                                <div class="margin-form">
                                ';
        $html .= $this->fontOptions($panel = "f_price", $panelupper = "RC_F_PRICE");
        $html .= '
                                <p id="priceexample" class="fontshow">' . $this->l('98$ 134,25€ 786£ 455Руб') . '</p>
                                <p class="clear helpcontent" style="margin-top:0.5em">' . $this->l('Choose special font for price. Default: Montserrat') . '</p>
                                </div>

                                <label>Product names in category</label>
                                <div class="margin-form">
                                ';
        $html .= $this->fontOptions($panel = "f_pn", $panelupper = "RC_F_PN");
        $html .= '
                                <p id="pnexample" class="fontshow">' . $this->l('Product name') . '</p>
                                <p class="clear helpcontent" style="margin-top:0.5em">' . $this->l('Choose special font for product names. Default: Montserrat') . '</p>
                        </div>

                    </div>
      </div>
      <div class="tab-content hide" id="tab-fonts2">
                 <h3 class="first">' . $this->l('Font size') . '</h3>
            <div class="half_container low_paddings" style="display:inline-block; width:100%">
            <div class="half">
                    <div class="roytc_row">
                         <label>Body font size</label>
                         <div class="margin-form">
                            <input type="text" name="font_size_body" id="font_size_body" value="' . Configuration::get('RC_FONT_SIZE_BODY') . '" />px
                            <p class="clear helpcontent">' . $this->l('Default: 16px') . '</p>
                         </div>
                    </div>
                    <div class="roytc_row">
                        <label>Headings and titles</label>
                        <div class="margin-form">
                        <input type="text" name="font_size_head" id="font_size_head" value="' . Configuration::get('RC_FONT_SIZE_HEAD') . '" />px
                        <p class="clear helpcontent">' . $this->l('Default: 24px') . '</p>
                        </div>
                    </div>
                    <div class="roytc_row">
                        <label>Product name in category</label>
                        <div class="margin-form">
                        <input type="text" name="font_size_pn" id="font_size_pn" value="' . Configuration::get('RC_FONT_SIZE_PN') . '" />px
                        <p class="clear helpcontent">' . $this->l('Default: 16px') . '</p>
                        </div>
                    </div>
                    <div class="roytc_row">
                        <label>Product list price</label>
                        <div class="margin-form">
                        <input type="text" name="font_size_price" id="font_size_price" value="' . Configuration::get('RC_FONT_SIZE_PRICE') . '" />px
                        <p class="clear helpcontent">' . $this->l('Default: 24px') . '</p>
                        </div>
                    </div>
            </div>
            <div class="half half_right">
                    <div class="roytc_row">
                        <label>Buttons font size</label>
                        <div class="margin-form">
                        <input type="text" name="font_size_buttons" id="font_size_buttons" value="' . Configuration::get('RC_FONT_SIZE_BUTTONS') . '" />px
                        <p class="clear helpcontent">' . $this->l('Default: 20px') . '</p>
                        </div>
                    </div>
                    <div class="roytc_row">
                        <label>Product page price</label>
                        <div class="margin-form">
                        <input type="text" name="font_size_pp" id="font_size_pp" value="' . Configuration::get('RC_FONT_SIZE_PP') . '" />px
                        <p class="clear helpcontent">' . $this->l('Default: 36px') . '</p>
                        </div>
                    </div>
                    <div class="roytc_row">
                        <label>Product page title</label>
                        <div class="margin-form">
                        <input type="text" name="font_size_prod" id="font_size_prod" value="' . Configuration::get('RC_FONT_SIZE_PROD') . '" />px
                        <p class="clear helpcontent">' . $this->l('Default: 24px') . '</p>
                        </div>
                    </div>
            </div>
            </div>
      </div>
      <div class="tab-content hide" id="tab-fonts3">
                 <h3 class="first">' . $this->l('UPPERCASE or Normal text?') . '</h3>
            <div class="half_container low_paddings" style="display:inline-block; width:100%">
            <div class="half" style="width:680px;">
                     <div class="roytc_row">
                        <label>Top Menu elements</label>
                        <div class="margin-form" style="margin-top:0; padding-top:10px;">
                            <input type="radio" class="regular-radio" name="nc_up_menu" value="1" id="nc_up_menu1" ' . ((Configuration::get('NC_UP_MENU') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode1" for="nc_up_menu1"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_up_menu" value="2" id="nc_up_menu2" ' . ((Configuration::get('NC_UP_MENU') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode2" for="nc_up_menu2"> <span style="color:#00a380"></span></label>
                     </div></div>
                     <div class="roytc_row">
                        <label>Headings</label>
                        <div class="margin-form" style="margin-top:70px; padding-top:10px;">
                            <input type="radio" class="regular-radio" name="nc_up_head" value="1" id="nc_up_head1" ' . ((Configuration::get('NC_UP_HEAD') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode1" for="nc_up_head1"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_up_head" value="2" id="nc_up_head2" ' . ((Configuration::get('NC_UP_HEAD') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode2" for="nc_up_head2"> <span style="color:#00a380"></span></label>
                     </div></div>
                     <div class="roytc_row">
                        <label>Homepage product blocks titles</label>
                        <div class="margin-form" style="margin-top:70px; padding-top:10px;">
                            <input type="radio" class="regular-radio" name="nc_up_hp" value="1" id="nc_up_hp1" ' . ((Configuration::get('NC_UP_HP') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode1" for="nc_up_hp1"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_up_hp" value="2" id="nc_up_hp2" ' . ((Configuration::get('NC_UP_HP') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode2" for="nc_up_hp2"> <span style="color:#00a380"></span></label>
                     </div></div>
                     <div class="roytc_row">
                        <label>Footer titles</label>
                        <div class="margin-form" style="margin-top:70px; padding-top:10px;">
                            <input type="radio" class="regular-radio" name="nc_up_f" value="1" id="nc_up_f1" ' . ((Configuration::get('NC_UP_F') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode1" for="nc_up_f1"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_up_f" value="2" id="nc_up_f2" ' . ((Configuration::get('NC_UP_F') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode2" for="nc_up_f2"> <span style="color:#00a380"></span></label>
                     </div></div>
                     <div class="roytc_row">
                        <label>Blog post titles</label>
                        <div class="margin-form" style="margin-top:70px; padding-top:10px;">
                            <input type="radio" class="regular-radio" name="nc_up_bp" value="1" id="nc_up_bp1" ' . ((Configuration::get('NC_UP_BP') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode1" for="nc_up_bp1"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_up_bp" value="2" id="nc_up_bp2" ' . ((Configuration::get('NC_UP_BP') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode2" for="nc_up_bp2"> <span style="color:#00a380"></span></label>
                     </div></div>
            </div>
            <div class="half half_right" style="width:620px">
                     <div class="roytc_row">
                        <label style="width:220px">Buttons</label>
                        <div class="margin-form" style="margin-top:0; padding-top:10px; padding-left: 230px!important">
                            <input type="radio" class="regular-radio" name="nc_up_but" value="1" id="nc_up_but1" ' . ((Configuration::get('NC_UP_BUT') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode1" for="nc_up_but1"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_up_but" value="2" id="nc_up_but2" ' . ((Configuration::get('NC_UP_BUT') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode2" for="nc_up_but2"> <span style="color:#00a380"></span></label>
                     </div></div>
                     <div class="roytc_row">
                        <label style="width:220px">Product names in category</label>
                        <div class="margin-form" style="margin-top:70px; padding-top:10px; padding-left: 230px!important">
                            <input type="radio" class="regular-radio" name="nc_up_nc" value="1" id="nc_up_nc1" ' . ((Configuration::get('NC_UP_NC') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode1" for="nc_up_nc1"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_up_nc" value="2" id="nc_up_nc2" ' . ((Configuration::get('NC_UP_NC') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode2" for="nc_up_nc2"> <span style="color:#00a380"></span></label>
                     </div></div>
                     <div class="roytc_row">
                        <label style="width:220px">Product name on product page</label>
                        <div class="margin-form" style="margin-top:70px; padding-top:10px; padding-left: 230px!important">
                            <input type="radio" class="regular-radio" name="nc_up_np" value="1" id="nc_up_np1" ' . ((Configuration::get('NC_UP_NP') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode1" for="nc_up_np1"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_up_np" value="2" id="nc_up_np2" ' . ((Configuration::get('NC_UP_NP') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode2" for="nc_up_np2"> <span style="color:#00a380"></span></label>
                     </div></div>
                     <div class="roytc_row">
                        <label style="width:220px">Info tabs on product page</label>
                        <div class="margin-form" style="margin-top:70px; padding-top:10px; padding-left: 230px!important">
                            <input type="radio" class="regular-radio" name="nc_up_mi" value="1" id="nc_up_mi1" ' . ((Configuration::get('NC_UP_MI') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode1" for="nc_up_mi1"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_up_mi" value="2" id="nc_up_mi2" ' . ((Configuration::get('NC_UP_MI') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds upmode2" for="nc_up_mi2"> <span style="color:#00a380"></span></label>
                     </div></div>
            </div>
            </div>
      </div>

      <div class="tab-content hide" id="tab-fonts4">
                 <h3 class="first">' . $this->l('Font weight') . '</h3>
                    <div class="roytc_row" style="margin-top:0; margin-bottom:0;">
                        <div class="margin-form" style="margin-top:0; margin-bottom:0;">
                            <p class="clear helpcontent" style="margin-top:0; margin-bottom:0;">Font weights: <span style="font-weight:300">Thin (300)</span>, <span style="font-weight:400">Regular (400)</span>, <span style="font-weight:500">Medium (500)</span>, <span style="font-weight:600">Semibold (600)</span>, <span style="font-weight:700">Bold (700)</span>. <br /><br />If your current font not support selected weight, the closest supported weight will be applied.</p>
                        </div>
                    </div>
            <div class="half_container low_paddings" style="display:inline-block; width:100%">
            <div class="half" style="padding-bottom:0">
                  <div class="roytc_row font_weight">
                        <label>Headings</label>
                        <div class="margin-form">
                              <input type="text" id="nc_fw_heading" name="nc_fw_heading" readonly class="slider_val" value="' . Configuration::get('NC_FW_HEADING') . '" />
                              <div id="slider_nc_fw_heading"></div>
                        </div>
                  </div>
                  <div class="roytc_row font_weight">
                        <label>Menu elements</label>
                        <div class="margin-form">
                              <input type="text" id="nc_fw_menu" name="nc_fw_menu" readonly class="slider_val" value="' . Configuration::get('NC_FW_MENU') . '" />
                              <div id="slider_nc_fw_menu"></div>
                        </div>
                  </div>
                  <div class="roytc_row font_weight">
                        <label>Buttons</label>
                        <div class="margin-form">
                              <input type="text" id="nc_fw_but" name="nc_fw_but" readonly class="slider_val" value="' . Configuration::get('NC_FW_BUT') . '" />
                              <div id="slider_nc_fw_but"></div>
                        </div>
                  </div>
            </div>
            <div class="half half_right" style="padding-bottom:0">
                  <div class="roytc_row font_weight">
                        <label>Normal text</label>
                        <div class="margin-form">
                              <input type="text" id="nc_fw_ct" name="nc_fw_ct" readonly class="slider_val" value="' . Configuration::get('NC_FW_CT') . '" />
                              <div id="slider_nc_fw_ct"></div>
                        </div>
                  </div>
                  <div class="roytc_row font_weight">
                        <label>Product name</label>
                        <div class="margin-form">
                              <input type="text" id="nc_fw_pn" name="nc_fw_pn" readonly class="slider_val" value="' . Configuration::get('NC_FW_PN') . '" />
                              <div id="slider_nc_fw_pn"></div>
                        </div>
                  </div>
                  <div class="roytc_row font_weight">
                        <label>Prices</label>
                        <div class="margin-form">
                              <input type="text" id="nc_fw_price" name="nc_fw_price" readonly class="slider_val" value="' . Configuration::get('NC_FW_PRICE') . '" />
                              <div id="slider_nc_fw_price"></div>
                        </div>
                  </div>
            </div>
            </div>

      </div>
      <div class="tab-content hide" id="tab-fonts5">
                 <h3 class="first">' . $this->l('Font style') . '</h3>
            <div class="half_container low_paddings" style="display:inline-block; width:100%">
            <div class="half" style="padding-bottom:0;">
                     <div class="roytc_row">
                        <label>Italic style for product names?</label>
                        <div class="margin-form" style="margin-top:0; padding-top:10px;">
                            <input type="radio" class="regular-radio" name="nc_ital_pn" value="1" id="nc_ital_pn1" ' . ((Configuration::get('NC_ITAL_PN') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds it_mode1" for="nc_ital_pn1"> <span></span></label>
                            <input type="radio" class="regular-radio" name="nc_ital_pn" value="2" id="nc_ital_pn2" ' . ((Configuration::get('NC_ITAL_PN') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds it_mode2" for="nc_ital_pn2"> <span></span></label>
                     </div></div>
            </div>
            <div class="half half_right" style="padding-bottom:0;">
            <div class="roytc_row">
               <label>Italic style for product price?</label>
               <div class="margin-form" style="margin-top:0; padding-top:10px;">
                   <input type="radio" class="regular-radio" name="nc_italic_pp" value="1" id="nc_italic_pp1" ' . ((Configuration::get('NC_ITALIC_PP') == "1") ? 'checked="checked" ' : '') . ' />
                   <label class="ds it_mode1" for="nc_italic_pp1"> <span></span></label>
                   <input type="radio" class="regular-radio" name="nc_italic_pp" value="2" id="nc_italic_pp2" ' . ((Configuration::get('NC_ITALIC_PP') == "2") ? 'checked="checked" ' : '') . ' />
                   <label class="ds it_mode2" for="nc_italic_pp2"> <span></span></label>
               </div>
            </div>
            </div>
            </div>

              <div class="roytc_row" style="margin-top:0; margin-bottom:0;">
                  <div class="margin-form" style="margin-top:0; margin-bottom:0;">
                      <p class="clear helpcontent" style="margin-bottom:0;">Italic style <em>looks like this</em>.</p>
                  </div>
              </div>


                 <h3 style="margin-top:40px;">' . $this->l('Letter Spacing') . '</h3>
            <div class="half_container low_paddings" style="display:inline-block; width:100%">
            <div class="half" style="padding-bottom:0;">
                  <div class="roytc_row">
                        <label>Body letter-spacing</label>
                        <div class="margin-form">
                              <input type="text" name="nc_ls"  value="' . Configuration::get('NC_LS') . '" />px
                        </div>
                  </div>
                  <div class="roytc_row">
                        <label>Headings letter-spacing</label>
                        <div class="margin-form">
                              <input type="text" name="nc_ls_h"  value="' . Configuration::get('NC_LS_H') . '" />px
                        </div>
                  </div>
                  <div class="roytc_row">
                        <label>Menu elements</label>
                        <div class="margin-form">
                              <input type="text" name="nc_ls_m"  value="' . Configuration::get('NC_LS_M') . '" />px
                        </div>
                  </div>
            </div>
            <div class="half half_right" style="padding-bottom:0;">
                  <div class="roytc_row">
                        <label>Titles letter-spacing</label>
                        <div class="margin-form">
                              <input type="text" name="nc_ls_t"  value="' . Configuration::get('NC_LS_T') . '" />px
                        </div>
                  </div>
                  <div class="roytc_row">
                        <label>Buttons letter-spacing</label>
                        <div class="margin-form">
                              <input type="text" name="nc_ls_b"  value="' . Configuration::get('NC_LS_B') . '" />px
                        </div>
                  </div>
                  <div class="roytc_row">
                        <label>Product names in category</label>
                        <div class="margin-form">
                              <input type="text" name="nc_ls_p"  value="' . Configuration::get('NC_LS_P') . '" />px
                        </div>
                  </div>
            </div>
            </div>
                    <div class="roytc_row" style="margin-top:0; margin-bottom:0;">
                        <div class="margin-form" style="margin-top:0; margin-bottom:0;">
                            <p class="clear helpcontent" style="margin-bottom:30px;">Recommended values 0-2 pixels.</p>
                        </div>
                    </div>

      </div>
            </div>

            <div class="tab-pane" id ="tab-homepage">
                 <h2 class="rtc_title3">' . $this->l('Homepage content') . '</h2>
                  <div class="nav_inside_container">
                        <ul class="nav_inside tabs">
                              <li class="inside_tab tab-homepage2 active">
                                    <a data-inside="tab" href="#tab-homepage2">Content module</a>
                              </li>
                              <li class="inside_tab tab-homepage3">
                                    <a data-inside="tab" href="#tab-homepage3">Product sliders</a>
                              </li>
                              <li class="inside_tab tab-homepage6">
                                    <a data-inside="tab" href="#tab-homepage6">Brand slider</a>
                              </li>
                        </ul>
                  </div>

            <div class="tab-content hide" id="tab-homepage2">

                  <p class="clear helpcontent" style="margin-left:60px; margin-bottom:40px; margin-top:60px">Here you can set settings for banners in each hook. You can set spacing from top and bottom of this group of banners and between them.</p>


                    <h3>' . $this->l('Before Header content/banners ') . '</h3>
                <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                  <div class="half" style="width:680px; padding-bottom:0;">
                     <div class="roytc_row">
                          <label>Top spacing</label>
                          <div class="margin-form">
                          <input type="text" name="ban_ts_behead" value="' . Configuration::get('RC_BAN_TS_BEHEAD') . '" /> px
                     </div></div>
                     <div class="roytc_row">
                          <label>Bottom spacing</label>
                          <div class="margin-form">
                          <input type="text" name="ban_bs_behead" value="' . Configuration::get('RC_BAN_BS_BEHEAD') . '" /> px
                     </div></div>
                 </div><div class="half" style="width:620px; padding-bottom:0;">
                <div class="roytc_row">
                      <label>Spacing between banners?</label>
                      <div class="margin-form">
                            <input type="radio" class="regular-radio" name="ban_spa_behead" id="ban_spa_behead1" value="1" ' . ((Configuration::get('RC_BAN_SPA_BEHEAD') == "1") ? 'checked="checked" ' : '') . '/>
                            <label class="t" for="ban_spa_behead1"> Yes</label>
                            <input type="radio" class="regular-radio" name="ban_spa_behead" id="ban_spa_behead2" value="2" ' . ((Configuration::get('RC_BAN_SPA_BEHEAD') == "2") ? 'checked="checked" ' : '') . '/>
                            <label class="t" for="ban_spa_behead2"> No</label>
                </div></div>
                </div></div>


                                  <h3>' . $this->l('Top hook content/banners') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:680px; padding-bottom:0;">
                   <div class="roytc_row">
                        <label>Top spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_ts_top" value="' . Configuration::get('RC_BAN_TS_TOP') . '" /> px
                   </div></div>
                   <div class="roytc_row">
                        <label>Bottom spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_bs_top" value="' . Configuration::get('RC_BAN_BS_TOP') . '" /> px
                   </div></div>
               </div><div class="half" style="width:620px; padding-bottom:0;">
                  <div class="roytc_row">
                        <label>Spacing between banners?</label>
                        <div class="margin-form">
                              <input type="radio" class="regular-radio" name="ban_spa_top" id="ban_spa_top1" value="1" ' . ((Configuration::get('RC_BAN_SPA_TOP') == "1") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="ban_spa_top1"> Yes</label>
                              <input type="radio" class="regular-radio" name="ban_spa_top" id="ban_spa_top2" value="2" ' . ((Configuration::get('RC_BAN_SPA_TOP') == "2") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="ban_spa_top2"> No</label>
                  </div></div>
              </div></div>


                     <h3>' . $this->l('Left column content/banners') . '</h3>
                 <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                     <div class="half" style="width:680px; padding-bottom:0;">
                           <div class="roytc_row">
                                <label>Top spacing</label>
                                <div class="margin-form">
                                <input type="text" name="ban_ts_left" value="' . Configuration::get('RC_BAN_TS_LEFT') . '" /> px
                           </div></div>
                     </div><div class="half" style="width:620px; padding-bottom:0;">
                           <div class="roytc_row">
                                <label>Bottom spacing</label>
                                <div class="margin-form">
                                <input type="text" name="ban_bs_left" value="' . Configuration::get('RC_BAN_BS_LEFT') . '" /> px
                           </div></div>
                 </div></div>
                     <h3>' . $this->l('Right column content/banners') . '</h3>
                 <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                     <div class="half" style="width:680px; padding-bottom:0;">
                           <div class="roytc_row">
                                <label>Top spacing</label>
                                <div class="margin-form">
                                <input type="text" name="ban_ts_right" value="' . Configuration::get('RC_BAN_TS_RIGHT') . '" /> px
                           </div></div>
                     </div><div class="half" style="width:620px; padding-bottom:0;">
                           <div class="roytc_row">
                                <label>Bottom spacing</label>
                                <div class="margin-form">
                                <input type="text" name="ban_bs_right" value="' . Configuration::get('RC_BAN_BS_RIGHT') . '" /> px
                           </div></div>
                 </div></div>


                  <h3>' . $this->l('Home products tab content/banners') . '</h3>

              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:680px; padding-bottom:0;">
                   <div class="roytc_row">
                        <label>Top spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_ts_pro" value="' . Configuration::get('RC_BAN_TS_PRO') . '" /> px
                   </div></div>
                   <div class="roytc_row">
                        <label>Bottom spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_bs_pro" value="' . Configuration::get('RC_BAN_BS_PRO') . '" /> px
                   </div></div>
               </div><div class="half" style="width:620px; padding-bottom:0;">
                    <div class="roytc_row">
                          <label>Spacing between banners?</label>
                          <div class="margin-form">
                                <input type="radio" class="regular-radio" name="ban_spa_pro" id="ban_spa_pro1" value="1" ' . ((Configuration::get('RC_BAN_SPA_PRO') == "1") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_pro1"> Yes</label>
                                <input type="radio" class="regular-radio" name="ban_spa_pro" id="ban_spa_pro2" value="2" ' . ((Configuration::get('RC_BAN_SPA_PRO') == "2") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_pro2"> No</label>
                    </div></div>
              </div></div>


                  <h3>' . $this->l('Before Footer content/banners') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:680px; padding-bottom:0;">
                   <div class="roytc_row">
                        <label>Top spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_ts_befoot" value="' . Configuration::get('RC_BAN_TS_BEFOOT') . '" /> px
                   </div></div>
                   <div class="roytc_row">
                        <label>Bottom spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_bs_befoot" value="' . Configuration::get('RC_BAN_BS_BEFOOT') . '" /> px
                   </div></div>
               </div><div class="half" style="width:620px; padding-bottom:0;">
                    <div class="roytc_row">
                          <label>Spacing between banners?</label>
                          <div class="margin-form">
                                <input type="radio" class="regular-radio ban_spa_befoot" name="ban_spa_befoot" id="ban_spa_befoot1" value="1" ' . ((Configuration::get('RC_BAN_SPA_BEFOOT') == "1") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_befoot1"> Yes</label>
                                <input type="radio" class="regular-radio ban_spa_befoot" name="ban_spa_befoot" id="ban_spa_befoot2" value="2" ' . ((Configuration::get('RC_BAN_SPA_BEFOOT') == "2") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_befoot2"> No</label>
                    </div></div>
              </div></div>


                  <h3>' . $this->l('Footer hook content/banners') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:680px; padding-bottom:0;">
                   <div class="roytc_row">
                        <label>Top spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_ts_foot" value="' . Configuration::get('RC_BAN_TS_FOOT') . '" /> px
                   </div></div>
                   <div class="roytc_row">
                        <label>Bottom spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_bs_foot" value="' . Configuration::get('RC_BAN_BS_FOOT') . '" /> px
                   </div></div>
               </div><div class="half" style="width:620px; padding-bottom:0;">
                  <div class="roytc_row">
                        <label>Spacing between banners?</label>
                        <div class="margin-form">
                              <input type="radio" class="regular-radio ban_spa_foot" name="ban_spa_foot" id="ban_spa_foot1" value="1" ' . ((Configuration::get('RC_BAN_SPA_FOOT') == "1") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="ban_spa_foot1"> Yes</label>
                              <input type="radio" class="regular-radio ban_spa_foot" name="ban_spa_foot" id="ban_spa_foot2" value="2" ' . ((Configuration::get('RC_BAN_SPA_FOOT') == "2") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="ban_spa_foot2"> No</label>
                  </div></div>
              </div></div>


                  <h3>' . $this->l('Side cart content/banners') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:680px; padding-bottom:0;">
                   <div class="roytc_row">
                        <label>Top spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_ts_sidecart" value="' . Configuration::get('RC_BAN_TS_SIDECART') . '" /> px
                   </div></div>
                   <div class="roytc_row">
                        <label>Bottom spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_bs_sidecart" value="' . Configuration::get('RC_BAN_BS_SIDECART') . '" /> px
                   </div></div>
               </div><div class="half" style="width:620px; padding-bottom:0;">
                    <div class="roytc_row">
                          <label>Spacing between banners?</label>
                          <div class="margin-form">
                                <input type="radio" class="regular-radio ban_spa_sidescart" name="ban_spa_sidescart" id="ban_spa_sidescart1" value="1" ' . ((Configuration::get('RC_BAN_SPA_SIDECART') == "1") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_sidescart1"> Yes</label>
                                <input type="radio" class="regular-radio ban_spa_sidescart" name="ban_spa_sidescart" id="ban_spa_sidescart2" value="2" ' . ((Configuration::get('RC_BAN_SPA_SIDECART') == "2") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_sidescart2"> No</label>
                    </div></div>
              </div></div>

                  <h3>' . $this->l('Side search content/banners') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:680px; padding-bottom:0;">
                   <div class="roytc_row">
                        <label>Top spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_ts_sidesearch" value="' . Configuration::get('RC_BAN_TS_SIDESEARCH') . '" /> px
                   </div></div>
                   <div class="roytc_row">
                        <label>Bottom spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_bs_sidesearch" value="' . Configuration::get('RC_BAN_BS_SIDESEARCH') . '" /> px
                   </div></div>
               </div><div class="half" style="width:620px; padding-bottom:0;">
                    <div class="roytc_row">
                          <label>Spacing between banners?</label>
                          <div class="margin-form">
                                <input type="radio" class="regular-radio ban_spa_sidesearch" name="ban_spa_sidesearch" id="ban_spa_sidesearch1" value="1" ' . ((Configuration::get('RC_BAN_SPA_SIDESEARCH') == "1") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_sidesearch1"> Yes</label>
                                <input type="radio" class="regular-radio ban_spa_sidesearch" name="ban_spa_sidesearch" id="ban_spa_sidesearch2" value="2" ' . ((Configuration::get('RC_BAN_SPA_SIDESEARCH') == "2") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_sidesearch2"> No</label>
                    </div></div>
              </div></div>

                  <h3>' . $this->l('Side mail content/banners') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:680px; padding-bottom:0;">
                   <div class="roytc_row">
                        <label>Top spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_ts_sidemail" value="' . Configuration::get('RC_BAN_TS_SIDEMAIL') . '" /> px
                   </div></div>
                   <div class="roytc_row">
                        <label>Bottom spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_bs_sidemail" value="' . Configuration::get('RC_BAN_BS_SIDEMAIL') . '" /> px
                   </div></div>
               </div><div class="half" style="width:620px; padding-bottom:0;">
                    <div class="roytc_row">
                          <label>Spacing between banners?</label>
                          <div class="margin-form">
                                <input type="radio" class="regular-radio" name="ban_spa_sidemail" id="ban_spa_sidemail1" value="1" ' . ((Configuration::get('RC_BAN_SPA_SIDEMAIL') == "1") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_sidemail1"> Yes</label>
                                <input type="radio" class="regular-radio" name="ban_spa_sidemail" id="ban_spa_sidemail2" value="2" ' . ((Configuration::get('RC_BAN_SPA_SIDEMAIL') == "2") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_sidemail2"> No</label>
                    </div></div>
              </div></div>

                  <h3>' . $this->l('Side mobile menu content/banners') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:680px; padding-bottom:0;">
                   <div class="roytc_row">
                        <label>Top spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_ts_sidemobilemenu" value="' . Configuration::get('RC_BAN_TS_SIDEMOBILEMENU') . '" /> px
                   </div></div>
                   <div class="roytc_row">
                        <label>Bottom spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_bs_sidemobilemenu" value="' . Configuration::get('RC_BAN_BS_SIDEMOBILEMENU') . '" /> px
                   </div></div>
               </div><div class="half" style="width:620px; padding-bottom:0;">
                    <div class="roytc_row">
                          <label>Spacing between banners?</label>
                          <div class="margin-form">
                                <input type="radio" class="regular-radio" name="ban_spa_sidemobilemenu" id="ban_spa_sidemobilemenu1" value="1" ' . ((Configuration::get('RC_BAN_SPA_SIDEMOBILEMENU') == "1") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_sidemobilemenu1"> Yes</label>
                                <input type="radio" class="regular-radio" name="ban_spa_sidemobilemenu" id="ban_spa_sidemobilemenu2" value="2" ' . ((Configuration::get('RC_BAN_SPA_SIDEMOBILEMENU') == "2") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_sidemobilemenu2"> No</label>
                    </div></div>
              </div></div>


                  <h3>' . $this->l('Product page Before buy content/banners') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:680px; padding-bottom:0;">
                   <div class="roytc_row">
                        <label>Top spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_ts_product" value="' . Configuration::get('RC_BAN_TS_PRODUCT') . '" /> px
                   </div></div>
                   <div class="roytc_row">
                        <label>Bottom spacing</label>
                        <div class="margin-form">
                        <input type="text" name="ban_bs_product" value="' . Configuration::get('RC_BAN_BS_PRODUCT') . '" /> px
                   </div></div>
               </div><div class="half" style="width:620px; padding-bottom:0;">
                    <div class="roytc_row">
                          <label>Spacing between banners?</label>
                          <div class="margin-form">
                                <input type="radio" class="regular-radio" name="ban_spa_product" id="ban_spa_product1" value="1" ' . ((Configuration::get('RC_BAN_SPA_PRODUCT') == "1") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_product1"> Yes</label>
                                <input type="radio" class="regular-radio" name="ban_spa_product" id="ban_spa_product2" value="2" ' . ((Configuration::get('RC_BAN_SPA_PRODUCT') == "2") ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="ban_spa_product2"> No</label>
                    </div></div>
              </div></div>

            </div>




            <div class="tab-content hide" id="tab-homepage3">

                  <h3 class="first">' . $this->l('Featured Products slider') . '</h3>

              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:600px; padding-bottom:0;">
                  <div class="roytc_row">
				<label>Enable slider?</label>
				<div class="margin-form">
					<input type="radio" class="regular-radio" name="nc_carousel_featured" id="nc_carousel_featured_1" value="1" ' . ((Configuration::get('NC_CAROUSEL_FEATUREDS') == "1") ? 'checked="checked" ' : '') . '/>
					<label class="t" for="nc_carousel_featured_1"> Yes</label>
					<input type="radio" class="regular-radio" name="nc_carousel_featured" id="nc_carousel_featured_2" value="2" ' . ((Configuration::get('NC_CAROUSEL_FEATUREDS') == "2") ? 'checked="checked" ' : '') . '/>
					<label class="t" for="nc_carousel_featured_2"> No</label>
				</div></div>
                  <div class="roytc_row">
				<label>Enable autoscroll?</label>
				<div class="margin-form">
					<input type="radio" class="regular-radio" name="nc_auto_featured" id="nc_auto_featured_1" value="true" ' . ((Configuration::get('NC_AUTO_FEATURED') == "true") ? 'checked="checked" ' : '') . '/>
					<label class="t" for="nc_auto_featured_1"> Yes</label>
					<input type="radio" class="regular-radio" name="nc_auto_featured" id="nc_auto_featured_0" value="false" ' . ((Configuration::get('NC_AUTO_FEATURED') == "false") ? 'checked="checked" ' : '') . '/>
					<label class="t" for="nc_auto_featured_0"> No</label>
				</div></div>

              </div></div>

        <div class="roytc_row ds_wrap">
            <label>Products per row?</label>
            <div class="margin-form">
                <input type="radio" class="regular-radio" name="nc_items_featured" id="nc_items_featured2" value="2" ' . ((Configuration::get('NC_ITEMS_FEATUREDS') == 2) ? 'checked="checked" ' : '') . '/>
                <label class="ds items_onrow2" for="nc_items_featured2"><span>2</span></label>
                <input type="radio" class="regular-radio" name="nc_items_featured" id="nc_items_featured3" value="3" ' . ((Configuration::get('NC_ITEMS_FEATUREDS') == 3) ? 'checked="checked" ' : '') . '/>
                <label class="ds items_onrow3" for="nc_items_featured3"><span>3</span></label>
                <input type="radio" class="regular-radio" name="nc_items_featured" id="nc_items_featured4" value="4" ' . ((Configuration::get('NC_ITEMS_FEATUREDS') == 4) ? 'checked="checked" ' : '') . '/>
                <label class="ds items_onrow4" for="nc_items_featured4"><span>4</span></label>
                <input type="radio" class="regular-radio" name="nc_items_featured" id="nc_items_featured5" value="5" ' . ((Configuration::get('NC_ITEMS_FEATUREDS') == 5) ? 'checked="checked" ' : '') . '/>
                <label class="ds items_onrow5" for="nc_items_featured5"><span>5</span></label>
            </div></div>


                  <h3>' . $this->l('Best sellers Products slider') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:600px; padding-bottom:0;">
                  <div class="roytc_row">
				<label>Enable slider?</label>
				<div class="margin-form">
					<input type="radio" class="regular-radio" name="nc_carousel_best" id="nc_carousel_best_1" value="1" ' . ((Configuration::get('NC_CAROUSEL_BEST') == "1") ? 'checked="checked" ' : '') . '/>
					<label class="t" for="nc_carousel_best_1"> Yes</label>
					<input type="radio" class="regular-radio" name="nc_carousel_best" id="nc_carousel_best_2" value="2" ' . ((Configuration::get('NC_CAROUSEL_BEST') == "2") ? 'checked="checked" ' : '') . '/>
					<label class="t" for="nc_carousel_best_2"> No</label>
				</div></div>
                  <div class="roytc_row">
				<label>Enable autoscroll?</label>
				<div class="margin-form">
					<input type="radio" class="regular-radio" name="nc_auto_best" id="nc_auto_best_1" value="true" ' . ((Configuration::get('NC_AUTO_BEST') == "true") ? 'checked="checked" ' : '') . '/>
					<label class="t" for="nc_auto_best_1"> Yes</label>
					<input type="radio" class="regular-radio" name="nc_auto_best" id="nc_auto_best_0" value="false" ' . ((Configuration::get('NC_AUTO_BEST') == "false") ? 'checked="checked" ' : '') . '/>
					<label class="t" for="nc_auto_best_0"> No</label>
				</div></div>
               </div><div class="half" style="padding-bottom:0;">
              </div></div>
                      <div class="roytc_row ds_wrap">
                          <label>Products per row?</label>
                          <div class="margin-form">
                              <input type="radio" class="regular-radio" name="nc_items_best" id="nc_items_best2" value="2" ' . ((Configuration::get('NC_ITEMS_BESTS') == 2) ? 'checked="checked" ' : '') . '/>
                              <label class="ds items_onrow2" for="nc_items_best2"><span>2</span></label>
                              <input type="radio" class="regular-radio" name="nc_items_best" id="nc_items_best3" value="3" ' . ((Configuration::get('NC_ITEMS_BESTS') == 3) ? 'checked="checked" ' : '') . '/>
                              <label class="ds items_onrow3" for="nc_items_best3"><span>3</span></label>
                              <input type="radio" class="regular-radio" name="nc_items_best" id="nc_items_best4" value="4" ' . ((Configuration::get('NC_ITEMS_BESTS') == 4) ? 'checked="checked" ' : '') . '/>
                              <label class="ds items_onrow4" for="nc_items_best4"><span>4</span></label>
                              <input type="radio" class="regular-radio" name="nc_items_best" id="nc_items_best5" value="5" ' . ((Configuration::get('NC_ITEMS_BESTS') == 5) ? 'checked="checked" ' : '') . '/>
                              <label class="ds items_onrow5" for="nc_items_best5"><span>5</span></label>
                          </div></div>

                  <h3>' . $this->l('New Products slider') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:600px; padding-bottom:0;">
                  <div class="roytc_row">
				<label>Enable slider?</label>
				<div class="margin-form">
					<input type="radio" class="regular-radio" name="nc_carousel_new" id="nc_carousel_new_1" value="1" ' . ((Configuration::get('NC_CAROUSEL_NEW') == "1") ? 'checked="checked" ' : '') . '/>
					<label class="t" for="nc_carousel_new_1"> Yes</label>
					<input type="radio" class="regular-radio" name="nc_carousel_new" id="nc_carousel_new_2" value="2" ' . ((Configuration::get('NC_CAROUSEL_NEW') == "2") ? 'checked="checked" ' : '') . '/>
					<label class="t" for="nc_carousel_new_2"> No</label>
				</div></div>
                  <div class="roytc_row">
				<label>Enable autoscroll?</label>
				<div class="margin-form">
					<input type="radio" class="regular-radio" name="nc_auto_new" id="nc_auto_new_1" value="true" ' . ((Configuration::get('NC_AUTO_NEW') == "true") ? 'checked="checked" ' : '') . '/>
					<label class="t" for="nc_auto_new_1"> Yes</label>
					<input type="radio" class="regular-radio" name="nc_auto_new" id="nc_auto_new_0" value="false" ' . ((Configuration::get('NC_AUTO_NEW') == "false") ? 'checked="checked" ' : '') . '/>
					<label class="t" for="nc_auto_new_0"> No</label>
				</div></div>
               </div><div class="half" style="padding-bottom:0;">
              </div></div>
              <div class="roytc_row ds_wrap">
                  <label>Products per row?</label>
                  <div class="margin-form">
                      <input type="radio" class="regular-radio" name="nc_items_new" id="nc_items_new2" value="2" ' . ((Configuration::get('NC_ITEMS_NEWS') == 2) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow2" for="nc_items_new2"><span>2</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_new" id="nc_items_new3" value="3" ' . ((Configuration::get('NC_ITEMS_NEWS') == 3) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow3" for="nc_items_new3"><span>3</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_new" id="nc_items_new4" value="4" ' . ((Configuration::get('NC_ITEMS_NEWS') == 4) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow4" for="nc_items_new4"><span>4</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_new" id="nc_items_new5" value="5" ' . ((Configuration::get('NC_ITEMS_NEWS') == 5) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow5" for="nc_items_new5"><span>5</span></label>
                  </div></div>

                  <h3>' . $this->l('Special products slider') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:600px; padding-bottom:0;">
                  <div class="roytc_row">
                        <label>Enable slider?</label>
                        <div class="margin-form">
                              <input type="radio" class="regular-radio" name="nc_carousel_sale" id="nc_carousel_sale_1" value="1" ' . ((Configuration::get('NC_CAROUSEL_SALE') == "1") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_carousel_sale_1"> Yes</label>
                              <input type="radio" class="regular-radio" name="nc_carousel_sale" id="nc_carousel_sale_2" value="2" ' . ((Configuration::get('NC_CAROUSEL_SALE') == "2") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_carousel_sale_2"> No</label>
                        </div></div>
                  <div class="roytc_row">
                        <label>Enable autoscroll?</label>
                        <div class="margin-form">
                              <input type="radio" class="regular-radio" name="nc_auto_sale" id="nc_auto_sale_1" value="true" ' . ((Configuration::get('NC_AUTO_SALE') == "true") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_auto_sale_1"> Yes</label>
                              <input type="radio" class="regular-radio" name="nc_auto_sale" id="nc_auto_sale_0" value="false" ' . ((Configuration::get('NC_AUTO_SALE') == "false") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_auto_sale_0"> No</label>
                        </div></div>
               </div><div class="half" style="padding-bottom:0;">
              </div></div>
              <div class="roytc_row ds_wrap">
                  <label>Products per row?</label>
                  <div class="margin-form">
                      <input type="radio" class="regular-radio" name="nc_items_sale" id="nc_items_sale2" value="2" ' . ((Configuration::get('NC_ITEMS_SALES') == 2) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow2" for="nc_items_sale2"><span>2</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_sale" id="nc_items_sale3" value="3" ' . ((Configuration::get('NC_ITEMS_SALES') == 3) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow3" for="nc_items_sale3"><span>3</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_sale" id="nc_items_sale4" value="4" ' . ((Configuration::get('NC_ITEMS_SALES') == 4) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow4" for="nc_items_sale4"><span>4</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_sale" id="nc_items_sale5" value="5" ' . ((Configuration::get('NC_ITEMS_SALES') == 5) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow5" for="nc_items_sale5"><span>5</span></label>
                  </div></div>

                  <h3>' . $this->l('Custom category slider 1') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:600px; padding-bottom:0;">
                  <div class="roytc_row">
                        <label>Enable slider?</label>
                        <div class="margin-form">
                              <input type="radio" class="regular-radio" name="nc_carousel_custom1" id="nc_carousel_custom1_1" value="1" ' . ((Configuration::get('NC_CAROUSEL_CUSTOM1') == "1") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_carousel_custom1_1"> Yes</label>
                              <input type="radio" class="regular-radio" name="nc_carousel_custom1" id="nc_carousel_custom1_2" value="2" ' . ((Configuration::get('NC_CAROUSEL_CUSTOM1') == "2") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_carousel_custom1_2"> No</label>
                        </div></div>
                  <div class="roytc_row">
                        <label>Enable autoscroll?</label>
                        <div class="margin-form">
                              <input type="radio" class="regular-radio" name="nc_auto_custom1" id="nc_auto_custom1_1" value="true" ' . ((Configuration::get('NC_AUTO_CUSTOM1') == "true") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_auto_custom1_1"> Yes</label>
                              <input type="radio" class="regular-radio" name="nc_auto_custom1" id="nc_auto_custom1_0" value="false" ' . ((Configuration::get('NC_AUTO_CUSTOM1') == "false") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_auto_custom1_0"> No</label>
                        </div></div>
               </div><div class="half" style="padding-bottom:0;">
              </div></div>
              <div class="roytc_row ds_wrap">
                  <label>Products per row?</label>
                  <div class="margin-form">
                      <input type="radio" class="regular-radio" name="nc_items_custom1" id="nc_items_custom12" value="2" ' . ((Configuration::get('NC_ITEMS_CUSTOM1S') == 2) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow2" for="nc_items_custom12"><span>2</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_custom1" id="nc_items_custom13" value="3" ' . ((Configuration::get('NC_ITEMS_CUSTOM1S') == 3) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow3" for="nc_items_custom13"><span>3</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_custom1" id="nc_items_custom14" value="4" ' . ((Configuration::get('NC_ITEMS_CUSTOM1S') == 4) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow4" for="nc_items_custom14"><span>4</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_custom1" id="nc_items_custom15" value="5" ' . ((Configuration::get('NC_ITEMS_CUSTOM1S') == 5) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow5" for="nc_items_custom15"><span>5</span></label>
                  </div></div>

                  <h3>' . $this->l('Custom category slider 2') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:600px; padding-bottom:0;">
                  <div class="roytc_row">
                        <label>Enable slider?</label>
                        <div class="margin-form">
                              <input type="radio" class="regular-radio" name="nc_carousel_custom2" id="nc_carousel_custom2_1" value="1" ' . ((Configuration::get('NC_CAROUSEL_CUSTOM2') == "1") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_carousel_custom2_1"> Yes</label>
                              <input type="radio" class="regular-radio" name="nc_carousel_custom2" id="nc_carousel_custom2_2" value="2" ' . ((Configuration::get('NC_CAROUSEL_CUSTOM2') == "2") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_carousel_custom2_2"> No</label>
                        </div></div>
                  <div class="roytc_row">
                        <label>Enable autoscroll?</label>
                        <div class="margin-form">
                              <input type="radio" class="regular-radio" name="nc_auto_custom2" id="nc_auto_custom2_1" value="true" ' . ((Configuration::get('NC_AUTO_CUSTOM2') == "true") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_auto_custom2_1"> Yes</label>
                              <input type="radio" class="regular-radio" name="nc_auto_custom2" id="nc_auto_custom2_0" value="false" ' . ((Configuration::get('NC_AUTO_CUSTOM2') == "false") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_auto_custom2_0"> No</label>
                        </div></div>
               </div><div class="half" style="padding-bottom:0;">
              </div></div>
              <div class="roytc_row ds_wrap">
                  <label>Products per row?</label>
                  <div class="margin-form">
                      <input type="radio" class="regular-radio" name="nc_items_custom2" id="nc_items_custom22" value="2" ' . ((Configuration::get('NC_ITEMS_CUSTOM2S') == 2) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow2" for="nc_items_custom22"><span>2</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_custom2" id="nc_items_custom23" value="3" ' . ((Configuration::get('NC_ITEMS_CUSTOM2S') == 3) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow3" for="nc_items_custom23"><span>3</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_custom2" id="nc_items_custom24" value="4" ' . ((Configuration::get('NC_ITEMS_CUSTOM2S') == 4) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow4" for="nc_items_custom24"><span>4</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_custom2" id="nc_items_custom25" value="5" ' . ((Configuration::get('NC_ITEMS_CUSTOM2S') == 5) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow5" for="nc_items_custom25"><span>5</span></label>
                  </div></div>

                  <h3>' . $this->l('Custom category slider 3') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                <div class="half" style="width:600px; padding-bottom:0;">
                  <div class="roytc_row">
                        <label>Enable slider?</label>
                        <div class="margin-form">
                              <input type="radio" class="regular-radio" name="nc_carousel_custom3" id="nc_carousel_custom3_1" value="1" ' . ((Configuration::get('NC_CAROUSEL_CUSTOM3') == "1") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_carousel_custom3_1"> Yes</label>
                              <input type="radio" class="regular-radio" name="nc_carousel_custom3" id="nc_carousel_custom3_2" value="2" ' . ((Configuration::get('NC_CAROUSEL_CUSTOM3') == "2") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_carousel_custom3_2"> No</label>
                        </div></div>
                  <div class="roytc_row">
                        <label>Enable autoscroll?</label>
                        <div class="margin-form">
                              <input type="radio" class="regular-radio" name="nc_auto_custom3" id="nc_auto_custom3_1" value="true" ' . ((Configuration::get('NC_AUTO_CUSTOM3') == "true") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_auto_custom3_1"> Yes</label>
                              <input type="radio" class="regular-radio" name="nc_auto_custom3" id="nc_auto_custom3_0" value="false" ' . ((Configuration::get('NC_AUTO_CUSTOM3') == "false") ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="nc_auto_custom3_0"> No</label>
                        </div></div>
               </div><div class="half" style="padding-bottom:0;">
              </div></div>
              <div class="roytc_row ds_wrap">
                  <label>Products per row?</label>
                  <div class="margin-form">
                      <input type="radio" class="regular-radio" name="nc_items_custom3" id="nc_items_custom32" value="2" ' . ((Configuration::get('NC_ITEMS_CUSTOM3S') == 2) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow2" for="nc_items_custom32"><span>2</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_custom3" id="nc_items_custom33" value="3" ' . ((Configuration::get('NC_ITEMS_CUSTOM3S') == 3) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow3" for="nc_items_custom33"><span>3</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_custom3" id="nc_items_custom34" value="4" ' . ((Configuration::get('NC_ITEMS_CUSTOM3S') == 4) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow4" for="nc_items_custom34"><span>4</span></label>
                      <input type="radio" class="regular-radio" name="nc_items_custom3" id="nc_items_custom35" value="5" ' . ((Configuration::get('NC_ITEMS_CUSTOM3S') == 5) ? 'checked="checked" ' : '') . '/>
                      <label class="ds items_onrow5" for="nc_items_custom35"><span>5</span></label>
                  </div></div>

            </div>

            <div class="tab-content hide" id="tab-homepage6">
                  <h3 class="first">' . $this->l('Brand / Manufacturer logo slider') . '</h3>
                  <div class="roytc_row ds_wrap">
                      <label>Brands per row?</label>
                      <div class="margin-form">
                          <input type="radio" class="regular-radio" name="brand_per_row" id="brand_per_row3" value="3" ' . ((Configuration::get('RC_BRAND_PER_ROW') == 3) ? 'checked="checked" ' : '') . '/>
                          <label class="ds items_onrow3" for="brand_per_row3"><span>3</span></label>
                          <input type="radio" class="regular-radio" name="brand_per_row" id="brand_per_row4" value="4" ' . ((Configuration::get('RC_BRAND_PER_ROW') == 4) ? 'checked="checked" ' : '') . '/>
                          <label class="ds items_onrow4" for="brand_per_row4"><span>4</span></label>
                          <input type="radio" class="regular-radio" name="brand_per_row" id="brand_per_row5" value="5" ' . ((Configuration::get('RC_BRAND_PER_ROW') == 5) ? 'checked="checked" ' : '') . '/>
                          <label class="ds items_onrow5" for="brand_per_row5"><span>5</span></label>
                          <input type="radio" class="regular-radio" name="brand_per_row" id="brand_per_row6" value="6" ' . ((Configuration::get('RC_BRAND_PER_ROW') == 6) ? 'checked="checked" ' : '') . '/>
                          <label class="ds items_onrow6" for="brand_per_row6"><span>6</span></label>
                      </div></div>

                  <h3 class="first">' . $this->l('If name list (not logos) selected') . '</h3>
            			<div class="roytc_row" style="margin-top:0">
                        <label>Brands name color</label>
                        <div class="margin-form" style="margin-top:0">
                        <input type="color" name="brand_name"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_BRAND_NAME') . '" />
                  </div></div>
                  <div class="roytc_row">
                        <label>Brands name color hover</label>
                        <div class="margin-form">
                        <input type="color" name="brand_name_hover"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_BRAND_NAME_HOVER') . '" />
                  </div></div>
            </div>
            </div>

            <div class="tab-pane" id="tab-page">
                  <h2 class="rtc_title4">' . $this->l('Page content') . '</h2>

                  <h3 class="first">' . $this->l('Breadcrumb') . '</h3>

                  <div class="roytc_row ds_wrap">
                          <label>Breadcrumb align</label>
                          <div class="margin-form" style="margin-top:0; padding-top:10px;">
                              <input type="radio" class="regular-radio" name="b_layout" value="1" id="b_layout1" ' . ((Configuration::get('RC_B_LAYOUT') == "1") ? 'checked="checked" ' : '') . ' />
                              <label class="ds align1" for="b_layout1"> <span></span></label>
                              <input type="radio" class="regular-radio" name="b_layout" value="2" id="b_layout2" ' . ((Configuration::get('RC_B_LAYOUT') == "2") ? 'checked="checked" ' : '') . ' />
                              <label class="ds align2" for="b_layout2"> <span></span></label>
                              <input type="radio" class="regular-radio" name="b_layout" value="3" id="b_layout3" ' . ((Configuration::get('RC_B_LAYOUT') == "3") ? 'checked="checked" ' : '') . ' />
                              <label class="ds align3" for="b_layout3"> <span></span></label>
                    </div></div>

                    <div class="roytc_row">
                          <label>Breadcrumb link</label>
              			    	<div class="margin-form">
              					<input type="color" name="b_link"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_B_LINK') . '" />
              			</div></div>
                    <div class="roytc_row">
                          <label>Breadcrumb link hover</label>
              			    	<div class="margin-form">
              					<input type="color" name="b_link_hover"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_B_LINK_HOVER') . '" />
              			</div></div>
                    <div class="roytc_row">
                          <label>Breadcrumb slash between links</label>
                          <div class="margin-form">
                                <input type="color" name="b_separator"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_B_SEPARATOR') . '" />
                    </div></div>

                  <h3>' . $this->l('CMS content') . '</h3>

                        <div class="roytc_row">
                             <label>Blockquote quotes</label>
                             <div class="margin-form">
                             <input type="color" name="page_bq_q" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PAGE_BQ_Q') . '" />
                        </div></div>

                  <h3>' . $this->l('Alert messages') . '</h3>
                        <div class="roytc_row">
                             <label>Warning alert color</label>
                             <div class="margin-form">
                             <input type="color" name="warning_message_color" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_WARNING_MESSAGE_COLOR') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Success alert color</label>
                             <div class="margin-form">
                             <input type="color" name="success_message_color" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SUCCESS_MESSAGE_COLOR') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Error alert color</label>
                             <div class="margin-form">
                             <input type="color" name="danger_message_color" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_DANGER_MESSAGE_COLOR') . '" />
                        </div></div>

                  <h3 class="first">' . $this->l('Contact Us') . '</h3>
                        <div class="roytc_row">
                             <label>Contact page icons</label>
                             <div class="margin-form">
                             <input type="color" name="contact_icon" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_CONTACT_ICON') . '" />
                        </div></div>

            </div>

            <div class="tab-pane" id="tab-sidebar">
                  <h2 class="rtc_title19">' . $this->l('Sidebar blocks and Filter') . '</h2>
                  <div class="nav_inside_container">
                        <ul class="nav_inside tabs">
                              <li class="inside_tab tab-sidebar1 active">
                                    <a data-inside="tab" href="#tab-sidebar1">Sidebar blocks</a>
                              </li>
                              <li class="inside_tab tab-sidebar2">
                                    <a data-inside="tab" href="#tab-sidebar2">Sale block</a>
                              </li>
                        </ul>
                  </div>

                <div class="tab-content hide" id="tab-sidebar1">
                  <h3 class="first">' . $this->l('Blocks titles') . '</h3>
                        <div class="roytc_row">
                              <label>Enable background for blocks titles?</label>
                              <div class="margin-form">
                                  <input type="radio" class="regular-radio sidebar_title" name="sidebar_title" id="sidebar_title1" value="1" ' . ((Configuration::get('RC_SIDEBAR_TITLE') == 1) ? 'checked="checked" ' : '') . '/>
                                  <label class="t" for="sidebar_title1"> Yes</label>
                                  <input type="radio" class="regular-radio sidebar_title" name="sidebar_title" id="sidebar_title0" value="0" ' . ((Configuration::get('RC_SIDEBAR_TITLE') == 0) ? 'checked="checked" ' : '') . '/>
                                  <label class="t" for="sidebar_title0"> No</label>
                        </div></div>
                  <div class="if_sidebar_title">
                        <div class="roytc_row" style="margin-top:0;">
                             <label>Sidebar title background</label>
                             <div class="margin-form" style="margin-top:0;">
                             <input type="color" name="sidebar_title_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_TITLE_BG') . '" />
                        </div></div>
                  </div>
                        <div class="roytc_row" style="margin-top:0;">
                              <label>Enable title border?</label>
                              <div class="margin-form" style="margin-top:0;">
                                  <input type="radio" class="regular-radio sidebar_title_b" name="sidebar_title_b" id="sidebar_title_b1" value="1" ' . ((Configuration::get('RC_SIDEBAR_TITLE_B') == 1) ? 'checked="checked" ' : '') . '/>
                                  <label class="t" for="sidebar_title_b1"> Yes</label>
                                  <input type="radio" class="regular-radio sidebar_title_b" name="sidebar_title_b" id="sidebar_title_b0" value="0" ' . ((Configuration::get('RC_SIDEBAR_TITLE_B') == 0) ? 'checked="checked" ' : '') . '/>
                                  <label class="t" for="sidebar_title_b0"> No</label>
                        </div></div>
                  <div class="if_sidebar_title_b">
                     <div class="half_container low_paddings" style="display:inline-block; width:100%">
                        <div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row" style="margin-top:0;">
                             <label>Top border width</label>
                             <div class="margin-form" style="margin-top:0;">
                             <input type="text" name="sidebar_title_b1" value="' . Configuration::get('RC_SIDEBAR_TITLE_B1') . '" /> px
                        </div></div>
                        <div class="roytc_row">
                             <label>Left border width</label>
                             <div class="margin-form">
                             <input type="text" name="sidebar_title_b4" value="' . Configuration::get('RC_SIDEBAR_TITLE_B4') . '" /> px
                        </div></div>
                        <div class="roytc_row">
                             <label>Sidebar title border</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_title_border" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_TITLE_BORDER') . '" />
                        </div></div>
                  </div><div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row" style="margin-top:0;">
                             <label>Right border width</label>
                             <div class="margin-form" style="margin-top:0;">
                             <input type="text" name="sidebar_title_b2" value="' . Configuration::get('RC_SIDEBAR_TITLE_B2') . '" /> px
                        </div></div>
                        <div class="roytc_row">
                             <label>Bottom border width</label>
                             <div class="margin-form">
                             <input type="text" name="sidebar_title_b3" value="' . Configuration::get('RC_SIDEBAR_TITLE_B3') . '" /> px
                        </div></div>
                    </div></div>
                  </div>
                  <div class="roytc_row">
                       <label>Border radius</label>
                       <div class="margin-form" style="margin-top:0;">
                       <input type="text" name="sidebar_title_br" value="' . Configuration::get('RC_SIDEBAR_TITLE_BR') . '" /> px
                  </div></div>
                  <div class="hr"></div>

                        <div class="roytc_row">
                             <label>Sidebar title link</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_title_link" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_TITLE_LINK') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Sidebar hover title link</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_title_link_hover" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_TITLE_LINK_HOVER') . '" />
                        </div></div>

                        <h3>' . $this->l('Sidebar block content') . '</h3>
                        <div class="roytc_row" style="margin-top:0;">
                             <label>Sidebar block content background</label>
                             <div class="margin-form" style="margin-top:0;">
                             <input type="color" name="sidebar_block_content_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BG') . '" />
                        </div></div>

                        <div class="roytc_row" style="margin-top:0;">
                              <label>Enable content border?</label>
                              <div class="margin-form" style="margin-top:0;">
                                  <input type="radio" class="regular-radio sidebar_content_b" name="sidebar_content_b" id="sidebar_content_b1" value="1" ' . ((Configuration::get('RC_SIDEBAR_CONTENT_B') == 1) ? 'checked="checked" ' : '') . '/>
                                  <label class="t" for="sidebar_content_b1"> Yes</label>
                                  <input type="radio" class="regular-radio sidebar_content_b" name="sidebar_content_b" id="sidebar_content_b0" value="0" ' . ((Configuration::get('RC_SIDEBAR_CONTENT_B') == 0) ? 'checked="checked" ' : '') . '/>
                                  <label class="t" for="sidebar_content_b0"> No</label>
                        </div></div>
                  <div class="if_sidebar_content_b">
                     <div class="half_container low_paddings" style="display:inline-block; width:100%">
                        <div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row" style="margin-top:0;">
                             <label>Top border width</label>
                             <div class="margin-form" style="margin-top:0;">
                             <input type="text" name="sidebar_content_b1" value="' . Configuration::get('RC_SIDEBAR_CONTENT_B1') . '" /> px
                        </div></div>
                        <div class="roytc_row">
                             <label>Left border width</label>
                             <div class="margin-form">
                             <input type="text" name="sidebar_content_b4" value="' . Configuration::get('RC_SIDEBAR_CONTENT_B4') . '" /> px
                        </div></div>
                        </div><div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row" style="margin-top:0;">
                             <label>Right border width</label>
                             <div class="margin-form" style="margin-top:0;">
                             <input type="text" name="sidebar_content_b2" value="' . Configuration::get('RC_SIDEBAR_CONTENT_B2') . '" /> px
                        </div></div>
                        <div class="roytc_row">
                             <label>Bottom border width</label>
                             <div class="margin-form">
                             <input type="text" name="sidebar_content_b3" value="' . Configuration::get('RC_SIDEBAR_CONTENT_B3') . '" /> px
                        </div></div>
                    </div></div>
                        <div class="roytc_row">
                             <label>Sidebar block content border</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_block_content_border" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BORDER') . '" />
                        </div></div>
                  </div>
                      <div class="roytc_row">
                           <label>Border radius</label>
                           <div class="margin-form" style="margin-top:0;">
                           <input type="text" name="sidebar_content_br" value="' . Configuration::get('RC_SIDEBAR_CONTENT_BR') . '" /> px
                      </div></div>

                     <div class="half_container low_paddings" style="display:inline-block; width:100%">
                     <div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                             <label>Block content text color</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_block_text_color" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_BLOCK_TEXT_COLOR') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Block content separators</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_item_separator" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_ITEM_SEPARATOR') . '" />
                        </div></div>
                    </div><div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                             <label>Block content link</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_block_link" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_BLOCK_LINK') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Block content hover link</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_block_link_hover" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_BLOCK_LINK_HOVER') . '" />
                        </div></div>
                    </div></div>
                        <div class="hr"></div>
                        <div class="roytc_row">
                             <label>Filter titles</label>
                             <div class="margin-form">
                             <input type="color" name="pl_filter_t" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_FILTER_T') . '" />
                        </div></div>
                </div>

                <div class="tab-content hide" id="tab-sidebar2">
                      <h3 class="first">' . $this->l('Sidebar blocks with products') . '</h3>
                        <div class="roytc_row">
                             <label>Sidebar product price</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_product_price" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_PRODUCT_PRICE') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Sidebar product old price</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_product_oprice" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_PRODUCT_OPRICE') . '" />
                        </div></div>

                     <h3>' . $this->l('Sidebar block buttons') . '</h3>
                     <div class="half_container very_low_paddings" style="display:inline-block; width:100%">
                        <div class="half">
                        <div class="roytc_row">
                             <label>Button background</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_button_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_BUTTON_BG') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Button border</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_button_border" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_BUTTON_BORDER') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Button color</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_button_color" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_BUTTON_COLOR') . '" />
                        </div></div>
                     </div><div class="half half_right">
                        <div class="roytc_row">
                             <label>Button background hover</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_button_hbg" class="colorpicker cs_sc" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_BUTTON_HBG') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Button border hover</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_button_hborder" class="colorpicker cs_sc" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_BUTTON_HBORDER') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Button color hover</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_button_hcolor" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_BUTTON_HCOLOR') . '" />
                        </div></div>
                     </div></div>

                     <h3>' . $this->l('Sidebar product slider controls') . '</h3>
                     <div class="half_container very_low_paddings" style="display:inline-block; width:100%">
                        <div class="half">
                        <div class="roytc_row">
                             <label>Override controls color</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_c" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_C') . '" />
                        </div></div>
                     </div><div class="half half_right">
                        <div class="roytc_row">
                             <label>Override controls color hover</label>
                             <div class="margin-form">
                             <input type="color" name="sidebar_hc" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_SIDEBAR_HC') . '" />
                        </div></div>
                     </div></div>
                </div>

            </div>


            <div class="tab-pane" id ="tab-productlist">
                    <h2 class="rtc_title5">' . $this->l('Products and Category') . '</h2>
                  <div class="nav_inside_container">
                        <ul class="nav_inside tabs">
                              <li class="inside_tab tab-productlist1 active">
                                    <a data-inside="tab" href="#tab-productlist1">Product list</a>
                              </li>
                              <li class="inside_tab tab-productlist2">
                                    <a data-inside="tab" href="#tab-productlist2">Product item design</a>
                              </li>
                        </ul>
                  </div>

                <div class="tab-content hide" id="tab-productlist1">
                    <h3 class="first">' . $this->l('How much products per row you want to show on category page?') . '</h3>
      					<div class="roytc_row ds_wrap">
                    <label>Products per row?</label>
                    <div class="margin-form">
                        <input type="radio" class="regular-radio" name="nc_product_switch" id="nc_product_switch_2" value="2" ' . ((Configuration::get('NC_PRODUCT_SWITCH') == 2) ? 'checked="checked" ' : '') . '/>
                        <label class="ds items_onrow2" for="nc_product_switch_2"><span>2</span></label>
                        <input type="radio" class="regular-radio" name="nc_product_switch" id="nc_product_switch_3" value="3" ' . ((Configuration::get('NC_PRODUCT_SWITCH') == 3) ? 'checked="checked" ' : '') . '/>
                        <label class="ds items_onrow3" for="nc_product_switch_3"><span>3</span></label>
                        <input type="radio" class="regular-radio" name="nc_product_switch" id="nc_product_switch_4" value="4" ' . ((Configuration::get('NC_PRODUCT_SWITCH') == 4) ? 'checked="checked" ' : '') . '/>
                        <label class="ds items_onrow4" for="nc_product_switch_4"><span>4</span></label>
                        <input type="radio" class="regular-radio" name="nc_product_switch" id="nc_product_switch_5" value="5" ' . ((Configuration::get('NC_PRODUCT_SWITCH') == 5) ? 'checked="checked" ' : '') . '/>
                        <label class="ds items_onrow5" for="nc_product_switch_5"><span>5</span></label>
                    </div></div>

                        <h3>' . $this->l('Category page content') . '</h3>
                            <div class="roytc_row">
                            <label>Display category image, description?</label>
                            <div class="margin-form">
                                <input type="radio" class="regular-radio" name="nc_cat" id="nc_cat_1" value="1" ' . ((Configuration::get('NC_CAT_S') == 1) ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="nc_cat_1"> Yes</label>
                                <input type="radio" class="regular-radio" name="nc_cat" id="nc_cat_0" value="0" ' . ((Configuration::get('NC_CAT_S') == 0) ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="nc_cat_0"> No</label>
                            </div></div>
                            <div class="roytc_row">
                            <label>Display subcategories thumbnails?</label>
                            <div class="margin-form">
                                <input type="radio" class="regular-radio" name="nc_subcat" id="nc_subcat_1" value="1" ' . ((Configuration::get('NC_SUBCAT_S') == 1) ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="nc_subcat_1"> Yes</label>
                                <input type="radio" class="regular-radio" name="nc_subcat" id="nc_subcat_0" value="0" ' . ((Configuration::get('NC_SUBCAT_S') == 0) ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="nc_subcat_0"> No</label>
                            </div></div>

                            <div class="hr"></div>

                            <div class="roytc_row">
                                 <label>Grid/list icon color</label>
                                 <div class="margin-form">
                                 <input type="color" name="pl_nav_grid" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_NAV_GRID') . '" />
                            </div></div>

                        <h3>' . $this->l('Pagination') . '</h3>

                            <div class="roytc_row">
                                 <label>Number color</label>
                                 <div class="margin-form">
                                 <input type="color" name="pl_number_color" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_NUMBER_COLOR') . '" />
                            </div></div>
                            <div class="roytc_row">
                                 <label>Number color active</label>
                                 <div class="margin-form">
                                 <input type="color" name="pl_number_color_hover" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_NUMBER_COLOR_HOVER') . '" />
                            </div></div>


                </div>

                <div class="tab-content hide" id="tab-productlist2">
                        <h3 class="first">' . $this->l('Product item design') . '</h3>
                          <div class="roytc_row ds_wrap">
                                  <label>Layout of product container</label>
                                  <div class="margin-form" style="margin-top:0; padding-top:10px;">
                                      <input type="radio" class="regular-radio nc_pc_layout" name="nc_pc_layout" value="1" id="nc_pc_layout1" ' . ((Configuration::get('NC_PC_LAYOUTS') == "1") ? 'checked="checked" ' : '') . ' />
                                      <label class="ds nc_pc_layout1" for="nc_pc_layout1"> <span>1 . Full image</span></label>
                                      <input type="radio" class="regular-radio nc_pc_layout" name="nc_pc_layout" value="3" id="nc_pc_layout3" ' . ((Configuration::get('NC_PC_LAYOUTS') == "3") ? 'checked="checked" ' : '') . ' />
                                      <label class="ds nc_pc_layout3" for="nc_pc_layout3"> <span>2 . Full image Separate</span></label>
                                      <input type="radio" class="regular-radio nc_pc_layout" name="nc_pc_layout" value="2" id="nc_pc_layout2" ' . ((Configuration::get('NC_PC_LAYOUTS') == "2") ? 'checked="checked" ' : '') . ' />
                                      <label class="ds nc_pc_layout2" for="nc_pc_layout2"> <span>3 . Container</span></label>
                            </div></div>
                            <div class="hr"></div>
                  <div class="half_container low_paddings" style="display:inline-block; width:100%">
                     <div class="half">
                        <div class="roytc_row">
                             <label>Item border</label>
                             <div class="margin-form">
                             <input type="color" name="pl_item_border" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_ITEM_BORDER') . '" />
                        </div></div>
                         <div class="roytc_row">
                              <label>Product item background</label>
                              <div class="margin-form">
                              <input type="color" name="pl_item_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_ITEM_BG') . '" />
                         </div></div>
                  </div><div class="half half_right">
                      <div class="roytc_row">
                           <label>Item border hover</label>
                           <div class="margin-form">
                           <input type="color" name="nc_pl_item_borderh" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_PL_ITEM_BORDERH') . '" />
                      </div></div>
                  </div></div>

                  <div class="hr"></div>

                  <div class="half_container low_paddings" style="display:inline-block; width:100%">
                  <div class="half">
                              <div class="roytc_row">
                                   <label>Product name</label>
                                   <div class="margin-form">
                                   <input type="color" name="pl_product_name" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_PRODUCT_NAME') . '" />
                              </div></div>
                       </div><div class="half half_right">
                              <div class="roytc_row">
                                   <label>Product description</label>
                                   <div class="margin-form">
                                   <input type="color" name="pl_list_description" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_LIST_DESCRIPTION') . '" />
                              </div></div>
                          </div></div>

                        <div class="hr"></div>

                        <div class="half_container low_paddings" style="display:inline-block; width:100%">
                        <div class="half">
                              <div class="roytc_row">
                                   <label>Product price</label>
                                   <div class="margin-form">
                                   <input type="color" name="pl_product_price" class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_PL_PRODUCT_PRICE') . '" />
                              </div></div>
                       </div><div class="half half_right">
                              <div class="roytc_row">
                                   <label>Product old price</label>
                                   <div class="margin-form">
                                   <input type="color" name="pl_product_oldprice" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_PRODUCT_OLDPRICE') . '" />
                              </div></div>
                        </div></div>

                      <div class="hr"></div>
                        <div class="roytc_row" style="margin-top:0;">
                              <label>Display shadow on hover?</label>
                              <div class="margin-form" style="margin-top:0;">
                                  <input type="radio" class="regular-radio nc_pl_shadow" name="nc_pl_shadow" id="nc_pl_shadow1" value="1" ' . ((Configuration::get('NC_PL_SHADOWS') == 1) ? 'checked="checked" ' : '') . '/>
                                  <label class="t" for="nc_pl_shadow1"> Yes</label>
                                  <input type="radio" class="regular-radio nc_pl_shadow" name="nc_pl_shadow" id="nc_pl_shadow0" value="0" ' . ((Configuration::get('NC_PL_SHADOWS') == 0) ? 'checked="checked" ' : '') . '/>
                                  <label class="t" for="nc_pl_shadow0"> No</label>
                        </div></div>

                    <h3>' . $this->l('Quick view and Discover') . '</h3>

                      <div class="roytc_row">
                           <label>Icon background</label>
                           <div class="margin-form">
                           <input type="color" name="pl_hover_but_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_HOVER_BUT_BG') . '" />
                      </div></div>
                        <div class="roytc_row">
                             <label>Icon color</label>
                             <div class="margin-form">
                             <input type="color" name="pl_hover_but" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_HOVER_BUT') . '" />
                        </div></div>

                        <div class="roytc_row ds_wrap">
                              <label>Quick view icon</label>
                              <div class="margin-form" style="margin-top:0; padding-top:10px;">
                                  <input type="radio" class="regular-radio" name="nc_i_qv" value="search1" id="nc_i_qv1" ' . ((Configuration::get('NC_I_QVS') == "search1") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon nc_i_qv1" for="nc_i_qv1"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="nc_i_qv" value="search2" id="nc_i_qv2" ' . ((Configuration::get('NC_I_QVS') == "search2") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon nc_i_qv2" for="nc_i_qv2"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="nc_i_qv" value="search3" id="nc_i_qv3" ' . ((Configuration::get('NC_I_QVS') == "search3") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon nc_i_qv3" for="nc_i_qv3"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="nc_i_qv" value="search4" id="nc_i_qv4" ' . ((Configuration::get('NC_I_QVS') == "search4") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon nc_i_qv4" for="nc_i_qv4"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="nc_i_qv" value="qv1" id="nc_i_qv5" ' . ((Configuration::get('NC_I_QVS') == "qv1") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon nc_i_qv5" for="nc_i_qv5"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="nc_i_qv" value="qv2" id="nc_i_qv6" ' . ((Configuration::get('NC_I_QVS') == "qv2") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon nc_i_qv6" for="nc_i_qv6"> <span></span></label>
                        </div></div>
                        <div class="roytc_row ds_wrap">
                              <label>Select options</label>
                              <div class="margin-form">
                                  <input type="radio" class="regular-radio" name="nc_i_discover" value="discover1" id="nc_i_discover1" ' . ((Configuration::get('NC_I_DISCOVERS') == "discover1") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon nc_i_discover1" for="nc_i_discover1"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="nc_i_discover" value="discover2" id="nc_i_discover2" ' . ((Configuration::get('NC_I_DISCOVERS') == "discover2") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon nc_i_discover2" for="nc_i_discover2"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="nc_i_discover" value="cat_list" id="nc_i_discover3" ' . ((Configuration::get('NC_I_DISCOVERS') == "cat_list") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon nc_i_discover3" for="nc_i_discover3"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="nc_i_discover" value="cat_filter" id="nc_i_discover4" ' . ((Configuration::get('NC_I_DISCOVERS') == "cat_filter") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon nc_i_discover4" for="nc_i_discover4"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="nc_i_discover" value="search1" id="nc_i_discover5" ' . ((Configuration::get('NC_I_DISCOVERS') == "search1") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon nc_i_discover5" for="nc_i_discover5"> <span></span></label>
                        </div></div>
                        
                      <div class="hr"></div>
                      <div class="roytc_row" style="margin-top:0;">
                            <label>Display Quick View icon?</label>
                            <div class="margin-form" style="margin-top:0;">
                                <input type="radio" class="regular-radio nc_show_q" name="nc_show_q" id="nc_show_q1" value="1" ' . ((Configuration::get('NC_SHOW_QW') == 1) ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="nc_show_q1"> Yes</label>
                                <input type="radio" class="regular-radio nc_show_q" name="nc_show_q" id="nc_show_q0" value="0" ' . ((Configuration::get('NC_SHOW_QW') == 0) ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="nc_show_q0"> No</label>
                      </div></div>
                      <div class="roytc_row" style="margin-top:0;">
                            <label>Display Select option icon?</label>
                            <div class="margin-form" style="margin-top:0;">
                                <input type="radio" class="regular-radio nc_show_s" name="nc_show_s" id="nc_show_s1" value="1" ' . ((Configuration::get('NC_SHOW_SW') == 1) ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="nc_show_s1"> Yes</label>
                                <input type="radio" class="regular-radio nc_show_s" name="nc_show_s" id="nc_show_s0" value="0" ' . ((Configuration::get('NC_SHOW_SW') == 0) ? 'checked="checked" ' : '') . '/>
                                <label class="t" for="nc_show_s0"> No</label>
                      </div></div>


                    <h3>' . $this->l('Product labels') . '</h3>
                    <div class="half_container low_paddings" style="display:inline-block; width:100%">
                       <div class="half">
                          <div class="roytc_row">
                               <label>New label background</label>
                               <div class="margin-form">
                               <input type="color" name="pl_product_new_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_PRODUCT_NEW_BG') . '" />
                          </div></div>
                          <div class="roytc_row">
                               <label>New label border</label>
                               <div class="margin-form">
                               <input type="color" name="pl_product_new_border" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_PRODUCT_NEW_BORDER') . '" />
                          </div></div>
                          <div class="roytc_row">
                               <label>New label color</label>
                               <div class="margin-form">
                               <input type="color" name="pl_product_new_color" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_PRODUCT_NEW_COLOR') . '" />
                          </div></div>
                    </div><div class="half half_right">
                          <div class="roytc_row">
                               <label>Sale label background</label>
                               <div class="margin-form">
                               <input type="color" name="pl_product_sale_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_PRODUCT_SALE_BG') . '" />
                          </div></div>
                          <div class="roytc_row">
                               <label>Sale label border</label>
                               <div class="margin-form">
                               <input type="color" name="pl_product_sale_border" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_PRODUCT_SALE_BORDER') . '" />
                          </div></div>
                          <div class="roytc_row">
                               <label>Sale label color</label>
                               <div class="margin-form">
                               <input type="color" name="pl_product_sale_color" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PL_PRODUCT_SALE_COLOR') . '" />
                          </div></div>
                    </div></div>

                    <div class="hr"></div>

                        <div class="roytc_row">
                        <label>Enable Second Image on hover?</label>
                        <div class="margin-form">
                            <input type="radio" class="regular-radio" name="nc_second_img" id="nc_second_img_1" value="1" ' . ((Configuration::get('NC_SECOND_IMG_S') == 1) ? 'checked="checked" ' : '') . '/>
                            <label class="t" for="nc_second_img_1"> Yes</label>
                            <input type="radio" class="regular-radio" name="nc_second_img" id="nc_second_img_0" value="0" ' . ((Configuration::get('NC_SECOND_IMG_S') == 0) ? 'checked="checked" ' : '') . '/>
                            <label class="t" for="nc_second_img_0"> No</label>
                        </div></div>

                        <div class="roytc_row">
                              <label>Enable Color Swatches if product has colors?</label>
                              <div class="margin-form">
                                  <input type="radio" class="regular-radio" name="nc_colors" id="nc_colors1" value="1" ' . ((Configuration::get('NC_COLORS_S') == 1) ? 'checked="checked" ' : '') . '/>
                                  <label class="t" for="nc_colors1"> Yes</label>
                                  <input type="radio" class="regular-radio" name="nc_colors" id="nc_colors0" value="0" ' . ((Configuration::get('NC_COLORS_S') == 0) ? 'checked="checked" ' : '') . '/>
                                  <label class="t" for="nc_colors0"> No</label>
                        </div></div>

                        <h3>' . $this->l('Product ratings') . '</h3>

                        <div class="roytc_row">
                        <label>Reviews star-off color</label>
                             <div class="margin-form">
                             <input type="color" name="pp_reviews_staroff" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PP_REVIEWS_STAROFF') . '" />
                        </div></div>
                        <div class="roytc_row">
                        <label>Reviews star-on color</label>
                             <div class="margin-form">
                             <input type="color" name="pp_reviews_staron" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PP_REVIEWS_STARON') . '" />
                        </div></div>

                        <h3>' . $this->l('Special price countdown') . '</h3>
                        <div class="roytc_row">
                        <label>Hide days?</label>
                            <div class="margin-form">
                            <input type="radio" class="regular-radio" name="nc_count_days" id="nc_count_days_1" value="1" ' . ((Configuration::get('NC_COUNT_DAYS') == 1) ? 'checked="checked" ' : '') . '/>
                            <label class="t" for="nc_count_days_1"> Yes</label>
                            <input type="radio" class="regular-radio" name="nc_count_days" id="nc_count_days_0" value="0" ' . ((Configuration::get('NC_COUNT_DAYS') == 0) ? 'checked="checked" ' : '') . '/>
                            <label class="t" for="nc_count_days_0"> No</label>
                        </div></div>
                         <div class="roytc_row">
                             <label>Countdown background</label>
                             <div class="margin-form">
                             <input type="color" name="nc_count_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_COUNT_BG') . '" />
                        </div></div>
                         <div class="roytc_row">
                             <label>Countdown time color</label>
                             <div class="margin-form">
                             <input type="color" name="nc_count_time" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_COUNT_TIME') . '" />
                        </div></div>
                         <div class="roytc_row">
                             <label>Countdown text color</label>
                             <div class="margin-form">
                             <input type="color" name="nc_count_color" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_COUNT_COLOR') . '" />
                        </div></div>

                        <div class="hr"></div>

                        <div class="roytc_row">
                             <label>Watch background</label>
                             <div class="margin-form">
                             <input type="color" name="nc_count_watch_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_COUNT_WATCH_BG') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Watch color</label>
                             <div class="margin-form">
                             <input type="color" name="nc_count_watch" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_COUNT_WATCH') . '" />
                        </div></div>

                        <h3>' . $this->l('Change Add to cart to Select options if product have combinations?') . '</h3>
                        <div class="roytc_row">
                        <label>Select options feature</label>
                        <div class="margin-form">
                            <input type="radio" class="regular-radio" name="nc_ai" id="nc_ai1" value="1" ' . ((Configuration::get('NC_AIS') == 1) ? 'checked="checked" ' : '') . '/>
                            <label class="t" for="nc_ai1"> Yes</label>
                            <input type="radio" class="regular-radio" name="nc_ai" id="nc_ai0" value="0" ' . ((Configuration::get('NC_AIS') == 0) ? 'checked="checked" ' : '') . '/>
                            <label class="t" for="nc_ai0"> No</label>
                        </div></div>




                </div>

            </div>


            <div class="tab-pane" id ="tab-productpage">
                    <h2 class="rtc_title6">' . $this->l('Product page and Quick view') . '</h2>
                  <div class="nav_inside_container">
                        <ul class="nav_inside tabs">
                              <li class="inside_tab tab-productpage1 active">
                                    <a data-inside="tab" href="#tab-productpage1">Image column</a>
                              </li>
                              <li class="inside_tab tab-productpage2">
                                    <a data-inside="tab" href="#tab-productpage2">Main column</a>
                              </li>
                        </ul>
                  </div>

                <div class="tab-content hide" id="tab-productpage1">

                  <h3 class="first">' . $this->l('Image column design') . '</h3>
                    <div class="half_container low_paddings" style="display:inline-block; width:100%">
                      <div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                              <label>Enable border for product image?</label>
                              <div class="margin-form">
                                  <input type="radio" class="regular-radio pp_imgb" name="pp_imgb" id="pp_imgb1" value="1" ' . ((Configuration::get('RC_PP_IMGB') == 1) ? 'checked="checked" ' : '') . '/>
                                  <label class="t" for="pp_imgb1"> Yes</label>
                                  <input type="radio" class="regular-radio pp_imgb" name="pp_imgb" id="pp_imgb0" value="0" ' . ((Configuration::get('RC_PP_IMGB') == 0) ? 'checked="checked" ' : '') . '/>
                                  <label class="t" for="pp_imgb0"> No</label>
                        </div></div>
                        <div class="roytc_row" style="margin-top:0;">
                        <label>Product image border</label>
                             <div class="margin-form" style="margin-top:0;">
                             <input type="color" name="pp_img_border" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PP_IMG_BORDER') . '" />
                        </div></div>

                     </div><div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                        <label>Image thumbnails border</label>
                             <div class="margin-form">
                             <input type="color" name="pp_icon_border" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PP_ICON_BORDER') . '" />
                        </div></div>
                        <div class="roytc_row">
                        <label>Image thumbnails border hover</label>
                             <div class="margin-form">
                             <input type="color" name="pp_icon_border_hover" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PP_ICON_BORDER_HOVER') . '" />
                        </div></div>
                      </div></div>

                      <div class="roytc_row">
                        <label>How much thumbs visible?</label>
                        <div class="margin-form">
                          <input type="text" name="nc_pp_qq3" value="' . Configuration::get('NC_PP_QQ3S') . '" />
                            <p class="clear helpcontent">Recommendation: From 2 to 4. Default 3</p>
                        </div></div>

                  <div class="hr"></div>

                        <div class="roytc_row ds_wrap">
                              <label>Image zoom icon</label>
                              <div class="margin-form" style="margin-top:0; padding-top:10px;">
                                  <input type="radio" class="regular-radio" name="pp_z" value="search1" id="pp_z1" ' . ((Configuration::get('RC_PP_Z') == "search1") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon pp_z1" for="pp_z1"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="pp_z" value="plus" id="pp_z2" ' . ((Configuration::get('RC_PP_Z') == "plus") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon pp_z2" for="pp_z2"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="pp_z" value="round_plus" id="pp_z3" ' . ((Configuration::get('RC_PP_Z') == "round_plus") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon pp_z3" for="pp_z3"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="pp_z" value="qv2" id="pp_z4" ' . ((Configuration::get('RC_PP_Z') == "qv2") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds_icon pp_z4" for="pp_z4"> <span></span></label>
                        </div></div>
                        <div class="roytc_row">
                        <label>Icon color</label>
                             <div class="margin-form">
                             <input type="color" name="pp_zi" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PP_ZI') . '" />
                        </div></div>
                        <div class="roytc_row">
                        <label>Icon background</label>
                             <div class="margin-form">
                             <input type="color" name="pp_zihbg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PP_ZIHBG') . '" />
                        </div></div>

                        <h3>' . $this->l('Mobile layout') . '</h3>
                          <div class="roytc_row">
                                <label>Show dots?</label>
                                <div class="margin-form">
                                    <input type="radio" class="regular-radio" name="nc_mobadots" id="nc_mobadots1" value="1" ' . ((Configuration::get('NC_MOBADOTSS') == 1) ? 'checked="checked" ' : '') . '/>
                                    <label class="t" for="nc_mobadots1"> Yes</label>
                                    <input type="radio" class="regular-radio" name="nc_mobadots" id="nc_mobadots0" value="0" ' . ((Configuration::get('NC_MOBADOTSS') == 0) ? 'checked="checked" ' : '') . '/>
                                    <label class="t" for="nc_mobadots0"> No</label>
                          </div></div>
                          <div class="roytc_row">
                                <label>Sticky Add to cart on mobiles?</label>
                                <div class="margin-form">
                                    <input type="radio" class="regular-radio" name="nc_sticky_add" id="nc_sticky_add1" value="1" ' . ((Configuration::get('NC_STICKY_ADDS') == 1) ? 'checked="checked" ' : '') . '/>
                                    <label class="t" for="nc_sticky_add1"> Yes</label>
                                    <input type="radio" class="regular-radio" name="nc_sticky_add" id="nc_sticky_add0" value="0" ' . ((Configuration::get('NC_STICKY_ADDS') == 0) ? 'checked="checked" ' : '') . '/>
                                    <label class="t" for="nc_sticky_add0"> No</label>
                          </div></div>
                         <div class="roytc_row">
                         <label>Dots color</label>
                              <div class="margin-form">
                              <input type="color" name="nc_mobadotsc" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_MOBADOTSCS') . '" />
                         </div></div>

                </div>

                <div class="tab-content hide" id="tab-productpage2">

                        <h3 class="first">' . $this->l('Product Combinations') . '</h3>


                        <div class="roytc_row ds_wrap">
                              <label>Radio Button style</label>
                              <div class="margin-form">
                              <input type="radio" class="regular-radio nc_att_radio" name="nc_att_radio" id="nc_att_radio1" value="1" ' . ((Configuration::get('NC_ATT_RADIOS') == "1") ? 'checked="checked" ' : '') . '/>
                              <label class="ds nc_att_radio1" for="nc_att_radio1"> <span>1 . Circle</span></label>
                              <input type="radio" class="regular-radio nc_att_radio" name="nc_att_radio" id="nc_att_radio2" value="2" ' . ((Configuration::get('NC_ATT_RADIOS') == "2") ? 'checked="checked" ' : '') . '/>
                              <label class="ds nc_att_radio2" for="nc_att_radio2"> <span>2 . Rectangle</span></label>
                        </div></div>


                        <div class="roytc_row">
                        <label>Product variants label</label>
                             <div class="margin-form">
                             <input type="color" name="pp_att_label" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PP_ATT_LABEL') . '" />
                        </div></div>
                        <div class="roytc_row">
                        <label>Selected option border</label>
                             <div class="margin-form">
                             <input type="color" name="pp_att_color_active" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PP_ATT_COLOR_ACTIVE') . '" />
                        </div></div>


                      <h3>' . $this->l('Buy block') . '</h3>

                      <div class="roytc_row">
                      <label>Price color</label>
                           <div class="margin-form">
                           <input type="color" name="pp_price_color" class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_PP_PRICE_COLOR') . '" />
                      </div></div>
                      <div class="roytc_row">
                      <label>Old Price color</label>
                           <div class="margin-form">
                           <input type="color" name="pp_price_coloro" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PP_PRICE_COLORO') . '" />
                      </div></div>

                     <div class="half_container low_paddings" style="display:inline-block; width:100%">
                        <div class="half">
                        <div class="roytc_row">
                        <label>Add to cart product background</label>
                             <div class="margin-form">
                             <input type="color" name="nc_pp_add_bg" class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('NC_PP_ADD_BG') . '" />
                        </div></div>
                        <div class="roytc_row">
                        <label>Add to cart product border</label>
                             <div class="margin-form">
                             <input type="color" name="nc_pp_add_border" class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('NC_PP_ADD_BORDER') . '" />
                        </div></div>
                        <div class="roytc_row">
                        <label>Add to cart product color</label>
                             <div class="margin-form">
                             <input type="color" name="nc_pp_add_color" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_PP_ADD_COLOR') . '" />
                        </div></div>
                        
                        <div class="roytc_row ds_wrap">
                              <label>Old price display</label>
                              <div class="margin-form">
                                    <input type="radio" class="regular-radio nc_oldprice" name="nc_oldprice" id="nc_oldprice1" value="1" ' . ((Configuration::get('NC_OLDPRICE') == "1") ? 'checked="checked" ' : '') . '/>
                                    <label class="ds nc_oldprice1" for="nc_oldprice1"> <span>1 . Text after</span></label>
                                    <input type="radio" class="regular-radio nc_oldprice" name="nc_oldprice" id="nc_oldprice2" value="2" ' . ((Configuration::get('NC_OLDPRICE') == "2") ? 'checked="checked" ' : '') . '/>
                                    <label class="ds nc_oldprice2" for="nc_oldprice2"> <span>2 . Strikethrough number</span></label>
                        </div></div>

                     </div><div class="half half_right">
                     </div></div>

                  <h3>' . $this->l('Special price Countdown') . '</h3>
                         <div class="half_container low_paddings" style="display:inline-block; width:100%">
                            <div class="half">

             <div class="roytc_row">
             <label>Title</label>
                  <div class="margin-form">
                  <input type="color" name="nc_count_pr_title" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_COUNT_PR_TITLE') . '" />
             </div></div>
             <div class="roytc_row">
             <label>Countdown  background</label>
                  <div class="margin-form">
                  <input type="color" name="nc_count_pr_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_COUNT_PR_BG') . '" />
             </div></div>
             <div class="roytc_row">
             <label>Countdown border</label>
                  <div class="margin-form">
                  <input type="color" name="nc_count_pr_sep" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_COUNT_PR_SEP') . '" />
             </div></div>
                         </div><div class="half half_right">

          <div class="roytc_row">
          <label>Numbers</label>
               <div class="margin-form">
               <input type="color" name="nc_count_pr_numbers" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_COUNT_PR_NUMBERS') . '" />
          </div></div>
          <div class="roytc_row">
          <label>Text</label>
               <div class="margin-form">
               <input type="color" name="nc_count_pr_color" class="colorpicker" data-hex="true" value="' . Configuration::get('NC_COUNT_PR_COLOR') . '" />
          </div></div>
                         </div></div>

                </div>


                    <h3>' . $this->l('Info blocks') . '</h3>

                    <div class="roytc_row">
                    <label>Product info label</label>
                         <div class="margin-form">
                         <input type="color" name="pp_info_label" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PP_INFO_LABEL') . '" />
                    </div></div>
                    <div class="roytc_row">
                    <label>Product info value</label>
                         <div class="margin-form">
                         <input type="color" name="pp_info_value" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_PP_INFO_VALUE') . '" />
                    </div></div>

                    <div class="roytc_row">
                    <label>Display Quantity?</label>
                        <div class="margin-form">
                        <input type="radio" class="regular-radio" name="pp_display_q" id="pp_display_q_1" value="1" ' . ((Configuration::get('RC_PP_DISPLAY_Q') == 1) ? 'checked="checked" ' : '') . '/>
                        <label class="t" for="pp_display_q_1"> Yes</label>
                        <input type="radio" class="regular-radio" name="pp_display_q" id="pp_display_q_0" value="0" ' . ((Configuration::get('RC_PP_DISPLAY_Q') == 0) ? 'checked="checked" ' : '') . '/>
                        <label class="t" for="pp_display_q_0"> No</label>
                    </div></div>
                    <div class="roytc_row">
                    <label>Display Product code?</label>
                        <div class="margin-form">
                        <input type="radio" class="regular-radio" name="pp_display_refer" id="pp_display_refer_1" value="1" ' . ((Configuration::get('RC_PP_DISPLAY_REFER') == 1) ? 'checked="checked" ' : '') . '/>
                        <label class="t" for="pp_display_refer_1"> Yes</label>
                        <input type="radio" class="regular-radio" name="pp_display_refer" id="pp_display_refer_0" value="0" ' . ((Configuration::get('RC_PP_DISPLAY_REFER') == 0) ? 'checked="checked" ' : '') . '/>
                        <label class="t" for="pp_display_refer_0"> No</label>
                    </div></div>
                    <div class="roytc_row">
                    <label>Display Condition?</label>
                        <div class="margin-form">
                        <input type="radio" class="regular-radio" name="pp_display_cond" id="pp_display_cond_1" value="1" ' . ((Configuration::get('RC_PP_DISPLAY_COND') == 1) ? 'checked="checked" ' : '') . '/>
                        <label class="t" for="pp_display_cond_1"> Yes</label>
                        <input type="radio" class="regular-radio" name="pp_display_cond" id="pp_display_cond_0" value="0" ' . ((Configuration::get('RC_PP_DISPLAY_COND') == 0) ? 'checked="checked" ' : '') . '/>
                        <label class="t" for="pp_display_cond_0"> No</label>
                    </div></div>
                    <div class="roytc_row">
                    <label>Display Brand?</label>
                        <div class="margin-form">
                        <input type="radio" class="regular-radio" name="pp_display_brand" id="pp_display_brand_1" value="1" ' . ((Configuration::get('RC_PP_DISPLAY_BRAND') == 1) ? 'checked="checked" ' : '') . '/>
                        <label class="t" for="pp_display_brand_1"> Yes</label>
                        <input type="radio" class="regular-radio" name="pp_display_brand" id="pp_display_brand_0" value="0" ' . ((Configuration::get('RC_PP_DISPLAY_BRAND') == 0) ? 'checked="checked" ' : '') . '/>
                        <label class="t" for="pp_display_brand_0"> No</label>
                    </div></div>



            </div>


            <div class="tab-pane" id ="tab-cart">
                <h2 class="rtc_title7">' . $this->l('Cart and order') . '</h2>

              <h3 class="first">' . $this->l('Added to cart') . '</h3>

              <div class="roytc_row ds_wrap">
                    <label>Add to cart action</label>
                    <div class="margin-form">
                          <input type="radio" class="regular-radio o_add" name="o_add" id="o_add1" value="1" ' . ((Configuration::get('RC_O_ADDS') == "1") ? 'checked="checked" ' : '') . '/>
                          <label class="ds o_add1" for="o_add1"> <span>1 . Sidebar animation</span></label>
                          <input type="radio" class="regular-radio o_add" name="o_add" id="o_add2" value="2" ' . ((Configuration::get('RC_O_ADDS') == "2") ? 'checked="checked" ' : '') . '/>
                          <label class="ds o_add2" for="o_add2"> <span>2 . Classic Popup</span></label>
                          <input type="radio" class="regular-radio o_add" name="o_add" id="o_add3" value="3" ' . ((Configuration::get('RC_O_ADDS') == "3") ? 'checked="checked" ' : '') . '/>
                          <label class="ds o_add3" for="o_add3"> <span>3 . Mini Notify</span></label>
              </div></div>

              <div class="roytc_row">
              <label>Added background</label>
                   <div class="margin-form">
                   <input type="color" name="lc_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_LC_BG') . '" />
              </div></div>
              <div class="roytc_row">
              <label>Added color</label>
                   <div class="margin-form">
                   <input type="color" name="lc_c" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_LC_C') . '" />
              </div></div>


                <h3>' . $this->l('Order options') . '</h3>

                <div class="roytc_row">
                     <label>Order option border</label>
                     <div class="margin-form">
                     <input type="color" name="o_option" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_O_OPTION') . '" />
                </div></div>
                <div class="roytc_row">
                     <label>Order option selected border</label>
                     <div class="margin-form">
                     <input type="color" name="o_option_active" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_O_OPTION_ACTIVE') . '" />
                </div></div>

            <h3>' . $this->l('Block reassurance') . '</h3>
                <div class="roytc_row">
                     <label>Text</label>
                     <div class="margin-form">
                     <input type="color" name="o_info_text" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_O_INFO_TEXT') . '" />
                </div></div>


            </div>


            <div class="tab-pane" id ="tab-footer">
                    <h2 class="rtc_title10">' . $this->l('Footer') . '</h2>

                        <h3 class="first">' . $this->l('Footer Layout') . '</h3>
                        <div class="roytc_row ds_wrap">
                              <label>Select your Footer</label>
                              <div class="margin-form" style="margin-top:0; padding-top:10px;">
                                  <input type="radio" class="regular-radio" name="footer_lay" value="1" id="footer_lay1" ' . ((Configuration::get('RC_FOOTER_LAY') == "1") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds footer_lay1" for="footer_lay1"> <span>1 . Classic</span></label>
                                  <input type="radio" class="regular-radio" name="footer_lay" value="2" id="footer_lay2" ' . ((Configuration::get('RC_FOOTER_LAY') == "2") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds footer_lay2" for="footer_lay2"> <span>2 . Side aligned</span></label>
                                  <input type="radio" class="regular-radio" name="footer_lay" value="3" id="footer_lay3" ' . ((Configuration::get('RC_FOOTER_LAY') == "3") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds footer_lay3" for="footer_lay3"> <span>3 . Centered</span></label>
                                  <input type="radio" class="regular-radio" name="footer_lay" value="4" id="footer_lay4" ' . ((Configuration::get('RC_FOOTER_LAY') == "4") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds footer_lay4" for="footer_lay4"> <span>4 . Minimal</span></label>
                        </div></div>

                        <h3>' . $this->l('Footer logo') . '</h3>
                        <div class="roytc_row" style="margin-top:0;">
                              <label>Logo upload</label>
                              <div class="margin-form" style="margin-top:0;">
                                    <input id="logo_footer_field2" type="file" name="logo_footer_field2">
                                    <input id="logo_footer_button2" type="submit" class="button" name="logo_footer_button2" value="' . $this->l('Upload') . '">
                                    <p class="clear helpcontent">' . $this->l('There is no max height or max width for footer logo. So you should resize your logo to size you need before upload. It is set this way for cases if you want upload really wide logo for footer with centered logo or high logo with full footer column height. Preffered format - transparent .png') . '</p>
                              </div>';
        $logo_footer_ext = Configuration::get('NC_LOGO_FOOTER');
        if ($logo_footer_ext != "") {
            if (Shop::getContext() == Shop::CONTEXT_SHOP)
                $adv_imgname = 'logo-footer' . '-' . (int)$this->context->shop->getContextShopID();

            $html .= '<label>Uploaded logo</label>
                                                      <div class="margin-form">
                                                      <img class="imgback" src="' . $this->_path . 'upload/' . $adv_imgname . '.' . $logo_footer_ext . '" /><br /><br />
                                                      <input id="logo_footer_delete2" type="submit" class="button" value="' . $this->l('Delete image') . '" name="logo_footer_delete2">
                                                      </div>';
        }

        $html .= '
                        </div>

                        <h3>' . $this->l('Footer Design') . '</h3>
                  <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                  <div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                             <label>Footer main background</label>
                             <div class="margin-form">
                             <input type="color" name="footer_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_FOOTER_BG') . '" />
                        </div></div>
                  </div><div class="half" style="width:620px; padding-bottom:0;">

                  </div></div>
                        <div class="hr"></div>
                  <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                  <div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                              <label>Footer titles</label>
                              <div class="margin-form">
                               <input type="color" name="footer_titles" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_FOOTER_TITLES') . '" />
                        </div></div>
                        <div class="roytc_row">
                              <label>Text color</label>
                              <div class="margin-form">
                               <input type="color" name="footer_text" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_FOOTER_TEXT') . '" />
                        </div></div>
                  </div><div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                              <label>Link color</label>
                              <div class="margin-form">
                               <input type="color" name="footer_link" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_FOOTER_LINK') . '" />
                        </div></div>
                        <div class="roytc_row">
                              <label>Link hover color</label>
                              <div class="margin-form">
                               <input type="color" name="footer_link_h"  class="colorpicker" data-hex="true" value="' . Configuration::get('RC_FOOTER_LINK_H') . '" />
                        </div></div>
                  </div></div>

                  <h3>' . $this->l('Newsletter block') . '</h3>
              <div class="half_container low_paddings" style="display:inline-block; width:100%;">
              <div class="half" style="width:620px; padding-bottom:0;">
                  <div class="roytc_row">
                       <label>Newsletter input background</label>
                       <div class="margin-form">
                       <input type="color" name="footer_news_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_FOOTER_NEWS_BG') . '" />
                  </div></div>
                  <div class="roytc_row">
                       <label>Newsletter input border</label>
                       <div class="margin-form">
                       <input type="color" name="footer_news_border" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_FOOTER_NEWS_BORDER') . '" />
                  </div></div>
                  <div class="roytc_row">
                       <label>Newsletter button color</label>
                       <div class="margin-form">
                       <input type="color" name="footer_news_button" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_FOOTER_NEWS_BUTTON') . '" />
                  </div></div>
              </div><div class="half" style="width:620px; padding-bottom:0;">
                  <div class="roytc_row">
                       <label>Newsletter input default text</label>
                       <div class="margin-form">
                       <input type="color" name="footer_news_placeh" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_FOOTER_NEWS_PLACEH') . '" />
                  </div></div>
                  <div class="roytc_row">
                       <label>Newsletter input color</label>
                       <div class="margin-form">
                       <input type="color" name="footer_news_color" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_FOOTER_NEWS_COLOR') . '" />
                  </div></div>
              </div></div>

            </div>

            <div class="tab-pane" id ="tab-blog">
                    <h2 class="rtc_title9">' . $this->l('Blog') . '</h2>

                    <h3 class="first">' . $this->l('Home Latest posts') . '</h3>

                  <div class="roytc_row ds_wrap">
                        <label>Blog post layout</label>
                        <div class="margin-form">
                              <input type="radio" class="regular-radio bl_lay" name="bl_lay" id="bl_lay_1" value="1" ' . ((Configuration::get('RC_BL_LAY') == "1") ? 'checked="checked" ' : '') . '/>
                              <label class="ds blog_lay1" for="bl_lay_1"> <span>1 . Classic</span></label>
                              <input type="radio" class="regular-radio bl_lay" name="bl_lay" id="bl_lay_2" value="2" ' . ((Configuration::get('RC_BL_LAY') == "2") ? 'checked="checked" ' : '') . '/>
                              <label class="ds blog_lay2" for="bl_lay_2"> <span>2 . Minimal</span></label>
                  </div></div>

                  <div class="roytc_row ds_wrap">
                        <label>Post container</label>
                        <div class="margin-form" style="margin-top:0; padding-top:10px;">
                            <input type="radio" class="regular-radio" name="bl_cont" value="1" id="bl_cont1" ' . ((Configuration::get('RC_BL_CONT') == "1") ? 'checked="checked" ' : '') . ' />
                            <label class="ds blog_wrap1" for="bl_cont1"> <span>1 . No</span></label>
                            <input type="radio" class="regular-radio" name="bl_cont" value="2" id="bl_cont2" ' . ((Configuration::get('RC_BL_CONT') == "2") ? 'checked="checked" ' : '') . ' />
                            <label class="ds blog_wrap2" for="bl_cont2"> <span>2 . Text only</span></label>
                            <input type="radio" class="regular-radio" name="bl_cont" value="3" id="bl_cont3" ' . ((Configuration::get('RC_BL_CONT') == "3") ? 'checked="checked" ' : '') . ' />
                            <label class="ds blog_wrap3" for="bl_cont3"> <span>3 . Whole post</span></label>
                  </div></div>

                  <div class="roytc_row ds_wrap">
                        <label>Posts per row?</label>
                        <div class="margin-form">
                              <input type="radio" class="regular-radio bl_row" name="bl_row" id="bl_row2" value="2" ' . ((Configuration::get('RC_BL_ROW') == "2") ? 'checked="checked" ' : '') . '/>
                              <label class="ds items_onrow2" for="bl_row2"> <span>2</span></label>
                              <input type="radio" class="regular-radio bl_row" name="bl_row" id="bl_row3" value="3" ' . ((Configuration::get('RC_BL_ROW') == "3") ? 'checked="checked" ' : '') . '/>
                              <label class="ds items_onrow3" for="bl_row3"> <span>3</span></label>
                              <input type="radio" class="regular-radio bl_row" name="bl_row" id="bl_row4" value="4" ' . ((Configuration::get('RC_BL_ROW') == "4") ? 'checked="checked" ' : '') . '/>
                              <label class="ds items_onrow4" for="bl_row4"> <span>4</span></label>
                  </div></div>

                  <h3>' . $this->l('Home latest posts header') . '</h3>

               <div class="half_container low_paddings" style="display:inline-block; width:100%;">
               <div class="half" style="width:620px; padding-bottom:0;">
                  <div class="roytc_row">
                       <label>Header color</label>
                       <div class="margin-form">
                       <input type="color" name="bl_head" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_BL_HEAD') . '" />
                  </div></div>
                  <div class="roytc_row">
                        <label>Header hover color</label>
                        <div class="margin-form">
                        <input type="color" name="bl_head_hover"  class="colorpicker cs_mc" data-hex="true" value="' . Configuration::get('RC_BL_HEAD_HOVER') . '" />
                  </div></div>
               </div><div class="half" style="width:620px; padding-bottom:0;">
               </div></div>

                 <h3>' . $this->l('Home latest post design') . '</h3>

                  <div class="half_container low_paddings" style="display:inline-block; width:100%;">
                  <div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                             <label>Home post title</label>
                             <div class="margin-form">
                             <input type="color" name="bl_h_title" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_BL_H_TITLE') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Home post title hover</label>
                             <div class="margin-form">
                             <input type="color" name="bl_h_title_h" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_BL_H_TITLE_H') . '" />
                        </div></div>
                          <div class="roytc_row">
                               <label>Post date and meta</label>
                               <div class="margin-form">
                               <input type="color" name="bl_h_meta" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_BL_H_META') . '" />
                          </div></div>
                  </div><div class="half" style="width:620px; padding-bottom:0;">
                        <div class="roytc_row">
                             <label>Post background</label>
                             <div class="margin-form">
                             <input type="color" name="bl_h_bg" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_BL_H_BG') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Post border</label>
                             <div class="margin-form">
                             <input type="color" name="bl_h_border" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_BL_H_BORDER') . '" />
                        </div></div>
                  </div></div>

                   <h3>' . $this->l('Blog category') . '</h3>

                        <div class="roytc_row ds_wrap">
                              <label>Posts in a row</label>
                              <div class="margin-form" style="margin-top:0; padding-top:10px;">
                                  <input type="radio" class="regular-radio" name="bl_c_row" value="2" id="bl_c_row2" ' . ((Configuration::get('RC_BL_C_ROW') == "2") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds items_onrow2" for="bl_c_row2"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="bl_c_row" value="3" id="bl_c_row3" ' . ((Configuration::get('RC_BL_C_ROW') == "3") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds items_onrow3" for="bl_c_row3"> <span></span></label>
                                  <input type="radio" class="regular-radio" name="bl_c_row" value="4" id="bl_c_row4" ' . ((Configuration::get('RC_BL_C_ROW') == "4") ? 'checked="checked" ' : '') . ' />
                                  <label class="ds items_onrow4" for="bl_c_row4"> <span></span></label>
                        </div></div>

                        <div class="roytc_row">
                             <label>Post description</label>
                             <div class="margin-form">
                             <input type="color" name="bl_desc" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_BL_DESC') . '" />
                        </div></div>

                        <div class="roytc_row">
                             <label>Read more color</label>
                             <div class="margin-form">
                             <input type="color" name="bl_rm_color" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_BL_RM_COLOR') . '" />
                        </div></div>
                        <div class="roytc_row">
                             <label>Read more hover color</label>
                             <div class="margin-form">
                             <input type="color" name="bl_rm_hover" class="colorpicker" data-hex="true" value="' . Configuration::get('RC_BL_RM_HOVER') . '" />
                        </div></div>
            </div>

            <div class="tab-pane" id ="tab-css">
                    <h2 class="rtc_title12">' . $this->l('Custom CSS') . '</h2>
                        <div class="roytc_row">
                             <label style="width:250px;">Put your Custom CSS here</label>
                             <div class="margin-form" style="padding-left:330px;">
                             <textarea name="nc_css" id="code" cols="70" rows="10">' . Configuration::get('NC_CSS') . '</textarea>
                             <p class="clear helpcontent">Click on area and put your custom CSS code. Then click Save changes to override CSS of theme</p>
                             <p class="clear link">Highlighting CSS editor by <a href="http://codemirror.net/" target="_blank">codemirror</a></p>
                        </div></div>
            </div>

            <div class="tab-pane" id ="tab-ie">
                    <h2 class="rtc_title13">' . $this->l('Import / Export config') . '</h2>
                        <div class="roytc_row">
                             <div class="margin-form-ie exp">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                             <label>Export config</label>
                                 <input id="export_changes" type="submit" class="button save_button" value="' . $this->l('Export config') . '" name="export_changes">
                             </div>
                             <div class="margin-form-ie imp" style="float:right;">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                             <label>Import config</label>
                                 <input id="ayon_import_file" type="file" name="ayon_import_file">
                                 <input id="ayon_import_submit" type="submit" class="button" name="ayon_import_submit" value="' . $this->l('Import config') . '">
                            </div>
                             <p class="clear helpcontent">After you finished to customize your design, you can EXPORT your config and save it on your computer. <br /> Then you can import it and set your design in one click. <br /> It is useful to make backup of your work.</p>
                             <p class="clear helpcontent">Do not forget to Save Changes after Import.</p>
                             <p class="clear helpcontent">Exported config include all your Customizer settings except uploaded images.</p>
                        </div>
            </div>
                </div>
              </div>
        <div class="roytc_save">
            <div class="reset_container">Reset changes</div>
            <div class="reset_popup"><span>Are you sure?</span><div><input id="reset_changes" type="submit" class="reset_button" value="' . $this->l('Yes') . '" name="reset_changes"><input type="button" class="button no-button" value="No"></div></div>
            <input id="save_changes" type="submit" class="save_button" value="' . $this->l('Save changes') . '" name="save_changes">
	    </div>

			</fieldset>
			</form>
			';

            
        $html .= '';

        return $html;
    }

    public function generateCss()
    {
        $css = '';

        // ****************  General Settings styles start

        // FONTS BY class

        $fontHeadings = Configuration::get('RC_F_HEADINGS');
        $fontButtons = Configuration::get('RC_F_BUTTONS');
        $fontText = Configuration::get('RC_F_TEXT');
        $fontPrice = Configuration::get('RC_F_PRICE');
        $fontPn = Configuration::get('RC_F_PN');

        $ffsupport = '';

        if ((Configuration::get('RC_LATIN_EXT') == 1) || (Configuration::get('RC_CYRILLIC') == 1)) {
            $ffsupport .= '&subset=';
        }

        if ((Configuration::get('RC_LATIN_EXT')) == 1) {
            $ffsupport .= 'latin,latin-ext';
        }
        if ((Configuration::get('RC_LATIN_EXT') == 1) && (Configuration::get('RC_CYRILLIC') == 1)) {
            $ffsupport .= ',';
        }
        if ((Configuration::get('RC_CYRILLIC')) == 1) {
            $ffsupport .= 'cyrillic,cyrillic-ext';
        }

        $font_w = ':wght@400;500;600;700';
        $font_include = '';

        $arr = array($fontHeadings, $fontButtons, $fontText, $fontPrice, $fontPn);
        $filtered = array();

        foreach ($arr as $item) {
            if (!in_array($item, $filtered)) {
                $filtered[] = $item;
            }
        }

        $arr = $filtered;
        $sysFonts = $this->systemFonts;
        $arr = array_filter($arr, function ($v) use ($sysFonts) {
            return !in_array($v, $sysFonts);
        });

        for ($i = 0; $i < count($arr); ++$i) {
            $font = $arr[$i];
            $font = str_replace(' ', '+', $font);
            $font_include .= "@import url('https://fonts.googleapis.com/css2?family=" . $font . $font_w . "&display=swap'); ";
        }


        $css .= $font_include;

        // FONTS BY class

        if (Configuration::get('RC_G_LAY') == "1") {
            $css .= '
            .layout-full-width.page-index #main {
                padding: 0;
            }
            ';
        }
        if (Configuration::get('RC_G_LAY') == "2") {
            $css .= '
                  @media (min-width:992px) {
                    .container {
                      width:100%;
                      padding:0 110px;
                    }
                    #content-wrapper.left-column, #content-wrapper.right-column, .layout-left-column #content-wrapper, .layout-right-column #content-wrapper {
                      width:75%
                    }
                    #left-column.side-column, #right-column.side-column, .layout-left-column #left-column, .layout-right-column #right-column {
                      width:25%;
                    }
                    body.layout-full-width #main {
                      padding-left:0;
                      padding-right:0;
                    }
                  }
            ';
        }

        if (Configuration::get('RC_G_LAY') == "3") {
            $css .= '
                  @media (min-width:992px) {

                    #wrapper .container { width:100%; }

                    #content-wrapper.left-column, #content-wrapper.right-column, .layout-left-column #content-wrapper, .layout-right-column #content-wrapper {
                      width:75%
                    }
                    #left-column.side-column, #right-column.side-column, .layout-left-column #left-column, .layout-right-column #right-column {
                      width:25%;
                    }
                    #wrapper {
                      padding:22px 7px;
                      margin:22px auto 0;
                    }
                    body { padding-top: ' . Configuration::get('RC_G_TP') . 'px }
                    body { padding-bottom: ' . Configuration::get('RC_G_BP') . 'px }  }

                    @media (max-width: 767px) {
                      #wrapper {
                        padding:22px 7px;
                      }
                    }
                    @media (min-width: 768px) {
                      #wrapper {
                        max-width:690px;
                        margin-left:auto;
                        margin-right:auto;
                        padding:22px 7px;
                      }
                    }
                    @media (min-width: 992px) {
                      #wrapper {
                        max-width:930px;
                        padding:22px 7px;
                      }
                    }
                    @media (min-width:1200px) {
                      #wrapper {
                        max-width:1110px;
                      }
                    }
                    @media (min-width:1440px) {
                      #wrapper {
                        max-width:1230px;
                      }
                    }
                    @media (max-width: 991px) {
                      #header .header-nav {
                        margin-bottom:40px;
                      }
                      #wrapper { margin-top:0 }
                    }
                    @media (max-width: 767px) {
                      #header .header-nav {
                        margin-bottom:0;
                      }
                    }
            ';

            if (Configuration::get('RC_BODY_BOX_SW') == "1" && Configuration::get('RC_MAIN_BACKGROUND_COLOR')) {
                $css .= '
               body { background-color: ' . Configuration::get('RC_MAIN_BACKGROUND_COLOR') . ' }
      ';
            }

            if (Configuration::get('RC_BODY_BOX_SW') == "2") {
                $css .= 'body {
                  background: -webkit-linear-gradient(' . Configuration::get('NC_BODY_GG') . 'deg,' . Configuration::get('NC_BODY_GS') . ' 0%,' . Configuration::get('NC_BODY_GE') . ' 100%);
                  background: -moz-linear-gradient(' . Configuration::get('NC_BODY_GG') . 'deg,' . Configuration::get('NC_BODY_GS') . ' 0%,' . Configuration::get('NC_BODY_GE') . ' 100%);
                  background: -o-linear-gradient(' . Configuration::get('NC_BODY_GG') . 'deg,' . Configuration::get('NC_BODY_GS') . ' 0%,' . Configuration::get('NC_BODY_GE') . ' 100%);
                  background: linear-gradient(' . Configuration::get('NC_BODY_GG') . 'deg,' . Configuration::get('NC_BODY_GS') . ' 0%,' . Configuration::get('NC_BODY_GE') . ' 100%);
                  background-attachment: fixed;
            }
      ';
            }

            if (Configuration::get('RC_BODY_BOX_SW') == "3") {

                if (Configuration::get('NC_BODY_IM_BG_EXT')) {
                    if (Shop::getContext() == Shop::CONTEXT_SHOP)
                        $adv_imgname = 'nc_body_im_background' . '-' . (int)$this->context->shop->getContextShopID();
                    $css .= '
                      body {
                      background-image: url(../upload/' . $adv_imgname . '.' . Configuration::get('NC_BODY_IM_BG_EXT') . '); }
                      ';
                    if (Configuration::get('NC_BODY_IM_BG_FIXED')) {
                        $css .= 'body { background-attachment:fixed; }
                      ';
                    }
                }
                if (Configuration::get('NC_BODY_IM_BG_REPEAT')) {
                    switch (Configuration::get('NC_BODY_IM_BG_REPEAT')) {
                        case 1:
                            $repeat_option = 'repeat-x';
                            break;
                        case 2:
                            $repeat_option = 'repeat-y';
                            break;
                        case 3:
                            $repeat_option = 'no-repeat';
                            break;
                        default:
                            $repeat_option = 'repeat';
                    }
                    $css .= 'body { background-repeat: ' . $repeat_option . '; }
                  ';
                }
                if (Configuration::get('NC_BODY_IM_BG_POSITION')) {
                    switch (Configuration::get('NC_BODY_IM_BG_POSITION')) {
                        case 1:
                            $position_option = 'center top';
                            break;
                        case 2:
                            $position_option = 'right top';
                            break;
                        default:
                            $position_option = 'left top';
                    }
                    $css .= 'body { background-position: ' . $position_option . '; }
                  ';
                }
            }


            $css .= '
        #wrapper, #footer, #header:before, .bread_wrapper { background: none }
        body, html { height:auto }
      ';

            if (Configuration::get('NC_MAIN_BGS') == "1") {
                $css .= '#wrapper { background-color: ' . Configuration::get('NC_MAIN_BC') . ' }
      ';
            }

            if (Configuration::get('NC_MAIN_BGS') == "2") {
                $css .= '#wrapper {
                  background: -webkit-linear-gradient(' . Configuration::get('NC_MAIN_GG') . 'deg,' . Configuration::get('NC_MAIN_GS') . ' 0%,' . Configuration::get('NC_MAIN_GE') . ' 100%);
                  background: -moz-linear-gradient(' . Configuration::get('NC_MAIN_GG') . 'deg,' . Configuration::get('NC_MAIN_GS') . ' 0%,' . Configuration::get('NC_MAIN_GE') . ' 100%);
                  background: -o-linear-gradient(' . Configuration::get('NC_MAIN_GG') . 'deg,' . Configuration::get('NC_MAIN_GS') . ' 0%,' . Configuration::get('NC_MAIN_GE') . ' 100%);
                  background: linear-gradient(' . Configuration::get('NC_MAIN_GG') . 'deg,' . Configuration::get('NC_MAIN_GS') . ' 0%,' . Configuration::get('NC_MAIN_GE') . ' 100%);
            }
      ';
            }

            if (Configuration::get('NC_MAIN_BGS') == "3") {

                if (Configuration::get('NC_MAIN_IM_BG_EXT')) {
                    if (Shop::getContext() == Shop::CONTEXT_SHOP)
                        $adv_imgname = 'nc_main_im_background' . '-' . (int)$this->context->shop->getContextShopID();
                    $css .= '
                      #wrapper {
                      background-image: url(../upload/' . $adv_imgname . '.' . Configuration::get('NC_MAIN_IM_BG_EXT') . '); }
                      ';
                    if (Configuration::get('NC_MAIN_IM_BG_FIXED')) {
                        $css .= '#wrapper { background-attachment:fixed; }
                      ';
                    }
                }
                if (Configuration::get('NC_MAIN_IM_BG_REPEAT')) {
                    switch (Configuration::get('NC_MAIN_IM_BG_REPEAT')) {
                        case 1:
                            $repeat_option = 'repeat-x';
                            break;
                        case 2:
                            $repeat_option = 'repeat-y';
                            break;
                        case 3:
                            $repeat_option = 'no-repeat';
                            break;
                        default:
                            $repeat_option = 'repeat';
                    }
                    $css .= '#wrapper { background-repeat: ' . $repeat_option . '; }
                  ';
                }
                if (Configuration::get('NC_MAIN_IM_BG_POSITION')) {
                    switch (Configuration::get('NC_MAIN_IM_BG_POSITION')) {
                        case 1:
                            $position_option = 'center top';
                            break;
                        case 2:
                            $position_option = 'right top';
                            break;
                        default:
                            $position_option = 'left top';
                    }
                    $css .= '#wrapper { background-position: ' . $position_option . '; }
                  ';
                }
            }

            if (Configuration::get('NC_MAIN_BGS') == "4") {
                $css .= '#wrapper { background: none; }';
            }
        }

        if (Configuration::get('RC_G_LAY') == "4") {
            $css .= '
                  @media (min-width:992px) {

                    .container { padding:0 40px; }
                    #wrapper .container { width:100%; }
                    .footer-container .container { padding:0 60px; }

                    #content-wrapper.left-column, #content-wrapper.right-column, .layout-left-column #content-wrapper, .layout-right-column #content-wrapper {
                      width:75%
                    }
                    #left-column.side-column, #right-column.side-column, .layout-left-column #left-column, .layout-right-column #right-column {
                      width:25%;
                    }

                    .lay_boxed {
                      padding:0;
                      margin:0 auto;
                    }

                    body { padding-top: ' . Configuration::get('RC_G_TP') . 'px }
                    body { padding-bottom: ' . Configuration::get('RC_G_BP') . 'px }  }

                    @media (max-width: 767px) {
                      .lay_boxed {
                        padding:0;
                      }
                    }
                    @media (min-width: 768px) {
                      .lay_boxed {
                        max-width:690px;
                        margin-left:auto;
                        margin-right:auto;
                      }
                    }
                    @media (min-width: 992px) {
                      .lay_boxed {
                        max-width:930px;
                      }
                    }
                    @media (min-width:1200px) {
                      .lay_boxed {
                        max-width:1110px;
                      }
                    }
                    @media (min-width:1440px) {
                      .lay_boxed {
                        max-width:1230px;
                      }
                    }
                    @media (max-width: 991px) {
                      #header .header-nav {
                        margin-bottom:40px;
                      }
                      .lay_boxed { margin-top:0 }
                    }
                    @media (max-width: 767px) {
                      #header .header-nav {
                        margin-bottom:0;
                      }
                    }
            ';

            if (Configuration::get('RC_BODY_BOX_SW') == "1" && Configuration::get('RC_MAIN_BACKGROUND_COLOR')) {
                $css .= '
               body { background-color: ' . Configuration::get('RC_MAIN_BACKGROUND_COLOR') . ' }
      ';
            }

            if (Configuration::get('RC_BODY_BOX_SW') == "2") {
                $css .= 'body {
                  background: -webkit-linear-gradient(' . Configuration::get('NC_BODY_GG') . 'deg,' . Configuration::get('NC_BODY_GS') . ' 0%,' . Configuration::get('NC_BODY_GE') . ' 100%);
                  background: -moz-linear-gradient(' . Configuration::get('NC_BODY_GG') . 'deg,' . Configuration::get('NC_BODY_GS') . ' 0%,' . Configuration::get('NC_BODY_GE') . ' 100%);
                  background: -o-linear-gradient(' . Configuration::get('NC_BODY_GG') . 'deg,' . Configuration::get('NC_BODY_GS') . ' 0%,' . Configuration::get('NC_BODY_GE') . ' 100%);
                  background: linear-gradient(' . Configuration::get('NC_BODY_GG') . 'deg,' . Configuration::get('NC_BODY_GS') . ' 0%,' . Configuration::get('NC_BODY_GE') . ' 100%);
                  background-attachment: fixed;
            }
      ';
            }

            if (Configuration::get('RC_BODY_BOX_SW') == "3") {

                if (Configuration::get('NC_BODY_IM_BG_EXT')) {
                    if (Shop::getContext() == Shop::CONTEXT_SHOP)
                        $adv_imgname = 'nc_body_im_background' . '-' . (int)$this->context->shop->getContextShopID();
                    $css .= '
                      body {
                      background-image: url(../upload/' . $adv_imgname . '.' . Configuration::get('NC_BODY_IM_BG_EXT') . '); }
                      ';
                    if (Configuration::get('NC_BODY_IM_BG_FIXED')) {
                        $css .= 'body { background-attachment:fixed; }
                      ';
                    }
                }
                if (Configuration::get('NC_BODY_IM_BG_REPEAT')) {
                    switch (Configuration::get('NC_BODY_IM_BG_REPEAT')) {
                        case 1:
                            $repeat_option = 'repeat-x';
                            break;
                        case 2:
                            $repeat_option = 'repeat-y';
                            break;
                        case 3:
                            $repeat_option = 'no-repeat';
                            break;
                        default:
                            $repeat_option = 'repeat';
                    }
                    $css .= 'body { background-repeat: ' . $repeat_option . '; }
                  ';
                }
                if (Configuration::get('NC_BODY_IM_BG_POSITION')) {
                    switch (Configuration::get('NC_BODY_IM_BG_POSITION')) {
                        case 1:
                            $position_option = 'center top';
                            break;
                        case 2:
                            $position_option = 'right top';
                            break;
                        default:
                            $position_option = 'left top';
                    }
                    $css .= 'body { background-position: ' . $position_option . '; }
                  ';
                }
            }


            $css .= '
        #wrapper, #footer, #header:before, .bread_wrapper { background: none }
        body, html { height:auto }
      ';

            if (Configuration::get('NC_MAIN_BGS') == "1") {
                $css .= '.lay_boxed { background-color: ' . Configuration::get('NC_MAIN_BC') . ' }
      ';
            }

            if (Configuration::get('NC_MAIN_BGS') == "2") {
                $css .= '.lay_boxed {
                  background: -webkit-linear-gradient(' . Configuration::get('NC_MAIN_GG') . 'deg,' . Configuration::get('NC_MAIN_GS') . ' 0%,' . Configuration::get('NC_MAIN_GE') . ' 100%);
                  background: -moz-linear-gradient(' . Configuration::get('NC_MAIN_GG') . 'deg,' . Configuration::get('NC_MAIN_GS') . ' 0%,' . Configuration::get('NC_MAIN_GE') . ' 100%);
                  background: -o-linear-gradient(' . Configuration::get('NC_MAIN_GG') . 'deg,' . Configuration::get('NC_MAIN_GS') . ' 0%,' . Configuration::get('NC_MAIN_GE') . ' 100%);
                  background: linear-gradient(' . Configuration::get('NC_MAIN_GG') . 'deg,' . Configuration::get('NC_MAIN_GS') . ' 0%,' . Configuration::get('NC_MAIN_GE') . ' 100%);
            }
      ';
            }

            if (Configuration::get('NC_MAIN_BGS') == "3") {

                if (Configuration::get('NC_MAIN_IM_BG_EXT')) {
                    if (Shop::getContext() == Shop::CONTEXT_SHOP)
                        $adv_imgname = 'nc_main_im_background' . '-' . (int)$this->context->shop->getContextShopID();
                    $css .= '
                      .lay_boxed {
                      background-image: url(../upload/' . $adv_imgname . '.' . Configuration::get('NC_MAIN_IM_BG_EXT') . '); }
                      ';
                    if (Configuration::get('NC_MAIN_IM_BG_FIXED')) {
                        $css .= '.lay_boxed { background-attachment:fixed; }
                      ';
                    }
                }
                if (Configuration::get('NC_MAIN_IM_BG_REPEAT')) {
                    switch (Configuration::get('NC_MAIN_IM_BG_REPEAT')) {
                        case 1:
                            $repeat_option = 'repeat-x';
                            break;
                        case 2:
                            $repeat_option = 'repeat-y';
                            break;
                        case 3:
                            $repeat_option = 'no-repeat';
                            break;
                        default:
                            $repeat_option = 'repeat';
                    }
                    $css .= '.lay_boxed { background-repeat: ' . $repeat_option . '; }
                  ';
                }
                if (Configuration::get('NC_MAIN_IM_BG_POSITION')) {
                    switch (Configuration::get('NC_MAIN_IM_BG_POSITION')) {
                        case 1:
                            $position_option = 'center top';
                            break;
                        case 2:
                            $position_option = 'right top';
                            break;
                        default:
                            $position_option = 'left top';
                    }
                    $css .= '.lay_boxed { background-position: ' . $position_option . '; }
                  ';
                }
            }

            if (Configuration::get('NC_MAIN_BGS') == "4") {
                $css .= '.lay_boxed { background: none; }';
            }
        }


        if (Configuration::get('RC_G_LAY') == '1' || Configuration::get('RC_G_LAY') == '2') {

            $css .= '
            #wrapper, #footer, #header:before, .bread_wrapper { background: none }
            body, html { height:auto }
          ';

            if (Configuration::get('NC_MAIN_BGS') == "1") {
                $css .= 'body { background-color: ' . Configuration::get('NC_MAIN_BC') . ' }
          ';
            }

            if (Configuration::get('NC_MAIN_BGS') == "2") {
                $css .= 'body {
                      background: -webkit-linear-gradient(' . Configuration::get('NC_MAIN_GG') . 'deg,' . Configuration::get('NC_MAIN_GS') . ' 0%,' . Configuration::get('NC_MAIN_GE') . ' 100%);
                      background: -moz-linear-gradient(' . Configuration::get('NC_MAIN_GG') . 'deg,' . Configuration::get('NC_MAIN_GS') . ' 0%,' . Configuration::get('NC_MAIN_GE') . ' 100%);
                      background: -o-linear-gradient(' . Configuration::get('NC_MAIN_GG') . 'deg,' . Configuration::get('NC_MAIN_GS') . ' 0%,' . Configuration::get('NC_MAIN_GE') . ' 100%);
                      background: linear-gradient(' . Configuration::get('NC_MAIN_GG') . 'deg,' . Configuration::get('NC_MAIN_GS') . ' 0%,' . Configuration::get('NC_MAIN_GE') . ' 100%);
                }
          ';
            }

            if (Configuration::get('NC_MAIN_BGS') == "3") {

                if (Configuration::get('NC_MAIN_IM_BG_EXT')) {
                    if (Shop::getContext() == Shop::CONTEXT_SHOP)
                        $adv_imgname = 'nc_main_im_background' . '-' . (int)$this->context->shop->getContextShopID();
                    $css .= '
                          body {
                          background-image: url(../upload/' . $adv_imgname . '.' . Configuration::get('NC_MAIN_IM_BG_EXT') . '); }
                          ';
                    if (Configuration::get('NC_MAIN_IM_BG_FIXED')) {
                        $css .= 'body { background-attachment:fixed; }
                          ';
                    }
                }
                if (Configuration::get('NC_MAIN_IM_BG_REPEAT')) {
                    switch (Configuration::get('NC_MAIN_IM_BG_REPEAT')) {
                        case 1:
                            $repeat_option = 'repeat-x';
                            break;
                        case 2:
                            $repeat_option = 'repeat-y';
                            break;
                        case 3:
                            $repeat_option = 'no-repeat';
                            break;
                        default:
                            $repeat_option = 'repeat';
                    }
                    $css .= 'body { background-repeat: ' . $repeat_option . '; }
                      ';
                }
                if (Configuration::get('NC_MAIN_IM_BG_POSITION')) {
                    switch (Configuration::get('NC_MAIN_IM_BG_POSITION')) {
                        case 1:
                            $position_option = 'center top';
                            break;
                        case 2:
                            $position_option = 'right top';
                            break;
                        default:
                            $position_option = 'left top';
                    }
                    $css .= 'body { background-position: ' . $position_option . '; }
                      ';
                }
            }

            if (Configuration::get('NC_MAIN_BGS') == "4") {
                $css .= 'body { background: none; }';
            }
        }




        // ****************  Some Globals styles start

        if (Configuration::get('NC_LOADERS') == 0) {
            $css .= '.loader-overlay { display:none!important; }
      ';
        }

        if (Configuration::get('NC_LOADER_LOGOS') == 1) {
            $css .= '
      .logo_loader { display:none!important; }
      ';
        }
        if (Configuration::get('NC_LOADER_LOGOS') == 2) {
            $css .= '
      .roy-loader:after {
        width: 60px;
        height: 60px;
        margin-top:30px;
        margin-left:-30px;
      }
      .logo_loader {
        position:absolute;
        top:50%;
        left:50%;
        transform: translate(-50%, -100%);
       }
      ';
        }
        if (Configuration::get('NC_LOADER_LOGOS') == 3) {
            $css .= '
      .roy-loader:after {
        display:none;
      }
      .logo_loader {
        position:absolute;
        top:50%;
        left:50%;
        transform: translate(-50%, -50%);
       }
      ';
        }
        if (Configuration::get('NC_LOADER_LOGOS') == 4) {
            $css .= '
      .roy-loader:after {
        width: 220px;
        height: 220px;
        margin-top:-110px;
        margin-left:-110px;
      }
      .logo_loader {
        max-width:120px;
        max-height:120px;
        position:absolute;
        top:50%;
        left:50%;
        transform: translate(-50%, -50%);
       }
      ';
        }

        if (Configuration::get('NC_LOADER_LOGOS') == 1) {
            if (Configuration::get('NC_LOGO_LOADER')) {
                if (Shop::getContext() == Shop::CONTEXT_SHOP)
                    $adv_imgname = 'logo-loader' . '-' . (int)$this->context->shop->getContextShopID();
                $css .= '
                  .loader-logo {
                  background-image: url(../upload/' . $adv_imgname . '.' . Configuration::get('NC_LOGO_LOADER') . '); }';
            }
        }
        if (Configuration::get('NC_LOADER_BG')) {
            $css .= '.roy-loader { background-color: ' . Configuration::get('NC_LOADER_BG') . ' }
      ';
        }
        if (Configuration::get('NC_LOADER_COLOR')) {
            $css .= '
      .roy-loader:after {
        background-color: transparent;
        border-top: 3px solid ' . Configuration::get('NC_LOADER_COLOR') . ';
        border-right: 3px solid ' . Configuration::get('NC_LOADER_COLOR') . ';
        border-bottom: 3px solid ' . Configuration::get('NC_LOADER_COLOR') . ';
        border-left: 2px solid transparent; }
      ';
        }


        if (Configuration::get('NC_LOADER_LAYS') == "1") {
            $css .= '

            ';
        }


        if (Configuration::get('NC_HEADER_BGS') == "1") {
            $css .= '#header { background-color: ' . Configuration::get('NC_HEADER_BC') . ' }
      ';
        }

        if (Configuration::get('NC_HEADER_BGS') == "2") {
            $css .= '#header {
                  background: -webkit-linear-gradient(' . Configuration::get('NC_HEADER_GG') . 'deg,' . Configuration::get('NC_HEADER_GS') . ' 0%,' . Configuration::get('NC_HEADER_GE') . ' 100%);
                  background: -moz-linear-gradient(' . Configuration::get('NC_HEADER_GG') . 'deg,' . Configuration::get('NC_HEADER_GS') . ' 0%,' . Configuration::get('NC_HEADER_GE') . ' 100%);
                  background: -o-linear-gradient(' . Configuration::get('NC_HEADER_GG') . 'deg,' . Configuration::get('NC_HEADER_GS') . ' 0%,' . Configuration::get('NC_HEADER_GE') . ' 100%);
                  background: linear-gradient(' . Configuration::get('NC_HEADER_GG') . 'deg,' . Configuration::get('NC_HEADER_GS') . ' 0%,' . Configuration::get('NC_HEADER_GE') . ' 100%);
            }
      ';
        }

        if (Configuration::get('NC_HEADER_BGS') == "3") {

            if (Configuration::get('NC_HEADER_IM_BG_EXT')) {
                if (Shop::getContext() == Shop::CONTEXT_SHOP)
                    $adv_imgname = 'nc_header_im_background' . '-' . (int)$this->context->shop->getContextShopID();
                $css .= '
                      #header {
                      background-image: url(../upload/' . $adv_imgname . '.' . Configuration::get('NC_HEADER_IM_BG_EXT') . '); }
                      ';
                if (Configuration::get('NC_HEADER_IM_BG_FIXED')) {
                    $css .= '#header { background-attachment:fixed; }
                      ';
                }
            }
            if (Configuration::get('NC_HEADER_IM_BG_REPEAT')) {
                switch (Configuration::get('NC_HEADER_IM_BG_REPEAT')) {
                    case 1:
                        $repeat_option = 'repeat-x';
                        break;
                    case 2:
                        $repeat_option = 'repeat-y';
                        break;
                    case 3:
                        $repeat_option = 'no-repeat';
                        break;
                    default:
                        $repeat_option = 'repeat';
                }
                $css .= '#header { background-repeat: ' . $repeat_option . '; }
                  ';
            }
            if (Configuration::get('NC_HEADER_IM_BG_POSITION')) {
                switch (Configuration::get('NC_HEADER_IM_BG_POSITION')) {
                    case 1:
                        $position_option = 'center top';
                        break;
                    case 2:
                        $position_option = 'right top';
                        break;
                    default:
                        $position_option = 'left top';
                }
                $css .= '#header { background-position: ' . $position_option . '; }
                  ';
            }
        }

        if (Configuration::get('NC_HEADER_BGS') == "4") {
            $css .= '#header { background: none; }';
        }



        if (Configuration::get('RC_G_BG_CONTENT')) {
            $css .= '.product_add_mini, #main, #middlecolumns, .product-comment-list-item, #blockcart-modal .modal-body, body#checkout section.checkout-step, .img-thumbnail, #new_comment_form, #cart_summary .cart_separator td, .card, #product #main>.row>div.col-image .col-image-inside, .social-sharing .share_text, #content-wrapper.left-column #main, #content-wrapper.right-column #main, .layout-left-column #content-wrapper #main, .layout-right-column #content-wrapper #main, #product #main>.row>div.col-content .col-content-inside, .tabs, #product #main .featured-products { background: ' . Configuration::get('RC_G_BG_CONTENT') . ' }
      .radio-label:before { box-shadow: inset 0 0 0 8px ' . Configuration::get('RC_G_BG_CONTENT') . ', 0 0 0 3px ' . Configuration::get('RC_G_BG_CONTENT') . '; }
      .input-radio:hover+span:before { box-shadow: inset 0 0 0 7px ' . Configuration::get('RC_G_BG_CONTENT') . ', 0 0 0 3px ' . Configuration::get('RC_I_B_FOCUS') . '; }
      .input-radio:checked+span:before { box-shadow: inset 0 0 0 6px ' . Configuration::get('RC_G_BG_CONTENT') . ', 0 0 0 3px ' . Configuration::get('RC_I_B_FOCUS') . '; }
      .input-color:checked+span:before { box-shadow: inset 0 0 0 6px ' . Configuration::get('RC_G_BG_CONTENT') . ', 0 0 0 3px ' . Configuration::get('RC_I_B_FOCUS') . '; }
      .color:hover:before, .custom-checkbox input[type=checkbox]+span.color:hover:before { box-shadow: inset 0 0 0 7px ' . Configuration::get('RC_G_BG_CONTENT') . ', 0 0 0 0 ' . Configuration::get('RC_I_B_COLOR') . '; }
      .color:before, .custom-checkbox input[type=checkbox]+span.color:before { box-shadow: inset 0 0 0 8px ' . Configuration::get('RC_G_BG_CONTENT') . ', 0 0 0 3px ' . Configuration::get('RC_G_BG_CONTENT') . '; }
      ';
        }


        if (Configuration::get('RC_G_BG_CONTENT') == Configuration::get('NC_MAIN_BC')) {
            $css .= '
        .product_add_mini, body#cms #main, #middlecolumns, #blockcart-modal .modal-body, body#checkout section.checkout-step, .img-thumbnail, #new_comment_form, #cart_summary .cart_separator td, .card, .social-sharing, #product #main>.row>div.col-content .col-content-inside, .tabs, #product #main .featured-products, .cart-grid-right .cart-summary { border: 2px solid ' . Configuration::get('RC_G_BORDER') . ' }
        #product-comments-list .product-comment-list-item 
        { 
            border-bottom: 2px solid ' . Configuration::get('RC_G_BORDER') . ';
            border-left: 2px solid ' . Configuration::get('RC_G_BORDER') . ';
            border-right: 2px solid ' . Configuration::get('RC_G_BORDER') . ';
        }       
        #product-comments-list .product-comment-list-item:first-child
        { 
            border-bottom: none;
            border-top: 2px solid ' . Configuration::get('RC_G_BORDER') . ';
            border-left: 2px solid ' . Configuration::get('RC_G_BORDER') . ';
            border-right: 2px solid ' . Configuration::get('RC_G_BORDER') . ';
        }
        #product-comments-list .product-comment-list-item:first-child#empty-product-comment {
            border-bottom: 2px solid ' . Configuration::get('RC_G_BORDER') . ';
            margin-top: 40px;
        }



        .images-container .product-images { margin: 12px 0 14px; }
        #main .images-container .js-qv-mask { width:100% }

        #content-wrapper.left-column #main, #content-wrapper.right-column #main, .layout-left-column #content-wrapper #main, .layout-right-column #content-wrapper #main {
          padding-right:0;
        }
        @media(max-width:991px) {
          #content-wrapper.left-column #main, #content-wrapper.right-column #main, .layout-left-column #content-wrapper #main, .layout-right-column #content-wrapper #main {
            padding-left:0;
          }
          #header .header-nav .left-nav {
            border-bottom: 2px solid ' . Configuration::get('RC_G_BORDER') . '
          }
        }
        #footer {
          padding-top: 30px;
        }
        #product #main .featured-products {
          padding-top:2.5rem;
          margin-top: 30px!important;
        }
        body#contact #main {
          border:2px solid ' . Configuration::get('RC_G_BORDER') . ';
          border-radius:5px;
          padding-right:1.875rem!important;
        }
        @media(max-width:991px) {
        body#contact #main {
          margin-top:30px;
          padding: 1.5rem 1.875rem!important;
        } }
        ';
            if (Configuration::get('RC_PP_IMGB') == "1") {
                $css .= '.social-sharing { margin-top:-2px }';
            }
        }
        if (Configuration::get('NC_MOB_HEADER') == Configuration::get('NC_MAIN_BC')) {
            $css .= '
          .header-mobile { border-bottom: 2px solid ' . Configuration::get('RC_G_BORDER') . ' }
        ';
        }

        if (Configuration::get('NC_HEMOS') == "2") {
            $css .= '
          .header-mobile {
            position:absolute;
            top:-70px;
          }
          .roy_levibox.mobile>div.box-menu {
            position: fixed;
            bottom: 0;
            top:auto;
          }
          body.side_open .box-menu {
            opacity: 1;
            pointer-events: auto;
          }
          .header-mobile .logo-mobile {
            justify-content: center;
            width: 100%;
            padding: 0 30px;
          }
          @media (max-width: 991px) {
            .box-arrow {
              display:none!important;
            }
            .side_close.menu_close {
              top: auto;
              bottom: 0;
              right: 0;
              border-radius: 0;
              width: 25%;
              height: 70px;
            }
            .side_menu .side_menu_rel>div#side_menu_wrap {
              width: 100%;
            }
          }
        ';
        }

        if (Configuration::get('NC_HEMOS') == "3") {
            $css .= '
        .roy_levibox.mobile {
          bottom:auto;
          top:0;
        }
        .roy_levibox.mobile>div {
          bottom:auto!important;
          top:0;
        }

          .header-mobile {
            z-index: 2011;
            width:36%;
            border: none!important;
          }
          .header-mobile.scroll-down {
            box-shadow:none;
          }

          body.side_open .box-menu {
            opacity: 1;
            pointer-events: auto;
          }
          .header-mobile .logo-mobile {
            justify-content: center;
            padding: 0 0 0 20px;
            width: 100%;
          }
          .menu_acc {
            display:none;
          }
          .roy_levibox.mobile>.box-acc {
            display:none;
          }
          .roy_levibox.mobile>.box-search {
            left:40%!important;
            width: 20%;
          }
          .roy_levibox.mobile .box-one.box-arrow.box-cart, .roy_levibox.mobile>div.box-cart {
              left: 60%;
              width: 20%;
          }
          .roy_levibox.mobile .box-one.box-arrow.box-menu, .roy_levibox.mobile>div.box-menu {
              width: 20%;
          }

          @media (max-width: 991px) {
            .box-arrow {
              display:none!important;
            }
            .side_menu {
              top:70px;
            }
            .side_menu:before {
              top:0;
              height:100%;
            }
            .side_close {
              bottom:auto;
              top:0;
            }
            .side_close.menu_close {
                top: 10px;
                right: 10px;
                bottom: auto;
            }
            .side_close.search_close {
              left:40%;
              width: 20%;
            }
            .side_close.cart_close {
              left:60%;
              width: 20%;
            }
            .side_menu .side_menu_rel>div#side_menu_wrap {
              width: 100%;
            }
            .menu_acc {
              display:block;
              margin-bottom:40px;
              font-size: 1.25rem!important;
              line-height: 1.25rem;
              margin: 20px 0 0;
            }
          }


        ';
        }




        if (Configuration::get('RC_G_BORDER')) {
            $css .= 'hr, #empty-product-comment, #product-comments-list .product-comment-list-item::before, #product_comments_block_tab button.usefulness_btn, #product_comments_block_tab div.comment, #subcategories ul li .subcategory-image a, #blockcart-modal .divide-right, body#checkout section.checkout-step, .active_filters, .active_filters .filter-block, .product-features>dl.data-sheet dd.value, #module-smartblog-details #main .page-content ul.footer_links, .tags_block .block_content a, #tags_blog_block_left .block_content a, .sdstags-update .tags a, .page-my-account #content .links a span.link-item, .card, .product-features>dl.data-sheet dt.name, .social-sharing, .discover_qw { border-color: ' . Configuration::get('RC_G_BORDER') . ' }
       .product-info:before, .product-add-to-cart:before, #main .page-footer:before { background: ' . Configuration::get('RC_G_BORDER') . ' }
      ';
        }
        if (Configuration::get('RC_G_BODY_TEXT')) {
            $css .= 'body, p, .active_filters .filter-block .close { color: ' . Configuration::get('RC_G_BODY_TEXT') . ' }
      ';
        }
        if (Configuration::get('RC_G_BODY_COMMENT')) {
            $css .= '.text-muted, body#checkout section.checkout-step .delete-address, body#checkout section.checkout-step .edit-address, body#checkout section.checkout-step .address, .sdsarticleHeader .meta, .product-line-grid-right .cart-line-product-actions .remove-from-cart, .product-line-grid-right .product-price .remove-from-cart, .cart-grid-body .product-line-info.atts *, .sdsarticleHeader span, .sdsarticleHeader span a, .pagination .showing, .form-control-comment, #main .page-footer a i, .col-content-inside .comments_note .star_content .nb-comments, .fl { color: ' . Configuration::get('RC_G_BODY_COMMENT') . ' }
      ';
        }
        if (Configuration::get('RC_G_BODY_LINK')) {
            $css .= 'a, a:visited, .active_filters .filter-block, .cart-grid-right .promo-discounts .cart-summary-line .label .code { color: ' . Configuration::get('RC_G_BODY_LINK') . ' }
      body#checkout section.checkout-step .step-edit .edit svg * { stroke: ' . Configuration::get('RC_G_BODY_LINK') . '!important }
      ';
        }
        if (Configuration::get('RC_G_BODY_LINK_HOVER')) {
            $css .= 'a:hover, a:focus, #main h1:not(.active-filter-title) a i:before, #product .featured-products h2 a i:before, .products-section-title a i:before, h1.page-header a i:before, h2.page-header a i:before, h3.page-header a i:before, h4.page-header a i:before, h5.page-header a i:before, h6.page-header a i:before { color: ' . Configuration::get('RC_G_BODY_LINK_HOVER') . ' }
      body#checkout section.checkout-step:hover .step-edit .edit svg * { stroke: ' . Configuration::get('RC_G_BODY_LINK_HOVER') . '!important }
      ';
        }
        if (Configuration::get('RC_LABEL')) {
            $css .= 'label, #blockcart-modal .modal-body p strong { color: ' . Configuration::get('RC_LABEL') . ' }
      ';
        }
        if (Configuration::get('RC_G_HEADER')) {
            $css .= '.product_add_mini, .h1, .h2, .h3, .h4, #product .featured-products h2 a, .products-section-title a, #product_comments_block_tab .comment_author_infos strong, h4.title_block, #main h1:not(.active-filter-title), #new_comment_form .product .product_desc .product_name, #new_comment_form .title, .tabs .nav-tabs .nav-link, .elementor-widget-roy_product_tabs .nav-tabs .nav-link, #product .featured-products h2, .products-section-title, h1.page-header, h2.page-header, h3.page-header, h4.page-header, h5.page-header, h6.page-header { color: ' . Configuration::get('RC_G_HEADER') . ' }
      .tabs .nav-tabs .nav-link:hover, .elementor-widget-roy_product_tabs .nav-tabs .nav-link:hover, .tabs .nav-tabs .nav-link.active, .tabs .tab-pane .product-features .h6, .tabs .tab-pane label { border-color:' . Configuration::get('RC_G_HEADER') . ' }
      ';
        }
        if (Configuration::get('RC_HEADER_UNDER')) {
            $css .= '#main h1:before, #product .featured-products h2:before, .details__title:before, .products-section-title:before, h1.page-header:before, h2.page-header:before, h3.page-header:before, h4.page-header:before, h5.page-header:before, h6.page-header:before { background: ' . Configuration::get('RC_HEADER_UNDER') . ' }
      .tabs .nav-tabs, .elementor-widget-roy_product_tabs .nav-tabs, .separator { border-color:' . Configuration::get('RC_HEADER_UNDER') . ' }
      ';
        }
        if (Configuration::get('RC_HEADER_DECOR')) {
            $css .= '#main h1:after, #product .featured-products h2:after, .details__title:after, .products-section-title:after, h1.page-header:after, h2.page-header:after, h3.page-header:after, h4.page-header:after, h5.page-header:after, h6.page-header:after { background: ' . Configuration::get('RC_HEADER_DECOR') . ' }
      .tabs .nav-tabs .nav-link.active, .elementor-widget-roy_product_tabs .nav-tabs .nav-link.active { border-color:' . Configuration::get('RC_HEADER_DECOR') . ' }
      ';
        }
        if (Configuration::get('RC_G_CC')) {
            $css .= '.owl-carousel .owl-nav>* { color: ' . Configuration::get('RC_G_CC') . ' }';
        }
        if (Configuration::get('RC_G_CH')) {
            $css .= '.owl-carousel .owl-nav>*:hover { color: ' . Configuration::get('RC_G_CH') . ' }';
        }
        if (Configuration::get('RC_G_HB')) {
            $css .= '#ui_tip { background: ' . Configuration::get('RC_G_HB') . ' }
      ';
        }
        if (Configuration::get('RC_G_HC')) {
            $css .= '#ui_tip { color: ' . Configuration::get('RC_G_HC') . ' }';
        }

        if (Configuration::get('RC_G_BG_EVEN')) {
            $css .= '.product-features>dl.data-sheet dd.value:nth-of-type(2n), .product-features>dl.data-sheet dt.name:nth-of-type(2n) { background-color: ' . Configuration::get('RC_G_BG_EVEN') . ' }
      ';
        }
        if (Configuration::get('RC_G_COLOR_EVEN')) {
            $css .= '.product-features>dl.data-sheet dd.value:nth-of-type(2n), .product-features>dl.data-sheet dt.name:nth-of-type(2n) { color: ' . Configuration::get('RC_G_COLOR_EVEN') . ' }
      ';
        }

        if (Configuration::get('RC_G_ACC_ICON')) {
            $css .= '.page-my-account #content .links a i { color: ' . Configuration::get('RC_G_ACC_ICON') . ' }
      ';
        }
        if (Configuration::get('RC_G_ACC_TITLE')) {
            $css .= '.page-my-account #content .links a span.link-item { color: ' . Configuration::get('RC_G_ACC_TITLE') . ' }
      ';
        }

        if (Configuration::get('RC_FANCY_NBG')) {
            $css .= '#product-modal .modal-content .modal-body .image-caption { background: ' . Configuration::get('RC_FANCY_NBG') . ' }';
        }
        if (Configuration::get('RC_FANCY_NC')) {
            $css .= '#product-modal-name { color: ' . Configuration::get('RC_FANCY_NC') . ' }';
        }


        // ****************  Header options styles start

        if (Configuration::get('RC_HEADER_LAY') == "1") {

            $css .= '
          #header .row.action #_desktop_logo { text-align: left }
        ';

            if (Configuration::get('RC_SEARCH_LAY') == "2" || Configuration::get('RC_SEARCH_LAY') == "4") {
                $css .= '
          .header-top .search-widget form button[type=submit] { float:right; } 
          ';
            }
        }
        if (Configuration::get('RC_HEADER_LAY') == "2") {
            $css .= '
    .header_lay2 .search-widget {
      float:left;
      padding:0;
      width: auto;
    }
    ';
        }

        if (Configuration::get('NC_HEADER_SHADOWS') == "0") {
            $css .= '
  #header .row.action .blockcart a,
  #header .row.action .blockcart a:hover,
  .header-top .search-widget form input[type=text],
  .header-top .search-widget form input[type=text]:hover,
  .header-top .search-widget form input[type=text]:focus,
  .ets_mm_megamenu {
    box-shadow:none!important;
  }
    ';
        }

        $css .= '
      #header .ets_mm_megamenu.sticky_enabled.scroll_heading {
          background:  ' . Configuration::get('NC_HEADER_ST_BGCOLOR') . '!important;
      }
      #header .ets_mm_megamenu.sticky_enabled.scroll_heading li.menu_home a:after {
          background-color: ' . Configuration::get('NC_HEADER_ST_LINKCOLOR') . '!important;
      }
      #header .ets_mm_megamenu.sticky_enabled.scroll_heading li.menu_home:hover a:after {
          background-color: ' . Configuration::get('NC_HEADER_ST_LINKCOLORHOVER') . '!important;
      }

      #header .ets_mm_megamenu.sticky_enabled.scroll_heading .mm_menus_li > a {
          color:  ' . Configuration::get('NC_HEADER_ST_LINKCOLOR') . '!important;
      }
      #header .ets_mm_megamenu.sticky_enabled.scroll_heading .mm_menus_li:hover > a {
          color:  ' . Configuration::get('NC_HEADER_ST_LINKCOLORHOVER') . '!important;
      }
      ';

        if (Configuration::get('RC_HEADER_NBG')) {
            $css .= '#header .header-nav {
        background: ' . Configuration::get('RC_HEADER_NBG') . ';
        border-color: ' . Configuration::get('RC_HEADER_NB') . ';
        color: ' . Configuration::get('RC_HEADER_NT') . ';
      }
      ';
        }

        if (Configuration::get('RC_HEADER_NL')) {
            $css .= '
      #header .header-nav a, #header .header-nav span, #header .header-nav .contact-link span { color: ' . Configuration::get('RC_HEADER_NL') . ' ; }
      @media (max-width: 991px) {
        #header .header-nav .left-nav .mob-select select {
          color: ' . Configuration::get('RC_HEADER_NL') . ' ;
      } }
      ';
        }
        if (Configuration::get('RC_HEADER_NLH')) {
            $css .= '#header .header-nav a:hover, #header .header-nav span:hover { color: ' . Configuration::get('RC_HEADER_NLH') . ' ; }
      ';
        }
        if (Configuration::get('RC_HEADER_NS')) {
            $css .= '
        #header .header-nav .left-nav ul.dropdown-menu {
          background: ' . Configuration::get('RC_HEADER_NS') . ' ;
          border-color: ' . Configuration::get('RC_HEADER_NB') . ';
        }
      ';
        }






        // SEARCH styles

        if (Configuration::get('RC_SEARCH_LAY') == "1") {
            $css .= '

            ';
        }
        if (Configuration::get('RC_SEARCH_LAY') == "2") {
            $css .= '
              .header-top .search-widget form input[type=text] { display:none }
              .header-top .search-widget form button[type=submit] {
                background: ' . Configuration::get('RC_SEARCH_BG') . ';
                border: 2px solid ' . Configuration::get('RC_SEARCH_LINE') . ';
                border-radius:5px;
                height: 4rem;
                width: 4rem;
                position:relative;
                bottom:auto;
                right:auto;
                display:flex;
                align-items:center;
                justify-content:center;
              }
              .header-top .search-widget form button[type=submit] .search { height:28px; }
            ';
        }
        if (Configuration::get('RC_SEARCH_LAY') == "3") {
            $css .= '
              .header-top .search-widget form input[type=text] {
                background:none!important;
                border-width:0 0 2px 0!important;
                box-shadow:none!important;
                border-radius:0!important;
                padding-left:0;
               }
              .header-top .search-widget form input[type=text]:focus {
                padding-left:0;
               }
              .header-top .search-widget form button[type=submit] {
                padding-right: 0;
                right:0;
               }
              .header-top .search-widget form button[type=submit] .search { height:28px; }
            ';
        }
        if (Configuration::get('RC_SEARCH_LAY') == "4") {
            $css .= '
              .header-top .search-widget form input[type=text] { display:none }
              .header-top .search-widget form button[type=submit] {
                height: 4rem;
                width: 4rem;
                position:relative;
                bottom:auto;
                right:auto;
                display:flex;
                align-items:center;
                justify-content:center;
              }
              .header-top .search-widget form button[type=submit] .search { height:28px; }
            ';
        }

        $css .= '
              .side_menu .search-widget form button[type=submit] i,
              .side_menu .search-widget form button[type=submit] svg {
                width:24px!important;
                height:24px!important;
              }
            ';

        if (Configuration::get('RC_SEARCH_BG')) {
            $css .= '
            .header-top .search-widget form input[type=text] {
              background: ' . Configuration::get('RC_SEARCH_BG') . ';
              border-color: ' . Configuration::get('RC_SEARCH_LINE') . ';
              color:' . Configuration::get('RC_SEARCH_T') . ';
            }
            .header-top .search-widget form button[type=submit] svg * {
              stroke:' . Configuration::get('RC_SEARCH_ICON') . '!important;
            }
            ';
        }
        if (Configuration::get('RC_SEARCH_BG_HOVER')) {
            $css .= '
            .header-top .search-widget form input[type=text]:focus {
              background: ' . Configuration::get('RC_SEARCH_BG_HOVER') . ';
              border-color: ' . Configuration::get('RC_SEARCH_LINEH') . '!important;
              color:' . Configuration::get('RC_SEARCH_T_HOVER') . ';
            }
            .header-top .search-widget form input[type=text]:focus+button svg * {
              stroke:' . Configuration::get('RC_SEARCH_ICONH') . '!important;
            }
            ';
        }

        if (Configuration::get('RC_SEARCH_INPUT')) {
            $css .= '
            .header-top .search-widget form ::-webkit-input-placeholder {
                color: ' . Configuration::get('RC_SEARCH_INPUT') . ' !important; }
            .header-top .search-widget form :-moz-placeholder {
                color: ' . Configuration::get('RC_SEARCH_INPUT') . ' !important; }
            .header-top .search-widget form ::-moz-placeholder {
                color: ' . Configuration::get('RC_SEARCH_INPUT') . ' !important; }
            .header-top .search-widget form :-ms-input-placeholder {
                color: ' . Configuration::get('RC_SEARCH_INPUT') . ' !important; }
            ';
        }
        if (Configuration::get('RC_SEARCH_INPUTH')) {
            $css .= '
            .header-top .search-widget form input:focus::-webkit-input-placeholder {
                color: ' . Configuration::get('RC_SEARCH_INPUTH') . ' !important; }
            .header-top .search-widget form input:focus:-moz-placeholder {
                color: ' . Configuration::get('RC_SEARCH_INPUTH') . ' !important; }
            .header-top .search-widget form input:focus::-moz-placeholder {
                color: ' . Configuration::get('RC_SEARCH_INPUTH') . ' !important; }
            .header-top .search-widget form input:focus:-ms-input-placeholder {
                color: ' . Configuration::get('RC_SEARCH_INPUTH') . ' !important; }
            ';
        }


        // Cart styles start

        if (Configuration::get('RC_CART_LAY') == "1") {
            $css .= '
            #header .row.action .blockcart a span.text {
              display:inline-block;
            }
            ';
        }
        if (Configuration::get('RC_CART_LAY') == "2") {
            $css .= '
            #header .row.action .blockcart a {
              padding-right:0.8rem;
            }
            #header .row.action .blockcart a span.cart-products-count:not(.hidden) {
              padding-right:1rem;
            }
            ';
        }
        if (Configuration::get('RC_CART_LAY') == "3") {
            $css .= '
            #header .row.action .blockcart a {
              background: none!important;
              border: none!important;
              box-shadow: none!important;
              padding-right:0;
            }
            #header .row.action .blockcart a span.text {
              display:inline-block;
            }
            #header .row.action .blockcart a i { top:17px; }
            ';
        }
        if (Configuration::get('RC_CART_LAY') == "4") {
            $css .= '
            #header .row.action .blockcart a {
              background: none!important;
              border: none!important;
              box-shadow: none!important;
              padding-right:0;
              padding-left:40px!important;
            }
            #header .row.action .blockcart a i { top:17px; left: 12px; }
            #header .row.action .blockcart a span.cart-products-count {
              background: ' . Configuration::get('RC_CART_BG') . ';
              border-radius:50%;
              width:35px;height:35px;
             text-align: center;
             line-height: 37px;
             font-size: 18px;
             font-weight: bold;
            }
            #header .row.action .blockcart a:hover span.cart-products-count {
              background: ' . Configuration::get('RC_CART_BG_HOVER') . ';
            }
            ';
        }

        if ((Configuration::get('RC_CART_LAY') == "3" || Configuration::get('RC_CART_LAY') == "4") && Configuration::get('RC_SEARCH_LAY') == "4") {
            $css .= '
            #header .row.action .blockcart a { padding-left:44px; }
            #header .row.action .blockcart a i { left:0; }
            ';
        }


        // new cart icons
        if (Configuration::get('RC_CART_ICON')) {
            $css .= '
                #header .row.action .blockcart a i {
                  -webkit-mask-image: url(../images/rt_' . Configuration::get('RC_CART_ICON') . '.svg);
                  mask-image: url(../images/rt_' . Configuration::get('RC_CART_ICON') . '.svg);
                } ';
        }
        if (Configuration::get('RC_CART_ICON') == "cart12" || Configuration::get('RC_CART_ICON') == "cart13" || Configuration::get('RC_CART_ICON') == "cart12") {
            $css .= '
                #header .row.action .blockcart a i {
                  margin-top:-2px;
                } ';
        }
        if (Configuration::get('RC_CART_ICON') == "cart6" || Configuration::get('RC_CART_ICON') == "cart7" || Configuration::get('RC_CART_ICON') == "cart8" || Configuration::get('RC_CART_ICON') == "cart9") {
            $css .= '
                #header .row.action .blockcart a i {
                  margin-top:-1px;
                } ';
        }



        if (Configuration::get('RC_CART_BG')) {
            $css .= '
            #header .row.action .blockcart a {
              background: ' . Configuration::get('RC_CART_BG') . ';
              border: 2px solid ' . Configuration::get('RC_CART_B') . ';
              color: ' . Configuration::get('RC_CART_T') . ';
            }
            #header .row.action .blockcart a span.text {
              color: ' . Configuration::get('RC_CART_T') . ';
              text-transform: none;
              text-shadow: none;
            }
            #header .row.action .blockcart a span.cart-products-count {
              color: ' . Configuration::get('RC_CART_Q') . ';
            }
            #header .row.action .blockcart a i {
              background-color: ' . Configuration::get('RC_CART_I') . ';
            }
            ';
        }

        if (Configuration::get('RC_CART_BG_HOVER')) {
            $css .= '
            #header .row.action .blockcart a:hover {
              background: ' . Configuration::get('RC_CART_BG_HOVER') . ';
              border-color: ' . Configuration::get('RC_CART_B_HOVER') . ';
              color: ' . Configuration::get('RC_CART_T_HOVER') . ';
            }
            #header .row.action .blockcart a:hover span.cart-products-count {
              color: ' . Configuration::get('RC_CART_Q_HOVER') . ';
            }
            #header .row.action .blockcart a:hover i {
              background-color: ' . Configuration::get('RC_CART_I_HOVER') . ';
            }
            ';
        }


        //  MENU styles start

        if (Configuration::get('NC_M_ALIGN_S') == "2") {
            $css .= '
                  @media (min-width: 992px) {
                  .ets_mm_megamenu ul { justify-content: center; } }
                  ';
        }
        if (Configuration::get('NC_M_ALIGN_S') == "3") {
            $css .= '
                  @media (min-width: 992px) {
                  .ets_mm_megamenu ul { justify-content: flex-end; } }
                  ';
        }


        if (Configuration::get('NC_M_LAYOUT_S') == "1") {
            $css .= '
                  @media (min-width: 992px) {
                    #header .row.action {

                    }
                    #header .header-top {
                      margin-bottom:38px;
                    }
                    .row.topmenu {
                      margin-bottom:-34px;
                      position: relative;
                      z-index:100;
                    }
                  }
                  ';
        }
        if (Configuration::get('NC_M_LAYOUT_S') == "2") {
            $css .= '
                  @media (min-width: 992px) {
                    .ets_mm_megamenu:not(.scroll_heading), .layout_layout1 .ets_mm_megamenu_content, .ets_mm_megamenu.layout_layout1:not(.scroll_heading), .ets_mm_megamenu.layout_layout1:not(.ybc_vertical_menu) .mm_menus_ul {
                      background:none!important;
                      border:none!important;
                      box-shadow:none!important;
                    }
                    .ets_mm_megamenu li.menu_home a {
                      padding-left:0;
                       width: 42px!important;
                    }
                    .ets_mm_megamenu li.menu_home a:after {
                      left:0!important;
                    }
                    .ets_mm_megamenu.layout_layout1 .mm_menus_li:hover,
                    .ets_mm_megamenu.layout_layout1 .mm_menus_li.active,
                    .layout_layout1 .mm_menus_li:hover > a, #header .layout_layout1 .mm_menus_li:hover > a
                    {
                      background:none!important;
                    }
                    .row.topmenu {
                      margin-bottom:20px;
                    }
                    #header .row.action {
                      margin:40px 0 10px;
                    }
                  }
                  ';
        }


        $css .= '
                    #header .ets_mm_megamenu.sticky_enabled.scroll_heading .mm_menus_li > a
                    {
                      background:' . Configuration::get('NC_HEADER_ST_BGCOLOR') . '!important;
                    }
                    #header .ets_mm_megamenu.sticky_enabled.scroll_heading .mm_menus_li:hover > a
                    {
                      background:' . Configuration::get('NC_HEADER_ST_BGCOLORHOVER') . '!important;
                    }
                  ';

        if (Configuration::get('NC_M_UNDER_S') == "0") {
            $css .= '
          .ets_mm_megamenu.layout_layout1 .mm_menus_ul .mm_menus_li > a:before, .ets_mm_megamenu .mm_columns_ul:before, .layout_layout1 .mm_menus_li.mm_has_sub:hover > a:after { display:none }
        ';
        }
        if (Configuration::get('NC_M_UNDER_S') == "1") {
            $css .= '
          .layout_layout1 .mm_menus_ul .mm_menus_li > a:before { background: ' . Configuration::get('NC_M_UNDER_COLOR') . '!important }
        ';
        }
        if (Configuration::get('NC_M_BR_S')) {
            $css .= '
          .ets_mm_megamenu, .ets_mm_megamenu .mm_columns_ul, .ets_mm_block_content ul li ul { border-radius: ' . Configuration::get('NC_M_BR_S') . 'px!important }
        ';
        }
        if (Configuration::get('NC_M_BR_S') == "0") {
            $css .= '
          .ets_mm_megamenu, .ets_mm_megamenu .mm_columns_ul, .ets_mm_block_content ul li ul { border-radius: 0px!important }
        ';
        }


        if (Configuration::get('NC_M_OVERRIDE_S') == "2") {

            if (Configuration::get('NC_M_LAYOUT_S') == "1") {
                if (Configuration::get('RC_M_BG')) {
                    $css .= '
                  @media(min-width:992px) {
                    .ets_mm_megamenu.layout_layout1 { background: ' . Configuration::get('RC_M_BG') . '!important }
                    .layout_layout1 .ets_mm_megamenu_content { background: none!important }
                  }
                  ';
                }
                if (Configuration::get('RC_M_LINK_BG_HOVER')) {
                    $css .= '
                  @media(min-width: 992px) {
                  .layout_layout1 .mm_menus_li:hover > a, #header .layout_layout1 .mm_menus_li:hover > a { background: ' . Configuration::get('RC_M_LINK_BG_HOVER') . '!important } }
                  ';
                }
            }
            if (Configuration::get('RC_M_LINK')) {
                $css .= '
              #header .layout_layout1 .mm_menus_li:not(.mm_menus_li_tab) > a, .mm_columns_ul_tab.mm_tab_no_content .mm_tabs_li a { color: ' . Configuration::get('RC_M_LINK') . '!important }
              .ets_mm_megamenu li.menu_home a:after {
                background-color: ' . Configuration::get('RC_M_LINK') . ';
                -webkit-mask-image: url(../images/rt_home.svg);
                mask-image: url(../images/rt_home.svg);
              }
              ';
            }
            if (Configuration::get('RC_M_LINK_HOVER')) {
                $css .= '
              #header .layout_layout1 .mm_menus_li > a:hover { color: ' . Configuration::get('RC_M_LINK_HOVER') . '!important }
              #header .layout_layout1 .mm_menus_li:hover > a { color: ' . Configuration::get('RC_M_LINK_HOVER') . '!important }
              .ets_mm_megamenu li.menu_home a:hover:after {
                background-color: ' . Configuration::get('RC_M_LINK_HOVER') . ';
                -webkit-mask-image: url(../images/rt_home.svg);
                mask-image: url(../images/rt_home.svg);
              }
              ';
            }


            // 2021 Menu line border if menu background same as header background

            if (Configuration::get('NC_M_LAYOUT_S') == "1") {

                if (Configuration::get('NC_HEADER_BGS') == "4" && (Configuration::get('NC_MAIN_BC') === Configuration::get('RC_M_BG'))) {
                    $css .= '
                    .ets_mm_megamenu.layout_layout1:not(.ybc_vertical_menu) .mm_menus_ul {
                        border: 2px solid ' . Configuration::get('RC_M_POPUP_LBORDER') . ';
                    }
                ';
                }
            }


            // Sub
            $css .= '
          @media(min-width:992px) {
          .layout_layout1.ets_mm_megamenu .mm_columns_ul, .ets_mm_block_content ul li ul {
            background: ' . Configuration::get('RC_M_POPUP_LBG') . '!important;
            border-color: ' . Configuration::get('RC_M_POPUP_LBORDER') . '!important; }
            .ets_mm_block_content { color: ' . Configuration::get('RC_M_POPUP_LCHEVRON') . '!important }
            .ets_mm_block > h4, .ets_mm_block > .h4 { border-color:' . Configuration::get('RC_M_POPUP_LBORDER') . '!important; }
            #header .layout_layout1 .ets_mm_block_content a, .ets_mm_block > h4, .ets_mm_block > .h4 { color: ' . Configuration::get('RC_M_POPUP_LLINK') . '!important }
            #header .layout_layout1 .mm_tab_li_content a:hover, #header .layout_layout1 .mm_block_type_html .ets_mm_block_content a:hover, #header .layout_layout1 .mm_columns_ul .mm_block_type_product .product-title > a:hover { color: ' . Configuration::get('RC_M_POPUP_LLINK_HOVER') . 'important }
          }
          ';
        }



        // Mobile

        if (Configuration::get('NC_MOB_HEADER')) {
            $css .= '
            .header-mobile { background: ' . Configuration::get('NC_MOB_HEADER') . ' }
            ';
        }
        if (Configuration::get('NC_MOB_MENU')) {
            $css .= '
            .roy_levibox .box-one.box-menu i svg * { stroke: ' . Configuration::get('NC_MOB_MENU') . '!important }

            .side-menu .ets_mm_megamenu ul {
              color:' . Configuration::get('NC_SIDE_TEXT') . '!important;
            }
            .side-menu .ets_mm_megamenu a, .side-menu .ets_mm_megamenu h4 {
              color:' . Configuration::get('NC_SIDE_TITLE') . '!important;
            }

            ';
        }


        if (Configuration::get('NC_MOB_CAT') == "2") {
            $css .= '
                  @media (max-width:479px) {
                    #products #js-product-list .product-item {
                        width:50%;
                        flex-grow:1;
                    }
                    #products #js-product-list .product-item .comments_note,
                    #products #js-product-list .product-item .countcontainer {
                        display:none;
                    }
                    #products #js-product-list .thumbnail-container .add_to_cart {
                        padding: 10px 6px;
                        font-size: 16px;
                        line-height:16px;
                    }
                  }
            ';
        }
        if (Configuration::get('NC_MOB_HP') == "2") {
            $css .= '
                  @media (max-width:479px) {
                    .featured-products:not(.slider):not(.slider-on) .product-item {
                        width:50%;
                        flex-grow:1;
                    }
                    .featured-products .product-item .comments_note,
                    .featured-products .product-item .countcontainer {
                        display:none;
                    }
                    .featured-products  .thumbnail-container .add_to_cart {
                        padding: 10px 6px;
                        font-size: 16px;
                        line-height:16px;
                    }
                  }
            ';
        }


        // LEVIBOX styles

        if (Configuration::get('RC_LEVI_POSITION') == "1") {
            $css .= '
            ';
        }
        if (Configuration::get('RC_LEVI_POSITION') == "2") {
            $css .= '
            ';
        }

        if (Configuration::get('NC_LEVI_BG')) {
            $css .= '
            .roy_levibox {
              background:' . Configuration::get('NC_LEVI_BG') . ';
              border:2px solid ' . Configuration::get('NC_LEVI_BORDER') . ';
            }
            ';
        }
        if (Configuration::get('NC_LEVI_CART')) {
            $css .= '
            .roy_levibox .box-one.box-cart i svg * {
              stroke: ' . Configuration::get('NC_LEVI_CART') . '!important;
            }
            .roy_levibox .box-one.box-cart .prod_count {
              background:' . Configuration::get('NC_LEVI_CART_A') . ';
            }
            ';
        }
        if (Configuration::get('NC_LEVI_I')) {
            $css .= '
            .roy_levibox .box-one:not(.box-cart):not(.box-menu) i svg * {
              stroke: ' . Configuration::get('NC_LEVI_I') . '!important;
            }
            .roy_levibox .box-one i:hover svg * {
              stroke: ' . Configuration::get('NC_LEVI_I_HOVER') . '!important;
            }
            ';
        }
        if (Configuration::get('NC_LEVI_CLOSE')) {
            $css .= '
            .side_close { background:' . Configuration::get('NC_LEVI_CLOSE') . '; }
            .side_close i svg * {
              stroke: ' . Configuration::get('NC_LEVI_CLOSE_I') . '!important;
            }
            ';
        }

        if (Configuration::get('NC_SIDE_BG')) {
            $css .= '
            .side_menu {
              background:' . Configuration::get('NC_SIDE_BG') . ';
              color:' . Configuration::get('NC_SIDE_TEXT') . ';
            }
            .side_menu .cart-prods li .product-quantity, .side_menu .cart-prods li .remove-from-cart {
              background:' . Configuration::get('NC_SIDE_BG') . ';
            }
            .side_menu p, .side_menu .cart-prods li .product-price, .side_menu #side_acc_wrap .acc_ul li.name a, .side_menu #side_acc_wrap .acc_ul li.logout a {
              color:' . Configuration::get('NC_SIDE_TEXT') . ';
            }
            .side_menu .cart-prods li .remove-from-cart i svg * {
              stroke:' . Configuration::get('NC_SIDE_TEXT') . '!important;
            }
            .side_menu #side_menu_wrap .menu_selectors .mob-select select, .side_menu a:not(.btn), .side_menu .search_tags_roy ul li a, .side_menu .cart-prods li:hover .product-price, .side_menu .cart-prods li .product-name, .side_menu .side_title, .side_menu form#contactable-contactForm p.contactable-header {
              color:' . Configuration::get('NC_SIDE_TITLE') . ';
            }
            .side_menu .ets_mm_megamenu li.menu_home a:after {
                background-color: ' . Configuration::get('NC_SIDE_TITLE') . '!important;
            }
            .side_menu .cart-total *, .side_menu .cart-prods li .product-atts {
              color:' . Configuration::get('NC_SIDE_LIGHT') . ';
            }
            .side_menu .block-social ul li:not(:hover) {
              background-color:' . Configuration::get('NC_SIDE_LIGHT') . ';
            }
            .side-menu .ets_mm_block > h4, .side-menu .ets_mm_block > .h4, .side_menu .arrow:before {
              border-color:' . Configuration::get('NC_SIDE_TITLE') . ';
            }
            .side_menu #side_menu_wrap .menu_selectors, .side_menu #side_acc_wrap .acc_ul li.name, .side_menu #side_acc_wrap .acc_ul li.logout {
              border-color:' . Configuration::get('NC_SIDE_SEP') . ';
            }
            .side_menu .card-block:after {
              background-color:' . Configuration::get('NC_SIDE_SEP') . ';
            }

            .side_menu .layout_layout1 .mm_menus_li, .side_menu .layout_layout1 .mm_menus_li:not(:mm_menus_li_tab):hover > a, .side_menu .layout_layout1.ets_mm_megamenu .mm_columns_ul, .side_menu .ets_mm_block_content ul li ul {
              background:none!important;
              border:none!important;
            }
            ';
        }


        // ****************  Typography styles start


        if (Configuration::get('RC_FONT_SIZE_BODY')) {
            $font14 = Configuration::get('RC_FONT_SIZE_BODY') + 1;
            $font15 = Configuration::get('RC_FONT_SIZE_BODY') + 2;
            $font_pp = Configuration::get('RC_FONT_SIZE_BODY') - 1;
            $css .= 'p { font-size: ' . Configuration::get('RC_FONT_SIZE_BODY') . 'px; }
            .product-information, .product-information p, .tabs .tab-pane p { font-size: ' . $font_pp . 'px; }
            ';
        }

        if (Configuration::get('RC_FONT_SIZE_HEAD')) {
            $lineheight = Configuration::get('RC_FONT_SIZE_HEAD') + 2;
            $headTablet = Configuration::get('RC_FONT_SIZE_HEAD') - 2;
            $css .= '
            .elementor-widget-roy_product_tabs .nav-tabs .nav-link, .roy_blog .products-section-title a, .sds_post_title_home a, .tabs .nav-tabs .nav-link, #main h1:not(.active-filter-title), #product .featured-products h2, .products-section-title, h1.page-header, h2.page-header, h3.page-header, h4.page-header, h5.page-header, h6.page-header {
              font-size: ' . Configuration::get('RC_FONT_SIZE_HEAD') . 'px;
            }
            @media(max-width: 991px) {
            .elementor-widget-roy_product_tabs .nav-tabs .nav-link, .sds_post_title_home a {
              font-size: ' . $headTablet . 'px;
            } }
            .side-column>* .title.hidden-lg-up .h3, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title a, .side-column>.sidebar-block .sidebar-title span {
                font-size: ' . Configuration::get('RC_FONT_SIZE_HEAD') . 'px;
            }
            ';
        }

        if (Configuration::get('RC_FONT_SIZE_PP')) {
            $pplh =    Configuration::get('RC_FONT_SIZE_PP') + 4;
            $css .= '
            .product-price { font-size: ' . Configuration::get('RC_FONT_SIZE_PP') . 'px; line-height: ' . $pplh . 'px; }
            ';
        }

        if (Configuration::get('RC_FONT_SIZE_BUTTONS')) {
            $css .= '
            .btn, .contactable-submit {font-size: ' . Configuration::get('RC_FONT_SIZE_BUTTONS') . 'px; }
            ';
        }

        if (Configuration::get('RC_FONT_SIZE_PRICE')) {
            $fontpricelh =    Configuration::get('RC_FONT_SIZE_PRICE') + 2;
            $fontpriceold =    Configuration::get('RC_FONT_SIZE_PRICE') - 2;
            $css .= '
            #products .product-price-and-shipping .price, .featured-products .product-price-and-shipping .price, .product-accessories .product-price-and-shipping .price, .product-miniature .product-price-and-shipping .price {
              font-size: ' . Configuration::get('RC_FONT_SIZE_PRICE') . 'px; line-height: ' . $fontpricelh . 'px; }
              #products .regular-pric, .elementor-widget-roy_product_tabs .regular-price, .featured-products .regular-price, .product-accessories .regular-price, .product-miniature .regular-price {
                font-size: ' . $fontpriceold . 'px; line-height: ' . $fontpricelh . 'px; }
              }
              
            ';
        }

        if (Configuration::get('RC_FONT_SIZE_PROD')) {
            $fontprodlh =  Configuration::get('RC_FONT_SIZE_PROD') + 2;
            $css .= '
            #main h1.product-title, .modal h1.product-title {font-size: ' . Configuration::get('RC_FONT_SIZE_PROD') . 'px; line-height: ' . Configuration::get('RC_FONT_SIZE_PROD') . 'px; }
            ';
        }

        if (Configuration::get('RC_FONT_SIZE_PN')) {
            $fontsizepnlh = Configuration::get('RC_FONT_SIZE_PN');
            if (Configuration::get('RC_FONT_SIZE_PN') < 16) $fontsizepnlh = 18;
            $css .= '
            #products .product-title a, .featured-products .product-title a, .product-accessories .product-title a, .product-miniature .product-title a {
              font-size: ' . Configuration::get('RC_FONT_SIZE_PN') . 'px; line-height: ' . $fontsizepnlh . 'px;}
            ';
        }

        $sfontHeadings = Configuration::get('RC_F_HEADINGS');
        $fontHeadings = substr($sfontHeadings, 0, strpos($sfontHeadings, ":"));

        $sfontButtons = Configuration::get('RC_F_BUTTONS');
        $fontButtons = substr($sfontButtons, 0, strpos($sfontButtons, ":"));

        $sfontText = Configuration::get('RC_F_TEXT');
        $fontText = substr($sfontText, 0, strpos($sfontText, ":"));

        $sfontPrice = Configuration::get('RC_F_PRICE');
        $fontPrice = substr($sfontPrice, 0, strpos($sfontPrice, ":"));

        $sfontPn = Configuration::get('RC_F_PN');
        $fontPn = substr($sfontPn, 0, strpos($sfontPn, ":"));

        if ($fontText == '')
            $fontText = $sfontText;
        if ($fontPrice == '')
            $fontPrice = $sfontPrice;
        if ($fontPn == '')
            $fontPn = $sfontPn;
        if ($fontButtons  == '')
            $fontButtons  = $sfontButtons;
        if ($fontHeadings  == '')
            $fontHeadings  = $sfontHeadings;
        if (Configuration::get('RC_F_HEADINGS') or Configuration::get('RC_F_BUTTONS') or Configuration::get('RC_F_TEXT') or Configuration::get('RC_F_PRICE') or Configuration::get('RC_F_PN')) {
            $css .= "
            #search_filters h4, .product-comment-modal .modal-dialog h3, .reviews-list-title h3, .cart-grid-body .card-block h1, #main h1:not(.active-filter-title), #header .row.action .blockcart a span.text, .page-my-account #content .links a span.link-item, #product .featured-products h2, .products-section-title, h1.page-header, h2.page-header, h3.page-header, h4.page-header, h5.page-header, h6.page-header, .block-contact-title, .blockcms-title, .footer-container h3, .myaccount-title, .myaccount-title a, .side-column>* .title.hidden-md-up .h3, .side-column>.links h3, #blockcart-modal .product-name, .side-column>.sidebar-block .sidebar-title a, .side-column>.sidebar-block .sidebar-title span, .layout_layout1 .mm_menus_li > a, .has-discount .discount, .nav-tabs .nav-link, .side_menu .side_title, .side_menu form#contactable-contactForm p.contactable-header, .sds_post_title_home a, .footer-container .links .h3, #main h2
            {
            font-family: '" . $fontHeadings . "', Oswald, Verdana, sans-serif; }
            ";
            $css .= "
            .layout_layout1 .mm_menus_li > a, .ets_mm_block > h4, .ets_mm_block > .h4, .menu_acc
            {
            font-family: '" . $fontHeadings . "', Oswald, Verdana, sans-serif!important; }
            ";
            $css .= "
            .btn, .contactable-submit {
            font-family:'" . $fontButtons . "', Oswald, Verdana, sans-serif; }
            ";
            $css .= "
            html, body {
            font-family:'" . $fontText . "', Verdana, sans-serif; }
            ";
            $css .= "
            #products .regular-price, .elementor-widget-roy_product_tabs .regular-price, .featured-products .regular-price, .product-accessories .regular-price, .product-miniature .regular-price, .product-price, #products .product-price-and-shipping .price, .featured-products .product-price-and-shipping .price, .product-accessories .product-price-and-shipping .price, .product-miniature .product-price-and-shipping .price
            {
            font-family:'" . $fontPrice . "', Oswald, Verdana, sans-serif; }
            ";
            $css .= "
            #products .product-title a, .featured-products .product-title a, .product-accessories .product-title a, .product-miniature .product-title a {
            font-family:'" . $fontPn . "', Verdana, sans-serif; }
            ";
        }

        if (Configuration::get('NC_UP_MENU') == "1") {
            $css .= '
            .mm_menus_li > a { text-transform: none!important; }
            ';
        }
        if (Configuration::get('NC_UP_HEAD') == "1") {
            $css .= '
            h1, h2, h3, h4, h5, h6, #search_filters .facet .facet-title, #search_filters h4, #main h1:not(.active-filter-title), #product .featured-products h2, .products-section-title, h1.page-header, h2.page-header, h3.page-header, h4.page-header, h5.page-header, h6.page-header, .block-contact-title, .blockcms-title, .myaccount-title, .myaccount-title a, .side-column>* .title.hidden-md-up .h3, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title a, .side-column>.sidebar-block .sidebar-title span, .has-discount .discount, .tabs .nav-tabs .nav-link, .elementor-widget-roy_product_tabs .nav-tabs .nav-link, .side_menu .side_title, .side_menu form#contactable-contactForm p.contactable-header, #main h2
             { text-transform: none!important; }
            ';
        }
        if (Configuration::get('NC_UP_BUT') == "1") {
            $css .= '
            .btn, .contactable-submit { text-transform: none; }
            ';
        }

        if (Configuration::get('NC_UP_MENU') == "2") {
            $css .= '
            .mm_menus_li > a { text-transform: uppercase!important; }
            ';
        }
        if (Configuration::get('NC_UP_HEAD') == "2") {
            $css .= '
            h1, h2, h3, h4, h5, h6, .menu_acc, #search_filters h4, #header .row.action .blockcart a span.text, #main h1:not(.active-filter-title), #product .featured-products h2, .products-section-title, h1.page-header, h2.page-header, h3.page-header, h4.page-header, h5.page-header, h6.page-header, .block-contact-title, .blockcms-title, .myaccount-title, .myaccount-title a, .side-column>* .title.hidden-md-up .h3, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title a, .side-column>.sidebar-block .sidebar-title span, .layout_layout1 .mm_menus_li > a, .has-discount .discount, .tabs .nav-tabs .nav-link, .elementor-widget-roy_product_tabs .nav-tabs .nav-link, .side_menu .side_title, .side_menu form#contactable-contactForm p.contactable-header, #main h2 { text-transform: uppercase; }
            ';
        }
        if (Configuration::get('NC_UP_BUT') == "2") {
            $css .= '.btn, .contactable-submit { text-transform: uppercase; }';
        }
        if (Configuration::get('NC_UP_HP') == "2") {
            $css .= '.products-section-title { text-transform: uppercase!important; }';
        } else {
            $css .= '.products-section-title { text-transform: none!important; }';
        }
        if (Configuration::get('NC_UP_NC') == "2") {
            $css .= '#products .product-title a, .featured-products .product-title a, .product-accessories .product-title a, .product-miniature .product-title a { text-transform: uppercase; }';
        } else {
            $css .= '#products .product-title a, .featured-products .product-title a, .product-accessories .product-title a, .product-miniature .product-title a { text-transform: none; }';
        }
        if (Configuration::get('NC_UP_F') == "2") {
            $css .= '.blockcms-title, .footer-container h3, .myaccount-title, .myaccount-title a, .myaccount-title a:visited, .block-contact-title { text-transform: uppercase!important; }';
        } else {
            $css .= '.blockcms-title, .myaccount-title, .myaccount-title a, .myaccount-title a:visited, .footer-container h3, .block-contact-title { text-transform: none!important; }';
        }
        if (Configuration::get('NC_UP_NP') == "2") {
            $css .= '#main h1.product-title, .modal h1.product-title { text-transform: uppercase!important; }';
        } else {
            $css .= '#main h1.product-title, .modal h1.product-title { text-transform: none!important; }';
        }
        if (Configuration::get('NC_UP_MI') == "2") {
            $css .= '.tabs .nav-tabs .nav-link { text-transform: uppercase; }';
        } else {
            $css .= '.tabs .nav-tabs .nav-link { text-transform: none!important; }';
        }
        if (Configuration::get('NC_UP_BP') == "2") {
            $css .= '.sds_post_title_home a { text-transform: uppercase; }';
        } else {
            $css .= '.sdsarticleHeader .products-section-title, .sds_post_title_home a { text-transform: none!important; }';
        }

        if (Configuration::get('NC_FW_MENU')) {
            $css .= '.mm_menus_li > a { font-weight: ' . Configuration::get('NC_FW_MENU') . '!important } ';
        }
        if (Configuration::get('NC_FW_HEADING')) {
            $css .= 'h1, h2, h3, h4, h5, h6, #main h1:not(.active-filter-title) a, .product-comment-modal .modal-dialog h3, .sds_post_title_home a, #product .featured-products h2 a, .products-section-title a, #search_filters h4, #main h1:not(.active-filter-title), #product .featured-products h2, .products-section-title, h1.page-header, h2.page-header, h3.page-header, h4.page-header, h5.page-header, h6.page-header, .block-contact-title, .blockcms-title, .footer-container h3, .myaccount-title, .myaccount-title a, .side-column>* .title.hidden-md-up .h3, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title a, .side-column>.sidebar-block .sidebar-title span,  .has-discount .discount, .tabs .nav-tabs .nav-link, .elementor-widget-roy_product_tabs .nav-tabs .nav-links, .side_menu .side_title, .side_menu form#contactable-contactForm p.contactable-header, .sds_post_title_home a, .footer-container .links .h3, #main h2 { font-weight: ' . Configuration::get('NC_FW_HEADING') . '!important } ';
        }
        if (Configuration::get('NC_FW_BUT')) {
            $css .= '.btn, .contactable-submit { font-weight: ' . Configuration::get('NC_FW_BUT') . ' } ';
        }
        if (Configuration::get('NC_FW_PN')) {
            $css .= '#products .product-title a, .featured-products .product-title a, .product-accessories .product-title a, .product-miniature .product-title a { font-weight: ' . Configuration::get('NC_FW_PN') . '!important } ';
        }
        if (Configuration::get('NC_FW_CT')) {
            $css .= 'body, p { font-weight: ' . Configuration::get('NC_FW_CT') . ' } ';
        }
        if (Configuration::get('NC_FW_PRICE')) {
            $css .= '.product-price, .cart-grid-body .product-line-grid .product-line-grid-right .price .product-price, #products .product-price-and-shipping .price, .elementor-widget-roy_product_tabs .product-price-and-shipping .price, .featured-products .product-price-and-shipping .price, .product-accessories .product-price-and-shipping .price, .product-miniature .product-price-and-shipping .price { font-weight: ' . Configuration::get('NC_FW_PRICE') . ' } ';
        }

        if (Configuration::get('NC_ITAL_PN') == "2") {
            $css .= '#products .product-title a, .featured-products .product-title a, .product-accessories .product-title a, .product-miniature .product-title a { font-style: italic; }';
        }
        if (Configuration::get('NC_ITALIC_PP') == "2") {
            $css .= '.price { font-style: italic!important; }';
        }

        if (Configuration::get('NC_LS') !== "0") {
            $css .= 'body { letter-spacing: ' . Configuration::get('NC_LS') . 'px } ';
        }
        if (Configuration::get('NC_LS_H') !== "0") {
            $css .= '.products-section-title { letter-spacing: ' . Configuration::get('NC_LS_H') . 'px } ';
        }
        if (Configuration::get('NC_LS_T') !== "0") {
            $css .= 'h1, h2, h3, h4, h5, h6, #main h1:not(.active-filter-title), #product .featured-products h2, .products-section-title, h1.page-header, h2.page-header, h3.page-header, h4.page-header, h5.page-header, h6.page-header, .block-contact-title, .blockcms-title, .footer-container h3, .myaccount-title, .myaccount-title a, .side-column>* .title.hidden-md-up .h3, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title a, .side-column>.sidebar-block .sidebar-title span, .has-discount .discount, .nav-tabs .nav-link, .side_menu .side_title, .side_menu form#contactable-contactForm p.contactable-header, .sds_post_title_home a, .footer-container .links .h3, #main h2 { letter-spacing: ' . Configuration::get('NC_LS_T') . 'px } ';
        }
        if (Configuration::get('NC_LS_B') !== "0") {
            $css .= '.sdsreadMore .more a, .ac_results li, .ac_results li a { letter-spacing: ' . Configuration::get('NC_LS_B') . 'px } ';
        }
        if (Configuration::get('NC_LS_M') !== "0") {
            $css .= '.mm_menus_li > a { letter-spacing: ' . Configuration::get('NC_LS_M') . 'px!important } ';
        }
        if (Configuration::get('NC_LS_P') !== "0") {
            $css .= '#products .product-title a, .featured-products .product-title a, .product-accessories .product-title a, .product-miniature .product-title a { letter-spacing: ' . Configuration::get('NC_LS_P') . 'px } ';
        }


        if (Configuration::get('NC_B_RADIUS')) {
            $css .= '
            .btn { -webkit-border-radius: ' . Configuration::get('NC_B_RADIUS') . 'px; -moz-border-radius: ' . Configuration::get('NC_B_RADIUS') . 'px; border-radius: ' . Configuration::get('NC_B_RADIUS') . 'px; }
            ';
        }
        if (Configuration::get('NC_B_SHS') == 0) {
            $css .= '
            .btn { box-shadow:none!important }
            ';
        }

        if (Configuration::get('RC_B_NORMAL_BG')) {
            $css .= '
            .btn, a.btn { background-color: ' . Configuration::get('RC_B_NORMAL_BG') . ' }
            ';
        }
        if (Configuration::get('RC_B_NORMAL_BORDER')) {
            $css .= '
            .btn, a.btn { border-color: ' . Configuration::get('RC_B_NORMAL_BORDER') . ' }
            ';
        }
        if (Configuration::get('RC_B_NORMAL_COLOR')) {
            $css .= '
            .btn, a.btn { color: ' . Configuration::get('RC_B_NORMAL_COLOR') . ' }
            #category #left-column #search_filter_controls>button svg *, #_mobile_search_filters_clear_all svg * {
              stroke:' . Configuration::get('RC_B_NORMAL_COLOR') . '!important;
            }
            ';
        }

        if (Configuration::get('RC_B_NORMAL_BG_HOVER')) {
            $css .= '
            .btn:hover, .btn:focus, a.btn:hover, .btn.btn-primary:active, .btn.btn-primary.disabled:hover { background-color: ' . Configuration::get('RC_B_NORMAL_BG_HOVER') . ' }
            ';
        }
        if (Configuration::get('RC_B_NORMAL_BORDER_HOVER')) {
            $css .= '
            .btn:hover, .btn:focus, a.btn:hover, .btn.btn-primary:active, .btn.btn-primary.disabled:hover { border-color: ' . Configuration::get('RC_B_NORMAL_BORDER_HOVER') . ' }
            ';
        }
        if (Configuration::get('RC_B_NORMAL_COLOR_HOVER')) {
            $css .= '
            .btn:hover, .btn:focus, a.btn:hover, .btn.btn-primary:active, .btn.btn-primary.disabled:hover { color: ' . Configuration::get('RC_B_NORMAL_COLOR_HOVER') . '; outline:none!important; }
            ';
        }

        if (Configuration::get('RC_B_EX_BG')) {
            $css .= '
            .btn.bright { background-color: ' . Configuration::get('RC_B_EX_BG') . ' }
            ';
        }
        if (Configuration::get('RC_B_EX_BORDER')) {
            $css .= '
            .btn.bright { border-color: ' . Configuration::get('RC_B_EX_BORDER') . ' } ';
        }
        if (Configuration::get('RC_B_EX_COLOR')) {
            $css .= '
            .btn.bright { color: ' . Configuration::get('RC_B_EX_COLOR') . ' } ';
        }

        if (Configuration::get('RC_I_BG')) {
            $css .= '.bootstrap-touchspin .input-group-btn-vertical>.btn, .form-control, .alert, input, textarea, .form-control:disabled, .form-control[readonly], .form-control-select, body select.form-control:not([size]):not([multiple]), .customizationUploadLine textarea, input.uniform-input, select.uniform-multiselect, textarea.uniform { background-color: ' . Configuration::get('RC_I_BG') . ' } ';
        }
        if (Configuration::get('RC_I_B_COLOR')) {
            $css .= '.bootstrap-touchspin .input-group-btn-vertical>.btn, .form-control, .alert, input, textarea, .form-control-select, body select.form-control:not([size]):not([multiple]), #attributes .attribute_list #color_to_pick_list li, .customizationUploadLine textarea, input.uniform-input, select.uniform-multiselect, textarea.uniform { border-color: ' . Configuration::get('RC_I_B_COLOR') . ' }
            .input-group .input-group-btn>.btn, .input-group .input-group-btn>.btn[data-action=show-password]:before { background:' . Configuration::get('RC_I_B_COLOR') . ' }
            ';
        }
        if (Configuration::get('RC_I_COLOR')) {
            $css .= '.form-control, .alert, input, textarea, .form-control-select, body select.form-control:not([size]):not([multiple]), div.selector:after, .customizationUploadLine textarea, input.uniform-input, select.uniform-multiselect, textarea.uniform { color: ' . Configuration::get('RC_I_COLOR') . ' }
            ';
        }
        if (Configuration::get('RC_I_BG_FOCUS')) {
            $css .= '.product-quantity .input-group-btn-vertical .btn:hover, .form-control:focus, input:focus, textarea:focus, .form-control-select:focus, body select.form-control:not([size]):not([multiple]):focus, input.uniform-input:focus, select.uniform-multiselect:focus, textarea.uniform:focus { background-color: ' . Configuration::get('RC_I_BG_FOCUS') . ' } ';
        }
        if (Configuration::get('RC_I_B_FOCUS')) {
            $css .= '.product-quantity .input-group-btn-vertical .btn:hover, .form-control:focus, input:focus, textarea:focus, .form-control-select:focus, body select.form-control:not([size]):not([multiple]):focus, input.uniform-input:focus, select.uniform-multiselect:focus, textarea.uniform:focus { border-color: ' . Configuration::get('RC_I_B_FOCUS') . '!important; z-index:2; } ';
        }
        if (Configuration::get('RC_I_COLOR_FOCUS')) {
            $css .= '.product-quantity .input-group-btn-vertical .btn i, .form-control:focus, input:focus, textarea:focus, .form-control-select:focus, body select.form-control:not([size]):not([multiple]):focus, input.uniform-input:focus, select.uniform-multiselect:focus, textarea.uniform:focus { color: ' . Configuration::get('RC_I_COLOR_FOCUS') . ' }
            .input-group .input-group-btn.group-span-filestyle .buttonText svg *,
            .input-group .input-group-btn>.btn[data-action=show-password] i svg *
            {
                stroke: ' . Configuration::get('RC_I_COLOR_FOCUS') . '!important;
            }
            ';
        }
        if (Configuration::get('RC_I_PH')) {
            $css .= '
              input::-webkit-input-placeholder,
              textarea::-webkit-input-placeholder {
                color: ' . Configuration::get('RC_I_PH') . '!important;
              }
              input::-moz-placeholder,
              textarea::-moz-placeholder {
                color: ' . Configuration::get('RC_I_PH') . '!important;
              }
              input:-ms-input-placeholder,
              textarea:-ms-input-placeholder {
                color: ' . Configuration::get('RC_I_PH') . '!important;
              }
              input:-moz-placeholder,
              textarea:-moz-placeholder {
                color: ' . Configuration::get('RC_I_PH') . '!important;
              }
                input:active::-webkit-input-placeholder,
                textarea:active::-webkit-input-placeholder {
                  color: ' . Configuration::get('RC_I_PH') . '!important;
                }
                input:active::-moz-placeholder,
                textarea:active::-moz-placeholder {
                  color: ' . Configuration::get('RC_I_PH') . '!important;
                }
                input:active:-ms-input-placeholder,
                textarea:active:-ms-input-placeholder {
                  color: ' . Configuration::get('RC_I_PH') . '!important;
                }
                input:active:-moz-placeholder,
                textarea:active:-moz-placeholder {
                  color: ' . Configuration::get('RC_I_PH') . '!important;
                }
            ';
        }

        if (Configuration::get('RC_I_B_RADIUS')) {
            $css .= '.form-control, input:not(.btn), textarea, .form-control-select { -webkit-border-radius: ' . Configuration::get('RC_I_B_RADIUS') . 'px!important; -moz-border-radius: ' . Configuration::get('RC_I_B_RADIUS') . 'px!important; border-radius: ' . Configuration::get('RC_I_B_RADIUS') . 'px!important; } ';
        }

        if (Configuration::get('RC_RC_BG_ACTIVE')) {
            $css .= '
            .custom-checkbox input[type=checkbox]+span .checkbox-checked { color: ' . Configuration::get('RC_RC_BG_ACTIVE') . ' }
            .custom-radio input[type=radio]:checked+span { background: ' . Configuration::get('RC_RC_BG_ACTIVE') . ' }
            ';
        }


        $css .= '
            #search_filters .ui-slider .ui-slider-handle {
                top: -.5em;
                width: 1em;
                height: 1.4em;
                background: ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BG') . ';
                border: 2px solid ' . Configuration::get('RC_RC_BG_ACTIVE') . ';
            }
            #search_filters .ui-slider-horizontal {
              background: ' . Configuration::get('RC_I_B_COLOR') . ';
            }
            #search_filters .ui-widget-header {
              background: ' . Configuration::get('RC_RC_BG_ACTIVE') . ';
            }
            ';


        // ****************  HOMEPAGE CONTENT styles start



        $css .= '#roycontent_beforeheader {
              margin-top:' . Configuration::get('RC_BAN_TS_BEHEAD') . 'px;
              margin-bottom:' . Configuration::get('RC_BAN_BS_BEHEAD') . 'px;
            }';
        if (Configuration::get('RC_BAN_TS_BEHEAD') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_beforeheader { margin-top:30px!important; } } ';
        }
        if (Configuration::get('RC_BAN_BS_BEHEAD') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_beforeheader { margin-bottom:30px!important } } ';
        }
        if (Configuration::get('RC_BAN_SPA_BEHEAD') == "2") {
            $css .= '
            #roycontent_beforeheader ul { padding:0 15px!important }
            #roycontent_beforeheader ul li { margin:0!important; padding:0!important }
            ';
        }


        $css .= '#roycontent_top {
              margin-top:' . Configuration::get('RC_BAN_TS_TOP') . 'px;
              margin-bottom:' . Configuration::get('RC_BAN_BS_TOP') . 'px;
            }';
        if (Configuration::get('RC_BAN_TS_TOP') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_top { margin-top:30px!important; } } ';
        }
        if (Configuration::get('RC_BAN_BS_TOP') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_top { margin-bottom:30px!important } } ';
        }
        if (Configuration::get('RC_BAN_SPA_TOP') == "2") {
            $css .= '
            #roycontent_top ul { padding:0 15px!important }
            #roycontent_top ul li { margin:0!important; padding:0!important }
            ';
        }


        $css .= '
            #roycontent_left { margin-top:' . Configuration::get('RC_BAN_TS_LEFT') . 'px!important;
              margin-bottom:' . Configuration::get('RC_BAN_BS_LEFT') . 'px!important }
            #roycontent_right { margin-top:' . Configuration::get('RC_BAN_TS_RIGHT') . 'px!important;
              margin-bottom:' . Configuration::get('RC_BAN_BS_RIGHT') . 'px!important }
            @media (max-width:767px) {
            #roycontent_left, #roycontent_right { margin-top:0!important; margin-bottom:0!important; display:block!important; } }
            ';


        $css .= '#roycontent_hometabcontent {
              margin-top:' . Configuration::get('RC_BAN_TS_PRO') . ';
              margin-bottom:' . Configuration::get('RC_BAN_BS_PRO') . ';
            }';
        if (Configuration::get('RC_BAN_TS_PRO') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_hometabcontent { margin-top:30px!important; } } ';
        }
        if (Configuration::get('RC_BAN_BS_PRO') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_hometabcontent { margin-bottom:30px!important } } ';
        }
        if (Configuration::get('RC_BAN_SPA_PRO') == "2") {
            $css .= '
            #roycontent_hometabcontent ul { padding:0 15px!important }
            #roycontent_hometabcontent ul li { margin:0!important; padding:0!important }
            ';
        }


        $css .= '#roycontent_footerbefore {
              margin-top:' . Configuration::get('RC_BAN_TS_BEFOOT') . ';
              margin-bottom:' . Configuration::get('RC_BAN_BS_BEFOOT') . ';
            }';
        if (Configuration::get('RC_BAN_TS_BEFOOT') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_footerbefore { margin-top:30px!important; } } ';
        }
        if (Configuration::get('RC_BAN_BS_BEFOOT') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_footerbefore { margin-bottom:30px!important } } ';
        }
        if (Configuration::get('RC_BAN_SPA_BEFOOT') == "2") {
            $css .= '
            #roycontent_footerbefore ul { padding:0 15px!important }
            #roycontent_footerbefore ul li { margin:0!important; padding:0!important }
            ';
        }


        $css .= '#roycontent_footer {
              margin-top:' . Configuration::get('RC_BAN_TS_FOOT') . ';
              margin-bottom:' . Configuration::get('RC_BAN_BS_FOOT') . ';
            }';
        if (Configuration::get('RC_BAN_TS_FOOT') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_footer { margin-top:30px!important; } } ';
        }
        if (Configuration::get('RC_BAN_BS_FOOT') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_footer { margin-bottom:30px!important } } ';
        }
        if (Configuration::get('RC_BAN_SPA_FOOT') == "2") {
            $css .= '
            #roycontent_footer ul { padding:0 15px!important }
            #roycontent_footer ul li { margin:0!important; padding:0!important }
            ';
        }


        $css .= '#roycontent_sidecart {
              margin-top:' . Configuration::get('RC_BAN_TS_SIDECART') . ';
              margin-bottom:' . Configuration::get('RC_BAN_BS_SIDECART') . ';
            }';
        if (Configuration::get('RC_BAN_TS_SIDECART') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_sidecart { margin-top:30px!important; } } ';
        }
        if (Configuration::get('RC_BAN_BS_SIDECART') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_sidecart { margin-bottom:30px!important } } ';
        }
        if (Configuration::get('RC_BAN_SPA_SIDECART') == "2") {
            $css .= '
            #roycontent_sidecart ul { padding:0 15px!important }
            #roycontent_sidecart ul li { margin:0!important; padding:0!important }
            ';
        }


        $css .= '#roycontent_sidesearch {
              margin-top:' . Configuration::get('RC_BAN_TS_SIDESEARCH') . ';
              margin-bottom:' . Configuration::get('RC_BAN_BS_SIDESEARCH') . ';
            }';
        if (Configuration::get('RC_BAN_TS_SIDESEARCH') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_sidesearch { margin-top:30px!important; } } ';
        }
        if (Configuration::get('RC_BAN_BS_SIDESEARCH') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_sidesearch { margin-bottom:30px!important } } ';
        }
        if (Configuration::get('RC_BAN_SPA_SIDESEARCH') == "2") {
            $css .= '
            #roycontent_sidesearch ul { padding:0 15px!important }
            #roycontent_sidesearch ul li { margin:0!important; padding:0!important }
            ';
        }


        $css .= '#roycontent_sidemail {
              margin-top:' . Configuration::get('RC_BAN_TS_SIDEMAIL') . ';
              margin-bottom:' . Configuration::get('RC_BAN_BS_SIDEMAIL') . ';
            }';
        if (Configuration::get('RC_BAN_TS_SIDEMAIL') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_sidemail { margin-top:30px!important; } } ';
        }
        if (Configuration::get('RC_BAN_BS_SIDEMAIL') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_sidemail { margin-bottom:30px!important } } ';
        }
        if (Configuration::get('RC_BAN_SPA_SIDEMAIL') == "2") {
            $css .= '
            #roycontent_sidemail ul { padding:0 15px!important }
            #roycontent_sidemail ul li { margin:0!important; padding:0!important }
            ';
        }


        $css .= '#roycontent_sidemobilemenu {
              margin-top:' . Configuration::get('RC_BAN_TS_SIDEMOBILEMENU') . ';
              margin-bottom:' . Configuration::get('RC_BAN_BS_SIDEMOBILEMENU') . ';
            }';
        if (Configuration::get('RC_BAN_TS_SIDEMOBILEMENU') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_sidemobilemenu { margin-top:30px!important; } } ';
        }
        if (Configuration::get('RC_BAN_BS_SIDEMOBILEMENU') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_sidemobilemenu { margin-bottom:30px!important } } ';
        }
        if (Configuration::get('RC_BAN_SPA_SIDEMOBILEMENU') == "2") {
            $css .= '
            #roycontent_sidemobilemenu ul { padding:0 15px!important }
            #roycontent_sidemobilemenu ul li { margin:0!important; padding:0!important }
            ';
        }


        $css .= '#roycontent_productbeforebuy {
              margin-top:' . Configuration::get('RC_BAN_TS_PRODUCT') . ';
              margin-bottom:' . Configuration::get('RC_BAN_BS_PRODUCT') . ';
            }';
        if (Configuration::get('RC_BAN_TS_PRODUCT') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_productbeforebuy { margin-top:30px!important; } } ';
        }
        if (Configuration::get('RC_BAN_BS_PRODUCT') > "30") {
            $css .= '@media (max-width:767px) { #roycontent_productbeforebuy { margin-bottom:30px!important } } ';
        }
        if (Configuration::get('RC_BAN_SPA_PRODUCT') == "2") {
            $css .= '
            #roycontent_productbeforebuy ul { padding:0 15px!important }
            #roycontent_productbeforebuy ul li { margin:0!important; padding:0!important }
            ';
        }


        // brands slider
        if (Configuration::get('RC_BRAND_NAME')) {
            $css .= '
            #roy_brands ul.brands_text a { color: ' . Configuration::get('RC_BRAND_NAME') . ' }
            ';
        }
        if (Configuration::get('RC_BRAND_NAME_HOVER')) {
            $css .= '
            #roy_brands ul.brands_text a:hover { color: ' . Configuration::get('RC_BRAND_NAME_HOVER') . ' }
            ';
        }


        // Featured
        if (Configuration::get('NC_CAROUSEL_FEATUREDS') == "2") {
            if (Configuration::get('NC_ITEMS_FEATUREDS') == "2") {
                $css .= '
              @media (min-width:480px) {
              .roy_featured.featured-products .product-item {
                    width:50%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_FEATUREDS') == "4") {
                $css .= '
              @media (min-width:480px) {
              .roy_featured.featured-products .product-item {
                    width:25%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_FEATUREDS') == "5") {
                $css .= '
              @media (min-width:480px) {
              .roy_featured.featured-products .product-item {
                    width:20%;
              } }
        ';
            }
        }

        // Best
        if (Configuration::get('NC_CAROUSEL_BEST') == "2") {
            if (Configuration::get('NC_ITEMS_BESTS') == "2") {
                $css .= '
              @media (min-width:480px) {
              .roy_best.featured-products .product-item {
                    width:50%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_BESTS') == "4") {
                $css .= '
              @media (min-width:480px) {
              .roy_best.featured-products .product-item {
                    width:25%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_BESTS') == "5") {
                $css .= '
              @media (min-width:480px) {
              .roy_best.featured-products .product-item {
                    width:20%;
              } }
        ';
            }
        }

        // NEW
        if (Configuration::get('NC_CAROUSEL_NEW') == "2") {
            if (Configuration::get('NC_ITEMS_NEWS') == "2") {
                $css .= '
              @media (min-width:480px) {
              .roy_new.featured-products .product-item {
                    width:50%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_NEWS') == "4") {
                $css .= '
              @media (min-width:480px) {
              .roy_new.featured-products .product-item {
                    width:25%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_NEWS') == "5") {
                $css .= '
              @media (min-width:480px) {
              .roy_new.featured-products .product-item {
                    width:20%;
              } }
        ';
            }
        }

        // SALE
        if (Configuration::get('NC_CAROUSEL_SALE') == "2") {
            if (Configuration::get('NC_ITEMS_SALES') == "2") {
                $css .= '
              @media (min-width:480px) {
              .roy_specials.featured-products .product-item {
                    width:50%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_SALES') == "4") {
                $css .= '
              @media (min-width:480px) {
              .roy_specials.featured-products .product-item {
                    width:25%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_SALES') == "5") {
                $css .= '
              @media (min-width:480px) {
              .roy_specials.featured-products .product-item {
                    width:20%;
              } }
        ';
            }
        }

        // CC1
        if (Configuration::get('NC_CAROUSEL_CUSTOM1') == "2") {
            if (Configuration::get('NC_ITEMS_CUSTOM1S') == "2") {
                $css .= '
              @media (min-width:480px) {
              .roy_specials.featured-products .product-item {
                    width:50%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_CUSTOM1S') == "4") {
                $css .= '
              @media (min-width:480px) {
              .roy_specials.featured-products .product-item {
                    width:25%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_CUSTOM1S') == "5") {
                $css .= '
              @media (min-width:480px) {
              .roy_specials.featured-products .product-item {
                    width:20%;
              } }
        ';
            }
        }

        // CC2
        if (Configuration::get('NC_CAROUSEL_CUSTOM2') == "2") {
            if (Configuration::get('NC_ITEMS_CUSTOM2S') == "2") {
                $css .= '
              @media (min-width:480px) {
              .roy_specials.featured-products .product-item {
                    width:50%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_CUSTOM2S') == "4") {
                $css .= '
              @media (min-width:480px) {
              .roy_specials.featured-products .product-item {
                    width:25%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_CUSTOM2S') == "5") {
                $css .= '
              @media (min-width:480px) {
              .roy_specials.featured-products .product-item {
                    width:20%;
              } }
        ';
            }
        }

        // CC3
        if (Configuration::get('NC_CAROUSEL_CUSTOM3') == "2") {
            if (Configuration::get('NC_ITEMS_CUSTOM3S') == "2") {
                $css .= '
              @media (min-width:480px) {
              .roy_specials.featured-products .product-item {
                    width:50%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_CUSTOM3S') == "4") {
                $css .= '
              @media (min-width:480px) {
              .roy_specials.featured-products .product-item {
                    width:25%;
              } }
        ';
            }
            if (Configuration::get('NC_ITEMS_CUSTOM3S') == "5") {
                $css .= '
              @media (min-width:480px) {
              .roy_specials.featured-products .product-item {
                    width:20%;
              } }
        ';
            }
        }



        // ****************  PAGE AND SIDEBAR styles start

        if (Configuration::get('RC_B_LAYOUT') == "2") {
            $css .= '
          .breadcrumb ol { text-align:center }
      ';
        }
        if (Configuration::get('RC_B_LAYOUT') == "3") {
            $css .= '
          .breadcrumb ol { text-align:right }
      ';
        }
        if (Configuration::get('RC_B_LINK')) {
            $css .= '
			.breadcrumb li, .breadcrumb li a { color: ' . Configuration::get('RC_B_LINK') . ' }
			';
        }
        if (Configuration::get('RC_B_LINK_HOVER')) {
            $css .= '
			.breadcrumb li a:hover { color: ' . Configuration::get('RC_B_LINK_HOVER') . ' }
			';
        }
        if (Configuration::get('RC_B_SEPARATOR')) {
            $css .= '
      .breadcrumb li:after { color: ' . Configuration::get('RC_B_SEPARATOR') . ' }
      ';
        }


        if (Configuration::get('RC_PAGE_BQ_Q')) {
            $css .= '
                  #main .page-content .testimonials span.before, #main .page-content .testimonials span.after { color: ' . Configuration::get('RC_PAGE_BQ_Q') . ' }
            ';
        }


        if (Configuration::get('RC_WARNING_MESSAGE_COLOR')) {
            $css .= '
                  .alert-warning { border-color: ' . Configuration::get('RC_WARNING_MESSAGE_COLOR') . ' }
                  ';
        }
        if (Configuration::get('RC_SUCCESS_MESSAGE_COLOR')) {
            $css .= '
                  .done { color: ' . Configuration::get('RC_SUCCESS_MESSAGE_COLOR') . ' }
                  ';
        }
        if (Configuration::get('RC_DANGER_MESSAGE_COLOR')) {
            $css .= '
                  .alert-danger {
                    border-color: ' . Configuration::get('RC_DANGER_MESSAGE_COLOR') . ' }
                  ';
        }

        if (Configuration::get('RC_CONTACT_ICON')) {
            $css .= '
            .contact-rich .block .icon svg * { stroke: ' . Configuration::get('RC_CONTACT_ICON') . '!important }
            ';
        }

        // ****************  SIDEBAR styles start


        if (Configuration::get('RC_SIDEBAR_TITLE') == 0) {
            $css .= '
                  .side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { background: none }
                  ';
        }

        if (Configuration::get('RC_SIDEBAR_TITLE_BG') && (Configuration::get('RC_SIDEBAR_TITLE') == 1)) {
            $css .= '
            .side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { background: ' . Configuration::get('RC_SIDEBAR_TITLE_BG') . '  }
                  ';
        }

        if ((Configuration::get('RC_SIDEBAR_TITLE_B') == "1") && Configuration::get('RC_SIDEBAR_TITLE_B1')) {
            $css .= '
                  .side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { border-top: ' . Configuration::get('RC_SIDEBAR_TITLE_B1') . 'px solid ' . Configuration::get('RC_SIDEBAR_TITLE_BORDER') . '; }
                  ';
        }
        if ((Configuration::get('RC_SIDEBAR_TITLE_B') == "1") && Configuration::get('RC_SIDEBAR_TITLE_B2')) {
            $css .= '
                  .side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { border-right: ' . Configuration::get('RC_SIDEBAR_TITLE_B2') . 'px solid ' . Configuration::get('RC_SIDEBAR_TITLE_BORDER') . '; }
                  ';
        }
        if ((Configuration::get('RC_SIDEBAR_TITLE_B') == "1") && Configuration::get('RC_SIDEBAR_TITLE_B3')) {
            $css .= '
                  .side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { border-bottom: ' . Configuration::get('RC_SIDEBAR_TITLE_B3') . 'px solid ' . Configuration::get('RC_SIDEBAR_TITLE_BORDER') . '; }
                  ';
        }
        if ((Configuration::get('RC_SIDEBAR_TITLE_B') == "1") && Configuration::get('RC_SIDEBAR_TITLE_B4')) {
            $css .= '
                  .side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { border-left: ' . Configuration::get('RC_SIDEBAR_TITLE_B4') . 'px solid ' . Configuration::get('RC_SIDEBAR_TITLE_BORDER') . '; }
                  ';
        }
        if (Configuration::get('RC_SIDEBAR_TITLE_B') == "1" && Configuration::get('RC_SIDEBAR_TITLE_B1') == "0") {
            $css .= '.side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { border-top: none; } ';
        }
        if (Configuration::get('RC_SIDEBAR_TITLE_B') == "1" && Configuration::get('RC_SIDEBAR_TITLE_B2') == "0") {
            $css .= '.side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { border-right: none; } ';
        }
        if (Configuration::get('RC_SIDEBAR_TITLE_B') == "1" && Configuration::get('RC_SIDEBAR_TITLE_B3') == "0") {
            $css .= '.side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { border-bottom: none; } ';
        }
        if (Configuration::get('RC_SIDEBAR_TITLE_B') == "1" && Configuration::get('RC_SIDEBAR_TITLE_B4') == "0") {
            $css .= '.side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { border-left: none; } ';
        }

        if (Configuration::get('RC_SIDEBAR_TITLE_B') == "0") {
            $css .= '
          .side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { border: none; }
        ';
        }
        if (Configuration::get('RC_SIDEBAR_TITLE_BR')) {
            $css .= '
            .side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { -webkit-border-radius: ' . Configuration::get('RC_SIDEBAR_TITLE_BR') . 'px; -moz-border-radius: ' . Configuration::get('RC_SIDEBAR_TITLE_BR') . 'px; border-radius: ' . Configuration::get('RC_SIDEBAR_TITLE_BR') . 'px; }
            ';
        }
        if (Configuration::get('RC_SIDEBAR_TITLE_BR') == "0") {
            $css .= '
            .side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { -webkit-border-radius: 0; -moz-border-radius: 0; border-radius: 0; }
            ';
        }
        if ((Configuration::get('RC_SIDEBAR_TITLE_B') == "0" && Configuration::get('RC_SIDEBAR_TITLE') == "0") || (Configuration::get('RC_SIDEBAR_TITLE') == "0" && Configuration::get('RC_SIDEBAR_TITLE_B4') == "0")) {
            $css .= '
            @media(min-width:992px){
          .side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { padding-left:1px; } }
          ';
        }
        if ((Configuration::get('RC_SIDEBAR_TITLE_B') == "0" && Configuration::get('RC_SIDEBAR_TITLE') == "0")) {
            $css .= '
          .side-column>* .title.hidden-md-up, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title { padding-bottom:18px; min-height:64px; }
          ';
        }



        if (Configuration::get('RC_SIDEBAR_TITLE_LINK')) {
            $css .= '
            .side-column>* .title.hidden-md-up .h3, .side-column>.links h3, .side-column>.sidebar-block .sidebar-title a, .side-column>.sidebar-block .sidebar-title span, .sidebar-block .title .float-xs-right, .side-column > .links .title .float-xs-right, .sidebar-block .links .navbar-toggler { color: ' . Configuration::get('RC_SIDEBAR_TITLE_LINK') . ' }
            ';
        }
        if (Configuration::get('RC_SIDEBAR_TITLE_LINK_HOVER')) {
            $css .= '
            .side-column>.sidebar-block .sidebar-title a:hover { color: ' . Configuration::get('RC_SIDEBAR_TITLE_LINK_HOVER') . ' }
            ';
        }

        if (Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BG')) {
            $css .= '
                  .side-column>.contact-rich, .side-column>.links ul, .side-column>.sidebar-block .sidebar-content, #search_filters { background-color: ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BG') . ' }
                  #search_filters .color:before, #search_filters .custom-checkbox input[type=checkbox]+span.color:before {
                        box-shadow: 0 0 0 3px ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BG') . ';
                    }
                    .side-column>.block-categories .sidebar-content .collapse-icons .add, .side-column>.block-categories .sidebar-content .collapse-icons .remove { color: ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BG') . ' }
                    .side-column>.block-categories .collapse-icons[aria-expanded=true] .remove { background-color: ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BG') . ' }

                    @media (max-width: 991px) {
                    #category #left-column #search_filter_controls>span button {
                        background: ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BG') . ';
                    } }
            ';
        }
        if ((Configuration::get('RC_SIDEBAR_CONTENT_B') == "1") && Configuration::get('RC_SIDEBAR_CONTENT_B1')) {
            $css .= '
                  .side-column>.contact-rich, .side-column>.links ul, .side-column>.sidebar-block .sidebar-content, #search_filters { border-top: ' . Configuration::get('RC_SIDEBAR_CONTENT_B1') . 'px solid ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BORDER') . '; }
                  ';
        }
        if ((Configuration::get('RC_SIDEBAR_CONTENT_B') == "1") && Configuration::get('RC_SIDEBAR_CONTENT_B2')) {
            $css .= '
                  .side-column>.contact-rich, .side-column>.links ul, .side-column>.sidebar-block .sidebar-content, #search_filters { border-right: ' . Configuration::get('RC_SIDEBAR_CONTENT_B2') . 'px solid ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BORDER') . '; }
                  ';
        }
        if ((Configuration::get('RC_SIDEBAR_CONTENT_B') == "1") && Configuration::get('RC_SIDEBAR_CONTENT_B3')) {
            $css .= '
                  .side-column>.contact-rich, .side-column>.links ul, .side-column>.sidebar-block .sidebar-content, #search_filters { border-bottom: ' . Configuration::get('RC_SIDEBAR_CONTENT_B3') . 'px solid ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BORDER') . '; }
                  ';
        }
        if ((Configuration::get('RC_SIDEBAR_CONTENT_B') == "1") && Configuration::get('RC_SIDEBAR_CONTENT_B4')) {
            $css .= '
                  .side-column>.contact-rich, .side-column>.links ul, .side-column>.sidebar-block .sidebar-content, #search_filters { border-left: ' . Configuration::get('RC_SIDEBAR_CONTENT_B4') . 'px solid ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BORDER') . '; }
                  ';
        }
        if (Configuration::get('RC_SIDEBAR_CONTENT_B') == "1" && Configuration::get('RC_SIDEBAR_CONTENT_B1') == "0") {
            $css .= ' .side-column>.contact-rich, .side-column>.links ul, .side-column>.sidebar-block .sidebar-content, #search_filters { border-top: none; } ';
        }
        if (Configuration::get('RC_SIDEBAR_CONTENT_B') == "1" && Configuration::get('RC_SIDEBAR_CONTENT_B2') == "0") {
            $css .= ' .side-column>.contact-rich, .side-column>.links ul, .side-column>.sidebar-block .sidebar-content, #search_filters { border-right: none; } ';
        }
        if (Configuration::get('RC_SIDEBAR_CONTENT_B') == "1" && Configuration::get('RC_SIDEBAR_CONTENT_B3') == "0") {
            $css .= ' .side-column>.contact-rich, .side-column>.links ul, .side-column>.sidebar-block .sidebar-content, #search_filters { border-bottom: none; } ';
        }
        if (Configuration::get('RC_SIDEBAR_CONTENT_B') == "1" && Configuration::get('RC_SIDEBAR_CONTENT_B4') == "0") {
            $css .= ' .side-column>.contact-rich, .side-column>.links ul, .side-column>.sidebar-block .sidebar-content, #search_filters { border-left: none; } ';
        }

        if (Configuration::get('RC_SIDEBAR_CONTENT_B') == "0") {
            $css .= '.side-column>.contact-rich, .side-column>.links ul, .side-column>.sidebar-block .sidebar-content, #search_filters { border: none; } ';
        }

        if (Configuration::get('RC_SIDEBAR_CONTENT_BR') > "0") {
            $css .= '
            .side-column>.contact-rich, .side-column>.links ul, .side-column>.sidebar-block .sidebar-content, #search_filters { -webkit-border-radius: ' . Configuration::get('RC_SIDEBAR_CONTENT_BR') . 'px; -moz-border-radius: ' . Configuration::get('RC_SIDEBAR_CONTENT_BR') . 'px; border-radius: ' . Configuration::get('RC_SIDEBAR_CONTENT_BR') . 'px; }
            ';
        }
        if (Configuration::get('RC_SIDEBAR_CONTENT_BR') == "0") {
            $css .= '
            .side-column>.contact-rich, .side-column>.links ul, .side-column>.sidebar-block .sidebar-content, #search_filters { -webkit-border-radius: 0; -moz-border-radius: 0; border-radius: 0; }
            ';
        }

        if (Configuration::get('RC_SIDEBAR_BLOCK_TEXT_COLOR')) {
            $css .= '
            .side-column > .sidebar-block .sidebar-content,
            .side-column > .contact-rich,
            .side-column > .links ul, .side-column>#roy_specials_col .product-miniature .product-description .prod-short-desc p { color: ' . Configuration::get('RC_SIDEBAR_BLOCK_TEXT_COLOR') . ' }
                  ';
        }
        if (Configuration::get('RC_SIDEBAR_BLOCK_LINK')) {
            $css .= '
                  #category #left-column #search_filters .facet .navbar-toggler i, #search_filters .js-search-filters-clear-all span, #search_filters .facet .facet-label a, .side-column>.contact-rich a, .side-column>.links ul a, .side-column>.sidebar-block .sidebar-content a { color: ' . Configuration::get('RC_SIDEBAR_BLOCK_LINK') . ' }
                  #search_filters .js-search-filters-clear-all i svg *, #category #left-column #search_filter_controls>span button i svg * { stroke: ' . Configuration::get('RC_SIDEBAR_BLOCK_LINK') . '!important }
                  @media (max-width: 991px) {
                    #category #left-column #search_filters .facet .h6 { color: ' . Configuration::get('RC_SIDEBAR_BLOCK_LINK') . ' } }
                  ';
        }
        if (Configuration::get('RC_SIDEBAR_BLOCK_LINK_HOVER')) {
            $css .= '
                  #search_filters .js-search-filters-clear-all:hover span, #search_filters .facet .facet-label a:hover, .side-column>.contact-rich a:hover, .side-column>.links ul a:hover, .side-column>.sidebar-block .sidebar-content a:hover { color: ' . Configuration::get('RC_SIDEBAR_BLOCK_LINK_HOVER') . ' }
                  .side-column>.block-categories .collapse-icons .add:hover:after, .side-column>.block-categories .collapse-icons .add:hover:before, .side-column>.block-categories .collapse-icons .remove:hover:after, .side-column>.block-categories .collapse-icons .remove:hover:before  { border-color: ' . Configuration::get('RC_SIDEBAR_BLOCK_LINK_HOVER') . ' }
                  #search_filters .js-search-filters-clear-all:hover i svg * { stroke: ' . Configuration::get('RC_SIDEBAR_BLOCK_LINK_HOVER') . '!important }
                  ';
        }
        if (Configuration::get('RC_SIDEBAR_ITEM_SEPARATOR')) {
            $css .= '
          .side-column>.block-categories .category-sub-menu li[data-depth="1"], .side-column>.block-categories .category-sub-menu li[data-depth="0"]>a, #search_filters .js-search-filters-clear-all { border-color: ' . Configuration::get('RC_SIDEBAR_ITEM_SEPARATOR') . ' }
          .side-column>.block-categories .collapse-icons .add:before, .side-column>.block-categories .collapse-icons .add:after, .side-column>.block-categories .collapse-icons .remove:before, .side-column>.block-categories .collapse-icons .remove:after { border-color: ' . Configuration::get('RC_SIDEBAR_BLOCK_LINK') . ' }
          .side-column>.block-categories li[data-depth="0"] .collapse>ul:before { background-color: ' . Configuration::get('RC_SIDEBAR_ITEM_SEPARATOR') . ' }

          @media (max-width: 991px) {
          #category #left-column #search_filters .facet {
              border-bottom: 1px solid ' . Configuration::get('RC_SIDEBAR_ITEM_SEPARATOR') . ';
          }
          #category #left-column #search_filters .facet ul li {
              border-top: 1px solid ' . Configuration::get('RC_SIDEBAR_ITEM_SEPARATOR') . ';
          } }
        ';
        }
        if (Configuration::get('RC_PL_FILTER_T')) {
            $css .= '
                #search_filters .facet .facet-title, #search_filters h4 { color: ' . Configuration::get('RC_PL_FILTER_T') . ' }
                #search_filters .js-search-filters-clear-all { text-transform:none!important }
        ';
        }


        if (Configuration::get('RC_SIDEBAR_C')) {
            $css .= '.side-column .owl-carousel .owl-nav>* { color: ' . Configuration::get('RC_SIDEBAR_C') . ' } ';
        }
        if (Configuration::get('RC_SIDEBAR_HC')) {
            $css .= '.side-column .owl-carousel .owl-nav>*:hover { color: ' . Configuration::get('RC_SIDEBAR_HC') . ' } ';
        }


        if (Configuration::get('RC_SIDEBAR_PRODUCT_PRICE')) {
            $css .= '
                  .side-column>#roy_specials_col .product-item .product-price-and-shipping .price { color: ' . Configuration::get('RC_SIDEBAR_PRODUCT_PRICE') . ' }
                  ';
        }
        if (Configuration::get('RC_SIDEBAR_PRODUCT_OPRICE')) {
            $css .= '
                  .sidebar-block .product-miniature .regular-price { color: ' . Configuration::get('RC_SIDEBAR_PRODUCT_OPRICE') . ' }
                  ';
        }

        if (Configuration::get('RC_SIDEBAR_BUTTON_BG')) {
            $css .= '
                  .sidebar-block .btn { background-color: ' . Configuration::get('RC_SIDEBAR_BUTTON_BG') . ' }
                  ';
        }
        if (Configuration::get('RC_SIDEBAR_BUTTON_BORDER')) {
            $css .= '
                  .sidebar-block .btn { border-color: ' . Configuration::get('RC_SIDEBAR_BUTTON_BORDER') . ' }
                  ';
        }
        if (Configuration::get('RC_SIDEBAR_BUTTON_COLOR')) {
            $css .= '
                  .sidebar-block .btn { color: ' . Configuration::get('RC_SIDEBAR_BUTTON_COLOR') . ' }
                  ';
        }
        if (Configuration::get('RC_SIDEBAR_BUTTON_HBG')) {
            $css .= '
                  .sidebar-block .btn:hover { background-color: ' . Configuration::get('RC_SIDEBAR_BUTTON_HBG') . ' }
                  ';
        }
        if (Configuration::get('RC_SIDEBAR_BUTTON_HBORDER')) {
            $css .= '
                  .sidebar-block .btn:hover { border-color: ' . Configuration::get('RC_SIDEBAR_BUTTON_HBORDER') . ' }
                  ';
        }
        if (Configuration::get('RC_SIDEBAR_BUTTON_HCOLOR')) {
            $css .= '
                  .sidebar-block .btn:hover  { color: ' . Configuration::get('RC_SIDEBAR_BUTTON_HCOLOR') . ' }
                  ';
        }


        // ****************  PRODUCTS AND CATEGORIES styles start

        if (Configuration::get('NC_PRODUCT_SWITCH') == "2") {
            $css .= '
                  @media (min-width:480px) {
                  #products #js-product-list .product-item {
                        width:50%;
                  } }
            ';
        }
        if (Configuration::get('NC_PRODUCT_SWITCH') == "3") {
            $css .= '
            ';
        }
        if (Configuration::get('NC_PRODUCT_SWITCH') == "4") {
            $css .= '
                  @media (min-width:480px) {
                  #products #js-product-list .product-item {
                        width:25%;
                  } }
            ';
        }
        if (Configuration::get('NC_PRODUCT_SWITCH') == "5") {
            $css .= '
                  @media (min-width:480px) {
                  #products #js-product-list .product-item {
                        width:20%;
                  } }
            ';
        }

        if (Configuration::get('NC_CAT_S') == 0) {
            $css .= '
            .block-category { display:none }
			';
        }
        if (Configuration::get('NC_SUBCAT_S') == 0) {
            $css .= '
            #subcategories { display:none }
			';
        }
        if (Configuration::get('RC_PL_NAV_GRID')) {
            $css .= '
            i.gl svg * { stroke: ' . Configuration::get('RC_PL_NAV_GRID') . '!important; }
            ';
        }
        if (Configuration::get('RC_PL_NUMBER_COLOR')) {
            $css .= '.pagination .current a { color: ' . Configuration::get('RC_PL_NUMBER_COLOR') . ' }
    			';
        }
        if (Configuration::get('RC_PL_NUMBER_COLOR_HOVER')) {
            $css .= '.pagination a { color: ' . Configuration::get('RC_PL_NUMBER_COLOR_HOVER') . ' }
    			';
        }


        if (Configuration::get('NC_PC_LAYOUTS') == "1") {
            $css .= '';
            if (Configuration::get('RC_PL_ITEM_BG')) {
                $css .= '#products .product-description, .featured-products .product-description, .product-accessories .product-description, .product-miniature .product-description { background: ' . Configuration::get('RC_PL_ITEM_BG') . ' }
                ';
            }
            if (Configuration::get('RC_PL_ITEM_BORDER')) {
                $css .= '#products .product-description, .featured-products .product-description, .product-accessories .product-description, .product-miniature .product-description { border-color: ' . Configuration::get('RC_PL_ITEM_BORDER') . ' } ';
            }
            if (Configuration::get('NC_PL_ITEM_BORDERH')) {
                $css .= '#products .thumbnail-container:hover .product-description, .featured-products .thumbnail-container:hover .product-description, .product-accessories .thumbnail-container:hover .product-description, .product-miniature .thumbnail-container:hover .product-description { border-color: ' . Configuration::get('NC_PL_ITEM_BORDERH') . ' } ';
            }
        }

        if (Configuration::get('NC_PC_LAYOUTS') == "3") {
            $css .= '';
            if (Configuration::get('RC_PL_ITEM_BG')) {
                $css .= '#products .product-description, .featured-products .product-description, .product-accessories .product-description, .product-miniature .product-description { background: ' . Configuration::get('RC_PL_ITEM_BG') . ' }
                ';
            }
            if (Configuration::get('RC_PL_ITEM_BORDER')) {
                $css .= '
                #products .product-description, .featured-products .product-description, .product-accessories .product-description, .product-miniature .product-description { border-color: ' . Configuration::get('RC_PL_ITEM_BORDER') . ' }
                #products .product-description, .featured-products .product-description, .product-accessories .product-description, .product-miniature .product-description {
                    border-top-width: 2px;
                    border-radius: 4px;
                    margin-top: 4px;
                }
                #products #js-product-list .product-item.product_show_list .thumbnail-container .product-description {
                    border-left-width: 2px!important;
                }
                #products #js-product-list .product-item.product_show_list .thumbnail-container .product-image {
                    margin-right: 14px;
                }
                ';
            }
            if (Configuration::get('NC_PL_ITEM_BORDERH')) {
                $css .= '
                #products .thumbnail-container:hover, .featured-products .thumbnail-container:hover, .product-accessories .thumbnail-container:hover, .product-miniature .thumbnail-container:hover 
                {
                    box-shadow: none;
                }
                #products #js-product-list .product-item.product_show_list .thumbnail-container:focus, #products #js-product-list .product-item.product_show_list .thumbnail-container:hover {
                    box-shadow: none;
                }
                #products .thumbnail-container:hover .product-description, .featured-products .thumbnail-container:hover .product-description, .product-accessories .thumbnail-container:hover .product-description, .product-miniature .thumbnail-container:hover .product-description 
                {
                    border-color: ' . Configuration::get('NC_PL_ITEM_BORDERH') . ';
                    box-shadow: 0 20px 22px 0px rgba(0, 0, 0, 0.14);
                }
                ';
            }
        }

        if (Configuration::get('NC_PC_LAYOUTS') == "2") {
            $css .= '
                #products .product-description, .featured-products .product-description, .product-accessories .product-description, .product-miniature .product-description { background: none; border:none }
                #products .product-image, .featured-products .product-image, .product-accessories .product-image, .product-miniature .product-image {
                  padding: 11px;
                  border-radius: 4px 4px 0 0; }
                #products .thumbnail-container, .featured-products .thumbnail-container, .product-accessories .thumbnail-container, .product-miniature .thumbnail-container {
                  border-radius: 4px;
                  border: 2px solid ' . Configuration::get('RC_PL_ITEM_BORDER') . ';
                  background: ' . Configuration::get('RC_PL_ITEM_BG') . ' }
                #products .thumbnail-container:hover, .featured-products .thumbnail-container:hover, .product-accessories .thumbnail-container:hover, .product-miniature .thumbnail-container:hover {
                  border-color: ' . Configuration::get('NC_PL_ITEM_BORDERH') . ';
                }
                #roy_specials_col .product-miniature .thumbnail-container { background:none; border:none; }
                #roy_specials_col .product-miniature .product-image { padding:0; }

                  #products .action-block, .featured-products .action-block, .product-accessories .action-block, .product-miniature .action-block { width:calc(100% - 22px); bottom:3px; }
                  .countcontainer { padding:22px }
                  #products .product-thumbnail, .featured-products .product-thumbnail, .product-accessories .product-thumbnail, .product-miniature .product-thumbnail { border-radius:3px }
                  ';
            if (Configuration::get('NC_PL_ITEM_BORDERH')) {
                $css .= '#products .thumbnail-container:hover .product-image, .featured-products .thumbnail-container:hover .product-image, .product-accessories .thumbnail-container:hover .product-image, .product-miniature .thumbnail-container:hover .product-image { border-color: ' . Configuration::get('NC_PL_ITEM_BORDERH') . ' } ';
            }
        }

        if (Configuration::get('RC_PL_PRODUCT_NAME')) {
            $css .= '#products .product-title a, .featured-products .product-title a, .product-accessories .product-title a, .product-miniature .product-title a { color: ' . Configuration::get('RC_PL_PRODUCT_NAME') . ' }
          ';
        }
        if (Configuration::get('RC_PL_LIST_DESCRIPTION')) {
            $css .= '#products .prod-short-desc, .featured-products .prod-short-desc, .product-accessories .prod-short-desc, .product-miniature .prod-short-desc { color: ' . Configuration::get('RC_PL_LIST_DESCRIPTION') . ' }
          ';
        }
        if (Configuration::get('RC_PL_PRODUCT_PRICE')) {
            $css .= '.cart-grid-right .cart-summary .cart-summary-line .value, .side_menu .cart-total .value-total, .product-line-grid-right .product-price, #products .product-price-and-shipping .price, .featured-products .product-price-and-shipping .price, .product-accessories .product-price-and-shipping .price, .product-miniature .product-price-and-shipping .price { color: ' . Configuration::get('RC_PL_PRODUCT_PRICE') . ' }
          ';
        }
        if (Configuration::get('RC_PL_PRODUCT_OLDPRICE')) {
            $css .= '#products .regular-price, .featured-products .regular-price, .product-accessories .regular-price, .product-miniature .regular-price { color: ' . Configuration::get('RC_PL_PRODUCT_OLDPRICE') . ' }
          ';
        }
        if (Configuration::get('NC_PL_SHADOWS') == 0) {
            $css .= '.thumbnail-container:hover, .thumbnail-container:focus { box-shadow:none!important } ';
        }


        if (Configuration::get('RC_PL_HOVER_BUT')) {
            $css .= '
          #products .action-block .action-btn, .featured-products .action-block .action-btn, .product-accessories .action-block .action-btn, .product-miniature .action-block .action-btn { background-color: ' . Configuration::get('RC_PL_HOVER_BUT_BG') . ' }
          .action-btn i svg * { stroke:' . Configuration::get('RC_PL_HOVER_BUT') . '!important }
          ';
        }

        if (Configuration::get('RC_PL_PRODUCT_NEW_BG')) {
            $css .= '
          .col-image .discount-amount, .col-image .discount-percentage, .col-image .on-sale, .col-image .online-only, .col-image .pack, .col-image .product-flags .new, .product-miniature .discount-amount, .product-miniature .discount-percentage, .product-miniature .on-sale, .product-miniature .online-only, .product-miniature .pack, .product-miniature .product-flags .new {
            background-color: ' . Configuration::get('RC_PL_PRODUCT_NEW_BG') . ' ;
            border-color: ' . Configuration::get('RC_PL_PRODUCT_NEW_BORDER') . ';
            color: ' . Configuration::get('RC_PL_PRODUCT_NEW_COLOR') . ';
          }
          ';
        }
        if (Configuration::get('RC_PL_PRODUCT_SALE_BG')) {
            $css .= '
          .has-discount .discount, .col-image .discount-amount.discount-amount, .col-image .discount-amount.discount-percentage, .col-image .discount-amount.on-sale, .col-image .discount-percentage.discount-amount, .col-image .discount-percentage.discount-percentage, .col-image .discount-percentage.on-sale, .col-image .on-sale.discount-amount, .col-image .on-sale.discount-percentage, .col-image .on-sale.on-sale, .col-image .online-only.discount-amount, .col-image .online-only.discount-percentage, .col-image .online-only.on-sale, .col-image .pack.discount-amount, .col-image .pack.discount-percentage, .col-image .pack.on-sale, .col-image .product-flags .new.discount-amount, .col-image .product-flags .new.discount-percentage, .col-image .product-flags .new.on-sale, .product-miniature .discount-amount.discount-amount, .product-miniature .discount-amount.discount-percentage, .product-miniature .discount-amount.on-sale, .product-miniature .discount-percentage.discount-amount, .product-miniature .discount-percentage.discount-percentage, .product-miniature .discount-percentage.on-sale, .product-miniature .on-sale.discount-amount, .product-miniature .on-sale.discount-percentage, .product-miniature .on-sale.on-sale, .product-miniature .online-only.discount-amount, .product-miniature .online-only.discount-percentage, .product-miniature .online-only.on-sale, .product-miniature .pack.discount-amount, .product-miniature .pack.discount-percentage, .product-miniature .pack.on-sale, .product-miniature .product-flags .new.discount-amount, .product-miniature .product-flags .new.discount-percentage, .product-miniature .product-flags .new.on-sale {
            background-color: ' . Configuration::get('RC_PL_PRODUCT_SALE_BG') . ' ;
            border-color: ' . Configuration::get('RC_PL_PRODUCT_SALE_BORDER') . ';
            color: ' . Configuration::get('RC_PL_PRODUCT_SALE_COLOR') . '
          }
          ';
        }

        if (Configuration::get('NC_SECOND_IMG_S') == "1") {
            $css .= '
            .roy_secondimg {
              display: block; width: 100%; height: 100%; position: absolute; overflow: hidden; top: 0; left: 0; opacity: 0;
              -webkit-transition: all .4s cubic-bezier(.36,.76,0,.88);
              transition: all .4s cubic-bezier(.36,.76,0,.88);
              transform: translateY(8px);
            }
            .thumbnail-container:hover .roy_secondimg {
              opacity:1;
              transform: translateY(0);
            }
            ';
        }

        if (Configuration::get('NC_COLORS_S') == "1") {
            $css .= '#products .variant-links, .featured-products .variant-links, .product-accessories .variant-links, .product-miniature .variant-links { display:block; } ';
        }

        $css .= '
          .variant-links .color:before, .custom-checkbox input[type=checkbox]+span.color:before { box-shadow: inset 0 0 0 8px ' . Configuration::get('RC_PL_ITEM_BG') . ', 0 0 0 3px ' . Configuration::get('RC_PL_ITEM_BG') . '; }
          .variant-links .color:hover:before, .custom-checkbox input[type=checkbox]+span.color:hover:before {
              box-shadow: inset 0 0 0 7px ' . Configuration::get('RC_PL_ITEM_BG') . ', 0 0 0 0 ' . Configuration::get('RC_I_B_FOCUS') . ';
          }

          .side-column .variant-links .color:before, .custom-checkbox input[type=checkbox]+span.color:before { box-shadow: inset 0 0 0 8px ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BG') . ', 0 0 0 3px ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BG') . '; }
          .side-column .variant-links .color:hover:before, .custom-checkbox input[type=checkbox]+span.color:hover:before {
              box-shadow: inset 0 0 0 7px ' . Configuration::get('RC_SIDEBAR_BLOCK_CONTENT_BG') . ', 0 0 0 0 ' . Configuration::get('RC_I_B_FOCUS') . ';
          }
          ';


        if (Configuration::get('RC_PP_REVIEWS_STAROFF')) {
            $css .= '
          .star-content div.star, 
          .comments_note div.star, #productCommentsBlock div.star {
            background-color: ' . Configuration::get('RC_PP_REVIEWS_STAROFF') . ';
          }
          ';
        }
        if (Configuration::get('RC_PP_REVIEWS_STARON')) {
            $css .= '
            .star-content div.star-on,
            .star-content div.star-hover,
            .comments_note div.star.star_on, #productCommentsBlock div.star_hover, #productCommentsBlock div.star.star_on, #new_comment_form div.star_hover, #new_comment_form div.star_on {
            background-color: ' . Configuration::get('RC_PP_REVIEWS_STARON') . ';
          }
          ';
        }


        if (Configuration::get('NC_COUNT_DAYS') == 1) {
            $css .= '
              .countcontainer .county .county-days-wrapper { display:none!important }
              .countcontainer .county .county-hours-wrapper:before { display:none!important }
              .countcontainer .county .county-label-days { display:none!important }
              .countcontainer .county > span { width: 33.3% !important }
              .countcontainer .county .titles > span { width: 33.3% !important }
    			';
        }
        if (Configuration::get('NC_COUNT_BG')) {
            $css .= '
        			.roycountdown:before, .roycountoff:before { background-color: ' . Configuration::get('NC_COUNT_BG') . '!important }
    			';
        }
        if (Configuration::get('NC_COUNT_COLOR')) {
            $css .= '.county-label-days, .county-label-hours, .county-label-minutes, .county-label-seconds { color: ' . Configuration::get('NC_COUNT_COLOR') . '!important }
        			@media(min-width:480px) { .roycountoff { color: ' . Configuration::get('NC_COUNT_COLOR') . '!important } }
    			';
        }
        if (Configuration::get('NC_COUNT_TIME')) {
            $css .= '.county .county-days-wrapper, .county .county-hours-wrapper, .county .county-minutes-wrapper, .county .county-seconds-wrapper { color: ' . Configuration::get('NC_COUNT_TIME') . '!important }
              @media(max-width:479px) { .roycountoff { color: ' . Configuration::get('NC_COUNT_TIME') . '!important } }
    			';
        }
        if (Configuration::get('NC_COUNT_WATCH')) {
            $css .= '
              .sidebar-content .thumbnail-container .count_icon svg *, .product_count_block .countcontainer .count_icon svg * { fill: ' . Configuration::get('NC_COUNT_WATCH') . '!important; stroke: ' . Configuration::get('NC_COUNT_WATCH') . '!important }
              .sidebar-content .thumbnail-container .count_icon, .product_count_block .countcontainer .count_icon { background: ' . Configuration::get('NC_COUNT_WATCH_BG') . ' }
    			';
        }



        // ****************  PRODUCT PAGE styles start

        if (Configuration::get('RC_PP_IMGB') == "1") {
            $css .= '.product-cover img { border:2px solid ' . Configuration::get('RC_PP_IMG_BORDER') . ' }';
            if (Configuration::get('RC_PP_ICON_BORDER')) {
                $css .= '
                .images-container .product-images li.thumb-container:before { box-shadow: inset 0 0 0 2px ' . Configuration::get('RC_PP_ICON_BORDER') . ' }
                .quickview .images-container .product-images li.thumb-container:before { box-shadow: none }
                ';
            }
            if (Configuration::get('RC_PP_ICON_BORDER_HOVER')) {
                $css .= '
                .images-container .product-images li.thumb-container:hover:before { box-shadow: inset 0 0 0 2px ' . Configuration::get('RC_PP_ICON_BORDER_HOVER') . ' }
                .quickview .images-container .product-images li.thumb-container:hover:before { box-shadow: none }
                ';
            }
        }

        if (Configuration::get('NC_MOBADOTSCS')) {
            $css .= '.product-images .owl-dots .owl-dot span { background: ' . Configuration::get('NC_MOBADOTSCS') . ' } ';
        }



        if (Configuration::get('RC_PP_Z')) {
            $css .= '
              .product-cover .layer .zoom-in {
                -webkit-mask-image: url(../images/rt_' . Configuration::get('RC_PP_Z') . '.svg);
                mask-image: url(../images/rt_' . Configuration::get('RC_PP_Z') . '.svg);
              } ';
        }

        if (Configuration::get('RC_PP_ZI')) {
            $css .= '.product-cover .layer .zoom-in {
              background-color: ' . Configuration::get('RC_PP_ZI') . '!important }
              ';
        }
        if (Configuration::get('RC_PP_ZIHBG')) {
            $css .= '.product-cover .layer { background: ' . Configuration::get('RC_PP_ZIHBG') . ' }';
        }


        if (Configuration::get('RC_PP_PRICE_COLOR')) {
            $css .= '.product-price { color: ' . Configuration::get('RC_PP_PRICE_COLOR') . ' } ';
        }
        if (Configuration::get('RC_PP_PRICE_COLORO')) {
            $css .= '.has-discount .product-discount { color: ' . Configuration::get('RC_PP_PRICE_COLORO') . ' } ';
        }


        if (Configuration::get('NC_PP_ADD_BG')) {
            $css .= ' .add .btn.add-to-cart { background-color: ' . Configuration::get('NC_PP_ADD_BG') . '; border-color: ' . Configuration::get('NC_PP_ADD_BORDER') . '; color: ' . Configuration::get('NC_PP_ADD_COLOR') . ' }';
        }

        if (Configuration::get('NC_ATT_RADIOS') == "2") {
            $css .= '
            .radio-label {
                width: auto;
                min-width: 42px;
                padding: 0 10px;
                border-radius: 2px;
             }
             .radio-label:before {
               border-radius: 2px;
             }
             .input-radio:hover+span:before {
               transform:none;
             }

            ';
        }
        if (Configuration::get('NC_OLDPRICE') == "2") {
            $css .= '
            .product-prices div.product-price .regular-price { text-decoration: line-through }
            .product-prices div.product-price .regular-price > span { display: none }
            
            .product-prices div.product-price { display: flex; flex-direction: column }
            .product-prices div.product-price .product-discount { order: 1 }
            .product-prices div.product-price .current-price { order: 2 }

            @media(max-width: 991px) {
            .product-prices div.product-price .product-discount { margin-top: 20px } }
            ';
        }

        if (Configuration::get('RC_PP_ATT_LABEL')) {
            $css .= '
            .product-actions .product-variants-item .control-label:before, .product-actions .product-variants-item.hover .control-label:before { background: ' . Configuration::get('RC_PP_ATT_LABEL') . ' }
            .product-actions .product-variants-item.hover .control-label { color: ' . Configuration::get('RC_PP_ATT_LABEL') . ' }
            ';
        }
        if (Configuration::get('RC_PP_ATT_COLOR_ACTIVE')) {
            $css .= '

            .product-variants .input-radio:hover+span:before { box-shadow: inset 0 0 0 7px ' . Configuration::get('RC_G_BG_CONTENT') . ', 0 0 0 2.5px ' . Configuration::get('RC_PP_ATT_COLOR_ACTIVE') . '; }
            .product-variants .input-radio:checked+span:before { box-shadow: inset 0 0 0 5px ' . Configuration::get('RC_G_BG_CONTENT') . ', 0 0 0 2.5px ' . Configuration::get('RC_PP_ATT_COLOR_ACTIVE') . '; }
            .product-variants .input-color:checked+span:before { box-shadow: inset 0 0 0 5px ' . Configuration::get('RC_G_BG_CONTENT') . ', 0 0 0 2.5px ' . Configuration::get('RC_PP_ATT_COLOR_ACTIVE') . '; }
            .product-variants .color:hover:before, .custom-checkbox input[type=checkbox]+span.color:hover:before { box-shadow: inset 0 0 0 7px ' . Configuration::get('RC_G_BG_CONTENT') . ', 0 0 0 2.5px ' . Configuration::get('RC_PP_ATT_COLOR_ACTIVE') . '; }

            ';
        }


        if (Configuration::get('RC_PP_INFO_LABEL')) {
            $css .= '.product-info label, .social-sharing .share_text span { color: ' . Configuration::get('RC_PP_INFO_LABEL') . ' }';
        }
        if (Configuration::get('RC_PP_INFO_VALUE')) {
            $css .= '
              .product-info a, .product-info span, .social-sharing .share_text:hover span { color: ' . Configuration::get('RC_PP_INFO_VALUE') . ' }
              .social-sharing .share_text svg * { stroke: ' . Configuration::get('RC_PP_INFO_VALUE') . '!important }
              ';
        }


        if (Configuration::get('NC_COUNT_PR_TITLE')) {
            $css .= '
      			.product_count_block .countcontainer .roycounttitle, .product_count_block .countcontainer .roycountoff { color: ' . Configuration::get('NC_COUNT_PR_TITLE') . '!important }
			';
        }
        if (Configuration::get('NC_COUNT_PR_NUMBERS')) {
            $css .= '
      			.product_count_block .county .county-days-wrapper, .product_count_block .county .county-hours-wrapper, .product_count_block .county .county-minutes-wrapper, .product_count_block .county .county-seconds-wrapper { color: ' . Configuration::get('NC_COUNT_PR_NUMBERS') . '!important }
			';
        }
        if (Configuration::get('NC_COUNT_PR_BG')) {
            $css .= '
      			.product_count_block .roycountdown:before, .product_count_block .roycountoff:before { background-color: ' . Configuration::get('NC_COUNT_PR_BG') . '!important }
			';
        }
        if (Configuration::get('NC_COUNT_PR_COLOR')) {
            $css .= '
      			.product_count_block .county-label-days, .product_count_block .county-label-hours, .product_count_block .county-label-minutes, .product_count_block .county-label-seconds { color: ' . Configuration::get('NC_COUNT_PR_COLOR') . '!important }
			';
        }
        if (Configuration::get('NC_COUNT_PR_SEP')) {
            $css .= '
            .product_count_block .roycountdown:before, .product_count_block .roycountoff:before { border: 2px solid ' . Configuration::get('NC_COUNT_PR_SEP') . '!important }
			';
        }



        // ****************  ORDER styles start

        if (Configuration::get('RC_O_ADDS') == "1") {
            $css .= '
            ';
        }

        if (Configuration::get('RC_O_OPTION')) {
            $css .= '
      			.login-tabs li a, body#checkout section.checkout-step .delivery-option, body#checkout section.checkout-step .address-item { border-color: ' . Configuration::get('RC_O_OPTION') . ' }
			';
        }
        if (Configuration::get('RC_O_OPTION_ACTIVE')) {
            $css .= '
      			.login-tabs li a.active, body#checkout section.checkout-step .delivery-option.active, body#checkout section.checkout-step .address-item.selected { border-color: ' . Configuration::get('RC_O_OPTION_ACTIVE') . ' }
			';
        }
        if (Configuration::get('RC_O_INFO_TEXT')) {
            $css .= '
      			#checkout #block-reassurance li .block-reassurance-item span { color: ' . Configuration::get('RC_O_INFO_TEXT') . ' }
			';
        }
        if (Configuration::get('RC_LC_BG')) {
            $css .= '
      			#blockcart-modal .modal-header, .product_add_mini:before { background: ' . Configuration::get('RC_LC_BG') . ' }
			';
        }
        if (Configuration::get('RC_LC_C')) {
            $css .= '
      			#blockcart-modal .modal-title, #blockcart-modal close, #blockcart-modal .modal-title i.material-icons { color: ' . Configuration::get('RC_LC_C') . ' }
			';
        }



        // ****************  FOOTER styles start


        if (Configuration::get('RC_FOOTER_LAY') == "1") {
            $css .= '
            ';
        }
        if (Configuration::get('RC_FOOTER_LAY') == "2") {
            $css .= '
            .block-contact p { margin-top:0 }
            #block-newsletter-label { display:none }
            @media(min-width:992px) {
              .block_newsletter .col-lg-7 { float:right }
              .block_myaccount_infos.links, .footer-container h3 { display:none }
              .footer-container .col-md-5.links {
                width: 66.66667%;
              }
              .footer-container .col-md-5.links .col-md-6.wrapper {
                width: 100%;
                text-align: right;
              }
              .footer-container .links li {
                display: inline;
                margin-right: 6px;
                padding-bottom: 12px;
              }
              .footer-container .links li:after {
                content: ".";
                margin: 0 -2px 0 4px;
                font-size: 16px;
                vertical-align: -1px;
              }
              .footer-container .links li:last-child:after {
                display:none;
              }
            }
            ';
        }
        if (Configuration::get('RC_FOOTER_LAY') == "3") {
            $css .= '
            .block_newsletter { display:none }
            @media(min-width:992px) {
              .block_newsletter .col-lg-7 { float:right }
              .block-social { width:100%; text-align: center; }
              .block_myaccount_infos.links, .footer-container h3 { display:none }
              .footer-container .links:not(.block-contact) {
                display:none;
              }
              .block-contact { width:100%; text-align:center }
            }
            @media(max-width: 991px) {
                .block-contact.links {
                    float: none;
                    width: auto;
                }
                .block-contact.links .hidden-sm-down {
                    display: block!important;
                    text-align: center;
                }
                .block-contact.links .hidden-md-up {
                    display: none;
                }
                .footer-container .row > div:not(.block-contact) {
                    display: none;
                } 
            }                
            ';
        }
        if (Configuration::get('RC_FOOTER_LAY') == "4") {
            $css .= '
            .footer-container { padding:4rem }
            .block_newsletter { display:none }
            .block-social li { margin:0.25rem }
            .row.social { padding:0; }
            .block-social { width:100%; text-align: center; padding:.5rem 0 0; }
            @media(max-width:992px) {
              .block-social { margin-bottom:90px; }
            }
            ';
        }


        if (Configuration::get('RC_FOOTER_BG')) {
            $css .= '.footer-container { background: ' . Configuration::get('RC_FOOTER_BG') . '}
      	';
        }
        if (Configuration::get('RC_FOOTER_TITLES')) {
            $css .= '.blockcms-title, .myaccount-title, .myaccount-title a, .myaccount-title a:visited, .footer-container h3, .block-contact-title { color: ' . Configuration::get('RC_FOOTER_TITLES') . '}
          @media (max-width: 767px) {
          .footer-container .links .h3 { color: ' . Configuration::get('RC_FOOTER_TITLES') . '} }
        ';
        }
        if (Configuration::get('RC_FOOTER_TEXT')) {
            $css .= '
      			.footer-container, .footer-container p, .block_newsletter p#block-newsletter-label { color: ' . Configuration::get('RC_FOOTER_TEXT') . ' }
        ';
        }
        if (Configuration::get('RC_FOOTER_LINK')) {
            $css .= '
      			.footer-container li a, .block-contact a, .block-contact span { color: ' . Configuration::get('RC_FOOTER_LINK') . ' }
            #footer .footer-container a:before { background: ' . Configuration::get('RC_FOOTER_LINK') . ' }
        ';
        }
        if (Configuration::get('RC_FOOTER_LINK_H')) {
            $css .= '
            .footer-container li a:hover, .block-contact a:hover { color: ' . Configuration::get('RC_FOOTER_LINK_H') . ' }
            #footer .footer-container a:hover:before { background: ' . Configuration::get('RC_FOOTER_LINK_H') . ' }
        ';
        }

        if (Configuration::get('RC_FOOTER_NEWS_BG')) {
            $css .= '
            #footer .block_newsletter form input[type=text] { background: ' . Configuration::get('RC_FOOTER_NEWS_BG') . '}
  			';
        }
        if (Configuration::get('RC_FOOTER_NEWS_BORDER')) {
            $css .= '
            #footer .block_newsletter form input[type=text] { border: 2px solid ' . Configuration::get('RC_FOOTER_NEWS_BORDER') . ' }
        ';
        }
        if (Configuration::get('RC_FOOTER_NEWS_COLOR')) {
            $css .= '
            #footer .block_newsletter form input[type=text] { color: ' . Configuration::get('RC_FOOTER_NEWS_COLOR') . '}
        ';
        }
        if (Configuration::get('RC_FOOTER_NEWS_PLACEH')) {
            $css .= '
            #footer .block_newsletter form input[type=text]::-webkit-input-placeholder {
                color: ' . Configuration::get('RC_FOOTER_NEWS_PLACEH') . '!important; }
            #footer .block_newsletter form input[type=text]:-moz-placeholder,
            #footer .block_newsletter form input[type=text]::-moz-placeholder {
                color: ' . Configuration::get('RC_FOOTER_NEWS_PLACEH') . '!important; }
            #footer .block_newsletter form input[type=text]:-ms-input-placeholder {
                color: ' . Configuration::get('RC_FOOTER_NEWS_PLACEH') . '!important; }
            ';
        }
        if (Configuration::get('RC_FOOTER_NEWS_BUTTON')) {
            $css .= '
          .block_newsletter form button.go:before { color: ' . Configuration::get('RC_FOOTER_NEWS_BUTTON') . ' }
        ';
        }


        // ****************  BLOG styles start

        if (Configuration::get('RC_BL_CONT') == "1") {
            $css .= '
            .news_content { background:none!important; border:none!important; padding:0!important; }
            .news_content .sds_post_title_home a { margin-bottom: 10px!important }
            ';
        }
        if (Configuration::get('RC_BL_CONT') == "2") {
            $css .= '
            .news_content { background: ' . Configuration::get('RC_BL_H_BG') . '; border: 2px solid ' . Configuration::get('RC_BL_H_BORDER') . ' }
            .news_content .sds_post_title_home a { margin-bottom: 10px!important }
            ';
        }
        if (Configuration::get('RC_BL_CONT') == "3") {
            $css .= '
            .news_content { background:none!important; border:none!important; padding:4px 4px 0!important; }
            .sds_blog_post { padding:18px!important; background: ' . Configuration::get('RC_BL_H_BG') . '; border: 2px solid ' . Configuration::get('RC_BL_H_BORDER') . '; border-radius:4px; }
            ';
        }

        if (Configuration::get('RC_BL_LAY') == "1") {
            $css .= '
            ';
        }
        if (Configuration::get('RC_BL_LAY') == "2") {
            $css .= '
            .sds_blog_post {
              display: flex;
              align-items: center;
            }
            .news_module_image_holder { max-width:110px; float:left; margin-right:16px; }
            .news_content { padding: 0; }
            .news_content .sds_post_title_home a { font-size:20px; line-height:20px; margin-bottom:10px }
            ';
        }
        if (Configuration::get('RC_BL_LAY') == "2" && Configuration::get('RC_BL_CONT') == "2") {
            $css .= '
            .sds_blog_post { align-items!important: flex-start!important; }
            .news_module_image_holder { margin-right:10px!important; }
            .news_content { padding: 16px 22px!important; }
            ';
        }


        if (Configuration::get('RC_BL_HEAD')) {
            $css .= '
            .roy_blog .products-section-title a { color: ' . Configuration::get('RC_BL_HEAD') . ' }
            ';
        }
        if (Configuration::get('RC_BL_HEAD_HOVER')) {
            $css .= '
            .roy_blog .products-section-title a:hover { color: ' . Configuration::get('RC_BL_HEAD_HOVER') . ' }
            ';
        }

        if (Configuration::get('RC_BL_H_TITLE')) {
            $css .= '
            .sds_post_title_home a { color: ' . Configuration::get('RC_BL_H_TITLE') . ' }
            ';
        }
        if (Configuration::get('RC_BL_H_TITLE_H')) {
            $css .= '
            .sds_post_title_home a:hover { color: ' . Configuration::get('RC_BL_H_TITLE_H') . ' }
            ';
        }

        if (Configuration::get('RC_BL_H_META')) {
            $css .= '
            .news_date span { color: ' . Configuration::get('RC_BL_H_META') . ' }
            ';
        }

        if (Configuration::get('RC_BL_DESC')) {
            $css .= '
            .sdsarticle-des { color: ' . Configuration::get('RC_BL_DESC') . ' }
            ';
        }
        if (Configuration::get('RC_BL_RM_COLOR')) {
            $css .= '
            .sdsreadMore a.r_more { color: ' . Configuration::get('RC_BL_RM_COLOR') . ' }
            ';
        }
        if (Configuration::get('RC_BL_RM_HOVER')) {
            $css .= '
            .sdsreadMore a.r_more:hover { color: ' . Configuration::get('RC_BL_RM_HOVER') . ' }
            ';
        }

        if (Configuration::get('RC_BL_C_ROW') == "3") {
            $css .= '
            #smartblogcat .sdsarticleCat:nth-child(odd) { clear:none }
            @media (min-width: 992px) {
              #smartblogcat .sdsarticleCat { width:33% }
              #smartblogcat .sdsarticleCat:nth-of-type(3n+1) { clear:left; }
            }
            ';
        }
        if (Configuration::get('RC_BL_C_ROW') == "4") {
            $css .= '
            #smartblogcat .sdsarticleCat:nth-child(odd) { clear:none }
            @media (min-width: 992px) {
              #smartblogcat .sdsarticleCat { width:25% }
              #smartblogcat .sdsarticleCat:nth-of-type(4n+1) { clear:left; }
            }
            ';
        }


        if (Configuration::get('NC_STICKY_ADDS') == "1") {
            $css .= '
                @media (max-width:991px) {
                    .product-add-to-cart .product-quantity {
                        position: fixed;
                        z-index: 5;
                        bottom: 0;
                        left: 0;
                        width: 100%;
                        display: flex;
                        padding: 14px 14px;
                        background:' . Configuration::get('RC_G_BG_CONTENT') . ';
                        box-shadow: 0 -10px 20px rgba(0,0,0,0.08);
                    }
                    .product-quantity .qty {
                        margin-right: 8px;
                        margin-bottom: 0;
                    }
                    .product-quantity .qty #quantity_wanted {
                        min-width: 0;
                    }
                    body#product #footer {
                        padding-bottom: 84px;
                    }
                    .product_add_mini {
                        bottom: 100px;
                    }
                }
            ';

            
            if (Configuration::get('NC_HEMOS') == "1" || Configuration::get('NC_HEMOS') == "2") {
                $css .= '
                    @media (max-width:991px) {
                        .product-add-to-cart .product-quantity {
                            bottom: 69px;
                        }
                        .product_add_mini {
                            bottom: 170px;
                        }
                    }
                ';
            }
        }


        // ****************  CSS styles start


        if (Configuration::get('NC_CSS') != "") {
            $css .= Configuration::get('NC_CSS');
        }

        if (Shop::getContext() == Shop::CONTEXT_SHOP)
            $this->context->controller->addCSS(($this->_path) . 'css/rt_customizer_' . (int)$this->context->shop->getContextShopID() . '.css', 'all');
        $myFile = $this->local_path . "css/rt_customizer_" . (int)$this->context->shop->getContextShopID() . ".css";

        $fh = fopen($myFile, 'w') or die("can't open file");
        fwrite($fh, $css);
        fclose($fh);
    }


    public function fontOptions($panel, $panelupper)
    {
        $html = "";
        $html .= '<select id="' . $panel . '" name="' . $panel . '">';

        $fonts = explode(';', $this->gfonts);
        foreach ($fonts as $f) {
            $html .= '<option ' . ((Configuration::get($panelupper) == $f) ? 'selected="selected" ' : '') . 'value="' . $f . '">' . $f . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    public function backgroundOptions($panel, $panelupper)
    {
        $html = "";
        $html .= '
                <div class="roytc_row" style="margin-top:0;">
                        <label>Background image</label>
                        <div class="margin-form" style="margin-top:0;">
                              <input id="' . $panel . '_field" type="file" name="' . $panel . '_field">
                              <input id="' . $panel . '_upload" type="submit" class="button" name="' . $panel . '_upload" value="upload">
                        </div></div>';
        $custom_background_image = Configuration::get('NC_' . $panelupper . '_BG_EXT');
        if ($custom_background_image != "") {

            if (Shop::getContext() == Shop::CONTEXT_SHOP)
                $adv_imgname = $panel . '_background' . '-' . (int)$this->context->shop->getContextShopID();


            $html .= '

                              <label>Background image</label>
                              <div class="margin-form" style="margin-bottom:0;">
                              <img class="imgback" src="' . $this->_path . 'upload/' . $adv_imgname . '.' . $custom_background_image . '" /><br /><br />
                              <input id="' . $panel . '_delete" type="submit" class="button" value="' . $this->l('Delete image') . '" name="' . $panel . '_delete">
                              <p class="clear helpcontent">' . $this->l('If you want to show Patterns or Background Color, delete your background image') . '</p>
                              </div>

                              <div class="roytc_row ds_wrap">
                              <label>Background repeat</label>
                              <div class="margin-form">
                              <input type="radio" class="regular-radio" name="' . $panel . '_bg_repeat" id="' . $panel . '_bg_repeat_0" value="0" ' . ((Configuration::get('NC_' . $panelupper . '_BG_REPEAT') == 0) ? 'checked="checked" ' : '') . '/>
                              <label class="ds b_rp1" for="' . $panel . '_bg_repeat_0"><span>1 . Repeat</span></label>

                              <input type="radio" class="regular-radio" name="' . $panel . '_bg_repeat" id="' . $panel . '_bg_repeat_1" value="1" ' . ((Configuration::get('NC_' . $panelupper . '_BG_REPEAT') == 1) ? 'checked="checked" ' : '') . '/>
                              <label class="ds b_rp2" for="' . $panel . '_bg_repeat_1"><span>2 . Repeat-x</span></label>

                              <input type="radio" class="regular-radio" name="' . $panel . '_bg_repeat" id="' . $panel . '_bg_repeat_2" value="2" ' . ((Configuration::get('NC_' . $panelupper . '_BG_REPEAT') == 2) ? 'checked="checked" ' : '') . '/>
                              <label class="ds b_rp3" for="' . $panel . '_bg_repeat_2"><span>3 . Repeat-y</span></label>

                              <input type="radio" class="regular-radio" name="' . $panel . '_bg_repeat" id="' . $panel . '_bg_repeat_3" value="3" ' . ((Configuration::get('NC_' . $panelupper . '_BG_REPEAT') == 3) ? 'checked="checked" ' : '') . '/>
                              <label class="ds b_rp4" for="' . $panel . '_bg_repeat_3"><span>4 . No-repeat</span></label>
                              </div>
                              </div>

                              <div class="roytc_row ds_wrap">
                              <label>Background position</label>
                              <div class="margin-form">
                              <input type="radio" class="regular-radio" name="' . $panel . '_bg_position" id="' . $panel . '_bg_position_0" value="0" ' . ((Configuration::get('NC_' . $panelupper . '_BG_POSITION') == 0) ? 'checked="checked" ' : '') . '/>
                              <label class="ds align1" for="' . $panel . '_bg_position_0"><span>1 . Left</span></label>

                              <input type="radio" class="regular-radio" name="' . $panel . '_bg_position" id="' . $panel . '_bg_position_1" value="1" ' . ((Configuration::get('NC_' . $panelupper . '_BG_POSITION') == 1) ? 'checked="checked" ' : '') . '/>
                              <label class="ds align2" for="' . $panel . '_bg_position_1"><span>2 . Center</span></label>

                              <input type="radio" class="regular-radio" name="' . $panel . '_bg_position" id="' . $panel . '_bg_position_2" value="2" ' . ((Configuration::get('NC_' . $panelupper . '_BG_POSITION') == 2) ? 'checked="checked" ' : '') . '/>
                              <label class="ds align3" for="' . $panel . '_bg_position_2"><span>3 . Right</span></label>
                              </div>
                              </div>

                              <div class="roytc_row">
                              <label>Fixed background attachment</label>
                              <div class="margin-form">
                              <input type="radio" class="regular-radio" name="' . $panel . '_bg_fixed" id="' . $panel . '_bg_fixed_1" value="1" ' . ((Configuration::get('NC_' . $panelupper . '_BG_FIXED') == 1) ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="' . $panel . '_bg_fixed_1"> Yes</label>
                              <input type="radio" class="regular-radio" name="' . $panel . '_bg_fixed" id="' . $panel . '_bg_fixed_0" value="0" ' . ((Configuration::get('NC_' . $panelupper . '_BG_FIXED') == 0) ? 'checked="checked" ' : '') . '/>
                              <label class="t" for="' . $panel . '_bg_fixed_0"> No</label>
                              </div>
                              </div>
                        ';
        }

        return $html;
    }

    public function hookdisplayBackOfficeHeader($params)
    {
        $this->context->controller->addCSS(($this->_path) . 'css/forall.css');
    }
    
    public function defineRoyThemeVars()
    { 
        
        // Define font vars
        $fontHeadings = Configuration::get('RC_F_HEADINGS');
        $fontButtons = Configuration::get('RC_F_BUTTONS');
        $fontText = Configuration::get('RC_F_TEXT');
        $fontPrice = Configuration::get('RC_F_PRICE');
        $fontPn = Configuration::get('RC_F_PN');

        $ffsupport = '';

        if ((Configuration::get('RC_LATIN_EXT') == 1) || (Configuration::get('RC_CYRILLIC') == 1)) {
            $ffsupport .= '&subset=';
        }

        if ((Configuration::get('RC_LATIN_EXT')) == 1) {
            $ffsupport .= 'latin,latin-ext';
        }
        if ((Configuration::get('RC_LATIN_EXT') == 1) && (Configuration::get('RC_CYRILLIC') == 1)) {
            $ffsupport .= ',';
        }
        if ((Configuration::get('RC_CYRILLIC')) == 1) {
            $ffsupport .= 'cyrillic,cyrillic-ext';
        }

        $font_w = ':wght@400;500;600;700';
        $font_include = '';

        $arr = array($fontHeadings, $fontButtons, $fontText, $fontPrice, $fontPn);
        $filtered = array();

        foreach ($arr as $item) {
            if (!in_array($item, $filtered)) {
                $filtered[] = $item;
            }
        }

        $arr = $filtered;
        $sysFonts = $this->systemFonts;
        $arr = array_filter($arr, function ($v) use ($sysFonts) {
            return !in_array($v, $sysFonts);
        });

        for ($i = 0; $i < count($arr); ++$i) {
            $font = $arr[$i];
            $font = str_replace(' ', '+', $font);
            $font_include .= "<link href='https://fonts.googleapis.com/css2?family=" . $font . $font_w . "&display=swap' rel='stylesheet' type='text/css'>";
        }
        

        // Define customizer vars
        $theme_settings = array(
            'o_add' => (Configuration::get('RC_O_ADDS')),
            'nc_ai' => (Configuration::get('NC_AIS')),
            'nc_i_qv' => (Configuration::get('NC_I_QVS')),
            'nc_i_discover' => (Configuration::get('NC_I_DISCOVERS')),
            'pp_z' => (Configuration::get('RC_PP_Z')),
            'nc_loader_lay' => (Configuration::get('NC_LOADER_LAYS')),
            'nc_loader_logo' => (Configuration::get('NC_LOADER_LOGOS')),
            'nc_loader' => (Configuration::get('NC_LOADERS')),
            'levi_position' => (Configuration::get('RC_LEVI_POSITION')),

            'bl_row' => (Configuration::get('RC_BL_ROW')),
            'brand_per_row' => (Configuration::get('RC_BRAND_PER_ROW')),
            'g_lay' => (Configuration::get('RC_G_LAY')),
            'g_tp' => (Configuration::get('RC_G_TP')),
            'logo_mobile_ext' => (Configuration::get('NC_LOGO_MOBILE')),
            'logo_normal_ext' => (Configuration::get('NC_LOGO_NORMAL')),
            'logo_footer_ext' => (Configuration::get('NC_LOGO_FOOTER')),
            'nc_logo_loader' => (Configuration::get('NC_LOGO_LOADER')),
            'mini_r' => (Configuration::get('RC_MINI_R')),
            'cart_icon' => (Configuration::get('RC_CART_ICON')),
            'search_lay' => (Configuration::get('RC_SEARCH_LAY')),
            'nc_i_search' => (Configuration::get('NC_I_SEARCHS')),
            'header_lay' => (Configuration::get('RC_HEADER_LAY')),
            'header_trah' => (Configuration::get('RC_HEADER_TRAH')),
            'header_trao' => (Configuration::get('RC_HEADER_TRAO')),
            'g_pro_w' => (Configuration::get('RC_G_PRO_W')),
            'g_info_w' => (Configuration::get('RC_G_INFO_W')),
            'g_bra_w' => (Configuration::get('RC_G_BRA_W')),
            'nc_mob_hp' => (Configuration::get('NC_MOB_HP')),
            'nc_carousel_featured' => (Configuration::get('NC_CAROUSEL_FEATUREDS')),
            'nc_auto_featured' => (Configuration::get('NC_AUTO_FEATURED')),
            'nc_items_featured' => (Configuration::get('NC_ITEMS_FEATUREDS')),
            'nc_carousel_best' => (Configuration::get('NC_CAROUSEL_BEST')),
            'nc_auto_best' => (Configuration::get('NC_AUTO_BEST')),
            'nc_items_best' => (Configuration::get('NC_ITEMS_BESTS')),
            'nc_carousel_new' => (Configuration::get('NC_CAROUSEL_NEW')),
            'nc_auto_new' => (Configuration::get('NC_AUTO_NEW')),
            'nc_items_new' => (Configuration::get('NC_ITEMS_NEWS')),
            'nc_carousel_sale' => (Configuration::get('NC_CAROUSEL_SALE')),
            'nc_auto_sale' => (Configuration::get('NC_AUTO_SALE')),
            'nc_items_sale' => (Configuration::get('NC_ITEMS_SALES')),
            'nc_carousel_custom1' => (Configuration::get('NC_CAROUSEL_CUSTOM1')),
            'nc_auto_custom1' => (Configuration::get('NC_AUTO_CUSTOM1')),
            'nc_items_custom1' => (Configuration::get('NC_ITEMS_CUSTOM1S')),
            'nc_carousel_custom2' => (Configuration::get('NC_CAROUSEL_CUSTOM2')),
            'nc_auto_custom2' => (Configuration::get('NC_AUTO_CUSTOM2')),
            'nc_items_custom2' => (Configuration::get('NC_ITEMS_CUSTOM2S')),
            'nc_carousel_custom3' => (Configuration::get('NC_CAROUSEL_CUSTOM3')),
            'nc_auto_custom3' => (Configuration::get('NC_AUTO_CUSTOM3')),
            'nc_items_custom3' => (Configuration::get('NC_ITEMS_CUSTOM3S')),
            'nc_carousel_custom4' => (Configuration::get('NC_CAROUSEL_CUSTOM4')),
            'nc_auto_custom4' => (Configuration::get('NC_AUTO_CUSTOM4')),
            'nc_items_custom4' => (Configuration::get('NC_ITEMS_CUSTOM4')),
            'nc_carousel_custom5' => (Configuration::get('NC_CAROUSEL_CUSTOM5')),
            'nc_auto_custom5' => (Configuration::get('NC_AUTO_CUSTOM5')),
            'nc_items_custom5' => (Configuration::get('NC_ITEMS_CUSTOM5')),
            'breadcrumb' => (Configuration::get('RC_BREADCRUMB')),
            'pp_display_print' => (Configuration::get('RC_PP_DISPLAY_PRINT')),

            'nc_pl_shadow' => (Configuration::get('NC_PL_SHADOWS')),
            'nc_show_q' => (Configuration::get('NC_SHOW_QW')),
            'nc_show_s' => (Configuration::get('NC_SHOW_SW')),
            'nc_pc_layout' => (Configuration::get('NC_PC_LAYOUTS')),

            'nc_hemo' => (Configuration::get('NC_HEMOS')),
            'nc_cat' => (Configuration::get('NC_CAT_S')),
            'nc_cat_title' => (Configuration::get('NC_CAT_TITLES')),
            'nc_subcat' => (Configuration::get('NC_SUBCAT_S')),
            'nc_second_img' => (Configuration::get('NC_SECOND_IMG_S')),
            'nc_colors' => (Configuration::get('NC_COLORS_S')),
            'pp_display_refer' => (Configuration::get('RC_PP_DISPLAY_REFER')),
            'pp_display_cond' => (Configuration::get('RC_PP_DISPLAY_COND')),
            'pp_display_brand' => (Configuration::get('RC_PP_DISPLAY_BRAND')),
            'pp_display_q' => (Configuration::get('RC_PP_DISPLAY_Q')),
            'pp_reviews_display_top' => (Configuration::get('RC_PP_REVIEWS_DISPLAY_TOP')),
            'footer_lay' => (Configuration::get('RC_FOOTER_LAY')),
            'footer_copyright_display' => (Configuration::get('RC_FOOTER_COPYRIGHT_DISPLAY')),

            'nc_mobadots' => (Configuration::get('NC_MOBADOTSS')),
            'nc_pp_qq3' => (Configuration::get('NC_PP_QQ3S')),
            'font_include' => $font_include
        );

        $this->context->smarty->assign('roythemes', $theme_settings);
    }

    public function hookActionProductSearchAfter($params)
    {
        $this->defineRoyThemeVars();
    }

    function hookHeader($params)
    {

        global $smarty;

        if (
            isset($_SERVER['HTTP_USER_AGENT']) &&
            (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
        )
            header('X-UA-Compatible: IE=edge,chrome=1');

        $this->defineRoyThemeVars();

        if (Shop::getContext() == Shop::CONTEXT_SHOP)
            $this->context->controller->addCSS(($this->_path) . 'css/rt_customizer_' . (int)$this->context->shop->getContextShopID() . '.css', 'all');
    }
}
