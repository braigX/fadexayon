




# CHANGELOG




## 1.0.1 2018-11-20

- `#6660` Suppression des surcharges et sélection du contexte pour une commande TNT (BOOM-4050 et BOOM-5821).
- `#6737` [BO] Affichage du 1er numéro de suivi dans le champ "Numéro de suivi" et dans une nouvelle colonne dans la liste des colis.
- `#6738` Amélioration de la gestion du cache (erreurs si le cache est mal installé comme l'extension APCu).
- `#6700` Ajout d'une constante pour le statut permettant de déclencher la création d'expédition.
- `#6739` [BO] Désactivation des checkbox pour la sélection des boutiques dans les transporteurs TNT de Prestashop.




## 1.0.2 2018-11-21

- `#6874` Poids maximum d'expédition pour DEPOT à 30kg pour ajouter un colis.
- `#6876` :
  - Prévenir les exceptions de BDD si les tables ne sont pas créées à l'installation.
  - Fix sélecteur du format des BT (inversion avec et sans logo).
  - Le champ expéditeur société est limité à 32 caractères au lieu de 128 dans la configuration.
  - SOAP/cURL : Vérification du certificat serveur via le fichier cacert.pem de Prestashop.
- `#6892` MAJ de la documentation et amélioration de la compatibilité pour PS 1.7.5.0.




## 1.0.3 2019-01-10

- `#6073` Actualisation automatique du lien de la preuve de livraison sur le détail d'une commande.
- `#5984` [BO] Actualisation automatique du statut de suivis des colis (nouvelle colonne sur la liste des colis).
- `#5984` Actualisation automatique du statut livré de la commande sur la page de commande et via action groupée sur la liste.
- `#6898` Statuts de commande optionnels pour la création d'expédition et la livraison des colis (via constantes).
- `#6913` Ajouter un lien de suivi de l'ensemble des colis pour une commande, disponible pour l'envoi de l'email dès le statut expédiée.
  - {tntofficiel_tracking_url_text} et {tntofficiel_tracking_url_html}
- `#6958` :
  - Amélioration de la création des transporteurs, pour les langues installées sur la boutique (champs delay).
  - Fix sur la méthode isPaymentReady (une erreur de typage).
  - Fix AdminTNTOfficielCarrierController->viewAccess (signature incomplète et méthode inutilisée) (compatibilité PHP7.2).
  - Fix Restauration du timeout de connection de sockets SOAP.
- `#7334` Configuration tarifaire : Impossible d'ajouter plus de 10 tranches. Le maximum est maintenant de 128 tranches.
- `#7335` Configuration tarifaire : Aide à la saisie avec auto-correction des virgules (,) en point (.), vérification directe du format des nombres et arrondis automatique.
- Ajout de l'Offre Essentiel.
- Suppression des fonts icon inutilisée.
- Prévenir les conflits PHP pour les pdf.




## 1.0.4 2019-05-27

- `#7493` Fix date de ramassage sans affichage de l'erreur si elle est invalide (Action groupée, détail d'un commande).
- `#7498` Amélioration du pdf manifeste.




## 1.0.5 2019-06-20

- `#7587` Amélioration de la protection contre le downgrade.
- `#7425` Amélioration du suivi des colis.
- `#7631` Amélioration du suivi des colis (suite).
- `#7592` Mise en place des services P* (18h).




## 1.0.6 2019-11-27

- `#7653` Amélioration de la compatibilité avec les thèmes.
- `#7699` Fix : Pas de transporteurs affichés pour la destination 78882 ST QUENTIN EN YVELINES CEDEX.
- `#7650` [BO] TNECO-117 Actualisation du couple CP/Ville.
- `#7842` Trim sur le code postal, ville, nom de société, etc.
- `#7863` Suppression des avertissements avec chmod.
- `#7864` [BO] Fix CSS sur les informations complémentaires.
- `#7865` Fix appels avec addJS et addCSS.
- `#8136` MAJ URL ZDA et prise en charge de l'absence du certificat Prestashop pour les requêtes cURL.
- `#8127` Ajout des services ASSU et RP ASSU.




