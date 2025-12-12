# Token-First Composition Workflow - Documentation Update

**Date**: 2025-12-12  
**Version**: 3.1.0  
**Status**: ✅ COMPLETE

---

## 📊 Executive Summary

**What changed**: Introduced and documented the **Token-First Composition Workflow** as a core principle for all component composition in PS Theme (Molecules, Organisms, Templates, Pages).

**Why**: To establish a consistent, maintainable approach to customizing parent/child components without breaking base implementations or creating specificity wars.

**Impact**: All future component development (Molecules+) must follow this 4-step cascade workflow.

---

## 🎯 Core Principle

> **When a component composes other components** (include, embed, extends), follow the **Token-First cascade workflow** to respect design specs without breaking parent components or duplicating code.

### The 4-Step Token-First Cascade

```
┌─────────────────────────────────────────┐
│ Need: Component styling customization   │
└───────────────┬─────────────────────────┘
                │
                ▼
┌──────────────────────────────────────────┐
│ STEP 1: Does parent have native params?  │  ← Check first
└───────┬──────────────────────────────────┘
    YES │ NO
    ✅ STOP │
            │
┌───────────▼─────────────────────────────┐
│ STEP 2: Utility class exists?           │  ← Then check helpers
└───────┬─────────────────────────────────┘
    YES │ NO
    ✅ STOP │
            │
┌───────────▼─────────────────────────────┐
│ STEP 3: Parent/child expose tokens?     │  ← ⭐ PREFERRED
└───────┬─────────────────────────────────┘
    YES │ NO
        ▼
    Override tokens in consumer's CSS
        │
    ✅ STOP
            │
            ▼
┌────────────────────────────────────────┐
│ STEP 4: Targeted CSS override          │  ← Last resort
│ & .ps-parent__element { ... }          │
└────────────────────────────────────────┘
        │
    ✅ STOP
```

---

## 📁 Files Created

### 1. New Instruction File (Primary Documentation)

**File**: `.github/instructions/composition-token-first.instructions.md`

**Content** (2,500+ words):
- Core principle definition
- Applicability by Atomic level (Atoms excluded, Molecules+ included)
- Complete 4-step workflow with examples
- Decision tree diagram
- Anti-patterns to avoid
- Best practices (token discovery, naming, organization, documentation)
- Real-world example (Card Offer Search)
- Pre-implementation checklist

**Frontmatter**:
```yaml
applyTo:
  - "source/patterns/components/**/*"
  - "source/patterns/collections/**/*"
  - "source/patterns/layouts/**/*"
  - "source/patterns/pages/**/*"
priority: CRITICAL
related:
  - css.instructions.md
  - components.instructions.md
  - atomic-design.instructions.md
  - card-inheritance.instructions.md
```

---

## 📝 Files Updated

### 2. Main Instruction Hub

**File**: `.github/copilot-instructions.md`

**Changes**:
- ✅ Updated version: 3.0.0 → 3.1.0
- ✅ Added Token-First to documentation structure with 🔥 NEW badge
- ✅ Updated Project At-a-Glance with applicability notes
- ✅ Enhanced Quick Decision Tree with Token-First references
- ✅ Added Zero Tolerance Rule: "Modifying parent component CSS directly"

---

### 3. Atomic Design Instructions

**File**: `.github/instructions/atomic-design.instructions.md`

**Changes**:
- ✅ Added prominent Token-First section at top of Composition Rules
- ✅ Updated Rule 1 (Atoms): Clarified Token-First does NOT apply
- ✅ Updated Rule 2 (Molecules): Added Token-First requirement + CSS example
- ✅ Updated Rule 3 (Organisms): Added Token-First requirement + CSS example
- ✅ Updated Step 3 (Composition Strategy): Added token override question
- ✅ Added CSS examples showing token override patterns

**Key Addition**:
```markdown
### ⚡ Token-First Workflow for Composition

**📘 Comprehensive Documentation**: See `composition-token-first.instructions.md`

**Applies to**: Molecules, Organisms, Templates, Pages  
**Does NOT apply to**: Atoms (autonomous components)
```

