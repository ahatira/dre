# PS Theme Instructions - Navigation Hub

**Last Updated**: 2025-12-12  
**Maintainers**: Design System Team

---

## 🚀 Quick Start Guide

### For Humans (First Time Contributors)

**Step-by-step reading order**:

1. **START HERE**: [core.instructions.md](core.instructions.md) - Understand the tech stack, design tokens, and build system
2. **THEN READ**: [atomic-design.instructions.md](atomic-design.instructions.md) - Learn the composition philosophy (Atoms → Molecules → Organisms → Templates → Pages)
3. **CRITICAL**: [composition-token-first.instructions.md](composition-token-first.instructions.md) - Master the 4-step workflow for composing components (REQUIRED for Molecules+)
4. **NEXT**: [components.instructions.md](components.instructions.md) - Learn the 5-file structure, BEM methodology, and component standards
5. **REFERENCE**: Other files as needed based on your current task (see Quick Task Map below)

**Estimated reading time for basics**: ~1.5 hours

---

### For AI Agents (Context Priority)

**⚡ Always read in this order**:

1. **MANDATORY FIRST**: [composition-token-first.instructions.md](composition-token-first.instructions.md) - CRITICAL for any Molecules/Organisms/Templates/Pages work
2. **THEN**: File(s) matching the current task (see path-scoped `applyTo:` in frontmatter)
3. **REFERENCE**: Related files listed in `related:` frontmatter field

**Navigation tip**: Use frontmatter `applyTo:` patterns to auto-filter relevant instructions.

---

## 📊 Instruction Files Map

