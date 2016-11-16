<?php
require_once( "onixparser.php" );

$rawFile = file_get_contents( "./test.xml" );

$onix = new OnixParser( $rawFile );

$record[ "RecordReference" ] = $onix->fetchValue( "/Product/RecordReference", 1, 1 );
$record[ "NotificationType" ] = $onix->fetchValue( "/Product/NotificationType", 1, 1 );
$record[ "DeletionCode" ] = $onix->fetchValue( "/Product/DeletionCode", 0, 1 );
$record[ "DeletionText" ] = $onix->fetchValue( "/Product/NotificationTypeDeletionText", 0, 1 );
$record[ "RecordSourceType" ] = $onix->fetchValue( "/Product/RecordSourceType", 0, 1 );
$record[ "RecordSourceIdentifier" ] = $onix->fetchValue( "/Product/RecordSourceIdentifier", 0, 1 );
$record[ "RecordSourceIdentifierType" ] = $onix->fetchValue( "/Product/RecordSourceIdentifierType", 0, 1 );
$record[ "RecordSourceName" ] = $onix->fetchValue( "/Product/RecordSourceName", 0, 1 );
$record[ "ProductIdentifier" ] = $onix->fetchCompsite
( array(
    "/Product/ProductIdentifier"
    , array( "ProductIDType", 1, 1 )
    , array( "IDTypeName", 0, 1 )
    , array( "IDValue", 1, 1 )
    ), 1, -1
);
$record[ "Barcode" ] = $onix->fetchValue( "/Product/Barcode", 0, -1 );
$record[ "ProductForm" ] = $onix->fetchValue( "/Product/ProductForm", 1, 1 );
$record[ "ProductFormDetail" ] = $onix->fetchValue( "/Product/ProductFormDetail", 0, -1 );
$record[ "ProductFormFeature" ] = $onix->fetchCompsite
( array(
    "/Product/ProductFormFeature"
    , array( "ProductFormFeatureType", 1, 1 )
    , array( "ProductFormFeatureValue", 0, 1 )
    , array( "ProductFormFeatureDescription", 0, 1 )
    ), 0, -1
);
$record[ "ProductPackaging" ] = $onix->fetchValue( "/Product/ProductPackaging", 0, 1 );
$record[ "ProductFormDescription" ] = $onix->fetchValue( "/Product/ProductFormDescription", 0, 1 );
$record[ "NumberOfPieces" ] = $onix->fetchValue( "/Product/NumberOfPieces", 0, 1 );
$record[ "TradeCategory" ] = $onix->fetchValue( "/Product/TradeCategory", 0, 1 );
$record[ "ProductContentType" ] = $onix->fetchValue( "/Product/ProductContentType", 0, -1 );
// next up: "Contained item product identifier composite"




$record[ "Title" ] = $onix->fetchCompsite
( array(
    "/Product/Title"
    , array( "TitleType", 1, 1 )
    , array( "AbbreviatedLength", 0, 1 )
    , array( "TitleText", 0, 1 ) // carries attributes: textformat, language, transliteration, textcase.
    , array( "TitlePrefix", 0, 1 )
    , array( "TitleWithoutPrefix", 0, 1 )
    , array( "Subtitle", 0, 1 )
    ), 1, -1
);




$record[ "Contributor" ] = $onix->fetchCompsite
( array(
    "/Product/Contributor"
    , array( "SequenceNumber", 0, 1 )
    , array( "ContributorRole", 1, -1 )
    , array( "LanguageCode", 0, -1 )
    , array( "SequenceNumberWithinRole", 0, 1 )
    , array( "PersonName", 0, 1 )
    , array( "PersonNameInverted", 0, 1 )    
    , array( "TitlesBeforeNames", 0, 1 )
    , array( "NamesBeforeKey", 0, 1 )
    , array( "PrefixToKey", 0, 1 )
    , array( "KeyNames", 0, 1 )
    , array( "NamesAfterKey", 0, 1 )
    , array( "SuffixToKey", 0, 1 )
    , array( "LettersAfterNames", 0, 1 )
    , array( "TitlesAfterNames", 0, 1 )
    , array
    (
        "PersonNameIdentifier" // nb, does not include the full path, like a composite usually does
        , array( "PersonNameIDType", 1, 1 )
        , array( "IDTypeName", 0, 1 )
        , array( "IDValue", 1, 1 )
        , 0, -1
    )    
    // skipping 'name' composite for now
    // skipping 'person date' composite for now
    // skipping ProfessionalAffiliation
    , array( "CorporateName", 0, 1 )
    , array( "BiographicalNote", 0, 1 )
    // skipping <Website>
    , array( "ContributorDescription", 0, 1 )
    , array( "UnnamedPersons", 0, 1 )
    , array( "CountryCode", 0, -1 )
    , array( "RegionCode", 0, -1 )    
    ), 0, -1
);



//have to test recursion in composites, use the SupplyDetail and Price

$record[ "SupplyDetail" ] = $onix->fetchCompsite
( array(
    "/Product/SupplyDetail"
    , array( "SupplierEANLocationNumber", 0, 1 )
    , array( "SupplierSAN", 0, -1 )
    // skip <SupplierIdentifier>
    , array( "SupplierName", 0, 1 )
    , array( "TelephoneNumber", 0, -1 )
    , array( "FaxNumber", 0, -1 )
    , array( "EmailAddress", 0, -1 )
    // skip <Website>
    , array( "SupplierRole", 0, 1 )
    , array( "SupplyToCountry", 0, -1 )
    , array( "SupplyToTerritory", 0, 1 )
    , array( "SupplyToCountryExcluded", 0, -1 )
    , array( "SupplyRestrictionDetail", 0, 1 )
    , array( "ReturnsCodeType", 0, 1 )
    , array( "LastDateForReturns", 0, 1 )
    , array( "AvailabilityCode", 0, 1 )
    , array( "ProductAvailability", 0, 1 )
    // skip <NewSupplier>, including sub <SupplierIdentifier>
    // skip a pile of stuff ...
    , array
    (
        "Price"
        , array( "PriceTypeCode", 1, 1 ) // not actually required, see spec.
        , array( "PriceQualifier", 0, 1 )
        , array( "PriceTypeDescription", 0, 1 )
        , array( "PricePer", 0, 1 )
        , array( "MinimumOrderQuantity", 0, 1 )
        // skip <BatchBonus>
        , array( "ClassOfTrade", 0, 1 )
        , array( "BICDiscountGroupCode", 0, 1 )
        // skip <DiscountCoded>
        , array( "DiscountPercent", 0, 1 )
        , array( "PriceStatus", 0, 1 )
        , array( "PriceAmount", 1, 1 )
        , array( "CurrencyCode", 0, 1 )
        , array( "CountryCode", 0, -1 )
        
        , 1, -1
    )
    
), 0, -1 );

    
    

    
    
    

print_r( $record );
echo "\n";
?>