## 1.0.7 2019-12-23

- `#8197` Week-end du calendrier non-sélectionnable pour la date de ramassage.
- `#8390` Prise en compte de la livraison offerte globalement à partir de la configuration Prestashop.
- `#8350` [EVOL] Optimisation avec cache+ttl en BDD pour les appels au Webservice.
- `#8394` Prévenir les appels inutiles au webservice le week-end ou si la date est passée (pickupdate, shippingdate).
- `#8395` Gestion des dépendances d'appels au webservice lors d'erreurs de communication.
- `#8396` Optimisation des timeouts des requêtes pour limiter l'effet gel si le webservice est en surcharge.
  - 'Error Fetching http headers', 'Service Temporarily Unavailable', 'Internal Server Error'.




## 1.0.8 2020-03-26

- `#8479` Message d'information sur les commandes associées à un transporteur qui n'est plus disponible sur le compte.
- `#8480` Ne pas journaliser d'erreurs si un autre module interroge le hook pour les variables d'e-mails avec une commande non TNT.
- `#8481` Fix date de ramassage par défaut le week-end pour le ramassage régulier.
- `#8570` [LOG] Vérou Exclusif pour la journalisation.
- `#8389` [LOG] Limiter la journalisation en taille occupée (64 Mib par dossier).
- `#8401` [EVOL] Prix et date prévisionnelle de livraison en fonction du point de livraison.
- `#8630` [BO] Ajout et Edition d'adresse: Rendre accessible la vérification du code postal ville lorsque la maintenance est activée.
- `#8638` Optimisation pour la montée en charge.
- `#8639` Optimisation Mémoïsation getPSCart.
- `#7866` [BO] Détail de commande: Popin de correction de la ville et avertissement sur la compatibilité B2B/B2C du transporteur avec l'adresse.




## 1.0.9 2020-06-11

- `#8763` Mise en cache de la faisabilité les jours fériés.
- `#8781` Refactoring de la gestion des exceptions.
- `#8782` Exclure certaines mise en cache possible mais inutile.
- `#8783` Ne pas faire de vérification directe du code postal/ville sur le formulaire d'édition/ajout d'adresse dans le tunnel de commande.
- `#8402` [EVOL] Confirmation de sauvegarde de la configuration du compte sur les boutiques d'un groupe.




## 1.0.10 2020-10-12

- `#8970` Compatibilité de la librairie PDF avec les futures versions de PHP 7.
- `#9049` Fix date de fermeture >= 15h.
- `#9069` Style et largeur de champs dans la configuration du compte (small screen).
- `#9068` Fix: Vérifier la validité des objets avant une propriété.
- [LOG] Journalisation de la désinstallation dans une méthode séparée.
- Gel du statut des colis 2 jours après la livraison.
- `#8403` [EVOL] Sélection d'un statut déclenchant la création d'expédition et d'un second optionnel à appliquer ensuite.
- `#8407` [EVOL] Actualisation automatique du statut de livraison des colis.
- `#8818` [EVOL] Gestion des statuts de livraison des colis.
- `#8405` [EVOL] Rappel du taux de TVA.
- `#8406` [EVOL] Rappel des départements concernés dans la zone tarifaire 1 ou 2.
- `#8404` [EVOL] Assurance valeur déclarée.
- `#9100` Prévenir la mise en cache si plus de 65535 octets.
- `#9095` v1.0.10 validator.
- `#9110` Installation : Message d'erreur explicite indiquant la version minimum requise de Prestashop.
- `#9112` Fix variable globale smarty $link.




## 1.0.11 2020-11-09

- `#9113` Supporter plus de 10 marqueurs pour la carte des points de livraison.
- `#9206` Validateur 1.0.11.




## 1.0.12 2021-06-01

