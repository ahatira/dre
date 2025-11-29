import radioTwig from './radio.twig';
import data from './radio.yml';

export default {
  title: 'Elements/Radio',
  tags: ['autodocs'],
  argTypes: {
    name: { control: 'text', description: 'Nom du groupe radio (requis pour sélection unique)' },
    value: { control: 'text', description: 'Valeur unique du radio' },
    label: { control: 'text', description: 'Texte du label' },
    checked: { control: 'boolean', description: 'État coché' },
    disabled: { control: 'boolean', description: 'État désactivé' },
  },
  args: { ...data },
};

export const Default = {
  render: (args) => radioTwig(args),
  args: { ...data },
};

// === Grouped Showcase Stories ===

export const AllStates = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1rem; padding: 1rem;">
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Unchecked</p>
        ${radioTwig({ name: 'demo1', value: '1', label: 'Option 1', checked: false })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Checked</p>
        ${radioTwig({ name: 'demo2', value: '2', label: 'Option 2', checked: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Disabled Unchecked</p>
        ${radioTwig({ name: 'demo3', value: '3', label: 'Option 3', checked: false, disabled: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Disabled Checked</p>
        ${radioTwig({ name: 'demo4', value: '4', label: 'Option 4', checked: true, disabled: true })}
      </div>
    </div>
  `,
  args: {},
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2rem; padding: 1rem;">
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Choix unique</p>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
          ${radioTwig({ name: 'plan', value: 'basic', label: 'Plan Basic', checked: false })}
          ${radioTwig({ name: 'plan', value: 'premium', label: 'Plan Premium', checked: true })}
          ${radioTwig({ name: 'plan', value: 'enterprise', label: 'Plan Enterprise', checked: false })}
        </div>
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Sélection de type</p>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
          ${radioTwig({ name: 'type', value: 'individual', label: 'Particulier', checked: true })}
          ${radioTwig({ name: 'type', value: 'business', label: 'Professionnel', checked: false })}
        </div>
      </div>
    </div>
  `,
  args: {},
};
