---
title: Terminology Reference
version: 1.0.0
lastUpdated: 2025-12-12
applyTo:
  - ".github/instructions/**/*.md"
priority: LOW
related:
  - README.md
  - CODE_EXAMPLES_STYLE_GUIDE.md
status: ACTIVE
---

# Terminology Reference

**Purpose**: Standardize technical vocabulary across all instruction files for consistency and clarity.

---

## 🎯 Language Policy

**Primary Language**: American English (color, behavior, organization)

**Exceptions**:
- **AI Chat**: French (copilot responses to user)
- **Commit Messages**: French body (English type/scope)
- **Multi-Expert Mode**: French (user-facing guide)
- **User-Facing Content**: French (button labels, YAML mocks)

**Rationale**: English documentation = universal accessibility + industry standard

---

## 📚 Core Design System Terms

### Atomic Design Levels

| Term | Definition | Directory | Examples |
|------|------------|-----------|----------|
| **Atom** | Smallest indivisible UI element | `elements/` | button, icon, label, badge |
| **Molecule** | Group of atoms functioning together | `components/` | card, form-field, alert |
| **Organism** | Complex UI section composed of molecules | `collections/` | header, footer, navigation |
| **Template** | Page-level layout structure | `layouts/` | homepage-layout, article-layout |
| **Page** | Specific template instance with content | `pages/` | homepage, about-page |

**Forbidden Synonyms**:
- ❌ "Element" when referring to Atom level (use "Atom" or "element atom")
- ❌ "Component" generically (specify: Atom, Molecule, Organism)
- ❌ "Block" for Molecule (BEM context only)

**Usage**:
```markdown
✅ "The Button atom exposes tokens..."
✅ "Card molecule composes Badge atom"
❌ "The Button element includes an icon"
❌ "Card component uses Badge component"
```

---

### Composition Methods

| Term | Twig Syntax | Meaning | When to Use |
|------|-------------|---------|-------------|
| **Include** | `{% include %}` | Insert external template | Atoms in Molecules (most common) |
| **Embed** | `{% embed %}` | Insert template with block overrides | Templates extending base layouts |
| **Extend** | `{% extends %}` | Inherit from parent template | Page-level inheritance |
| **Use** | Generic | Reference another component | General discussion (not Twig-specific) |
| **Compose** | N/A | Combine multiple components | Architectural discussion |

**Forbidden Synonyms**:
- ❌ "Import" (JavaScript context, not Twig)
- ❌ "Require" (Node.js context)

**Usage**:
```markdown
✅ "Molecule includes atoms via {% include %}"
✅ "Card uses Badge and Button atoms"
✅ "Alert molecule composes Icon, Heading, and Text atoms"
❌ "Card imports Badge and Button"
```

---

### BEM Terminology

| Term | CSS Class Pattern | Meaning | Example |
|------|-------------------|---------|---------|
| **Block** | `.ps-block` | Root component class | `.ps-card` |
| **Element** | `.ps-block__element` | Component sub-part | `.ps-card__header` |
| **Modifier** | `.ps-block--modifier` | Component variant | `.ps-card--elevated` |
| **Variant** | Synonym for modifier | Color/style variation | `--primary`, `--large` |

**Forbidden Patterns**:
- ❌ "Sub-component" (use "Element")
- ❌ "Option" (use "Modifier" or "Variant")
- ❌ "Type" (ambiguous, use "Variant")

**Usage**:
```markdown
✅ "Add size modifier: .ps-button--large"
✅ "Card element: .ps-card__footer"
✅ "Color variants: primary, secondary, success"
❌ "Add size option to button"
❌ "Card sub-component for footer"
```

---

### Design Token Terminology

| Term | Scope | Pattern | Example |
|------|-------|---------|---------|
| **Token** | Global primitive | `--{category}-{property}` | `--primary`, `--size-4` |
| **Component Token** | Component-scoped | `--ps-{component}-{property}` | `--ps-button-padding-x` |
| **Semantic Token** | Meaning-based alias | `--{meaning}` | `--primary`, `--success`, `--danger` |
| **Primitive Token** | Raw value | `--{color}-{shade}` | `--green-600`, `--gray-400` |

**Forbidden Synonyms**:
- ❌ "Variable" alone (use "CSS variable" or "token")
- ❌ "Custom property" (technically correct but prefer "token")
- ❌ "Value" (ambiguous)

**Usage**:
```markdown
✅ "Override component token: --ps-card-padding"
✅ "Use semantic token: var(--primary)"
✅ "Primitives: --green-600, --size-4"
❌ "Change the card variable"
❌ "Use custom property for color"
```

---

### Accessibility Terminology

