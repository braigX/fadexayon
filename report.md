# Rapport d’avancement - Module `idxrcustomproduct`

Date : 2026-03-03

## P1 - Bloquant / Critique

| # | Sujet | Correctif appliqué | État |
|---|---|---|---|
| 1 | Commandes envoyées sans dimensions | Renforcement de la sauvegarde des données de personnalisation (snapshots + extra) et corrections côté JS/serveur pour éviter la perte des champs texte/nombre. | Fait |
| 2 | Validation globale avant panier/checkout | Ajout de contrôles front avant envoi (champs requis selon le cas), synchronisation finale avant submit, et protection contre les envois incomplets. | Fait |
| 2.1 | Normalisation numérique `12,5 -> 12.5` | Conversion automatique des virgules en points sur les inputs concernés. | Fait |
| 2.2 | Prix par trou | Ajout d’un prix fixe perçages (`holes_fixed_price`) côté configuration, transmis au front et intégré au total. | Fait |
| 3 | Affichage diamètre/rayon | Harmonisation des libellés et du rendu des dimensions (diamètre/rayons) dans l’aperçu SVG. | Fait |

## P2 - Fonctionnalités métier

| # | Sujet | Correctif appliqué | État |
|---|---|---|---|
| 4 | Dashboard de pricing admin | Création d’un onglet `Pricing` avec configuration des prix fixes + grille dynamique par épaisseur (AJAX CRUD). | Fait |
| 5 | Prix par forme | Base de tarification par forme présente et raccordée aux calculs existants. | Fait |
| 6 | Prix découpe linéaire | Prise en charge du tarif de découpe selon les règles de calcul du module. | Fait |
| 7 | Prix finition linéaire | Prise en charge des tarifs collage/polissage selon la configuration. | Fait |
| 8 | Grille par épaisseur | Nouvelle table `idxrcustomproduct_thickness_rates` + gestion active/inactive + sélection de l’épaisseur la plus proche (exacte, sinon supérieure, sinon inférieure). | Fait |
| 9 | SVG CAO 1:1 | Export SVG revu : sauvegarde de l’échelle active, export temporaire en échelle 1, restauration de l’échelle d’origine. | Fait |

## P3 - Expérience client

| # | Sujet | Correctif appliqué | État |
|---|---|---|---|
| 10 | Sauvegarder ma personnalisation | Boutons Save/Restore, modales, loaders, stockage serveur par client/produit, miniatures SVG. | Fait |
| 11 | Liste des simulations (compte client) | Page `Mes simulations` avec lister, renommer, dupliquer, supprimer, aperçu. | Fait |
| 12 | Restaurer une simulation dans l’éditeur | Restauration via page simulations + paramètre URL (`idxr_restore_sim`) + relance de la logique de sélection. | Fait |

## P4 - Performance front-office

| # | Sujet | Correctif appliqué | État |
|---|---|---|---|
| 13 | Optimisation avancée | Audit et recommandations (coverage/Lighthouse), nettoyage cache JS/CSS, ajustements de cache-busting en dev quand CCC JS est désactivé. | Etrain de devlopment de nv module |
