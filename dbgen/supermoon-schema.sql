
-- ------------------------------------------------------------------------------
create table onix_product_core
(
    id_onix_product_core integer unsigned not null auto_increment,
    primary key( id_onix_product_core )
);

-- ------------------------------------------------------------------------------
create table onix_product_singletons
(
    id_onix_product_singletons integer unsigned not null auto_increment,
    id_onix_product_core integer not null,
    
    barcode_code char( 2 ),             -- code list 6
    productform_code char( 2 ),         -- code list 7
    productformdetail_code char( 2 ),   -- code list 78
    productpackaging_code char( 2 ),    -- code list 80
    productformdescription text,
    numberofpieces integer,
    tradecategory_code char( 2 ),       -- code list 12
    productcontenttype_code char( 2 ),  -- code list 81
    noseries integer( 1 ),              -- either 1 to indicate that this book is not in a series, or null to indicate it _is_ in a series
    thesistype char( 2 ),               -- code list 72
    thesispresentedto varchar( 255 ),
    thesisyear integer( 4 ),
    contributorstatement text,
    nocontributor integer( 1 ),
    
    index( id_onix_product_core ),
    primary key( id_onix_product_singletons )
);

-- ------------------------------------------------------------------------------
create table productidentifier -- the short name of the composite
(
    id_productidentifier integer unsigned not null auto_increment,
    id_onix_product_core integer,
    id_set integer,

    productidtype_code char( 2 ), -- code list 5
    idtypename varchar( 255 ),
    idvalue varchar( 255 ),

    index( id_set ),
    index( id_onix_product_core ),
    primary key( id_productidentifier )
);

-- ------------------------------------------------------------------------------
create table productformfeature
(
    id_productformfeature integer unsigned not null auto_increment,
    id_onix_product_core integer not null,

    productformfeaturetype_code char( 2 ), -- code list 79
    productformfeaturevalue varchar( 255 ),
    productformfeaturedescription varchar( 255 ),
    
    index( id_onix_product_core ),
    primary key( id_productformfeature )
);

-- Skipping containeditem

-- Skipping productclassification

-- ------------------------------------------------------------------------------
-- this is singleton data, but at the risk of preoptimizing, it might make sense
-- to split it out
create table epublication
(
    id_epublication integer unsigned not null auto_increment,
    id_onix_product_core integer not null,
    
    epubtype_code char( 2 ), -- code list 10
    epubtypeversion varchar( 20 ),
    epubtypedescription varchar( 255 ),
    epubformat_code char( 2 ), -- code list 11
    epubformatversion varchar( 255 ),
    epubformatdescription varchar( 255 ),
    epubsource_code char( 2 ), -- code list 11
    epubsourceversion varchar( 20 ),
    epubsourcedescription varchar( 255 ),
    epubtypenote varchar( 255 ),
    
    index( id_onix_product_core ),
    primary key( id_epublication )
);

-- ------------------------------------------------------------------------------
-- SERIES
create table series_cross_reference
(
    id_series_cross_reference integer unsigned not null auto_increment,
    id_onix_product_core integer, -- point to the book in the series
    id_series integer, -- point to the series

    numberwithinseries varchar( 100 ),
    pubsequencenumberwithinseries integer( 4 ),   

    index( id_series ),
    index( id_onix_product_core ),
    primary key( id_series_cross_reference )
);
create table series
(
    id_series integer unsigned not null auto_increment,

    seriesidtype_code char( 2 ), -- code list 13
    idtypename varchar( 100 ),
    idvalue varchar( 100 ),
    titleofseries varchar( 255 ),
    yearofannual varchar( 9 ),

    primary key( id_series )
);

-- ------------------------------------------------------------------------------
-- SET
create table set_cross_reference
(
    id_set_cross_reference integer unsigned not null auto_increment,
    id_set integer not null,
    
    SetPartNumber varchar( 40 ),
    SetPartTitle varchar( 255 ),
    ItemNumberWithinSet varchar( 40 ),
    LevelSequenceNumber varchar( 200 ),
    SetItemTitle varchar( 255 ),
    
    index( id_set ),
    primary key( id_set_cross_reference )
);
create table bookset        -- haha 'set' is a reserved word
(
    id_bookset integer unsigned not null auto_increment,
    id_onix_product_core integer not null,

    TitleOfSet varchar( 255 ),

    index( id_onix_product_core ),
    primary key( id_bookset )
);

-- ------------------------------------------------------------------------------
create table title
(
    id_title integer unsigned not null auto_increment,
    id_onix_product_core integer, -- record is a title of a book
    id_series integer, -- record is a title of a series
    
    titletype_code char( 2 ), -- code list 15
    abbreviatedlength integer( 6 ),
    titletext varchar( 255 ),
    titleprefix varchar( 40 ),
    titlewithoutprefix varchar( 255 ),
    subtitle varchar( 255 ),
    
    index( id_series ),
    index( id_onix_product_core ),
    primary key( id_title )
);

