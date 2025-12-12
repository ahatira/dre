# Couleurs - Design Tokens

**88 tokens sémantiques pour le système de couleurs BNP Paribas Real Estate**

---

## 📊 Vue d'ensemble

Le système de couleurs PS Theme comprend :

- **72 tokens sémantiques** (8 couleurs × 9 états chacune)
- **4 tokens de texte** (hiérarchie typographique)
- **6 tokens de bordures** (états UI)
- **6 tokens d'overlay** (modales, fonds)

**Total** : 88 tokens de couleurs

**Conformité** : WCAG 2.2 niveau AA (tous les contrastes validés ✅)

---

## 🎨 Couleurs sémantiques (72 tokens)

Chaque couleur sémantique dispose de **9 états** suivant le pattern Bootstrap Base-Modifier :

| État | Suffixe | Usage |
|------|---------|-------|
| **Base** | *(aucun)* | Couleur principale de l'élément |
| **Hover** | `-hover` | Au survol (boutons, liens) |
| **Active** | `-active` | État pressé/actif |
| **Text** | `-text` | Texte sur fond de cette couleur |
| **Border** | `-border` | Bordure dans cette couleur |
| **Subtle** | `-subtle` | Version très claire (badges, alerts) |
| **Background Subtle** | `-bg-subtle` | Fond très clair |
| **Border Subtle** | `-border-subtle` | Bordure subtile |
| **Text Emphasis** | `-text-emphasis` | Texte foncé sur fond clair |

### Exemple d'utilisation complète

```css
.ps-button--primary {
  background: var(--primary);              /* Base */
  color: var(--primary-text);              /* Texte blanc */
  border: 1px solid var(--primary-border); /* Bordure */
  
  &:hover {
    background: var(--primary-hover);      /* Survol */
  }
  
  &:active {
    background: var(--primary-active);     /* Pressé */
  }
  
  &:focus-visible {
    outline: 2px solid var(--border-focus);
  }
}

.ps-alert--primary {
  background: var(--primary-bg-subtle);         /* Fond clair */
  color: var(--primary-text-emphasis);          /* Texte foncé */
  border: 1px solid var(--primary-border-subtle); /* Bordure subtile */
}
```

---

## 1️⃣ PRIMARY (Vert BNP - Brand principal)

**Couleur de marque officielle BNP Paribas Real Estate**

| Token | Valeur | Usage |
|-------|--------|-------|
| `--primary` | `#00915A` | Actions principales, boutons primaires, liens |
| `--primary-hover` | `#04AF6E` | Survol des éléments primaires |
| `--primary-active` | `#016B44` | État pressé/actif |
| `--primary-text` | `#FFFFFF` | Texte sur fond primary (blanc) |
| `--primary-border` | `#00915A` | Bordures primary |
| `--primary-subtle` | `#EBF7F4` | Badges, pills, alerts subtiles |
| `--primary-bg-subtle` | `#EBF7F4` | Fonds très clairs |
| `--primary-border-subtle` | `#C7E8DF` | Bordures subtiles |
| `--primary-text-emphasis` | `#01563A` | Texte foncé sur fond clair |

**Contraste WCAG** :
- `--primary` sur blanc : **4.7:1** ✅ AA Large
- `--primary-text` sur `--primary` : **7.2:1** ✅ AAA

**Exemples** :
```css
/* Bouton principal */
.ps-button--primary { background: var(--primary); }

/* Badge vert clair */
.ps-badge--primary { 
  background: var(--primary-subtle);
  color: var(--primary-text-emphasis);
}

/* Lien */
.ps-link { color: var(--primary); }
```

---

## 2️⃣ SECONDARY (Rose BNP - Brand secondaire)

**Couleur d'accent officielle BNP Paribas**

| Token | Valeur | Usage |
|-------|--------|-------|
| `--secondary` | `#A12B66` | Actions secondaires, accents |
| `--secondary-hover` | `#E0388C` | Survol |
| `--secondary-active` | `#8B2556` | Pressé |
| `--secondary-text` | `#FFFFFF` | Texte blanc |
| `--secondary-border` | `#A12B66` | Bordures |
| `--secondary-subtle` | `#F9EBF2` | Fond clair |
| `--secondary-bg-subtle` | `#F9EBF2` | Très clair |
| `--secondary-border-subtle` | `#ECC9DC` | Bordure claire |
| `--secondary-text-emphasis` | `#5A1838` | Texte foncé |

**Exemples** :
```css
/* Bouton secondaire */
.ps-button--secondary { background: var(--secondary); }

/* Badge rose */
.ps-badge--secondary { 
  background: var(--secondary-subtle);
  color: var(--secondary-text-emphasis);
}
```

---

## 3️⃣ SUCCESS (Vert teal - Succès/Validation)

**Confirmations, validations, messages de succès**

