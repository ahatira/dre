# Limitations

Some site building features do not work in specific cases.


## UI Styles Block

The **Site branding** block can't receive styles as its content is handled
in a specific manner in [Core](https://git.drupalcode.org/project/drupal/-/blob/11.x/core/modules/system/templates/block--system-branding-block.html.twig?ref_type=heads).
And so it does not use the `attributes` and `title_attributes` variables.


## UI Styles Page

The **Navigation (Collapsible)** region can't receive styles as its wrapper had
been removed to allow proper flex effects on the blocks inside this region.
