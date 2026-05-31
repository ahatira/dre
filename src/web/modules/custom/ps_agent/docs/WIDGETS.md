# ps_agent — Widget Integration

## Current Implementation (Autocomplete)

The ps_offer module already has autocomplete widgets configured for agent selection:

- **field_primary_agent**: `entity_reference_autocomplete` widget
- **field_secondary_agents**: `entity_reference_autocomplete_tags` widget

These widgets work out-of-the-box with ps_agent entities and provide:
- Search by agent name (display_name field)
- Type-ahead suggestions
- Keyboard navigation
- Mobile-friendly interface

## Adding Inline Entity Form (Optional Enhancement)

To allow creating/editing agents directly from the offer form without leaving the page, you can add the **inline_entity_form** module.

### Installation

```bash
# Add to composer
cd /var/www/html
composer require 'drupal/inline_entity_form:^3.0@RC'

# Enable the module
drush en inline_entity_form -y
```

### Configure Inline Entity Form Widget

1. Navigate to `/admin/structure/types/manage/offer/form-display`

2. For **field_primary_agent**:
   - Change widget to "Inline entity form - Simple"
   - Configure settings:
     - Allow users to add existing entities: Yes
     - Allow users to add new entities: Yes (if agents can be created from offer form)
     - Form mode: default

3. For **field_secondary_agents**:
   - Change widget to "Inline entity form - Complex"
   - Configure similar settings

### Agent Form Configuration

If you want agents to be created inline, ensure the AgentForm is properly configured:

```php
// In src/Form/AgentForm.php
public function form(array $form, FormStateInterface $form_state): array {
  $form = parent::form($form, $form_state);
  
  // Only show essential fields when embedded
  if ($this->getRequest()->query->has('_wrapper_format')) {
    // Hide non-essential fields for inline context
    $form['created']['#access'] = FALSE;
    $form['uid']['#access'] = FALSE;
  }
  
  return $form;
}
```

## Alternative: Entity Browser (Future)

Another option for agent selection is **entity_browser**, which provides:
- Visual grid of agents with avatars
- Advanced filtering
- Bulk selection
- Media library-style interface

This is useful for:
- Visual identification of agents by photo
- Managing many agents at once
- Non-technical users

## Recommendation

For **production use**:
1. Keep the autocomplete widget (already working, fast, accessible)
2. Add inline_entity_form only if users need to create agents from offer form
3. Consider entity_browser for visual selection if UI feedback requests it

The current autocomplete implementation is **sufficient for MVP** and follows Drupal best practices.
