import skeletonTwig from './skeleton.twig';
import skeletonData from './skeleton.yml';

export default {
  title: 'Elements/Skeleton',
  tags: ['autodocs'],
  args: skeletonData,
  argTypes: {
    variant: {
      description: 'Predefined skeleton shape',
      control: 'select',
      options: ['text', 'circle', 'rectangle', 'avatar', 'button', 'card'],
      table: { category: 'Appearance' },
    },
    width: {
      description: 'CSS width value (e.g., "100%", "200px")',
      control: 'text',
      table: { category: 'Layout' },
    },
    height: {
      description: 'CSS height value (overrides variant default)',
      control: 'text',
      table: { category: 'Layout' },
    },
    animation: {
      description: 'Animation type',
      control: 'select',
      options: ['pulse', 'wave', 'none'],
      table: { category: 'Appearance' },
    },
    borderRadius: {
      description: 'CSS border-radius (overrides variant default)',
      control: 'text',
      table: { category: 'Appearance' },
    },
  },
};

// Default story
export const Default = {
  name: 'Default (text)',
  render: (args) => skeletonTwig(args),
  args: skeletonData,
};

// All variants
export const Variants = {
  name: 'Variants',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Text (single line)</p>
        ${skeletonTwig({ variant: 'text', width: '80%' })}
      </div>
      
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Circle (avatar/icon)</p>
        ${skeletonTwig({ variant: 'circle' })}
      </div>
      
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Rectangle (image)</p>
        ${skeletonTwig({ variant: 'rectangle', width: '300px' })}
      </div>
      
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Avatar</p>
        ${skeletonTwig({ variant: 'avatar' })}
      </div>
      
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Button</p>
        ${skeletonTwig({ variant: 'button' })}
      </div>
      
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Card</p>
        ${skeletonTwig({ variant: 'card', width: '300px' })}
      </div>
    </div>
  `,
};

// Animation types
export const Animations = {
  name: 'Animations',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 400px;">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Pulse (default)</p>
        ${skeletonTwig({ variant: 'text', animation: 'pulse' })}
      </div>
      
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">Wave (shimmer)</p>
        ${skeletonTwig({ variant: 'text', animation: 'wave' })}
      </div>
      
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); font-weight: 600; color: var(--gray-700);">None (static)</p>
        ${skeletonTwig({ variant: 'text', animation: 'none' })}
      </div>
    </div>
  `,
};

// Property card loading state (Real Estate context)
export const PropertyCardLoading = {
  name: 'Property Card Loading',
  render: () => `
    <div style="max-width: 350px; padding: var(--size-4); border: 1px solid var(--gray-200); border-radius: var(--radius-3);">
      ${skeletonTwig({ variant: 'rectangle', width: '100%', height: '200px' })}
      <div style="margin-top: var(--size-4);">
        ${skeletonTwig({ variant: 'text', width: '60%' })}
      </div>
      <div style="margin-top: var(--size-2);">
        ${skeletonTwig({ variant: 'text', width: '90%' })}
      </div>
      <div style="margin-top: var(--size-2);">
        ${skeletonTwig({ variant: 'text', width: '70%' })}
      </div>
      <div style="margin-top: var(--size-4); display: flex; gap: var(--size-2);">
        ${skeletonTwig({ variant: 'button', width: '100px' })}
        ${skeletonTwig({ variant: 'button', width: '100px' })}
      </div>
    </div>
  `,
};

// Agent profile loading state
export const AgentProfileLoading = {
  name: 'Agent Profile Loading',
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${skeletonTwig({ variant: 'avatar' })}
      <div style="flex: 1;">
        ${skeletonTwig({ variant: 'text', width: '120px' })}
        <div style="margin-top: var(--size-1);">
          ${skeletonTwig({ variant: 'text', width: '180px' })}
        </div>
      </div>
    </div>
  `,
};

// Multiple text lines
export const TextLines = {
  name: 'Text Lines',
  render: () => `
    <div style="max-width: 500px; display: flex; flex-direction: column; gap: var(--size-2);">
      ${skeletonTwig({ variant: 'text', width: '100%' })}
      ${skeletonTwig({ variant: 'text', width: '95%' })}
      ${skeletonTwig({ variant: 'text', width: '87%' })}
      ${skeletonTwig({ variant: 'text', width: '92%' })}
      ${skeletonTwig({ variant: 'text', width: '60%' })}
    </div>
  `,
};
