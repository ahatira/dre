# BNP Editor - Migration vers formats standards Drupal

## Vue d'ensemble

Le module BNP Editor a été refactorisé pour utiliser les formats de texte standards Drupal au lieu d'un format custom `bnp_rich_text`.

## Changements

### Avant (v0.x)

- **1 format custom**: `bnp_rich_text`
- Configuration personnalisée non-standard
- Compatibilité limitée avec modules contrib

### Après (v1.0)

- **4 formats standards Drupal**: `full_html`, `basic_html`, `restricted_html`, `plain_text`
- Configurations basées sur demo_umami (Drupal best practices)
- Support complet modules contrib
- Meilleure intégration écosystème Drupal

## Mapping des formats

| Ancien format | Nouveau format recommandé | Notes |
|--------------|---------------------------|-------|
| `bnp_rich_text` | `full_html` | Fonctionnalités équivalentes + améliorations |
| N/A | `basic_html` | Nouveau - pour éditeurs standards |
| N/A | `restricted_html` | Nouveau - pour utilisateurs non fiables |
| N/A | `plain_text` | Nouveau - texte brut |

## Migration si module déjà installé

Si vous avez déjà installé une version antérieure avec `bnp_rich_text`:

### Étape 1: Sauvegarder la configuration

```bash
# Exporter configuration actuelle
drush cex -y

# Sauvegarder
cp -r config/sync config/sync.backup
```

### Étape 2: Désinstaller ancienne version

```bash
# Désinstaller (garde les contenus)
drush pmu bnp_editor -y
drush cr
```

### Étape 3: Migrer contenus (optionnel)

Si des contenus utilisent `bnp_rich_text`, les migrer vers `full_html`:

```bash
# Via Drush SQL
drush sql:query "UPDATE node__body SET body_format = 'full_html' WHERE body_format = 'bnp_rich_text';"
drush sql:query "UPDATE node_revision__body SET body_format = 'full_html' WHERE body_format = 'bnp_rich_text';"

# Pour d'autres entités avec champs texte
drush sql:query "UPDATE block_content__body SET body_format = 'full_html' WHERE body_format = 'bnp_rich_text';"
drush sql:query "UPDATE block_content_revision__body SET body_format = 'full_html' WHERE body_format = 'bnp_rich_text';"
```

### Étape 4: Supprimer ancien format

```bash
# Supprimer configuration ancien format
drush config:delete filter.format.bnp_rich_text
drush config:delete editor.editor.bnp_rich_text
```

### Étape 5: Installer nouvelle version

```bash
# Mettre à jour les fichiers du module
# (via Git pull ou copie de fichiers)

# Réinstaller
drush en bnp_editor -y
drush cr
```

### Étape 6: Vérifier et tester

```bash
# Vérifier formats installés
drush config:get filter.format.full_html
drush config:get filter.format.basic_html
drush config:get filter.format.restricted_html
drush config:get filter.format.plain_text

# Tester sur un contenu
# Aller sur /node/add/article et tester les formats
```

### Étape 7: Mettre à jour permissions

```bash
# Aller sur /admin/people/permissions
# Assigner les nouvelles permissions:
# - use text format full_html → Administrator, Editor
# - use text format basic_html → Editor, Content Manager
# - use text format restricted_html → Authenticated users
```

### Étape 8: Exporter nouvelle configuration

```bash
drush cex -y
git add config/sync
git commit -m "Migrate to BNP Editor v1.0 with standard Drupal formats"
```

## Migration pour nouvelle installation

Si vous installez BNP Editor pour la première fois:

```bash
# Installation simple
drush en bnp_editor -y
drush cr

# Configurer permissions
# Aller sur /admin/people/permissions

# C'est tout!
```

## Différences fonctionnelles

### Full HTML (nouveau) vs bnp_rich_text (ancien)

