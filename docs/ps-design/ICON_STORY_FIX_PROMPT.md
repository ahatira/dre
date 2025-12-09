# Generic Prompt: Fix Icon Stories in Components

**Purpose**: Standardize and correct icon story implementations across all components using the Icon.twig component and data-icon CSS system.

**Version**: 1.0.0  
**Last Updated**: 2025-12-09

---

## 🎯 Context

The PS Theme icon system uses a **two-layer architecture**:

1. **Icon.twig Component**: Reusable Twig wrapper that renders a semantic `<i>` element with icon rendering
   - Renders ONLY `<i class="ps-icon" data-icon="name">` (no text content)
   - Supports: `icon` (required), `position` (start|end), `ariaLabel`, `ariaHidden`, `attributes`
   - Use in Twig templates and Storybook stories

2. **data-icon CSS System**: Low-level CSS primitive for rendering icons on ANY HTML element
   - Applied directly to button, span, link, heading, div, p, etc.
   - Supports text content (automatic spacing via flexbox + gap)
   - Supports `data-icon-position="start"` (::before, default) or `"end"` (::after)
   - Color inherited from parent via `currentColor`

**Key Resource**: `.github/instructions/icon-system.instructions.md` (authoritative reference)

---

## 📋 Checklist: Icon Story Implementation

### Before Starting

- [ ] Read component spec: `docs/design/{level}/{component}.md`
- [ ] Check component's current icon usage: search for `icon`, `data-icon`, `Icon.twig` in `.stories.jsx`
- [ ] Verify 141 icons available in registry: `source/patterns/documentation/icons-registry.json`
- [ ] Review icon-system.instructions.md two-layer architecture

### Implementation Rules

**Icon.twig Usage** (in stories):
- ✅ Use `{% include '@elements/icon/icon.twig' with { icon: 'name' } %}` in Twig templates
- ✅ Use `iconTwig({ icon: 'name' })` in Storybook stories (JavaScript)
- ✅ Supports: `icon` (required string), `position` ('start'|'end'), `ariaLabel`, `ariaHidden`
- ❌ Do NOT use Icon.twig for text content (renders `<i>` only)
- ❌ Do NOT apply `data-icon-position` to Icon.twig output

**data-icon System** (direct HTML):
- ✅ Apply `data-icon="name"` directly to ANY HTML element
- ✅ Use for icon + text combinations: `<button data-icon="check">Confirm</button>`
- ✅ Use `data-icon-position="end"` for trailing icon position (renders via ::after)
- ✅ Icon automatically inherits color from parent
- ✅ Automatic spacing via flexbox + gap (0.375em default)
- ❌ Never include `icon-` prefix in icon name

**Story Structure**:
```jsx
export const StoryName = {
  render: () => `
    <!-- Compare Icon.twig vs data-icon approaches -->
    <!-- Show real-world examples -->
    <!-- Include code snippets for documentation -->
  `,
  parameters: {
    docs: {
      description: {
        story: 'Clear explanation of what this story demonstrates.'
      }
    }
  }
}
```

### Common Icon Story Patterns

**Pattern 1: Icon + Text (data-icon preferred)**
```html
<!-- Preferred: Direct data-icon on element -->
<button data-icon="arrow-right" data-icon-position="end">
  Proceed
</button>

<!-- Code snippet for docs -->
<code>&lt;button data-icon="arrow-right" data-icon-position="end"&gt;Proceed&lt;/button&gt;</code>
```

**Pattern 2: Icon Only (either approach)**
```jsx
// Icon.twig approach
${iconTwig({ icon: 'check', ariaLabel: 'Confirmed' })}

// data-icon approach
<button data-icon="search" aria-label="Search"></button>

<!-- Code snippets -->
<code>&lt;button data-icon="search" aria-label="Search"&gt;&lt;/button&gt;</code>
```

**Pattern 3: Color Inheritance (data-icon only)**
```html
<span style="color: var(--success);" data-icon="check">
  Verified
</span>

<code>&lt;span style="color: var(--success);" data-icon="check"&gt;Verified&lt;/span&gt;</code>
```

