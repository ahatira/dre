import formTwig from './form.twig';
import data from './form.yml';

export default {
  title: 'Components/Form',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: 'Drupal form wrapper for composing form fields, actions, and other form elements. Simple wrapper that applies form semantics and base styling.',
      },
    },
  },
  argTypes: {
    children: {
      description: 'Form content (form-field components, buttons, etc.)',
      control: 'text',
      table: { category: 'Content' },
    },
  },
  render: (args) => formTwig(args),
};

export const Default = {
  args: data,
};

export const WithFormField = {
  args: {
    children: `
      <div class="form-item">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" />
      </div>
      <div class="form-item">
        <label for="message">Message</label>
        <textarea id="message" name="message" class="form-control"></textarea>
      </div>
    `,
  },
};

export const WithMultipleFields = {
  args: {
    children: `
      <div class="form-item">
        <label for="firstname">First Name</label>
        <input type="text" id="firstname" name="firstname" class="form-control" />
      </div>
      <div class="form-item">
        <label for="lastname">Last Name</label>
        <input type="text" id="lastname" name="lastname" class="form-control" />
      </div>
      <div class="form-item">
        <label for="property">Property Type</label>
        <select id="property" name="property" class="form-control">\n          <option>Select...</option>\n          <option>Apartment</option>\n          <option>House</option>\n          <option>Commercial</option>\n        </select>
      </div>
      <div class="form-item">
        <button type="submit" class="ps-button ps-button--primary">Search</button>
      </div>
    `,
  },
};
