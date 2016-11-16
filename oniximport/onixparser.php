<?php
class OnixParser
{
    private $theXMLDocument = null;
    private $theXPath = null;
    
     const COMPOSITEPATH = 0;
     const TERMINALPATHPART = 0;
     const MINIMUM = 1;
     const MAXIMUM = 2;
    
    //---------------------------------------------------------------
    public function __construct( $xml )
    {
        $this->theXMLDocument = new DOMDocument();
        $this->theXMLDocument->preserveWhiteSpace = false;
        $this->theXMLDocument->loadXML( $xml );
        
        $this->theXPath = new DOMXpath( $this->theXMLDocument );
    }
    
    //---------------------------------------------------------------
    public function getProductCount()
    {
        $onixProductNodes = $this->theXPath->query( "/Product/*" );
        return $onixProductNodes->length;
    }
    
    //---------------------------------------------------------------
    // $minimum can be: 0, or 1
    // $maximum can be: 1, or -1
    // $RecordReference = $onix->fetchValue( "/Product/RecordReference", 1, 1 );
    public function fetchValue( $xpath, $minimum=1, $maximum=1 )
    {
        if( !in_array( $minimum, array( 0, 1 ) ) || !in_array( $maximum, array( 1, -1 ) ) )
        {
            throw new exception( "OnixParser::fetchValue( '".$xpath."', ".$minimum.", ".$maximum." ) parameter is out of range." );
        }
        
        $pathparts = explode( "/", $xpath );
        $name = array_pop( $pathparts );
        $result[ $name ] = array();
        
        $nodeList = $this->theXPath->query( $xpath );
        $count = $nodeList->length;
        
        if( 1 == $minimum && 0 == $count )
        {
            throw new exception( "OnixParser::fetchValue( '".$xpath."', ".$minimum.", ".$maximum." ) located ".$count." instances." );
        }
        if( 1 == $maximum && 1 < $count )
        {
            throw new exception( "OnixParser::fetchValue( '".$xpath."', ".$minimum.", ".$maximum." ) located ".$count." instances." );
        }
        
        for( $n=0; $n<$count; $n+=1 )
        {
            $result[ $name ][ $n ] = $nodeList->item( $n )->nodeValue;
        }
        
        return $result;
    }

    /*
    fetchCompsite
    (
        array
        (
            "/Product/ProductIdentifier"
            , array( "ProductIDType", 1, 1 )
            , array( "IDTypeName", 0, 1 )
            , array( "IDValue", 1, 1 )
            , 1, -1 
        )
    )
    
    /Product/ProductIdentifier[$n]/IDValue
    
    */
    //---------------------------------------------------------------
    public function fetchCompsite( $xpaths, $compositeMinimum, $compositeMaximum )
    {
        if( !in_array( $compositeMinimum, array( 0, 1 ) ) || !in_array( $compositeMaximum, array( 1, -1 ) ) )
        {
            throw new exception( "OnixParser::fetchCompsite( '".$xpaths[ self::COMPOSITEPATH ]."', ".$compositeMinimum.", ".$compositeMaximum." ) parameter is out of range." );
        }

        $pathparts = explode( "/", $xpaths[ self::COMPOSITEPATH ] );
        $compositeName = array_pop( $pathparts );
        $result[ $compositeName ] = array();

        $compositeList = $this->theXPath->query( $xpaths[ self::COMPOSITEPATH ] );
        $compositeCount = $compositeList->length;

        if( 1 == $compositeMinimum && 0 == $compositeCount )
        {
            throw new exception( "OnixParser::fetchValue( '".$xpaths[ self::COMPOSITEPATH ]."', ".$compositeMinimum.", ".$compositeMaximum." ) located ".$compositeCount." instances." );
        }
        if( 1 == $compositeMaximum && 1 < $count )
        {
            throw new exception( "OnixParser::fetchValue( '".$xpaths[ self::COMPOSITEPATH ]."', ".$compositeMinimum.", ".$compositeMaximum." ) located ".$compositeCount." instances." );
        }
        
        for( $n=1; $n<=$compositeCount; $n+=1 )
        {
            $interiorValues = array();
            for( $s=1; $s<count($xpaths); $s+=1 )
            {
                // is this a contained element or a contained composite? ... 
                if( is_array( $xpaths[ $s ][ 1 ] ) )
                {
                    // contained composite
                    $subCompositeCount = count( $xpaths[ $s ] );
                    $subCompositeMin = $xpaths[ $s ][ $subCompositeCount-2 ];
                    $subCompositeMax = $xpaths[ $s ][ $subCompositeCount-1 ];
                    // have to build the root path since it contains a [#] selector
                    $subCompositePath = $xpaths[ self::COMPOSITEPATH ] . "[".$n."]/" . $xpaths[ $s ][ self::TERMINALPATHPART ];
                    $subCompositeXPaths[ self::COMPOSITEPATH ] = $subCompositePath;
                    // and just copy over the rest of the subpaths
                    for( $a=1; $a<$subCompositeCount-2; $a+=1 )
                    {
                        $subCompositeXPaths[] = $xpaths[ $s ][ $a ];
                    }
                    // and be recursive
                    $interiorValues = array_merge( $interiorValues, $this->fetchCompsite( $subCompositeXPaths, $subCompositeMin, $subCompositeMax ) );
                }
                else
                {
                    // simple element
                    $terminalPathPart = $xpaths[ $s ][ self::TERMINALPATHPART ]; // for elements contained in the composite, just the element name was provided
                    $xpath = $xpaths[ self::COMPOSITEPATH ] ."[".$n."]/".$terminalPathPart;
                    $interiorValues = array_merge( $interiorValues, $this->fetchValue( $xpath, $xpaths[ $s ][ self::MINIMUM ], $xpaths[ $s ][ self::MAXIMUM ] ) );
                }
            }
            $result[ $compositeName ][ $n ] = $interiorValues;
        }
        return $result;
    }
}
?>