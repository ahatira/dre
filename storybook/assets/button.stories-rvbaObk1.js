import { b as a } from './button-Bf_lQ4Tb.js';
import { i as Q } from './icons-list-Di3fqTRs.js';
import './iframe-D21U4yYN.js';
import './twig-BPJOkNgt.js';
const X = {
    label: 'Button',
    variant: 'neutral',
    outline: !1,
    size: 'medium',
    disabled: !1,
    loading: !1,
    fullWidth: !1,
  },
  re = {
    title: 'Elements/Button',
    tags: ['autodocs'],
    parameters: {
      docs: {
        description: {
          component:
            'Interactive action trigger with semantic variants, sizes, and styles. Supports icons, loading/disabled states, links, and full-width layout using design tokens.',
        },
      },
    },
    argTypes: {
      label: {
        description: 'Button text',
        control: { type: 'text' },
        table: {
          category: 'Content',
          type: { summary: 'string', required: !0 },
          defaultValue: { summary: 'Button' },
        },
      },
      icon: {
        description: 'Icon name to display (optional)',
        control: { type: 'select' },
        options: Q.categories.generic,
        table: { category: 'Content', type: { summary: 'string' } },
      },
      iconPosition: {
        description: 'Icon position relative to text',
        control: { type: 'select' },
        options: ['left', 'right'],
        table: {
          category: 'Content',
          type: { summary: 'left | right' },
          defaultValue: { summary: 'right' },
        },
      },
      variant: {
        description:
          'Semantic variant (neutral: gray default, primary: green, secondary: pink, success/info/warning/danger)',
        control: { type: 'select' },
        options: ['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'],
        table: {
          category: 'Appearance',
          type: { summary: 'primary | secondary | neutral | success | info | warning | danger' },
          defaultValue: { summary: 'neutral' },
        },
      },
      outline: {
        description: 'Outline version (border only, transparent background)',
        control: { type: 'boolean' },
        table: {
          category: 'Appearance',
          type: { summary: 'boolean' },
          defaultValue: { summary: !1 },
        },
      },
      size: {
        description: 'Button size (small: 34px, medium: 36px, large: 40px)',
        control: { type: 'select' },
        options: ['small', 'medium', 'large'],
        table: {
          category: 'Appearance',
          type: { summary: 'small | medium | large' },
          defaultValue: { summary: 'medium' },
        },
      },
      fullWidth: {
        description: 'Full width button (width: 100%)',
        control: { type: 'boolean' },
        table: {
          category: 'Appearance',
          type: { summary: 'boolean' },
          defaultValue: { summary: !1 },
        },
      },
      disabled: {
        description: 'Disable button (reduces opacity to 50%)',
        control: { type: 'boolean' },
        table: {
          category: 'Behavior',
          type: { summary: 'boolean' },
          defaultValue: { summary: !1 },
        },
      },
      loading: {
        description: 'Display loading state',
        control: { type: 'boolean' },
        table: {
          category: 'Behavior',
          type: { summary: 'boolean' },
          defaultValue: { summary: !1 },
        },
      },
      url: {
        description: 'Destination URL (transforms button to link)',
        control: { type: 'text' },
        table: { category: 'Link', type: { summary: 'string' } },
      },
      target: {
        description: 'Link target attribute',
        control: { type: 'select' },
        options: ['_self', '_blank'],
        table: {
          category: 'Link',
          type: { summary: '_self | _blank' },
          defaultValue: { summary: '_self' },
        },
      },
      baseClass: {
        description:
          'Override BEM block class name (for custom button variants in parent components)',
        control: { type: 'text' },
        table: {
          category: 'Advanced',
          type: { summary: 'string' },
          defaultValue: { summary: 'ps-button' },
        },
      },
      toggle: {
        description:
          'Enable toggle functionality via data-ps-toggle="button". Toggles .active class and aria-pressed attribute on click.',
        control: { type: 'boolean' },
        table: {
          category: 'Behavior',
          type: { summary: 'boolean' },
          defaultValue: { summary: !1 },
        },
      },
      active: {
        description:
          'Pre-toggled state (only applies when toggle=true). Renders .active class and aria-pressed="true".',
        control: { type: 'boolean' },
        table: {
          category: 'Behavior',
          type: { summary: 'boolean' },
          defaultValue: { summary: !1 },
        },
      },
    },
  },
  r = { render: (e) => a(e), args: { ...X, variant: 'neutral' } },
  t = {
    render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map((e) => a({ label: e.charAt(0).toUpperCase() + e.slice(1), variant: e })).join('')}
    </div>
  `,
  },
  n = {
    render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map((e) => a({ label: e.charAt(0).toUpperCase() + e.slice(1), variant: e, outline: !0 })).join('')}
    </div>
  `,
  },
  s = {
    render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${['small', 'medium', 'large'].map((e) => a({ label: e.charAt(0).toUpperCase() + e.slice(1), variant: 'primary', size: e })).join('')}
    </div>
  `,
  },
  i = {
    render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${a({ label: 'Search', variant: 'primary', icon: 'search', iconPosition: 'left' })}
      ${a({ label: 'Next', variant: 'primary', icon: 'arrow-right', iconPosition: 'right' })}
      ${a({ icon: 'close', variant: 'primary', size: 'medium' })}
    </div>
  `,
  },
  o = { render: () => a({ label: 'Full Width Button', variant: 'primary', fullWidth: !0 }) },
  l = {
    render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${a({ label: 'Loading...', variant: 'primary', loading: !0 })}
      ${a({ label: 'Loading...', variant: 'secondary', outline: !0, loading: !0 })}
    </div>
  `,
  },
  c = {
    render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${a({ label: 'Disabled', variant: 'primary', disabled: !0 })}
      ${a({ label: 'Disabled', variant: 'secondary', outline: !0, disabled: !0 })}
    </div>
  `,
  },
  d = {
    name: 'Custom Base Class (Advanced)',
    render: () => `
    <style>
      .custom-action { padding: var(--size-3) var(--size-5); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); cursor: pointer; }
      .custom-action:hover { background: var(--primary-hover); }
      .custom-action__icon { margin-left: var(--size-2); }
    </style>
    <div style="display: flex; gap: var(--size-4); flex-direction: column;">
      <p><strong>Default button:</strong></p>
      ${a({ label: 'Standard Button', variant: 'primary', icon: 'arrow-right' })}
      <p><strong>With baseClass override (custom-action):</strong></p>
      ${a({ baseClass: 'custom-action', label: 'Custom Styled', icon: 'arrow-right' })}
      <p><em>Note: baseClass is used by parent components (alert, modal, etc.) to fully control button styling via their own BEM classes.</em></p>
    </div>
  `,
  },
  p = {
    name: 'Toggle State',
    render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map((e) => a({ label: e.charAt(0).toUpperCase() + e.slice(1), variant: e, icon: 'heart', toggle: !0 })).join('')}
    </div>
  `,
    parameters: {
      docs: {
        description: {
          story:
            'Toggle button functionality with all color variants. Click to toggle .active class and aria-pressed attribute. Uses data-ps-toggle="button" behavior.',
        },
      },
    },
  },
  u = {
    name: 'Toggle State (Pre-Active)',
    render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map((e) => a({ label: e.charAt(0).toUpperCase() + e.slice(1), variant: e, icon: 'heart', toggle: !0, active: !0 })).join('')}
    </div>
  `,
    parameters: {
      docs: {
        description: {
          story:
            'Pre-toggled buttons (initial active state) with all variants. Renders with .active class and aria-pressed="true".',
        },
      },
    },
  },
  g = {
    name: 'Toggle Icon (Icon-Only Buttons)',
    render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      ${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger']
        .map(
          (e) => `
        <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2);">
          <span style="font-size: var(--size-305); color: var(--gray-700);">${e}</span>
          <button class="ps-button ps-button--${e} ps-button--icon-only" data-ps-toggle="button" aria-label="Toggle ${e}" aria-pressed="false" style="width: var(--size-6); height: var(--size-6);">
            <span class="ps-button__icon" data-icon="heart" aria-hidden="true"></span>
          </button>
        </div>
      `
        )
        .join('')}
    </div>
  `,
    parameters: {
      docs: {
        description: {
          story:
            'Toggle icon-only buttons with all color variants. Inactive: gray (#333333), Active: variant color. Perfect for favorite/like/bookmark actions.',
        },
      },
    },
  };
var m, y, v;
r.parameters = {
  ...r.parameters,
  docs: {
    ...((m = r.parameters) == null ? void 0 : m.docs),
    source: {
      originalSource: `{
  render: args => buttonTwig(args),
  args: {
    ...data,
    variant: 'neutral'
  }
}`,
      ...((v = (y = r.parameters) == null ? void 0 : y.docs) == null ? void 0 : v.source),
    },
  },
};
var b, f, h;
t.parameters = {
  ...t.parameters,
  docs: {
    ...((b = t.parameters) == null ? void 0 : b.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      \${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map(variant => buttonTwig({
    label: variant.charAt(0).toUpperCase() + variant.slice(1),
    variant
  })).join('')}
    </div>
  \`
}`,
      ...((h = (f = t.parameters) == null ? void 0 : f.docs) == null ? void 0 : h.source),
    },
  },
};
var w, x, z;
n.parameters = {
  ...n.parameters,
  docs: {
    ...((w = n.parameters) == null ? void 0 : w.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      \${[
  // Show neutral outline first as the default
  'neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map(variant => buttonTwig({
    label: variant.charAt(0).toUpperCase() + variant.slice(1),
    variant,
    outline: true
  })).join('')}
    </div>
  \`
}`,
      ...((z = (x = n.parameters) == null ? void 0 : x.docs) == null ? void 0 : z.source),
    },
  },
};
var T, $, C;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((T = s.parameters) == null ? void 0 : T.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      \${['small', 'medium', 'large'].map(size => buttonTwig({
    label: size.charAt(0).toUpperCase() + size.slice(1),
    variant: 'primary',
    size
  })).join('')}
    </div>
  \`
}`,
      ...((C = ($ = s.parameters) == null ? void 0 : $.docs) == null ? void 0 : C.source),
    },
  },
};
var A, S, k;
i.parameters = {
  ...i.parameters,
  docs: {
    ...((A = i.parameters) == null ? void 0 : A.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      \${buttonTwig({
    label: 'Search',
    variant: 'primary',
    icon: 'search',
    iconPosition: 'left'
  })}
      \${buttonTwig({
    label: 'Next',
    variant: 'primary',
    icon: 'arrow-right',
    iconPosition: 'right'
  })}
      \${buttonTwig({
    icon: 'close',
    variant: 'primary',
    size: 'medium'
  })}
    </div>
  \`
}`,
      ...((k = (S = i.parameters) == null ? void 0 : S.docs) == null ? void 0 : k.source),
    },
  },
};
var B, _, V;
o.parameters = {
  ...o.parameters,
  docs: {
    ...((B = o.parameters) == null ? void 0 : B.docs),
    source: {
      originalSource: `{
  render: () => buttonTwig({
    label: 'Full Width Button',
    variant: 'primary',
    fullWidth: true
  })
}`,
      ...((V = (_ = o.parameters) == null ? void 0 : _.docs) == null ? void 0 : V.source),
    },
  },
};
var D, I, U;
l.parameters = {
  ...l.parameters,
  docs: {
    ...((D = l.parameters) == null ? void 0 : D.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      \${buttonTwig({
    label: 'Loading...',
    variant: 'primary',
    loading: true
  })}
      \${buttonTwig({
    label: 'Loading...',
    variant: 'secondary',
    outline: true,
    loading: true
  })}
    </div>
  \`
}`,
      ...((U = (I = l.parameters) == null ? void 0 : I.docs) == null ? void 0 : U.source),
    },
  },
};
var j, P, W;
c.parameters = {
  ...c.parameters,
  docs: {
    ...((j = c.parameters) == null ? void 0 : j.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      \${buttonTwig({
    label: 'Disabled',
    variant: 'primary',
    disabled: true
  })}
      \${buttonTwig({
    label: 'Disabled',
    variant: 'secondary',
    outline: true,
    disabled: true
  })}
    </div>
  \`
}`,
      ...((W = (P = c.parameters) == null ? void 0 : P.docs) == null ? void 0 : W.source),
    },
  },
};
var L, O, E;
d.parameters = {
  ...d.parameters,
  docs: {
    ...((L = d.parameters) == null ? void 0 : L.docs),
    source: {
      originalSource: `{
  name: 'Custom Base Class (Advanced)',
  render: () => \`
    <style>
      .custom-action { padding: var(--size-3) var(--size-5); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); cursor: pointer; }
      .custom-action:hover { background: var(--primary-hover); }
      .custom-action__icon { margin-left: var(--size-2); }
    </style>
    <div style="display: flex; gap: var(--size-4); flex-direction: column;">
      <p><strong>Default button:</strong></p>
      \${buttonTwig({
    label: 'Standard Button',
    variant: 'primary',
    icon: 'arrow-right'
  })}
      <p><strong>With baseClass override (custom-action):</strong></p>
      \${buttonTwig({
    baseClass: 'custom-action',
    label: 'Custom Styled',
    icon: 'arrow-right'
  })}
      <p><em>Note: baseClass is used by parent components (alert, modal, etc.) to fully control button styling via their own BEM classes.</em></p>
    </div>
  \`
}`,
      ...((E = (O = d.parameters) == null ? void 0 : O.docs) == null ? void 0 : E.source),
    },
  },
};
var F, N, R;
p.parameters = {
  ...p.parameters,
  docs: {
    ...((F = p.parameters) == null ? void 0 : F.docs),
    source: {
      originalSource: `{
  name: 'Toggle State',
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      \${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map(variant => buttonTwig({
    label: variant.charAt(0).toUpperCase() + variant.slice(1),
    variant,
    icon: 'heart',
    toggle: true
  })).join('')}
    </div>
  \`,
  parameters: {
    docs: {
      description: {
        story: 'Toggle button functionality with all color variants. Click to toggle .active class and aria-pressed attribute. Uses data-ps-toggle="button" behavior.'
      }
    }
  }
}`,
      ...((R = (N = p.parameters) == null ? void 0 : N.docs) == null ? void 0 : R.source),
    },
  },
};
var M, q, G;
u.parameters = {
  ...u.parameters,
  docs: {
    ...((M = u.parameters) == null ? void 0 : M.docs),
    source: {
      originalSource: `{
  name: 'Toggle State (Pre-Active)',
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      \${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map(variant => buttonTwig({
    label: variant.charAt(0).toUpperCase() + variant.slice(1),
    variant,
    icon: 'heart',
    toggle: true,
    active: true
  })).join('')}
    </div>
  \`,
  parameters: {
    docs: {
      description: {
        story: 'Pre-toggled buttons (initial active state) with all variants. Renders with .active class and aria-pressed="true".'
      }
    }
  }
}`,
      ...((G = (q = u.parameters) == null ? void 0 : q.docs) == null ? void 0 : G.source),
    },
  },
};
var H, J, K;
g.parameters = {
  ...g.parameters,
  docs: {
    ...((H = g.parameters) == null ? void 0 : H.docs),
    source: {
      originalSource: `{
  name: 'Toggle Icon (Icon-Only Buttons)',
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      \${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map(variant => \`
        <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2);">
          <span style="font-size: var(--size-305); color: var(--gray-700);">\${variant}</span>
          <button class="ps-button ps-button--\${variant} ps-button--icon-only" data-ps-toggle="button" aria-label="Toggle \${variant}" aria-pressed="false" style="width: var(--size-6); height: var(--size-6);">
            <span class="ps-button__icon" data-icon="heart" aria-hidden="true"></span>
          </button>
        </div>
      \`).join('')}
    </div>
  \`,
  parameters: {
    docs: {
      description: {
        story: 'Toggle icon-only buttons with all color variants. Inactive: gray (#333333), Active: variant color. Perfect for favorite/like/bookmark actions.'
      }
    }
  }
}`,
      ...((K = (J = g.parameters) == null ? void 0 : J.docs) == null ? void 0 : K.source),
    },
  },
};
const te = [
  'Default',
  'AllVariants',
  'AllOutlines',
  'AllSizes',
  'WithIcons',
  'FullWidth',
  'Loading',
  'Disabled',
  'CustomBaseClass',
  'Toggle',
  'ToggleActive',
  'ToggleIcon',
];
export {
  n as AllOutlines,
  s as AllSizes,
  t as AllVariants,
  d as CustomBaseClass,
  r as Default,
  c as Disabled,
  o as FullWidth,
  l as Loading,
  p as Toggle,
  u as ToggleActive,
  g as ToggleIcon,
  i as WithIcons,
  te as __namedExportsOrder,
  re as default,
};
