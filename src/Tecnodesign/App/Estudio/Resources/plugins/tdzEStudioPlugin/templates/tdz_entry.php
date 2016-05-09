<?php
/**
 * Default entry template
 *
 * @package      tdzEStudioPlugin
 * @author       Tecnodesign <ti@tecnodz.com>
 * @link         http://tecnodz.com/
 * @copyright    Tecnodesign (c) 2010
 * @version      SVN: $Id: tdz_entry.php 967 2011-12-08 09:45:41Z capile $
 */
$class='';$sf='';$dim='.200x100';
if(is_array($figures) && count($figures)>0)
{
  foreach($figures as $imgid=>$fig)
  {
    if(!isset($fig['src']))continue;
    $src=$fig['src'];
    if(strpos($src,$dim)===false)
    {
      if(preg_match('/^(.+)(\.[a-z]{3,4})$/',$src,$m))
        $src=$m[1].$dim.$m[2];
      else
        $src.=$dim;
    }
    $sf .= '<figure id="fig'.$imgid.'">';
    $fig += array('alt'=>'');
    $sf .= '<img alt="'.tdz::xmlEscape($fig['alt']).'" src="'.tdz::xmlEscape($src).'" border="0" />';
    if(isset($fig['title'])) $sf .= '<legend>'.tdz::xmlEscape($fig['title']).'</legend>';
    $sf .= '</figure>';
  }
  if($sf!='')
  {
    $class=' figures';  
    $sf = '<div class="media-thumbnail">'.$sf.'</div>';
  }
}
 
$s = '<article><div class="hentry tdze'.$class.'" id="e'.$id.'">';
$s .= '<h3 class="entry-title">'.(($link)?('<a href="'.tdz::xmlEscape($link).'" rel="bookmark" title="'.tdz::xmlEscape($title).'">'.tdz::xmlEscape($title).'</a>'):(tdz::xmlEscape($title))).'</h3>';
$s .= '<div class="entry-content">'.$sf.$summary.'</div>';
$pub = strtotime($published);
$s .= '<p class="date">';
if($pub)
  $s .= '<abbr class="published" title="'.date('c',$pub).'">'.date('d/m/Y H:i',$pub).'</abbr>';
/*
$mod = strtotime($updated);
if($mod)
  $s .= '<span class="updated">Última atualização <abbr class="updated" title="'.date('c',$mod).'">'.date('d/m/Y H:i',$mod).'</abbr></span>';
*/
$s .= '</p></div></article>';
echo $s;
