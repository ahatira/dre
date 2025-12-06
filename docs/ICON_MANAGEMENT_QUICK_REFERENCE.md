# SVG Icon Management: Quick Reference
## PS Theme - Decision Guide & Implementation Checklist

**Version**: 1.0.0  
**Date**: 2025-12-06  
**Quick Links**: 
- [Strategic Overview](./ICON_MANAGEMENT_BEST_PRACTICES.md)
- [Technical Implementation](./ICON_MANAGEMENT_TECHNICAL_GUIDE.md)

---

## 🎯 Quick Decision Matrix

### Current State (Font-Based)
```
✅ What you have:
  - Icon font (bnpre-icons, icons-poi)
  - data-icon="name" pattern in Twig
  - Font files (.woff2, .ttf, .eot)
  - Storybook gallery stories
  - ~170 SVG source files in source/assets/icons/

⚠️  Known limitations:
  - Single color per icon
  - Limited animation support
  - Font hinting can blur small sizes
  - No SVG features (gradients, masks)
```

---

## 🚀 Recommended Action Path

### Phase 1: Maintain Current (No changes)
**Status**: ✅ Working well  
**Timeline**: Keep as-is  
**Reason**: Font system works perfectly for Drupal

```bash
# Current workflow
npm run build
# → Compiles with icon font
# → Storybook shows gallery
# → Drupal components use data-icon
```

---

### Phase 2: Add Sprite Support (RECOMMENDED)
**Status**: 📋 Optional enhancement  
**Timeline**: 1-2 days work  
**Benefits**: Modern alternative, future-proof, easy fallback

#### What to Do

**1. Generate Sprite Automatically** (5 min)
```bash
# Copy sprite generator script
# File: scripts/generate-sprite.mjs
# See: ICON_MANAGEMENT_TECHNICAL_GUIDE.md § 1.1

# Add to package.json
"sprite:generate": "node scripts/generate-sprite.mjs"
"build": "npm run sprite:generate && vite build"
```

**2. Create Sprite-Based Icon Template** (10 min)
```twig
{# source/patterns/elements/icon/icon-sprite.twig #}
<svg class="ps-icon" aria-hidden="true">
  <use href="#icon-{{ name }}" />
</svg>
```

**3. Update Storybook** (10 min)
```jsx
// Add new story variant
export const SpriteBasedPreview = {
  render: (args) => {
    // Inject sprite
    // Render with sprite template
  }
};
```

**4. Document & Test** (30 min)
- [ ] Sprite generates correctly (170+ icons)
- [ ] Storybook displays both font and sprite variants
- [ ] Performance comparison (measure file sizes)
- [ ] Accessibility check (WCAG 2.2 AA)

#### Expected Results
```
Current (Font):      ~45 KB (gzipped)
New (Sprite):        ~50-60 KB (gzipped)
Difference:          Small but modern approach
Main benefit:        Foundation for Phase 3
```

---

### Phase 3: Full Migration (OPTIONAL - Future)
**Status**: 🔄 Consider in Drupal 12+  
**Timeline**: 2-3 weeks (when benefits justify)  
**Changes**: Replace font with sprite entirely

#### Would give you:
- ✅ Multi-color icons per SVG
- ✅ Better performance for 170+ icons
- ✅ Full SVG feature support
- ✅ Future-proof technology stack

#### Effort required:
- Update `icon.twig` default behavior
- Remove font declarations from CSS
- Migrate existing Drupal component calls
- Performance testing across browsers

---

## ⚡ Quick Implementation (Phase 2 Only)

### 1. Copy Sprite Generator (2 min)

**File**: `scripts/generate-sprite.mjs`  
👉 See ICON_MANAGEMENT_TECHNICAL_GUIDE.md § 1.1  
👉 Copy entire "Basic Sprite Generator" section

### 2. Update package.json (1 min)

```json
{
  "scripts": {
    "sprite:generate": "node scripts/generate-sprite.mjs",
    "build": "npm run sprite:generate && vite build",
    "watch": "npm run sprite:generate && vite build --watch"
  }
}
```

### 3. Create Icon Sprite Template (3 min)

