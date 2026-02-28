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
class LGCookiesLawPubliLG extends LGCookiesLawPubli
{
    public static $modules = [
        'lgcookieslaw' => [
            'name' => [
                'en' => 'EU Cookie Law GDPR (Banner + Blocker) – Update 2024 – Prestashop Modul',
                'es' => 'Módulo Ley de Cookies RGPD (Aviso + Bloqueador) – Nuevo 2024',
                'fr' => 'Module Loi Cookies RGPD (Avis + Bloqueur) – Mise à jour 2024 – Prestashop',
            ],
            'description' => [
                'en' => 'Updated module to fully comply with the regulations regarding cookies of 2024. It allows you to block cookies until you obtain the consent of the user who accesses your website. And the possibility of selecting the type of cookies to install between functional and third-party.',
                'es' => 'Módulo actualizado para cumplir plenamente con la normativa referente a cookies de 2024. Te permite bloquear las cookies hasta obtener el consentimiento del usuario que accede a tu web. Y la posibilidad de seleccionar el tipo de cookies a instalar entre funcionales y de terceros.',
                'fr' => 'Module mis à jour pour se conformer pleinement à la réglementation en matière de cookies de 2024. Il vous permet de bloquer les cookies jusqu’à ce que vous obteniez le consentement de l’utilisateur qui accède à votre site Web. Et la possibilité de sélectionner le type de cookies à installer entre fonctionnel et tiers.',
            ],
            'url' => [
                'en' => 'https://www.lineagrafica.es/en/modulos-prestashop/eu-cookie-law-banner-blocker-prestashop-module/',
                'es' => 'https://www.lineagrafica.es/modulos-prestashop/modulo-prestashop-ley-de-cookies-aviso-bloqueador/',
                'fr' => 'https://www.lineagrafica.es/fr/modulos-prestashop/module-loi-europeenne-sur-les-cookies-bandeau-bloqueur-prestashop/',
            ],
            'rating' => 4.5,
        ],
        'lgcomments' => [
            'name' => [
                'en' => 'Store Reviews + Product Reviews + Google Rich Snippets – Prestashop Module',
                'es' => 'Módulo Prestashop Opiniones Tienda + Opiniones Productos + Rich Snippets',
                'fr' => 'Module Avis Boutique + Avis Produits + Google Rich Snippets',
            ],
            'description' => [
                'en' => 'Get your own system of verified reviews about your store and products (comments and ratings) and Google Rich Snippets.',
                'es' => 'Implementa tu propio sistema de opiniones verificadas en tu tienda y productos (comentarios y valoraciones) y Google Rich Snippets.',
                'fr' => 'Mettez en place votre propre système d\'avis vérifiés pour votre boutique et produits (commentaires et notes) et Google Rich Snippets.',
            ],
            'url' => [
                'en' => 'https://www.lineagrafica.es/en/modulos-prestashop/prestashop-module-verified-store-product-reviews/',
                'es' => 'https://www.lineagrafica.es/modulos-prestashop/modulo-prestashop-opiniones-verificadas-tienda-productos/',
                'fr' => 'https://www.lineagrafica.es/fr/modulos-prestashop/module-avis-clients-boutique-produits-prestashop/',
            ],
            'rating' => 4,
        ],
        'lgconsultas' => [
            'name' => [
                'en' => 'Frequently Asked Questions about Products – FAQ – Prestashop Module',
                'es' => 'Módulo Prestashop Consultas sobre Productos – FAQ',
                'fr' => 'Module Foire aux Questions sur les Produits – FAQ – Prestashop',
            ],
            'description' => [
                'en' => 'Add a FAQ directly to your product sheets. Each product has its own questions and answers (like on Amazon). Receive and answer your customer inquiries and display the questions/answers on the product sheets. Questions about Products.',
                'es' => 'Añade un FAQ directamente a tus fichas de producto. Cada producto tiene sus propias preguntas / respuestas (como en Amazon). Permite recibir y contestar a las consultas y mostrarlas en las fichas de productos. Consultas sobre Productos.',
                'fr' => 'Ajoutez une FAQ directement sur vos fiches produits. Chaque produit a ses propres questions / réponses (comme sur Amazon). Recevez et répondez aux questions de vos clients et affichez-les sur les fiches produits. Questions sur les Produits.',
            ],
            'url' => [
                'en' => 'https://www.lineagrafica.es/en/modulos-prestashop/prestashop-module-questions-about-products-faq/',
                'es' => 'https://www.lineagrafica.es/modulos-prestashop/modulo-prestashop-consultas-sobre-productos-faq/',
                'fr' => 'https://www.lineagrafica.es/fr/modulos-prestashop/module-prestashop-questions-sur-produits-faq/',
            ],
            'rating' => 4.5,
        ],
        'lgseoredirect' => [
            'name' => [
                'en' => 'URL Redirects 301, 302, 303 and 404 – SEO Module',
                'es' => 'Módulo Prestashop Redirecciones 301, 302, 303 de URLs y 404 – SEO',
                'fr' => 'Module Redirections 301, 302, 303 des URLs et 404 - SEO',
            ],
            'description' => [
                'en' => 'Create an unlimited number of 301, 302 and 303 URL redirects to optimize the SEO of your website and avoid the 404 errors. Also includes a CSV importer to create redirects in bulk.',
                'es' => 'Crea un número ilimitado de redirecciones (redirección 301, 302 y 303) para optimizar el SEO de tu tienda y eliminar los errores 404. Incluye también un importador de CSV para crear redireccionamiento de forma masiva.',
                'fr' => 'Créez un nombre illimité de redirections 301, 302 et 303 pour optimiser le référencement SEO de votre boutique et supprimer les erreurs 404. Inclut également un importateur de CSV pour créer des redirections en masse.',
            ],
            'url' => [
                'en' => 'https://www.lineagrafica.es/en/modulos-prestashop/301-302-303-url-redirects-seo-prestashop-module/',
                'es' => 'https://www.lineagrafica.es/modulos-prestashop/modulo-prestashop-redirecciones-301-302-303-de-urls/',
                'fr' => 'https://www.lineagrafica.es/fr/modulos-prestashop/module-redirections-301-302-303-des-urls-et-404-seo-prestashop/',
            ],
            'rating' => 5,
        ],
        'lgsitemaps' => [
            'name' => [
                'en' => 'Multilingual and Multistore Sitemap Pro – SEO Module',
                'es' => 'Módulo Sitemaps Pro Multi-Idiomas y Multi-Tiendas – SEO',
                'fr' => 'Module Sitemaps Pro Multilingues et Multi-Boutiques - SEO',
            ],
            'description' => [
                'en' => 'Generate sitemaps of all the urls of your store automatically (cron), in all the languages of your store (multilingual), for all your stores (multistore) and including product, categories and manufacturer images.',
                'es' => 'Genera sitemaps para todas las URLs de tu tienda automáticamente (cron), en todos los idiomas de tu tienda, para todas las tiendas (multi-tiendas) e incluyendo las imágenes de producto, categoría y fabricante.',
                'fr' => 'Générez des sitemaps de toutes les URLs de votre boutique, automatiquement (cron), dans toutes les langues de votre site, pour toutes vos boutiques (multi-boutique) et en incluant les images du produit, catégorie et fabricant.',
            ],
            'url' => [
                'en' => 'https://www.lineagrafica.es/en/modulos-prestashop/prestashop-module-multilingual-sitemap-generator/',
                'es' => 'https://www.lineagrafica.es/modulos-prestashop/modulo-prestashop-generador-multi-idioma-sitemaps/',
                'fr' => 'https://www.lineagrafica.es/fr/modulos-prestashop/module-prestashop-generateur-multilingue-sitemaps/',
            ],
            'rating' => 5,
        ],
        'lgcanonicalurls' => [
            'name' => [
                'en' => 'Canonical SEO URLs + Google Hreflang Pro – Prestashop Module',
                'es' => 'Módulo Prestashop Canonical SEO URLs + Google Hreflang Pro',
                'fr' => 'Module URLs Canoniques SEO + Google Hreflang Pro – Prestashop',
            ],
            'description' => [
                'en' => 'Add canonical tags to your homepage, product, category, CMS, manufacturer and supplier pages in order to avoid duplicate content and improve your SEO.',
                'es' => 'Añade etiquetas canónicas a tu homepage y páginas de productos, categorías, CMS, fabricantes y proveedores para evitar contenido duplicado y mejorar tu posicionamiento SEO.',
                'fr' => 'Ajoutez des balises canoniques à votre page d\'accueil et à vos pages de produits, catégories, CMS, fabricants et fournisseurs pour éviter les doublons de contenu et améliorer votre positionnement SEO',
            ],
            'url' => [
                'en' => 'https://www.lineagrafica.es/en/modulos-prestashop/canonical-urls-to-avoid-duplicate-content-prestashop-module/',
                'es' => 'https://www.lineagrafica.es/modulos-prestashop/modulo-prestashop-urls-canonicas-evitar-contenido-duplicado/',
                'fr' => 'https://www.lineagrafica.es/fr/modulos-prestashop/module-urls-canoniques-pour-eviter-le-contenu-duplique-prestashop/',
            ],
            'rating' => 5,
        ],
        'lgpaypal' => [
            'name' => [
                'en' => 'Prestashop Module – PayPal with fee or with discount',
                'es' => 'Módulo Prestashop – PayPal con recargo o con descuento',
                'fr' => 'Module Prestashop – PayPal avec surcharge ou avec remise',
            ],
            'description' => [
                'en' => 'Allows you to add the Paypal payment method to your online shop with fees and/or discounts. In addition, you can create as many payment configurations as you wish, differentiating by country, customer group or cart amount.',
                'es' => 'Permite añadir el método de pago de Paypal en tu tienda online con recargo y/o descuentos. Además, puedes crear tantas configuraciones de pago como desees diferenciando por países, grupos de clientes o importe del carrito.',
                'fr' => 'Permet d’ajouter le mode de paiement Paypal à votre boutique en ligne avec des surcharges et/ou des remises. En outre, vous pouvez créer autant de configurations de paiement que vous le souhaitez, en les différenciant par pays, groupe de clients ou montant du panier.',
            ],
            'url' => [
                'en' => 'https://www.lineagrafica.es/en/modulos-prestashop/prestashop-module-paypal-with-fee-or-with-discount/',
                'es' => 'https://www.lineagrafica.es/modulos-prestashop/modulo-prestashop-paypal-con-recargo-o-con-descuento/',
                'fr' => 'https://www.lineagrafica.es/fr/modulos-prestashop/module-prestashop-paypal-avec-surcharge-ou-avec-remise/',
            ],
            'rating' => 5,
        ],
        'lgexpertreviews' => [
            'name' => [
                'en' => 'Prestashop Module – Expert reviews on product sheets (E-A-T)',
                'es' => 'Módulo Prestashop – Opiniones de expertos en fichas de producto (E-A-T)',
                'fr' => 'Module Prestashop – Opinions d’experts sur les pages produits (E-A-T)',
            ],
            'description' => [
                'en' => 'Include expert reviews about your products on the product page of your shop. Thanks to this you can improve the E-A-T of your shop. Do you know what it is? E-A-T are the initials of Expertise, Authoritativeness and Trustworthiness.',
                'es' => 'Incluye en la ficha de producto de tu tienda opiniones de expertos sobre tus productos. Gracias a esto podrás mejorar el E-A-T de tu tienda. ¿Sabes qué es? E-A-T son las siglas en español de Experiencia, Autoridad y Confiabilidad.',
                'fr' => 'Incluez dans la page produit de votre e-commerce l´opinions d’experts sur vos produits. Grâce à cela, vous pouvez améliorer l’E-A-T de votre boutique en ligne. Savez-vous ce que c’est? E-A-T signifie Expérience, Autorité et Fiabilité.',
            ],
            'url' => [
                'en' => 'https://www.lineagrafica.es/en/modulos-prestashop/prestashop-module-expert-reviews-on-product-sheets-e-a-t/',
                'es' => 'https://www.lineagrafica.es/modulos-prestashop/opiniones-de-expertos-en-fichas-de-producto-e-a-t/',
                'fr' => 'https://www.lineagrafica.es/fr/modulos-prestashop/avis-experts-pages-produits/',
            ],
            'rating' => 5,
        ],
        'lgwhatsapp' => [
            'name' => [
                'en' => 'Prestashop Module – Whatsapp Chat and Orders',
                'es' => 'Módulo Prestashop – WhatsApp Chat y Pedidos',
                'fr' => 'Chat et commandes WhatsApp – Prestashop Module',
            ],
            'description' => [
                'en' => 'Allows you to create a chat that allows your customers to communicate with you on WhatsApp.',
                'es' => 'Permite crear un chat que permite a sus clientes comunicarse con usted en WhatsApp.',
                'fr' => 'Permet de créer un chat qui permet à vos clients de communiquer avec vous sur WhatsApp.',
            ],
            'url' => [
                'en' => 'https://www.lineagrafica.es/en/modulos-prestashop/prestashop-module-whatsapp-chat-and-orders/',
                'es' => 'https://www.lineagrafica.es/modulos-prestashop/whatsapp-chat-y-pedidos/',
                'fr' => 'https://www.lineagrafica.es/fr/modulos-prestashop/chat-et-commandes-whatsapp-prestashop-module/',
            ],
            'rating' => 5,
        ],
    ];
}
