Rapport des modifications

1. Découpe des angles du rayon de bordure

- Prix fixe par angle arrondi du rectangle principal (HT) :
  Prix fixe appliqué à chaque coin arrondi du grand rectangle principal.
- Prix fixe par angle arrondi de la découpe rectangle (HT) :
  Prix fixe appliqué à chaque coin arrondi du rectangle découpé.
- Le calcul est désormais effectué par angle actif :
  chaque coin avec un rayon supérieur à `0` ajoute son montant au prix final.

2. Affichage du détail des prix en mode test

- Le tableau `fixedTable` affiche maintenant les nouvelles lignes de tarification des angles arrondis.
- Le détail visible comprend :
  le nombre d’angles détectés, le prix unitaire configuré, le montant HT et le montant TTC.
- Cela permet à l’administrateur de contrôler plus facilement le format de calcul appliqué.

3. Amélioration de l’aperçu SVG de la découpe rectangle

- Les libellés des rayons affichent correctement la valeur saisie par l’utilisateur pour chaque angle.
- Les dimensions des rayons de la découpe rectangle ont été rapprochées visuellement de la forme découpée.
- L’affichage du rectangle principal n’a pas été modifié.

4. Gestion de l’option de trous `Bords`

- Le type de trous `Bords` est masqué dans l’interface lorsque la forme principale est :
  cercle, demi-cercle ou ellipse.
- Cette modification est uniquement visuelle pour guider l’utilisateur dans ses choix.
- Aucun blocage de commande ni d’ajout au panier n’a été ajouté en cas de sélection incohérente existante.

5. Réorganisation de la configuration dans l’administration

- L’option `Afficher les personnalisations sauvegardées (Oui/Non) sur la page "Mon compte"` a été déplacée vers l’onglet de configuration générale.
- L’ancien onglet `Cartes Mon compte`, qui contenait uniquement cette option, a été supprimé.
- Cette modification simplifie l’organisation de la configuration du module dans l’administration.
