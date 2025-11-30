# ps-text

Atom: Typography for paragraphs and text content

## API

- `text` (string) - Text content **required**
- `variant` (string) - body|small|large (default: body)
- `tag` (string) - p|span|div (default: p)
- `align` (string) - left|center|right (default: left)
- `muted` (boolean) - Secondary text color (default: false)
- `strong` (boolean) - Bold weight for emphasis (default: false)

## Text Variants

- **body**: 16px - Standard paragraph text (default)
- **small**: 14px - Captions, helper text, footnotes
- **large**: 18px - Lead paragraphs, emphasized content

## BEM

- Block: `ps-text`
- Modifiers:
  - Size: `--small`, `--large` (body is default so no modifier class)
  - State: `--muted`, `--strong`
  - Alignment: `--align-center`, `--align-right` (left is default so no modifier class)

## Usage

```twig
{# Standard paragraph #}
{% include '@ps_theme/text/text.twig' with { 
  text: 'This is a paragraph.'
} %}

{# Lead text (large + strong) #}
{% include '@ps_theme/text/text.twig' with { 
  text: 'Introduction paragraph with emphasis.',
  variant: 'large',
  strong: true
} %}

{# Small muted caption #}
{% include '@ps_theme/text/text.twig' with { 
  text: '* Information not contractual',
  variant: 'small',
  muted: true
} %}

{# Centered body text #}
{% include '@ps_theme/text/text.twig' with { 
  text: 'Centered content',
  align: 'center'
} %}

{# Inline span #}
{% include '@ps_theme/text/text.twig' with { 
  text: 'Inline text',
  tag: 'span'
} %}
```

## Tokens

Uses:
- `--font-sans` (BNPP Sans)
- `--font-size-0` (14px - small)
- `--font-size-1` (16px - body)
- `--font-size-2` (18px - large)
- `--font-weight-400` (regular)
- `--font-weight-700` (bold - strong)
- `--leading-normal` (1.5 - body)
- `--leading-snug` (1.375 - small)
- `--leading-relaxed` (1.625 - large)
- `--gray-900` (#1F2A33 - default text)
- `--gray-600` (#6B7780 - muted)
- `--size-4` (16px - margin-bottom)

## Typography Scale

```
large: 18px / 1.625 / 400
body:  16px / 1.5 / 400
small: 14px / 1.375 / 400

muted: gray-600 (#6B7780)
strong: font-weight-700
```

## Accessibility

- Use semantic HTML tags (`p` for paragraphs)
- Ensure text contrast ≥ 4.5:1 (WCAG AA)
- Muted text maintains sufficient contrast with --gray-600
- Limit line length to ~65-75 characters for readability

## Use Cases

- **body**: Main content paragraphs, descriptions
- **small**: Captions, disclaimers, metadata, helper text
- **large**: Lead paragraphs, introductions, callouts
- **muted**: Secondary information, timestamps, labels
- **strong**: Inline emphasis, important statements
