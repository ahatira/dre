# Component Development Roadmap

**Based on Mockup Analysis - BNP Paribas Real Estate Homepage**

## 🎯 Development Phases

### **PHASE 0 - Foundation (Already Completed ✅)**
Components that are already implemented and used throughout:
- Button (atom)
- Badge (atom)
- Card (molecule)
- Breadcrumb (molecule)
- Alert (molecule)
- Accordion Item (molecule)
- Accordion (organism)
- Hero (organism)
- Heading, Text, Link, Icon, Image, Avatar, Logo (atoms)

**Status**: 13 components ✅

---

### **PHASE 1 - Homepage MVP (Critical Path)**
**Timeline**: 2-3 weeks  
**Priority**: Must have for homepage launch

#### Week 1: Site Structure
- [ ] **Header** (organism) - Logo, nav menu, search bar, contact CTA, language selector
  - Atoms: Logo, Button, Icon
  - Molecules: Menu Item (nav items with hover/active states)
  
- [ ] **Navigation** (organism) - Main menu with dropdowns
  - Molecules: Menu Item
  - Atoms: Link, Icon

- [ ] **Footer** (organism) - Multi-column layout with links, contact, social
  - Atoms: Logo, Link, Icon, Text

#### Week 2: Search & Hero Enhancement
- [ ] **Input** (atom) - Text input for location, budget, surface
  - With label, placeholder, focus states
  
- [ ] **Select** (atom) - Dropdown for property type, price range, sorting
  - Native or custom component
  
- [ ] **Toggle** (atom) - Buy/Rent switcher in hero
  
- [ ] **Search Bar** (molecule) - Hero search widget
  - Combines: Input, Select, Button, Toggle
  - Responsive stacking on mobile
  
- [ ] **Form Field** (molecule) - Input + label + validation
  - For contact forms, filters

#### Week 3: Content Display
- [ ] **Card Grid** (organism) - 4-col, 3-col, responsive grids
  - Property cards grid (4-col desktop, 2-col mobile)
  - Service cards grid (4-col)
  - Commercial search cards (4-col)
  - News articles grid (3-col)
  
- [ ] **Carousel** (organism) - Featured properties slider
  - Prev/Next buttons
  - Counter (e.g., "1/4")
  - Autoplay optional

**Result**: Fully functional homepage ✅

---

### **PHASE 2 - Advanced Interactions (Secondary)**
**Timeline**: 2 weeks  
**Priority**: Enhance user experience, prepare for search results

- [ ] **Checkbox** + **Radio** groups (molecules)
  - Property amenities checklist
  - Property type selection
  - Filter groups
  
- [ ] **Dropdown** (molecule) - Menu overlay for sorting/filtering
  - Sort options (price, date, relevance)
  - View toggle (grid/list)
  
- [ ] **Pagination** (molecule) - Page navigation
  - Numbers, prev/next, jump to page
  - For search results, article archives
  
- [ ] **Modal** (organism) - Dialog/overlay
  - Contact advisor modal
  - Filters modal for mobile
  - Appointment booking
  
- [ ] **Tabs** (molecule) - Tab navigation
  - Property detail tabs: Description, Photos, Energy, Similar
  
- [ ] **Media Object** (molecule) - Image + text layout
  - Service features (icon + title + desc)
  - Expert profiles (photo + name/role/office)

**Result**: Full property detail page + search results

---

### **PHASE 3 - Data & Utility (Tertiary)**
**Timeline**: 1-2 weeks  
**Priority**: Complete feature set

- [ ] **Data Table** (organism) - Market data, comparisons
  - Sortable columns
  - Filterable data
  - Responsive horizontal scroll
  
- [ ] **List** (organism) - Property list view
  - Alternative to grid view
  - With faceted filters sidebar
  
- [ ] **Tooltip** (molecule) - Contextual hints
  - On form labels, info icons
  
- [ ] **Toast** (molecule) - Temporary notifications
  - Form submission success
  - Action confirmations
  
- [ ] **Menu Item** (molecule) - Navigation link variants
  - With badge, submenu indicator
  
- [ ] **Eyebrow** (atom) - Label above heading
  - Property metadata ("New listing", "Featured")
  
- [ ] **Flag** (atom) - Language flag icon
  - Language selector
  