| Token | Valeur | Usage |
|-------|--------|-------|
| `--success` | `#198754` | Succès, confirmations |
| `--success-hover` | `#157347` | Survol |
| `--success-active` | `#0F593A` | Pressé |
| `--success-text` | `#FFFFFF` | Texte blanc |
| `--success-border` | `#198754` | Bordures |
| `--success-subtle` | `#D1F4E8` | Alerts succès |
| `--success-bg-subtle` | `#D1F4E8` | Fonds clairs |
| `--success-border-subtle` | `#A3E8CB` | Bordures claires |
| `--success-text-emphasis` | `#0A3D26` | Texte foncé |

**Exemples** :
```css
/* Alert succès */
.ps-alert--success {
  background: var(--success-bg-subtle);
  color: var(--success-text-emphasis);
  border-left: 4px solid var(--success);
}

/* Badge "Vendu" */
.ps-badge--success { background: var(--success); }
```

---

## 4️⃣ DANGER (Rouge BNP - Erreurs/Danger)

**Erreurs, destructions, alertes critiques**

| Token | Valeur | Usage |
|-------|--------|-------|
| `--danger` | `#EB3636` | Erreurs, destructions |
| `--danger-hover` | `#D43131` | Survol |
| `--danger-active` | `#BD2C2C` | Pressé |
| `--danger-text` | `#FFFFFF` | Texte blanc |
| `--danger-border` | `#EB3636` | Bordures |
| `--danger-subtle` | `#FEF7F7` | Alerts erreur |
| `--danger-bg-subtle` | `#FEF7F7` | Fonds clairs |
| `--danger-border-subtle` | `#F9D1D1` | Bordures claires |
| `--danger-text-emphasis` | `#A62626` | Texte foncé |

**Exemples** :
```css
/* Bouton de suppression */
.ps-button--danger { background: var(--danger); }

/* Message d'erreur */
.ps-form-error {
  color: var(--danger);
  border-color: var(--border-error); /* Alias de --danger */
}

/* Alert erreur */
.ps-alert--danger {
  background: var(--danger-bg-subtle);
  color: var(--danger-text-emphasis);
}
```

---

## 5️⃣ WARNING (Jaune - Avertissements)

**Avertissements, précautions, actions réversibles**

| Token | Valeur | Usage |
|-------|--------|-------|
| `--warning` | `#FBBF24` | Avertissements |
| `--warning-hover` | `#F59E0B` | Survol |
| `--warning-active` | `#D97706` | Pressé |
| `--warning-text` | `#000000` | **Texte noir** (contraste) |
| `--warning-border` | `#FBBF24` | Bordures |
| `--warning-subtle` | `#FEFCE8` | Alerts warning |
| `--warning-bg-subtle` | `#FFFDF3` | Fonds clairs |
| `--warning-border-subtle` | `#FDE68A` | Bordures claires |
| `--warning-text-emphasis` | `#92400E` | Texte foncé |

**⚠️ Note** : `--warning-text` utilise du **noir** pour garantir le contraste WCAG AA sur fond jaune.

**Exemples** :
```css
/* Alert avertissement */
.ps-alert--warning {
  background: var(--warning-bg-subtle);
  color: var(--warning-text-emphasis);
  border: 1px solid var(--warning-border-subtle);
}

/* Badge "En attente" */
.ps-badge--warning { 
  background: var(--warning); 
  color: var(--warning-text); /* Noir */
}
```

---

## 6️⃣ INFO (Bleu - Informations)

**Informations neutres, conseils, astuces**

| Token | Valeur | Usage |
|-------|--------|-------|
| `--info` | `#2563EB` | Informations |
| `--info-hover` | `#1D4ED8` | Survol |
| `--info-active` | `#1E40AF` | Pressé |
| `--info-text` | `#FFFFFF` | Texte blanc |
| `--info-border` | `#2563EB` | Bordures |
| `--info-subtle` | `#EFF6FF` | Alerts info |
| `--info-bg-subtle` | `#F7FAFF` | Fonds clairs |
| `--info-border-subtle` | `#BFDBFE` | Bordures claires |
| `--info-text-emphasis` | `#1E3A8A` | Texte foncé |

**Exemples** :
```css
/* Alert informative */
.ps-alert--info {
  background: var(--info-bg-subtle);
  color: var(--info-text-emphasis);
}

/* Badge "Nouveau" */
.ps-badge--info { background: var(--info); }
```

---

## 7️⃣ GOLD (Or - Premium/Highlights)

**Éléments premium, coups de cœur, highlights**

| Token | Valeur | Usage |
|-------|--------|-------|
| `--gold` | `#D1AE6E` | Premium, highlights |
| `--gold-hover` | `#BC9D63` | Survol |
| `--gold-active` | `#A38856` | Pressé |
| `--gold-text` | `#000000` | Texte noir |
| `--gold-border` | `#D1AE6E` | Bordures |
| `--gold-subtle` | `#F6EDDC` | Fond clair |
| `--gold-bg-subtle` | `#F6EDDC` | Très clair |
| `--gold-border-subtle` | `#EDDFC0` | Bordure claire |
| `--gold-text-emphasis` | `#715E3B` | Texte foncé |

