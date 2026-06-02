# Work Directory

This folder contains build-time sources only.

- `work/styles/scss`: Sass sources compiled to `assets/css`.
- `work/scripts`: JS sources maintained by frontend team.
- `work/icons`: SVG icons waiting for optimization/integration.
- `work/tools`: build helper scripts.

Canonical style sources live directly under `work/styles/scss/*` folders (`component`, `form`, `media-library`, etc.).

Legacy cleanup tracking is maintained in `work/legacy-debt-dashboard.md`.

Production deployments may exclude this folder if generated `assets/*` are committed.
