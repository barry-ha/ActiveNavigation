<?php

namespace JVS;
function here($line) 
{
  return "<!-- $line -->";
}

/**
 * Format navigation items into unordered lists and valid HTML
 * + Optional: For the active page, generate: class="active"
 * + Optional: Indent each level by given amount
 * + Optional: Allow for custom HTML lists to accommodate Bootstrap requirements
 * 
 * To determine the "active" decoration requires a two-pass design for 
 * look-ahead, so that a "group" link can indicate active if any of its 
 * contained links are the active web page.
 * 
 * For example output, see README.md
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
   * @param  string $indent    A tab character or string of blanks for indentation
   * @param  int    $level     Optional nesting level, 1=topmost
   * @return string            HTML output
   */
  public function render(array $menu, $indent, $level=1)
  {
    $html = PHP_EOL . "$indent<ul>" . PHP_EOL;

    foreach ($menu as $label => $item) {
      $url = $item['url'];
      $active = $item['active'];

      $html .= "$indent<li>";

      // Link to url if present otherwise link to "#"
      $class = ($active ? 'class="active" ' : '');
      $href = 'href="' . ((!is_int($label) and !is_array($url)) ? $url : '#') . '"';

      $html .= "<a $class$href>";
      $html .= !is_int($label) ? $label : $url;
      $html .= '</a>';

      // Run recursively if a nested array is found
      if (is_array($url)) {
        $html .= $this->render($url, $indent . $indent, $level + 1);
        $html .= "$indent</li>" . PHP_EOL;
      } else {
        $html .= '</li>' . PHP_EOL;
      }
    }

    $html .= "$indent</ul>" . PHP_EOL;

    return $html;
  }

  /**
   * Helper function to count the number of array elements marked 'active'
   */
  protected function countActive(array $menu)
  {
    $count = 0;   // assume no element is active
    foreach ($menu as $item) {
      $count += $item['active'];
    }
    //echo "-----countActive = $count of " . sizeof($menu) . " elements".PHP_EOL;
    return $count;
  }

  /**
   * Search array to flag active elements, including nested levels.
   * The 'nested array' input is key-value pairs.
   * The resulting output is key-array pairs. 
   * 
   * @param  array  $menuItems = array of ('label' => 'url')
   * @return array  $menu      = array of ('label' => array('url'=>text, 'active'=>bool))
   */
  public function flagActive(array $menuItems, $activeURL)
  {
    $menu = array();
    $groupActive = 0;  // assume no element is active
    foreach ($menuItems as $label => $url) {

      // Run recursively if a nested array is found
      if (is_array($url)) {
        $item = array();
        $item['url'] = $this->flagActive($url, $activeURL);
        $item['active'] = $this->countActive($item['url']); // examine the nested array returned by flagActive
        $menu[$label] = $item;
      } else {
        $item = array();
        $item['url'] = $url;
        $item['active'] = ($url == $activeURL) ? 1 : 0;
        $groupActive += $item['active'];
        $menu[$label] = $item;
      }
    }

    return $menu;
  }

  public function make(array $menuItems, $activeURL = '', $indent = '')
  {
    $menu = $this->flagActive($menuItems, $activeURL);
    $html = $this->render($menu, $indent);
    return $html;
  }
}

/**
 * Derived class BootstrapNavigation 
 * @param 
 */
class BootstrapNavigation extends SimpleNavigation
{
  /**
   * Output navigation for https://getbootstrap.com/docs/4.0/components/navbar/
   *
   * @param  array  $menuItems array of ('label' => array('url'=>text, 'active'=>bool))
   * @param  string $indent    Tabs or spaces for left-side indentation
   * @param  int    $level     Optional nesting level, 1=topmost
   * @return string            HTML output
   */
  protected $itemNum = 0;   // keeps track of nav item number, to help render unique ID attributes