- `#9042` [BO] Compatibilité des commandes sous PS1.7.7.X (hook ActionGetAdminOrderButtons, displayAdminOrderSide, js, css).
- `#9228` [FO] Fix PS1.7.7.X validation des informations complémentaires.
- `#9236` [FO] Fix PS1.7.7.X affichage des informations complémentaires.
- `#9238` [BO] Fix PS1.7.7.X nouveau lien sur la liste des commandes TNT.
- `#9237` [BO] Fix PS1.7.7.X action groupée pour modifier le statut en simple boutique.
- `#9240` [BO] Fix PS1.7.7.X Validation directe du code postal et de la ville sur le détail d'une commande.
- `#9239` [BO] Erreur de validation du code postal/ville sur le nouveau formulaire d'adresse.
- `#9501` Exception MySQL possible lors de la recherche si aucune adresse pour un client.
- `#9529` Deprecated : FrontController addJS(), addCSS().
- `#9568` [LOG] Journalisation de la suppression des transporteurs TNT inutilisés.
- `#8847` Message remplaçant l'erreur account number is not registered.
- `#9231` Validation Pestashop 1.7.7.0.
- `#9341` Générer une `1.0.12`.
- `#9661` Fonctions serialize/unserialize interdites.
- `#9662` PHP sans HTML (use smarty).
- `#9614` isContextReady avec vérification d'installation des tables.




## 1.0.13 2023-06-26

