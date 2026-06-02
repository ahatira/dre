# BNP Editor - Quick Start Guide

## Installation en 3 minutes

### 1. Activer le module

```bash
cd /path/to/drupal/src
drush en bnp_editor -y
drush cr
```

### 2. Importer les traductions (optionnel)

```bash
# Pour le français
drush locale:import fr modules/custom/bnp_editor/translations/fr.po --type=customized --override=all
```

### 3. Configurer les permissions

Aller sur `/admin/people/permissions` et assigner:
- ✅ **Use Full HTML text format** → Administrator, Editor
- ✅ **Use Basic HTML text format** → Editor, Content Manager
- ✅ **Use Restricted HTML text format** → Authenticated users
- ✅ **Administer BNP Editor** → Administrator

**C'est tout!** Les 4 formats de texte standards sont prêts.

---

## Formats disponibles

### 🎨 Full HTML - Format complet
- **Pour qui**: Administrateurs, éditeurs de confiance
- **Fonctionnalités**: Toutes les options CKEditor 5, médias, tableaux, code source
- **Upload images**: ✅ Activé

### ✏️ Basic HTML - Format standard
- **Pour qui**: Éditeurs, contributeurs
- **Fonctionnalités**: Formatage de base, listes, liens, médias, titres
- **Upload images**: ❌ Via bibliothèque média uniquement

### 🔒 Restricted HTML - Format sécurisé
- **Pour qui**: Utilisateurs authentifiés, commentaires
- **Fonctionnalités**: Tags HTML limités, pas d'éditeur visuel
- **Sécurité**: Haute - Tags restreints

### 📝 Plain Text - Texte brut
- **Pour qui**: Tous les utilisateurs
- **Fonctionnalités**: Texte brut, pas de HTML
- **Sécurité**: Maximum - Tout HTML échappé

---

## Utilisation immédiate

### Créer du contenu

1. Créer ou éditer un contenu (node, block, etc.)
2. Sélectionner le format de texte approprié:
   - **Full HTML** pour contenu riche avec tous les outils
   - **Basic HTML** pour contenu standard
   - **Restricted HTML** pour contenu utilisateur non fiable
   - **Plain Text** pour texte simple
3. Utiliser la barre d'outils CKEditor (Full/Basic HTML seulement)

### Barre d'outils Full HTML

- **Texte**: Bold, Italic, Strikethrough, Underline, Superscript, Subscript
- **Listes**: À puces, Numérotées (avec styles et numérotation)
- **Alignement**: Gauche, Centre, Droite, Justifié
- **Liens**: Insertion de liens
- **Médias**: Images, Vidéos, Documents
- **Structure**: Titres H2-H6, Citations, Blocs de code, Tableaux
- **Avancé**: Édition source HTML, Rechercher/Remplacer

### Barre d'outils Basic HTML

- **Texte**: Bold, Italic
- **Listes**: À puces, Numérotées
- **Liens**: Insertion de liens
- **Médias**: Images, Vidéos, Documents
- **Structure**: Titres H2-H6, Citations
- **Avancé**: Édition source limitée

---

## Modules contrib optionnels

Enrichir les fonctionnalités avec des modules contrib:

### Installation rapide des essentiels

```bash
# Amélioration des liens
composer require drupal/linkit drupal/editor_advanced_link drupal/anchor_link

# Médias et embeds
composer require drupal/entity_embed drupal/ckeditor_media_resize

# Plugins CKEditor
composer require drupal/ckeditor5_plugin_pack drupal/ckeditor_emoji

# Activer les modules
drush en linkit editor_advanced_link anchor_link entity_embed ckeditor_media_resize ckeditor5_plugin_pack ckeditor_emoji -y
drush cr
```

Voir [CONTRIB_MODULES.md](CONTRIB_MODULES.md) pour la liste complète.

---

## Configuration rapide

### Paramètres globaux

Accéder à `/admin/config/content/bnp-editor` pour:
- ✅ Activer/désactiver les plugins custom
- ✅ Configurer l'intégration média
- ✅ Définir les protocoles de liens (http, https, mailto, tel)

### Personnaliser les formats

1. Aller sur `/admin/config/content/formats`
2. Éditer un format (ex: Full HTML)
3. Ajuster la toolbar CKEditor
4. Configurer les filtres
5. Enregistrer
6. Exporter: `drush cex -y`