**Pattern 4: Position Control (both approaches)**
```jsx
// Icon.twig with position
${iconTwig({ icon: 'check', position: 'start' })}
${iconTwig({ icon: 'arrow-right', position: 'end' })}

// data-icon with position
<span data-icon="check" data-icon-position="start">Approved</span>
<button data-icon="next" data-icon-position="end">Next</button>

<!-- Code snippets -->
<code>{% include '@elements/icon/icon.twig' with { icon: 'check', position: 'start' } %}</code>
<code>&lt;button data-icon="next" data-icon-position="end"&gt;Next&lt;/button&gt;</code>
```

---

## 🛠️ Step-by-Step Fix Process

### Step 1: Identify Icon Usage
Run grep search to find icon references:
```bash
grep -r "icon" source/patterns/{level}/{component}/ --include="*.stories.jsx"
```

Common issues to detect:
- ❌ Hardcoded icon names without checking sprite
- ❌ Using Icon.twig with text content (`{{ caller() }}`)
- ❌ Missing position parameter when needed
- ❌ Incorrect HTML structure for data-icon usage
- ❌ Icon names with `icon-` prefix
- ❌ Missing aria-labels for icon-only buttons

### Step 2: Create/Update Story
Structure your icon stories following this pattern:

```jsx
export const [StoryName] = {
  render: () => `
    <div style="display: flex; gap: var(--size-8); flex-direction: column;">
      <!-- Optional: Info box explaining the story -->
      <div style="background: var(--info-bg-subtle); padding: var(--size-4); border-radius: var(--radius-2); border-left: 4px solid var(--info);">
        <p style="margin: 0; color: var(--info); font-size: var(--font-size-1);">
          💡 Explanation of what this story demonstrates
        </p>
      </div>

      <!-- Examples grid -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-5);">
        <!-- Example 1 -->
        <div style="...">
          <h4>Example Title</h4>
          <!-- Rendered example -->
          <!-- Code snippet -->
        </div>
        
        <!-- Example 2 -->
        <div style="...">
          <h4>Example Title</h4>
          <!-- Rendered example -->
          <!-- Code snippet -->
        </div>
      </div>

      <!-- Optional: Key differences or summary -->
      <div style="background: var(--info-bg-subtle); padding: var(--size-4); border-radius: var(--radius-2); border-left: 4px solid var(--info);">
        <strong>Key Points:</strong>
        <ul>
          <li>Point 1</li>
          <li>Point 2</li>
        </ul>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Clear, concise description of what this story demonstrates.'
      }
    }
  }
}
```

### Step 3: Validate Icons
For each icon reference, verify:
- [ ] Icon name exists in `source/patterns/documentation/icons-registry.json`
- [ ] Icon name has NO `icon-` prefix
- [ ] Icon is rendered via `data-icon="name"` attribute or `iconTwig({ icon: 'name' })`
- [ ] Correct layer used (Icon.twig for wrapper, data-icon for direct HTML)
- [ ] Accessibility proper (aria-label on icon-only, aria-hidden on decorative)

### Step 4: Test & Build
```bash
npm run build          # Full validation
npm run watch         # Visual check at http://localhost:6006
```

Verify:
- [ ] Build passes (linting, formatting, icons, vite)
- [ ] Storybook loads without errors
- [ ] Story renders correctly in browser
- [ ] Icons display properly (no 404 or rendering issues)
- [ ] Code snippets are accurate and scannable

### Step 5: Commit
```bash
git add source/patterns/{level}/{component}/
git commit -m "docs(storybook): Fix icon stories in {component}