- `#9041` Test de compatibilité du module avec PS 1.7.6.7.
- `#9668` Ramassage occasionnel : Message d'erreur sur la date de ramassage plus explicite.
- `#9687` MAJ de la documentation.
- `#9691` Nettoyage, Formatage et Coding Style.
- `#9693` Création du transporteur - Sélectionner uniquement la Zone correspondante au pays FR.
- `#9704` Indiquer Aucune taxe s'il n'y a pas de groupe de taxe associé au transporteur.
- `#9748` Recherche pour l'autocompletion des informations complémentaires (téléphone) uniquement avec des adresses du même pays.
- `#9749` Vérification du code pays ISO FR existant (supprimer en BDD).
- `#9777` Ne pas afficher les boutons pour obtenir le BT si la commande n'est pas associé à un transporteur TNT.
- `#9824` [BO] Fix CSS affichage des messages d'erreur ou de confirmation.
- `#9859` [LOG] Désactiver le bouton de téléchargement de la journalisation si absence de l'extension zip.
- `#9868` [LOG] Fix téléchargement de l'archive de journalisation vide.
- `#9876` Valider et générer une `1.0.13`.
- `#9872` Accepter les numéros mobiles étendus (ex : 07009999999999).
- `#9874` isReady avec vérification des extensions requises.
- `#9900` Fix PNG Alpha détection avec appel différé.
- `#9918` Ne pas indiquer le nombre de commerçants partenaires.
- `#9941` Optimisation static getPSCarrierByID pour les commandes et paniers.
- `#9942` Optimisation static getPSShopByID et getPSShopGroupByID pour les comptes.
- `#9956` Optimisation static getPSAddressByID et getPSCustomerByID pour les informations destinataires.
- `#9960` Désactiver le bouton BT si données en BDD indisponibles.
- `#9961` Fix CSS alignement des horaires.
- `#9964` Fix pagination PDF Manifeste et simplification (2 classes supprimées).
- `#9975` [LOG] Journalisation des requêtes SQL.
- `#9981` Filtrage des propriétés pays (variable JS).
- `#10056` Compatibilité avec le module sendinblue.
- `#10067` Contrôles supplémentaires sur les retours SOAP et le cache (log explicite).
- `#10070` Entretien historique versionning.
- `#10102` [BO] PS 1.7.8.X - Commande - Fix calcul du poids total des colis.
- `#10103` PS 1.7.8.X - Fix génération du pdf manifeste.
- `#10119` Compatibilité avec le module CDiscount.
- `#10120` Fix association transporteur boutique.
- `#10196` Fix label delay description.
- `#10246` TNTOfficiel_Ready() Execution Javascript même en cas d'erreur sur une callback jQuery ready.
- `#10266` Gestion de l'exception looks like we got no XML document.
- `#10286` Désactiver et prévenir l'exécution en cas de migration sur PS1.X.
- `#10287` Longueurs maximales des champs du model Address.
- `#10293` Heure d'actualisation du cache.
- `#10348` Date de ramassage non figée après création du BT.
- `#10358` [BO] Prévenir le double échappement HTML des messages d'alerte.
- `#10437` hookActionOrderHistoryAddAfter chargement pour commande TNT uniquement (prévenir une exception).
- `#10453` Fix mapping des statuts de colis.
- `#10552` Masquer la carte si pas de clé API.
- `#10559` Méthode getTaxInfos avec pays explicite.
- `#10560` Renommage de 2 méthodes du model receiver.
- `#10565` Utilisation du code pays réel avec citiesGuide.
- `#10566` Fix méthode statique TNTOfficielReceiver.
- `#10569` [BO] Fix protection des informations complémentaires.
- `#10574` Fix auto-sélection de la date de ramassage à J+3 en type régulier.
- `#10584` Optimisation static getPSCartByID et getPSOrderByID pour les paniers et commandes.
- `#10590` Fallback pour déterminer le nom de contrôleur.
- `#10593` Ajout de méthodes pour obtenir les objets Prestashop.
- `#10596` Amélioration de la gestion de la propagation des événements.
- `#10597` Implémentation du namespace tntofficiel sur les événements du module.
- `#10602` Enlever les fonctions événements dépréciées.
- `#10603` [BO] Avertissement de non sauvegarde des nouvelles lignes de zone tarifaire.
- `#10605` [BO] Aide à la saisie du poids des colis.
- `#10608` Supprimer la variable JS globale isCarrierListDisplay.
- `#10609` Ajout de la fonction TNTOfficiel_getCheckoutTNTRadio.
- `#10610` Fix du lien de la documentation et URL de base de boutique.
- `#10613` Amélioration de isPaymentReady.
- `#10618` Compatibilité avec le module "One page Checkout" par ETS-Soft.
- `#10624` Supprimer l'utilisation de _PS_JS_DIR_.
- `#10629` Fix pays des points de livraison.
- `#10631` Ajout de getURLDomain() et getURIBase().
- `#10632` Fix addJS() et addCSS() pour les URI virtuelles de boutiques.
- `#10635` Référence commande customisée.
- `#10637` Supprimer le cache Symfony après l'installation du module.
- `#10640` Indiquer les boutiques du paramètrage de compte.
- `#10641` Ajout CONTRIBUTING.md.
- `#10647` Fix de la méthode de liaison d'événement de déchargement de page.
- `#10648` Ajout de TNTOfficiel_hasInputChange().
- `#10651` Fix lien et passage en HTTPS.
- `#10653` Indiquer les boutiques pour la création des services de livraison.
- `#10671` Prevenir des requêtes SQL inutiles pour le chargement de transporteurs TNT.
- `#10672` Ne pas créer de ligne de commande TNT vide.
- `#10681` isPaymentReady - Contrôler le panier uniquement si la selection est un transporteur TNT.
- `#10685` isPaymentReady - Contrôler le panier avec adresse de livraison comme optionnel.
- `#10686` getDeliveryPoint en fonction d'un transporteur spécifié.
- `#10694` getURIBase avec chemin physique optionnel.
- `#10695` Amélioration de getAdminMessage().
- `#10724` Utilisation du code erreur dans le cookie.
- `#10735` MAJ - Supprimer les fichiers inutilisés.
- `#10737` Avertissement de colis trop lourd en cas de changement du transporteur.
- `#10738` Inclure le fichier des ZDA par défaut.
- `#10740` [BO] Order - Compatibilité des templates avec les thèmes Bootstrap.
- `#10742` [BO] Aide à la saisie du montant d'assurance des colis.
- `#10744` Décorréler la méthode updateParcel().
- `#10745` [BO] Order - Déduplictation Javascript.
- `#10746` Fusionner les requêtes AJAX des colis.
- `#10747` [BO] Order - Limiter la création de colis à 30 pour une commande.
- `#10748` Déterminer le poids et le montant d'assurance total des colis.
- `#10750` fonction JS globale TNTOfficiel_getCodeTranslate.
- `#10751` Traduction des classes.
- `#10752` [BO] protection des colis en lecture seule si le bon de transport est créé.
- `#10754` PHP Remplacer les opérateurs 'and' et 'or' (précédence).
- `#10760` Détail des erreurs de requêtes AJAX.
- `#10761` [BO] Order - Erreur 500 lors de la sélection du point de livraison.
- `#10762` [LOG] Journalisation SQL du type slowlog.
- `#10763` [BO] Filtrer les actions.
- `#10765` Ajout TNTOfficiel_Reload.
- `#10770` Limiter le nom et prénom de l'expéditeur pour la demande de ramassage.
- `#10776` Limiter le nom et prénom sur le formulaire expéditeur.
- `#10777` Remplacer Tools::jsonDecode et Tools::jsonEncode.
- `#10782` Méthode AdminControllerCore->l() dépréciée.
- `#10783` Méthode Module::isInstalled() dépréciée.
- `#10784` Ajouter de nouvelles traductions.
- `#10785` Remplacer les ocurrences TNTOfficiel.translate.
- `#10786` Supprimer les éléments dépréciés restant.
- `#10815` Supporter la plupart des unités de poids (kg/g/mg/lb/oz).
- `#10818` Amélioration de TNTOfficiel_getCheckoutTNTRadio.
- `#10819` Fix classname tntofficiel-delivery-option.
- `#10820` [FO] Erreur lors de la sélection du point de livraison.
- `#10822` Amélioration de la méthode d'ouverture de la Popin.
- `#10823` Affichage du point de livraison sans rafraichissement de page.
- `#10824` Ajout de la méthode TNTOfficiel_getCheckoutTNTCarrierID().
- `#10825` Ajout de la methode TNTOfficiel_isCheckoutTNTCarrierDisplay().
- `#10826` Fix updateExtraDataDisplay() en fonction de l'ID transporteur.
- `#10853` [BO] Erreur creation de commande sans les informations complémentaires.
- `#10870` jQuery.isArray déprécié dans jQuery 3.2.
- `#10871` Déprécié dans jQuery 3.3. Ajout TNTOfficiel_isType() et TNTOfficiel_bind().
- `#10873` Remplacer certaines méthodes jQuery parents(), parent() par closest().
- `#10905` Trim sur les informations complémentaires. Ajout TNTOfficiel_trim().
- `#10940` Fix auto du panier si option avec adresse de livraison incohérente.
- `#10984` [BO] Fix message du formulaire de création du transporteur.
- `#10985` Fix validation des formulaires.
- `#10992` Méthode pour effacer les étapes du checkout.
- `#11006` AJAX Fix Erreur Réseau (connectivité).
- `#11014` [BO] Order List - filter_key explicite pour les colonnes.
- `#11084` Avertissement si Fancybox non disponible.
- `#11100` [LOG] Journalisation - Prise en compte de l'IP client via proxy.
- `#11118` Gestion Erreur de communication serveur GenericJDBCException.
- `#11145` Gestion Erreur de communication serveur ConstraintViolationException.
- `#11146` FO Panier - Fix Notice Template pour ReceiverInfo.
- `#11150` Séparation des logs SQL.
- `#11165` AJAX Fix Erreur Redirection.
- `#11173` Rapport de communication WS.
- `#11260` [BO] Ajout d'une page de status.
- `#11269` Détection des surcharges du modules.
- `#11284` Détection des surcharges du thème.
- `#11285` Ajout de la méthode TNTOfficiel_Tools::searchFiles().
- `#11316` [FO] Compatibilité avec les thèmes dont l'option de livraison est masquée.
- `#11348` getURIBase() avec option BaseURI du context de boutique ou non.
- `#11349` [BO] Fix addJS() et addCSS() avec __PS_BASE_URI__.
- `#11350` [FO] Ajout de getURLFrontBase() pour l'URL de boutique et de getPathImage() + getURLModulePath().
- `#11351` Ajout de getDirModule() pour obtenir le chemin de dossier absolu.
- `#11355` [FO] Supporter les URLs avec addJS() et addCSS().
- `#11358` Ajout de getFolderBase().
- `#11360` Méthodes statique pour les URL, URI, Dir, Path, Folder.
- `#11361` [FO] Fix emplacement des marqueurs pour les cartes.
- `#11362` Migration - Rennomage des transporteurs sans configuration TNT.
- `#11396` [LOG] Amélioration de la journalisation.
- `#11398` [BO] Transporteurs - Afficher le poids maximum d'un colis.
- `#11405` [BO] Indiquer si la zone par défaut est utilisée.
- `#11422` [FO] Dump de l'état du panier et des options de livraison.
- `#11423` [LOG] Journalisation - Ajout de constantes pour le style d'écriture.
- `#11428` [BO] Trim Paramétrage du compte marchand.
- `#11431` Surcharge du prix du transporteur.
- `#11434` Refactoring chemin template.
- `#11438` Fix addOrderStateHistory sans statut courant.
- `#11454` Poids minimum d'un colis à la création de commande.
- `#11458` [BO] Enlever l'option ZDA pour DROPOFFPOINT et DEPOT.
- `#11478` Fix B64URLInflate padding.
- `#11479` [BO] Etat des tansporteurs TNT.
- `#11514` TNTOfficiel::isContextReady() sans vérification du contrôleur pour le paiement (pagenotfound).
- `#11515` Méthodes de désinscription de gestionnaires d'erreur et d'exception.
- `#11519` Journaliser le changement de transporteur TNT lors de la création de commande.
- `#11481` MAJ du délai slowlog MySQL SELECT.
- `#11524` Amélioration de la gestion des erreurs SOAP.
- `#11362` Migration - Rennomage des transporteurs sans configuration TNT.
- `#11567` Prévenir la création de compte pour un transporteur invalide.
- `#11568` Gestion Erreur de communication 'Cannot open connection'.
- `#11580` Fix disponibilité des services de transporteur en cas d'erreur de communication.
- `#11595` Ajout de la validation du fixe dans le formulaire des informations complémentaires.
- `#11607` Ajout de la recherche du numéro de fixe (Auto en BO).
- `#11619` Fix Prise en compte des options sans adresses (ID:O) pour la vérification des incohérences.
- `#11620` [LOG] Debug - Journalisation lors de l'arrêt forcé.
- `#11649` getContextInfo pour obtenir les informations du contexte (Controller, Employee and Customer).
- `#11650` getScriptInfo pour obtenir les informations de script (location, module, admin, front).
- `#11652` getZipArchiveError pour obtenir l'erreur ZipArchive.
- `#11653` Refactor AdminTNTOrders en AdminTNTOfficielOrders.
- `#11654` Refactor AdminAccountSetting en AdminTNTOfficielAccount.
- `#11655` Refactor AdminCarrierSetting en AdminTNTOfficielCarrier.
- `#11660` Fix TNTOfficiel static load.
- `#11661` Compat PS8 getVersionMin getVersionMax.
- `#11662` cURL Obtenir le message de la verification de pair SSL.
- `#11670` [FO] Ne pas créer de receiver en BDD pour une livraison qui n'est pas en France.
- `#11673` getCountryISO pour déduplication.
- `#11680` Méthode pour obtenir le dernier bundle de certificats racine.
- `#11681` Fallback - Gestion des alternatives pour obtenir le fichier des certificats racine (App, PHP ou OS).
- `#11682` Fix SOAP pour permettre la communication si aucune liste de certificat n'est disponible.
- `#11698` License headers are not up to date.




