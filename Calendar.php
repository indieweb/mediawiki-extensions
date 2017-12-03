<?php

$wgExtensionCredits['parserhook'][] = array(
  'name' => 'Calendar',
  'author' => 'Aaron Parecki',
  'description' => 'Adds <nowiki><calendar></nowiki> tag for creation of a single month calendar',
  'url' => 'https://github.com/indieweb/mediawiki-extensions'
);

$wgHooks['ParserFirstCallInit'][] = function(Parser &$parser) {
  $parser->setHook("calendar", function($input, $config) {
    $lines = explode("\n", $input);

    $month = isset($config['month']) ? $config['month'] : date('m');
    $year  = isset($config['year']) ? $config['year'] : date('Y');

    $mwCalendar = new mwMonthCalendar($month, $year);

    foreach( $config as $key=>$val )
    {
      $mwCalendar->setConfig($key, $val);
    }

    foreach($lines as $line)
    {
      if( trim($line) != '' ) 
      {
        $eventDay = substr($line,0,strpos($line,' '));
        $eventText = substr($line,strpos($line,' ')+1);
        $mwCalendar->addEvent($eventDay, $eventText);
      }
    }

    return $mwCalendar->show();
  });
  return true;
};


class mwMonthCalendar
{
  private $year;
  private $month;
  private $timestamp;
  private $config;
  private $events;

  function __construct($month, $year)
  {
    $this->year = $year;
    $this->month = $month;
    $this->timestamp = mktime(0,0,0,$month,1,$year);
    $this->config['tableWidth'] = '100%';
    $this->config['showHeader'] = 1;
    $this->config['cellHeight'] = 90;
    $this->config['weekStart'] = 'Monday';
    $this->config['highlightToday'] = 1;
  }

  function addEvent($eventDate, $eventText)
  {
    $this->events[$eventDate] = $eventText;
  }

  function setConfig($key, $val)
  {
    $this->config[$key] = $val;
  }

  function show()
  {
    global $wgOut, $wgParser, $wgLocalTZoffset;
    
    $s = ($this->config['weekStart'] == 'Monday' ? 1 : 0);
    $o = '';

    ob_start();
    ?>
<style>
.mwMonthCalendar table { border-collapse: collapse; }
.cal-week, .cal-cell { border: 1px #DFDFDF solid; padding: 2px; }
.cal-week { text-align: center; font-weight: bold; background-color: #EFEFEF; }
.cal-cell { vertical-align: top; height: <?php echo $this->config['cellHeight']; ?>px; width: 14%; }
.cal-day { text-align: right; font-weight: bold; background-color: #EFEFEF; color: #333333; }
.cal-weekend { background-color: #f5f5f5; }
.cal-weekend .cal-day { background-color: #e1e1e1; }
.cal-today { background-color: #FFFFDF; }
</style>
    <?php
    $css = ob_get_clean();

    // $wgOut->addInlineStyle($css);

    $o .= $css;
    $o .= '<div class="mwMonthCalendar">';
    $o .= '<table width="'.$this->config['tableWidth'].'">';
    if( $this->config['showHeader'] ) { $o .= '<tr><td colspan="7"><h2><div class="mw-headline">'.strftime('%B %Y', $this->timestamp).'</div></h2></td></tr>'; }
    $o .= '<tr>';
    for( $i=0; $i<7; $i++ ) 
    {
      $o .= '<td class="cal-week">'.strftime('%A',strtotime(sprintf(($s==1?'2007-10':'2008-06').'-%02d',$i+1))).'</td>';
    }
    $o .= '</tr>';
    
    $firstDayOfWeek = strftime('%u', $this->timestamp);
    
    $lastDayOfWeek = strftime(($s==1?'%u':'%w'),mktime(0,0,0,$this->month,date('t',$this->timestamp),$this->year));
    if( $firstDayOfWeek > $s )
    {
      $o .= '<tr>';
      for( $i=$s; $i<$firstDayOfWeek; $i++ )
      {
        $o .= '<td class="cal-cell cal-' . ($i==6||$i==7 ? 'weekend' : 'weekday') . '">&nbsp;</td>';
      }
    }
    for( $i=1; $i<=date('t',$this->timestamp); $i++ ) 
    {
      $thisDay = mktime(0,0,0,$this->month,$i,$this->year);
      if( strftime('%w',$thisDay) == $s )
      {
        $o .= '</tr>';
      }

      $o .= '<td class="cal-cell'
        .( ($this->config['highlightToday'] && date('Y-m-d',strtotime(($wgLocalTZoffset/60).' hours')) == date('Y-m-d',$thisDay)) ? ' cal-today':'' )
        .' '.( date('D', $thisDay) == 'Sat' || date('D', $thisDay) == 'Sun' ? 'cal-weekend' : 'cal-weekday' )
        .'">';
      $o .= '<div class="cal-day">'.$i.'</div>';
      $o .= '<p>'.(array_key_exists($i,$this->events)?$wgParser->recursiveTagParse($this->events[$i]):'&nbsp;').'</p>';
      $o .= '</td>';

      if( strftime(($s==1?'%u':'%w'),$thisDay) == ($s==1?7:6) )
      {
        $o .= '</tr>';
      }
    }
    if( $lastDayOfWeek < ($s==1?7:6) )
    {
      for( $i=$lastDayOfWeek; $i<($s==1?7:6); $i++ )
      {
        $o .= '<td class="cal-cell cal-' . ($i==6||$i==5 ? 'weekend' : 'weekday') . '">&nbsp;</td>';
      }
      $o .= '</tr>';
    }
    
    $o .= '</table>';
    $o .= '</div>';
    return $o;    
  }

}

?>
