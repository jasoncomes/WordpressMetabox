# Composer Setup
```
"require": {
    "wordpressmeta": "dev-master"
},
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/jasoncomes/WordpressMetabox"
    }
],
```

Install Meta Library project by using composer:
```
composer install
```

To Update:
```
composer update
```

If Composer is not install on your machine, run:
```
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

Setup Autoloader in `functions.php`:
```
require_once ABSPATH . '../../vendor/autoload.php';
```


# Metabox
Used to build simple custom metabox.

## Initialize
```
use WordpressMetabox\Metabox;

$metabox = new Metabox();
$metabox->settings = [
   ... Settings
];
$metabox->fields = [
    ... Fields
];
$metabox->build();
```

## Settings
```
[
    'title'        => '',
    'id'           => '',
    'instructions' => '',
    'prefix'       => '',
    'post_type'    => [] || '',
    'post'         => [] || '',
    'parent'       => [] || '',
    'template'     => [] || '',
    'taxonomy'     => [],
    'operator'     => '',
    'location'     => 'normal',
    'priority'     => 'high',
    'dependency'   => [],
    'remove'       => ['custom-fields']
]
```

##### title
(string) (Required) Title of the meta box.

##### id
(string) (Optional) Unique ID of metabox, if not entered will autogenerate a random number custom-metabox-`1-100000`.

##### instructions
(string) (Optional) Can contain html tags.

##### prefix
(string) (Optional) Adds to the field names below. So prefix = `something` will append `something_fieldname`. If you add the `something_` it will still be `something_fieldname`.

##### location
(string) (Optional) The context within the screen where the boxes should display. Available contexts vary from screen to screen. Post edit screen contexts include 'normal', 'side', and 'advanced'. Comments screen contexts include 'normal' and 'side'. Default `normal`.

##### priority
(string) (Optional) The priority within the context where the boxes should show ('high', 'low'). Default `high`.

##### post_type
(string|array) (Optional) The post type which to show the box. Accepts a single post type or an array of post types. Default `all post` types.

##### post
(string|array) (Optional) Metabox will only display on specific posts entered into array. e.g. `['242', '234', '12']` or `'12'`

##### parent
(string|array) (Optional) Metabox will only display on children posts of specific parent post(s) entered into array. e.g. `['242']` or `'242'`

##### taxonomy
(array) (Optional) Metabox will only display on terms that are specified in taxonomies. e.g. `['category' => 'rankings']` or `['category' => 'rankings', 'post_tag' => 'schools']` or `['category' => ['rankings', 'schools'], 'post_tag' => 'schools']`

##### template
(string|array) (Optional) Metabox will only display on specified template. e.g. `['front-page.php', 'contact-page.php']` or `'front-page.php'`

##### operator
(string) (Optional) How you'd like to query your location rules together, `post_type`, `post`, `parent`, `taxonomy`. Accepts `AND` or `OR`. Default is set to 'OR'.

##### dependency 
(array) (Optional) Metabox dependency based on metafield conditions. Conditionals include `>, <, >=, <=, ==, !=, between, outside`. For `>, <, >=, <=`, only accept numeric values. For `==, !=`, can accept single value or array of values to match. e.g. `value` or `[3, "343", 55, "Value"]`. For `between & outside` only accepts array of values. e.g. `[2, 6]`. 
```
'dependency' => [
    'key'       => 'metakey',
    'value'     => 'value', // Format: 'value' || '[value, value, ...]'
    'condition' => '>, <, >=, <=, ==, !=, between, outside',
]
```

##### remove
(string|array) (Optional) Remove support for a feature from a post type. `custom-fields` is set as default.
- 'title'
- 'editor' (content)
- 'author'
- 'thumbnail' (featured image) (current theme must also support Post Thumbnails)
- 'excerpt'
- 'trackbacks'
- 'custom-fields'
- 'comments' (also will see comment count balloon on edit screen)
- 'revisions' (will store revisions)
- 'page-attributes' (template and menu order) (hierarchical must be true)
- 'post-formats' removes post formats, 


## Fields
```
$metabox->fields = [
    ...
];
```

##### Checkbox
```
'metakey' => [
    'type'        => 'checkbox',
    'label'       => '', 
    'description' => '', 
    'dependency'  => [],
    'callback'    => '',
],
```
Checkboxes, values are always `0` & `1`. e.g. If checked `metakey` will have meta value of `1` otherwise it'll be `0`. 


##### Date
```
'metakey' => [
    'type'        => 'date', 
    'value'       => '', // Format: YYYY-MM-DD
    'label'       => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'pattern'     => '',
    'dependency'  => [],
    'style'       => '',
    'callback'    => '',
],
```


##### Editor
```
'metakey' => [
    'type'        => 'editor', 
    'value'       => '',
    'label'       => '', 
    'description' => '', 
    'dependency'  => [],
    'callback'    => '',
],
```


##### Email
```
'metakey' => [
    'type'        => 'email', 
    'value'       => '',
    'label'       => '', 
    'placeholder' => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'pattern'     => '',
    'maxlength'   => '',
    'dependency'  => [],
    'style'       => '',
    'callback'    => '',
],
```


##### Number
```
'metakey' => [
    'type'        => 'number', 
    'value'       => '',
    'label'       => '', 
    'placeholder' => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'pattern'     => '',
    'min'         => '',
    'max'         => '',
    'maxlength'   => '',
    'dependency'  => [],
    'style'       => '',
    'callback'    => '',
],
```


##### Radio
```
'metakey' => [
    'type'        => 'radio', 
    'label'       => '', 
    'description' => '', 
    'dependency'  => [],
    'options'     => ['Category 1' => 'value', 'Category 1' => 'value', ...],
    'callback'    => '',
],
```


##### Select
```
'metakey' => [
    'type'        => 'select-multiple', 
    'label'       => '', 
    'description' => '', 
    'required'    => false, 
    'dependency'  => [],
    'options'     => ['Label' => 'value', 'Label' => 'value', ...],
    'style'       => '',
    'callback'    => '',
],
```


##### Select Multiple
```
'metakey' => [
    'type'        => 'select-multiple', 
    'label'       => '', 
    'description' => '', 
    'required'    => false, 
    'dependency'  => [],
    'options'     => ['Label' => 'value', 'Label' => 'value', ...],
    'style'       => '',
    'callback'    => '',
],
```


##### Select2
```
'metakey' => [
    'type'        => 'select2', 
    'label'       => '', 
    'description' => '', 
    'required'    => false, 
    'dependency'  => [],
    'options'     => ['Label' => 'value', 'Label' => 'value', ...],
    'callback'    => '',
],
```


##### Select2 Multiple
```
'metakey' => [
    'type'        => 'select2-multiple', 
    'label'       => '', 
    'description' => '', 
    'required'    => false, 
    'dependency'  => [],
    'options'     => ['Label' => 'value', 'Label' => 'value', ...],
    'callback'    => '',
],
```


##### Tel
```
'metakey' => [
    'type'        => 'tel', 
    'value'       => '',
    'label'       => '', 
    'placeholder' => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'pattern'     => '', 
    'maxlength'   => '',
    'dependency'  => [],
    'style'       => '',
    'callback'    => '',
],
```
For validation, use `pattern` attribute with `tel` input if browser doesn't support HTML5 `tel` validation.


##### Text
```
'metakey' => [
    'type'        => 'text', 
    'value'       => '',
    'label'       => '', 
    'placeholder' => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'pattern'     => '',
    'maxlength'   => '',
    'dependency'  => [],
    'style'       => '',
    'callback'    => '',
],
```


##### Textarea
```
'metakey' => [
    'type'        => 'textarea', 
    'value'       => '',
    'label'       => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'dependency'  => [],
    'style'       => '',
    'callback'    => '',
],
```


##### URL
```
'metakey' => [
    'type'        => 'url', 
    'value'       => '',
    'label'       => '', 
    'placeholder' => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'pattern'     => '',
    'maxlength'   => '',
    'dependency'  => [],
    'style'       => '',
    'callback'    => '',
],
```

##### Snippet
```
'Lorem <strong>ipsum dolor</strong> sit amet, consectetur adipisicing elit. Architecto et repudiandae earum placeat ea, tempore error facilis, assumenda atque accusamus mollitia laborum fugit accusantium. Eligendi <a href="#">unde ratione</a> deleniti corrupti, quae.<hr />',
```
Can be inserted between metakeys to create titles, breaks, messages or additional instructions. 


##### Object
```
'metakey' => [
    'label'       => '',
    'description' => '',
    'dependency'  => [],
    'callback'    => '',
    'repeater'    => true|false
    'subfields'   => [
        'subkey' => [
            'type'   => 'text', 
            'value'  => '',
            'label'  => '',
            ...
        ],
        'subkey' => [
            'type'   => 'text', 
            'value'  => '',
            'label'  => '',
            ...
        ],
    ],
],
```
Builds a json object into the `metakey`, this can be a repeater with an array of objects stored if `repeater` set to true. Note: Editor field currently doesn't work with repeater field. Also subkeys do not except `dependency` or `callback` attributes.

### Field Properties Information

##### type
(string) (Required)

##### value 
(string) (Optional)

##### label 
(string) (Required)

##### placeholder 
(string) (Optional)

##### description 
(string) (Optional) Can contain html tags.

##### options 
(array) (Optional) e.g. `'options'  => ['Category 1' => 'value', 'Category 1' => 'value', ...]`

##### pattern 
(string) (Optional) The pattern attribute specifies a regular expression that the <input> element's value is checked against. Note: The pattern attribute works with the following input types: text, date, search, url, number, tel, and email. e.g. `[A-Za-z]{3}`

##### required 
(bool) (Optional)

##### readonly 
(bool) (Optional) Note: The readonly attribute works with the following input types: text, date, search, url, number, tel, and email.

##### maxlength
(number) (Optional) Note: The maxlength attribute works with the following input types: text, date, search, url, number, tel, and email.

##### min 
(number) (Optional) Note: The min attribute works with the following input types: number.

##### max 
(number) (Optional) Note: The max attribute works with the following input types: number.

##### dependency 
(array) (Optional) Field dependency based on other metafield conditions. Conditionals include `>, <, >=, <=, ==, !=, between, outside`. For `>, <, >=, <=`, only accept numeric values. For `==, !=`, can accept single value or array of values to match. e.g. `value` or `[3, "343", 55, "Value"]`. For `between & outside` only accepts array of values. e.g. `[2, 6]`. 

##### style 
(string) (Optional) Give the field a little more style love. Work with all fields except, select2s, checkbox, and radio field types. 

##### subfields 
(array) (Optional) Object children fields. This gets saved as an array. NOTE: `Dependency & Callbacks` attributes don't work with subfield attributes.

##### repeater 
(bool) (Optional) Whether or not object children subfields are repeated. This creates an array of objects. NOTE: `Editor` fields currently do not work in repeaters.

```
'dependency' => [
    'key'       => 'metakey',
    'value'     => 'value', // Format: 'value' || '[value, value, ...]'
    'condition' => '>, <, >=, <=, ==, !=, between, outside',
],
```


## Example Usage:
```
$widgetXYZ = new Metabox();
$widgetXYZ->settings = [
   'title'        => 'Title',
   'instructions' => '',
   'post_type'    => ['post', 'pages'],
   'location'     => 'side',
   'priority'     => 'high',
];
$widgetXYZ->fields = [
    'title' => [
        'value'       => 'Default Title Name',
        'type'        => 'text',
        'description' => '',
        'label'       => 'Title',
        'placeholder' => 'Title',
    ],
    'cta' => [
        'type'  => 'text',
        'label' => 'Title',
        'placeholder' => 'tittle',
        'pattern' => '[A-Za-z]{3}',
        'required' => true,
        'dependency' => [
            'key'       => 'title',
            'value'     => 'value',
            'condition' => '==',
        ]
    ],
    'degree_level_id' => [
        'type'  => 'select',
        'label' => 'Degree Level',
        'options'  => $arrayMap,
    ],
    'category_id' => [
        'type'  => 'select',
        'label' => 'Category',
        'options'  => ['Category 1' => 'value', 'Category 1' => 'value', ...],  
    ],
    'subject_id' => [
        'type'  => 'text',
        'label' => 'Degree Level',
        'options'  => ['Category 1' => 'value', 'Category 1' => 'value', ...],   
        'dependency' => [
            'key'       => 'category_id',
            'value'     => '[1, 6]',
            'condition' => '==',
        ]
    ],
];
$widgetXYZ->build();
```