```
┌──────────────────────────────────────────────────────────────────┐
│ 🎯 CORE CONCEPTS (Read First)                                    │
├──────────────────────────────────────────────────────────────────┤
│ core.instructions.md                                             │
│ → Tech stack (Drupal 10/11, Storybook, Vite, PostCSS, Twig)     │
│ → Design tokens system (3-layer architecture)                   │
│ → Build system (npm scripts, cache management)                  │
│ Priority: CRITICAL | Read time: 15 min                          │
│                                                                  │
│ atomic-design.instructions.md                                    │
│ → Brad Frost's Atomic Design methodology                        │
│ → 5-level hierarchy (Atoms → Molecules → Organisms → Templates →│
│    Pages)                                                        │
│ → Composition rules (what composes what)                        │
│ Priority: CRITICAL | Read time: 20 min                          │
│                                                                  │
│ composition-token-first.instructions.md ⭐ MOST CRITICAL         │
│ → 4-step cascade workflow (params → utils → tokens → CSS)       │
│ → Token override patterns for composing components              │
│ → Applies to: Molecules, Organisms, Templates, Pages (NOT Atoms)│
│ Priority: CRITICAL | Read time: 15 min                          │
└──────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│ 🛠️ IMPLEMENTATION STANDARDS (Reference During Work)              │
├──────────────────────────────────────────────────────────────────┤
│ components.instructions.md                                       │
│ → 5-file mandatory structure (.twig, .css, .yml, .stories, .md) │
│ → BEM methodology (strict rules)                                │
│ → Component naming conventions                                  │
│ Priority: HIGH | Read time: 20 min                              │
│                                                                  │
│ css.instructions.md                                              │
│ → Design tokens (zero hardcoded values)                         │
│ → 3-layer CSS variables architecture                            │
│ → Nesting with & syntax (mandatory for new components)          │
│ → Token-First integration for composing components              │
│ Priority: HIGH | Read time: 15 min                              │
│                                                                  │
│ templates.instructions.md                                        │
│ → Twig template standards (Drupal-compatible patterns)          │
│ → YAML data structure (Faker.js for realistic Real Estate data) │
│ → NO arrow functions, NO .filter(v => v) (Drupal incompatible)  │
│ Priority: HIGH | Read time: 10 min                              │
│                                                                  │
│ javascript.instructions.md                                       │
│ → Drupal behaviors pattern (Drupal.behaviors + once())           │
│ → Event handling (scoped to context)                            │
│ → Library registration (ps.libraries.yml)                       │
│ Priority: MEDIUM | Read time: 10 min                            │
│                                                                  │
│ storybook.instructions.md                                        │
│ → Autodocs configuration (tags: ['autodocs'])                   │
│ → Story structure (Default + Showcases)                         │
│ → ArgTypes categorization (Content, Appearance, Behavior, etc.) │
│ Priority: HIGH | Read time: 10 min                              │
│                                                                  │
│ accessibility.instructions.md                                    │
│ → WCAG 2.2 Level AA compliance (mandatory)                      │
│ → Contrast ratios, keyboard navigation, focus-visible           │
│ → ARIA attributes, semantic HTML                                │
│ Priority: HIGH | Read time: 15 min                              │
└──────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│ ⚙️ SPECIALIZED WORKFLOWS (Task-Specific)                         │
├──────────────────────────────────────────────────────────────────┤
│ workflows.instructions.md                                        │
│ → Component generation workflow (11 steps)                      │
│ → Conformity audit checklist (100% score required)              │
│ → Git commit message format                                     │
│ Priority: HIGH | Read time: 15 min                              │
│                                                                  │
│ card-inheritance.instructions.md                                 │
│ → Card component embedding pattern ({% embed %})                 │
│ → card-{bundle}-{view_mode} naming convention                   │
│ → Token-First integration for Cards                             │
│ → Complete reference implementation (Card Offer Slide)          │
│ Priority: MEDIUM | Read time: 45 min (very comprehensive)       │
│                                                                  │
│ base-stories.instructions.md                                     │
│ → Token documentation stories (colors, fonts, shadows, etc.)    │
│ → Different structure: 4 files (NO README.md)                   │
│ → NO autodocs tag (exception to Storybook rules)                │
│ Priority: LOW | Read time: 10 min                               │
│                                                                  │
│ icon-system.instructions.md                                      │
│ → Icon sprite system (icons-sprite.svg)                         │
│ → data-icon attribute usage (NO icon- prefix in code)           │
│ → Build script (npm run build:icons)                            │
│ Priority: LOW | Read time: 5 min                                │
└──────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│ 🧠 META PROCESSES (Advanced)                                     │
├──────────────────────────────────────────────────────────────────┤
│ multi-expert-mode.instructions.md                                │
│ → 6-role expert analysis (Drupal, Atomic, CSS, Storybook, etc.) │
│ → Risk detection, dependency checking, impact matrix            │
│ → Automatic activation for CRITICAL/HIGH priority tasks         │
│ Priority: LOW | Read time: 20 min                               │
│                                                                  │
│ card-inheritance-prompt.md                                       │
│ → Complete analysis/implementation prompt template for Cards    │
│ → Copy/paste template for AI agents                             │
│ Priority: LOW | Read time: 5 min                                │
└──────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Dependency Graph

```
core.instructions.md
    ↓
atomic-design.instructions.md
    ↓
composition-token-first.instructions.md ⭐ CRITICAL
    ↓
    ├─→ components.instructions.md
    │       ↓
    │   css.instructions.md
    │       ↓
    │   templates.instructions.md
    │       ↓
    │   javascript.instructions.md
    │       ↓
    │   storybook.instructions.md
    │       ↓
    │   accessibility.instructions.md
    │       ↓
    │   workflows.instructions.md (aggregates all standards)
    │
    └─→ card-inheritance.instructions.md
            ↓
        card-inheritance-prompt.md