## 1.0.14 2023-07-31

- `#9275` Les messages d'erreurs ne s'affichent plus sur le détail d'une commande.
- `#9276` Double paiement au changement de statut lors de la création d'expédition.
- `#11701` Processsus de MAJ 1.0.14.
- `#11717` MAJ lib FPDI parser 1.3.1 to 1.5.2.
- `#11718` MAJ lib FPDI template et context.
- `#11719` MAJ lib FPDI filters. Ajout FilterASCIIHexDecode.
- `#11720` Customisation pour fusion en mémoire des pages PDF.
- `#11721` Fix Notice multiples fpdi_pdf_parser getPageRotation.
- `#11733` Fix auto completion mobile fixe.
- `#11738` [LOG] Journalisation - Exclure les erreurs utilisateur du type 'deprecated'.
- `#11739` Amélioration de la MAJ des colis (CRON).
- `#11741` Fix getScriptInfo - Undefined index: file.
- `#11748` Ajout cron.php - CRON pour Tâche planifiée.
- `#11760` Amélioration CRON par IP.
- `#11785` Debug - Safe Dump.
- `#11786` Fix facture double créée lors de l'enchaînement de statut.
- `#11801` Fix getAdminLink pages de configuration.
- `#11802` Fix getAdminLink des templates.
- `#11803` Fix getAdminLink AJAX.
- `#11809` [BO] Fix Templates - Affichage des messages JS.
- `#11810` [BO] Fallback affichage des message dynamiques.
- `#11815` getCodeTranslate - TypeError Exception.
- `#11821` [BO] Fix getControllerName.
- `#11824` Fix isAdminController.
- `#11826` [BO] AdminAlert affichage dynamique permanant.
- `#11827` [BO] Detail de commande - Fix Template assign.
- `#11828` [BO] TNTOfficiel_AdminAlert Ajout du type de message 'info'.
- `#11829` Compat htmlspecialchars_decode PHP 8.1.0.
- `#11833` Vider la liste des fichiers/dossiers du module à toujours supprimer.
- `#11831` Validator - Concatenation should be spaced.
- `#11832` Validator - There should not be blank lines between docblock and the documented element.
- `#11835` Validator - The keyword 'elseif' should be used.
- `#11836` Validator - Multi-line arrays must have a trailing comma.
- `#11837` Générer une `1.0.14`.




