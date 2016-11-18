<?php
class SQLStorage
{
    var $mysqli;
    
    //---------------------------------------------------------------
    public function __construct( $parameters )
    {
        $this->mysqli = new mysqli( "localhost", $parameters[ "databaseuser" ], $parameters[ "databasepwd" ], $parameters[ "databasename" ] );
        if( $this->mysqli->connect_errno )
        {
            echo "Error: Failed to make a MySQL connection, here is why: \n";
            echo "Errno: " . $this->mysqli->connect_errno . "\n";
            echo "Error: " . $this->mysqli->connect_error . "\n";
            exit;
        }
    }
    
    //---------------------------------------------------------------
    // accepts onix parse results:
    // $data[ProductIdentifier][1..n]{[ProductIDType][0],[IDTypeName][0],[IDValue][0]} 
    public function findOrCreateProductCore( $data )
    {
        $preferredIdentifiers = array( "15", "02" );
        
        foreach( $preferredIdentifiers as $identifier )
        {
            foreach( $data["ProductIdentifier"] as $n => $identifierComposite )
            {
                print_r($identifierComposite);
                if( $identifierComposite["ProductIDType"][0] == $identifier )
                {
                    $sql = "select * from productidentifier where productidtype_code =" . $identifier;
                    $sql .= " and idvalue =". $identifierComposite["IDValue"][0];
                    $sql .= " left join onix_product_core on onix_product_core.id_onix_product_core=productidentifier.id_onix_product_core";
                    
                    echo $sql . "\n\n";
                    
                    $set = $this->mysqli->query( $sql );
                    
                    print_r( $set );
                    
                }
            }
        }
        
        
    }
    
}
?>