---

### 4. Component Structure Standards

**File**: `.github/instructions/components.instructions.md`

**Changes**:
- ✅ Updated version: 3.0.0 → 3.1.0
- ✅ Added "Core Principle: Token-First Composition" section at top
- ✅ Added `related:` frontmatter with composition-token-first.instructions.md
- ✅ Updated CSS Structure section with 4-tier token organization:
  - STEP 3: Override parent tokens
  - STEP 3: Override child tokens
  - Own component tokens
  - Targeted overrides (last resort)

**Key Addition**:
```css
.ps-component {
  /* ═══ STEP 3: Override Parent/Child Tokens (Token-First) ═══ */
  --ps-parent-padding-x: var(--size-6);
  --ps-button-size: var(--size-6);
  
  /* ═══ Own tokens ═══ */
  --ps-component-padding-y: var(--size-3);
}
```

---

### 5. CSS Standards

**File**: `.github/instructions/css.instructions.md`

**Changes**:
- ✅ Updated version: 3.0.0 → 3.1.0
- ✅ Added "Token-First Composition Workflow" section at top
- ✅ Added `related:` frontmatter with composition-token-first.instructions.md
- ✅ Updated Layer 2 section with composing components pattern
- ✅ Added real example (Card Offer Search overriding Card tokens)

**Key Addition**:
```markdown
## 🎯 Token-First Composition Workflow

**Example** (Card Offer Search overriding Card tokens):

```css
.ps-card-offer-search {
  /* STEP 3: Override parent (Card) tokens */
  --ps-card-padding-x: var(--size-6);
  
  /* STEP 3: Override child (Badge, Button) tokens */
  --ps-badge-font-size: var(--font-size-0);
}
```
```

---

### 6. Card Inheritance Pattern

**File**: `.github/instructions/card-inheritance.instructions.md`

**Changes**:
- ✅ Updated version: 3.0.0 → 3.1.0
- ✅ Added "Core Principle: Token-First Inheritance" section at top
- ✅ Updated changelog with v3.1.0 entry
- ✅ Added `related:` frontmatter with composition-token-first.instructions.md
- ✅ Completely rewrote Section 4 (CSS Integration Strategy):
  - New 4.1: CSS Architecture with Token-First Pattern
  - New 4.2: Token Discovery Before Writing CSS
  - Renumbered 4.3: CSS Token Scope Rules (preserved original content)
- ✅ Updated Table of Contents with "⭐ Updated with Token-First" badge

**Key Addition**:
```markdown
### 🎯 Token-First Workflow for Card Inheritance

**MANDATORY**: All Card-based components MUST follow the **4-step Token-First cascade**.

**Quick reference**:
1. Check Card native params → 2. Check utility classes → 3. Override tokens ⭐ → 4. Targeted CSS
```

---

## 📊 Documentation Coverage

| File | Status | Version | Changes |
|------|--------|---------|---------|
| **composition-token-first.instructions.md** | ✅ CREATED | 1.0.0 | Full workflow documentation (2,500+ words) |
| **copilot-instructions.md** | ✅ UPDATED | 3.1.0 | Core principle reference, decision tree updates |
| **atomic-design.instructions.md** | ✅ UPDATED | - | Composition rules with Token-First |
| **components.instructions.md** | ✅ UPDATED | 3.1.0 | CSS structure with 4-tier token organization |
| **css.instructions.md** | ✅ UPDATED | 3.1.0 | Token-First section + Layer 2 composing pattern |
| **card-inheritance.instructions.md** | ✅ UPDATED | 3.1.0 | Complete Section 4 rewrite with Token-First |

**Total documentation**: ~5,000 words across 6 files

---

## 🔍 Real-World Example

**Component**: Card Offer Search (`source/patterns/components/card-offer-search/`)

**Before** (problematic patterns):
```css
.ps-card-offer-search {
  /* ❌ Hardcoded values */
  padding: 30px 24px;
  
  /* ❌ Duplicating Card styles */
  border: 1.5px solid #EBEDEF;
  
  /* ❌ Direct CSS overrides first */
  & .ps-card__content {
    padding: 30px 24px;
  }
}
```

