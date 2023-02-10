<?php

system("clear");


$show = 'show tables;';
$user_prompt = "";

do {
    $user_prompt = readline("mysql>");
    $comma = substr($user_prompt, strlen($user_prompt) - 1, strlen($user_prompt));

    if ($comma != ";") {
        do {
            $user_prompt_children = readline("     >");
            $comma = substr($user_prompt_children, strlen($user_prompt_children) - 1, strlen($user_prompt_children));
        } while ($comma != ";");
    }

    $comma = " ";

    switch ($user_prompt) {
        case $show:
            $data = showTables();
            generateTable($data);
            break;
        case strtoupper($show):
            $data = showTables();
            generateTable($data);
            break;
        case 'show tables;':
            $data = showTables();
            generateTable($data);
            break;
        case 'SHOW tables;':
            $data = showTables();
            generateTable($data);
            break;
        default:
            // helpShowTables();
            break;
    }

    $separate_array = explode(" ", $user_prompt);
    if (($separate_array[0] == "describe" || $separate_array[0] == "DESCRIBE") || ($separate_array[0] == "desc" || $separate_array[0] == "DESC")) {

        $length_word = array();
        echo trim(str_replace(";", "", trim($separate_array[1])));

        $return = describe(trim(str_replace(";", "", trim($separate_array[1]))));
        if (is_array($return)) {
            print_r($return);
            $length_word = generateLongTable($return);
            print_r($length_word);
        }
    }

    if (($separate_array[0] == "select" && $separate_array[2] == "from") || ($separate_array[0] == "SELECT" && $separate_array[2] == "FROM")) {
        $data = selectFrom(trim($separate_array[1]), trim(str_replace(";", "", $separate_array[3])));
        generateTable($data);
    }
} while ($user_prompt != "exit;");


function generateLongTable($array)
{
    return lengthColumn($array);
}

function lengthColumn($array)
{
    $i = 0;
    $width = 0;
    $new_array = [];
    foreach ($array as $key => $value) {
        foreach ($array[$key] as $key2 => $val) {
            if (is_int($key2)) {
                if ($key2 + 1 == $i) {
                    if ($width < strlen($key2)) {
                        $width = strlen($key2);
                    }

                    if ($width < strlen($val)) {
                        $width = strlen($val);
                    }
                    $new_array[$i] = $width;
                }
            } else {
                $i++;
            }
        }
        $i = 0;
    }
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

function connectToDatabase()
{
    return new \PDO("mysql:dbname=cinema;host=localhost", "damien", "PETITnuage-26");
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

function margeMiddle($length_of_line, $len_of_word)
{
    $len = $length_of_line - $len_of_word - 2;
    $line = "";
    for ($i = 0; $i < $len; $i++) {
        $line .= " ";
    }

    return $line;
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