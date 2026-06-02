# BNP Editor

Module dédié à la configuration CKEditor 5 avec support des formats de texte standards Drupal et plugins contrib pour la plateforme BNP.

## Description

Ce module fournit:
- **4 formats de texte standards Drupal** configurés et optimisés
- Infrastructure pour plugins CKEditor 5 contrib
- Support complet des médias et embeds
- Paramètres globaux d'éditeur configurables
- Internationalisation complète (7 langues)

## Formats de Texte

### Full HTML (`full_html`)
Format HTML complet avec toutes les fonctionnalités CKEditor 5:
- Formatage avancé (bold, italic, strikethrough, superscript, subscript)
- Listes avec propriétés (styles, numérotation, réversion)
- Liens avec attributs avancés
- Tableaux avec insertion et édition
- Intégration média complète (images, vidéos, documents)
- Alignement de texte (left, center, right, justify)
- Blocs de code avec coloration syntaxique
- Édition source HTML
- Upload d'images inline

**Permissions**: Réservé aux administrateurs et éditeurs de confiance

### Basic HTML (`basic_html`)
Format HTML de base pour éditeurs standards:
- Formatage basique (bold, italic)
- Listes simples (à puces, numérotées)
- Liens
- Citations (blockquote)
- Titres (H2-H6)
- Intégration média
- Édition source limitée

**Permissions**: Éditeurs, contributeurs

### Restricted HTML (`restricted_html`)
Format restreint pour utilisateurs non fiables:
- Tags HTML limités (a, em, strong, cite, blockquote, ul, ol, li, h2-h6)
- Pas d'éditeur visuel (saisie directe)
- Conversion automatique des URLs en liens
- Paragraphes automatiques

**Permissions**: Utilisateurs authentifiés, commentaires

### Plain Text (`plain_text`)
Texte brut sans HTML:
- Tous les tags HTML sont échappés
- Conversion automatique des URLs en liens
- Paragraphes automatiques
- Pas d'éditeur visuel

**Permissions**: Tous les utilisateurs

## Installation

```bash
cd /path/to/drupal/src
drush en bnp_editor -y
drush cr
```

## Configuration

### Paramètres globaux

Accéder à `/admin/config/content/bnp-editor` pour:
- Activer/désactiver les plugins custom
- Configurer l'intégration média
- Définir les protocoles de liens autorisés

### Modules contrib optionnels

Le module supporte de nombreux modules contrib pour étendre les fonctionnalités:

**Amélioration des liens**:
- `linkit` - Autocomplete pour liens internes
- `editor_advanced_link` - Attributs de liens avancés
- `anchor_link` - Ancres et liens internes
- `extlink` - Icônes et comportements liens externes

**Média & Embeds**:
- `entity_embed` - Embed d'entités Drupal
- `ckeditor_media_embed` - Embed oEmbed (YouTube, Vimeo)
- `ckeditor_media_resize` - Redimensionnement média
- `blazy` - Lazy loading images/médias
- `slick` - Carousels et sliders

**Plugins CKEditor**:
- `ckeditor5_plugin_pack` - Collection de plugins utiles
- `ckeditor_emoji` - Picker d'emojis
- `ckeditor_bidi` - Support bidirectionnel (RTL/LTR)
- `ckeditor5_paste_filter` - Nettoyage contenu collé

**Amélioration contenu**:
- `token_filter` - Remplacement de tokens
- `pathologic` - Correction chemins de liens
- `ace_editor` - Éditeur de code avec coloration syntaxique
- `edit_media_modal` - Édition média en modal

Voir [CONTRIB_MODULES.md](CONTRIB_MODULES.md) pour la liste complète et les instructions d'installation.

### Installation modules contrib

```bash
# Exemple: installer Linkit et Editor Advanced Link
composer require drupal/linkit drupal/editor_advanced_link
drush en linkit editor_advanced_link -y
drush cr

# Mettre à jour les configurations de format si nécessaire
drush cex -y
```

## Permissions

Configurer les permissions sur `/admin/people/permissions`:

- **Administer BNP Editor** → Administrator
- **Use Full HTML text format** → Administrator, Editor
- **Use Basic HTML text format** → Editor, Content Manager
- **Use Restricted HTML text format** → Authenticated users
- **Use Plain text format** → Tous les utilisateurs

## Internationalisation

Support complet pour 7 langues:
- 🇫🇷 Français (fr)
- 🇳🇱 Néerlandais (nl)
- 🇪🇸 Espagnol (es)
- 🇮🇹 Italien (it)
- 🇱🇺 Luxembourgeois (lb)
- 🇵🇱 Polonais (pl)
- 🇩🇪 Allemand (de)

```bash
# Importer les traductions
drush locale:import fr modules/custom/bnp_editor/translations/fr.po
drush cr
```

## Structure

```
bnp_editor/
├── config/install/          # Configurations 4 formats
│   ├── filter.format.full_html.yml
│   ├── editor.editor.full_html.yml
│   ├── filter.format.basic_html.yml
│   ├── editor.editor.basic_html.yml
│   ├── filter.format.restricted_html.yml
│   ├── filter.format.plain_text.yml
│   └── bnp_editor.settings.yml
├── src/
│   ├── Form/                # Formulaires d'admin
│   ├── Plugin/CKEditor5Plugin/  # Plugins CKEditor 5
│   └── Service/             # Services métier
├── translations/            # Fichiers .po (7 langues)
├── CONTRIB_MODULES.md       # Guide modules contrib
└── Documentation complète
```

## Développement

### Créer un plugin CKEditor 5 custom

1. Créer la classe dans `src/Plugin/CKEditor5Plugin/`
2. Créer le JavaScript dans `js/ckeditor5_plugins/`
3. Déclarer la librairie dans `bnp_editor.libraries.yml`
4. `drush cr`

Voir [ARCHITECTURE.md](ARCHITECTURE.md) pour plus de détails.

## Documentation

- **[QUICKSTART.md](QUICKSTART.md)** - Démarrage rapide (3 minutes)
- **[INSTALL.md](INSTALL.md)** - Instructions d'installation détaillées
- **[ARCHITECTURE.md](ARCHITECTURE.md)** - Documentation technique
- **[CONTRIB_MODULES.md](CONTRIB_MODULES.md)** - Guide modules contrib
- **[CHANGELOG.md](CHANGELOG.md)** - Historique des versions
- **[bnp_editor.api.php](bnp_editor.api.php)** - Hooks disponibles

## Support

```bash
# Vérifier les modules optionnels installés
drush pm:list --status=enabled | grep -E "(linkit|entity_embed|ckeditor)"

# Voir les logs
drush watchdog:show --type=bnp_editor

# Status du module
drush status-report | grep -i editor
```

## Conformité projet

- ✅ Config-First: Toute configuration exportable
- ✅ Dependency Injection: Services injectés
- ✅ Drupal Native: APIs standard + formats Drupal standards
- ✅ Internationalisation: 7 langues obligatoires
- ✅ Code en anglais: Identifiants et commentaires
- ✅ UI traduisible: Système translation Drupal

---

**Version**: 1.0.0  
**Drupal**: 11.x  
**Package**: BNP  
**Licence**: Propriétaire - BNP Paribas Real Estate
