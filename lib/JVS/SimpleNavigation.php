<?php

namespace JVS;

/**
 * Format navigation items into proper HTML
 * + For the active page, generate: class="active"
 * + Indent each level according to given $indent
 * 
 * To determine the "active" decoration requires a two-pass design for 
 * look-ahead, so that a "group" link can indicate active if any of its 
 * contained links are the active web page.
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
    $html = "$indent<ul>";
    $prevLevel = -1;
    $nextLevel = -1;

    foreach ($menu as $item) {
      $label = $item['label'];
      $url = $item['url'];
      $active = $item['active'];
      $level = $item['level'];
      $startGroup = $item['startGroup'];  // true = this item contains a nested array
      $endGroup = $item['endGroup'];      // true = this is the last item in a nested array

      $i = '';
      if (!empty($indent)) {
        $i = PHP_EOL;
        for ($k=0; $k<$level; $k++) $i .= $indent;
      }

      $html .= "$i<li>";

      // Link to url if present otherwise link to "#"
      $html .= '<a '
            . ($active ? 'class="active" ' : '')
            . 'href="' 
            . ((!is_int($label) and !is_array($url)) ? $url : '#') 
            . '">'
            . (!is_int($label) ? $label : $url)
            . '</a>';

      if ($startGroup) {
        $html .= "$i$indent<ul>";
      } else {
        $html .= '</li>';
      }

      if ($endGroup) {
        $html .= "$i</ul>";
      }
    }

    $html .= PHP_EOL . "$indent</ul>" . PHP_EOL;

    return $html;
  }

  /**
   * Search array to flag any elements that are 'active' in the nav bar
   * The 'nested array' input is converted to a one-dimensional list 
   * which uses 'level' to indicate nesting.
   * 
   * @return array of (item['label'], item['url'], item['active'], item['level'])
   */
  public function flagActive(array $menuItems, $activeURL, $level=1)
  {
    $ii = 1;
    $menu = array();
    foreach ($menuItems as $label => $url) {
      $item = array();
      $item['label'] = $label;
      $item['url'] = $url;
      $item['active'] = ($url == $activeURL) ? true : false;
      $item['level'] = $level;
      $item['startGroup'] = (is_array($url) ? true : false);  // true = this item contains a nested array
      $item['endGroup'] = ($ii == sizeof($menuItems) && $level > 1 ? true : false);  // true = this is the last item in a nested array
      $menu[] = $item;

      // Run recursively if a nested array is found
      if (is_array($url)) {
        $menu = array_merge($menu, $this->flagActive($url, $activeURL, $level + 1));
      }
      $ii++;
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
