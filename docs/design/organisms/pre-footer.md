# Pre-Footer (Organism)

**Niveau Atomic Design** : Organism / Footer Section  
**Catégorie** : CTAs & Links  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Section avant le footer avec appels à l'action, liens utiles, ou newsletter. Variantes de grille (2/3/4 colonnes), thème clair/foncé.

---

## 🏗️ Structure BEM

```html
<section class="ps-pre-footer ps-pre-footer--3col ps-pre-footer--light" aria-label="Liens utiles">
  <div class="ps-pre-footer__col">
    <h3 class="ps-pre-footer__title">Besoin d'aide ?</h3>
    <p class="ps-pre-footer__text">Contactez nos experts.</p>
    <a class="ps-button ps-button--primary" href="#">Nous contacter</a>
  </div>
  <div class="ps-pre-footer__col">
    <h3 class="ps-pre-footer__title">Ressources</h3>
    <ul class="ps-pre-footer__links">
      <li><a class="ps-link" href="#">FAQ</a></li>
      <li><a class="ps-link" href="#">Guides</a></li>
    </ul>
  </div>
  <div class="ps-pre-footer__col">
    <h3 class="ps-pre-footer__title">Newsletter</h3>
    <form class="ps-pre-footer__form">
      <label class="ps-label" for="pf-email">Email</label>
      <input class="ps-field" id="pf-email" type="email" placeholder="vous@exemple.com" />
      <button class="ps-button ps-button--secondary" type="submit">S'inscrire</button>
    </form>
  </div>
</section>
```

### Classes BEM

```
ps-pre-footer                              // Block
  ps-pre-footer__col                       // Column
  ps-pre-footer__title                     // Column title
  ps-pre-footer__text                      // Column text
  ps-pre-footer__links                     // Links list
  ps-pre-footer__form                      // Newsletter form

Modificateurs :
  ps-pre-footer--2col|--3col|--4col        // Grid columns
  ps-pre-footer--light|--dark              // Theme
```

---

## 📐 Props (Component API)

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Pre-Footer'
status: stable
group: organisms
description: 'CTA/links pre-footer section.'

props:
  type: object
  properties:
    columns: { type: number, enum: [2,3,4], default: 3 }
    theme: { type: string, enum: ['light','dark'], default: 'light' }
    cols:
      type: array
      items:
        type: object
        properties:
          title: { type: string }
          content: { type: string }
        required: ['title','content']
    attributes:
      type: Drupal\\Core\\Template\\Attribute
```

---

## 🔧 Template Twig

```twig
{% set columns = columns|default(3) %}
{% set theme = theme|default('light') %}
{% set classes = ['ps-pre-footer', 'ps-pre-footer--' ~ columns ~ 'col', 'ps-pre-footer--' ~ theme] %}

<section {{ attributes.addClass(classes) }} aria-label="Liens utiles">
  {% for c in cols %}
    <div class="ps-pre-footer__col">
      <h3 class="ps-pre-footer__title">{{ c.title }}</h3>
      {{ c.content|raw }}
    </div>
  {% endfor %}
</section>
```

---

## 🎨 Styles SCSS

```scss
.ps-pre-footer {
  display: grid; gap: var(--size-5);
  grid-template-columns: repeat(3, 1fr);
  padding: var(--size-6) 0;

  &--2col { grid-template-columns: repeat(2, 1fr); }
  &--3col { grid-template-columns: repeat(3, 1fr); }
  &--4col { grid-template-columns: repeat(4, 1fr); }

  &--dark { background: var(--gray-900); color: var(--white); }

  @media (max-width: var(--size-tablet)) { & { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: var(--size-mobile)) { & { grid-template-columns: 1fr; } }
}
```

---

## ♿ Accessibilité

- Titres de colonnes clairs.
- Labels pour champs newsletter.

---

## 🧪 Exemples d'usage

```twig
{% include '@ps_theme/ps-pre-footer/ps-pre-footer.twig' with {
  columns: 3,
  theme: 'light',
  cols: [
    { title: 'Besoin d\'aide ?', content: '<p>Contactez nos experts. <a class="ps-button ps-button--primary" href="#">Nous contacter</a></p>' },
    { title: 'Ressources', content: '<ul class="ps-pre-footer__links"><li><a class="ps-link" href="#">FAQ</a></li><li><a class="ps-link" href="#">Guides</a></li></ul>' },
    { title: 'Newsletter', content: '<form class="ps-pre-footer__form"><label class="ps-label" for="pf-email">Email</label><input class="ps-field" id="pf-email" type="email" placeholder="vous@exemple.com" /><button class="ps-button ps-button--secondary" type="submit">S\'inscrire</button></form>' }
  ]
} %}
```

---

## 📚 Ressources

- Composition: atoms (link, button, label, field)
- Tokens: spacing, colors