**File**: `source/patterns/elements/icon/icon-sprite.twig`

```twig
{# Icon using SVG sprite (alternative to font) #}
<svg class="ps-icon" aria-hidden="true">
  <use href="#icon-{{ name }}" />
</svg>
```

### 4. Add Sprite Story (5 min)

**File**: `source/patterns/elements/icon/icon.stories.jsx`

Add at end:
```jsx
export const SpritePreview = {
  render: (args) => {
    // Inject sprite into preview
    if (!document.querySelector('.ps-icon-sprite')) {
      const div = document.createElement('div');
      div.innerHTML = `<svg class="ps-icon-sprite" style="display: none;">
        <!-- Sprite content auto-injected -->
      </svg>`;
      document.body.appendChild(div.firstElementChild);
    }
    
    return `
      <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
        ${iconTwig({ ...args })}
      </div>
    `;
  },
  args: { ...data }
};
```

### 5. Update Build (1 min)

```bash
npm run build
# Now generates both:
# - dist/sprites/icons.svg (new)
# - dist/ps-theme.css (existing with fonts)
# - Storybook previews both options
```

### 6. Verify (5 min)

```bash
# Check sprite was generated
ls -lh dist/sprites/icons.svg

# Run Storybook
npm run watch

# Verify:
# - Default story works (font) ✓
# - New SpritePreview story works ✓
# - Gallery displays all 170 icons ✓
```

---

## 📊 Comparison: Current vs Recommended

| Aspect | Current (Font) | Phase 2 (Font + Sprite) | Phase 3 (Sprite Only) |
|--------|---|---|---|
| **Setup** | Done ✅ | +1 script | +CSS changes |
| **Drupal** | Perfect ✅ | Perfect ✅ | Perfect ✅ |
| **Colors** | Single | Single + Future | Multi-color ✨ |
| **Animation** | Limited | Limited + Future | Full ✨ |
| **Performance** | Good | Good | Better ✨ |
| **Migration** | - | No breaking changes | Planned |
| **Maintenance** | Font generation | Both systems | Sprite only |
| **Browser Support** | IE11 ✓ | IE11 ✓ | Modern only |
| **Recommend?** | ✅ Current | ✅ Do Phase 2 | ⏳ Wait for v2.0 |

---

## 🔍 Testing Checklist (Phase 2)

### Build & Output
- [ ] `npm run build` completes without errors
- [ ] `dist/sprites/icons.svg` exists and contains 170+ symbols
- [ ] `dist/sprites/icons.json` metadata file created
- [ ] Storybook builds successfully
- [ ] No console errors in Storybook preview

### Functionality
- [ ] Font-based icons still render (backward compatibility)
- [ ] Sprite-based icons render correctly
- [ ] Icon gallery shows all icons
- [ ] All sizes work (xs → xxl)
- [ ] All colors work (default → info)
- [ ] Disabled state works

### Accessibility
- [ ] Icons with ariaLabel have proper ARIA attributes
- [ ] Decorative icons have aria-hidden="true"
- [ ] Icons keyboard accessible when in interactive elements
- [ ] Color contrast meets WCAG AA (test with axe)

### Performance
- [ ] Sprite file size < 100 KB
- [ ] Font files still used (backward compatibility)
- [ ] No duplicate HTTP requests
- [ ] Initial page load unaffected

---

## 🐛 Troubleshooting

### Problem: Sprite not generating
```bash
# Check if icons exist
ls source/assets/icons/ | wc -l

# Check for invalid SVG files
find source/assets/icons/ -name "*.svg" -type f | head -5

# Run generator with debug
node scripts/generate-sprite.mjs 2>&1 | head -50
```

### Problem: Icons not displaying in Storybook
```jsx
// Ensure sprite is injected BEFORE rendering
if (!document.querySelector('.ps-icon-sprite')) {
  // Fetch and inject sprite
  const response = await fetch('/dist/sprites/icons.svg');
  const svg = await response.text();
  const temp = document.createElement('div');
  temp.innerHTML = svg;
  document.body.insertBefore(temp.firstElementChild, document.body.firstChild);
}
```