```

**Reading rule**: Always read parent files before children in the dependency graph.

---

## 🎯 Quick Task Map

**🆕 Creating a new component?**

| Component Level | Files to Read (in order) |
|----------------|--------------------------|
| **Atom** (element) | 1. atomic-design → 2. components → 3. css → 4. workflows |
| **Molecule** (component) | 1. atomic-design → 2. **composition-token-first** ⭐ → 3. components → 4. css → 5. workflows |
| **Organism** (collection) | 1. atomic-design → 2. **composition-token-first** ⭐ → 3. components → 4. css → 5. workflows |
| **Template** (layout) | 1. atomic-design → 2. **composition-token-first** ⭐ → 3. components → 4. templates → 5. workflows |
| **Page** | 1. atomic-design → 2. **composition-token-first** ⭐ → 3. components → 4. templates → 5. workflows |

**🐛 Fixing a bug?**

| Bug Type | Files to Read |
|----------|---------------|
| **CSS issue** (spacing, colors, layout) | css → composition-token-first (if composing) |
| **Accessibility issue** | accessibility → components |
| **JavaScript behavior** | javascript → components |
| **Storybook story** | storybook → components |
| **Twig template** | templates → components |

**🔧 Working on Card variants?**

| Task | Files to Read |
|------|---------------|
| **New Card variant** | 1. card-inheritance → 2. composition-token-first → 3. components |
| **Fix Card issue** | 1. card-inheritance → 2. Check parent Card component |
| **Understand Card pattern** | 1. card-inheritance (read Section 8: Reference Implementation) |

**📚 Setting up Storybook?**

| Task | Files to Read |
|------|---------------|
| **Component story** | storybook → components |
| **Base story** (tokens) | base-stories → storybook |
| **ArgTypes setup** | storybook (Section: ArgTypes Categorization) |

**♿ Ensuring accessibility?**

| Task | Files to Read |
|------|---------------|
| **WCAG compliance** | accessibility → components |
| **Keyboard navigation** | accessibility (Section: Keyboard Navigation) |
| **ARIA attributes** | accessibility (Section: ARIA) |
| **Screen readers** | accessibility (Section: Screen Readers) |

---

## 📏 File Statistics

| File | Lines | Complexity | Est. Read Time | Last Updated |
|------|-------|------------|----------------|--------------|
| card-inheritance.instructions.md | 2,319 | ⭐⭐⭐⭐⭐ | 45 min | 2025-12-12 |
| components.instructions.md | 747 | ⭐⭐⭐⭐ | 20 min | 2025-12-12 |
| css.instructions.md | 678 | ⭐⭐⭐ | 15 min | 2025-12-12 |
| atomic-design.instructions.md | 791 | ⭐⭐⭐⭐ | 20 min | 2025-12-05 |
| accessibility.instructions.md | 584 | ⭐⭐⭐ | 15 min | 2025-12-05 |
| composition-token-first.instructions.md | 546 | ⭐⭐⭐⭐ | 15 min | 2025-12-12 |
| workflows.instructions.md | ~500 | ⭐⭐⭐ | 15 min | 2025-12-05 |
| templates.instructions.md | ~400 | ⭐⭐ | 10 min | 2025-12-05 |
| storybook.instructions.md | ~300 | ⭐⭐ | 10 min | 2025-12-05 |
| javascript.instructions.md | ~300 | ⭐⭐ | 10 min | 2025-12-05 |
| base-stories.instructions.md | ~200 | ⭐ | 10 min | 2025-12-05 |
| core.instructions.md | ~500 | ⭐⭐⭐ | 15 min | 2025-12-05 |
| icon-system.instructions.md | ~150 | ⭐ | 5 min | 2025-12-05 |
| multi-expert-mode.instructions.md | ~600 | ⭐⭐⭐ | 20 min | 2025-12-08 |
| card-inheritance-prompt.md | ~100 | ⭐ | 5 min | 2025-12-10 |

**Total**: ~8,715 lines | **Total read time** (all files): ~4.5 hours

---

## 🔗 Related Resources

### Main Copilot Instructions
- [copilot-instructions.md](../copilot-instructions.md) - AI behavior configuration, language policy, Zero Tolerance Rules

### Project Documentation
- [Project README](../../README.md) - Project setup, build commands, JavaScript bundling
- [Design System Changelog](../../docs/ps-design/CHANGELOG.md) - Complete implementation history
- [Component Manifest](../../docs/design/COMPONENT_MANIFEST.yml) - All 87 components inventory
- [Design Specifications](../../docs/design/) - Complete specs for Atoms/Molecules/Organisms/Templates/Pages

### Build & Tooling
- [package.json](../../package.json) - npm scripts, dependencies
- [vite.config.js](../../vite.config.js) - Vite build configuration
- [biome.json](../../biome.json) - Biome linter/formatter config
- [.storybook/](../../.storybook/) - Storybook configuration

---

## ❓ FAQ

### "Where do I start as a new contributor?"

**Answer**: Follow the "For Humans" reading order at the top of this page. Start with `core.instructions.md`, then `atomic-design.instructions.md`, then `composition-token-first.instructions.md`. That's the foundation (~50 minutes reading).

### "I'm creating a Molecule, which files do I need?"

**Answer**: 
1. Read: `composition-token-first.instructions.md` (MANDATORY ⭐)
2. Read: `components.instructions.md` (file structure, BEM)
3. Read: `css.instructions.md` (tokens, nesting)
4. Reference: `workflows.instructions.md` (generation steps)

### "What's the difference between atoms and molecules?"

**Answer**: Read `atomic-design.instructions.md` Section 2 (Composition Rules). **TL;DR**: Atoms are autonomous (Button, Icon), Molecules compose Atoms (Card, Form Field). Molecules MUST follow Token-First workflow, Atoms don't.

### "How do I customize a parent component without breaking it?"

**Answer**: Read `composition-token-first.instructions.md` - This is THE core workflow. Use the 4-step cascade: params → utility classes → **override tokens (preferred)** → targeted CSS (last resort).

### "Can I modify card.css for my Card variant?"

**Answer**: ❌ **NO!** Never modify parent component CSS. Instead, override tokens in YOUR component's CSS:

```css
.ps-card-my-variant {
  /* Override Card tokens in YOUR CSS */
  --ps-card-padding-x: var(--size-6);
  --ps-card-gap: var(--size-4);
}
```

Read: `composition-token-first.instructions.md` + `card-inheritance.instructions.md`

### "Why are some files in French and others in English?"

**Answer**: **Language Policy** (defined in `copilot-instructions.md`):
- 🇬🇧 **AI Instructions**: English ONLY (all `.instructions.md` files)
- 🇫🇷 **User Communication**: French ONLY (chat responses, commit messages)
- 🇬🇧 **Code Documentation**: English ONLY (README, JSDoc, comments)
- 🇫🇷 **Project Logs**: French ONLY (CHANGELOG.md, meeting notes)

### "How do I know which instruction file applies to my current file?"

**Answer**: Check the `applyTo:` field in the file's frontmatter (YAML header). Example:

```yaml
---
applyTo:
  - "source/patterns/components/**/*"
