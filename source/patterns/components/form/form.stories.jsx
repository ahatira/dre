import formTwig from './form.twig';
import data from './form.yml';

export default {
  title: 'Components/Form',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: 'Drupal form wrapper. Use with Drupal form render arrays.',
      },
    },
  },
  argTypes: {
    class: {
      description: 'CSS class for form',
      control: 'text',
      table: { category: 'Appearance' },
    },
    method: {
      description: 'Form method (POST, GET)',
      control: 'select',
      options: ['POST', 'GET'],
      table: { category: 'Behavior' },
    },
  },
  render: (args) =>
    formTwig({
      ...args,
      attributes: { class: args.class, method: args.method },
      children: `
        <div class="form-item">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" class="form-control" />
        </div>
        <div class="form-item">
          <button type="submit" class="ps-button">Submit</button>
        </div>
      `,
    }),
};

export const Default = {
  args: data,
};