- [ ] **Chip** (atom) - Removable tag/filter
  - Active filter pills

**Result**: Complete feature-rich website

---

### **PHASE 4 - Layouts & Pages (Templates)**
**Timeline**: 1 week  
**Priority**: Drupal integration

- [ ] **Page** (layout) - Base page template
- [ ] **Node** (layout) - Content node template
- [ ] **Article** (layout) - News/blog article layout
- [ ] **Landing** (layout) - Campaign pages
- [ ] **Grid** (layout) - Multi-column grid
- [ ] **Two Column** (layout) - Content + sidebar
- [ ] **Three Column** (layout) - Left sidebar + content + right sidebar
- [ ] **Error** (layout) - 404/500 pages

#### Complete Pages (for Storybook demos):
- [ ] **Homepage** (full page with all sections)
- [ ] **Property Listing** (search results with filters)
- [ ] **Property Detail** (single property with tabs/gallery)
- [ ] **Article** (news/blog article)
- [ ] **Contact** (contact form + office locator)
- [ ] **About** (company info + team)
- [ ] **Services** (service categories)
- [ ] **Office Finder** (office locations + map)
- [ ] **News List** (article archive)
- [ ] **Search Results** (global search)
- [ ] **404 Error** (not found page)
- [ ] **500 Error** (server error page)

**Result**: Complete theme ready for production

---

## 📋 Critical Dependencies

### **Must exist BEFORE starting**:
1. ✅ Button - Used everywhere
2. ✅ Card - Core layout component
3. ✅ Hero - Homepage foundation
4. ✅ Icon system - Service cards, navigation
5. ✅ Badge - Featured/new indicators
6. ✅ Accordion - FAQ section

### **Must complete BEFORE Phase 2**:
1. Header/Navigation - Site structure
2. Footer - Site structure
3. Input/Select - Search functionality
4. Search Bar - Hero widget
5. Card Grid - Content display

### **Can work in parallel**:
- Different card types (property, article, service, etc.)
- Form components (Input, Select, Checkbox, Radio)
- Utility atoms (Eyebrow, Flag, Chip, Separator)

---

## 🚀 Quick Start Checklist

**Before starting Phase 1**:
- [ ] Read `.github/instructions/02-component-development.md` (workflow)
- [ ] Review `.github/instructions/03-technical-implementation.md` (code standards)
- [ ] Check existing components for patterns (Button, Card, Badge)
- [ ] Run `npm run watch` to start Storybook dev server

**For each component**:
- [ ] Create spec in `docs/design/{level}/{component}.md` if needed
- [ ] Use `npm run generate:pattern` or create 4-file structure
- [ ] Follow Token-First workflow for styling
- [ ] Add Storybook stories with Autodocs
- [ ] Test responsive design (desktop, tablet, mobile)
- [ ] Run `npm run build` validation
- [ ] Update CHANGELOG.md
- [ ] Commit with structured message

---

## 📊 Progress Tracking

```
PHASE 1 (MVP Homepage)
█████░░░░░░░░░░░░░░░ 5/18 components (28%)
  ✅ Hero
  ✅ Card
  ✅ Accordion
  ❌ Header
  ❌ Footer
  ❌ Navigation
  ❌ Input
  ❌ Select
  ❌ Toggle
  ❌ Search Bar
  ❌ Form Field
  ❌ Card Grid
  ❌ Carousel
  + 5 more...

PHASE 2 (Advanced)
░░░░░░░░░░░░░░░░░░░░ 0/15 components (0%)

PHASE 3 (Data & Utility)
░░░░░░░░░░░░░░░░░░░░ 0/12 components (0%)

PHASE 4 (Layouts & Pages)
░░░░░░░░░░░░░░░░░░░░ 0/22 templates/pages (0%)

OVERALL: 13/87 components (15%)
```

---

## 🎯 Next Steps

1. **Start Phase 1 Week 1**: Header, Navigation, Footer
2. **Prepare component specs**: Document exact requirements from mockups
3. **Establish patterns**: Ensure consistency across all components
4. **Test responsive**: Verify mobile/tablet behavior matches mockups
5. **Build Storybook**: Populate with real content examples

---

*Last Updated: December 13, 2025*  
*Based on: BNP Paribas Real Estate Homepage Mockups*
