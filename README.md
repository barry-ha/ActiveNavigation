## PHP Navigation with Active Page

[![Build Status](https://travis-ci.org/JV-Software/SimpleNavigation.png?branch=master)](https://travis-ci.org/JV-Software/SimpleNavigation)

A class for rendering navigation from an array of items and highlighting the active page.

This is intended to be backward-compatible with the original source JV-Software/SimpleNavigation.

### Installation

#### With Composer

In your `composer.json` file, require the library:

```
"require": {
    "jvs/activenavigation": "dev-master"
}
```

And run `composer install` or `composer update` if you already installed some
packages.

#### Without Composer

1. Download the zip file
2. Extract to your project folder
3. Make sure to require the main class `require_once 'lib/JVS/ActiveNavigation.php';`

### Usage - Simple Array

`ActiveNavigation` provides a simple `make` function that expects an array with the menu items you want to render. It can be a simple array:

```php
$simpleNav = new JVS\ActiveNavigation;
$navItems = array('Home', 'About Us', 'Blog');

echo $simpleNav->make($navItems);
```

Which outputs:

```html
<ul>
    <li><a href="#">Home</a></li>
    <li><a href="#">About Us</a></li>
    <li><a href="#">Blog</a></li>
</ul>
```

### Usage - Simple Array with Links

An array with key/value pairs representing the link's name and url:

```php
$simpleNav = new JVS\ActiveNavigation;
$navItems = array(
    'Home'     => 'http://www.example.com/',
    'About Us' => 'http://www.example.com/about.php',
    'Blog'     => 'http://www.example.com/blog.php',
);

echo $simpleNav->make($navItems);
```

Which outputs:

```html
<ul>
    <li><a href="http://www.example.com/">Home</a></li>
    <li><a href="http://www.example.com/about.php">About Us</a></li>
    <li><a href="http://www.example.com/blog.php">Blog</a></li>
</ul>
```

### Usage - Nested Array

Or a fully nested multi-level array of navigation items:

```php
$simpleNav = new JVS\ActiveNavigation;
$navItems = array(
    'Home'     => 'http://www.example.com/',
    'About Us' => array(
        'Our Company' => 'http://www.example.com/about/company.php',
        'Our Team'    => 'http://www.example.com/about/team.php',
    ),
    'Blog'     => 'http://www.example.com/blog.php',
);

echo $simpleNav->make($navItems);
```

Which outputs:

```html
<ul>
    <li><a href="http://www.example.com/">Home</a></li>
    <li>
        <a href="http://www.example.com/about.php">About Us</a>
        <ul>
            <li><a href="http://www.example.com/about/company.php"></a></li>
            <li><a href="http://www.example.com/about/team.php"></a></li>
        </ul>  
    </li>
    <li><a href="http://www.example.com/blog.php">Blog</a></li>
</ul>
```

### Usage - Active Page

An optional URL argument to the ```make``` function allows it to find and decorate both the active link and the enclosing item with ```class="active"```:

```php
$simpleNav = new JVS\ActiveNavigation;
$navItems = array(
  'Home'  => 'index.html', 
  'About' => array(
    'Page 1' => 'page1.html', 
    'Page 2' => 'page2.html', 
    'Page 3' => 'page3.html', 
  ),
  'Contact'  => 'contact.html',
);
echo $simpleNav->make($navItems, 'page2.html');
```

Which outputs:

```html
<ul>
    <li><a href="index.html">Home</a></li>
    <li>
        <a class="active" href="#">About</a>
        <ul>
            <li><a href="page1.html">Page 1</a></li>
            <li><a class="active" href="page2.html">Page 2</a></li>
            <li><a href="page3.html">Page 3</a></li>
        </ul>  
    </li>
    <li><a href="blog.html">Blog</a></li>
</ul>
```

### Usage - Indentation

You can pretty-print the html output with an optional argument for indentation. For example, set this to two spaces or four spaces or a \t tab character according to your own coding style. If the third argument is not specified, by default the html output is run together on a single line. 

```html
echo $simpleNav->make($navItems, 'page2.html', '  ');
```

### Inspiration

Forked from https://github.com/JV-Software/SimpleNavigation

### Contributing

Feel free to submit bugs or pull requests, just make sure to include unit tests if relevant.