---

## Vérification rapide

### Test des formats

```bash
# Vérifier que les formats sont installés
drush config:get filter.format.full_html
drush config:get filter.format.basic_html
drush config:get filter.format.restricted_html
drush config:get filter.format.plain_text

# Vérifier les éditeurs CKEditor
drush config:get editor.editor.full_html
drush config:get editor.editor.basic_html
```

### Test en interface

1. Aller sur `/node/add/article`
2. Tester chaque format dans le champ Body
3. Vérifier que CKEditor se charge correctement
4. Tester les boutons de la barre d'outils
5. Enregistrer et vérifier le rendu

### Vérifier les modules optionnels

```bash
# Voir les modules contrib installés
drush pm:list --status=enabled | grep -E "(linkit|entity_embed|ckeditor|anchor|emoji)"

# Status du système
drush status-report | grep -i editor
```

---

## Troubleshooting 5 minutes

### L'éditeur ne charge pas

```bash
# 1. Vider les caches
drush cr

# 2. Vérifier CKEditor 5
drush pm:list --status=enabled | grep ckeditor5

# 3. Console navigateur (F12) pour erreurs JS
```

### Les formats ne s'affichent pas

```bash
# 1. Reconstruire les permissions
drush php:eval "node_access_rebuild();"

# 2. Vérifier les permissions utilisateur
drush user:information [username]

# 3. Vérifier les rôles
drush role:list
```

### Modules contrib non reconnus

```bash
# 1. Vérifier l'installation
composer show | grep -E "(linkit|entity_embed|ckeditor)"

# 2. Activer explicitement
drush en linkit -y

# 3. Vider les caches
drush cr

# 4. Vérifier les configurations
drush config:status
```

---

## Commandes utiles

```bash
# Gestion module
drush en bnp_editor -y              # Activer
drush pmu bnp_editor -y             # Désactiver
drush cr                            # Vider caches

# Configuration
drush cex -y                        # Exporter config
drush cim -y                        # Importer config
drush config:get bnp_editor.settings

# Traductions
drush locale:import fr modules/custom/bnp_editor/translations/fr.po

# Diagnostics
drush watchdog:show --type=bnp_editor
drush status-report
drush pm:list --status=enabled | grep bnp

# Tests
drush test-run --module bnp_editor
```

---

## Prochaines étapes

### 1. Installer modules contrib recommandés

Voir [CONTRIB_MODULES.md](CONTRIB_MODULES.md) pour:
- Linkit (autocomplete liens internes)
- Entity Embed (embed entités)
- CKEditor 5 Plugin Pack (plugins additionnels)

### 2. Personnaliser les formats

Adapter les configurations aux besoins:
- Ajouter/supprimer boutons toolbar
- Ajuster les tags HTML autorisés
- Configurer les filtres spécifiques

### 3. Former les utilisateurs

- **Full HTML**: Réservé aux admins - toutes les fonctionnalités
- **Basic HTML**: Éditeurs - fonctionnalités standards
- **Restricted HTML**: Utilisateurs - sécurité maximale
- **Plain Text**: Tous - texte simple uniquement

---

## Support rapide

### Documentation complète

- **[README.md](README.md)** - Vue d'ensemble complète
- **[INSTALL.md](INSTALL.md)** - Installation détaillée
- **[ARCHITECTURE.md](ARCHITECTURE.md)** - Documentation technique
- **[CONTRIB_MODULES.md](CONTRIB_MODULES.md)** - Guide modules contrib
- **[CHANGELOG.md](CHANGELOG.md)** - Historique versions

### Questions fréquentes

**Q: Différence entre Full HTML et Basic HTML?**  
R: Full HTML = tous les outils + upload images. Basic HTML = outils standards, pas d'upload.

**Q: Quel format pour les commentaires?**  
R: Restricted HTML (sécurité maximale, tags limités).

**Q: Comment ajouter un bouton toolbar?**  
R: Éditer `/admin/config/content/formats/manage/full_html` → Toolbar configuration

**Q: Les modules contrib sont-ils obligatoires?**  
R: Non, optionnels. Le module fonctionne sans eux.

---

**Besoin d'aide?**  
`drush watchdog:show --type=bnp_editor` ou consulter la documentation complète.

---

## Cas d'usage courants