- Correct Icon.twig vs data-icon usage
- Add [specific changes: e.g., 'position parameter examples', 'accessibility labels']
- Simplified [what was simplified: e.g., 'story layout', 'example count']
- All 141 icons verified in registry"
```

---

## 📚 Real-World Examples (Reference)

See `source/patterns/elements/icon/icon.stories.jsx` for complete implementation:
- **WithPositioning**: Compares Icon.twig vs data-icon, shows position control
- **DataIconSystem**: 6 real-world examples (button, badge, link, heading, icon-only, color)
- **ColorInheritance**: Shows color inheritance via currentColor
- **Gallery**: All 141 icons browsable with copy-to-clipboard

---

## ❌ Zero-Tolerance Rules

These will ALWAYS be rejected:

- ❌ Icon.twig with text content: `{{ caller() }}` → Icons render `<i>` only
- ❌ Icon.twig with `data-icon-position` attribute → Position is CSS-only primitive
- ❌ Icon names with prefix: `icon-check` → Use `check` (system auto-prefixes)
- ❌ Arrow functions in Twig: `filter(v => v)` → Use ternary operators
- ❌ Missing accessibility: Icon-only buttons need `aria-label`
- ❌ Hardcoded color values in icon CSS → Use semantic tokens
- ❌ Stories without clear, scannable layout → Use grid + gap for organization
- ❌ Code snippets that don't match rendered examples → Keep docs & code in sync

---

## 🔍 Troubleshooting

**Issue**: Icon not rendering (blank or broken)
- **Check**: Icon name in registry? Run `npm run icons:check -- check` (replace with actual name)
- **Check**: Correct attribute? Use `data-icon` not `icon` or `data-icon-name`
- **Check**: Build passed? Run `npm run build` to regenerate icons.css

**Issue**: Icon.twig shows in Storybook but looks wrong
- **Check**: Using `iconTwig()` function (JavaScript) not `{% include %}` (Twig) in story render?
- **Check**: All required props? `icon` is required
- **Check**: Position parameter correct? `position: 'start'` or `'end'` only

**Issue**: Text + icon alignment wrong
- **Check**: Using `data-icon` directly on element (not Icon.twig)?
- **Check**: Parent has `display: flex` or `display: inline-flex`?
- **Check**: Gap property set? Default 0.375em, customize via `--ps-icon-gap` CSS var

**Issue**: Icon color not matching parent
- **Check**: Using `data-icon` (inherits via currentColor)?
- **Check**: Parent element has `color` property set?
- **Check**: CSS specificity issue? Icon inherits from direct parent only

---

## 📖 Key Resources

- **Icon Instructions**: `.github/instructions/icon-system.instructions.md` (complete architecture)
- **Icon Component**: `source/patterns/elements/icon/icon.twig` (semantic `<i>` wrapper)
- **Icon Registry**: `source/patterns/documentation/icons-registry.json` (all 141 icons)
- **Icon CSS**: `source/props/icons.css` (auto-generated rendering rules)
- **Icon Stories (Reference)**: `source/patterns/elements/icon/icon.stories.jsx` (complete example)
- **Token Checker**: `npm run tokens:check -- --token-name` (verify design tokens)

---

## 🎓 Quick Decision Matrix

**When to use Icon.twig**:
- [ ] Building a Twig template/story with icon logic
- [ ] Need reusable component with parameters
- [ ] Want encapsulated, semantic markup
- [ ] Example: Icon atom story, documentation wrapper

**When to use data-icon**:
- [ ] Icon + text content together
- [ ] Applying icon to native HTML (button, link, span, heading)
- [ ] Icon inherits color from parent
- [ ] Want simplest possible markup
- [ ] Example: Button with trailing icon, badge, link with icon

**Never mix both**:
- ❌ Don't use Icon.twig to wrap data-icon element
- ❌ Don't apply data-icon-position to Icon.twig output
- ❌ Don't use Icon.twig for text content

---

## 📝 How to Use This Prompt

### 🎯 Scenario 1: Fix Icon Stories in an Existing Component

**Your request to Copilot**:
```
Je dois corriger les stories des icônes dans le composant Button.
Les stories actuelles n'utilisent pas correctement Icon.twig vs data-icon.

Utilise le prompt: docs/ps-design/ICON_STORY_FIX_PROMPT.md

Composant: source/patterns/elements/button/
Problèmes actuels:
- Icon stories mélangent Icon.twig et data-icon sans distinction claire
- Pas d'exemples de data-icon direct sur button
- Code snippets manquants

Fais une fix complète avec:
1. Clarifier Icon.twig (composant <i>) vs data-icon (système CSS)
2. Ajouter exemples réels: button avec icon + text, icon trailing, etc.
3. Inclure code snippets pour chaque exemple
4. Vérifier build + commit structuré
```

### 🎯 Scenario 2: Create Icon Stories for a New Component

**Your request to Copilot**:
```
Je crée un nouveau composant Alert et j'ai besoin des stories pour les icônes.