## 1.0.15 2023-11-20

- `#10830` Vérifier le code postal/ville de l'adresse sélectionnée.
- `#11011` [BO] Colis sauvegarde automatique des poids colis.
- `#11858` Ajout TNTOfficiel_getDateFormat.
- `#11859` getDateTimeFormat traductions des jours et mois.
- `#11860` Simplification saveShipment.
- `#11862` [BO] Date de ramassage et de livraison au format l d F Y.
- `#11868` Processsus de MAJ 1.0.15.
- `#11869` Fix MAJ colis date des commandes.
- `#11876` Ajout d'options surchargables.
- `#11877` Option pour création de transporteurs multiples.
- `#11879` TNTOfficiel_getDateFormat - Supporter le format datetime.
- `#11881` Ajout TNTOfficiel_getDateTZ.
- `#11882` Ajout TNTOfficiel_getDateUTCLocalTZ.
- `#11883` Option TNTOfficiel_getDateFormat pour affichage UTC.
- `#11884` Afficher la date de ramassage réellement appliquée.
- `#11885` Option getDateTime pour un timezone (UTC par défaut).
- `#11908` [BO] Transporteurs TNT - Action groupée pour supprimer des transporteurs TNT.
- `#11909` [BO] Transporteurs TNT - Colonne livraison gratuite.
- `#11910` [BO] Transporteurs TNT - Message aucun transporteurs créé.
- `#11942` [BO] Traductions AdminTNTOfficielCarrier.
- `#11943` [BO] Liste Transporteurs TNT - Fix filter prefix getCookieFilterPrefix().
- `#11944` [BO] Liste Transporteurs TNT - Fix filter prefix getCookieOrderByPrefix().
- `#11979` Fix dns_get_record Warning if no resolving.
- `#11991` Ajout setter setField pour ObjectModel.
- `#11992` Fix getFormAccountAuth return value.
- `#11993` Fix files in gitignore.
- `#12004` Amélioration du titre des erreurs AJAX 5XX.
- `#12005` Afficher la description des erreurs AJAX 5XX en console.
- `#12006` [BO] Séparation de la colonne POD/Action.
- `#12007` [BO] Ligne de colis en lecture seule si le numéro de suivi existe.
- `#12008` Ajout fonction JS pour obtenir l'identifiant d'un element.
- `#12009` [BO] Colis - Restaurer le focus après la MAJ.
- `#12010` [BO] Colis - MAJ icones des actions.
- `#12011` [BO] Colis - Highlight du champs à vérifier.
- `#12021` [FO] Vérifier que l'adresse sélectionnée appartient au client.
- `#12043` Fix Uncaught TypeError: $ is not a function.
- `#12062` Fix TNTOfficiel_CreatePageSpinner.
- `#12068` Fix checkAddressPostcodeCity.
- `#12097` Fix propriété AJAX async et cache.
- `#12098` Fix AJAX recherche code postal ville d'un point de livraison.
- `#12112` Fix hookDisplayBeforeCarrier Cart reference.
- `#11956` EOD service P_ 18:00 Express.
- `#12387` Undefined index: fltHRAAdditionalCost.
- `#12388` Undefined offset: 0.
- `#12392` Limite des colis à 30kg en INDIVIDUAL et DROPOFFPOINT.
- `#12394` [BO] Nouveau tri de la liste de création des services.
- `#12426` getContextCarrierModelList - Fix pour inclure les transporteurs supprimées.
- `#12427` [BO] JS Inclure les transporteurs supprimées.
- `#12430` Fix date de ramassage duedate & shippingdate.
- `#12431` Amélioration des messages de la date de ramassage.
- `#12433` Validator.
- `#12432` Générer une `1.0.15`.




