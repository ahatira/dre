# Prompt: Create Atom Component

> **⚠️ NOTE (December 2025)**: This prompt may reference old 5-file structure.  
> **CURRENT STANDARD**: **4-file structure** (.twig .css .yml .stories.jsx - README.md removed).  
> **Reference**: See `.github/instructions/02-component-development.md` for current workflow.

**Purpose**: Generate a complete atom component following PS Theme v4.0.0 standards.

---

## 📋 Prompt Template

```
Create a new atom component: {COMPONENT_NAME}

CONTEXT:
- Project: PS Theme (Drupal 10/11 + Storybook HTML + Vite)
- Location: source/patterns/elements/{component}/
- Standards: Atomic Design + BEM + Token-First + WCAG 2.2 AA
- Instructions: .github/instructions/ (01 Principles → 02 Workflow → 03 Technical → 04 QA)

SPECIFICATION:
Read the design spec at: docs/design/atoms/{component}.md

WORKFLOW (11 steps from 02-component-development.md):

1. READ SPEC
   - Parse docs/design/atoms/{component}.md
   - List all required props, variants, states
   - Identify dependencies (other atoms/icons)

2. VERIFY TOKENS
   - Check all required tokens exist in source/props/
   - List missing tokens (if any) - DO NOT CREATE, document need in README
   - Use: npm run tokens:check -- --token-name

3. CREATE 5 FILES
   Required structure:
   source/patterns/elements/{component}/
   ├── {component}.twig
   ├── {component}.css
   ├── {component}.yml
   ├── {component}.stories.jsx
   └── README.md

4. IMPLEMENT TWIG
   CRITICAL RULES:
   - Header comment with @param for ALL props
   - All defaults: {%- set prop = prop|default('value') -%}
   - Classes with ternary + null: condition ? 'class' : null
   - ❌ NEVER: filter(v => v), .map(), .includes() (Drupal incompatible)
   - Minimal markup: Default values = NO modifier classes in output
   - Real Estate context (addresses, property types, prices)

5. IMPLEMENT CSS
   MANDATORY:
   - ALL values from tokens (ZERO hardcoded: no #colors, no 16px, no 150ms)
   - Nesting with &: .ps-{component} { &__element { } &--modifier { } }
   - Cascade order: Base → Elements → Modifiers → States
   - Semantic colors ONLY (--primary, --secondary, --success, --danger, --warning, --info, NOT --green-600)
   - Focus-visible on ALL interactives
   - Component-scoped variables (Layer 2): --ps-{component}-{property}: var(--global-token);

6. CREATE YAML
   - Real Estate realistic data (use @faker-js/faker examples)
   - All required props defined
   - Meaningful defaults

7. CREATE STORYBOOK
   CRITICAL:
   - tags: ['autodocs'] MANDATORY in export default
   - Import: import componentTwig from './{component}.twig';
   - Render: render: (args) => componentTwig(args)
   - ❌ NO React/JSX
   - ArgTypes: Categorize (Content | Appearance | Behavior | Link | Accessibility | Layout)
   - Stories: Default + Showcases (AllColors, AllSizes, UseCases)
   - ❌ NO individual variant stories (Primary, Secondary, Small...)

8. CREATE README
   Required sections:
   - Usage (Twig example)
   - Props (table: name | type | default | description)
   - BEM Structure (tree format)
   - Design Tokens (list all tokens used)
   - Accessibility (WCAG AA checklist)
   - Examples (3+ real-world use cases)

9. VALIDATE BUILD
   Run: npm run build
   Must pass with 0 errors

10. RUN AUDIT
    Use 100-point conformity checklist from 04-quality-assurance.md
    Minimum score: 90/100 for production

11. COMMIT
    Format:
    feat(elements): Add {component} component
    
    - Implement 5-file structure (twig, css, yml, stories, README)
    - Support X variants with Y states
    - Full Autodocs with categorized argTypes
    - Real Estate context in examples
    - References spec: docs/design/atoms/{component}.md
    - Conformity score: X/100

UPDATE CHANGELOG:
Add entry to docs/ps-design/CHANGELOG.md with implementation details

REFERENCE COMPONENTS:
Study these perfect implementations:
- Button: source/patterns/elements/button/ (complete states, nesting)
- Badge: source/patterns/elements/badge/ (semantic colors, pill variant)
- Avatar: source/patterns/elements/avatar/ (minimal markup, adaptive sizing)

SUCCESS CRITERIA:
✅ All 5 files created and named correctly
✅ Build passes (npm run build)
✅ Conformity audit ≥ 90/100
✅ Storybook renders without errors
✅ All tokens used (zero hardcoded values)
✅ WCAG 2.2 AA compliant
✅ Real Estate context throughout
```

---

## 🎯 Usage Example

Replace `{COMPONENT_NAME}` with actual component:

```
Create a new atom component: input

CONTEXT:
- Project: PS Theme (Drupal 10/11 + Storybook HTML + Vite)
- Location: source/patterns/elements/input/
[...rest of prompt...]
```

---

**Estimated Time**: 3-4 hours  
**Difficulty**: Medium  
**Prerequisites**: Read 01-core-principles.md, 02-component-development.md