### Ajouter un lien

1. Sélectionner le texte
2. Cliquer sur l'icône **lien** 🔗
3. Entrer l'URL
4. Enregistrer

### Insérer un tableau

1. Cliquer sur l'icône **tableau**
2. Choisir dimensions (lignes × colonnes)
3. Remplir les cellules

### Formater avec des titres

1. Placer le curseur sur la ligne
2. Sélectionner **Heading 2**, **Heading 3**, etc.
3. Le titre est formaté automatiquement

### Voir le code HTML source

1. Cliquer sur l'icône **Source** `<>`
2. Éditer le HTML si nécessaire
3. Fermer pour revenir en mode visuel

---

## Vérification rapide

### Test de l'éditeur

1. Aller sur `/node/add/article` (ou tout type de contenu)
2. Dans le champ Body, vérifier que CKEditor 5 se charge
3. Tester les boutons de la barre d'outils
4. Enregistrer le contenu
5. Vérifier le rendu en front-end

### Vérifier les logs

```bash
# Voir les logs du module
drush watchdog:show --type=bnp_editor

# Vérifier qu'il n'y a pas d'erreurs
drush watchdog:show --severity=Error
```

### Vérifier la configuration

```bash
# Lister les formats de texte
drush config:get filter.format.bnp_rich_text

# Vérifier les paramètres du module
drush config:get bnp_editor.settings
```

---

## Troubleshooting 5 minutes

### L'éditeur ne charge pas

```bash
# 1. Vider les caches
drush cr

# 2. Vérifier que CKEditor 5 est activé
drush pm:list --status=enabled | grep ckeditor5

# 3. Vérifier les erreurs JavaScript
# → Ouvrir la console navigateur (F12)
```

### Les traductions ne s'affichent pas

```bash
# 1. Réimporter les traductions
drush locale:import fr modules/custom/bnp_editor/translations/fr.po

# 2. Vider les caches
drush cr

# 3. Vérifier la langue active
drush config:get system.site default_langcode
```

### Les permissions ne fonctionnent pas

```bash
# 1. Reconstruire les permissions
drush php:eval "node_access_rebuild();"

# 2. Vider les caches
drush cr

# 3. Vérifier les rôles de l'utilisateur
drush user:information [username]
```

---

## Commandes utiles

```bash
# Activer le module
drush en bnp_editor -y

# Désactiver le module
drush pmu bnp_editor -y

# Vider les caches
drush cr

# Exporter la configuration
drush cex -y

# Importer la configuration
drush cim -y

# Voir les logs
drush watchdog:show --type=bnp_editor

# Lancer les tests
drush test-run --module bnp_editor
```

---

## Prochaines étapes

### Développement

Créer votre premier plugin CKEditor 5:

1. Copier `src/Plugin/CKEditor5Plugin/BnpEditorExample.php`
2. Renommer et adapter à votre besoin
3. Créer le JavaScript correspondant
4. Déclarer dans `bnp_editor.libraries.yml`
5. `drush cr`

Voir [ARCHITECTURE.md](ARCHITECTURE.md) pour plus de détails.

### Documentation complète

- **[README.md](README.md)** - Documentation utilisateur complète
- **[INSTALL.md](INSTALL.md)** - Instructions d'installation détaillées
- **[ARCHITECTURE.md](ARCHITECTURE.md)** - Documentation technique
- **[bnp_editor.api.php](bnp_editor.api.php)** - Hooks disponibles

---

## Support rapide

### Questions fréquentes

**Q: Puis-je ajouter des boutons à la barre d'outils?**  
R: Oui, éditer `config/install/editor.editor.bnp_rich_text.yml` et ajouter les items dans `toolbar.items`.

**Q: Comment personnaliser les tags HTML autorisés?**  
R: Éditer `config/install/filter.format.bnp_rich_text.yml` et modifier `filters.filter_html.settings.allowed_html`.

**Q: Le module fonctionne avec CKEditor 4?**  
R: Non, le module est conçu pour CKEditor 5 (Drupal 11+).

**Q: Puis-je créer plusieurs formats de texte?**  
R: Oui, dupliquer et adapter les fichiers de configuration dans `config/install/`.

---

**Besoin d'aide?**  
Consulter la documentation complète ou vérifier les logs avec `drush watchdog:show --type=bnp_editor`
