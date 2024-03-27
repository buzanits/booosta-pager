<?php
namespace booosta\pager;

use \booosta\Framework as b;
b::init_module('pager');

class Pager extends \booosta\base\Module
{ 
  use moduletrait_pager;

  protected $start, $end, $size, $first, $url;
  protected $firstsymbol, $prevsymbol, $nextsymbol, $lastsymbol;
  protected $max_showpages;
  protected $actclass, $pagerclass = 'pager';

  public function __construct($url = null, $start = 1, $end = 20, $size = 10, $first = 0)
  {
    $this->start = $start;
    $this->end = $end;
    $this->size = $size;
    $this->url = $url;
    $this->first = $first;

    $this->firstsymbol = '<<';
    $this->prevsymbol = '<';
    $this->nextsymbol = '>';
    $this->lastsymbol = '>>';

    $this->max_showpages = null;
  }

  public function set_start($val) { $this->start = $val; }
  public function set_end($val) { $this->end = $val; }
  public function set_size($val) { $this->size = $val; }
  public function set_url($val) { $this->url = $val; }
  public function set_first($val) { $this->first = $val; }

  public function set_firstsymbol($symbol) { $this->firstsymbol = $symbol; }
  public function set_prevsymbol($symbol) { $this->prevsymbol = $symbol; }
  public function set_nextsymbol($symbol) { $this->nextsymbol = $symbol; }
  public function set_lastsymbol($symbol) { $this->lastsymbol = $symbol; }

  public function set_actclass($actclass) { $this->actclass = $actclass; }
  public function set_pagerclass($pagerclass) { $this->pagerclass = $pagerclass; }

  public function set_max_showpages($max_showpages) { $this->max_showpages = $max_showpages; }

  public function set_symbols($first, $prev, $next, $last)
  {
    $this->firstsymbol = $first;
    $this->prevsymbol = $prev;
    $this->nextsymbol = $next;
    $this->lastsymbol = $last;
  }

  public function get_html()
  {
    $pages = ceil(($this->end - $this->first + 1) / $this->size);
    $actpage = ceil(($this->start - $this->first + 1) / $this->size);

    if($actpage > 1):
      $firststr = $this->firstsymbol; 
      $prevstr = $this->prevsymbol;
    else:
      $firststr = '';
      $prevstr = '';
    endif;

    if($actpage < $pages):
      $laststr = $this->lastsymbol; 
      $nextstr = $this->nextsymbol;
    else:
      $laststr = '';
      $nextstr = '';
    endif;

    $firsturl = str_replace('{num}', $this->first, $this->url);
    if($firststr) $firstlink = "{LINK|$firststr|$firsturl}";
    $prevurl = str_replace('{num}', max($this->first, $this->start - $this->size), $this->url);
    if($prevstr) $prevlink = "{LINK|$prevstr|$prevurl}";

    $lasturl = str_replace('{num}', ($pages - 1) * $this->size + $this->first, $this->url);
    if($laststr) $lastlink = "{LINK|$laststr|$lasturl}";
    $nexturl = str_replace('{num}', min($pages * $this->size + $this->first, $this->start + $this->size), $this->url);
    if($nextstr) $nextlink = "{LINK|$nextstr|$nexturl}";

    $result = "<div class='$this->pagerclass'><span class='pager_left'>$firstlink $prevlink</span>";

    if($this->max_showpages === null || $this->max_showpages >= $pages):
      $startpage = 1;
      $lastpage = $pages;
    else:
      $virtual_startpage = $actpage - floor($this->max_showpages / 2);
      $virtual_lastpage = $actpage + ceil($this->max_showpages / 2) - 1;

      $startpage = max($virtual_startpage, 1);
      $lastpage = min($virtual_lastpage, $pages);

      if($startpage > $virtual_startpage) $lastpage += $startpage - $virtual_startpage;
      if($lastpage < $virtual_lastpage) $startpage -= $virtual_lastpage - $lastpage;
    endif;

    for($page = $startpage; $page <= $lastpage; $page++):
      if($page == $actpage):
        if($this->actclass):
          $prefix = "<span class='$this->actclass'>";
          $postfix = "</span>";
        else:
          $prefix = '<b>';
          $postfix = '</b>';
        endif;
        
        $link = $page;
      else:
        $prefix = ' ';
        $postfix = ' ';
        $url = str_replace('{num}', ($page - 1) * $this->size + $this->first, $this->url);
        $link = "{LINK|$page|$url}";
      endif;

      $result .= " $prefix $link $postfix ";
    endfor;

    $result .= "<span class='pager_right'>$nextlink $lastlink</span></div>";

    return $result;
  }
}
