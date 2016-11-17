<?php
// for parameters need:
// database credentials
// bs token
// filename with isbns in it

// parse parameters
$parameters = array
(
    "databasename"       => ""
    , "databaseuser"     => ""
    , "databasepwd"      => ""
    , "bibliosharetoken" => ""
    , "isbnfile"         => ""
);
for( $argid=1; $argid<$argc; $argid+=1 )
{
    $parts = explode( "=", $argv[ $argid ] );
    if( 2 != count( $parts ) )
    {
        writeHelp( "Invalid command line: '" . $argv[ $argid ] . "'" );
    }
    switch( $parts[ 0 ] )
    {
        default:
            writeHelp( "Unrecognized command line: '" . $argv[ $argid ] . "'" );
            break;
        
        case "databasename":
            $parameters[ "databasename" ] = $parts[ 1 ];
            break;
        
        case "databaseuser":
            $parameters[ "databaseuser" ] = $parts[ 1 ];
            break;
            
        case "databasepwd":
            $parameters[ "databasepwd" ] = $parts[ 1 ];
            break;
        
        case "bibliosharetoken":
            $parameters[ "bibliosharetoken" ] = $parts[ 1 ];
            break;
        
        case "isbnfile":
            $parameters[ "isbnfile" ] = $parts[ 1 ];
            break;
    }
}
foreach( $parameters as $param => $value )
{
    if( 0 == strlen( $value ) && "databasepwd" != $param ) // password can be blank, and we'll test the database params properly later on
    {
        writeHelp( "Missing parameters" );
    }
}

// convert parameter to array of isbns
$parameters[ "isbnfile" ] = convertIsbnFile( $parameters[ "isbnfile" ] );
echo "Found: " . count( $parameters[ "isbnfile" ] ) . " isbns.\n";

//---------------------------------------------------------------------------------------------
// accept URL, string of isbns, or file
function convertIsbnFile( $isbnfile )
{
    $results = array();
    $isbnPattern = str_repeat( "[0-9]", 13 );    
    $matches = array();
    $contents = null;
    
    // test for isbns in the string
    if( preg_match( "/" . $isbnPattern . "/", $isbnfile, $matches ) )
    {
        for( $n=0; $n<count($matches); $n+=1 )
        {
            $results[] = $matches[ $n ];
        }
    }
    else
    {
        if( 0 < strlen( stristr( $isbnfile, "http:/" ) )
         or 0 < strlen( stristr( $isbnfile, "https:/" ) ) )
        {
            // is a URL, load file into array of lines
            $contents = file( urlencode( $isbnfile ) );
        }
        else
        {
            // assume to be a file, load file into array of lines
            $contents = file( $isbnfile );
        }

        // parse array of lines of whatever into array of isbns
        foreach( $contents as $line )
        {
            //$matches = array();
            if( preg_match( "/" . $isbnPattern . "/", $line, $matches ) )
            {
                for( $n=0; $n<count($matches); $n+=1 )
                {
                    $results[] = $matches[ $n ];
                }
            }
        }
    }
    
    if( 0 == count( $results ) )
    {
        writeHelp( "Found no isbns" );
    }
    
    return $results;
}

//---------------------------------------------------------------------------------------------
function writeHelp( $msg )
{
    echo "\n\n" . $msg . "\n\n";
    echo "To run:\n";
    echo "php oniximport/importbiblioshare.php databasename=<name> databaseuser=<user> databasepwd=<pwd> bibliosharetoken=<token> isbnfile=<file>\n\n";
    echo "Where <file> can be a string of isbns, a file, or a URL.\n";
    exit( 1 );
}

?>