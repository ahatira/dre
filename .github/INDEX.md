# PS Theme Documentation Index

**Version**: 2.0.0  
**Last Updated**: 2025-12-01

---

## 📖 How to Use This Documentation

This index provides a **navigation map** for all PS Theme documentation. Use it to find the right document for your specific need.

---

## 🎯 Quick Start Guide

### I'm New to the Project

**Read in this order:**

1. **[README.md](../README.md)** (Project overview, setup instructions)
2. **[This INDEX](#-documentation-structure)** (Where everything is)
3. **[COMPLETE_RULES.md](#1-complete_rulesmd)** (All coding standards)
4. **[ATOMIC_DESIGN_RULES.md](#2-atomic_design_rulesmd)** (Component methodology)

### I'm Creating a New Component

**Workflow:**

1. Read **[ATOMIC_DESIGN_RULES.md](#2-atomic_design_rulesmd)** → Understand composition strategy
2. Read **[COMPONENT_TEMPLATE_STANDARD.md](#4-component_template_standardmd)** → File structure
3. Consult **[CSS_STANDARDS.md](#6-css_standardsmd)** → CSS best practices
4. Reference **[CSS_VARIABLES_SYSTEM.md](#3-css_variables_systemmd)** → Token usage (new system)
5. Follow **[STORYBOOK_DOC_TEMPLATE.md](#7-storybook_doc_templatemd)** → Documentation format
6. Audit with **[COMPONENT_CONFORMITY_PROMPT.md](#8-component_conformity_promptmd)** → Verify conformity

### I'm Refactoring an Existing Component

**Workflow:**

1. Run **[COMPONENT_CONFORMITY_PROMPT.md](#8-component_conformity_promptmd)** → Identify issues
2. Apply **[STANDARDIZE_COMPONENT_PROMPT.md](#9-standardize_component_promptmd)** → Fix workflow
3. Consult **[COMPLETE_RULES.md](#1-complete_rulesmd)** → Verify all standards
4. Migrate to **[CSS_VARIABLES_SYSTEM.md](#3-css_variables_systemmd)** (optional but recommended)

### I Have a Specific Question

**Use this decision tree:**

| Question | Document |
|----------|----------|
| "What are the coding rules?" | [COMPLETE_RULES.md](#1-complete_rulesmd) |
| "How do I compose atoms into molecules?" | [ATOMIC_DESIGN_RULES.md](#2-atomic_design_rulesmd) |
| "How do I use CSS tokens/variables?" | [CSS_VARIABLES_SYSTEM.md](#3-css_variables_systemmd) |
| "What files does a component need?" | [COMPONENT_TEMPLATE_STANDARD.md](#4-component_template_standardmd) |
| "How do I write Storybook docs?" | [STORYBOOK_DOC_TEMPLATE.md](#7-storybook_doc_templatemd) |
| "What are the CSS best practices?" | [CSS_STANDARDS.md](#6-css_standardsmd) |
| "How do I check if my component is correct?" | [COMPONENT_CONFORMITY_PROMPT.md](#8-component_conformity_promptmd) |
| "How do I migrate legacy code?" | [STANDARDIZE_COMPONENT_PROMPT.md](#9-standardize_component_promptmd) |

---

## 📚 Documentation Structure

### Core Standards (MANDATORY Reading)

#### 1. COMPLETE_RULES.md
**Path**: `.github/COMPLETE_RULES.md`  
**Size**: ~2300 lines  
**Status**: 🔒 **ABSOLUTE REFERENCE**

**Purpose**: **Single source of truth** for ALL project standards.

**Contains**:
- Stack technique (Vite, PostCSS, Storybook)
- File architecture (5 required files per component)
- BEM & nomenclature (strict `ps-` prefix)
- Design tokens (0 hardcoded values rule)
- CSS moderne (nesting with `&`)
- Cascade & spécificité
- Minimal markup principle
- Modifiers indépendants
- Icons system
- Semantic color naming
- Storybook standards
- Twig templates
- YAML configuration
- Documentation requirements
- **Base Stories Standards (Section 14.5)** - Token documentation workflow
- Accessibilité (WCAG 2.2 AA)
- Performance
- Workflow & validation
- Checklist complet (20 sections)

**When to read**: ALWAYS before any component work.

**Cross-references**:
- Extends: [ATOMIC_DESIGN_RULES.md](#2-atomic_design_rulesmd), [CSS_VARIABLES_SYSTEM.md](#3-css_variables_systemmd)
- Implemented by: [COMPONENT_TEMPLATE_STANDARD.md](#4-component_template_standardmd)
- Verified by: [COMPONENT_CONFORMITY_PROMPT.md](#8-component_conformity_promptmd)

---

#### 2. ATOMIC_DESIGN_RULES.md
**Path**: `.github/ATOMIC_DESIGN_RULES.md`  
**Size**: ~600 lines  
**Status**: 🔒 **MANDATORY for components > atoms**

**Purpose**: Enforce Brad Frost's Atomic Design methodology.

**Contains**:
- Atomic Design principles (atoms → molecules → organisms → templates → pages)
- Hierarchy & composition rules
- Component analysis workflow (4-step mandatory process)
- Atoms definition & examples (19 in PS Theme)
- Molecules definition & examples (20 in PS Theme)
- Organisms definition & examples (12 in PS Theme)
- Templates definition & examples (8 in PS Theme)
- Pages definition & examples (8 in PS Theme)
- Single responsibility principle
- **Composition before creation** (reuse atoms, don't recreate)
- Component reusability matrix
- Practical examples (FormField, SearchForm, Header)
- Anti-patterns to avoid (13 common mistakes)

**Key Concepts**:
- **"Molecules are groups of atoms functioning together as a unit"**
- **"Always compose existing atoms, never recreate markup"**
- **"Analyze required atoms BEFORE creating any component"**

**When to read**: 
- BEFORE creating any molecule, organism, template, or page
- When refactoring components to improve reusability
- When debugging composition issues

**Cross-references**:
- Referenced by: [COMPLETE_RULES.md](#1-complete_rulesmd) Section 2
- Examples: FormField molecule, SearchForm molecule, Header organism
- Related: [COMPONENT_TEMPLATE_STANDARD.md](#4-component_template_standardmd) Section "Composition"

---

#### 3. CSS_VARIABLES_SYSTEM.md
**Path**: `.github/CSS_VARIABLES_SYSTEM.md`  
**Size**: ~500 lines  
**Status**: 🎯 **TARGET ARCHITECTURE** (gradual migration)

**Purpose**: Bootstrap 5-inspired CSS custom properties system.

**Contains**:
- Three-layer architecture (Root → Component → Context)
- Root-level variables (global design tokens)
- Component-scoped variables (local defaults)
- Cascade system (how variables inherit and override)
- Naming conventions (`--ps-{component}-{property}`)
- Migration strategy (gradual, coexistence with legacy)
- Performance considerations
- Dark mode support
- 10+ practical examples

**Key Concepts**:
```css
/* Layer 1: Root primitives */
:root {
  --ps-green-600: hsl(162, 72%, 38%);
}

/* Layer 2: Component defaults */
.ps-button {
  --ps-button-bg: var(--ps-green-600);
  background: var(--ps-button-bg);
}

/* Layer 3: Context overrides */
.sidebar .ps-button {
  --ps-button-bg: var(--ps-purple-600);
}
```

**When to read**:
- When creating NEW components (MUST use new system)
- When refactoring legacy components (opportunistic migration)
- When implementing theming or dark mode
- When customizing components via JavaScript

**Cross-references**:
- Referenced by: [COMPLETE_RULES.md](#1-complete_rulesmd) Section 4
- Related: [CSS_STANDARDS.md](#6-css_standardsmd) for implementation details
- Examples: Button, FormField with component-scoped variables

---

### Component Development Templates

#### 4. COMPONENT_TEMPLATE_STANDARD.md
**Path**: `.github/COMPONENT_TEMPLATE_STANDARD.md`  
**Size**: ~300 lines  
**Status**: 📋 **MANDATORY STRUCTURE**

**Purpose**: Defines the exact structure for ALL components.

**Contains**:
- 5 required files (`.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`)
- Optional 6th file (`.js` for behaviors)
- File structure examples
- Naming conventions
- Header comment templates
- Props documentation format
- BEM structure examples
- Token documentation format

**5 Required Files**:
```
{component}/
├── {component}.twig         # Template with commented params
├── {component}.css          # Styles with nesting + tokens
├── {component}.yml          # Defaults + comments
├── {component}.stories.jsx  # Stories with Autodocs
└── README.md                # Complete documentation
```

**When to read**: 
- BEFORE creating any new component
- When verifying file completeness
- When unsure about file naming/structure

**Cross-references**:
- Implements: [COMPLETE_RULES.md](#1-complete_rulesmd) Section 2
- Examples: Button, Avatar, Badge, Divider, FormField
- Verified by: [COMPONENT_CONFORMITY_PROMPT.md](#8-component_conformity_promptmd) checklist

---

#### 5. copilot-instructions.md
**Path**: `.github/copilot-instructions.md`  
**Size**: ~200 lines  
**Status**: 🤖 **AI AGENT INSTRUCTIONS**

**Purpose**: Instructions for AI assistants (GitHub Copilot, etc.).

**Contains**:
- Project overview (Drupal theme, Storybook, Vite)
- Architecture summary (Atomic Design levels)
- Key conventions (BEM, tokens, nesting)
- Workflow shortcuts
- References to complete documentation

**When to read**: 
- If you're an AI agent working on this project
- For quick project context without reading full docs

**Cross-references**:
- References ALL core documents
- Quick guide to full documentation

---

### Detailed Standards & Patterns

#### 6. CSS_STANDARDS.md
**Path**: `.github/CSS_STANDARDS.md`  
**Size**: ~400 lines  
**Status**: 📐 **CSS DEEP DIVE**

**Purpose**: Comprehensive CSS authoring standards.

**Contains**:
- CSS nesting syntax (`&` postcss-nested)
- Token usage patterns
- Cascade control strategies
- Selector specificity management
- Accessibility (focus-visible, ARIA, contrast)
- Performance (critical CSS, lazy loading)
- Browser support (Autoprefixer, Browserslist)
- Common patterns and anti-patterns

**When to read**:
- When writing/refactoring CSS
- When debugging cascade/specificity issues
- When implementing accessibility features
- When optimizing performance

**Cross-references**:
- Referenced by: [COMPLETE_RULES.md](#1-complete_rulesmd) Sections 5-6
- Complements: [CSS_VARIABLES_SYSTEM.md](#3-css_variables_systemmd)
- Examples: Button CSS, FormField CSS

---

#### 7. STORYBOOK_DOC_TEMPLATE.md
**Path**: `.github/STORYBOOK_DOC_TEMPLATE.md`  
**Size**: ~150 lines  
**Status**: 📖 **STORYBOOK FORMAT**

**Purpose**: Standardized Storybook documentation format.

**Contains**:
- Autodocs tag requirement
- Description format (≤ 2 lines opening)
- ArgTypes categorization (Content, Appearance, Behavior, Accessibility, Layout)
- Stories structure (Default + Showcases, NO individual variants)
- Props table requirements
- Examples and screenshots

**Key Rules**:
```javascript
export default {
  tags: ['autodocs'], // REQUIRED
  parameters: {
    docs: {
      description: {
        component: 'Short description (≤ 2 lines).\n\n' +
                   'See Props, Showcases, README for details.'
      }
    }
  }
}
```

**When to read**:
- When creating `.stories.jsx` files
- When documenting component variants
- When adding ArgTypes

**Cross-references**:
- Referenced by: [COMPLETE_RULES.md](#1-complete_rulesmd) Section 11
- Implemented in: ALL `.stories.jsx` files
- Examples: `button.stories.jsx`, `form-field.stories.jsx`

---

### Audit & Quality Tools

#### 8. COMPONENT_CONFORMITY_PROMPT.md
**Path**: `.github/COMPONENT_CONFORMITY_PROMPT.md`  
**Size**: ~200 lines  
**Status**: ✅ **CONFORMITY CHECKLIST**

**Purpose**: Systematic audit to verify component conformity.

**Contains**:
- File structure checklist (5 required files)
- BEM strict verification
- Token usage verification (0 hardcoded values)
- CSS nesting validation
- Minimal markup check
- Modifiers independence test
- Semantic colors check
- Icons system validation
- Storybook standards check
- Accessibility audit (WCAG 2.2 AA)
- Score calculation

**Usage**:
```
"Vérifie la cohérence du composant [Name] en respectant 
STRICTEMENT toutes les règles du projet"
```

**When to use**:
- AFTER creating/refactoring a component
- Before committing code
- When unsure if component is compliant
- As part of PR review

**Cross-references**:
- Verifies: [COMPLETE_RULES.md](#1-complete_rulesmd) ALL sections
- Uses: [ATOMIC_DESIGN_RULES.md](#2-atomic_design_rulesmd) composition rules
- Generates: Conformity report with score

---

#### 9. STANDARDIZE_COMPONENT_PROMPT.md
**Path**: `.github/STANDARDIZE_COMPONENT_PROMPT.md`  
**Size**: ~150 lines  
**Status**: 🔧 **REFACTORING WORKFLOW**

**Purpose**: Step-by-step process to standardize legacy components.

**Contains**:
- Pre-refactoring analysis
- Systematic refactoring steps
- Token migration strategy
- BEM correction workflow
- Accessibility enhancement
- Testing and validation
- Documentation update

**Usage**:
```
"Standardise le composant [Name] selon les règles du projet"
```

**When to use**:
- When refactoring legacy components
- When bringing old code to current standards
- When migrating to new CSS variable system
- As part of technical debt cleanup

**Cross-references**:
- Implements: [COMPLETE_RULES.md](#1-complete_rulesmd) refactoring strategy
- May apply: [CSS_VARIABLES_SYSTEM.md](#3-css_variables_systemmd) migration
- Verified by: [COMPONENT_CONFORMITY_PROMPT.md](#8-component_conformity_promptmd)

---

## 🗂️ Project Design Documentation

These documents are in `docs/` (not `.github/`) and cover design specifications.

### docs/design/

**Purpose**: Complete design specifications for all 87 components.

**Structure**:
```
docs/design/
├── atoms/          (19 .md files) - Button, Icon, Badge, etc.
├── molecules/      (20 .md files) - FormField, Card, Breadcrumb, etc.
├── organisms/      (12 .md files) - Header, Footer, Grid, etc.
├── templates/      (8 .md files) - Homepage, Article, Listing, etc.
├── pages/          (8 .md files) - Specific page instances
├── tokens/         (7 .yaml files) - Design token references
├── INDEX.md        - Component inventory
└── README.md       - Design system overview
```

**When to read**:
- BEFORE implementing any component (read its .md spec)
- When understanding design system structure
- When checking component variants/states

### docs/ps-design/

**Purpose**: PS Design System progress tracking.

**Structure**:
```
docs/ps-design/
├── README.md               - Design system overview + roadmap
├── INDEX.md                - Component inventory + progress (6/87 done)
├── CHANGELOG.md            - Implementation history (includes base stories audit)
└── COMPONENT_TEMPLATE.md   - Template for new component specs
```

**When to read**:
- To check project progress
- To see what components are done/pending
- To understand implementation phases
- To review base stories completeness audit (2025-12-01 entry)

### source/patterns/base/

**Purpose**: Base stories documenting design tokens in Storybook.

**Structure**:
```
source/patterns/base/
├── animations/     - Durations (6) + Presets (20) + Easing curves (35)
├── aspects/        - Aspect ratios (7)
├── borders/        - Widths (6) + Radii (8) + Colors (5 from brand.css)
├── brand/          - Semantic colors (52 tokens)
├── colors/         - Neutrals (11) + Palettes (60)
├── fonts/          - Font families (3) + Size scale (15) + Weights (3)
├── shadows/        - Elevation shadows (16)
└── sizes/          - Spacing scale (33)
```

**Standards**: See [COMPLETE_RULES.md Section 14.5](#1-complete_rulesmd) for:
- Data source synchronization (CSS ↔ YAML ↔ Twig)
- Template structure requirements
- Token coverage verification
- Legacy token cleanup workflow
- Documentation accuracy requirements

**When to read**:
- When updating design tokens (props files)
- When creating/modifying base stories
- When documenting token systems
- When auditing token coverage completeness

---

## 🔀 Cross-Reference Map

### Document Relationships

```
COMPLETE_RULES.md (Master)
    ↓ references
    ├── ATOMIC_DESIGN_RULES.md (Composition methodology)
    ├── CSS_VARIABLES_SYSTEM.md (Token architecture)
    ├── CSS_STANDARDS.md (CSS authoring)
    └── STORYBOOK_DOC_TEMPLATE.md (Documentation format)
    
COMPONENT_TEMPLATE_STANDARD.md
    ↓ implemented by
    └── All components in source/patterns/

COMPONENT_CONFORMITY_PROMPT.md
    ↓ verifies
    └── Conformity against COMPLETE_RULES.md + ATOMIC_DESIGN_RULES.md
    
STANDARDIZE_COMPONENT_PROMPT.md
    ↓ applies
    ├── COMPLETE_RULES.md standards
    └── CSS_VARIABLES_SYSTEM.md migration
    ↓ validated by
    └── COMPONENT_CONFORMITY_PROMPT.md
```

### Implementation Flow

```
1. Read Design Spec
   docs/design/{level}/{component}.md
   
2. Analyze Composition
   ATOMIC_DESIGN_RULES.md → Identify atoms to reuse
   
3. Follow Structure
   COMPONENT_TEMPLATE_STANDARD.md → 5 required files
   
4. Apply Standards
   COMPLETE_RULES.md → All 20 sections
   CSS_VARIABLES_SYSTEM.md → Component-scoped variables
   CSS_STANDARDS.md → CSS authoring
   STORYBOOK_DOC_TEMPLATE.md → Documentation
   
5. Audit Conformity
  COMPONENT_CONFORMITY_PROMPT.md → Verify 100% compliance
   
6. Commit
   Git commit with detailed message
```

---

## 🎓 Learning Path

### Level 1: Beginner (First Week)

**Day 1-2: Project Setup**
- [ ] Read [README.md](../README.md)
- [ ] Run `npm install` and `npm run watch`
- [ ] Explore Storybook (http://localhost:6006)
- [ ] Browse `source/patterns/elements/` (see atoms)

**Day 3-4: Understand Standards**
- [ ] Read [COMPLETE_RULES.md](#1-complete_rulesmd) Sections 1-5
- [ ] Study existing atoms: `button`, `icon`, `badge`
- [ ] Read [BEM methodology](http://getbem.com/)

**Day 5-7: First Component**
- [ ] Read [ATOMIC_DESIGN_RULES.md](#2-atomic_design_rulesmd) Sections 1-4
- [ ] Analyze simple molecule (e.g., `badge` or `avatar`)
- [ ] Create a test variant following [COMPONENT_TEMPLATE_STANDARD.md](#4-component_template_standardmd)

### Level 2: Intermediate (Second Week)

**Week 2: Composition & Tokens**
- [ ] Read [ATOMIC_DESIGN_RULES.md](#2-atomic_design_rulesmd) Sections 5-9
- [ ] Read [CSS_VARIABLES_SYSTEM.md](#3-css_variables_systemmd) fully
- [ ] Study molecule composition: `form-field`, `card`
- [ ] Create a molecule composing 2+ atoms
- [ ] Use [COMPONENT_CONFORMITY_PROMPT.md](#8-component_conformity_promptmd) to self-check

### Level 3: Advanced (Week 3+)

**Week 3: Complex Components**
- [ ] Read ALL documentation (all 9 docs)
- [ ] Create organism (e.g., `product-grid`, `header`)
- [ ] Refactor legacy component with [STANDARDIZE_COMPONENT_PROMPT.md](#9-standardize_component_promptmd)
- [ ] Implement dark mode with [CSS_VARIABLES_SYSTEM.md](#3-css_variables_systemmd)

**Week 4: Mastery**
- [ ] Review PRs using audit checklists
- [ ] Contribute to documentation improvements
- [ ] Mentor new team members

---

## 🚀 Quick Command Reference

### Development

```bash
# Install dependencies
npm install

# Development mode (Vite + Storybook)
npm run watch

# Build production
npm run build

# Build Storybook static
npm run storybook:build
```

### Validation

```bash
# Lint CSS
npx stylelint "source/**/*.css"

# Lint JavaScript
npx biome check source/

# Check for hardcoded values (anti-pattern)
grep -rn "#[0-9]" source/patterns/  # Should return 0 results
```

### File Operations

```bash
# List all atoms
ls source/patterns/elements/

# Search for component usage
grep -r "ps-button" source/patterns/

# Find design tokens
grep -r "--primary" source/props/
```

---

## 📞 Getting Help

### Decision Tree

**"I don't know where to start"**
→ Start with this INDEX, then [README.md](../README.md)

**"I don't understand a rule"**
→ Check [COMPLETE_RULES.md](#1-complete_rulesmd), cross-reference examples

**"My component audit failed"**
→ Run [COMPONENT_CONFORMITY_PROMPT.md](#8-component_conformity_promptmd), fix issues, recheck

**"How do I compose atoms?"**
→ Read [ATOMIC_DESIGN_RULES.md](#2-atomic_design_rulesmd) Section 3 (4-step workflow)

**"How do I use tokens?"**
→ Read [CSS_VARIABLES_SYSTEM.md](#3-css_variables_systemmd) Section 4-5

**"Example of correct implementation?"**
→ Study `source/patterns/elements/button/` (reference implementation)

---

## 📊 Documentation Coverage

| Topic | Primary Doc | Secondary Docs | Examples |
|-------|-------------|----------------|----------|
| **Coding Standards** | COMPLETE_RULES.md | CSS_STANDARDS.md | All components |
| **Composition** | ATOMIC_DESIGN_RULES.md | COMPONENT_TEMPLATE.md | FormField, SearchForm |
| **CSS Tokens** | CSS_VARIABLES_SYSTEM.md | COMPLETE_RULES (Sec 4) | Button, FormField |
| **File Structure** | COMPONENT_TEMPLATE.md | COMPLETE_RULES (Sec 2) | All components |
| **Storybook** | STORYBOOK_DOC_TEMPLATE.md | COMPLETE_RULES (Sec 11) | *.stories.jsx files |
| **Audit** | COMPONENT_CONFORMITY_PROMPT.md | STANDARDIZE_PROMPT.md | Post-implementation |
| **Accessibility** | COMPLETE_RULES (Sec 15) | CSS_STANDARDS.md | All components |

---

## 🔄 Document Update Policy

### When to Update This INDEX

- [ ] New documentation file added to `.github/`
- [ ] Major restructuring of existing documents
- [ ] New learning resources or examples
- [ ] Cross-references need updating

### Version History

- **v2.0.0** (2025-12-01) - Added ATOMIC_DESIGN_RULES.md, CSS_VARIABLES_SYSTEM.md, comprehensive INDEX
- **v1.0.0** (2025-11-30) - Initial complete rules documentation

---

**This INDEX is your navigation hub. Bookmark it, reference it, update it.**
