# Change Invoice Ref - Dolibarr Module

## Description
Module Dolibarr permettant de modifier rapidement la référence d'une facture brouillon via un bouton d'action dédié.

## Fonctionnalités
- Ajout d'un bouton "Changer la référence" sur les fiches facture en statut brouillon
- Interface modale avec input texte pour saisir la nouvelle référence
- Vérification de l'unicité de la référence
- Avertissement si des documents existent déjà pour la facture avant renommage
- Respect des permissions et de l'isolation multi-entité
- Compatible Dolibarr v16 à v23

## Compatibilité
- Validation statique effectuée contre Dolibarr 23.0
- Hook utilisé : `invoicecard` via `addMoreActionsButtons`
- Statut brouillon vérifié avec `Facture::STATUS_DRAFT`
- Prérequis PHP aligné sur Dolibarr 23 : PHP 7.2 minimum

## Installation
1. Extraire le ZIP dans `htdocs/custom/`
2. Activer le module depuis Interface d'administration > Modules
3. Attribuer la permission "Change invoice reference" aux utilisateurs concernés

## Utilisation
1. Ouvrir une fiche facture client en statut brouillon
2. Cliquer sur le bouton "Changer la référence"
3. Saisir la nouvelle référence dans le champ texte
4. Si des documents existent déjà, choisir entre annuler le renommage pour supprimer les documents à la main, ou poursuivre quand même
5. Valider

## Informations
- Numéro de module: 136392
- Éditeur: Daxit Solutions
- Site: https://daxit.be
- Version: 1.0.2
- Compatibilité Dolibarr annoncée: 16.0 à 23.x
- Licence: GPL-3.0+

## Notes de version
### 1.0.2
- Ajout d'un avertissement avant renommage si des documents existent déjà pour la facture
- Deux choix proposés : annuler le changement pour supprimer les documents à la main, ou renommer quand même

### 1.0.1
- Mise à jour des métadonnées de compatibilité pour Dolibarr 23
- Mise à jour du prérequis PHP minimum à 7.2
- Mise à jour de la documentation

## Support
Pour toute question ou demande de support, contactez Daxit Solutions.
