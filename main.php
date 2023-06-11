<?php

system("clear");


$show = 'show tables;';
$user_prompt = "";
$bool = true;
$dbname = "";
$bool_comma = false;
$user_prompt_children = "";

do {
    $bool = true;
    $user_prompt = readline("mysql> ");
    $comma = substr(trim($user_prompt), strlen($user_prompt) - 1, strlen($user_prompt));

    if ($comma != ";" && $user_prompt != "") {
        do {
            $user_prompt_children = readline("     > ");
            $comma = substr($user_prompt_children, strlen($user_prompt_children) - 1, strlen($user_prompt_children));
            $bool = true;
        } while ($comma != ";");
    }

    $comma = " ";

    if ($bool == true && trim($user_prompt_children) == ";") {
        $user_prompt .= ";";
    }

    $separate_array = explode(" ", $user_prompt);

    if ($separate_array[0] == "use") {
        $dbname = trim(str_replace(";", "", $separate_array[1]));
        $data = multipleQuery(trim(str_replace(";", "", $user_prompt)),$dbname);
        if (is_array($data)) {
            echo "using $dbname...\n";
        }else{
            echo $data."\n";
            $dbname="";
        }
    }else {
        
    

    $user_prompt = trim($user_prompt);
    
    // echo $user_prompt;
    // echo "___".str_replace(" ","",$user_prompt)."___";

    $check = str_replace(" ", "", $user_prompt);
    if ($user_prompt != "clear;") {
        if ($user_prompt != "exit;" && $check != ";") {
            if (!empty($user_prompt) || $user_prompt == ";" || $user_prompt == " ") {
                $data = multipleQuery(trim(str_replace(";", "", $user_prompt)),$dbname);
                if (is_array($data)) {
                    generateLongTable($data);
                } else {
                    echo $data . "\n";
                }
            }
        }
    }else{
        system("clear");
    }

}
} while (trim($user_prompt) != "exit;");

function multipleQuery($query,$dbname="cinema")
{

    $array = [];
    $query_verified = html_entity_decode($query);
    try {
        $db = connectToDatabase($dbname);
        $statement = $db->prepare($query_verified);
        $statement->execute();
        $array = $statement->fetchAll();
    } catch (\PDOException $e) {
        return "Erreur : " . $e->getMessage();
    }
    return $array;
}
function generateLongTable($array)
{
    $length_column_arr = lengthColumn($array);
    $head_lines = generateHead($length_column_arr, $array);

    $body_lines = generateBody($head_lines[1], $array);
    // print_r($array);
    // print_r($length_column_arr);
    // print_r($array);
    // $separator = createSeparator($head_lines[1], $array);
    echo $head_lines[2];
    echo $head_lines[0];
    echo $head_lines[2];
    echo $body_lines;
    echo $head_lines[2];
}

function generateBody($length_column_arr, $array)
{
    $i = 0;
    $j = 0;
    $line = "";
    foreach ($array as $key => &$value) {
        $line .= "|";
        foreach ($value as $key2 => &$val) {
            if (is_int($key2)) {
                if (strlen($val) >= $length_column_arr[$i]) {
                    // $length_column_arr[$i] = strlen($val);
                    // echo "sup";
                    $line .= " " . $val . generateSpace($length_column_arr[$i], strlen($val)) . " ";
                } else {
                    // echo "min";
                    // if ($val=="") {
                    //     $line .= " ".generateSpace($length_column_arr[$i]) ." ";
                    // }else{

                    $line .= " " . $val . generateSpace($length_column_arr[$i], strlen($val)) . " ";
                    // }
                }
                // echo $length_column_arr[$i];
                // echo "\n";
                $i++;
                $line .= "|";
            }
        }
        $i = 0;
        $j++;
        $line .= "\n";
    }



    return $line;
}

function margeMiddle($length_of_line, $len_of_word)
{
    $len = $length_of_line - $len_of_word - 2;
    $line = "";
    for ($i = 0; $i < $len; $i++) {
        $line .= " ";
    }
    // echo "__($line)____";
    return $line;
}

function generateSpace($length_of_line, $len_of_word)
{
    $len = $length_of_line - $len_of_word;
    $line = "";
    for ($i = 0; $i < $len; $i++) {
        $line .= " ";
    }
    // echo "__($line)____";
    return $line;
}

function generateHead($length_column_arr, $array)
{
    $i = 0;
    $line = "|";
    foreach ($array as $key => &$value) {
        # code...

        foreach ($value as $key2 => &$val) {
            if (is_string($key2) && $key == 0) {
                if (strlen($key2) >= $length_column_arr[$i]) {
                    $length_column_arr[$i] = strlen($key2);
                    $line .= " " . $key2 . generateSpace($length_column_arr[$i], strlen($key2)) . " ";
                } else {
                    $line .= " " . $key2 . generateSpace($length_column_arr[$i], strlen($key2)) . " ";
                }
                // echo $length_column_arr[$i];
                // echo "\n";
                $i++;
                $line .= "|";
            }

        }
    }

    // print_r($array);
    $line .= "\n";
    $arrayLine_Length = [];
    $arrayLine_Length[0] = $line;
    $arrayLine_Length[1] = $length_column_arr;

    $new_separator = createSeparator($length_column_arr, $array);
    $arrayLine_Length[2] = $new_separator;

    return $arrayLine_Length;
}