---
```

This means the instructions apply to all files in `source/patterns/components/`.

### "I found a contradiction between two instruction files. What do I do?"

**Answer**: 
1. Check file versions (`version:` in frontmatter) - **newer wins**
2. Check priority (`priority:` in frontmatter) - **CRITICAL > HIGH > MEDIUM > LOW**
3. If still unclear, report the issue to Design System Team

### "Can I skip reading all instructions and just code?"

**Answer**: ❌ **Not recommended!** Minimum required reading (~1 hour):
1. `atomic-design.instructions.md` (20 min)
2. `composition-token-first.instructions.md` (15 min) - **CRITICAL if working on Molecules+**
3. `components.instructions.md` (20 min)
4. `workflows.instructions.md` (10 min)

Skipping these will result in conformity audit failures (100% score required).

---

## 🆘 Support

**Questions or issues with instructions?**

1. **First**: Check this README and search for keywords
2. **Then**: Read the relevant instruction file(s) completely
3. **If stuck**: Review related files (check `related:` in frontmatter)
4. **Still stuck**: Contact Design System Team

**Found a bug or improvement?**

- Create an issue or PR targeting `.github/instructions/` files
- Tag: `documentation`, `instructions`
- Maintainers will review and update

---

**Navigation Hub Version**: 1.0.0  
**Last Updated**: 2025-12-12  
**Maintainers**: Design System Team  
**Status**: ✅ ACTIVE
