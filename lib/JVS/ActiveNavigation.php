<?php

namespace JVS;

/**
 * Format navigation items into proper HTML
 * + For the active page, generate: class="active"
 * + Indent each level according to given $indent
 *
 * @author Barry Hansen <barry.hansen@gmail.com>
 * @author Javier Villanueva <info@jvsoftware.com>
 */
class SimpleNavigation
{
  /**
   * Output navigation
   *
   * @param  array  $menuItems Menu array of (item['label'],item['url'], item['active'], item['level'])
   * @return string            HTML output
   */
  public function render(array $menu, $indent)
  {
    $html = '';
    $prevlevel = -1;

    foreach ($menu as $item) {
      $label = $item['label'];
      $url = $item['url'];
      $active = $item['active'];
      $level = $item['level'];

      $i = '';
      if (!empty($indent)) {
        $i = PHP_EOL;
        for ($k=0; $k<$level; $k++) $i .= $indent;
      }

      if ($level > $prevlevel) {
        $html .= "$i<ul>";
      }
      if ($level < $prevlevel) {
        $html .= "$i$indent</ul>";
      }

      $html .= "$i<li>";

      // Link to url if present otherwise link to "#"
      $html .= '<a '
            . ($active ? 'class="active" ' : '')
            . 'href="' 
            . ((!is_int($label) and !is_array($url)) ? $url : '#') 
            . '">'
            . (!is_int($label) ? $label : $url)
            . '</a>'
            . '</li>';
      $prevlevel = $level;
    }

    $html .= "$indent</ul>" . PHP_EOL;

    return $html;
  }

  /**
   * Search array to flag any elements that are 'active' in the nav bar
   * The 'nested array' input is converted to a one-dimensional list 
   * which uses 'level' to indicate nesting.
   * 
   * @return array of (item['label'],item['url'], item['active'], item['level'])
   */
  public function flagActive(array $menuItems, $activeURL, $level=1)
  {
    $menu = array();
    foreach ($menuItems as $label => $url) {
      $item = array();
      $item['label'] = $label;
      $item['url'] = $url;
      $item['active'] = ($url == $activeURL) ? true : false;
      $item['level'] = $level;
      $menu[] = $item;

      // Run recursively if a nested array is found
      if (is_array($url)) {
        $menu = array_merge($menu, $this->flagActive($url, $activeURL, $level + 1));
      }
    }
    /* 
    echo PHP_EOL;
    echo "--- level = $level".PHP_EOL;
    var_dump($menu);
    echo PHP_EOL;
    /* */
    return $menu;
  }

  public function make(array $menuItems, $activeURL = '', $indent = '')
  {
    $menu = $this->flagActive($menuItems, $activeURL);
    $html = $this->render($menu, $indent);
    return $html;
  }
}
