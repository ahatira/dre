# Footer (Organism)

Type: Organism / Collection
Rôle: Pied de page avec pré-footer, colonnes de liens, légaux, social.
Statut: ✅ Stable
Version: 1.0.0

---

## BEM
```
ps-footer
  ps-footer__pre
  ps-footer__main
  ps-footer__columns
  ps-footer__column
  ps-footer__title
  ps-footer__list
  ps-footer__link
  ps-footer__bottom
  ps-footer__legal
  ps-footer__social
  ps-footer__copyright

Modificateurs:
  ps-footer--dark | --light | --with-pre
```

## API
```yaml
name: 'PS Footer'
status: stable
group: organisms
props:
  type: object
  properties:
    preFooter: { type: object, properties: { enabled: { type: boolean, default: false }, content: { type: string } } }
    columns: { type: array, items: { type: object, properties: { title: { type: string }, links: { type: array, items: { type: object, properties: { text: { type: string }, url: { type: string } } } } } } }
    socialLinks: { type: array, items: { type: object, properties: { platform: { type: string }, url: { type: string }, icon: { type: string } } } }
    legalLinks: { type: array, items: { type: object, properties: { text: { type: string }, url: { type: string } } } }
    copyright: { type: string }
    theme: { type: string, enum: ['dark','light'], default: 'light' }
```

## Twig
```twig
<footer class="ps-footer {{ theme ? 'ps-footer--' ~ theme }}" role="contentinfo">
  {% if preFooter.enabled %}
    <div class="ps-footer__pre">{{ preFooter.content }}</div>
  {% endif %}
  <div class="ps-footer__main">
    <div class="ps-footer__columns">
      {% for col in columns %}
        <section class="ps-footer__column">
          <h2 class="ps-footer__title">{{ col.title }}</h2>
          <ul class="ps-footer__list">
            {% for l in col.links %}
              <li class="ps-footer__link">{% include '@ps_theme/ps-link/ps-link.twig' with { text: l.text, url: l.url } %}</li>
            {% endfor %}
          </ul>
        </section>
      {% endfor %}
    </div>
    <div class="ps-footer__bottom">
      <div class="ps-footer__legal">
        {% for l in legalLinks %}
          {% include '@ps_theme/ps-link/ps-link.twig' with { text: l.text, url: l.url } %}
        {% endfor %}
      </div>
      <div class="ps-footer__social" aria-label="Réseaux sociaux">
        {% for s in socialLinks %}
          {% include '@ps_theme/ps-icon/ps-icon.twig' with { name: s.icon, ariaLabel: s.platform } %}
        {% endfor %}
      </div>
      <div class="ps-footer__copyright">{{ copyright }}</div>
    </div>
  </div>
</footer>
```

## Tokens
- Layout.footer, Spacing.section, Colors.neutral

## A11y
- `role="contentinfo"`, titres de section logiques
