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
class LGCookiesLawPubliPS extends LGCookiesLawPubli
{
    public static $modules = [
        'lgcookieslaw' => [
            'name' => [
                'en' => 'EU Cookie Law GDPR (Banner + Blocker) Module',
                'es' => 'Módulo Ley de Cookies RGPD (Aviso + Bloqueador)',
                'fr' => 'Module Loi Cookies RGPD (Avis + Bloqueur)',
            ],
            'description' => [
                'en' => 'This module allows you to fully comply with the GDPR/LOPD Cookies Section. Display a responsive and custom warning banner and disable cookies when users enter your website until you obtain their consent.',
                'es' => 'Módulo actualizado para cumplir plenamente con la normativa referente a cookies de 2024. Permite bloquear las cookies hasta obtener el consentimiento del usuario y seleccionar el tipo de cookies a instalar entre funcionales y de terceros.',
                'fr' => 'Module mis à jour pour se conformer pleinement à la réglementation en matière de cookies de 2024. Il vous permet de bloquer les cookies jusqu’à ce que vous obteniez le consentement de l’utilisateur qui accède à votre site Web.',
            ],
            'url' => [
                'en' => 'https://addons.prestashop.com/en/legal/8734-eu-cookie-law-gdpr-banner-blocker.html',
                'es' => 'https://addons.prestashop.com/es/marco-legal-ley-europea/8734-ley-de-cookies-rgpd-aviso-bloqueador.html',
                'fr' => 'https://addons.prestashop.com/fr/legislation/8734-loi-cookies-rgpd-avis-bloqueur.html',
            ],
            'rating' => 4.5,
        ],
        'lgcomments' => [
            'name' => [
                'en' => 'Store Reviews + Product Reviews + Google Rich Snippets Module',
                'es' => 'Módulo Opiniones Tienda + Opiniones Productos + Rich Snippets',
                'fr' => 'Module Avis Boutique + Avis Produits + Google Rich Snippets',
            ],
            'description' => [
                'en' => 'Get your own system of verified reviews about your store and products (comments and ratings) and Google Rich Snippets.',
                'es' => 'Implementa tu propio sistema de opiniones verificadas en tu tienda y productos (comentarios y valoraciones) y Google Rich Snippets.',
                'fr' => 'Mettez en place votre propre système d\'avis vérifiés pour votre boutique et produits (commentaires et notes) et Google Rich Snippets.',
            ],
            'url' => [
                'en' => 'https://addons.prestashop.com/en/customer-reviews/17896-store-reviews-product-reviews-google-rich-snippets.html',
                'es' => 'https://addons.prestashop.com/es/comentarios-clientes/17896-opiniones-tienda-opiniones-productos-rich-snippets.html',
                'fr' => 'https://addons.prestashop.com/fr/avis-clients/17896-avis-boutique-avis-produits-google-rich-snippets.html',
            ],
            'rating' => 4,
        ],
        'lgconsultas' => [
            'name' => [
                'en' => 'FAQ Product Sheets - Frequently asked questions Module',
                'es' => 'Módulo FAQ Fichas de Productos - Preguntas frecuentes',
                'fr' => 'Module FAQ Fiches Produits - Foire aux questions fréquentes',
            ],
            'description' => [
                'en' => 'Add a FAQ directly to your product sheets. Each product has its own questions and answers (like on Amazon). Receive and answer your customer inquiries and display the questions/answers on the product sheets. Questions about Products.',
                'es' => 'Añade un FAQ directamente a tus fichas de producto. Cada producto tiene sus propias preguntas / respuestas (como en Amazon). Permite recibir y contestar a las consultas y mostrarlas en las fichas de productos. Consultas sobre Productos.',
                'fr' => 'Ajoutez une FAQ directement sur vos fiches produits. Chaque produit a ses propres questions / réponses (comme sur Amazon). Recevez et répondez aux questions de vos clients et affichez-les sur les fiches produits. Questions sur les Produits.',
            ],
            'url' => [
                'en' => 'https://addons.prestashop.com/en/faq-frequently-asked-questions/18002-faq-product-sheets-frequently-asked-questions.html',
                'es' => 'https://addons.prestashop.com/es/preguntas-frecuentes/18002-faq-fichas-de-productos-preguntas-frecuentes.html',
                'fr' => 'https://addons.prestashop.com/fr/faq-questions-frequentes/18002-faq-fiches-produits-foire-aux-questions-frequentes.html',
            ],
            'rating' => 4.5,
        ],
        'lgseoredirect' => [
            'name' => [
                'en' => 'URL Redirects 301, 302, 303 and 404 – SEO Module',
                'es' => 'Módulo Redirecciones 301, 302, 303 de URLs y 404 - SEO',
                'fr' => 'Module Redirections 301, 302, 303 des URLs et 404 - SEO',
            ],
            'description' => [
                'en' => 'Create an unlimited number of 301, 302 and 303 URL redirects to optimize the SEO of your website and avoid the 404 errors. Also includes a CSV importer to create redirects in bulk.',
                'es' => 'Crea un número ilimitado de redirecciones (redirección 301, 302 y 303) para optimizar el SEO de tu tienda y eliminar los errores 404. Incluye también un importador de CSV para crear redireccionamiento de forma masiva.',
                'fr' => 'Créez un nombre illimité de redirections 301, 302 et 303 pour optimiser le référencement SEO de votre boutique et supprimer les erreurs 404. Inclut également un importateur de CSV pour créer des redirections en masse.',
            ],
            'url' => [
                'en' => 'https://addons.prestashop.com/en/url-redirects/11399-url-redirects-301-302-303-and-404-seo.html',
                'es' => 'https://addons.prestashop.com/es/url-redirecciones/11399-redirecciones-301-302-303-de-urls-y-404-seo.html',
                'fr' => 'https://addons.prestashop.com/fr/url-redirections/11399-redirections-301-302-303-des-urls-et-404-seo.html',
            ],
            'rating' => 5,
        ],
        'lgsitemaps' => [
            'name' => [
                'en' => 'Multilingual and Multistore Sitemap Pro – SEO Module',
                'es' => 'Módulo Sitemaps Pro Multi-Idiomas y Multi-Tiendas - SEO',
                'fr' => 'Module Sitemaps Pro Multilingues et Multi-Boutiques - SEO',
            ],
            'description' => [
                'en' => 'Generate sitemaps of all the urls of your store automatically (cron), in all the languages of your store (multilingual), for all your stores (multistore) and including product, categories and manufacturer images.',
                'es' => 'Genera sitemaps para todas las URLs de tu tienda automáticamente (cron), en todos los idiomas de tu tienda, para todas las tiendas (multi-tiendas) e incluyendo las imágenes de producto, categoría y fabricante.',
                'fr' => 'Générez des sitemaps de toutes les URLs de votre boutique, automatiquement (cron), dans toutes les langues de votre site, pour toutes vos boutiques (multi-boutique) et en incluant les images du produit, catégorie et fabricant.',
            ],
            'url' => [
                'en' => 'https://addons.prestashop.com/en/seo-natural-search-engine-optimization/7507-multilingual-and-multistore-sitemap-pro-seo.html',
                'es' => 'https://addons.prestashop.com/es/seo-posicionamiento-buscadores/7507-sitemaps-pro-multi-idiomas-y-multi-tiendas-seo.html',
                'fr' => 'https://addons.prestashop.com/fr/seo-referencement-naturel/7507-sitemaps-pro-multilingues-et-multi-boutiques-seo.html',
            ],
            'rating' => 5,
        ],
        'lgcanonicalurls' => [
            'name' => [
                'en' => 'Canonical SEO URLs + Google Hreflang Pro Module',
                'es' => 'Módulo Canonical SEO URLs + Google Hreflang Pro',
                'fr' => 'Module URLs Canoniques SEO + Google Hreflang Pro',
            ],
            'description' => [
                'en' => 'Add canonical tags to your homepage, product, category, CMS, manufacturer and supplier pages in order to avoid duplicate content and improve your SEO.',
                'es' => 'Añade etiquetas canónicas a tu homepage y páginas de productos, categorías, CMS, fabricantes y proveedores para evitar contenido duplicado y mejorar tu posicionamiento SEO.',
                'fr' => 'Ajoutez des balises canoniques à votre page d\'accueil et à vos pages de produits, catégories, CMS, fabricants et fournisseurs pour éviter les doublons de contenu et améliorer votre positionnement SEO',
            ],
            'url' => [
                'en' => 'https://addons.prestashop.com/en/url-redirects/21749-canonical-seo-urls-google-hreflang-pro.html',
                'es' => 'https://addons.prestashop.com/es/url-redirecciones/21749-canonical-seo-urls-google-hreflang-pro.html',
                'fr' => 'https://addons.prestashop.com/fr/url-redirections/21749-urls-canoniques-seo-google-hreflang-pro.html',
            ],
            'rating' => 5,
        ],
        'lgpaypal' => [
            'name' => [
                'en' => 'PayPal with fee or with discount Module',
                'es' => 'Módulo PayPal con recargo o con descuento',
                'fr' => 'Module PayPal avec surcharge ou avec remise',
            ],
            'description' => [
                'en' => 'Allows you to add the Paypal payment method to your online shop with fees and/or discounts. In addition, you can create as many payment configurations as you wish, differentiating by country, customer group or cart amount.',
                'es' => 'Permite añadir el método de pago de Paypal en tu tienda online con recargo y/o descuentos. Además, puedes crear tantas configuraciones de pago como desees diferenciando por paises, grupos de clientes o importe del carrito.',
                'fr' => 'Permet d’ajouter le mode de paiement Paypal à votre boutique en ligne avec des surcharges ou des remises. En outre, vous pouvez créer autant de configurations de paiement que vous le souhaitez, en les différenciant par pays, groupe de clients, etc.',
            ],
            'url' => [
                'en' => 'https://addons.prestashop.com/en/payment-card-wallet/89315-paypal-with-fee-or-with-discount.html',
                'es' => 'https://addons.prestashop.com/es/pago-tarjeta-carteras-digitales/89315-paypal-con-recargo-o-con-descuento.html',
                'fr' => 'https://addons.prestashop.com/fr/paiement-carte-wallet/89315-paypal-avec-surcharge-ou-avec-remise.html',
            ],
            'rating' => 5,
        ],
        'lgexpertreviews' => [
            'name' => [
                'en' => 'Expert reviews on product sheets (E-A-T) Module',
                'es' => 'Módulo Prestashop – Opiniones de expertos en fichas de producto (E-A-T)',
                'fr' => 'Module Opinions d’experts sur les pages produits (E-A-T)',
            ],
            'description' => [
                'en' => 'Include expert opinions about your products on the product page of your shop. Thanks to this you can improve the E-A-T of your shop. Do you know what it is? E-A-T are the initials of Expertise, Authoritativeness and Trustworthiness.',
                'es' => 'Incluye en la ficha de productos de tu tienda opiniones de expertos sobre tus productos. Gracias a esto podrás mejorar el E-A-T de su tienda. ¿Sabes qué es? E-A-T son las siglas en español de Experiencia, Autoridad y Confiabilidad.',
                'fr' => 'Incluez dans la page produit de votre e-commerce l´opinions d’experts sur vos produits. Grâce à cela, vous pouvez améliorer l’E-A-T de votre boutique en ligne. Savez-vous ce que c’est? E-A-T signifie Expérience, Autorité et Fiabilité.',
            ],
            'url' => [
                'en' => 'https://addons.prestashop.com/en/customer-reviews/88895-expert-reviews-on-product-sheets-e-a-t.html',
                'es' => 'https://addons.prestashop.com/es/comentarios-clientes/88895-opiniones-de-expertos-en-fichas-de-producto-e-a-t.html',
                'fr' => 'https://addons.prestashop.com/fr/avis-clients/88895-opinions-dexperts-sur-les-pages-produits-e-a-t.html',
            ],
            'rating' => 5,
        ],
        'lgwhatsapp' => [
            'name' => [
                'en' => 'WhatsApp Chat and Orders Module',
                'es' => 'Módulo WhatsApp Chat y Pedidos',
                'fr' => 'Module Chat et commandes WhatsApp',
            ],
            'description' => [
                'en' => 'Allows you to create a chat that allows your customers to communicate with you on WhatsApp.',
                'es' => 'Permite crear un chat que permite a sus clientes comunicarse con usted en WhatsApp.',
                'fr' => 'Permet de créer un chat qui permet à vos clients de communiquer avec vous sur WhatsApp.',
            ],
            'url' => [
                'en' => 'https://addons.prestashop.com/en/share-buttons-comments/85838-whatsapp-chat-and-orders.html',
                'es' => 'https://addons.prestashop.com/es/compartir-contenidos-comentarios/85838-whatsapp-chat-y-pedidos.html',
                'fr' => 'https://addons.prestashop.com/fr/boutons-partage-commentaires/85838-chat-et-commandes-whatsapp.html',
            ],
            'rating' => 5,
        ],
    ];
}
