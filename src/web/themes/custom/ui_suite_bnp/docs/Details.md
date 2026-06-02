# Details


## Accordion

By default, the `details` render element is displayed as a Bootstrap accordion.

This is disabled for Layout Builder form and controller routes.

If you need to disable this feature in other details, you can set the
attribute `#bootstrap_accordion` to `FALSE` to the element:

```php
$form['my_details'] = [
  '#type' => 'details',
  '#bootstrap_accordion' => FALSE,
  ...
];
```
