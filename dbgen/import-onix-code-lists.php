<?php

// invoke like:
// php import-onix-code-lists.php root supermoon

$xmlDataSource = "http://www.editeur.org/files/ONIX%20for%20books%20-%20code%20lists/ONIX_BookProduct_Codelists_Issue_34.xml";

$dbHost = "localhost";
$dbUser = $argv[ 1 ];
$dbDatabaseName = $argv[ 2 ];
$dbPassword = $argv[ 3 ];

/*
 * For each code list
 *   Drop possibly non-existent table
 *   Create new table
 *   For each code
 *     Insert code values into new row
 *   /For
 * /For
 *
 */

$xml = file_get_contents( $xmlDataSource );

$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->loadXML( $xml );

$xpath = new DOMXpath( $doc );

$mysqli = new mysqli( $dbHost, $dbUser, $dbPassword, $dbDatabaseName );
if( $mysqli->connect_errno )
{
    echo "Error: Failed to make a MySQL connection, here is why: \n";
    echo "Errno: " . $mysqli->connect_errno . "\n";
    echo "Error: " . $mysqli->connect_error . "\n";
    exit;
}

$codeLists = $xpath->query( "/ONIXCodeTable/CodeList" );
$numberOfCodeLists = $codeLists->length;
echo "We have " . $numberOfCodeLists . " code lists.\n";
for( $n=1; $n<$numberOfCodeLists; $n+=1 )
{
    $codeListNumber = $codeLists = $xpath->query( "/ONIXCodeTable/CodeList[ " . $n . " ]/CodeListNumber" )->item( 0 )->nodeValue;
    $codeListDescription = $xpath->query( "/ONIXCodeTable/CodeList[ " . $n . " ]/CodeListDescription" )->item( 0 )->nodeValue;
    $issueNumber = $xpath->query( "/ONIXCodeTable/CodeList[ " . $n . " ]/IssueNumber" )->item( 0 )->nodeValue;
    echo "Importing code list #" . $codeListNumber . " '" . $codeListDescription . "' Issue #" . $issueNumber . "\n";
    
    // create/truncate table
    $tableName = "onix_code_list_" . $codeListNumber;
    $sqlTable = "create table if not exists " . $tableName;
    $sqlTable .= "(";
    $sqlTable .= "code_value varchar(32) not null,";
    $sqlTable .= "code_description varchar(255),";
    $sqlTable .= "code_notes varchar(255),";
    $sqlTable .= "issue_number integer(4),";
    $sqlTable .= "primary key( code_value )";
    $sqlTable .= ")";
    $mysqli->query( $sqlTable );

    // truncate    
    $sqlTruncate = "truncate " . $tableName;
    $mysqli->query( $sqlTruncate );

    $codes = $xpath->query( "/ONIXCodeTable/CodeList[ " . $n . " ]/Code" );
    $numberOfCodes = $codes->length;
    for( $m=1; $m<=$numberOfCodes; $m+=1 )
    {
        // insert new code
        $data["CodeValue"]          = $xpath->query( "/ONIXCodeTable/CodeList[ " . $n . " ]/Code[ " . $m . " ]/CodeValue" )->item( 0 )->nodeValue;
        $data["CodeDescription"]    = $xpath->query( "/ONIXCodeTable/CodeList[ " . $n . " ]/Code[ " . $m . " ]/CodeDescription" )->item( 0 )->nodeValue;
        $data["CodeNotes"]          = $xpath->query( "/ONIXCodeTable/CodeList[ " . $n . " ]/Code[ " . $m . " ]/CodeNotes" )->item( 0 )->nodeValue;
        $data["IssueNumber"]        = $xpath->query( "/ONIXCodeTable/CodeList[ " . $n . " ]/Code[ " . $m . " ]/IssueNumber" )->item( 0 )->nodeValue;

        //echo "    " . $data["CodeValue"] . "\n";

        $sqlInsert = "insert into" . $tableName;
        $sqlInsert .= " (code_value,code_description,code_notes,issue_number)";
        $sqlInsert .= " values";
        $sqlInsert .= "(" . implode( ",", $data ) . ")";
        $mysqli->query( $sqlInsert );
    }
}
$mysqli->close ();
echo "Done importing code lists.\n";
?>