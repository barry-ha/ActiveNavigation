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
   * @param  array  $menuItems array of ('label' => array('url'=>text, 'active'=>bool))
   * @return string            HTML output
   */
  public function render(array $menu, $indent)
  {
    $html = "$indent<ul>" . PHP_EOL;

    foreach ($menu as $label=>$item) {
      $url = $item['url'];
      $active = $item['active'];

      $html .= "$indent$indent<li>";

      // Link to url if present otherwise link to "#"
      $class = ($active ? "class='active' " : '');
      $href = 'href="' . ((!is_int($label) and !is_array($url)) ? $url : '#') . '"';

      $html .= "<a $class$href>";
      $html .= !is_int($label) ? $label : $url;
      $html .= '</a>';

      // Run recursively if a nested array is found
      $html .= (is_array($url) ? $this->render($url, $indent.$indent) : '');

      $html .= '</li>' . PHP_EOL;
    }

    $html .= "$indent</ul>" . PHP_EOL;

    return $html;
  }

  /**
   * Search array to flag active elements, including nested levels.
   * The 'nested array' input is key-value pairs.
   * The resulting output is key-array pairs. 
   * 
   * @param  array  $menuItems Menu items array of ('label' => 'url')
   * @return array  $menu array of ('label' => array('url'=>text, 'active'=>bool))
   */
  public function flagActive(array $menuItems, $activeURL)
  {
    $menu = array();
    $ret = false; // ??? how to return this up the stack? help! can't return TWO values
    foreach ($menuItems as $label => $url) {
      // Run recursively if a nested array is found
      if (is_array($url)) {
        $item = array();
        $item['url'] = $this->flagActive($url, $activeURL);
        $item['active'] = false;  // todo
        $menu[$label] = $item;
      } else {
        $item = array();
        $item['url'] = $url;
        $item['active'] = ($url == $activeURL) ? true : false;
        $menu[$label] = $item;
      }
    }
    /* 
    echo PHP_EOL . PHP_EOL;
    var_dump($menu);
    echo PHP_EOL . PHP_EOL;
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
