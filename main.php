<?php

    const _minQuantaProcess = 1;
    const _maxQuantaProcess = 5;
    const _minBurst = 1;
    const _maxBurst = 5;
    const _minStartTime = 0;
    const _maxStartTime = 10;

    $class = array();

//    $class = array(
//        "priority" => 0,
//        "finished" => false,
//        "proces" => array(
//                array()
//                "quanta" => rand ( _minQuantaProcess , _maxQuantaProcess )
//            )
//    );

    $output = array();

    $time = 0;


function roundRobin(&$list,$start,$ord){
    static $neterminate = array();
    $countIteration = 0;
    $quanta = $list["quantaDeTimp"];

    unset($list["quantaDeTimp"]);

    $timeRound=0;

    //daca clasa a fost in stare de asteptare pana la acest moment
    //tabelul de rezultat va fi incarcat cu G pana la momentul asta
    for ($g = 0; $g < count($list); $g++) {
        for ($i = $start; $i < $GLOBALS["time"]; $i++) {
            if(!isset($GLOBALS['output'][$ord][$g][$i])) {
                if($list[$g] != 0){
                    $GLOBALS['output'][$ord][$g][$i] = "G";
                }
            }
        }
    }


    foreach($list as $value){
        $timeRound += $value;
    }

    if(!isset($neterminate[$ord])){
        $index = 0;
    }
    else{
        $index = $neterminate[$ord];
    }


    while(($timeRound != $countIteration) and (getActualPrioriti() >= $ord)) {

        for ($j = $index; $j < count($list); $j++) {
            $i = 0;

            if (isset($list[$j]) and $list[$j]!=0) {
                while (($i != $quanta) and ($list[$j] != 0)) {
                    for ($g = 0; $g < count($list); $g++) {
                        if ($list[$g] != 0) {
                            $GLOBALS['output'][$ord][$g][$GLOBALS['time']] = "G";
                        }
                    }
                    $GLOBALS['output'][$ord][$j][$GLOBALS['time']] = "E";
                    $list[$j]--;
                    $i++;
                    $GLOBALS['time']++;
                    $countIteration++;
                    if (getActualPrioriti() < $ord) {
                        if ($timeRound == $countIteration){
                            $GLOBALS["class"][$ord]["finished"] = true;
                        }
                        $neterminate[$ord] = ($j+1) % count($list);
                        $list["quantaDeTimp"] = $quanta;
                        return;
                    }
                }
            }
        }
        $index = 0;
    }
    $GLOBALS["class"][$ord]["finished"] = true;
    $list["quantaDeTimp"] = $quanta;
}

function afisareRezultat($list){
    $maxKey = 0;

    ksort($list);

    reset($list);
    $minKey = key($list[0][0]);

    foreach($list as $class){
        foreach ($class as $value) {
            foreach ($value as $key => $stare) {
                if ($maxKey < $key) {
                    $maxKey = $key;
                }
            }
        }
    }

    echo "<br>";

    for($g = 0; $g<count($list);$g++){
        for($h = 0; $h<count($list[$g]);$h++) {
            for ($i = $minKey; $i <= $maxKey; $i++) {
                if (!isset($list[$g][$h][$i])) {
                    $list[$g][$h][$i] = " ";
                }
            }
        }
    }

    echo "<table>";

    echo"<tr>";
    echo "<th >Clasa</th>";
    echo "<th >Proces</th>";
    for($i = 0;$i<$GLOBALS["time"]-1;$i++){
        echo "<th >$i</th>";
    }
    echo"</tr>";

    foreach ($list as $key => $class) {
        $nr=0;

        echo"<tr class='clas'>";
        echo "<th class='clas' rowspan='".count($class)  ."'>".$key."</th>";
        foreach ($class as $key => $value) {
            if ($nr != 0)
                echo "<tr>";

            if ($nr == 0) {
                echo "<th class='clas'>P<sub>$key</sub></th>";
                for ($i = 0; $i < $maxKey; $i++) {
                    if (isset($value[$i])) {
                        echo "<td class='clas'>" . $value[$i] . "</td>";
                    } else {
                        echo "<td class='clas'> </td>";
                    }
                }
                echo "</tr>";
            }else{
                echo "<th >P<sub>$key</sub></th>";
                for ($i = 0; $i < $maxKey; $i++) {
                    if (isset($value[$i])) {
                        echo "<td>" . $value[$i] . "</td>";
                    } else {
                        echo "<td> </td>";
                    }
                }
                echo "</tr>";
            }
            $nr++;
        }
    }
    echo"</table>";
}

function generareDate(&$class) //generarea datelor aleator
{
    for ($i = 0; $i < 5; $i++) {
        $class[$i] = array(
            "priority" => $i,
            "finished" => false,
            "startTime" => rand(_minStartTime,_maxStartTime)
        );

        $class[$i]["proces"]["quantaDeTimp"] = rand(_minQuantaProcess, _maxQuantaProcess);

        for ($j = 0; $j < rand(1, 4); $j++) {
            $class[$i]["proces"][$j] = rand(_minBurst, _maxBurst);
        }
    }
}

function afisareDateGenerate(&$class){
    echo "<div class='afis'>";
    echo "<table>";
    foreach ($class as $key => $value) {
        echo "<tr> ";
        echo "<th> Clasa " . $key . "</th>";
        echo "<th> Quanta " . $value["proces"]["quantaDeTimp"] . "</th>";
        echo "<th> Start " . $value["startTime"] . "</th>";
        for($i = 0; $i < count($value["proces"])-1;$i++){
            echo "<th> P<sub>" .$i. "</sub> Burst time " .$value["proces"][$i]. "</th>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
}

function getActualPrioriti(){  //returneaza cel mai prioritar clasa
    $class = $GLOBALS["class"];
    $classPrioriti = 100000;


    foreach($class as $value){
        if(($value["startTime"] <= $GLOBALS["time"]) and ($value["priority"] < $classPrioriti ) and ($value["finished"] == false)){
            $classPrioriti = $value["priority"];
        }
    }

    return $classPrioriti;
}

function getNotFinished(){     //returneaza numarul de clase care nu sau finisat procesele
    $nr = 0;
    foreach ($GLOBALS["class"] as $value){
        if($value["finished"] == false){
            $nr++;
        }
    }
    return $nr;
}

function Prioritiy($class){

    //Atat timp cat mai exista procese neterminate va rula
    while(getNotFinished() != 0){
        $actual = getActualPrioriti();  //cea mai prioritara clasa
        if($actual == 100000){//                inseamna ca nu este inca nici un proces gata de executie
            $GLOBALS["time"]++;
        }else{
            roundRobin($GLOBALS["class"][$actual]['proces'], $GLOBALS["class"][$actual]['startTime'], $GLOBALS["class"][$actual]['priority']);
        }
    }
}


generareDate($class);
afisareDateGenerate($class);
Prioritiy($class);
afisareRezultat($output);

?>


