import textareaTwig from './textarea.twig';
import data from './textarea.yml';

export default {
  title: 'Elements/Textarea',
  tags: ['autodocs'],
  render: (args) => textareaTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Textarea atom without label. Always pair with external <label> element. Supports validation states (error, success, warning) and disabled state. Styled per maquette: radius=0, single border, no shadow, focus-visible border color change.',
      },
    },
  },
  argTypes: {
    /* Content */
    value: {
      control: 'text',
      description: 'Current textarea content',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
    placeholder: {
      control: 'text',
      description: 'Placeholder text when empty',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Decrivez votre besoin...' },
      },
    },
    rows: {
      control: 'number',
      description: 'Number of visible rows (HTML rows attribute)',
      table: {
        category: 'Content',
        type: { summary: 'number' },
        defaultValue: { summary: '4' },
      },
    },

    /* Appearance - Validation State */
    state: {
      control: 'select',
      options: [null, 'error', 'success', 'warning'],
      description: 'Validation state (changes border color)',
      table: {
        category: 'Appearance',
        type: { summary: 'null | "error" | "success" | "warning"' },
        defaultValue: { summary: 'null' },
      },
    },

    /* Behavior */
    disabled: {
      control: 'boolean',
      description: 'Disable textarea (read-only, non-editable)',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    required: {
      control: 'boolean',
      description: 'Mark field as required',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },

    /* Accessibility */
    name: {
      control: 'text',
      description: 'Name attribute (form submission)',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'message' },
      },
    },
    id: {
      control: 'text',
      description: 'ID attribute (link to external label via for)',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
      },
    },
  },
};

/**
 * Default
 * Minimal textarea with placeholder
 */
export const Default = {
  render: (args) => textareaTwig(args),
  args: { ...data },
};

// ============ SHOWCASE ============

/**
 * All States
 * Grid showcase of all validation & disabled states
 */
