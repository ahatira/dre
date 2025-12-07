# Standardisation Base Stories - Prompt Concis

**Date**: 2025-12-08  
**Version**: 1.0.0

---

## 🎯 Mission

Standardiser chaque dossier `source/patterns/base/*` avec le template unifié.

## 📋 Dossiers à faire

aspects | backgrounds | borders | brand | colors | example | fonts | shadows | sizes | typography | utilities

---

## ⚡ Workflow (Par dossier)

### 1️⃣ Analyser d'abord

```bash
ls -la source/patterns/base/{story}/
cat source/patterns/base/{story}/{story}.twig
cat source/patterns/base/{story}/{story}.yml
cat source/patterns/base/{story}/{story}.stories.jsx
```

**Comprendre**:
- Structure actuelle du `.twig` (simple ou complexe?)
- Données dans `.yml` (quelles catégories? combien d'items?)
- Story dans `.stories.jsx` (état actuel)
- Présence d'un `.css` ou `.css` inutile?

### 2️⃣ Appliquer les changements

**Refactor `.twig`** → Utiliser template `_base-story.twig` comme `animations`

**Valider `.stories.jsx`**:
```jsx
const settings = {
  title: 'Base/{Name}',
  // ❌ NO tags: ['autodocs']
};
```

**Vérifier `.yml`** → Données réalistes, Faker.js si besoin

**Nettoyer** → Pas de `README.md`

### 3️⃣ Valider

```bash
npm run lint:check
npm run build
npm run watch  # Vérifier visuellement
```

---

## ✅ Checklist finale

- [ ] `.twig` refactorisé avec `_base-story.twig`
- [ ] `.stories.jsx` sans `tags: ['autodocs']`
- [ ] Pas de `README.md`
- [ ] `npm run build` ✓
- [ ] Storybook visuel OK

**Commit**: `refactor(base): standardise {story}`

---

## 💡 Clé

**Lire d'abord, agir après** — Chaque dossier peut être différent.
