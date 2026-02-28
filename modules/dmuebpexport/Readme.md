# DMU EBP Export (par [Dream me up](http://www.dream-me-up.fr))
```
   .--.
   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
        w w w . d r e a m - m e - u p . f r       '

  @author    Dream me up <prestashop@dream-me-up.fr>
  @copyright 2007 - 2016 Dream me up
  @license   All Rights Reserved

```
## changelog 3.1.2

* Correction sur le compte de TVA des "autres taxes"

## changelog 3.1.1

* Correction d'un bug dans le fichier de mise à jour 3.0.6

## changelog 3.1.0

* Mise aux normes validation PrestaShop Addons

## changelog 3.0.7

* Correction de la prise en compte des réductions par le HT au lieu du TTC.
* Correction sur la prise en compte des bons de réduction "livraison gratuite"

## changelog 3.0.6

* Changement des libellée pour les champs des règles de taxes
* Ajout des nouvelles normes de sécurité des modules
* Correction du traitement des arrondis

## changelog 3.0.5

* Gestion de la TVA des autres options (écotaxe, emballage...)
* Correction d'un cas spécifique sur la TVA du Transporteur

## changelog 3.0.4

* Ajout des traductions ES + IT
* Prise en charge des changements de l'id de TaxRulesGroup

## changelog 3.0.3

* Correction de la restriction sur l'export des avoirs par boutique

## changelog 3.0.2

* Correction d'un bug sur le hook hookDisplayAdminProductsExtra

## changelog 3.0.1

* Affichage de toutes les taxes et règles de taxes pour la configuration de la TVA
* Ajout de la possibilité d'afficher une ligne par mode de paiement
* Ventilation de la TVA sur les réductions directement sur la TVA produit

## changelog 3.0.0

* Compatibilité Prestashop 8

## changelog 2.8.5

* Correction de l'export si des comptes ont été configurés pour chaque mode de paiement

## changelog 2.8.4

* Correction d'un bug sur les comptes de produits avec TVA

## changelog 2.8.2

* Désactivation du mode debug

## changelog 2.8.1

* Correction d'un problème de compatibilité sur les numéros de comptes clients sous 1.7.6 et plus

## changelog 2.8.0

* Mise en place d'une configuration différente en fonction des taux de TVA des pays des règles de taxes

## changelog 2.7.1

* Correction sur la config des modes de paiement au cas ou il y a des crochets

## changelog 2.7.0

* Ajout de la possibilité de personnaliser le séparateur pour l'export CSV
* Configuration supplémentaire pour choisir si l'on souhaite exporter les factures et avoirs à zero euros

## changelog 2.6.2

* Ajout de journal dans l'export CSV
* Séparation de la société et du client dans 2 colonnes distinctes

## changelog 2.6.1

* Correction d'un bug sur la taxe des réductions
* Ajout de la possibilité de choisir l'encodage du fichier d'export

## changelog 2.6.0

* Il est désormais possible de choisir si l'on veut conserver les accents et les caractères spéciaux
* Correction d'un bug lors du remplacement des accents

## changelog 2.5.1

* Correction d'un problème de compatibilité avec le help en dessous des version 1.6.0.6

## changelog 2.5.0

* Correction d'un bug sur le numéro de compte comptable TTC en ventilation simple
* Retrait de tous les caractères spéciaux qui peuvent provoquer des problèmes d'import / exports avec différents logiciels

## changelog 2.4.2

* Correction d'un problème d'enregistrement des numéros de compte en ventilation avancée sur prestashop 1.7

## changelog 2.4.1

* Correction d'un problème, les comptes de TVA personnalisés n'étaient pas bien pris en compte

## changelog 2.4.0

* Correction d'un bug en ventilation avancée pour la récupération des avoirs
* Correction d'un bug pour le montant total des factures
* Correction d'un problème de prise en compte du montant transporteur dans les factures
* Correction d'un problème sur les réductions des factures

## changelog 2.3.0

* Refonte de l'export pour gérer les écritures qui ne sont pas équilibrées

## changelog 2.2.3

* Prise en compte des avoirs seulement concernant les commandes valides

## changelog 2.2.2

* Résolution du problème d'Avoir avec les remboursements partiels

## changelog 2.2.1

* Modification car : Si la commande utilise la TVA, pas forcement le transporteur.