-- ------------------------------------------------------------------------------
-- WORK IDENTIFIER
create table workidentifier
(
    id_workidentifier integer unsigned not null auto_increment,
    id_onix_product_core integer not null,

    workidtype_code char( 2 ), -- code list 16
    idtypename varchar( 255 ),
    idvalue varchar( 255 ),
    
    index( id_onix_product_core ),
    primary key( id_workidentifier )
);

-- ------------------------------------------------------------------------------
create table website
(
    id_website integer unsigned not null auto_increment,
    id_onix_product_core integer,
    id_contributor integer,

    websiterole_code char( 2 ), -- code list 73
    websitedescription varchar( 255 ), -- XHTML
    websitelink varchar( 255 ),
    
    index( id_contributor ),
    index( id_onix_product_core ),
    primary key( id_website )
);

-- ------------------------------------------------------------------------------
-- CONTRIBUTOR
create table contributor_cross_reference
(
    id_contributor_cross_reference integer unsigned not null auto_increment,
    id_onix_product_core integer, -- record is a contributor to a book
    id_series integer, -- record is a contributor to a series

    SequenceNumber integer( 3 ),
    ContributorRole_code char( 2 ), -- code list 17
    LanguageCode_code char( 3 ), -- code list 74
    SequenceNumberWithinRole integer( 3 ),

    index( id_series ),
    index( id_onix_product_core ),
    primary key( id_contributor_cross_reference )
);

-- ------------------------------------------------------------------------------
create table contributor
(
    id_contributor integer unsigned not null auto_increment,
    id_contributor_cross_reference integer, -- contributors are only referenced via the xref table
    
    personname varchar( 200 ),
    personnameinverted varchar( 200 ),
    titlesbeforenames varchar( 200 ),
    namesbeforekey varchar( 200 ),
    prefixtokey varchar( 200 ),
    keynames varchar( 200 ),
    namesafterkey varchar( 200 ),
    suffixtokey varchar( 200 ),
    lettersafternames varchar( 200 ),
    titlesafternames varchar( 200 ),
    -- a separate composite, inlined as a singleton here
    personnameidtype_code char( 2 ), -- code list 101
    idtypename varchar( 200 ),
    idvalue varchar( 100 ),
    -- separate composite inlined, "person date composite"
    persondaterole_code char( 2 ), -- code list 75
    dateformat_code char( 2 ), -- code list 55
    datevalue varchar( 32 ), -- NB the attribute is called "date", but that is a reserved work in sql
    -- separate composite inlined, "professional affiliation composite"
    professionalposition varchar( 200 ),
    affiliation varchar( 200 ),
    --
    corporatename varchar( 200 ),
    biographicalnote text,          -- XHTML
    -- ContributorDescription
    UnnamedPersons_code char( 2 ), -- code list 19
    CountryCode_code char( 2 ), -- code list 91
    RegionCode_code char( 16 ), -- code list 49
    
    index( id_contributor_cross_reference ),
    primary key( id_contributor )
);

-- ------------------------------------------------------------------------------
create table conference
(
    id_conference integer unsigned not null auto_increment,
    id_onix_product_core integer not null,

    ConferenceRole_code char( 2 ), -- code list 20
    ConferenceName varchar( 255 ),
    ConferenceAcronym varchar( 40 ),
    ConferenceNumber integer( 5 ),
    ConferenceTheme varchar( 40 ),
    ConferenceDate integer( 6 ),
    -- ConferencePlace 
    -- skipping all the conference sponsor data
    
    index( id_onix_product_core ),
    primary key( id_conference )
);

-- ------------------------------------------------------------------------------
create table edition
(
    id_edition integer unsigned not null auto_increment,
    id_onix_product_core integer not null,

    EditionTypeCode_code char( 3 ), -- code list 20
    EditionNumber integer( 4 ),
    EditionVersionNumber varchar( 40 ),
    EditionStatement varchar( 255 ),
    NoEdition  integer( 1 ),
    
    index( id_onix_product_core ),
    primary key( id_edition )
);

-- skipping ReligiousText



/*

-- ------------------------------------------------------------------------------
create table xxx
(
    id_xxx integer unsigned not null auto_increment,
    id_onix_product_core integer not null,


    
    index( id_onix_product_core ),
    primary key( id_xxx )
);
*/

/*


    
PR.11 Language
PR.12 Extents and other content
PR.13 Subject
PR.14 Audience
PR.15 Descriptions and other supporting text
PR.16 Links to image/audio/video files
PR.17 Prizes
PR.18 Content items
PR.19 Publisher
PR.20 Publishing status and dates, and copyright
PR.21 Territorial rights and other sales restrictions
PR.22 Dimensions
PR.23 Related products
PR.24 Supplier, availability and prices
PR.25 Market representation
PR.26 Sales promotion information

*/