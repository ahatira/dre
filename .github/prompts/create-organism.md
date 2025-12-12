# Prompt: Create Organism Component

**Purpose**: Generate a large organism component with complex Token-First composition.

---

## 📋 Prompt Template

```
Create a new organism component: {COMPONENT_NAME}

CONTEXT:
- Project: PS Theme (Drupal 10/11 + Storybook HTML + Vite)
- Location: source/patterns/collections/{component}/
- Standards: Atomic Design + BEM + Token-First + WCAG 2.2 AA
- CRITICAL: This is an ORGANISM (composes molecules + atoms) - Token-First workflow APPLIES

SPECIFICATION:
Read the design spec at: docs/design/organisms/{component}.md

DEPENDENCIES:
List all molecules and atoms this organism will compose
Verify each exists in source/patterns/

ORGANISM-SPECIFIC CONSIDERATIONS:

1. LAYOUT MANAGEMENT
   - Use CSS Grid or Flexbox for complex layouts
   - Define layout tokens at organism level:
     --ps-{organism}-gap: var(--size-6);
     --ps-{organism}-columns: 3;
     --ps-{organism}-padding: var(--size-8);
   
2. RESPONSIVE BEHAVIOR
   - Mobile-first approach
   - Breakpoint tokens from source/props/media.css
   - Layout shifts at appropriate breakpoints
   
3. MULTIPLE CHILD COMPONENTS
   - Follow Token-First STEP 3 for EACH child
   - Organize token overrides by child component:
     /* Button overrides */
     --ps-button-variant: var(--secondary);
     
     /* Card overrides */
     --ps-card-padding: var(--size-4);
     --ps-card-gap: var(--size-3);

4. COMPLEX STATES
   - Loading states
   - Empty states
   - Error states
   - Consider progressive disclosure

WORKFLOW (Extended 11 steps):

1-3. [Same as molecule: READ SPEC, VERIFY TOKENS, PLAN COMPOSITION]

   Additional for organism:
   - Draw component hierarchy tree
   - Plan responsive breakpoints
   - Identify state variations

4. CREATE 5 FILES
   source/patterns/collections/{component}/
   ├── {component}.twig
   ├── {component}.css
   ├── {component}.yml
   ├── {component}.stories.jsx
   └── README.md

5. IMPLEMENT TWIG
   
   {#
    * {Component} organism
    * Composes: card, button, badge, icon (list ALL)
    * @param array items - Collection items (required)
    * @param string layout - grid|list|masonry (optional, default: grid)
    * @param object filters - Filter options (optional)
    * @param object attributes - HTML attributes (optional)
   #}
   
   {%- set classes = [
     'ps-{component}',
     layout ? 'ps-{component}--' ~ layout : null,
     filters ? 'ps-{component}--with-filters' : null
   ] -%}
   
   <div {{ attributes.addClass(classes) }}>
     
     {# Filters section (if provided) #}
     {% if filters %}
       <div class="ps-{component}__filters">
         {% for filter in filters %}
           {% include '@components/filter/filter.twig' with {
             label: filter.label,
             options: filter.options,
             attributes: create_attribute().addClass('ps-{component}__filter')
           } only %}
         {% endfor %}
       </div>
     {% endif %}
     
     {# Items grid/list #}
     <div class="ps-{component}__items">
       {% for item in items %}
         {% include '@components/card/card.twig' with {
           title: item.title,
           description: item.description,
           image: item.image,
           cta: item.cta,
           attributes: create_attribute().addClass('ps-{component}__item')
         } only %}
       {% endfor %}
     </div>
     
     {# Load more / Pagination #}
     {% if pagination %}
       <div class="ps-{component}__pagination">
         {% include '@components/pagination/pagination.twig' with {
           current: pagination.current,
           total: pagination.total,
           attributes: create_attribute().addClass('ps-{component}__pagination-inner')
         } only %}
       </div>
     {% endif %}
     
   </div>

6. IMPLEMENT CSS (Complex Token-First)
   
   .ps-{component} {
     /* Layout base */
     display: flex;
     flex-direction: column;
     gap: var(--size-8);
     padding: var(--size-6);
     
     /* Token overrides for ALL children (grouped by component) */
     
     /* Filter overrides */
     --ps-filter-gap: var(--size-3);
     --ps-filter-padding: var(--size-2) var(--size-4);
     
     /* Card overrides */
     --ps-card-padding: var(--size-6);
     --ps-card-gap: var(--size-4);
     --ps-card-border: var(--border-light);
     --ps-card-shadow: var(--shadow-2);
     --ps-card-shadow-hover: var(--shadow-3);
     
     /* Button overrides (in cards) */
     --ps-button-variant: var(--primary);
     --ps-button-size: var(--size-4);
     
     /* Pagination overrides */
     --ps-pagination-gap: var(--size-2);
     
     &__filters {
       display: flex;
       gap: var(--size-4);
       flex-wrap: wrap;
       padding-bottom: var(--size-4);
       border-bottom: 1px solid var(--border-light);
     }
     
     &__items {
       display: grid;
       grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
       gap: var(--size-6);
       
       @media (--viewport-sm) {
         grid-template-columns: 1fr;
       }
       
       @media (--viewport-lg) {
         gap: var(--size-8);
       }
     }
     
     &__pagination {
       display: flex;
       justify-content: center;
       padding-top: var(--size-6);
     }
     
     /* Layout variants */
     &--list {
       .ps-{component}__items {
         grid-template-columns: 1fr;
       }
     }
     
     &--masonry {
       .ps-{component}__items {
         grid-template-rows: masonry;
       }
     }
     
     /* State modifiers */
     &--loading {
       opacity: 0.6;
       pointer-events: none;
     }
     
     &--empty {
       .ps-{component}__items {
         display: flex;
         justify-content: center;
         align-items: center;
         min-height: 400px;
       }
     }
   }

7. CREATE YAML (Complex data)
   
   Use @faker-js/faker for realistic datasets:
   
   items:
     - title: "{{ faker.location.streetAddress() }}"
       description: "{{ faker.commerce.productDescription() }}"
       price: "{{ faker.commerce.price() }}€"
       image:
         src: "{{ faker.image.urlLoremFlickr({ category: 'building' }) }}"
         alt: "{{ faker.location.city() }}"
       cta:
         text: "View Details"
         url: "#"
     # Repeat for 6-12 items
   
   layout: "grid"
   
   filters:
     - label: "Property Type"
       options:
         - "Apartment"
         - "House"
         - "Office"
     - label: "Price Range"
       options:
         - "< 200k€"
         - "200k-500k€"
         - "> 500k€"
   
   pagination:
     current: 1
     total: 5

8-11. [Same as molecule: STORYBOOK, README, VALIDATE, AUDIT, COMMIT]

README ADDITIONS FOR ORGANISMS:
- **Layout Management**: Document responsive behavior
- **Child Components**: List all molecules/atoms composed
- **Token Overrides**: Complete mapping by child component
- **State Variations**: Document loading, empty, error states
- **Performance Considerations**: Lazy loading, pagination strategies

STORYBOOK ADDITIONS:
Add state stories:
- Loading: With loading state
- Empty: No items scenario
- Error: Error state with message

COMMIT FORMAT:
feat(collections): Add {component} organism

- Implement 5-file structure with complex Token-First composition
- Compose: {list all molecules/atoms}
- Token overrides: {list all child token customizations}
- Responsive layouts: mobile|tablet|desktop
- States: loading, empty, error
- Support X layout variants
- Real Estate context with Faker.js
- References spec: docs/design/organisms/{component}.md
- Conformity score: X/100

SUCCESS CRITERIA:
✅ All dependencies exist (molecules + atoms)
✅ Token-First STEP 3 applied to ALL children
✅ Responsive layouts work at all breakpoints
✅ All states render correctly (loading, empty, error)
✅ Complex data handled gracefully (6-12+ items)
✅ Build passes, audit ≥ 90/100
✅ Performance acceptable (lazy loading if needed)
```

---

**Estimated Time**: 6-8 hours  
**Difficulty**: High  
**Prerequisites**: All composed molecules/atoms must exist first
