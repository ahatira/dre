import dividerTwig from './divider.twig';
import data from './divider.yml';

export default {
  title: 'Elements/Divider',
  tags: ['autodocs'],
  render: (args) => dividerTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Séparateur visuel pour structurer le contenu.\n\n' +
          '- Orientation: horizontal (défaut) ou vertical.\n' +
          '- Styles: solid, dashed, dotted — bordure tokenisée uniquement.\n' +
          '- Épaisseurs: thin, medium (défaut), thick — valeurs via tokens.\n' +
          '- Couleurs: neutral, primary, secondary, success, warning, danger, info — sémantiques de marque.\n' +
          '- Espacement: sm, md (défaut), lg autour du séparateur.\n' +
          '- Contenu: texte ou icône centrés (police d\'icônes), sans markup additionnel superflu.\n' +
          '- Accessibilité: rôle décoratif; éviter d\'interrompre la navigation; contraste assuré par tokens.\n' +
          '- Marquage minimal: `.ps-divider` porte les styles par défaut; les modificateurs n\'apparaissent que si une option diffère du défaut.',
      },
    },
  },
  argTypes: {
    orientation: {
      control: 'select',
      options: ['horizontal', 'vertical'],
      description: 'Divider orientation',
    },
    style: {
      control: 'select',
      options: ['solid', 'dashed', 'dotted'],
      description: 'Line style',
    },
    thickness: {
      control: 'select',
      options: ['thin', 'medium', 'thick'],
      description: 'Line thickness',
    },
    color: {
      control: 'select',
      options: ['neutral', 'primary', 'secondary', 'success', 'warning', 'danger', 'info'],
      description: 'Line color',
    },
    spacing: {
      control: 'select',
      options: ['sm', 'md', 'lg'],
      description: 'Spacing around divider',
    },
    text: {
      control: 'text',
      description: 'Optional centered text',
    },
    icon: {
      control: 'text',
      description: 'Optional centered icon name',
    },
  },
};

export const Default = {
  args: { ...data },
};

export const Dashed = {
  args: {
    ...data,
    style: 'dashed',
  },
};

export const Dotted = {
  args: {
    ...data,
    style: 'dotted',
  },
};

export const Thin = {
  args: {
    ...data,
    thickness: 'thin',
  },
};

export const Thick = {
  args: {
    ...data,
    thickness: 'thick',
  },
};

export const Primary = {
  args: {
    ...data,
    color: 'primary',
  },
};

export const Secondary = {
  args: {
    ...data,
    color: 'secondary',
  },
};

export const Success = {
  args: {
    ...data,
    color: 'success',
  },
};

export const Warning = {
  args: {
    ...data,
    color: 'warning',
  },
};

export const Danger = {
  args: {
    ...data,
    color: 'danger',
  },
};

export const Info = {
  args: {
    ...data,
    color: 'info',
  },
};

export const WithText = {
  args: {
    ...data,
    text: 'ou',
  },
};

export const WithTextPrimary = {
  args: {
    ...data,
    text: 'ou',
    color: 'primary',
  },
};

export const WithTextDashed = {
  args: {
    ...data,
    text: 'Section',
    style: 'dashed',
    color: 'secondary',
  },
};

export const WithIcon = {
  args: {
    ...data,
    icon: 'star',
  },
};

export const Vertical = {
  render: () => `
    <div style="display: flex; align-items: center; height: 100px; gap: 1rem;">
      <span>Texte gauche</span>
      ${dividerTwig({ orientation: 'vertical', spacing: 'md' })}
      <span>Texte droite</span>
    </div>
  `,
};

export const VerticalThin = {
  render: () => `
    <div style="display: flex; align-items: center; height: 80px; gap: 0.5rem;">
      <span>Option 1</span>
      ${dividerTwig({ orientation: 'vertical', thickness: 'thin', spacing: 'sm' })}
      <span>Option 2</span>
      ${dividerTwig({ orientation: 'vertical', thickness: 'thin', spacing: 'sm' })}
      <span>Option 3</span>
    </div>
  `,
};

