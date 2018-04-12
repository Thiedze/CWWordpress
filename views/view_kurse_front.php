<?php
/**
 * Created by IntelliJ IDEA.
 * User: JoetheJunkie
 * Date: 27.10.2016
 * Time: 18:25
 */

function qu_vars($vars){
	$vars[]= 'kursname';
	return $vars;
}

function kurse_front(){
    global $wp_query;

    $ret = '';

    if(isset($_GET['kursname'])){
        if($item = get_kurs_by_beauty(urldecode($_GET['kursname']))){

            $ret .= '<script>
                        jQuery(\'.entry-title\').hide();
                     </script>';
            /*$ret .= '<button onclick="location.href=\''.get_site_url().'/'.$wp_query->query_vars['pagename'].'\'">
                        Zur&uuml;ck
                     </button><p>&nbsp;</p>';*/
            $ret .= '<img src="'.$item->getBild().'" style="height:100px"/>';
            $ret .= '<h2>'.$item->getName().'</h2>';
	        $ret .= '<p><b>Maximale Teilnehmerzahl:</b> '.$item->getMaxTeilnehmer().'</p>';
            $ret .= wpautop($item->getBeschreibung());
            $ret .= '<p>&nbsp;</p>
                     <button onclick="location.href=\''.get_site_url().'/'.$wp_query->query_vars['pagename'].'\'">
                        Zur&uuml;ck
                     </button>';

            return $ret;
        }
    }

    $kurse = get_all_kurse();

    $counter = 0;

    foreach ($kurse as $kurs){

	    if(!$kurs->getShowFront()){
		    continue;
	    }

        if($counter % 2 == 0){
            $ret .= '<div class="row">';
        }

        $frei = max(($kurs->getMaxTeilnehmer()-$kurs->getTeilnehmer()),0);

        $ret.= '
        <div class="col-md-6 col-xs-12 col-lg-6 col-sm-6 kurse_a" style="padding: 20px;text-align: center">
            <a href="./?kursname='.$kurs->getBeautyName().'" style="text-decoration: none">
                <section>
                    <img src="'.$kurs->getBild().'" style="height:100px"><br />
                    <h4 style="font-weight: bold">'.$kurs->getName().'</h4>
                    '.($frei <= 0 ? '<span style="color: #d00;font-weight: bold">Ausgebucht</span>' : $frei.'  Pl&auml;tze frei').'
                </section>
            </a>
        </div>
            
        ';

        if($counter % 2 == 1){
            $ret .= '</div>';
        }

        $counter++;

    }

    if($counter % 2 == 1){
        $ret .= '</div>';
    }

    return $ret;

}

?>