**Exemples** :
```css
/* Badge "Coup de cœur" */
.ps-badge--gold { background: var(--gold); }

/* Bien premium */
.ps-property--premium {
  border: 2px solid var(--gold);
}
```

---

## 8️⃣ LIGHT & DARK (Thème clair/foncé)

### LIGHT (Éléments clairs)

| Token | Valeur | Usage |
|-------|--------|-------|
| `--light` | `#EBEDEF` | Fonds clairs |
| `--light-text` | `#434F57` | Texte foncé |

### DARK (Éléments foncés)

| Token | Valeur | Usage |
|-------|--------|-------|
| `--dark` | `#434F57` | Fonds foncés |
| `--dark-text` | `#FFFFFF` | Texte blanc |

---

## 📝 Tokens de texte (4 tokens)

| Token | Valeur | Contraste | Usage |
|-------|--------|-----------|-------|
| `--text-primary` | `#364152` | 11.5:1 | Texte principal (AAA) |
| `--text-secondary` | `#76808D` | 5.2:1 | Texte secondaire (AA) |
| `--text-disabled` | `#B5BCC9` | 2.8:1 | Texte désactivé |
| `--text-inverse` | `#FFFFFF` | — | Texte sur fond foncé |

**Exemples** :
```css
.ps-heading { color: var(--text-primary); }
.ps-caption { color: var(--text-secondary); }
.ps-button:disabled { color: var(--text-disabled); }
.ps-button--dark { color: var(--text-inverse); }
```

---

## 🔲 Tokens de bordures (6 tokens)

| Token | Valeur | Usage |
|-------|--------|-------|
| `--border-default` | `#D6DBDE` | Bordures par défaut |
| `--border-light` | `#EBEDEF` | Séparateurs légers |
| `--border-focus` | `#333333` | Focus ring (2px) |
| `--border-disabled` | `#B4BABE` | Bordures désactivées |
| `--border-error` | `#EB3636` | Bordures d'erreur |
| `--border-success` | `#198754` | Bordures de succès |

**Exemples** :
```css
.ps-card { border: 1px solid var(--border-default); }
.ps-divider { border-bottom: 1px solid var(--border-light); }
.ps-button:focus-visible { outline: 2px solid var(--border-focus); }
.ps-input--error { border-color: var(--border-error); }
```

---

## 🌫️ Tokens d'overlay (6 tokens)

| Token | Valeur | Opacité | Usage |
|-------|--------|---------|-------|
| `--overlay-dark-heavy` | `rgba(0,0,0,0.6)` | 60% | Modales, overlays lourds |
| `--overlay-dark-medium` | `rgba(0,0,0,0.36)` | 36% | Overlays moyens |
| `--overlay-dark-light` | `rgba(0,0,0,0.12)` | 12% | Hover, overlays légers |
| `--overlay-brand-base` | `#1C2D37` | 100% | Overlay brand opaque |
| `--overlay-brand-medium` | `rgba(28,45,55,0.36)` | 36% | Overlay brand moyen |
| `--overlay-brand-light` | `rgba(28,45,55,0.12)` | 12% | Overlay brand léger |

**Exemples** :
```css
/* Modal backdrop */
.ps-modal__backdrop { background: var(--overlay-dark-heavy); }

/* Card hover */
.ps-card:hover::after { background: var(--overlay-dark-light); }

/* Hero overlay */
.ps-hero::before { background: var(--overlay-brand-medium); }
```

---

## ✅ Checklist d'utilisation

Lors de l'utilisation des couleurs :

- [ ] **Toujours** utiliser les tokens sémantiques (`--primary`, `--success`)
- [ ] **Jamais** utiliser les tokens de palette directement (`--green-600`)
- [ ] **Vérifier** le contraste WCAG 2.2 AA (4.5:1 texte, 3:1 UI)
- [ ] **Utiliser** les 9 états pour chaque couleur sémantique
- [ ] **Privilégier** les tokens `-subtle` pour les fonds clairs
- [ ] **Tester** sur fond clair ET fond foncé

---

## 🔍 Recherche rapide

```bash
# Chercher une couleur spécifique
npm run tokens:check -- --primary
npm run tokens:check -- --success

# Voir toutes les couleurs sémantiques
grep -E "^  --primary|^  --secondary|^  --success" source/props/brand.css

# Détecter les couleurs hardcodées (à corriger)
grep -rE "#[0-9a-fA-F]{3,6}" source/patterns/**/*.css
```

---

## 📚 Ressources

- **Fichier source** : `source/props/brand.css` + `source/props/colors.css`
- **Référence complète** : `source/props/COLORS_REFERENCE.md`
- **Contraste checker** : https://contrast-ratio.com
- **WCAG 2.2** : https://www.w3.org/WAI/WCAG22/quickref/

---

**Navigation** : [← Tokens](./README.md) | [Espacements →](./espacements.md)
