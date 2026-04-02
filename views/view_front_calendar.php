<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 20.01.2017
 * Time: 15:00
 */

function calendar_front(){

	wp_enqueue_style( 'cal_reset.css' );
	wp_enqueue_style( 'cal_style.css' );

	//Als erstes die Tage
	$days = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag");

	//Dann noch die Zeiten
	$times = array();
	for($i = 7 ; $i <= 23; $i++){
		$times[] = $i.":00";
		/*if($i < 23){
			$times[] = $i.":30";
		}*/
	}

/**
 * Jetzt zeigen wir mal die Timeline...
 */
echo '
<div style="height: auto">
<div class="cd-schedule loading eventcal">
	<div class="timeline">
		<ul>';
	foreach ($times as $t){
		echo '<li><span><b>'.$t.'</b></span></li>';
	}
echo'
		</ul>
	</div>
';

/**
 * Jetzt müssen wir die Events zeigen...
 */

echo '
	<div class="events">
		<ul>';

/**
 * Hier zeigen wir die einzelnen Tage und darin nachher die Events
 */

foreach ($days as $num => $day) {
	echo '
			<li class="events-group">
				<div class="top-info"><span style="background: #ccc"><b>'.$day.'</b></span></div>

				<ul>';

			$events = get_all_events_by_day($num);

			if($events != null) {

				foreach ($events as $event) {

					echo '
					<li class="single-event" data-start="'.$event->getEventStart(true).'" data-end="'.$event->getEventEnd(true).'" data-content="event-abs-circuit" data-event="event-1" data-color="'.$event->getEventColor().'" style="background: '.$event->getEventColor().' !important" >
						<a href="#0" >
							<em class="event-name">'.$event->getEventName().''.(strlen(trim($event->getEventSubtext())) > 0 ? '<p style="margin-top: 5px;">'.$event->getEventSubtext().'</p>': '').'</em>
							<div class="invis" style = "display: none">'.nl2br($event->getEventDescription()).'</div >
						</a >
					</li >';
				}
			}

	echo'			</ul>
			</li>';
}

echo'			
		</ul>
	</div>

';


/**
 * Das hier ist das end div das alles beendet... mit ein wenig COntent für das JavaScript
 */
echo '

<div class="event-modal">
		<header class="header">
			<div class="content">
				<span class="event-date"></span>
				<h3 class="event-name"></h3>
			</div>

			<div class="header-bg"></div>
		</header>

		<div class="body">
			<div class="event-info"></div>
			<div class="body-bg"></div>
		</div>

		<a href="#0" class="close">Close</a>
	</div>

	<div class="cover-layer"></div>

</div>
</div>';
}

?>