# Modal

UI Suite BNP replaces the Drupal core modal to provide Bootstrap's one.


## Example

Here is an example to create a modal opening link with some customized options:

```php
$modal_options = [
  'dialogClasses' => 'modal-dialog-centered',
  'dialogShowHeader' => 'false',
];

$build['modal_link'] = [
  '#type' => 'link',
  '#title' => $this->t('Link title'),
  '#url' => Url::fromRoute('my_route'),
  '#attributes' => [
    'class' => ['use-ajax'],
    'data-dialog-type' => 'modal',
    'data-dialog-options' => Json::encode($modal_options),
  ],
];
```


## Options

The list of options as well as there default value can be found in
[dialog.js](../assets/js/misc/dialog/dialog.es6.js) (`drupalSettings.dialog`).

| Option                | Type    | Default value | Description                                                                  |
|-----------------------|---------|---------------|------------------------------------------------------------------------------|
| dialogClasses         | string  | (empty)       |                                                                              |
| dialogShowHeader      | boolean | true          |                                                                              |
| dialogShowHeaderTitle | boolean | true          |                                                                              |
| dialogStatic          | boolean | false         |                                                                              |
| dialogHeadingLevel    | integer | 5             |                                                                              |
| buttonClass           | string  | btn           |                                                                              |
| buttonPrimaryClass    | string  | btn-primary   | Will only have an impact if the button does not already have a `btn-` class. |