Utilise le prompt: docs/ps-design/ICON_STORY_FIX_PROMPT.md

Composant: source/patterns/components/alert/

Patterns à couvrir:
- Alert avec icon (warning, danger, success, info)
- Icon + text côte à côte
- Color inheritance (icon adopte la couleur de l'alert)
- Position du icon (start par défaut)

Utilise la structure de:
source/patterns/elements/icon/icon.stories.jsx (comme référence)

Inclure:
- 4-5 examples dans une grid
- Code snippets HTML/Twig
- Explication de Icon.twig vs data-icon
```

### 🎯 Scenario 3: Quick Icon Story Template

**Your request to Copilot**:
```
Génère une story template pour les icônes (réutilisable).

Utilise la section "Story Structure" du prompt: docs/ps-design/ICON_STORY_FIX_PROMPT.md

La story doit montrer:
1. Icon.twig approach
2. data-icon approach
3. Code snippets pour chaque
4. Info box explicative

Fais un template JSX réutilisable.
```

---

## 📝 Template Prompt for AI

Copy-paste this exact template and customize `{placeholders}`:

```
Je dois corriger/créer les stories des icônes pour le composant {COMPONENT_NAME}.

Utilise le prompt standardisé: docs/ps-design/ICON_STORY_FIX_PROMPT.md

**Composant**: source/patterns/{LEVEL}/{COMPONENT_NAME}/

**Problèmes/Objectifs**:
- {ISSUE 1: e.g., "Icon stories mélangent Icon.twig et data-icon sans distinction"}
- {ISSUE 2: e.g., "Pas d'exemples de data-icon direct sur l'élément"}
- {ISSUE 3: e.g., "Accessibility labels manquants"}

**Patterns à couvrir**:
- {PATTERN 1: e.g., "Button avec icon + text trailing"}
- {PATTERN 2: e.g., "Icon-only avec aria-label"}
- {PATTERN 3: e.g., "Color inheritance via currentColor"}

**Validation requise**:
✅ Icon.twig et data-icon clairement séparés dans les exemples
✅ Code snippets (Twig et HTML) pour chaque exemple
✅ Pas de préfixe "icon-" dans les noms
✅ aria-label sur les éléments icon-only
✅ Build pass: npm run build
✅ Commit structuré

**Référence**: source/patterns/elements/icon/icon.stories.jsx (implémentation complète)
```

**Exemple réel avec Button**:
```
Je dois corriger les stories des icônes pour le composant Button.

Utilise le prompt standardisé: docs/ps-design/ICON_STORY_FIX_PROMPT.md

**Composant**: source/patterns/elements/button/button.stories.jsx

**Problèmes**:
- Stories actuelles n'expliquent pas la différence Icon.twig vs data-icon
- Exemples manquants: button avec icon trailing (position="end")
- Code snippets pas assez détaillés

**Patterns à couvrir**:
- Button avec icon leading (position par défaut)
- Button avec icon trailing (data-icon-position="end")
- Icon + text spacing automatique
- Color inheritance du button

**Validation requise**:
✅ Deux approches claires: Icon.twig vs data-icon direct
✅ Code snippets pour Twig et HTML
✅ Exemples visuels avec grid layout
✅ Build pass + commit structuré
```

---

## ✅ Checklist: After Using Prompt

After Copilot implements the fix:

- [ ] Stories load in Storybook without errors
- [ ] Icon.twig and data-icon properly documented with clear examples
- [ ] Code snippets are accurate (match rendered output)
- [ ] All icon names exist in registry (grep in `icons-registry.json`)
- [ ] No `icon-` prefix in icon names
- [ ] Accessibility: aria-label on icon-only elements
- [ ] Build passes: `npm run build`
- [ ] Commit message follows format: `docs(storybook): Fix icon stories in {component}`
- [ ] Changes pushed to branch

If anything fails → refer back to **Troubleshooting** section above

---

**Maintainers**: Design System Team  
**Contact**: See project README for support channels  
**Last Updated**: 2025-12-09
