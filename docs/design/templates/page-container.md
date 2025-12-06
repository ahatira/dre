# Template: ps-page-container

Purpose
- Wraps global page content with responsive max-width, gutters, optional sidebar and pre-footer areas.
- Provides skip link support and consistent main landmark structure.

BEM
- Block: `ps-page-container`
- Elements: `__inner`, `__main`, `__sidebar`, `__breadcrumb`, `__pre-footer`
- Modifiers: `--fluid`, `--narrow`, `--default`, `--wide`, `--full`

Props (component.yml)
```yaml
name: ps-page-container
props:
  width:
    type: string
    enum: [narrow, default, wide, full]
    default: default
  fluid:
    type: boolean
    default: false
  with_skip_link:
    type: boolean
    default: true
  skip_link_label:
    type: string
    default: "Skip to content"
  breadcrumb:
    type: any
    required: false
  sidebar:
    type: any
    required: false
  pre_footer:
    type: any
    required: false
  content:
    type: any
    required: true
```

Twig
```twig
<div
  {{ attributes.addClass(
    'ps-page-container',
    'ps-page-container--' ~ (width|default('default')),
    fluid ? 'ps-page-container--fluid'
  ) }}
>
  {% if with_skip_link %}
    <a href="#main" class="ps-skip-link">{{ skip_link_label|default('Skip to content') }}</a>
  {% endif %}

  <div class="ps-page-container__inner">
    {% if breadcrumb %}
      <nav class="ps-page-container__breadcrumb" aria-label="Breadcrumb">
        {{ breadcrumb }}
      </nav>
    {% endif %}

    <main id="main" class="ps-page-container__main">
      {{ content }}
    </main>

    {% if sidebar %}
      <aside class="ps-page-container__sidebar" aria-label="Sidebar">
        {{ sidebar }}
      </aside>
    {% endif %}
  </div>

  {% if pre_footer %}
    <section class="ps-page-container__pre-footer" aria-label="Pre-footer">
      {{ pre_footer }}
    </section>
  {% endif %}
</div>
```

Tokens
- Layout: `layout.container.max_width`, `layout.breakpoints.*`, `layout.grid.gutter`
- Spacing: `spacing.scale.*`
- Colors: background and text from `colors.*`
- Transitions: `transitions.duration.medium`, `transitions.easing.standard`

SCSS (example)
```scss
.ps-page-container {
  margin-inline: auto;
  padding-inline: var(--size-6);
  max-width: var(--size-max-site-width);

  &--narrow { max-width: var(--size-content-3); }
  &--default { max-width: var(--size-max-site-width); }
  &--wide { max-width: var(--size-max-site-width); }
  &--full { max-width: 100%; }
  &--fluid { padding-inline: var(--size-4); }

  &__inner { display: grid; gap: var(--size-6); }
  &__main { min-width: 0; }
  &__sidebar { min-width: 0; }
  &__pre-footer { margin-top: var(--size-10); }
}

@media (min-width: var(--size-tablet)) {
  .ps-page-container__inner {
    grid-template-columns: 1fr auto;
  }
}
```

Accessibility
- Provides a skip link to `#main` when `with_skip_link` is true.
- Uses landmarks: `nav` for breadcrumb, `main` for primary content, `aside` for sidebar.
- Maintain logical heading order inside `content` and `sidebar` slots.

Usage
```twig
{% include '@ps/ps-page-container/ps-page-container.twig' with {
  width: 'default',
  breadcrumb: breadcrumb,
  content: content,
  sidebar: sidebar,
  pre_footer: pre_footer
} only %}
```