### Problem: Missing icons in sprite
```bash
# Validate sprite contains expected icons
grep -o '<symbol id="icon-[^"]*"' dist/sprites/icons.svg | wc -l

# Verify specific icon
grep 'id="icon-check"' dist/sprites/icons.svg
```

---

## 📈 Metrics to Track (Phase 2)

After implementation, monitor:

```javascript
// Performance monitoring
const metrics = {
  fontFileSize: 45,        // KB, gzipped
  spriteFileSize: 55,      // KB, gzipped
  iconLoadTime: 150,       // ms
  spriteLloadTime: 120,    // ms (improvement!)
  totalIconsManaged: 170,
  spriteCoverage: 100      // % of icons
};

// Accessibility
const a11y = {
  iconsWithAriaLabel: 85,  // %
  decorativeIconsWithHidden: 100,  // %
  wcagAACompliant: true
};
```

---

## 📚 When to Use Font vs Sprite

### Use Font (Current) When:
- ✅ Single color icons only
- ✅ Heavy Drupal integration
- ✅ Need IE11 support
- ✅ Simple icon management
- ✅ Already invested in font workflow

### Use Sprite When:
- ✅ Want modern approach
- ✅ Planning multi-color icons
- ✅ Need SVG animations
- ✅ Prefer CSS features (masks, gradients)
- ✅ Easier to integrate with JavaScript

### Hybrid (Recommended):
- ✅ Font for existing code (backward compatible)
- ✅ Sprite for new components (future-proof)
- ✅ Easy migration path
- ✅ No breaking changes
- ✅ Can deprecate font later

---

## 🎓 Next Steps

### Immediate (Today)
1. Read [Best Practices doc](./ICON_MANAGEMENT_BEST_PRACTICES.md) (15 min)
2. Read [Technical Guide](./ICON_MANAGEMENT_TECHNICAL_GUIDE.md) (30 min)
3. Decide: Phase 2 or stay with Phase 1?

### If Phase 2 (Start This Week)
1. Copy `scripts/generate-sprite.mjs` (5 min)
2. Create sprite template (10 min)
3. Add Storybook story (10 min)
4. Test & validate (15 min)
5. Document in team wiki (10 min)

### If Phase 3 (Plan for Later)
1. Track feedback on sprite performance
2. Measure user impact of multi-color icons
3. Plan migration in Drupal 12 cycle
4. Build migration guide

---

## 🔗 Related Documentation

| Document | Purpose | Read When |
|----------|---------|-----------|
| ICON_MANAGEMENT_BEST_PRACTICES.md | Strategic overview | Planning Phase 2 |
| ICON_MANAGEMENT_TECHNICAL_GUIDE.md | Code examples & implementation | Building Phase 2 |
| source/patterns/elements/icon/README.md | Component documentation | Using in projects |
| docs/ps-design/CHANGELOG.md | Implementation history | Tracking changes |

---

## ✅ Success Criteria

### Phase 2 Complete When:
- [ ] Sprite generation script in place
- [ ] Both font and sprite stories working
- [ ] Storybook displays complete icon gallery
- [ ] Performance measurement documented
- [ ] Team trained on usage
- [ ] README.md updated with sprite info
- [ ] No Drupal components broken
- [ ] Accessibility verified (WCAG AA)

### Go-Live Checklist:
- [ ] Merged to main branch
- [ ] Staging environment tested
- [ ] Performance acceptable
- [ ] No console errors
- [ ] No regressions in existing icons
- [ ] Documentation up-to-date

---

## 💡 Pro Tips

1. **Keep Both Systems**: Hybrid approach is low-risk
2. **Test in Drupal**: Always verify in actual Drupal components
3. **Monitor Performance**: Track metrics before/after
4. **Document Decisions**: Update team wiki with findings
5. **Plan Migration**: Create ticket for Phase 3 (future work)
6. **Communicate**: Let team know about sprite availability

---

**Start with Phase 2 in the next sprint!** 🚀

It's low effort, high value, and prepares your design system for modern icon management without any breaking changes.

---

**Last Updated**: 2025-12-06  
**Maintainer**: Design System Team  
**Status**: Ready for implementation

