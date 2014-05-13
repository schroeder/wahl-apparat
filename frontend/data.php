<?php

/*
The MIT License (MIT)

Copyright (c) 2014 opendatacity.de // isozaponol.de

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/


/*

The following code is only a demonstration how to build a valid data array with PHP.
Do not use this code in productive systems unless you know what you are doing!

*/

// How many parties will be shown by default.
$columnOffset = 5;
// path to the party file name
$partyFileName = 'data/parteien.csv';
// path to the question file name
$dataFileName = 'data/data.csv';


/* Read the party information and build the array. */
$partyData = @file_get_contents($partyFileName);
$partySettings = array();
$partyNames = array();
$partyDataArray = CSVToArray($partyData, $partyNames);
foreach ($partyDataArray as $key => $row)
{
    $partySettings[strtolower($row[0])] = $row;
}

/* Read the questions and build the array. */
$data = @file_get_contents($dataFileName);
$names = array();
$dataArray = CSVToArray($data, $names);
$np = (count($names) - $columnOffset) / 2;
$nr = count($names);

/* Now proccess the questions and form the needed array structure */
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

    foreach ($partyList as $party)
    {
        $partyInfo = array();

        $partyInfo["id"] = $party;

        /*
            Read abbr., set to content field, if set. Else use the default setting
        */
        if ($partySettings && array_key_exists($party, $partySettings) && $partySettings[$party][1] != "")
        {
            $partyInfo["title"] = $partySettings[$party][1];
        }
        else
        {
            $partyInfo["title"] = "Unbekannt";
        }

        /*
            Read party full title., set to content field, if set. Else use the default setting
        */
        if ( isset($partySettings) && array_key_exists($party, $partySettings) && $partySettings[$party][2] != "")
        {
            $partyInfo["text"] = $partySettings[$party][2];
        }
        else
        {
            $partyInfo["text"] = "Keine Informationen verfügbar.";
        }

        /*
            Read party icon name., set to content field, if set. Else use the default setting
        */
        if ( isset($partySettings) && array_key_exists($party, $partySettings) && $partySettings[$party][3] != "")
        {
            $partyInfo["icon"] = $partySettings[$party][3];
        }
        else
        {
            $partyInfo["icon"] = "default.png";
        }

        /*
            Read party fill color., set to content field, if set. Else use the default setting
        */
        if ( isset($partySettings) && array_key_exists($party, $partySettings) && $partySettings[$party][4] != "")
        {
            $partyInfo["fill"] = $partySettings[$party][4];
        }
        else
        {
            $partyInfo["fill"] = "#000000";
        }

        /*
            Read party stroke title., set to content field, if set. Else use the default setting
        */
        if (isset($partySettings) && array_key_exists($party, $partySettings) && $partySettings[$party][5] != "")
        {
            $partyInfo["stroke"] = $partySettings[$party][5];
        }
        else
        {
            $partyInfo["stroke"] = $partyInfo["fill"];
        }

        $partyInfoList[] = $partyInfo;
    }

}

/* Convert all array to json... */
$thesenJson = json_encode($thesen);
$theseParteiJson = json_encode($thesePartei);
$theseParteiTextJson = json_encode($theseParteiText);
$partyInfoListJson = json_encode($partyInfoList);
/* and print them. */
echo 'var wom = {
    "thesen": ' . $thesenJson . ',
    "thesenparteien": ' . $theseParteiJson . ',
    "thesenparteientext": ' . $theseParteiTextJson . ',
    "parteien": ' . $partyInfoListJson . '
};';

exit;

/* helper function to convert csv data to php array, put all column names into $names array. */
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