function createSeparator($length_column_arr, $array)
{
    $i = 0;
    $line = "+";
    foreach ($array as $key => &$value) {
        # code...

        foreach ($value as $key2 => &$val) {
            if (is_string($key2) && $key == 0) {
                for ($j = 0; $j < $length_column_arr[$i]; $j++) {
                    $line .= "-";
                }
                $i++;
                $line .= "--+";
            }

        }
    }

    // print_r($array);

    $line .= "\n";
    return $line;
}


function lengthColumn($array)
{
    $i = 0;
    $width = 0;
    $new_array = [];
    $j = 1;
    foreach ($array as $key => $value) {
        foreach ($value as $key2 => $val) {
            // echo "KEY : $key\n";
            // echo $key2."\n";
            // echo $val."\n";
            if ($key == 0) {
                # code...
                $new_array[$i] = $width;
            }

            if (is_string($key2)) {
                // if ($key2 + 1 == $i) {
                if (strlen($new_array[$i]) < strlen($key2)) {
                    $width = strlen($key2);
                    // echo "$i : $width [key2 $key2][val $val];\n";
                    $new_array[$i] = $val;
                }

                if (strlen($new_array[$i]) < strlen($val)) {
                    $width = strlen($val);
                    // echo "$i : $width [key2 $key2][val $val];\n";
                    $new_array[$i] = $val;
                    // echo $val."\n";
                }

                //}
            }

            if (is_int($key2)) {
                if (strlen($new_array[$i]) < strlen($val)) {
                    $width = strlen($val);
                    // echo "$i : $width [key2 $key2][val $val];\n";
                    $new_array[$i] = $val;
                }


                $i++;
            }

            $j++;



            if (is_int($key2)) {
                $width = 0;
            }
        }

        $j = 0;
        $i = 0;
        // print_r($value);
    }

    foreach ($new_array as $key => &$value) {
        $value = strlen($value);
    }
    //print_r($new_array);
    return $new_array;
}

function describe($table_name)
{

    $array = [];
    $table_name = html_entity_decode($table_name);
    try {
        $db = connectToDatabase();
        $statement = $db->prepare("describe $table_name;");
        $statement->execute();
        $array = $statement->fetchAll();
    } catch (\Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
    return $array;

}

function selectFrom($selection, $table_name)
{
    $array = [];
    $table_name = html_entity_decode($table_name);
    try {
        $db = connectToDatabase();
        $statement = $db->prepare("select $selection from $table_name;");
        $statement->execute();
        $array = $statement->fetchAll();
    } catch (\Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
    return $array;
}

function widthTable($array)
{
    $width = 1;
    foreach ($array as $value) {
        foreach ($value as $key => $val) {

            if ($width < strlen($val)) {
                $width = strlen($val);
            }

            if ($key != 0 && $width < strlen($key)) {
                $width = strlen($key);
            }
        }

    }

    return $width;
}

function generateTable($array)
{
    $width = widthTable($array);
    $db_name = getDbName();

    if (strlen($db_name) > $width) {
        $width = strlen($db_name);
    }

    $length_of_line = $width + 2;

    // TEST FOREACH
    foreach ($array as $key => $value) {
        foreach ($value as $key2 => $val) {
            if ($key == 0) {
                if ($key2 == 0) {
                    $len_of_word = strlen(getDbName());
                    echo separatorTable($length_of_line);
                    echo margeLeft() . $db_name . margeMiddle($length_of_line, $len_of_word) . margeRight();
                    echo separatorTable($length_of_line);
                }
            } else {
                if ($key2 == 0) {
                    $len_of_word = strlen($val);
                    if ($len_of_word == $width) {
                        echo margeLeft() . $val . margeRight();
                    } else {
                        echo margeLeft() . $val . margeMiddle($length_of_line, $len_of_word) . margeRight();
                    }
                }
            }
            # code...
        }
    }

    echo separatorTable($length_of_line);
}

function showTables()
{
    $array = [];
    try {
        $db = connectToDatabase();
        $statement = $db->prepare("show tables;");
        $statement->execute();
        $array = $statement->fetchAll();
    } catch (\Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
    return $array;
}

function helpShowTables()
{
    echo "help> syntax -> show tables;\n";
}

function connectToDatabase($dbname="cinema")
{
    return new \PDO("mysql:dbname=$dbname;host=localhost", "damien", "PETITnuage-26");
}


function separatorTable($length_of_line)
{
    $line = "+";
    for ($i = 0; $i < $length_of_line; $i++) {
        $line .= "-";
    }
    $line .= "+\n";
    return $line;
}

function margeLeft()
{
    return "| ";
}

function margeRight()
{
    return " |\n";
}

// UNUSED
// function heightTable($array)
// {
//     foreach ($array as $key => $value) {
//         $height = $key;
//     }

//     return $height + 2;
// }

function getDbName()
{

    $array = showTables();

    foreach ($array as $value) {
        foreach ($value as $key => $val) {
            if ($key != 0) {
                return $key;
            }
        }
    }
}