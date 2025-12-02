# ps-text

Atom: Semantic text component for paragraphs and inline content

## API

- `text` (string) — Text content (required)
- `size` (string) — xs | sm | md (default) | lg | xl | xxl
- `color` (string) — default | primary | secondary | success | info | warning | danger | dark | light
- `tag` (string) — p | span | div (default: p)
- `align` (string) — left | center | right (default: left)
- `muted` (boolean) — Secondary tone (overrides color to muted)
- `strong` (boolean) — Bold emphasis

Back-compat: `variant` (body | small | large) maps to `size` (md | sm | lg).

## Sizes

- **xs**: 12px — Footnotes, microcopy
- **sm**: 14px — Captions, helper text
- **md**: 16px — Standard body text (default)
- **lg**: 18px — Lead paragraphs
- **xl**: 20px — Introductions
- **xxl**: 24px — Hero intros / statements

## Colors (Semantic)

- **default**: Base text color
- **primary**, **secondary**
- **success**, **info**, **warning**, **danger**
- **dark**, **light**

## Usage

```twig
{# Standard paragraph #}
{% include '@elements/text/text.twig' with {
  text: 'This is a paragraph.',
  size: 'md'
} %}

{# Lead text (lg + strong) #}
{% include '@elements/text/text.twig' with {
  text: 'Introduction paragraph with emphasis.',
  size: 'lg',
  strong: true
} %}

{# Small muted caption #}
{% include '@elements/text/text.twig' with {
  text: '* Information not contractual',
  size: 'sm',
  muted: true
} %}

{# Centered body text #}
{% include '@elements/text/text.twig' with {
  text: 'Centered content',
  size: 'md',
  align: 'center'
} %}

{# Inline span #}
{% include '@elements/text/text.twig' with {
  text: 'Inline text',
  tag: 'span',
  size: 'md'
} %}
```

## Design Tokens (used)

- `--font-sans`
- `--font-size--1` (12px) · `--font-size-0` (14px) · `--font-size-1` (16px) · `--font-size-2` (18px) · `--font-size-3` (20px) · `--font-size-4` (24px)
- `--font-weight-400`, `--font-weight-700`
- `--leading-tight`, `--leading-snug`, `--leading-normal`, `--leading-relaxed`, `--leading-loose`
- `--gray-900` (default), `--gray-600` (muted)
- `--size-4` (margin-bottom)

## Typography Scale

```
xxl: 24px / 1.75 / 400
xl:  20px / 1.625 / 400
lg:  18px / 1.625 / 400
md:  16px / 1.5   / 400
sm:  14px / 1.375 / 400
xs:  12px / 1.25  / 400

muted: gray-600
strong: font-weight-700
```

## Accessibility

- Use semantic HTML tags (`p` for paragraphs)
- Ensure text contrast ≥ 4.5:1 (WCAG AA)
- Muted text maintains sufficient contrast with --gray-600
- Limit line length to ~65–75 characters for readability

## Use Cases

- **body**: Main content paragraphs, descriptions
- **small**: Captions, disclaimers, metadata, helper text
- **large**: Lead paragraphs, introductions, callouts
- **muted**: Secondary information, timestamps, labels
- **strong**: Inline emphasis, important statements