export const AllStates = {
  render: () => {
    const states = [
      {
        label: 'Default',
        args: { ...data, value: 'Text content' },
      },
      {
        label: 'Placeholder',
        args: { ...data, value: '', placeholder: 'Placeholder' },
      },
      {
        label: 'Focus',
        args: { ...data, value: 'Text content' },
      },
      {
        label: 'Success',
        args: { ...data, value: 'Text content', state: 'success' },
      },
      {
        label: 'Error',
        args: { ...data, value: 'Text content', state: 'error' },
      },
      {
        label: 'Warning',
        args: { ...data, value: 'Text content', state: 'warning' },
      },
      {
        label: 'Disabled (placeholder)',
        args: {
          ...data,
          value: '',
          placeholder: 'Not available',
          disabled: true,
        },
      },
      {
        label: 'Disabled (value)',
        args: { ...data, value: 'Read-only content', disabled: true },
      },
    ];

    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 500px;">
        ${states
          .map(
            (state) => `
          <div>
            <p style="margin-bottom: var(--size-2); font-weight: 600; font-size: 12px; color: var(--text-secondary);">
              ${state.label}
            </p>
            ${textareaTwig(state.args)}
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};

// ============ IN CONTEXT ============

/**
 * In Context
 * Real Estate: Property inquiry form with textarea
 */
export const InContext = {
  render: () => {
    return `
      <form style="display: flex; flex-direction: column; gap: var(--size-4); max-width: 600px; font-family: var(--font-sans);">
        <fieldset style="border: 1px solid var(--gray-300); padding: var(--size-4); border-radius: var(--radius-2);">
          <legend style="padding: 0 var(--size-2); font-size: var(--font-size-1); font-weight: var(--font-weight-600); color: var(--gray-900);">
            Property Inquiry
          </legend>

          <div style="display: flex; flex-direction: column; gap: var(--size-4); margin-top: var(--size-3);">
            <!-- Field 1: Required, empty -->
            <div style="display: flex; flex-direction: column; gap: var(--size-1);">
              <label for="form-property" style="font-weight: var(--font-weight-600); color: var(--text-primary);">
                Property Details
                <span style="color: var(--danger);">*</span>
              </label>
              ${textareaTwig({
                id: 'form-property',
                name: 'property',
                required: true,
                rows: 4,
                placeholder: 'Location, surface area, rooms, features, condition...',
              })}
              <small style="color: var(--text-secondary);">Required. Help us understand the property.</small>
            </div>

            <!-- Field 2: Required + successful validation -->
            <div style="display: flex; flex-direction: column; gap: var(--size-1);">
              <label for="form-criteria" style="font-weight: var(--font-weight-600); color: var(--text-primary);">
                Your Search Criteria
                <span style="color: var(--danger);">*</span>
              </label>
              ${textareaTwig({
                id: 'form-criteria',
                name: 'criteria',
                required: true,
                rows: 4,
                state: 'success',
                value: '4BR house with garden, suburbs, max 450k budget, close to schools',
              })}
              <small style="color: var(--success); display: flex; align-items: center; gap: var(--size-05);">
                <span>✓</span> <span>Clear and complete</span>
              </small>
            </div>

            <!-- Field 3: Optional + error state -->
            <div style="display: flex; flex-direction: column; gap: var(--size-1);">
              <label for="form-special" style="font-weight: var(--font-weight-600); color: var(--text-primary);">
                Special Requests
              </label>
              ${textareaTwig({
                id: 'form-special',
                name: 'special',
                rows: 3,
                state: 'error',
                value: 'URGENT!!!',
              })}
              <small style="color: var(--danger); display: flex; align-items: center; gap: var(--size-05);">
                <span>!</span> <span>Provide more context (financing, timeline, flexibility)</span>
              </small>
            </div>

            <!-- Field 4: Required + warning state -->
            <div style="display: flex; flex-direction: column; gap: var(--size-1);">
              <label for="form-contact" style="font-weight: var(--font-weight-600); color: var(--text-primary);">
                Best Time to Contact
                <span style="color: var(--danger);">*</span>
              </label>
              ${textareaTwig({
                id: 'form-contact',
                name: 'contact',
                required: true,
                rows: 2,
                state: 'warning',
                value: 'Weekdays after 7PM or weekend mornings (call only, no SMS)',
              })}
              <small style="color: var(--warning);">
                Tip: Clear phone preferences help our agents reach you faster.
              </small>
            </div>
          </div>
        </fieldset>

        <button type="submit" style="padding: var(--size-3) var(--size-6); background: var(--primary); color: white; border: none; border-radius: var(--radius-1); font-weight: var(--font-weight-600); cursor: pointer; align-self: flex-start;">
          Submit Inquiry
        </button>
      </form>
    `;
  },
};

/**
 * Real Estate context examples
 */
export const RealEstateContext = {
  render: () => {
    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-10);">
        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Contact Form - Property Inquiry (4 rows)</h4>
          <div style="max-width: 600px;">
            ${textareaTwig({
              name: 'property_inquiry',
              placeholder:
                'Décrivez le bien recherché (type, localisation, surface, budget, critères...).',
              rows: 4,
              id: 'inquiry-property',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--text-secondary);">
              Usage: Formulaire de contact - description du besoin client
            </p>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Valuation Request - Property Description (6 rows)</h4>
          <div style="max-width: 600px;">
            ${textareaTwig({
              name: 'property_description',
              placeholder:
                'Décrivez votre bien (type, surface, nombre de pièces, état, travaux récents, équipements...).',
              rows: 6,
              id: 'valuation-description',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--text-secondary);">
              Usage: Demande d'estimation - description détaillée du bien
            </p>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Visit Report - Agent Notes (8 rows, with content)</h4>
          <div style="max-width: 600px;">
            ${textareaTwig({
              name: 'visit_notes',
              value:
                "Client très intéressé par l'appartement. Points positifs : luminosité, balcon, proximité transports. Réserves : bruit rue passante, cuisine à refaire. Budget confirmé 380K€. Souhaite revoir avec conjoint samedi. À rappeler jeudi.",
              rows: 8,
              id: 'visit-notes',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--text-secondary);">
              Usage: CRM agent - notes de visite client
            </p>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Offer Negotiation - Comments (success state)</h4>
          <div style="max-width: 600px;">
            ${textareaTwig({
              name: 'negotiation_comments',
              value:
                'Offre acceptée à 365K€ (négociation -15K€). Conditions suspensives : financement (45j) + diagnostics. Signature compromis prévue 15/01. Vendeur libère bien fin février.',
              state: 'success',
              rows: 5,
              id: 'negotiation-comments',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--success);">
              ✓ Négociation finalisée avec succès
            </p>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Property Listing - Description (error state)</h4>
          <div style="max-width: 600px;">
            ${textareaTwig({
              name: 'listing_description',
              value: 'Bel appart.',
              state: 'error',
              rows: 4,
              id: 'listing-description',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--danger);">
              ✗ Description trop courte (minimum 50 caractères requis)
            </p>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Internal Note - Collaborative (warning state)</h4>
          <div style="max-width: 600px;">
            ${textareaTwig({
              name: 'internal_note',
              value: 'Vérifier zonage PLU avant promesse - parcelle en limite zone constructible.',
              state: 'warning',
              rows: 3,
              id: 'internal-note',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--warning);">
              ⚠ Note importante nécessitant vérification juridique
            </p>
          </div>
        </div>

        <div>
          <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">Archived Message (disabled)</h4>
          <div style="max-width: 600px;">
            ${textareaTwig({
              name: 'archived_message',
              value:
                'Demande initiale client du 12/03/2024 : Recherche T3 Lille centre, budget 250K€, livraison Q4 2024.',
              disabled: true,
              rows: 3,
              id: 'archived-message',
            })}
            <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--text-secondary);">
              Usage: Historique archivé - consultation seule
            </p>
          </div>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Common textarea usage patterns in real estate: property inquiry (4 rows), valuation request (6 rows), visit report (8 rows with content), offer negotiation (success state), listing description (error state), internal notes (warning state), and archived messages (disabled).',
      },
    },
  },
};
