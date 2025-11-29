import skipLinkTwig from './skip-link.twig';
import data from './skip-link.yml';

export default {
  title: 'Elements/Skip Link',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          "Lien d'évitement WCAG permettant d'accéder directement au contenu principal. Invisible par défaut, visible au focus clavier uniquement. Obligatoire pour conformité WCAG 2.2 AA.",
      },
    },
  },
  argTypes: {
    targetId: {
      control: 'text',
      description: "ID de l'ancre cible (ex: main-content, navigation, search)",
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: 'main-content' },
      },
    },
    label: {
      control: 'text',
      description: 'Texte du lien affiché',
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: 'Passer au contenu principal' },
      },
    },
  },
  args: { ...data },
};

export const Default = {
  render: (args) => skipLinkTwig(args),
  args: { ...data },
};

export const ToNavigation = {
  render: () => skipLinkTwig({ targetId: 'navigation', label: 'Passer à la navigation' }),
};

export const ToSearch = {
  render: () => skipLinkTwig({ targetId: 'search', label: 'Passer à la recherche' }),
};

export const UseCases = {
  render: () => `
    <div style="position: relative; padding: 4rem 1rem 1rem; border: 2px dashed var(--gray-300); min-height: 200px;">
      <p style="position: absolute; top: 1rem; left: 1rem; margin: 0; font-size: 12px; color: var(--gray-600);">
        👆 Appuyez sur Tab pour voir le skip link apparaître en haut à gauche
      </p>
      ${skipLinkTwig({ targetId: 'main-content', label: 'Passer au contenu principal' })}
      <div style="margin-top: 2rem;">
        <h3 style="margin: 0 0 0.5rem;">Cas d'usage</h3>
        <ul style="margin: 0; padding-left: 1.5rem;">
          <li>Doit être le premier élément focusable de la page</li>
          <li>Permet aux utilisateurs clavier de sauter la navigation répétitive</li>
          <li>Conformité WCAG 2.2 critère 2.4.1 (Niveau A)</li>
          <li>Apparaît uniquement au focus, invisible autrement</li>
        </ul>
      </div>
      <div id="main-content" style="margin-top: 2rem; padding: 1rem; background: var(--gray-50); border-radius: var(--radius-2);">
        <p style="margin: 0;"><strong>Zone de contenu principal</strong> - Le skip link pointe ici (id="main-content")</p>
      </div>
    </div>
  `,
  args: {},
};