**After** (Token-First):
```css
.ps-card-offer-search {
  /* ✅ STEP 3: Override Card tokens (PREFERRED) */
  --ps-card-padding-x: var(--size-6);  /* 24px */
  --ps-card-padding-y: var(--size-7);  /* 30px */
  --ps-card-border-width: 1.5px;
  --ps-card-border-color: var(--gray-200);
  
  /* ✅ STEP 3: Override child tokens */
  --ps-badge-font-size: var(--font-size-0);
  --ps-button-size: var(--size-6);
  
  /* ✅ Own tokens */
  --ps-card-offer-search-title-size: var(--font-size-1);
  
  /* ✅ STEP 4: Targeted CSS (only if necessary) */
  @media (min-width: 768px) {
    & .ps-card__media {
      flex: 0 0 33.6%;  /* Specific Figma requirement */
    }
  }
}
```

**Benefits**:
- ✅ No Card CSS modification
- ✅ All tokens from props/ (no hardcoded values)
- ✅ Clear hierarchy (parent → child → own → targeted)
- ✅ Maintainable and predictable

---

## 🎓 Learning Resources

### For Developers

1. **Start here**: `.github/instructions/composition-token-first.instructions.md`
2. **Quick reference**: `.github/copilot-instructions.md` (decision tree)
3. **Real example**: `source/patterns/components/card-offer-search/card-offer-search.css`

### For AI Agents

**When generating components**:
1. Read: `composition-token-first.instructions.md` (CRITICAL for Molecules+)
2. Discover tokens: `grep -r "--ps-{component}-" source/patterns/`
3. Follow 4-step cascade in CSS
4. Document overrides with comments

**Pre-implementation checklist**:
- [ ] Identified parent component and its tokens
- [ ] Checked utility classes availability
- [ ] Reviewed parent CSS tokens (grep search)
- [ ] Reviewed child atoms tokens (if composing atoms)
- [ ] Determined override strategy (STEP 3 preferred)
- [ ] Organized CSS in proper sections
- [ ] Added documentation comments

---

## 🚀 Next Steps

### Immediate (Complete)

- ✅ Created `composition-token-first.instructions.md`
- ✅ Updated all core instruction files
- ✅ Updated main `copilot-instructions.md`
- ✅ Verified Card Offer Search implementation

### Short-term (Recommended)

- [ ] Review all existing Molecules+ components for conformity
- [ ] Update component README files with Token-First references
- [ ] Add Token-First examples to Storybook documentation stories
- [ ] Create video tutorial or workshop for team

### Long-term (Ongoing)

- [ ] Audit all 87 components during implementation
- [ ] Maintain Token-First patterns in code reviews
- [ ] Gather feedback and refine workflow if needed

---

## 📈 Impact Assessment

### Benefits

1. **Consistency**: All components follow same customization pattern
2. **Maintainability**: Token overrides are predictable and scoped
3. **Performance**: No specificity wars or cascading issues
4. **Developer Experience**: Clear decision tree reduces guesswork
5. **AI Assistance**: Explicit rules enable accurate code generation

### Risks Mitigated

1. ❌ **Parent component breakage**: Eliminated (no direct CSS modification)
2. ❌ **Specificity wars**: Reduced (tokens have no specificity)
3. ❌ **Code duplication**: Prevented (reuse parent tokens)
4. ❌ **Hardcoded values**: Blocked (tokens enforce design system)
5. ❌ **Inconsistent patterns**: Standardized (4-step workflow)

---

## 📞 Support

**Questions or Issues?**
- Review: `.github/instructions/composition-token-first.instructions.md`
- Check: `copilot-instructions.md` Quick Decision Tree
- Example: `source/patterns/components/card-offer-search/`
- Contact: Design System Team

---

**Maintainers**: Design System Team  
**Status**: Production-ready  
**Effective**: 2025-12-12 onward  
**Applies to**: All Molecules, Organisms, Templates, Pages (NOT Atoms)