| Term | Meaning | Context | Example |
|------|---------|---------|---------|
| **WCAG** | Web Content Accessibility Guidelines | Standards | "WCAG 2.2 Level AA" |
| **ARIA** | Accessible Rich Internet Applications | Attributes | `aria-label`, `aria-expanded` |
| **Focus indicator** | Visual keyboard focus style | CSS | `:focus-visible` outline |
| **Screen reader** | Assistive technology | Testing | NVDA, VoiceOver, JAWS |
| **Contrast ratio** | Color luminance difference | WCAG metric | "4.5:1 for text" |

**Forbidden Synonyms**:
- ❌ "WAI-ARIA" (use "ARIA")
- ❌ "A11y" in documentation (use "Accessibility", abbreviation OK in code)
- ❌ "Focus ring" (use "Focus indicator")

**Usage**:
```markdown
✅ "Add ARIA label for screen readers"
✅ "Focus indicator must meet 3:1 contrast"
✅ "WCAG 2.2 AA requires keyboard navigation"
❌ "Add WAI-ARIA for a11y"
❌ "Focus ring for keyboard users"
```

---

### Storybook Terminology

| Term | Meaning | File | Example |
|------|---------|------|---------|
| **Story** | Component example/demo | `.stories.jsx` | `Default`, `AllColors` |
| **Showcase** | Multi-variant story | `.stories.jsx` | Story showing all colors |
| **ArgType** | Story control definition | `argTypes: {}` | Control type, description |
| **Autodocs** | Auto-generated docs | `tags: ['autodocs']` | Component documentation |

**Forbidden Synonyms**:
- ❌ "Example" generically (use "Story" in Storybook context)
- ❌ "Demo" generically (use "Story" or "Showcase")
- ❌ "Parameter" (use "ArgType" in Storybook)

**Usage**:
```markdown
✅ "Create Default story and AllColors showcase"
✅ "Add ArgType for color prop"
✅ "Enable Autodocs with tags: ['autodocs']"
❌ "Create examples for all variants"
❌ "Add parameter definition for color"
```

---

## 🔧 File & Structure Terms

### File Types

| Term | Extension | Purpose | Location |
|------|-----------|---------|----------|
| **Template** | `.twig` | Markup structure | `patterns/{level}/{component}/` |
| **Stylesheet** | `.css` | Component styles | `patterns/{level}/{component}/` |
| **Configuration** | `.yml` | Default data | `patterns/{level}/{component}/` |
| **Stories** | `.stories.jsx` | Storybook examples | `patterns/{level}/{component}/` |
| **Documentation** | `README.md` | Component guide | `patterns/{level}/{component}/` |
| **Behavior** | `.js` | JavaScript interaction | `patterns/{level}/{component}/` |

**Forbidden Synonyms**:
- ❌ "View" (use "Template")
- ❌ "Styles" (use "Stylesheet" or "CSS")
- ❌ "Config" (use "Configuration" or "YAML")
- ❌ "Docs" (use "Documentation" or "README")

---

### Component Structure Terms

| Term | Meaning | Context |
|------|---------|---------|
| **5-file structure** | Required component files | `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md` |
| **Props** | Component parameters | Twig variables passed via `with` |
| **Attributes** | HTML attributes object | `attributes` parameter in Twig |
| **Classes** | CSS class list | BEM class construction array |
| **Markup** | HTML structure | Twig output |

**Usage**:
```markdown
✅ "All components must follow 5-file structure"
✅ "Pass props via {% include %} with { ... } only"
✅ "Add classes using attributes.addClass()"
❌ "Component needs 5 files minimum"
❌ "Pass parameters to component"
```

---

## 🎨 CSS & Styling Terms

### Layout & Positioning

| Term | Meaning | Context |
|------|---------|---------|
| **Nesting** | CSS `&` syntax | PostCSS nested selectors |
| **Cascade** | CSS specificity order | Base → Modifiers → States |
| **Selector** | CSS targeting pattern | `.ps-component`, `&__element` |
| **Specificity** | CSS precedence weight | Class (0,0,1,0) vs ID (0,1,0,0) |

**Forbidden Synonyms**:
- ❌ "Inheritance" for cascade (CSS inheritance ≠ cascade)
- ❌ "Targeting" (use "Selector")

---

### States & Interactions

| Term | CSS Pattern | Meaning |
|------|-------------|---------|
| **Hover** | `:hover` | Mouse over state |
| **Active** | `:active` | Pressed/clicked state |
| **Focus** | `:focus` | Keyboard/mouse focus |
| **Focus-visible** | `:focus-visible` | Keyboard-only focus |
| **Disabled** | `:disabled` or `--disabled` | Inactive state |