## changelog 2.2.0

* Ajout de la possibilité de définir le code comptable client par client

## changelog 2.1.9

* Modification de la gestion des dates de facture (date de facturation)

## changelog 2.1.8

* Ajout des IDs de transaction côté export comptable

## changelog 2.1.7

* Vérification du numéro de facture pour pallier à certaines erreurs d'autres modules !

## changelog 2.1.6

* Ajout de DARTY aux marketPlaces

## changelog 2.1.5

* Sortie seulement des factures ayant une commande valide, pour éviter par exemple les commandes « Remboursées »

## changelog 2.1.4

* Modification de la gestion des dates de facture

## changelog 2.1.3

* Retro-compatiblité approximative pour 1.5.x.x 

## changelog 2.1.2

* Suppression des réductions « frais de port » dans la liste des réductions

## changelog 2.1.1

* Ajout du type de paiement à l'export comptable

## changelog 2.1.0

* Ajout de la devise

## changelog 2.0.12

* Ajout du nom de la société au nom du client

## changelog 2.0.11

* Correction de bug
* Correction UTF8 sur les pays

## changelog 2.0.10

* Correction de bug

## changelog 2.0.9

* Modification de la gestion des dates de facture
* Réduction du poids des images

## changelog 2.0.8

* Problème d'encodage UTF-8

## changelog 2.0.7

* Fix bug si produit supprimé

## changelog 2.0.6

* Compatibilité PrestaShop 1.7.x.x

## changelog 2.0.5

* Correction mode de paiement marketplace tiers (Fnac, Cdiscount, Priceminister, etc...)

## changelog 2.0.4

* Correction bug encodage Excel <> Openoffice
* Affichage ligne transporteur uniquement si supérieur à 0
* Correction formulaire export (bug attribut action)
* Correction montant TVA sur produits avec prix specifique

## changelog 2.0.3

* Correction bug fonction iniexistante prestashop 1.6.0.9 et anterieur

## changelog 2.0.2

* Correction bug concernant les tax produit modifies apres commande
* Ajout de controle pour les numeros de compte
* Traduction EN des entetes des colonnes fichier

## changelog 2.0.1

* Correction mise en page page de configuration (multiboutique)
* Correction pour les numéros compte transporteur par défaut (export EBP)
* Prise en compte des transporteurs inactifs

## changelog 2.0.0 : Upgrade prestashop 1.6.1

* Suppression des tests pour la rétrocompatilité (allegement du code)
* Restructuration du code : approche MVC
* Restructuration de l'arborescence, nouveaux dossiers : classes/controllers/translations/views
* Modification des licences sur les fichiers php et tpl
* La page d'administration hérite de la classe ModuleAdminController
* Utilisation du HelperForm pour la page configuration et la page d'administration
* Correction concernant la configuration des taxes ayant le même taux (ex: TVA FR 20%, USt. AT 20%, km EE 20%,...)
* Nouvelle règle d'arrondi : arrondi à l'inférieur à 2 décimales près.
* Correction pour les produits personnalisés + prix spécifiques

## changelog 1.2.8

* Correction pour les produits personnalisés

## changelog 1.2.7

* Correction pour exporter les factures avec un montant à 0

## changelog 1.2.6

* Correction pour les traductions
* Correction pour les avoirs
* Correction pour le multiboutique

## changelog 1.2.5

* Correction de l'affichage des comptes clients par mode de paiement
* Correction du numéro de compte des avoirs pour utiliser le compte client de la facture d'origine

## changelog 1.2.4

* Correction de l'exportation pour les versions avant Prestashop 1.4.4

## changelog 1.2.3

* Correction avec l'affichage du nom des clients pour les avoirs
* Correction pour l'affichage des avoirs partiels
* Correction de la TVA qui ne correspondait pas à celle des commandes
* Ajout de la personnalisation des comptes vente de produits HT par TVA dans la configuration
* Correction pour Prestashop 1.6

## changelog 1.2.2

* Ajout de la personnalisation des comptes par mode de paiement dans la configuration

## changelog 1.2.1

* correction du bug avec les réductions
* correction du bug de modification de la configuration du module
* correction du problème d'affichage de l'infobulle dans la configuration du module
* Ajout d'un champ pour changer le journal dans la configuration du module
