# Prompt: Create Molecule Component

**Purpose**: Generate a molecule component with Token-First composition workflow.

---

## 📋 Prompt Template

```
Create a new molecule component: {COMPONENT_NAME}

CONTEXT:
- Project: PS Theme (Drupal 10/11 + Storybook HTML + Vite)
- Location: source/patterns/components/{component}/
- Standards: Atomic Design + BEM + Token-First + WCAG 2.2 AA
- CRITICAL: This is a MOLECULE (composes atoms) - Token-First workflow APPLIES

SPECIFICATION:
Read the design spec at: docs/design/molecules/{component}.md

DEPENDENCIES:
List all atoms this molecule will compose (e.g., button, icon, badge)
Verify each exists in source/patterns/elements/

TOKEN-FIRST WORKFLOW (MANDATORY FOR MOLECULES):
Read: .github/instructions/02-component-development.md (Section 2)

When composing atoms, follow 4-step hierarchy:

STEP 1: Check native params
- Use atom's existing parameters (variant, size, layout, etc.)
- Example: {% include '@elements/button/button.twig' with { variant: 'primary', size: 'lg' } only %}

STEP 2: Check utility classes
- Use Drupal/project utility classes if available
- Example: attributes.addClass('u-padding-4 u-gap-2')

STEP 3: Override tokens ⭐ PREFERRED
- Define molecule-specific tokens in YOUR CSS
- Override parent/child component tokens
- Example in {component}.css:
  .ps-{component} {
    --ps-button-padding-y: var(--size-3);  /* Override button's padding */
    --ps-button-bg: var(--secondary);       /* Override button's color */
  }

STEP 4: Targeted CSS (last resort)
- Only for unique cases that can't use tokens
- Scope to molecule: .ps-{component} .ps-button { ... }

COMPOSITION RULES:
✅ Use {% include %} with 'only' keyword
✅ Use attributes.addClass() to add parent context classes
❌ NEVER modify atom's CSS directly
❌ NEVER use baseClass parameter (removed in v4.0.0)
❌ NEVER duplicate atom styles

WORKFLOW (11 steps + Token-First):

1-2. [Same as atom: READ SPEC, VERIFY TOKENS]

3. PLAN COMPOSITION
   - List atoms to include
   - Identify customization needs (colors, sizes, spacing)
   - Plan token overrides (STEP 3 approach)

4. CREATE 5 FILES
   source/patterns/components/{component}/
   ├── {component}.twig
   ├── {component}.css
   ├── {component}.yml
   ├── {component}.stories.jsx
   └── README.md

5. IMPLEMENT TWIG
   Template with composition:
   
   {#
    * {Component} molecule
    * Composes: button, icon, badge (list all atoms)
    * @param string title - Title text (required)
    * @param object button - Button properties (optional)
    * @param object attributes - HTML attributes (optional)
   #}
   
   {%- set classes = [
     'ps-{component}',
     variant ? 'ps-{component}--' ~ variant : null
   ] -%}
   
   <div {{ attributes.addClass(classes) }}>
     <div class="ps-{component}__content">
       {{ title }}
     </div>
     
     {# Compose button with Token-First STEP 1: native params #}
     {% include '@elements/button/button.twig' with {
       text: button.text|default('Action'),
       variant: button.variant|default('primary'),
       size: button.size|default('md'),
       attributes: create_attribute().addClass('ps-{component}__button')
     } only %}
   </div>

6. IMPLEMENT CSS (Token-First STEP 3)
   
   .ps-{component} {
     /* Molecule base styles */
     display: flex;
     flex-direction: column;
     gap: var(--size-4);
     padding: var(--size-6);
     
     /* Token overrides for composed atoms (STEP 3) */
     --ps-button-padding-y: var(--size-3);
     --ps-button-padding-x: var(--size-5);
     --ps-button-bg: var(--secondary);
     --ps-button-bg-hover: var(--secondary-hover);
     
     &__content {
       font-size: var(--font-size-6);
       color: var(--text-primary);
     }
     
     &__button {
       align-self: flex-start;
       /* Button styles inherited via token overrides above */
     }
     
     &--compact {
       --ps-button-padding-y: var(--size-2);  /* Variant-specific override */
       gap: var(--size-2);
     }
   }

7-11. [Same as atom: YAML, STORYBOOK, README, VALIDATE, AUDIT, COMMIT]

README ADDITIONS FOR MOLECULES:
Add these sections:
- **Composition**: List all atoms included
- **Token Overrides**: Document all token customizations
- **Token-First Steps Used**: Which steps (1-4) are applied

COMMIT FORMAT:
feat(components): Add {component} molecule

- Implement 5-file structure with Token-First composition
- Compose: button, icon, badge (list atoms)
- Token overrides: --ps-button-padding-y, --ps-button-bg (list all)
- Support X variants with Y states
- Real Estate vocabulary in content/examples
- References spec: docs/design/molecules/{component}.md
- Conformity score: X/100

SUCCESS CRITERIA:
✅ All atom dependencies exist and work
✅ Token-First STEP 3 approach used (token overrides in molecule CSS)
✅ NO direct modification of atom CSS files
✅ NO baseClass parameter usage
✅ Composition with {% include %} + 'only'
✅ attributes.addClass() for context classes
✅ Build passes, audit ≥ 90/100
```

---

## 🎯 Real-World Example

**Card Offer Search** (perfect Token-First implementation):

```twig
{# Composes: link, badge, button #}
<article class="ps-card-offer-search">
  {% include '@elements/link/link.twig' with {
    url: offer.url,
    text: offer.title,
    attributes: create_attribute().addClass('ps-card-offer-search__link')
  } only %}
  
  {% include '@elements/badge/badge.twig' with {
    text: offer.category,
    variant: 'primary',
    attributes: create_attribute().addClass('ps-card-offer-search__badge')
  } only %}
</article>
```

```css
.ps-card-offer-search {
  /* Token overrides for children (STEP 3) */
  --ps-link-color: var(--text-primary);
  --ps-link-color-hover: var(--primary);
  --ps-badge-padding: var(--size-2) var(--size-3);
  
  &__link {
    font-weight: var(--font-weight-bold);
  }
}
```

**See**: `source/patterns/components/card-offer-search/` for complete example

---

**Estimated Time**: 4-6 hours  
**Difficulty**: Medium-High  
**Prerequisites**: Read 02-component-development.md (Section 2: Token-First)
