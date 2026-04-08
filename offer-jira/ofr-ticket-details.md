# Offer detail backlog - detailed tickets

## OFR-01 Cadrage technique et baseline visuelle
- Goal: lock technical context and visual comparison baseline before implementation.
- Acceptance criteria:
  - Property Search variant selected and documented.
  - Baseline screenshots defined for desktop/mobile checkpoints.
  - Visual checklist approved (grid, spacing, typography, CTA, modal, map).

## OFR-02 Modele de donnees Offre
- Goal: define data contract to support all sections in the mockups.
- Acceptance criteria:
  - Mandatory/optional fields listed.
  - Mapping section -> field published.
  - Fallback strategy documented for missing fields.

## OFR-03 View mode et suggestion de template Offre
- Goal: route offer-like full node pages to a dedicated template.
- Acceptance criteria:
  - theme_suggestions_node_alter implemented.
  - Offer-like field detection documented.
  - Dedicated suggestion names are stable.

## OFR-04 Template node detail offre
- Goal: implement offer detail page skeleton in Twig.
- Acceptance criteria:
  - Hero + consultant split section present.
  - Summary section with title/price/meta/actions present.
  - Content sections and map placeholders present.
  - Fallback rendering remains safe.

## OFR-05 to OFR-14
- Use the CSV backlog for planning and prioritization.
