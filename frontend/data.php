<?php


$columnOffset = 5;

$data = @file_get_contents('data/data.csv');
$partyData = @file_get_contents('data/parteien.csv');

$partySettings = array();

$partyNames = array();
$partyDataArray = CSVToArray($partyData, $partyNames);
echo "<pre>";
var_dump($partyDataArray);
echo "</pre>";
echo "<pre>";
var_dump($partyNames);
echo "</pre>";
/*
        $rows = $partiesData->attribute('rows');

        foreach ($rows['sequential'] as $key => $row)
        {
            $partySettings[strtolower($row["columns"][0])] = $row["columns"];
        }
*/

$names = array();
$dataArray = CSVToArray($data, $names);
$np = (count($names) - $columnOffset) / 2;
$nr = count($names);

if ($dataArray)
{
    $thesen = array();
    foreach ($dataArray as $question)
    {
        $these = array();
        $thesePartei = array();
        $these["title"]   = $question[1];
        $these["text"]    = $question[2];
        $these["text2"]   = $question[3];
        $these["reverse"] = false;
    
        $thesen[] = $these;
    }
    $thesePartei = array();
    $j = 0;
    foreach ($dataArray as $key => $question)
    {
        $thesePartei[$key] = array();
        for ($i=$columnOffset;$i < $nr; $i=$i+2)
        {

            $answer = 0;
            if (strtolower($question[$i]) == "ja")
            {
                $answer = 1;
            }
            if (strtolower($question[$i]) == "nein")
            {
                $answer = -1;
            }
            $thesePartei[$j][] = $answer;
        }
        $j++;
    }
    $theseParteiText = array();
    $j = 0;
    foreach ($dataArray as $key => $question)
    {
        $theseParteiText[$key] = array();
        for ($i=$columnOffset+1;$i < $nr; $i=$i+2)
        {
            $theseParteiText[$j][] = $question[$i];
        }
        $j++;
    }

    $partyList = array();
    for ($i=$columnOffset;$i < $nr; $i=$i+2)
    {
        $partyList[] = strtolower($names[$i]);
    }

//    $partyNameList = $electionINI->variable( 'PartySettings','PartyName' );
//    $partyLongNameList = $electionINI->variable( 'PartySettings','PartyLongName' );
//    $partyIconList = $electionINI->variable( 'PartySettings','PartyIcon' );
//    $partyColorFillList = $electionINI->variable( 'PartySettings','PartyColorFill' );
//    $partyColorStrokeList = $electionINI->variable( 'PartySettings','PartyColorStroke' );
//    $partyInfoList = array();

    foreach ($partyList as $party)
    {
        $partyInfo = array();

        $partyInfo["id"] = $party;

        /*
            Read abbr., set to content field, if set. Else use the default setting
        */
        if ($partySettings && array_key_exists($party, $partySettings) && $partySettings[$party][0] != "")
        {
            $partyInfo["title"] = $partySettings[$party][0];
        }
        elseif (isset($partyNameList) && array_key_exists($party, $partyNameList) && $partyNameList[$party] != "")
        {
            $partyInfo["title"] = $partyNameList[$party];
        }
        else
        {
            $partyInfo["title"] = "Unbekannt";
        }

        /*
            Read party full title., set to content field, if set. Else use the default setting
        */
        if ( isset($partySettings) && array_key_exists($party, $partySettings) && $partySettings[$party][1] != "")
        {
            $partyInfo["text"] = $partySettings[$party][1];
        }
        elseif ( isset($partyLongNameList) && array_key_exists($party, $partyLongNameList) && $partyLongNameList[$party] != "")
        {
            $partyInfo["text"] = $partyLongNameList[$party];
        }
        else
        {
            $partyInfo["text"] = "Keine Informationen verfügbar.";
        }

        /*
            Read party icon name., set to content field, if set. Else use the default setting
        */
        if ( isset($partySettings) && array_key_exists($party, $partySettings) && $partySettings[$party][2] != "")
        {
            $partyInfo["icon"] = $partySettings[$party][2];
        }
        elseif ( isset($partyIconList) && array_key_exists($party, $partyIconList) && $partyIconList[$party] != "")
        {
            $partyInfo["icon"] = $partyIconList[$party];
        }
        else
        {
            $partyInfo["icon"] = "default.png";
        }

        /*
            Read party fill color., set to content field, if set. Else use the default setting
        */
        if ( isset($partySettings) && array_key_exists($party, $partySettings) && $partySettings[$party][3] != "")
        {
            $partyInfo["fill"] = $partySettings[$party][3];
        }
        elseif (isset($partyColorFillList) && array_key_exists($party, $partyColorFillList) && $partyColorFillList[$party] != "")
        {
            $partyInfo["fill"] = $partyColorFillList[$party];
        }
        else
        {
            $partyInfo["fill"] = "000000";
        }

        /*
            Read party stroke title., set to content field, if set. Else use the default setting
        */
        if (isset($partySettings) && array_key_exists($party, $partySettings) && $partySettings[$party][4] != "")
        {
            $partyInfo["stroke"] = $partySettings[$party][4];
        }
        elseif (isset($partyColorStrokeList) && array_key_exists($party, $partyColorStrokeList) && $partyColorStrokeList[$party] != "")
        {
            $partyInfo["stroke"] = $partyColorFillList[$party];
        }

        $partyInfoList[] = $partyInfo;
    }

}

$thesenJson = json_encode($thesen);
$theseParteiJson = json_encode($thesePartei);
$theseParteiTextJson = json_encode($theseParteiText);
$partyInfoListJson = json_encode($partyInfoList);
echo 'var wom = {
    "thesen": ' . $thesenJson . ',
    "thesenparteien": ' . $theseParteiJson . ',
    "thesenparteientext": ' . $theseParteiTextJson . ',
    "parteien": ' . $partyInfoListJson . '
};';
exit;

echo 'var wom = {
    "thesen": [ ],
    "thesenparteien": [ ],
    "parteien": [ ]
};';


function CSVToArray($csv, &$names, $delimiter = ',', $enclosure = '"', $escape = '\\', $terminator = "\n")
{ 
    $result = array(); 
    $rows = explode($terminator,trim($csv)); 

    $names = array_shift($rows); 
    $names = str_getcsv($names,$delimiter,$enclosure,$escape);

    $nc = count($names); 
    foreach ($rows as $row)
    { 
        if (trim($row))
        {
            $values = str_getcsv($row,$delimiter,$enclosure,$escape); 
            if (!$values)
            {
                $values = array_fill(0,$nc,null);
            }
            $result[] = $values; 
        } 
    } 
    return $result; 
}

?>
