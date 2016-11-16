# supermoon
a back of desk project to write a decent onix tool

# road map:
* make the test parsing code work, and pull some data into the database
* complete the schema sql
* complete the parser
* build management ui



# current intentional database limitations:

* only going to support one "PR.2.10 Barcode indicator"

    might consider a comma separated list of the code values
    same as "PR.3.2 Product form detail"
    same as "PR.3.11 Product content type code"
    totally skipped the entire "Contained item product identifier composite"

* there are a bunch of functionalities (pieces of data) that most publishers will never use

    generally, these will be annoying clutter
    clever thing to do: hide these by default, negotiate some way to enable them

* only going to support one "Series identifier composite" per series

* only going to support one "work identifier composite" per book

* only going to support one "Person name identifier composite" per contributor

* not going to implement "Name composite" (support for contributor alternate names, subsidiary to contributor composite)

* only going to support 1 "Person date composite" within a contributor

* only going to support 1 "Professional affiliation composite" within a contributor

* entirely skipping all the conference sponsor data, "ConferenceSponsor"

* entirely skipping ReligiousText






