#!/bin/bash

set -e

# ------------------------------------------------------------------
# build-dolibarr-module-zip.sh
#
# But :
# - être lancé depuis le dossier parent qui contient UN module Dolibarr
# - détecter le dossier module automatiquement
# - lire la version dans :
#     <module>/core/modules/mod*.class.php
# - construire :
#     module_<nommodule>-<version>.zip
# - exclure les fichiers/dossiers cachés et parasites (.git, .DS_Store, etc.)
# ------------------------------------------------------------------

CURRENT_DIR="$(pwd)"

echo "Répertoire courant : $CURRENT_DIR"

# Trouver les dossiers non cachés à la racine
MODULE_DIRS=""
COUNT=0

for d in */ ; do
    [ -d "$d" ] || continue

    # retire le slash final
    dir_name="${d%/}"

    # ignore les dossiers cachés
    case "$dir_name" in
        .*) continue ;;
    esac

    COUNT=$((COUNT + 1))
    MODULE_DIRS="$MODULE_DIRS $dir_name"
done

if [ "$COUNT" -eq 0 ]; then
    echo "Erreur : aucun dossier module trouvé dans $CURRENT_DIR"
    exit 1
fi

if [ "$COUNT" -gt 1 ]; then
    echo "Erreur : plusieurs dossiers trouvés :"
    for name in $MODULE_DIRS; do
        echo " - $name"
    done
    echo "Laisse un seul dossier module dans ce répertoire avant d'exécuter le script."
    exit 1
fi

MODULE_DIR="$(echo "$MODULE_DIRS" | awk '{print $1}')"

if [ ! -d "$MODULE_DIR/core/modules" ]; then
    echo "Erreur : dossier introuvable : $MODULE_DIR/core/modules"
    exit 1
fi

# Chercher le fichier mod*.class.php
MOD_FILE=""
for f in "$MODULE_DIR"/core/modules/mod*.class.php; do
    if [ -f "$f" ]; then
        MOD_FILE="$f"
        break
    fi
done

if [ -z "$MOD_FILE" ]; then
    echo "Erreur : aucun fichier mod*.class.php trouvé dans $MODULE_DIR/core/modules"
    exit 1
fi

echo "Fichier module détecté : $MOD_FILE"

# Extraire la version depuis une ligne comme :
# $this->version='7.3.0';
# ou
# $this->version = "7.3.0";
VERSION="$(sed -n "s/.*\$this->version[[:space:]]*=[[:space:]]*['\"]\([^'\"]*\)['\"].*/\1/p" "$MOD_FILE" | head -n 1)"

if [ -z "$VERSION" ]; then
    echo "Erreur : impossible d'extraire la version depuis $MOD_FILE"
    exit 1
fi

ZIP_NAME="module_${MODULE_DIR}-${VERSION}.zip"

echo "Module détecté : $MODULE_DIR"
echo "Version détectée : $VERSION"
echo "Archive cible   : $ZIP_NAME"

# Supprime l'archive existante si elle existe déjà
if [ -f "$ZIP_NAME" ]; then
    rm -f "$ZIP_NAME"
fi

# Création du zip
# Exclusions :
# - fichiers/dossiers cachés
# - .git
# - fichiers macOS parasites
# - .svn
# - node_modules
zip -r "$ZIP_NAME" "$MODULE_DIR" \
  -x "*/.*" \
  -x "__MACOSX/*" \
  -x "*/__MACOSX/*" \
  -x "*.DS_Store" \
  -x "*/.git/*" \
  -x "*/.gitignore" \
  -x "*/.svn/*" \
  -x "*/node_modules/*"

echo "OK : archive créée -> $ZIP_NAME"