  public function render(array $menu, $indent, $level=1)
  {
    /* 
    if ($level == 1) {
      echo PHP_EOL . '<div class="row"><pre>' . PHP_EOL;
      var_dump($menu);
      echo PHP_EOL . '</div><br/>' . PHP_EOL;
      }
    /* */
    $html = PHP_EOL;
    if ($level == 1) {
      // Begin brand
      $html .= $indent.'<a class="navbar-brand" href="#">Navbar</a>' . PHP_EOL;
      // Hamburger icon
      $html .= $indent.'<!-- Hamburger icon -->'.PHP_EOL;
      $html .= $indent.'<button class="navbar-toggler" type="button" ' . PHP_EOL;
      $html .= $indent.'  data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"' . PHP_EOL;
      $html .= $indent.'  aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">' . PHP_EOL;
      $html .= $indent.'  <span class="navbar-toggler-icon"></span>' . PHP_EOL;
      $html .= $indent.'</button>' . PHP_EOL;
      $html .= $indent.'<div class="collapse navbar-collapse" id="navbarNavDropdown">' . PHP_EOL;
      $html .= $indent.'  <ul class="navbar-nav nav-pills">' . PHP_EOL;
    }
    // Begin item list
    if (false) {
      // First item
      $html .= '    <li class="nav-item" id="'.$itemNum.'>' . PHP_EOL;
      $html .= '      <a class="nav-link" href="xindex.html">xHome</a>' . PHP_EOL;
      $html .= '    </li>' . PHP_EOL;
      // Second item has sub-items
      $html .= '    <li class="nav-item dropdown">' . PHP_EOL;
      $html .= '      <a class="nav-link dropdown-toggle active" href="xabout.html" id="navbarItem'.$itemNum.'"' . PHP_EOL;
      $html .= '         data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . PHP_EOL;
      $html .= '         xAbout Us' . PHP_EOL;
      $html .= '      </a>' . PHP_EOL;
      $html .= '      <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">' . PHP_EOL;
      $html .= '        <a class="dropdown-item" href="xpage1.html">xPage 1</a>' . PHP_EOL;
      $html .= '        <a class="dropdown-item" href="xpage2.html">xPage 2</a>' . PHP_EOL;
      $html .= '        <a class="dropdown-item active" href="xpage3.html">xPage 3</a>' . PHP_EOL;
      $html .= '      </div>' . PHP_EOL;
      $html .= '    </li>' . PHP_EOL;

    } else {

      foreach ($menu as $label => $item) {
        global $itemNum;
        $itemNum += 1;
        $url = $item['url'];
        $active = $item['active']; 
        $dropdown = is_array($url);

        /**** 
        if ($active) {
          if ($dropdown) {
            $html .= $indent."    <!-- Begin item $itemNum, level $level, is active, has dropdown -->".here(__LINE__).PHP_EOL;
          } else {
            $html .= $indent."    <!-- Begin item $itemNum, level $level, is active -->".PHP_EOL;
          }
        } else {
          if ($dropdown) {
            $html .= $indent."    <!-- Begin item $itemNum, level $level, has dropdown -->".PHP_EOL;
          } else {
            $html .= $indent."    <!-- Begin item $itemNum, level $level -->".PHP_EOL;
          }
        }
        /**** */

        if ($dropdown) {
          $html .= $indent .'    <li class="nav-item dropdown">'.'<!-- '.__LINE__.' -->'.PHP_EOL;
          if ($active) {
            $html .= $indent .'      <a class="nav-link dropdown-toggle active" href="#"'.PHP_EOL;
            $html .= $indent .'        data-bs-toggle="dropdown" id="navbarItem'.$itemNum.'" aria-expanded="false">'.PHP_EOL;
          } else {
            $html .= $indent .'      <a class="nav-link dropdown-toggle" href="#" '.PHP_EOL;
            $html .= $indent .'        data-bs-toggle="dropdown" id="navbarItem'.$itemNum.'" aria-expanded="false">'.PHP_EOL;
          }
          $html .= $indent .'        ' . $label . PHP_EOL;
          $html .= $indent .'      </a>'.here(__LINE__) . PHP_EOL;
          $html .= $indent .'      <ul class="dropdown-menu" aria-labelledby="whatGoesHere">'.here(__LINE__) . PHP_EOL;
        } else {
          // Link to url if present otherwise link to "#"
          $target = (!empty($label) and !is_array($url)) ? $url : '#';
          if ($level == 1) {
            if ($active) {
              $html .= $indent . '    <li class="nav-item">'.PHP_EOL;
              $html .= $indent . '      <a class="nav-link active" href="'.$target.'" aria-current="page">'.$label.'</a>'.here(__LINE__).PHP_EOL;
              $html .= $indent . '    </li>'.PHP_EOL;
            } else {
              $html .= $indent . '    <li class="nav-item">'.PHP_EOL;
              $html .= $indent . '      <a class="nav-link" href="'.$target.'">'.$label.'</a>'.here(__LINE__).PHP_EOL;
              $html .= $indent . '    </li>'.PHP_EOL;
            }
          } else {
            if ($active) {
              $html .= $indent . '    <li>'.PHP_EOL;
              $html .= $indent . '      <a class="dropdown-item active" href="'.$target.'" aria-current="page">'.$label.'</a>'.here(__LINE__).PHP_EOL;
              $html .= $indent . '    </li>'.PHP_EOL;
            } else {
              $html .= $indent . '    <li>'.PHP_EOL;
              $html .= $indent . '      <a class="dropdown-item" href="'.$target.'">'.$label.'</a>'.here(__LINE__).PHP_EOL;
              $html .= $indent . '    </li>'.PHP_EOL;
            }
          }
        }

        // Run recursively if a nested array is found
        if (is_array($url)) {
          $html .= $this->render($url, $indent . '    ', $level + 1);
          //$html .= $indent.'    </li>' . here(__LINE__) . PHP_EOL;
        } else {
          //$html .= $indent.'    </li>' . here(__LINE__) . PHP_EOL;
        }
      }
    }

    // end item list
    $html .= $indent.'  </ul>' . here(__LINE__) . PHP_EOL;
    $html .= $indent.'</li>' . here(__LINE__) . PHP_EOL;

    return $html;
  }
}