**Améliorations**:
- ✅ Plus de boutons toolbar (alignment, strikethrough, underline, superscript, subscript)
- ✅ Blocs de code avec coloration syntaxique
- ✅ Styles de texte (lead paragraph, highlighted, small)
- ✅ Upload d'images inline (limite 10MB, 4000x4000px)
- ✅ Find & Replace
- ✅ Support meilleur des modules contrib
- ✅ Meilleure sécurité (filtres améliorés)

**Équivalences**:
- ✅ Bold, italic, liens, listes
- ✅ Tableaux
- ✅ Titres H2-H6
- ✅ Citations
- ✅ Médias (images, vidéos, documents)
- ✅ Édition source

## Modules contrib compatibles

La nouvelle architecture supporte mieux les modules contrib:

### Avec ancien format (limité)
- ❓ linkit (support partiel)
- ❌ entity_embed (non testé)
- ❌ ckeditor5_plugin_pack (incompatibilité possible)

### Avec nouveaux formats (complet)
- ✅ linkit (support complet)
- ✅ entity_embed (support complet)
- ✅ ckeditor5_plugin_pack (support complet)
- ✅ editor_advanced_link (support complet)
- ✅ anchor_link (support complet)
- ✅ Et 15+ autres modules contrib

Voir [CONTRIB_MODULES.md](CONTRIB_MODULES.md) pour la liste complète.

## Rollback (si nécessaire)

Si vous devez revenir à l'ancienne version:

```bash
# 1. Restaurer sauvegarde configuration
cp -r config/sync.backup/* config/sync/

# 2. Désinstaller nouvelle version
drush pmu bnp_editor -y

# 3. Restaurer anciens fichiers module
git checkout HEAD~1 -- web/modules/custom/bnp_editor

# 4. Réinstaller
drush en bnp_editor -y
drush cim -y
drush cr

# 5. Migrer contenus retour
drush sql:query "UPDATE node__body SET body_format = 'bnp_rich_text' WHERE body_format = 'full_html';"
```

## Support migration

En cas de problème durant la migration:

### Vérifier formats disponibles

```bash
drush config:status | grep filter.format
drush config:status | grep editor.editor
```

### Vérifier contenus

```bash
# Compter contenus par format
drush sql:query "SELECT body_format, COUNT(*) as count FROM node__body GROUP BY body_format;"
```

### Logs

```bash
drush watchdog:show --type=bnp_editor
drush watchdog:show --severity=Error
```

### Reconstruire tout

```bash
drush cr
drush php:eval "node_access_rebuild();"
drush cache:rebuild
```

## Recommandations

1. **Tester d'abord en développement** avant de migrer en production
2. **Sauvegarder la base de données** avant la migration
3. **Exporter la configuration** avant et après la migration
4. **Vérifier tous les contenus** utilisant l'ancien format
5. **Former les utilisateurs** aux nouveaux formats disponibles

## FAQ Migration

**Q: Mes contenus seront-ils perdus?**  
R: Non, seul le format de texte change. Le contenu HTML reste identique.

**Q: Dois-je réécrire mes contenus?**  
R: Non, le HTML existant est compatible. Seul le format_id change dans la DB.

**Q: Puis-je garder les deux formats?**  
R: Techniquement oui, mais non recommandé. Préférer la migration complète.

**Q: Combien de temps prend la migration?**  
R: 10-30 minutes selon la taille de la base de données.

**Q: Que faire si j'ai des custom plugins?**  
R: Les migrer vers la nouvelle architecture. Voir [ARCHITECTURE.md](ARCHITECTURE.md).

## Checklist migration

- [ ] Sauvegarde configuration exportée
- [ ] Sauvegarde base de données
- [ ] Test en environnement dev
- [ ] Migration contenus vers full_html
- [ ] Suppression ancien format
- [ ] Installation nouvelle version
- [ ] Test formats en UI
- [ ] Mise à jour permissions
- [ ] Export configuration finale
- [ ] Déploiement production
- [ ] Formation utilisateurs
- [ ] Monitoring logs post-migration

---

**Date migration recommandée**: Lors d'une maintenance planifiée  
**Temps d'arrêt estimé**: 5-10 minutes  
**Risque**: Faible (migration SQL simple + config)