**Usage**:
```markdown
✅ "Add :focus-visible for keyboard users"
✅ "Hover state uses --primary-hover token"
❌ "Add focus for accessibility"
❌ "Mouse-over style"
```

---

## 🔄 Workflow Terms

| Term | Meaning | Context |
|------|---------|---------|
| **Token-First** | Composition methodology | 4-step cascade workflow |
| **Conformity audit** | Quality checklist | 100-point validation |
| **Refactoring** | Code restructuring | Legacy standardization |
| **Migration** | Version upgrade | Old pattern → new pattern |

**Usage**:
```markdown
✅ "Follow Token-First workflow for composition"
✅ "Run conformity audit before commit"
✅ "Refactor legacy component to new standards"
❌ "Use token approach"
❌ "Check component quality"
```

---

## 🚫 Anti-Patterns (Forbidden Terms)

### Ambiguous Terms

| ❌ Forbidden | ✅ Use Instead | Reason |
|-------------|---------------|---------|
| "Component" alone | Atom/Molecule/Organism | Too generic |
| "Element" alone | Atom or BEM Element | Context-dependent |
| "Part" | Element or Atom | Vague |
| "Piece" | Atom or Element | Informal |
| "Thing" | Specific term | Unprofessional |
| "Stuff" | Specific term | Unprofessional |

### Incorrect Technical Terms

| ❌ Forbidden | ✅ Use Instead | Reason |
|-------------|---------------|---------|
| "Import" | Include/Embed/Extend | JavaScript context |
| "Require" | Include | Node.js context |
| "Class component" | Molecule/Organism | React context |
| "Function component" | Atom | React context |
| "Props drilling" | Props passing | React jargon |

### Misleading Terms

| ❌ Forbidden | ✅ Use Instead | Reason |
|-------------|---------------|---------|
| "Global styles" | Base styles/Tokens | Implies cascade pollution |
| "Helper class" | Utility class | Ambiguous |
| "Magic number" | Hardcoded value | Jargon |
| "Dirty hack" | Workaround/Edge case | Unprofessional |

---

## ✅ Preferred Phrases

### Composition Context

```markdown
✅ "Card molecule composes Badge, Heading, and Button atoms"
✅ "Include atoms via {% include %} with only keyword"
✅ "Override parent tokens in consumer's CSS"
❌ "Card component uses Badge component"
❌ "Import atoms in card"
```

### Token Context

```markdown
✅ "Override component token: --ps-button-padding-x"
✅ "Use semantic token: var(--primary)"
✅ "Define primitives in colors.css"
❌ "Change button variable"
❌ "Use CSS variable for color"
```

### Workflow Context

```markdown
✅ "Follow Token-First cascade: params → utils → tokens → CSS"
✅ "Run conformity audit (100% score required)"
✅ "Refactor to meet new standards"
❌ "Use token approach"
❌ "Check quality"
```

---

## 📊 Terminology Statistics

Based on analysis of 15 instruction files:

| Category | Terms | Primary Files |
|----------|-------|---------------|
| **Atomic Design** | 5 levels | atomic-design.instructions.md |
| **BEM** | 3 components | components.instructions.md |
| **Tokens** | 4 types | css.instructions.md, core.instructions.md |
| **Composition** | 5 methods | composition-token-first.instructions.md |
| **Storybook** | 4 concepts | storybook.instructions.md |
| **Accessibility** | 5 standards | accessibility.instructions.md |

**Consistency**: 95% (current state after P0-P1 improvements)

---

## 🔍 Verification Commands

```bash
# Check for forbidden terms
grep -rE "(import|require|class component)" .github/instructions/*.md

# Check for ambiguous "component" usage
grep -rE "component (uses|includes)" .github/instructions/*.md

# Check for British spelling
grep -rE "(behaviour|colour|organisation)" .github/instructions/*.md

# Check for informal terms
grep -rE "(thing|stuff|piece)" .github/instructions/*.md
```

---

## 🔗 Related Documentation

- **CODE_EXAMPLES_STYLE_GUIDE.md**: Code formatting conventions
- **README.md**: File navigation and structure
- **copilot-instructions.md**: Language policy (French vs English)
- **multi-expert-mode.instructions.md**: French terminology for user-facing guide

---

## 🔄 Maintenance

**When to Update**:
- Adding new component types (beyond 5 atomic levels)
- Introducing new composition patterns
- Deprecating old terminology
- Identifying ambiguous usage in new files

**Process**:
1. Document new term in appropriate section
2. Add examples and anti-patterns
3. Update verification commands if needed
4. Cross-reference related instruction files
5. Commit with changelog entry

---

**Maintainers**: Design System Team  
**Last Updated**: 2025-12-12  
**Next Review**: When adding 5+ new instruction files or detecting terminology conflicts
