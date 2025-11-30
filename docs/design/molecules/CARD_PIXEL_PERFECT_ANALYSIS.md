# 📊 ANALYSE PIXEL PERFECT DES MAQUETTES CARD

**Date** : 2025-11-30  
**Statut** : Documentation de référence pour implémentation pixel perfect  
**Fichiers concernés** : `card.twig`, `card.css`, `card.yml`, `card.stories.jsx`

---

## 🔍 PHASE 1 : ANALYSE GLOBALE DE COHÉRENCE

### Observations Transversales

#### 1. **Typographie Commune**
- **Tags/Labels** : Bleu (#0288D1 ou similaire), padding horizontal visible, uppercase
- **Dates** : Gris clair, uppercase, petite taille
- **Titres** : Noir/dark gray, bold (700), ~18-20px
- **Descriptions** : Gris moyen, regular (400), ~14-16px, line-height ~1.5
- **Links/CTAs** : Vert BNP (#00915A), avec icône flèche →

#### 2. **Espacement & Structure**
- **Padding content** : ~20-24px (var(--size-5/6))
- **Gap entre éléments** : ~12-16px (var(--size-3/4))
- **Border-radius** : ~8px (var(--radius-4))
- **Shadow** : Légère sur hover, plus prononcée sur cards avec interactions

#### 3. **Patterns de Layout**
- **Vertical** : Image top, content bottom (News, Publications)
- **Horizontal** : Image left (~40-50%), content right (Product horizontal, Studies)
- **Sans image** : Content only avec icône (Solutions)

---

## 📋 PHASE 2 : ANALYSE DÉTAILLÉE PAR MAQUETTE

### 🗞️ MAQUETTE 1 : NEWS CARDS

#### Structure Visuelle
```
┌─────────────────────────────┐
│                             │
│        IMAGE 16:9           │
│      (aspect-ratio)         │
│                             │
├─────────────────────────────┤
│  [TAG LABEL]  DATE          │
│                             │
│  News title                 │
│                             │
│  Lorem ipsum dolor sit...   │
│                             │
│  Lire la suite →            │
└─────────────────────────────┘
```

#### Mesures Précises Détectées
- **Image** : aspect-ratio 16:9 (non 4:3 comme implémenté !)
- **Tag Label** : 
  - Background : #0288D1 (bleu)
  - Padding : 4px 12px
  - Font-size : 12px
  - Text-transform : uppercase
  - Border-radius : 2px
- **Date** :
  - Color : #9AA6B2 (gray-400)
  - Font-size : 12px
  - Text-transform : uppercase
  - Margin-left : 8px du tag
- **Title** :
  - Font-size : 18px (--font-size-2, pas --font-size-3 !)
  - Font-weight : 700
  - Color : #1F2A33 (dark)
  - Margin-top : 16px
- **Description** :
  - Font-size : 14px (--font-size-0)
  - Line-height : 1.5 (21px)
  - Color : #6B7780 (gray-600)
  - Margin-top : 12px
- **Link** :
  - Color : #00915A (green)
  - Font-size : 14px
  - Font-weight : 500
  - Icon : arrow-right (→)
  - Margin-top : 16px

#### 🚨 Problèmes Identifiés
1. ❌ Aspect-ratio image : **16:9** dans maquette, pas 4:3
2. ❌ Tag + Date layout : manque structure flex horizontale
3. ❌ Title font-size : **18px** (--font-size-2), pas 20px
4. ❌ Link style : devrait être link component, pas button
5. ❌ Padding content : semble **20px**, pas 24px

---

### 🏢 MAQUETTE 2 : PRODUCTS CARDS

#### Structure Visuelle (Vertical Compact)
```
┌──────────────────┐
│     IMAGE        │  ♡ (favorite icon top-right)
│    (square)      │
├──────────────────┤
│ Price • m²       │
│ Product title    │
│ 📍 Location      │
│                  │
│ Consulter... →   │
└──────────────────┘
```

#### Structure Visuelle (Horizontal Detailed)
```
┌────────┬──────────────────────────────────┐
│ Image  │ 👁 Already viewed  [EXCLUSIVITY]│
│ (with  │                                  │
│ carousel│ Rent Offices MADRID Barrio...   │
│  nav)  │ 611.3 m²                         │
│        │ 📍 28010 MADRID                  │
│        │                                  │
│        │ 20 000 € HT/HC/m²/an            │
│        │                        View... → │
└────────┴──────────────────────────────────┘
```

#### Mesures Précises Détectées

**Version Compact (Vertical)**
- **Image** : aspect-ratio 1:1 (square)
- **Favorite icon** : 
  - Position : absolute top-right
  - Margin : 12px
  - Size : 24px
  - Color : white avec border ou bg semi-transparent
- **Price/Surface line** :
  - Font-size : 16px (--font-size-1)
  - Font-weight : 700
  - Color : #1F2A33
  - Format : "Price • m²" avec bullet separator
- **Title** :
  - Font-size : 16px
  - Font-weight : 700
  - Color : #1F2A33
  - Margin-top : 8px
- **Location** :
  - Icon : pin-map (📍)
  - Font-size : 14px
  - Color : #6B7780
  - Margin-top : 8px
- **Link** :
  - Font-size : 14px
  - Color : #00915A
  - Margin-top : 12px

**Version Horizontal (Detailed)**
- **Image** : 
  - Width : ~35-40%
  - Aspect-ratio : 4:3
  - Carousel navigation : prev/next arrows overlay
- **Status badges** :
  - "Already viewed" : icon + text, gray
  - "EXCLUSIVITY" : badge gold/yellow
  - Position : top-right du content
- **Content padding** : 24px
- **Price** :
  - Font-size : 20px
  - Font-weight : 700
  - Format : "20 000 € HT/HC/m²/an"
- **Link bottom-right** : aligned right

#### 🚨 Problèmes Identifiés
1. ❌ **Favorite icon** : manque complètement
2. ❌ **Price/Surface format** : manque bullet separator "•"
3. ❌ **Carousel navigation** : pas implémenté
4. ❌ **Status badges** : "Already viewed" + "Exclusivity" manquent
5. ❌ **Price formatting** : manque structure spécifique
6. ❌ **Layout horizontal** : image devrait être 35% max, pas 40%

---

### 📚 MAQUETTE 3 : PUBLICATIONS CARDS

#### Structure Visuelle
```
┌────────────────┐
│                │
│     IMAGE      │
│   (portrait    │
│     3:4)       │
│                │
├────────────────┤
│ 📍 Location    │
│                │
│ DATE           │
│                │
│ Publication    │
│ title          │
│                │
│ Lorem ipsum... │
│                │
│ Lire l'étude → │
└────────────────┘
```

#### Mesures Précises Détectées
- **Image** : aspect-ratio **3:4** (portrait) ✅ Correct
- **Icon + Location** :
  - Icon : pin-map (📍)
  - Font-size : 14px
  - Color : #6B7780
  - Margin-bottom : 8px
- **Date** :
  - Font-size : 12px
  - Text-transform : uppercase
  - Color : #9AA6B2
  - Margin-bottom : 12px
- **Title** :
  - Font-size : 18px (--font-size-2)
  - Font-weight : 700
  - Margin-bottom : 12px
- **Description** :
  - Font-size : 14px
  - Line-height : 1.5
  - Color : #6B7780
- **Link** :
  - Text : "Lire l'étude" (pas "Voir détail")
  - Color : #00915A
  - Icon : arrow-right

#### 🚨 Problèmes Identifiés
1. ❌ **Meta structure** : Location devrait être **avant** date
2. ❌ **Date style** : devrait être après location, pas dans eyebrow
3. ✅ Aspect-ratio 3:4 correct

---

### 📊 MAQUETTE 4 : SOLUTIONS CARDS (Sans Image)

#### Structure Visuelle
```
┌─────────────────────────┐
│                         │
│          ⊗              │
│      (icon 48px)        │
│                         │
│    Solutions title      │
│                         │
│    Solutions details    │
│                         │
│  Consulter les sol...→  │
│                         │
└─────────────────────────┘
```

#### Mesures Précises Détectées
- **Pas d'image** : Card sans image
- **Icon central** :
  - Size : 48px (--size-12)
  - Color : #1F2A33 (dark)
  - Margin-bottom : 16px
  - Icon name : "close-square" ou similaire (⊗)
- **Title** :
  - Font-size : 18px
  - Font-weight : 700
  - Text-align : center
  - Margin-bottom : 12px
- **Details** :
  - Font-size : 14px
  - Color : #6B7780
  - Text-align : center
  - Margin-bottom : 16px
- **Link** :
  - Color : #00915A
  - Text-align : center
  - Display : block

#### 🚨 Problèmes Identifiés
1. ❌ **Layout centré** : tout le contenu devrait être centré
2. ❌ **Icon size** : besoin de 48px (--size-12)
3. ❌ **Pas d'image** : variant doit gérer cards sans image
4. ❌ **Text-align center** : manque sur variant solution

---

### 📘 MAQUETTE 5 : STUDIES CARDS (Horizontal)

#### Structure Visuelle
```
┌──────────┬────────────────────────────┐
│          │  [TAG LABEL]    DATE       │
│  IMAGE   │                            │
│ (square  │  Study title               │
│  1:1)    │                            │
│          │  Lorem ipsum dolor sit...  │
│          │  Enim fames hendrerit...   │
│          │                            │
│          │  Télécharger l'étude →     │
└──────────┴────────────────────────────┘
```

#### Mesures Précises Détectées
- **Layout** : Horizontal (image left)
- **Image** :
  - Width : ~40%
  - Aspect-ratio : 1:1 (square) ✅
  - Border-radius : left only (8px 0 0 8px)
- **Tag + Date** :
  - Layout : flex horizontal
  - Gap : 8px
  - Margin-bottom : 12px
- **Title** :
  - Font-size : 18px
  - Font-weight : 700
  - Margin-bottom : 12px
- **Description** :
  - Font-size : 14px
  - Line-height : 1.5
  - Lines visible : 3-4 lines
  - Margin-bottom : 16px
- **Link** :
  - Text : "Télécharger l'étude"
  - Color : #00915A
  - Border : 1px solid #00915A
  - Padding : 8px 16px
  - Border-radius : 4px
  - Display : inline-block

#### 🚨 Problèmes Identifiés
1. ❌ **Link style** : button outlined vert, pas link simple
2. ❌ **Image border-radius** : devrait être left-only en horizontal
3. ✅ Aspect-ratio 1:1 correct pour study

---

### 📦 MAQUETTE 6 : PUSH CARD (Calculateur)

#### Structure Visuelle (Sans image, fond gris)
```
┌───────────────────────────────────────┐
│                                       │
│  Calculate the target surface area... │
│                                       │
│  Quick and easy to use, the surface...│
│                                       │
│  ┌──────────────────┐                │
│  │ Start calculator │                │
│  └──────────────────┘                │
│                                       │
└───────────────────────────────────────┘
```

#### Mesures Précises Détectées
- **Background** : #F3F6F9 (--gray-50 ou --ps-color-neutral-100)
- **No image** : Pas d'image
- **No border** : Pas de bordure visible
- **Padding** : 32px (--size-8)
- **Title** :
  - Font-size : 20px (--font-size-3)
  - Font-weight : 700
  - Margin-bottom : 12px
- **Description** :
  - Font-size : 14px
  - Line-height : 1.5
  - Margin-bottom : 20px
- **Button** :
  - Style : outlined green
  - Text : "Start calculator"
  - Padding : 12px 24px
  - Border : 2px solid #00915A
  - Color : #00915A
  - Background : transparent
  - Border-radius : 4px

#### 🚨 Problèmes Identifiés
1. ❌ **Background gris** : manque variant avec background
2. ❌ **No border** : push devrait avoir option sans bordure
3. ❌ **Padding augmenté** : 32px vs 20px standard
4. ❌ **Button outlined** : besoin variant button outlined green

---

## 🎯 SYNTHÈSE DES CORRECTIONS NÉCESSAIRES

### ❌ CRITIQUE (Impact Pixel Perfect)

1. **NEWS aspect-ratio** : 16:9, pas 4:3
2. **Tag + Date layout** : manque structure horizontale flex
3. **Title font-size global** : 18px (--font-size-2), pas 20px
4. **Links style** : devrait être link component avec arrow, pas button
5. **Favorite icon** : manque sur product cards
6. **Price/Surface format** : manque bullet "•" separator
7. **Status badges** : "Already viewed" + "Exclusivity" manquent
8. **Solutions centered** : tout le contenu doit être centré
9. **Icon size** : 48px pour solutions
10. **Study link** : button outlined, pas link simple
11. **Push background** : manque variant avec bg gris
12. **Image border-radius** : left-only en horizontal layout

### ⚠️ IMPORTANT (Structure HTML)

13. **Meta order** : Location avant Date (publications)
14. **Carousel navigation** : prev/next arrows sur images
15. **Content padding** : varie selon variant (20px vs 24px vs 32px)

### ℹ️ AMÉLIORATION (UX)

16. **Hover states** : shadow plus prononcée
17. **Link underline** : hover only
18. **Price formatting** : structure spécifique avec unité

---

## 📝 PLAN D'IMPLÉMENTATION

### Phase 1 : Corrections CSS Critiques
- [ ] Fix aspect-ratios par variant (news 16:9, product 1:1, publication 3:4, study 1:1)
- [ ] Fix title font-size (18px partout)
- [ ] Fix content padding par variant
- [ ] Add solutions centered layout
- [ ] Add push background gray variant
- [ ] Fix horizontal image border-radius

### Phase 2 : Nouvelles Structures HTML
- [ ] Add favorite icon slot (product)
- [ ] Add tag + date horizontal layout (news, study)
- [ ] Add price/surface formatting (product)
- [ ] Add status badges (product)
- [ ] Add carousel navigation (product horizontal)
- [ ] Add icon central (solutions)

### Phase 3 : Composants Props
- [ ] Add `favorite` prop (boolean)
- [ ] Add `price` prop (string)
- [ ] Add `surface` prop (string)
- [ ] Add `status` prop (viewed, exclusivity)
- [ ] Add `icon` prop (solutions)
- [ ] Add `date` prop (separate from meta)
- [ ] Add `tag` prop (separate from eyebrow)

### Phase 4 : Stories Update
- [ ] Update AllVariants avec corrections pixel perfect
- [ ] Add ProductVertical + ProductHorizontal stories
- [ ] Add NewsWithTags story
- [ ] Add SolutionsWithIcon story
- [ ] Add StudiesWithOutlinedButton story
- [ ] Add PushCalculator story

---

**Prochaine étape** : Implémentation des corrections dans `card.css` et `card.twig`