export const AllStyles = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2rem;">
      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">Horizontal Styles</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Solid (default)</small>
            ${dividerTwig({ style: 'solid', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Dashed</small>
            ${dividerTwig({ style: 'dashed', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Dotted</small>
            ${dividerTwig({ style: 'dotted', spacing: 'sm' })}
          </div>
        </div>
      </div>

      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">Thickness</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Thin (1px)</small>
            ${dividerTwig({ thickness: 'thin', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Medium (2px, default)</small>
            ${dividerTwig({ thickness: 'medium', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Thick (4px)</small>
            ${dividerTwig({ thickness: 'thick', spacing: 'sm' })}
          </div>
        </div>
      </div>

      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">Colors</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Neutral (gray, default)</small>
            ${dividerTwig({ color: 'neutral', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Primary (green #00915A)</small>
            ${dividerTwig({ color: 'primary', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Secondary (purple #E0388C)</small>
            ${dividerTwig({ color: 'secondary', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Success (green-600)</small>
            ${dividerTwig({ color: 'success', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Warning (yellow-500)</small>
            ${dividerTwig({ color: 'warning', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Danger (red-600)</small>
            ${dividerTwig({ color: 'danger', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Info (blue-600)</small>
            ${dividerTwig({ color: 'info', spacing: 'sm' })}
          </div>
        </div>
      </div>

      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">With Content</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">With text</small>
            ${dividerTwig({ text: 'ou', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">With text + primary color</small>
            ${dividerTwig({ text: 'Section', color: 'primary', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">With text + dashed</small>
            ${dividerTwig({ text: 'Ou', style: 'dashed', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">With icon</small>
            ${dividerTwig({ icon: 'star', spacing: 'sm' })}
          </div>
        </div>
      </div>

      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">Spacing</h3>
        <div style="background: #f5f5f5; padding: 1rem;">
          <div style="background: white; padding: 0.5rem;">Content avant</div>
          ${dividerTwig({ spacing: 'sm', color: 'primary' })}
          <div style="background: white; padding: 0.5rem;">Small spacing (8px)</div>
          ${dividerTwig({ spacing: 'md', color: 'primary' })}
          <div style="background: white; padding: 0.5rem;">Medium spacing (16px, default)</div>
          ${dividerTwig({ spacing: 'lg', color: 'primary' })}
          <div style="background: white; padding: 0.5rem;">Large spacing (24px)</div>
        </div>
      </div>
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2rem; max-width: 600px;">
      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">Formulaire avec séparateur texte</h3>
        <div style="border: 1px solid #e0e0e0; padding: 1.5rem; border-radius: 4px;">
          <input type="email" placeholder="Email" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;" />
          <br/><br/>
          <input type="password" placeholder="Mot de passe" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;" />
          <br/><br/>
          <button style="width: 100%; padding: 0.75rem; background: #00915A; color: white; border: none; border-radius: 4px; cursor: pointer;">Se connecter</button>
          ${dividerTwig({ text: 'ou', spacing: 'md' })}
          <button style="width: 100%; padding: 0.75rem; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Continuer avec Google</button>
        </div>
      </div>

      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">Sections de contenu</h3>
        <div>
          <p style="margin: 0;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
          ${dividerTwig({ spacing: 'lg', thickness: 'thin' })}
          <p style="margin: 0;">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
          ${dividerTwig({ spacing: 'lg', thickness: 'thin' })}
          <p style="margin: 0;">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
        </div>
      </div>

      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">Séparateur avec accent</h3>
        <div>
          <h4 style="margin: 0 0 0.5rem 0; color: #00915A;">Section importante</h4>
          <p style="margin: 0; color: #666;">Contenu mis en avant avec divider primaire.</p>
          ${dividerTwig({ spacing: 'md', color: 'primary', thickness: 'thick' })}
          <h4 style="margin: 0 0 0.5rem 0;">Section standard</h4>
          <p style="margin: 0; color: #666;">Contenu normal avec divider neutre.</p>
        </div>
      </div>
    </div>
  `,
};