## 1.0.16 2024-02-28

- `#12442` Format date et heure canonique.
- `#12464` [BO] [Order] CSS Fix compat theme material.
- `#12469` MAJ documentation.
- `#12502` Format date l jS F Y.
- `#12503` [BO] [UI] Date de ramassage en lecture seule.
- `#12504` TNTOfficiel_getDateLocalTZUTC.
- `#12505` Compat PHP 8.
- `#12508` Lib PDF - Fix nom de classe.
- `#12509` Lib PDF - PSR reformat.
- `#12510` Fix traductions.
- `#12513` Processsus de MAJ 1.0.16.
- `#12518` Fix paiement panier virtuel.
- `#12519` Fix paiement on submit.
- `#12556` Fix TZ par défaut.
- `#12542` Fix ne pas charger l'API Google Map si absence de clé.
- `#12543` Amélioration du Spinner AJAX (excl. sync and script).
- `#12560` Traductions processus install uninstall.
- `#12561` Fix erreur SQL Unknown column.
- `#12562` TNTOfficiel_Install::isReady.
- `#12578` Remplacer DateTime::createFromFormat avec TNTOfficiel_Tools::getDateTime.
- `#12591` Méthode getDateTime avec TZ optionnel.
- `#12605` [LOG] Amélioration journalisation autoClean pour le compte.
- `#12606` [LOG] Ajout date dans la journalisation des requêtes.
- `#12607` SoapFault [HTTP] considéré comme un erreur de communication.
- `#12609` [LOG] Journalisation limitée à 2 ans.
- `#12610` Exception traduction de code.
- `#12620` [BO] Fix MAJ date de ramassage si point de livraison inexistant.
- `#12621` Ajout méthode hasDeliveryPoint pour Cart.
- `#12622` Ajout méthode isDeliveryPoint pour Carrier.
- `#12623` Refactor getDeliveryPointType pour Carrier.
- `#12627` Filtre et validation du champ sender email.
- `#12628` [BO] Ajout hookActionAdminCartsControllerBefore.
- `#12634` Précision date déjà passée.
- `#12715` Fix méthode hasDeliveryPoint pour Cart.
- `#12716` Ajout méthode isDeliveryPoint pour Cart.
- `#12717` Ajout méthode getDeliveryPointType pour Cart.
- `#12718` Fix hookDisplayAfterCarrier getDeliveryPoint using selected carrier from hook.
- `#12719` Refactor getDeliveryPointType pour Order.
- `#12721` Précision date invalide.
- `#12729` [BO] Fix DeliveryOption.
- `#12730` Fix SOAP sender email pour expeditionCreation et pickUpRequest.
- `#12631` Validator.
- `#12731` Fix structure.
- `#12732` Every single file of the package has to contain a valid license header.
